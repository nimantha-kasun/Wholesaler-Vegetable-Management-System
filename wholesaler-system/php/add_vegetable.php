<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $unit = $_POST['unit'] ?? '';

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO vegetables (name, type, unit) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $type, $unit);

        if ($stmt->execute()) {
            header("Location: ../entry.php?veg_success=1");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Vegetable name is required.";
    }

    $conn->close();
}
?>
