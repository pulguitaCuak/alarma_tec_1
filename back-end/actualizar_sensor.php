<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

require_once 'db.php';

$id_sensor = $_POST['id_sensor'] ?? 0;
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$estado = $_POST['estado'] ?? '';
$accion = $_POST['accion'] ?? 'actualizar';

try {
    // Si la acción es eliminar, usar estado 4 = Suspendido (para dar de baja)
    if ($accion === 'eliminar') {
        $query = "UPDATE sensores SET estado = 4 WHERE id_sensor = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_sensor]);
        
        echo json_encode(["ok" => true, "mensaje" => "Sensor dado de baja correctamente"]);
        exit;
    }

    // Para actualizar, permitir solo estados 1 (Activo) y 2 (Inactivo)
    $updates = [];
    $params = [];

    // Actualizar nombre si se proporciona
    if ($nombre !== '') {
        $updates[] = "nombre = ?";
        $params[] = $nombre;
    }

    // Actualizar descripción si se proporciona
    if ($descripcion !== '') {
        $updates[] = "descripcion = ?";
        $params[] = $descripcion;
    }

    // Actualizar estado si se proporciona (solo 1 o 2)
    if ($estado !== '' && ($estado == 1 || $estado == 2)) {
        $updates[] = "estado = ?";
        $params[] = intval($estado);
    }

    if (empty($updates)) {
        echo json_encode(["ok" => false, "mensaje" => "No hay datos para actualizar"]);
        exit;
    }

    $params[] = $id_sensor;
    $query = "UPDATE sensores SET " . implode(", ", $updates) . " WHERE id_sensor = ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    echo json_encode(["ok" => true, "mensaje" => "Sensor actualizado correctamente"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "mensaje" => "Error: " . $e->getMessage()]);
}
?>