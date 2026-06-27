<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("conection.php");

$user = $_POST['user'];
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE user ='$user' AND pass='$password'";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){

    $row = $result->fetch_assoc();

    $_SESSION['user'] = $row['user'];
    $_SESSION['user_type'] = $row['user_type'];

    header("Location: ../php/menu.php");
    exit();

}

$conn->close();

?>