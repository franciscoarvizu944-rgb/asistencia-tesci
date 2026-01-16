<?php
// Datos extraídos de tu URL de Railway
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = "52104";

$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    // Esto te ayudará a saber si Railway rechaza la conexión
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
