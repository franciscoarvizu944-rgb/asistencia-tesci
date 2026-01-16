<?php
// Desactivar errores visibles para mantener la pureza del JSON
error_reporting(0);
ini_set('display_errors', 0);

// Cabeceras de seguridad y compatibilidad CORS para la App Móvil
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Manejo de peticiones Pre-flight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

require_once '../config/conexion.php';

// Obtener los datos enviados desde Flutter
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$codigo = isset($_POST["codigo"]) ? trim($_POST["codigo"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

if ($email == "" || $codigo == "" || $password == "") {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
    exit;
}

// 1. Verificar que el código siga siendo válido y no haya expirado (10 min)
$sql = "SELECT id FROM usuarios 
        WHERE email = ? 
        AND token_recuperacion = ? 
        AND expira_token > NOW()";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $email, $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "El código ha expirado o es inválido"]);
    exit;
}

// 2. Hashear la nueva contraseña (Seguridad)
$hash = password_hash($password, PASSWORD_DEFAULT);

// 3. Actualizar contraseña y limpiar los campos de recuperación
$sqlUpdate = "UPDATE usuarios 
              SET password = ?, token_recuperacion = NULL, expira_token = NULL 
              WHERE email = ?";
$stmt2 = $conexion->prepare($sqlUpdate);
$stmt2->bind_param("ss", $hash, $email);

if ($stmt2->execute()) {
    echo json_encode([
        "status" => "ok", 
        "message" => "Tu contraseña ha sido actualizada correctamente"
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Error interno al actualizar la base de datos"
    ]);
}
?>