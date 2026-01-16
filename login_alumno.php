<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

header("Content-Type: application/json; charset=utf-8");

// --- CONEXIÓN DIRECTA A RAILWAY ---
$host = "shortline.proxy.rlwy.net"; 
$user = "root";            
$pass = "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW";            
$db   = "railway";
$port = 52104; 

$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Error de conexión con la base de datos"]);
    exit;
}
mysqli_set_charset($conexion, "utf8");

// --- PROCESO DE LOGIN ---
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

if (empty($email) || empty($password)) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Email y contraseña son obligatorios"]);
    exit;
}

// Buscamos al usuario por su email en la tabla 'usuarios'
$stmt = $conexion->prepare("SELECT nombre, password FROM usuarios WHERE email = ? AND rol = 'alumno'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Verificamos la contraseña encriptada (Hash)
    if (password_verify($password, $user['password'])) {
        ob_clean();
        echo json_encode([
            "status" => "ok", 
            "user" => ["nombre" => $user['nombre']]
        ]);
    } else {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
    }
} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "El usuario no existe o no es alumno"]);
}
ob_end_flush();
