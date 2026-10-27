<?php
require 'conexion.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $pdo->prepare("INSERT INTO citas 
        (usuario, nombre, telefono, email, direccion, calle, colonia, ciudad, 
         codigo_postal, fecha_cita, hora_cita, rango_horario, tipo_servicio, 
         descripcion, medidas, observaciones, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");

    $stmt->execute([
        $data['usuario']      ?? 0,
        $data['nombre']       ?? '',
        $data['telefono']     ?? '',
        $data['email']        ?? null,
        $data['direccion']    ?? '',
        $data['calle']        ?? '',
        $data['colonia']      ?? '',
        $data['ciudad']       ?? '',
        $data['cp']           ?? '',
        $data['fecha']        ?? '',
        $data['hora']         ?? '',
        $data['rangoHorario'] ?? null,
        $data['tipoServicio'] ?? null,
        $data['descripcion']  ?? '',
        $data['medidas']      ?? null,
        $data['observaciones']?? null,
    ]);

    echo json_encode(["ok" => true, "id" => $pdo->lastInsertId()]);
}

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM citas ORDER BY fecha_cita ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>