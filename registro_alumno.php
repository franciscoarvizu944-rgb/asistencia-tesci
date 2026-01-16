<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

header("Content-Type: application/json; charset=utf-8");

// --- CONEXIÓN DIRECTA A RAILWAY (Para evitar fallos de variables) ---
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = 52104; 

$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Fallo de conexión: " . mysqli_connect_error()]);
    exit;
}
mysqli_set_charset($conexion, "utf8");

// --- RECEPCIÓN DE DATOS ---
// Usamos los nombres exactos que pusiste en el index.html
$nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
$apellidos = isset($_POST["apellidos"]) ? trim($_POST["apellidos"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$numero_control = isset($_POST["numero_control"]) ? trim($_POST["numero_control"]) : "";

// Validación básica
if (empty($nombre) || empty($email) || empty($password) || empty($numero_control)) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
    exit;
}

// Seguridad: Encriptar contraseña
$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// --- INSERCIÓN EN LA TABLA ---
// Asegúrate de que tu tabla en Railway se llame 'usuarios' (en minúsculas)
$stmt = $conexion->prepare("INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, 'alumno')");
$stmt->bind_param("sssss", $numero_control, $nombre, $apellidos, $email, $pass_hash);

ob_clean();
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    // Manejo de correos o números de control ya registrados (Error 1062)
    if ($conexion->errno == 1062) {
        echo json_encode(["status" => "error", "message" => "El correo o número de control ya existe"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar: " . $stmt->error]);
    }
}
ob_end_flush();
