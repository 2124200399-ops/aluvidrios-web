<?php
require 'conexion.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT c.*, u.nombre AS cliente 
                         FROM citas c
                         JOIN usuarios u ON c.id_usuario = u.id_usuario
                         ORDER BY c.fecha_cita ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO citas (id_usuario, fecha_cita, descripcion, estado)
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['id_usuario'], $data['fecha_cita'], $data['descripcion'], $data['estado']]);
    echo json_encode(["ok" => true, "id" => $pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE citas SET estado=? WHERE id_cita=?");
    $stmt->execute([$data['estado'], $data['id']]);
    echo json_encode(["ok" => true]);
}

if ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $stmt = $pdo->prepare("DELETE FROM citas WHERE id_cita=?");
    $stmt->execute([$data['id']]);
    echo json_encode(["ok" => true]);
}
?>