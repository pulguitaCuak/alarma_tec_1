<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

require_once 'db.php';

$id_zona_sensor = $_POST['id_zona_sensor'] ?? 0;
$id_sensor = $_POST['id_sensor'] ?? 0;
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$id_tipo_sensor = $_POST['id_tipo_sensor'] ?? 0;
$estado = $_POST['estado'] ?? '';

try {
    if (!$id_zona_sensor || !$id_sensor || !$nombre || $id_tipo_sensor === '' || $estado === '') {
        echo json_encode(["ok" => false, "mensaje" => "Faltan datos requeridos"]);
        exit;
    }

    // Actualizar datos en la tabla sensores
    $stmt = $pdo->prepare("UPDATE sensores SET nombre = ?, descripcion = ?, id_tipo_sensor = ? WHERE id_sensor = ?");
    $stmt->execute([$nombre, $descripcion, $id_tipo_sensor, $id_sensor]);

    // Actualizar estado en la tabla zona_sensor
    $stmt = $pdo->prepare("UPDATE zona_sensor SET estado = ? WHERE id_zona_sensor = ?");
    $stmt->execute([$estado, $id_zona_sensor]);

    echo json_encode(["ok" => true, "mensaje" => "Sensor actualizado correctamente"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "mensaje" => "Error: " . $e->getMessage()]);
}

