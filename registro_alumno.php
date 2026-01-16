<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }
header("Content-Type: application/json; charset=utf-8");

// Conexión directa probada
$conexion = mysqli_connect("shortline.proxy.rlwy.net", "root", "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW", "railway", 52104);

if (!$conexion) {
    die(json_encode(["status" => "error", "message" => "Fallo de conexión"]));
}

// Recibir datos del formulario
$nombre = $_POST["nombre"] ?? "";
$apellidos = $_POST["apellidos"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";
$numero_control = $_POST["numero_control"] ?? "";

if (empty($nombre) || empty($email) || empty($password) || empty($numero_control)) {
    echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios"]);
    exit;
}

$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// INSERT exacto para tu tabla 'usuarios'
$sql = "INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, 'alumno')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $numero_control, $nombre, $apellidos, $email, $pass_hash);

ob_clean();
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error MySQL: " . $stmt->error]);
}
