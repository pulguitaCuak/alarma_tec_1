<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["error"=>"No autorizado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if(!isset($data['id_zona_sensor'])){
    http_response_code(400);
    echo json_encode(["error"=>"Falta id_zona_sensor"]);
    exit;
}

require_once "db.php";
$id_zona_sensor = intval($data['id_zona_sensor']);

try{
    $stmt = $pdo->prepare("SELECT estado FROM zona_sensor WHERE id_zona_sensor = ?");
    $stmt->execute([$id_zona_sensor]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row){
        http_response_code(404);
        echo json_encode(["error"=>"Registro no encontrado"]);
        exit;
    }

    $nuevo_estado = $row['estado'] == 1 ? 2 : 1; 

    $stmt = $pdo->prepare("UPDATE zona_sensor SET estado = ? WHERE id_zona_sensor = ?");
    $stmt->execute([$nuevo_estado, $id_zona_sensor]);

    echo json_encode(["success"=>true, "nuevo_estado"=>$nuevo_estado]);

}catch(PDOException $e){
    http_response_code(500);
    echo json_encode(["error"=>"Error al actualizar: ".$e->getMessage()]);
}
