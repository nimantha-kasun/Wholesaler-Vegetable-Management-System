<?php
include 'db.php';
$id = $_POST['farmer_id'];
$name = $_POST['name'];
$contact = $_POST['contact'];
$location = $_POST['location'];

$sql = "UPDATE farmers SET name='$name', contact='$contact', location='$location' WHERE farmer_id=$id";
$conn->query($sql);
$conn->close();
header("Location: ../farmers.html");
?>
