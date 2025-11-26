<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

require_once "db.php";

try {
    $sql = "
        SELECT 
            id_zona,
            descripcion AS nombre,
            estado
        FROM zonas
        WHERE estado != 2
        ORDER BY id_zona ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($zonas);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
