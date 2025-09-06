<?php
require 'db.php';

if (isset($_GET['id'])) {
  $stockId = $_GET['id'];

  $stmt = $conn->prepare("DELETE FROM stocks WHERE stock_id = ?");
  $stmt->bind_param("i", $stockId);

  if ($stmt->execute()) {
    header("Location: ../entry.php?deleted=1");
        exit();
  } else {
    echo "Failed to delete stock.";
  }
  $stmt->close();
} else {
  echo "Invalid request.";
}
?>
