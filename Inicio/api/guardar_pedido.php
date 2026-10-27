<?php
// ─────────────────────────────────────────────
//  Inicio/api/guardar_pedido.php
//  Guarda un pedido nuevo en la tabla `pedidos`
// ─────────────────────────────────────────────

require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { http_response_code(405); echo json_encode(['ok' => false, 'error' => 'Método no permitido']); exit; }

// ── Leer JSON del body ────────────────────────
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No se recibieron datos']);
    exit;
}

// ── Campos recibidos ──────────────────────────
$cliente         = trim($data['cliente']         ?? '');
$total           = (float)($data['total']        ?? 0);
$direccion_envio = trim($data['direccion_envio'] ?? '');
$estado          = $data['estado']               ?? 'pendiente';
$productos       = $data['productos']            ?? [];

// id_usuario: si tu sistema tiene login usa el ID real,
// por ahora usamos 1 (el administrador que creamos)
$id_usuario = (int)($data['id_usuario'] ?? 1);

// ── Validaciones ──────────────────────────────
$estados_validos = ['pendiente','pagado','enviado','completado','cancelado'];

if (!$cliente)                                  { echo json_encode(['ok' => false, 'error' => 'El nombre del cliente es obligatorio']);  exit; }
if ($total <= 0)                                { echo json_encode(['ok' => false, 'error' => 'El total debe ser mayor a 0']);           exit; }
if (empty($productos))                          { echo json_encode(['ok' => false, 'error' => 'El pedido no tiene productos']);          exit; }
if (!in_array($estado, $estados_validos))       { echo json_encode(['ok' => false, 'error' => 'Estado inválido']);                      exit; }

// ── Guardar productos como JSON en campo TEXT ─
$productos_json = json_encode($productos, JSON_UNESCAPED_UNICODE);

// ── INSERT ────────────────────────────────────
try {
    $stmt = $pdo->prepare('
        INSERT INTO pedidos (id_usuario, estado, total, direccion_envio, productos)
        VALUES (:id_usuario, :estado, :total, :direccion_envio, :productos)
    ');

    $stmt->execute([
        ':id_usuario'      => $id_usuario,
        ':estado'          => $estado,
        ':total'           => $total,
        ':direccion_envio' => $direccion_envio ?: null,
        ':productos'       => $productos_json,
    ]);

    $nuevo_id = (int)$pdo->lastInsertId();

    echo json_encode([
        'ok'        => true,
        'id_pedido' => $nuevo_id,
        'mensaje'   => 'Pedido guardado correctamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Error al guardar: ' . $e->getMessage()
    ]);
}