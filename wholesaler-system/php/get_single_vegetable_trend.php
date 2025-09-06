<?php
include 'db.php';

$veg_id = $_GET['veg_id'];

$sql = "SELECT date, SUM(quantity) AS qty 
        FROM stocks 
        WHERE veg_id = $veg_id 
        GROUP BY date 
        ORDER BY date ASC";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
