<?php
require 'db.php';

$q = $_GET['q'] ?? '';

$stmt = $conn->prepare("SELECT name FROM farmers WHERE name LIKE ?");
$search = "$q%";
$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();
$matches = [];

while ($row = $result->fetch_assoc()) {
    $matches[] = ['name' => $row['name']];
}

header('Content-Type: application/json');
echo json_encode($matches);
?>
