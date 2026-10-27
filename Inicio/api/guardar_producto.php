<?php
// aluvidrios/Inicio/api/guardar_producto.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/conexion.php';   // ajusta la ruta si es necesario

// ── Leer body JSON ────────────────────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);

if (!$body) {
    echo json_encode(['ok' => false, 'error' => 'Body JSON inválido']);
    exit;
}

// ── Campos requeridos ─────────────────────────────────────────────────
$id_producto = isset($body['id_producto']) ? (int)$body['id_producto'] : 0;
$nombre      = trim($body['nombre']      ?? '');
$precio      = isset($body['precio'])    ? (float)$body['precio']  : null;
$stock       = isset($body['stock'])     ? (int)$body['stock']     : 0;
$foto_url    = trim($body['foto_url']    ?? '');
$tipo_media  = in_array($body['tipo_media'] ?? '', ['imagen', 'sketchfab'])
               ? $body['tipo_media']
               : 'sketchfab';

if ($nombre === '' || $precio === null) {
    echo json_encode(['ok' => false, 'error' => 'Nombre y precio son obligatorios']);
    exit;
}

try {
    if ($id_producto > 0) {
        // ── ACTUALIZAR ────────────────────────────────────────────────
        $stmt = $pdo->prepare(
            'UPDATE productos
             SET nombre = :nombre,
                 precio = :precio,
                 stock  = :stock,
                 foto_url   = :foto_url,
                 tipo_media = :tipo_media,
                 updated_at = NOW()
             WHERE id_producto = :id'
        );
        $stmt->execute([
            ':nombre'     => $nombre,
            ':precio'     => $precio,
            ':stock'      => $stock,
            ':foto_url'   => $foto_url,
            ':tipo_media' => $tipo_media,
            ':id'         => $id_producto,
        ]);

        echo json_encode([
            'ok'          => true,
            'accion'      => 'actualizado',
            'id_producto' => $id_producto,
        ]);

    } else {
        // ── INSERTAR ──────────────────────────────────────────────────
        $stmt = $pdo->prepare(
            'INSERT INTO productos (nombre, precio, stock, foto_url, tipo_media, activo, created_at, updated_at)
             VALUES (:nombre, :precio, :stock, :foto_url, :tipo_media, 1, NOW(), NOW())'
        );
        $stmt->execute([
            ':nombre'     => $nombre,
            ':precio'     => $precio,
            ':stock'      => $stock,
            ':foto_url'   => $foto_url,
            ':tipo_media' => $tipo_media,
        ]);

        $nuevo_id = (int)$pdo->lastInsertId();

        echo json_encode([
            'ok'          => true,
            'accion'      => 'creado',
            'id_producto' => $nuevo_id,
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
