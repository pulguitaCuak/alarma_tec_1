<?php
session_start();
header('Content-Type: application/json');
                    //no me toquen lo que anda manga de gatos
require_once "db.php"; 

if (!isset($_SESSION['id_user'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['id_user'];
$sql = "
    SELECT e.id_equipo, e.nombre, 
           CASE WHEN es.descripcion = 'activo' THEN 'activo' ELSE 'inactivo' END AS estado,
           e.contrasena AS descripcion
    FROM equipo e
    INNER JOIN usuario_equipos ue ON e.id_equipo = ue.id_equipo
    INNER JOIN estado es ON e.estado = es.id_estado
    WHERE ue.id_usuario = :id_usuario
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($equipos);
?>
