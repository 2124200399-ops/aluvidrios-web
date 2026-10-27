<?php
require 'conexion.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT p.*, c.nombre AS categoria 
                         FROM productos p 
                         LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                         WHERE p.activo = 1
                         ORDER BY p.created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    $nombre      = $_POST['nombre']       ?? '';
    $precio      = $_POST['precio']       ?? 0;
    $stock       = $_POST['stock']        ?? 0;
    $descripcion = $_POST['descripcion']  ?? '';
    $id_cat      = $_POST['id_categoria'] ?? 1;
    $tipo_media  = $_POST['tipo_media']   ?? 'imagen';
    $sketchfab   = $_POST['sketchfab_id'] ?? null;
    $foto_url    = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext        = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($ext), $permitidos)) {
            $nombreArchivo = uniqid('prod_') . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/' . $nombreArchivo);
            $foto_url = 'uploads/' . $nombreArchivo;
        } else {
            echo json_encode(["error" => "Formato no permitido"]);
            exit;
        }
    } elseif ($tipo_media === 'sketchfab' && $sketchfab) {
        $foto_url = $sketchfab;
    }

    $stmt = $pdo->prepare("INSERT INTO productos 
        (id_categoria, nombre, descripcion, precio, stock, foto_url, tipo_media)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_cat, $nombre, $descripcion, $precio, $stock, $foto_url, $tipo_media]);
    echo json_encode(["ok" => true, "id" => $pdo->lastInsertId(), "foto_url" => $foto_url]);
}

if ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $stmt = $pdo->prepare("UPDATE productos SET nombre=?, precio=?, stock=?, descripcion=? WHERE id_producto=?");
    $stmt->execute([$data['nombre'], $data['precio'], $data['stock'], $data['descripcion'], $data['id']]);
    echo json_encode(["ok" => true]);
}

if ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $stmt = $pdo->prepare("UPDATE productos SET activo=0 WHERE id_producto=?");
    $stmt->execute([$data['id']]);
    echo json_encode(["ok" => true]);
}
?>