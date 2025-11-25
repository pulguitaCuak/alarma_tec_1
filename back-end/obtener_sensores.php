<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["error"=>"No autorizado"]);
    exit;
}
//no toquen si no saben, si saben hagan lo q quieran pero dejenlo andando
if(!isset($_GET['id_zona'])){
    http_response_code(400);
    echo json_encode(["error"=>"Falta id_zona"]);
    exit;
}

require_once "db.php";
$id_zona = intval($_GET['id_zona']);

try{
    $stmt = $pdo->prepare("
        SELECT 
            zs.id_zona_sensor, 
            zs.id_zona,
            s.id_sensor, 
            s.nombre, 
            s.descripcion,
            s.id_tipo_sensor,
            ts.nombre as tipo_sensor,
            ts.descripcion as descripcion_tipo,
            zs.estado AS estado_asignacion, 
            s.estado AS estado_sensor,
            s.fecha_instalacion,
            zs.fecha_asignacion
        FROM zona_sensor zs
        INNER JOIN sensores s ON zs.id_sensor = s.id_sensor
        LEFT JOIN tipo_sensor ts ON s.id_tipo_sensor = ts.id_tipo_sensor
        WHERE zs.id_zona = ? AND s.estado = 1
        ORDER BY s.id_sensor ASC
    ");
    $stmt->execute([$id_zona]);
    $sensores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($sensores);

}catch(PDOException $e){
    http_response_code(500);
    echo json_encode(["error"=>"Error en la consulta: ".$e->getMessage()]);
}
