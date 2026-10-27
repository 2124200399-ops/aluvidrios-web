<?php
require 'conexion.php';

$data  = json_decode(file_get_contents("php://input"), true);
$nombre = $data['nombre'] ?? '';
$email  = $data['email']  ?? '';
$foto   = $data['foto']   ?? null;

if (!$email) {
    echo json_encode(["ok" => false, "error" => "Falta el correo"]);
    exit;
}

// Buscar si ya existe el usuario con ese email
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    // No existe — crearlo automáticamente
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena, rol, foto_perfil)
                           VALUES (?, ?, ?, 'cliente', ?)");
    $stmt->execute([
        $nombre,
        $email,
        password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT), // contraseña aleatoria
        $foto
    ]);
    $id = $pdo->lastInsertId();
} else {
    $id = $usuario['id_usuario'];
}

echo json_encode([
    "ok"     => true,
    "id"     => $id,
    "nombre" => $nombre,
    "email"  => $email,
    "foto"   => $foto,
    "rol"    => "cliente"
]);
?>