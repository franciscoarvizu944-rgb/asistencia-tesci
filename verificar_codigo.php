<?php
// Desactivar errores visibles para no ensuciar el JSON
error_reporting(0);
ini_set('display_errors', 0);

// Cabeceras de seguridad para Flutter y manejo de acentos
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Responder a peticiones de verificación del navegador (Pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

require_once '../config/conexion.php';

// Obtener los datos enviados desde la app (Dio usa POST normal)
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$codigo = isset($_POST["codigo"]) ? trim($_POST["codigo"]) : "";

if ($email == "" || $codigo == "") {
    echo json_encode(["status" => "error", "message" => "Ingresa el código enviado a tu correo"]);
    exit;
}

// Consulta para verificar que el código coincida y no haya expirado
$sql = "SELECT id FROM usuarios 
        WHERE email = ? 
        AND token_recuperacion = ? 
        AND expira_token > NOW()";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $email, $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // El código es correcto y está vigente
    echo json_encode([
        "status" => "ok", 
        "message" => "Código verificado con éxito"
    ]);
} else {
    // Si no hay resultados, el código es incorrecto o ya pasaron los 10 minutos
    echo json_encode([
        "status" => "error", 
        "message" => "El código es incorrecto o ya ha expirado"
    ]);
}
?>
