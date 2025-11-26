<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

require_once 'db.php';

$id_usuario = $_POST['id_usuario'] ?? 0;
$cambios = json_decode($_POST['cambios'] ?? '{}', true);

if (!$id_usuario || empty($cambios)) {
    echo json_encode(["ok" => false, "mensaje" => "Datos inválidos"]);
    exit;
}

try {
    $pdo->beginTransaction();

    foreach ($cambios as $id_equipo => $asignar) {
        $id_equipo = intval($id_equipo);
        
        if ($asignar) {
            // Asignar: INSERT si no existe
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO usuario_equipos (id_usuario, id_equipo, fecha_asignacion)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$id_usuario, $id_equipo]);
        } else {
            // Desasignar: DELETE
            $stmt = $pdo->prepare("
                DELETE FROM usuario_equipos
                WHERE id_usuario = ? AND id_equipo = ?
            ");
            $stmt->execute([$id_usuario, $id_equipo]);
        }
    }

    $pdo->commit();
    echo json_encode(["ok" => true, "mensaje" => "Cambios guardados correctamente"]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["ok" => false, "mensaje" => "Error: " . $e->getMessage()]);
}
?>
