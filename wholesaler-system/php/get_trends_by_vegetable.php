<?php
include 'db.php';

// Get all unique vegetables with their stock quantities per date
$sql = "SELECT v.name AS vegetable, s.date, SUM(s.quantity) AS quantity
        FROM stocks s
        JOIN vegetables v ON s.veg_id = v.veg_id
        GROUP BY v.name, s.date
        ORDER BY s.date ASC";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $veg = $row['vegetable'];
    $date = $row['date'];
    $qty = $row['quantity'];

    if (!isset($data[$veg])) {
        $data[$veg] = [];
    }
    $data[$veg][$date] = $qty;
}

echo json_encode($data);
?>
