<?php
session_start();
include("conection.php");

// 1. CAPTURAR DATOS DEL FORMULARIO
$user = $_POST['user'] ?? '';
$password = $_POST['password'] ?? '';
$role = "Colaborador";

// Rutas fijas de redirección
$redirectSuccess = "../index.html";
$redirectError = "../html/createAccount.html";

// ==========================================
// 2. LÓGICA DE BASE DE DATOS
// ==========================================

// Verificar si existe el usuario
$check = "SELECT * FROM users WHERE user='$user'";
$result = $conn->query($check);

if ($result->num_rows > 0) {
    header("Location: " . $redirectError . "?error=exists");
    exit();
}

// Crear usuario
$sql = "INSERT INTO users (user, password, role) VALUES ('$user','$password','$role')";

if ($conn->query($sql)) {
    // ÉXITO: Va directo al index
    header("Location: " . $redirectSuccess);
    exit();
} else {
    // ERROR GENÉRICO: Regresa al formulario con el error
    header("Location: " . $redirectError . "?error=generic");
    exit();
}

$conn->close();
?>