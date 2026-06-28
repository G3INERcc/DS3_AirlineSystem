<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("connection.php");

$user = $_POST['user'];
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE username='$user' AND pass='$password'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    $row = $result->fetch_assoc();

    $_SESSION['id'] = $row['id'];
    $_SESSION['user'] = $row['username'];
    $_SESSION['user_type'] = $row['user_type'];

    header("Location: menu.php");
    exit();

} else {

    // volver al index
    header("Location: ../index.html?error=1");
    exit();

}

$conn->close();
?>