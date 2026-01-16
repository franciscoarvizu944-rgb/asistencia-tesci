<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

header("Content-Type: application/json; charset=utf-8");
require_once 'conexion.php';

$nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
$apellidos = isset($_POST["apellidos"]) ? trim($_POST["apellidos"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

// Extraemos el numero de control del email (ej. 213107216)
$numero_control = explode('@', $email)[0]; 

if (empty($nombre) || empty($email) || empty($password)) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
    exit;
}

$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// Insertamos respetando las columnas de tu tabla usuarios (numero_control, nombre, apellidos, email, password, rol)
$stmt = $conexion->prepare("INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, 'alumno')");
$stmt->bind_param("sssss", $numero_control, $nombre, $apellidos, $email, $pass_hash);

ob_clean();
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}
ob_end_flush();