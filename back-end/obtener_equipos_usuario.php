<?php
header('Content-Type: application/json');
require_once 'db.php';

$id_usuario = $_GET['id_usuario'] ?? 0;

try {
    $sql = "SELECT e.id_equipo, e.nombre, e.estado, e.descripcion
            FROM equipo e
            INNER JOIN usuario_equipos ue ON e.id_equipo = ue.id_equipo
            WHERE ue.id_usuario = ?
            ORDER BY e.nombre ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($equipos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener equipos: ' . $e->getMessage()]);
}
?>
