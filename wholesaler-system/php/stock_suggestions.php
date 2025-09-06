<?php
require 'db.php';

$term = $_GET['term'] ?? '';
$term = "%$term%";

$stmt = $conn->prepare("
  SELECT DISTINCT CONCAT(f.name, ' - ', v.name) AS label
  FROM stocks s
  JOIN farmers f ON s.farmer_id = f.farmer_id
  JOIN vegetables v ON s.veg_id = v.veg_id
  WHERE f.name LIKE ? OR v.name LIKE ?
  LIMIT 10
");
$stmt->bind_param("ss", $term, $term);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
  $suggestions[] = $row['label'];
}

echo json_encode($suggestions);
?>
