<?php
// Configuración para conexión EXTERNA (Render -> Railway)
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = 52104; // Usar como número, no como texto

// Intentar conexión con el puerto específico
$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    // Esto enviará el error técnico real a la pantalla para saber qué pasa
    die("Fallo crítico de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
