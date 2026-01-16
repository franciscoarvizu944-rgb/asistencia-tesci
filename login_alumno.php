<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

header("Content-Type: application/json; charset=utf-8");
require_once 'conexion.php';

$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

$stmt = $conexion->prepare("SELECT nombre, password FROM usuarios WHERE email = ? AND rol = 'alumno'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        ob_clean();
        echo json_encode(["status" => "ok", "user" => ["nombre" => $user['nombre']]]);
    } else {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
    }
} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Usuario no registrado"]);
}
ob_end_flush();