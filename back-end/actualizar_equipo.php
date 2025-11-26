<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

require_once 'db.php';

$id_equipo = $_POST['id_equipo'] ?? 0;
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

try {
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

    if (empty($updates)) {
        echo json_encode(["ok" => false, "mensaje" => "No hay datos para actualizar"]);
        exit;
    }

    $params[] = $id_equipo;
    $query = "UPDATE equipo SET " . implode(", ", $updates) . " WHERE id_equipo = ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    echo json_encode(["ok" => true, "mensaje" => "Equipo actualizado correctamente"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "mensaje" => "Error: " . $e->getMessage()]);
}
