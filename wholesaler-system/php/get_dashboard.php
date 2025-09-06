<?php
include 'db.php';

// 1. Count distinct vegetables
$veg_count_result = $conn->query("SELECT COUNT(*) AS veg_total FROM vegetables");
$veg_count = $veg_count_result->fetch_assoc()['veg_total'];

// 2. Count farmers
$farmers_result = $conn->query("SELECT COUNT(*) AS total FROM farmers");
$farmer_count = $farmers_result->fetch_assoc()['total'];

// 3. Get vegetable summary
$veg_result = $conn->query("SELECT v.name, SUM(s.quantity) AS total_qty
                            FROM stocks s
                            JOIN vegetables v ON s.veg_id = v.veg_id
                            GROUP BY s.veg_id");
$veg_summary = [];


// 4. Get total quantity of all vegetables
$qty_result = $conn->query("SELECT SUM(quantity) AS total_qty FROM stocks");
$total_quantity = $qty_result->fetch_assoc()['total_qty'];

while ($row = $veg_result->fetch_assoc()) {
    $veg_summary[] = $row;
}

// 4. Return data
echo json_encode([
    "vegetable_count" => $veg_count,
    "farmer_summary" => $farmer_count,
    "total_quantity" => $total_quantity,
    "vegetables" => $veg_summary
]);
?>
