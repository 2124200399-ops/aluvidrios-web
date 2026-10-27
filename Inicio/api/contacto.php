<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require "conexion.php";

// Lee JSON enviado desde el frontend
$data = json_decode(file_get_contents("php://input"), true);

$nombre   = $data['nombre']   ?? '';
$telefono = $data['telefono'] ?? null;
$correo   = $data['correo']   ?? '';
$mensaje  = $data['mensaje']  ?? '';

if ($telefono === '') $telefono = null;

if (!$nombre || !$correo || !$mensaje) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit;
}

try {
    $sql = "INSERT INTO mensajes_contacto (nombre, telefono, correo, mensaje)
            VALUES (:nombre, :telefono, :correo, :mensaje)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nombre',   $nombre);
    $stmt->bindValue(':telefono', $telefono, $telefono === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':correo',   $correo);
    $stmt->bindValue(':mensaje',  $mensaje);
    $stmt->execute();

    echo json_encode(["ok" => true]);

} catch (PDOException $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>