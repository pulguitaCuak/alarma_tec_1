<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_zona'])) {
    try {
        $id_zona = $_POST['id_zona'];

        // Cambiar estado a 2 (eliminado) en lugar de borrar físicamente
        $sql = "UPDATE zonas SET estado = 2 WHERE id_zona = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_zona]);
        
        echo json_encode(["success" => true, "message" => "Zona eliminada correctamente"]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al eliminar la zona: " . $e->getMessage()]);
    }
}
?>