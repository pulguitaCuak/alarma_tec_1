<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    // Por ahora permitir sin autenticación para pruebas
    // http_response_code(403);
    // echo json_encode([]);
    // exit;
}

require_once "db.php";

try {
    $sql = "
        SELECT 
            id_tipo_sensor,
            nombre,
            descripcion
        FROM tipo_sensor
        WHERE id_tipo_sensor IS NOT NULL
        ORDER BY nombre ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($tipos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>