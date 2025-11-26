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
            id_sensor,
            nombre,
            estado,
            id_tipo_sensor,
            descripcion
        FROM sensores
        WHERE estado != 2
        ORDER BY id_sensor ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $sensores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sensores);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
