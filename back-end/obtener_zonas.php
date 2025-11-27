<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if (!isset($_GET['id_equipo'])) {
    http_response_code(400);
    echo json_encode(["error" => "Falta id_equipo"]);
    exit;
}

$id_equipo = intval($_GET['id_equipo']);
require_once "db.php";

try {
    $stmt = $pdo->prepare("
        SELECT 
            z.id_zona,
            z.descripcion AS nombre_zona,
            z.descripcion,
            CASE 
                WHEN EXISTS (
                    SELECT 1
                    FROM zona_sensor zs
                    INNER JOIN sensores s ON zs.id_sensor = s.id_sensor
                    WHERE zs.id_zona = z.id_zona
                      AND zs.estado = 1
                      AND s.estado = 1
                ) THEN 'Zona en Peligro'
                ELSE 'Zona Normal'
            END AS estado_general
        FROM equipo_zona ez
        INNER JOIN zonas z ON ez.id_zona = z.id_zona
        WHERE ez.id_equipo = ?
        ORDER BY z.id_zona ASC
    ");

    $stmt->execute([$id_equipo]);
    $zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($zonas);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
