<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

header("Content-Type: application/json; charset=utf-8");
require_once 'conexion.php';

// Recibimos los datos del formulario (incluyendo el nuevo campo numero_control)
$nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
$apellidos = isset($_POST["apellidos"]) ? trim($_POST["apellidos"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$num_control_post = isset($_POST["numero_control"]) ? trim($_POST["numero_control"]) : "";

// Si el campo numero_control vino vacío, lo extraemos del email como respaldo
if (empty($num_control_post)) {
    $numero_control = explode('@', $email)[0];
} else {
    $numero_control = $num_control_post;
}

if (empty($nombre) || empty($email) || empty($password)) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
    exit;
}

// Encriptamos la contraseña para seguridad
$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// Insertamos en la tabla 'usuarios' de Railway
$stmt = $conexion->prepare("INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, 'alumno')");
$stmt->bind_param("sssss", $numero_control, $nombre, $apellidos, $email, $pass_hash);

ob_clean();
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    // Si el email ya existe, MySQL arrojará un error de duplicado
    if ($conexion->errno == 1062) {
        echo json_encode(["status" => "error", "message" => "Este correo o número de control ya está registrado"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar: " . $stmt->error]);
    }
}
ob_end_flush();
