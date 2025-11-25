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
$estado_zona = $_POST['estado_zona'] ?? '';

try {
    $updates = [];
    $params = [];

    // Actualizar nombre si se proporciona
    if ($nombre_zona !== '') {
        $updates[] = "descripcion = ?";
        $params[] = $nombre_zona;
    }

    // Nota: El estado se calcula dinámicamente en la consulta de obtener_zonas.php
    // basado en el estado de los sensores, así que aquí no lo actualizamos directamente
    // Si necesitas un campo de estado separado, deberías agregar una columna a la tabla

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
