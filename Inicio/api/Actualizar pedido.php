<?php
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { http_response_code(405); echo json_encode(['ok' => false, 'error' => 'Método no permitido']); exit; }

$data      = json_decode(file_get_contents('php://input'), true);
$id_pedido = (int)($data['id_pedido'] ?? 0);
$estado    = $data['estado'] ?? '';

$estados_validos = ['pendiente','pagado','enviado','completado','cancelado'];

if (!$id_pedido)                              { echo json_encode(['ok'=>false,'error'=>'ID inválido']); exit; }
if (!in_array($estado, $estados_validos))     { echo json_encode(['ok'=>false,'error'=>'Estado inválido']); exit; }

try {
    $stmt = $pdo->prepare('UPDATE pedidos SET estado = :estado WHERE id_pedido = :id');
    $stmt->execute([':estado' => $estado, ':id' => $id_pedido]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['ok'=>false,'error'=>'Pedido no encontrado']);
        exit;
    }
    echo json_encode(['ok'=>true,'mensaje'=>'Estado actualizado correctamente']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Error: '.$e->getMessage()]);
}