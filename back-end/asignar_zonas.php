<?php
header('Content-Type: application/json');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

$id_equipo = $_POST['id_equipo'] ?? null;
$cambios = json_decode($_POST['cambios'] ?? '{}', true);

if (!$id_equipo) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de equipo no proporcionado']);
    exit;
}

try {
    foreach ($cambios as $id_zona => $asignado) {
        if ($asignado) {
            // Insertar asignación
            $sql = "INSERT IGNORE INTO equipo_zona (id_equipo, id_zona) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_equipo, $id_zona]);
        } else {
            // Eliminar asignación
            $sql = "DELETE FROM equipo_zona WHERE id_equipo = ? AND id_zona = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_equipo, $id_zona]);
        }
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Cambios guardados correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
