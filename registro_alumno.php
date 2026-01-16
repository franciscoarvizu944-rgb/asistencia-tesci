<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// Conexión directa a tu puerto público de Railway
$conexion = mysqli_connect("shortline.proxy.rlwy.net", "root", "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW", "railway", 52104);

if (!$conexion) {
    die(json_encode(["status" => "error", "message" => "Fallo de conexión externa"]));
}

// Recibir datos del formulario
$numero_control = $_POST["numero_control"] ?? "";
$nombre = $_POST["nombre"] ?? "";
$apellidos = $_POST["apellidos"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

if (empty($numero_control) || empty($email)) {
    die(json_encode(["status" => "error", "message" => "Datos incompletos"]));
}

$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// INSERT MANUAL (Sin usar bind_param para asegurar que no haya fallos de tipo de dato)
$sql = "INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) 
        VALUES ('$numero_control', '$nombre', '$apellidos', '$email', '$pass_hash', 'alumno')";

ob_clean();
if (mysqli_query($conexion, $sql)) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error MySQL: " . mysqli_error($conexion)]);
}
mysqli_close($conexion);
