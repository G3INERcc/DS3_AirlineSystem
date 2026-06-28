<?php
$host = "localhost";
$user = "root";
$password = "";
$bd = "airlines_db";

$conn = new mysqli($host,$user,$password,$bd);
if($conn ->connect_error){
    die("Error de conexión" . $conn->connect_error);
}
?>