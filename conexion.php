<?php
// Datos de conexión EXTERNA (Render -> Railway)
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = 52104; // Puerto público de tu imagen f83821.png

// Forzar la conexión con el puerto
$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    // Esto nos dirá el error real en los registros de Render
    error_log("Fallo de conexión MySQL: " . mysqli_connect_error());
    die("Error de conexión");
}

mysqli_set_charset($conexion, "utf8");
