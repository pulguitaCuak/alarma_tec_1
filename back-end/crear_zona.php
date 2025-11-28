<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    try {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'] ?? '';
        $estado = $_POST['estado'] ?? 1;

        $sql = "INSERT INTO zonas (descripcion, estado) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $estado]);
        
        echo json_encode(["success" => true, "message" => "Zona creada correctamente"]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al crear la zona: " . $e->getMessage()]);
    }
}
?>