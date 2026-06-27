<?php
$host = "localhost";
$user = "root";
$password = "";
$bd = "ds3_proyecto";

$conn = new mysqli($host,$user,$password,$bd);
if($conn ->connect_error){
    die("Error de conexión" . $conn->connect_error);
}
?>