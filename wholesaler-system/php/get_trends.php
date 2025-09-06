<?php
include 'db.php';

// Get summed stock quantity by date
$sql = "SELECT date, SUM(quantity) AS total_quantity 
        FROM stocks 
        GROUP BY date 
        ORDER BY date ASC";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "date" => $row['date'],
        "quantity" => $row['total_quantity']
    ];
}

echo json_encode($data);
?>
