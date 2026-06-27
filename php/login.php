<?php

session_start();
include("conection.php");

$user = $_POST['user'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE user='$user' AND password='$password'";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){

    $row = $result->fetch_assoc();

    $_SESSION['user'] = $row['user'];
    $_SESSION['role'] = $row['role'];

    header("Location: ../php/menu.php");
    exit();

}

$conn->close();
?>