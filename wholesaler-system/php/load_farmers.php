<?php
require 'db.php';

$search = $_GET['search'] ?? ''; 

// Base query
$sql = "
SELECT 
    farmers.farmer_id,
    farmers.name,
    farmers.contact,
    farmers.location,
    IFNULL(GROUP_CONCAT(DISTINCT vegetables.name SEPARATOR ', '), 'None') AS vegetables,
    IFNULL(GROUP_CONCAT(stocks.quantity SEPARATOR ', '), '0') AS quantities,
    IFNULL(SUM(stocks.price * stocks.quantity), 0) AS total_price
FROM farmers
LEFT JOIN stocks ON farmers.farmer_id = stocks.farmer_id
LEFT JOIN vegetables ON stocks.veg_id = vegetables.veg_id
";

// Add search condition if needed
if (!empty($search)) {
  $sql .= " WHERE farmers.name LIKE ? ";
}

// Group and order
$sql .= " GROUP BY farmers.farmer_id ORDER BY farmers.name ASC";

// Prepare and execute
$stmt = $conn->prepare($sql);

if (!empty($search)) {
  $like = "$search%";
  $stmt->bind_param("s", $like);
}

$stmt->execute();
$result = $stmt->get_result();

// Output farmer table rows
while ($row = $result->fetch_assoc()) {
  echo "<tr>";
  echo "<td>" . htmlspecialchars($row['name']) . "</td>";
  echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
  echo "<td>" . htmlspecialchars($row['location']) . "</td>";
  echo "<td>" . nl2br(htmlspecialchars($row['vegetables'])) . "</td>";
  echo "<td>" . nl2br(htmlspecialchars($row['quantities'])) . "</td>";
  echo "<td>Rs. " . number_format($row['total_price'], 2) . "</td>";
  echo "<td><a href='php/delete_farmer.php?id={$row['farmer_id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
  echo "</tr>";
}
?>
