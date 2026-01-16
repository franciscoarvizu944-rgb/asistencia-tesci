<?php
// Datos de Railway (Dominio Público)
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = 52104; 

// Intentar la conexión
$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    header('Content-Type: application/json');
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . mysqli_connect_error()]));
}

mysqli_set_charset($conexion, "utf8");
?>
