<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO farmers (name, contact, location) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $contact, $location);

    if ($stmt->execute()) {
        header("Location: ../entry.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
