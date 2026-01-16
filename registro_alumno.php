<?php
// Limpieza total de errores previos
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// Conexión probada a tu puerto 52104
$conexion = mysqli_connect("shortline.proxy.rlwy.net", "root", "BwdNCiBYEWzVNbBnEWeVgDJZCUZXRyKW", "railway", 52104);

if (!$conexion) {
    die(json_encode(["status" => "error", "message" => "Fallo de conexión externa"]));
}

// Recibir datos exactos del index.html
$numero_control = $_POST["numero_control"] ?? "";
$nombre = $_POST["nombre"] ?? "";
$apellidos = $_POST["apellidos"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

if (empty($numero_control) || empty($email)) {
    die(json_encode(["status" => "error", "message" => "Faltan datos en el formulario"]));
}

$pass_hash = password_hash($password, PASSWORD_BCRYPT);

// INSERT simplificado para tu tabla 'usuarios'
// El ID es automático, por eso no se pone aquí
$sql = "INSERT INTO usuarios (numero_control, nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?, 'alumno')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $numero_control, $nombre, $apellidos, $email, $pass_hash);

ob_clean();
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "¡Registro exitoso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error en Railway: " . $stmt->error]);
}
ob_end_flush();
