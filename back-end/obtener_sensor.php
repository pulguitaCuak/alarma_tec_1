<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    // Por ahora permitir sin autenticación para pruebas
    // En producción descomentar esta línea:
    // http_response_code(403);
    // echo json_encode(["error" => "No autorizado"]);
    // exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Falta id del sensor"]);
    exit;
}

require_once "db.php";
$id_sensor = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.id_sensor, 
            s.nombre, 
            s.descripcion,
            s.id_tipo_sensor,
            s.estado,
            s.fecha_instalacion,
            COALESCE(ts.nombre, 'Desconocido') as tipo_sensor
        FROM sensores s
        LEFT JOIN tipo_sensor ts ON s.id_tipo_sensor = ts.id_tipo_sensor
        WHERE s.id_sensor = ? AND s.estado != 2
    ");
    $stmt->execute([$id_sensor]);
    $sensor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($sensor) {
        echo json_encode($sensor);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Sensor no encontrado"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
