<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_zona'], $_POST['nombre'])) {
    try {
        $id_zona = $_POST['id_zona'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'] ?? '';
        $estado = $_POST['estado'] ?? 1;

        $sql = "UPDATE zonas SET descripcion = ?, estado = ? WHERE id_zona = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $estado, $id_zona]);
        
        echo json_encode(["success" => true, "message" => "Zona actualizada correctamente"]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al actualizar la zona: " . $e->getMessage()]);
    }
}
?>