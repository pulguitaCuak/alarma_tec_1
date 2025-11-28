<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_zona'], $_POST['id_equipo'])) {
    try {
        $id_zona = $_POST['id_zona'];
        $id_equipo = $_POST['id_equipo'];

        // Verificar si ya existe la asignación
        $sqlCheck = "SELECT id_equipo_zona FROM equipo_zona WHERE id_zona = ? AND id_equipo = ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id_zona, $id_equipo]);
        
        if ($stmtCheck->fetch()) {
            echo json_encode(["success" => false, "message" => "El equipo ya está asignado a esta zona"]);
            exit;
        }

        $sql = "INSERT INTO equipo_zona (id_equipo, id_zona) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_equipo, $id_zona]);
        
        echo json_encode(["success" => true, "message" => "Equipo asignado correctamente"]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al asignar el equipo: " . $e->getMessage()]);
    }
}
?>