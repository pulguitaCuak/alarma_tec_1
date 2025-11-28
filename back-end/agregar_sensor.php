<?php
header('Content-Type: application/json');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Validar datos
$nombre = trim($_POST['nombre'] ?? '');
$id_tipo_sensor = intval($_POST['id_tipo_sensor'] ?? 0);
$estado = intval($_POST['estado'] ?? 1); // Por defecto Activo (1)
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$nombre || !$id_tipo_sensor) {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit;
}

// Validar que el estado sea 1 o 2
if ($estado !== 1 && $estado !== 2) {
    $estado = 1; // Por defecto Activo
}

try {
    // Verificar si el tipo de sensor existe
    $checkTipoStmt = $pdo->prepare("SELECT COUNT(*) FROM tipo_sensor WHERE id_tipo_sensor = ?");
    $checkTipoStmt->execute([$id_tipo_sensor]);
    
    if ($checkTipoStmt->fetchColumn() == 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'El tipo de sensor no existe']);
        exit;
    }

    // Verificar si el sensor ya existe
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM sensores WHERE nombre = ?");
    $checkStmt->execute([$nombre]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'El sensor ya existe']);
        exit;
    }

    // Insertar nuevo sensor
    $sql = "INSERT INTO sensores (nombre, id_tipo_sensor, estado, descripcion) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $id_tipo_sensor, $estado, $descripcion]);

    echo json_encode([
        'ok' => true, 
        'mensaje' => 'Sensor agregado correctamente'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false, 
        'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>