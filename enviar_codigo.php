<?php
// Desactivar errores visibles para no romper el JSON de Flutter
error_reporting(0);
ini_set('display_errors', 0);

// Cabeceras de seguridad y compatibilidad CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Responder rápido a peticiones de verificación (Pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

require_once '../config/conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

// Obtener el correo enviado desde Dio (Flutter)
$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "El correo es obligatorio"]);
    exit;
}

// Validar dominio institucional del TESCI
if (!preg_match('/@cuautitlan\.tecnm\.mx$/', $email)) {
    echo json_encode([
        "status" => "error", 
        "message" => "Usa tu correo @cuautitlan.tecnm.mx"
    ]);
    exit;
}

// Buscar si el usuario existe en la base de datos
$sql = "SELECT id, nombre FROM usuarios WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Este correo no está registrado"]);
    exit;
}

$user = $result->fetch_assoc();
$codigo = rand(1000, 9999); // Genera el código de 4 dígitos solicitado

// Guardar el código y la expiración (10 minutos)
$sql_update = "UPDATE usuarios SET token_recuperacion=?, expira_token=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id=?";
$stmt2 = $conexion->prepare($sql_update);
$stmt2->bind_param("si", $codigo, $user["id"]);
$stmt2->execute();

// Configuración de envío de correo
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'appasistencia11@gmail.com';
    $mail->Password   = 'sxwpnoermzshpasd'; // Contraseña de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8'; // Crucial para los acentos

    $mail->setFrom('appasistencia11@gmail.com', 'Sistema de Asistencia TESCI');
    $mail->addAddress($email, $user["nombre"]);

    $mail->isHTML(true);
    $mail->Subject = 'Código de recuperación - Asistencia TESCI';
    
    // Cuerpo del mensaje con ortografía cuidada
    $mail->Body = "
        <div style='font-family: sans-serif; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
            <h2 style='color: #3949ab;'>Recuperación de Contraseña</h2>
            <p>Hola, <strong>" . htmlspecialchars($user['nombre']) . "</strong>.</p>
            <p>Has solicitado un código para restablecer tu contraseña en la App de Asistencia.</p>
            <div style='background: #f4f4f4; padding: 15px; text-align: center; font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #1a237e;'>
                {$codigo}
            </div>
            <p style='color: #777; font-size: 14px; margin-top: 20px;'>
                Este código expirará en 10 minutos por tu seguridad.
            </p>
            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #999;'>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        </div>
    ";

    $mail->send();
    echo json_encode(["status" => "ok", "message" => "Código enviado con éxito"]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error", 
        "message" => "No se pudo enviar el correo. Inténtalo más tarde."
    ]);
}
?>
