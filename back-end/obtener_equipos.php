<?php
session_start();
header('Content-Type: application/json');

require_once "db.php"; 

if (!isset($_SESSION['id_user'])) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "
        SELECT 
            id_equipo, 
            nombre, 
            estado, 
            descripcion
        FROM equipo
        ORDER BY id_equipo ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($equipos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
