<?php
// Sin espacios antes de esta etiqueta
$host = "sql305.infinityfree.com"; 
$user = "if0_40900198";            
$pass = "3QBiO6kD1Pi";            
$db   = "if0_40900198_asistencia"; 

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    exit; 
}

mysqli_set_charset($conexion, "utf8");