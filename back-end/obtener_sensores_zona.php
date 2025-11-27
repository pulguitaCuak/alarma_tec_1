<?php
header('Content-Type: application/json');
session_start();

if (!isset($_GET['id_zona'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta id_zona']);
    exit;
}

require_once 'db.php';
$id_zona = intval($_GET['id_zona']);

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.id_sensor,
            s.nombre,
            s.descripcion,
            s.estado,
            s.id_tipo_sensor
        FROM zona_sensor zs
        INNER JOIN sensores s ON zs.id_sensor = s.id_sensor
        WHERE zs.id_zona = ?
        ORDER BY s.id_sensor ASC
    ");

    $stmt->execute([$id_zona]);
    $sensores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sensores);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
