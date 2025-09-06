<?php
include 'db.php';
$result = $conn->query("SELECT veg_id, name FROM vegetables ORDER BY name");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
