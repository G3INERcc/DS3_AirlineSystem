<?php

session_start();
include("connection.php");

// Capturar datos
$id = $_POST['id'] ?? '';
$user = $_POST['user'] ?? '';
$password = $_POST['password'] ?? '';

$role = "colaborador";

// rutas
$redirectSuccess = "../php/userManagement.php";
$redirectError = "../html/createUserAdm.html";

// ============================
// VALIDAR ID
// ============================

$checkID =
    "SELECT id
FROM user
WHERE id='$id'";

$resultID =
    $conn->query($checkID);

if ($resultID->num_rows > 0) {

    header(
        "Location: " . $redirectError . "?error=id"
    );

    exit();

}

// ============================
// VALIDAR USERNAME
// ============================

$checkUser =
    "SELECT username
FROM user
WHERE username='$user'";

$resultUser =
    $conn->query($checkUser);

if ($resultUser->num_rows > 0) {

    header(
        "Location: " . $redirectError . "?error=user"
    );

    exit();

}

// ============================
// INSERTAR
// ============================

$sql =

    "INSERT INTO user
(id,username,pass,user_type)

VALUES

('$id','$user','$password','$role')";

if ($conn->query($sql)) {

    header(
        "Location: " . $redirectSuccess
    );

    exit();

}

header(
    "Location: " . $redirectError . "?error=generic"
);

$conn->close();

?>