<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];
$hashed = hash('sha256', $password);

$sql = "SELECT * FROM admin WHERE username='$username' AND password='$hashed'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $_SESSION['admin'] = $username;
    header("Location: ../index.php");
} else {
    echo "Login failed. <a href='../login.html'>Try again</a>";
}
?>
