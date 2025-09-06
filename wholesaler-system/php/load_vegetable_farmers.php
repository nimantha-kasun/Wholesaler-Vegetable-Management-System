<?php
require 'db.php';

$sql = "SELECT 
          v.name AS vegetable_name,
          f.name AS farmer_name,
          SUM(s.quantity) AS quantity
        FROM stocks s
        JOIN vegetables v ON s.veg_id = v.veg_id
        JOIN farmers f ON s.farmer_id = f.farmer_id
        GROUP BY v.veg_id, f.farmer_id
        ORDER BY v.name, f.name";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $veg = $row['vegetable_name'];
    if (!isset($data[$veg])) {
        $data[$veg] = ['total' => 0, 'farmers' => []];
    }
    $data[$veg]['total'] += $row['quantity'];
    $data[$veg]['farmers'][] = [
        'name' => $row['farmer_name'],
        'qty' => $row['quantity']
    ];
}

echo json_encode($data);
?>
