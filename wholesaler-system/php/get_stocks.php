<?php
require 'db.php';

$search = $_GET['search'] ?? '';
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query with optional search
$searchClause = '';
$searchParams = [];
$searchTypes = '';

if (!empty($search)) {
    $searchClause = "WHERE farmers.name LIKE ? OR vegetables.name LIKE ?";
    $searchParams[] = "%$search%";
    $searchParams[] = "%$search%";
    $searchTypes = 'ss';
}

// Count total filtered rows
$countSql = "SELECT COUNT(*) as total FROM stocks 
             JOIN farmers ON stocks.farmer_id = farmers.farmer_id 
             JOIN vegetables ON stocks.veg_id = vegetables.veg_id 
             $searchClause";

$countStmt = $conn->prepare($countSql);
if (!empty($search)) {
    $countStmt->bind_param($searchTypes, ...$searchParams);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated data
$dataSql = "SELECT 
              stocks.stock_id,
              stocks.farmer_id,
              farmers.name AS farmer,
              stocks.veg_id,
              vegetables.name AS vegetable,
              stocks.quantity,
              stocks.price,
              stocks.date
            FROM stocks
            JOIN farmers ON stocks.farmer_id = farmers.farmer_id
            JOIN vegetables ON stocks.veg_id = vegetables.veg_id
            $searchClause
            ORDER BY stocks.date DESC
            LIMIT ?, ?";

$dataStmt = $conn->prepare($dataSql);
if (!empty($search)) {
    $dataTypes = $searchTypes . 'ii';
    $dataParams = [...$searchParams, $offset, $limit];
    $dataStmt->bind_param($dataTypes, ...$dataParams);
} else {
    $dataStmt->bind_param('ii', $offset, $limit);
}

$dataStmt->execute();
$dataResult = $dataStmt->get_result();

$data = [];
while ($row = $dataResult->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
?>
