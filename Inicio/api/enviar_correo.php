<?php
require 'conexion.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$data = json_decode(file_get_contents("php://input"), true);

// Guardar cita en BD
try {
    $stmt = $pdo->prepare("INSERT INTO citas 
        (nombre, telefono, email, direccion, calle, colonia, ciudad,
         codigo_postal, fecha_cita, hora_cita, rango_horario, tipo_servicio,
         descripcion, medidas, observaciones, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");

    $stmt->execute([
        $data['nombre']        ?? '',
        $data['telefono']      ?? '',
        $data['emailCliente']  ?? null,
        $data['direccion']     ?? '',
        $data['calle']         ?? '',
        $data['colonia']       ?? '',
        $data['ciudad']        ?? '',
        $data['cp']            ?? '',
        $data['fecha']         ?? '',
        $data['hora']          ?? '',
        $data['rangoHorario']  ?? null,
        $data['tipoServicio']  ?? null,
        $data['descripcion']   ?? '',
        $data['medidas']       ?? null,
        $data['observaciones'] ?? null,
    ]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => "Error BD: " . $e->getMessage()]);
    exit;
}

// Enviar correo automático
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aluvidriosav@gmail.com';
    $mail->Password   = 'csckjigsebvnwnjf'; //contraseña de app
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('aluvidriosav@gmail.com', 'AluVidrios');
    $mail->addAddress('aluvidriosav@gmail.com', 'AluVidrios');

    $mail->Subject = "Nueva cita agendada - {$data['fecha']} {$data['hora']}";
    $mail->Body    = "
Nueva solicitud de visita a domicilio:

DATOS DE CONTACTO
Nombre: {$data['nombre']}
Teléfono: {$data['telefono']}
Correo: {$data['emailCliente']}

DIRECCIÓN
{$data['direccion']}
{$data['calle']}, {$data['colonia']}
{$data['ciudad']} C.P. {$data['cp']}

FECHA Y HORA
Fecha: {$data['fecha']}
Hora: {$data['hora']}
Rango: {$data['rangoHorario']}

SERVICIO
Tipo: {$data['tipoServicio']}
Descripción: {$data['descripcion']}
Medidas: {$data['medidas']}
Observaciones: {$data['observaciones']}
    ";

    $mail->send();
    echo json_encode(["ok" => true]);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => "Error correo: " . $mail->ErrorInfo]);
}
?>