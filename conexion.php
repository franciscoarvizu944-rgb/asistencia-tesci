<?php
// Datos de conexión de Railway
$host = "mysql.railway.internal"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = "3306";

$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    die("Error de conexión");
}

mysqli_set_charset($conexion, "utf8");
