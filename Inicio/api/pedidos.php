<?php
require 'conexion.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT p.*, u.nombre AS cliente
                         FROM pedidos p
                         JOIN usuarios u ON p.id_usuario = u.id_usuario
                         ORDER BY p.created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE pedidos SET estado=? WHERE id_pedido=?");
    $stmt->execute([$data['estado'], $data['id']]);
    echo json_encode(["ok" => true]);
}

if ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $stmt = $pdo->prepare("UPDATE pedidos SET estado='cancelado' WHERE id_pedido=?");
    $stmt->execute([$data['id']]);
    echo json_encode(["ok" => true]);
}
?>