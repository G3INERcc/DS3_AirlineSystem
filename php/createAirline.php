<?php

session_start();
include("connection.php");

// Capturar datos
$code = $_POST['code'] ?? '';
$name = $_POST['name'] ?? '';
$country = $_POST['country'] ?? '';

// Rutas
$redirectSuccess = "../php/airlineManagement.php";
$redirectError = "../html/createAirline.html";

// ============================
// VALIDAR CÓDIGO
// ============================

$checkCode =
    "SELECT cod_airline
    FROM airline
    WHERE cod_airline='$code'";

$resultCode = $conn->query($checkCode);

if ($resultCode->num_rows > 0) {

    header(
        "Location: " . $redirectError . "?error=code"
    );

    exit();

}

// ============================
// VALIDAR NOMBRE
// ============================

$checkName =
    "SELECT name
    FROM airline
    WHERE name='$name'";

$resultName = $conn->query($checkName);

if ($resultName->num_rows > 0) {

    header(
        "Location: " . $redirectError . "?error=name"
    );

    exit();

}

// ============================
// INSERTAR
// ============================

$sql =

    "INSERT INTO airline
    (cod_airline, name, country)

    VALUES

    ('$code','$name','$country')";

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