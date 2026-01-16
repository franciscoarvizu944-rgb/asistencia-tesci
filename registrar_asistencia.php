<?php
// Desactivar errores visibles para no ensuciar el JSON
error_reporting(0);
ini_set('display_errors', 0);

// Cabeceras de seguridad y compatibilidad CORS para Flutter
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Configurar zona horaria del TESCI
date_default_timezone_set('America/Mexico_City'); 

// Responder a peticiones Pre-flight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

require_once '../config/conexion.php';

// Recibir datos desde Flutter (Dio envía los datos vía $_POST)
$alumno_nombre = isset($_POST['alumno_nombre']) ? trim($_POST['alumno_nombre']) : "";
// Ajustado para coincidir con el nombre de campo enviado desde Flutter
$codigo_qr_escaneado = isset($_POST['codigo_qr']) ? trim($_POST['codigo_qr']) : "";

// Validar que los datos no lleguen vacíos
if (empty($alumno_nombre) || empty($codigo_qr_escaneado)) {
    echo json_encode(["status" => "error", "message" => "Faltan datos para el registro"]);
    exit;
}

// 1. Verificar si el código QR existe en la sesión (Usando $conexion de tu conexion.php)
$query_sesion = $conexion->prepare("SELECT materia_id FROM asistencias_sesiones WHERE codigo_qr = ?");
$query_sesion->bind_param("s", $codigo_qr_escaneado);
$query_sesion->execute();
$res_sesion = $query_sesion->get_result();

if ($res_sesion->num_rows > 0) {
    $datos_sesion = $res_sesion->fetch_assoc();
    $materia_id = $datos_sesion['materia_id'];
    
    $fecha_hoy = date("Y-m-d");
    $hora_actual = date("H:i:s");

    // 2. VALIDACIÓN DE DUPLICADOS: Evitar que el mismo alumno firme dos veces la misma materia el día de hoy
    $query_duplicado = $conexion->prepare("SELECT id FROM asistencias WHERE alumno_nombre = ? AND materia_id = ? AND fecha = ?");
    $query_duplicado->bind_param("sis", $alumno_nombre, $materia_id, $fecha_hoy);
    $query_duplicado->execute();
    
    if ($query_duplicado->get_result()->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Ya registraste tu asistencia para esta sesión"]);
        exit;
    }

    // 3. Insertar la asistencia como 'presente'
    $estado = "presente"; 
    $sql_insert = "INSERT INTO asistencias (materia_id, fecha, hora, alumno_nombre, estado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("issss", $materia_id, $fecha_hoy, $hora_actual, $alumno_nombre, $estado);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "ok", 
            "message" => "Asistencia registrada correctamente",
            "detalles" => [
                "materia" => $materia_id,
                "hora" => $hora_actual
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error interno al guardar asistencia"]);
    }

} else {
    // Si el QR escaneado no coincide o la sesión no existe
    echo json_encode(["status" => "error", "message" => "El código QR no es válido o la sesión ha terminado"]);
}

$conexion->close();
?>