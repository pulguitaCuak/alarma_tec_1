<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

require_once 'db.php';

$id_zona = $_POST['id_zona'] ?? 0;
$nombre_zona = $_POST['nombre_zona'] ?? '';
$accion = $_POST['accion'] ?? 'actualizar'; // 'actualizar' o 'eliminar'

try {
    // Si la acción es eliminar, marcar como eliminado (soft delete - estado = 2)
    if ($accion === 'eliminar') {
        $query = "UPDATE zonas SET estado = 2 WHERE id_zona = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_zona]);
        
        echo json_encode(["ok" => true, "mensaje" => "Zona eliminada correctamente"]);
        exit;
    }

    // De lo contrario, actualizar
    $updates = [];
    $params = [];

    // Actualizar nombre si se proporciona
    if ($nombre_zona !== '') {
        $updates[] = "descripcion = ?";
        $params[] = $nombre_zona;
    }

    if (empty($updates)) {
        echo json_encode(["ok" => false, "mensaje" => "No hay datos para actualizar"]);
        exit;
    }

    $params[] = $id_zona;
    $query = "UPDATE zonas SET " . implode(", ", $updates) . " WHERE id_zona = ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    echo json_encode(["ok" => true, "mensaje" => "Zona actualizada correctamente"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "mensaje" => "Error: " . $e->getMessage()]);
}
?>
