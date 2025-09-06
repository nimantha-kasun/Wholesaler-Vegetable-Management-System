<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $farmer_id = $_POST['farmer_id'];
    $veg_id = $_POST['veg_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO stocks (farmer_id, veg_id, quantity, price, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidds", $farmer_id, $veg_id, $quantity, $price, $date);

    if ($stmt->execute()) {
        header("Location: ../entry.php?stock_success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
