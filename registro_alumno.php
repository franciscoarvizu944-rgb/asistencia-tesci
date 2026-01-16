<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// Llamamos a la conexión centralizada
require_once 'conexion.php';

$numero_control = $_POST["numero_control"] ?? "";
$nombre = $_POST["nombre"] ?? "";
$apellidos = $_POST["apellidos"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

if (empty($numero_control) || empty($email)) {
    die(json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]));
}

$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// Insertamos usando la variable $conexion del archivo conexion.php
$sql = "INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) 
        VALUES (?, ?, ?, ?, ?, 'alumno')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $numero_control, $nombre, $apellidos, $email, $pass_hash);

ob_clean();
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}
ob_end_flush();
?>
