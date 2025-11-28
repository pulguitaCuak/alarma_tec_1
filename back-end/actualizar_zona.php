<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_zona'])) {
    try {
        $id_zona = $_POST['id_zona'];
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $estado = $_POST['estado'] ?? 1;

        // Validar que al menos nombre o descripcion se proporcione
        if (empty($nombre) && empty($descripcion)) {
            http_response_code(400);
            echo json_encode(["ok" => false, "mensaje" => "Debe proporcionar nombre o descripción"]);
            exit;
        }

        // Si solo se proporciona nombre, usarlo como descripción (comportamiento anterior)
        if (!empty($nombre) && empty($descripcion)) {
            $descripcion = $nombre;
        }

        $sql = "UPDATE zonas SET descripcion = ?, estado = ? WHERE id_zona = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$descripcion, $estado, $id_zona]);
        
        echo json_encode(["ok" => true, "mensaje" => "Zona actualizada correctamente"]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["ok" => false, "mensaje" => "Error al actualizar la zona: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["ok" => false, "mensaje" => "ID de zona requerido"]);
}
?>