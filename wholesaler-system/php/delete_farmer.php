<?php
include 'db.php'; 
if (isset($_GET['id'])) {
    $farmer_id = intval($_GET['id']);

  
    $conn->query("DELETE FROM stocks WHERE farmer_id = $farmer_id");

    $sql = "DELETE FROM farmers WHERE farmer_id = $farmer_id";
    if ($conn->query($sql)) {
        header("Location: ../farmers.php?deleted=1");
    } else {
        echo "Error deleting farmer: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
