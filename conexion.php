<?php
// Datos finales de conexión Railway (Acceso Público)
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = 52104; // Puerto externo según tu captura de Railway

// Intentar la conexión
$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    // Esto imprimirá el error real en los logs de Render para poder debuguear
    error_log("Fallo de conexión: " . mysqli_connect_error());
    die(json_encode(["status" => "error", "message" => "Error interno de base de datos"]));
}

mysqli_set_charset($conexion, "utf8");
