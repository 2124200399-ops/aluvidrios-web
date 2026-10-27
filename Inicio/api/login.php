<?php
require 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$email    = $data['email']    ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["ok" => false, "error" => "Faltan campos"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($password, $usuario['contrasena'])) {
    echo json_encode([
        "ok"     => true,
        "id"     => $usuario['id_usuario'],
        "nombre" => $usuario['nombre'],
        "email"  => $usuario['email'],
        "rol"    => $usuario['rol'],
        "foto"   => $usuario['foto_perfil']
    ]);
} else {
    echo json_encode(["ok" => false, "error" => "Credenciales incorrectas"]);
}
?>