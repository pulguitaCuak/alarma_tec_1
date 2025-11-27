<?php
header('Content-Type: application/json');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

$id_zona = $_POST['id_zona'] ?? null;
$cambios = json_decode($_POST['cambios'] ?? '{}', true);

if (!$id_zona) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de zona no proporcionado']);
    exit;
}

try {
    foreach ($cambios as $id_sensor => $asignado) {
        if ($asignado) {
            // Insertar asignación
            $sql = "INSERT IGNORE INTO zona_sensor (id_zona, id_sensor, estado) VALUES (?, ?, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_zona, $id_sensor]);
        } else {
            // Eliminar asignación
            $sql = "DELETE FROM zona_sensor WHERE id_zona = ? AND id_sensor = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_zona, $id_sensor]);
        }
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Cambios guardados correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
