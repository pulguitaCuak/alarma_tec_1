<?php
header('Content-Type: application/json');
require 'db.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

try {
    $sql = "SELECT u.*, c.nombre AS cargo, e.descripcion AS estado_descripcion,
            COUNT(DISTINCT ue.id_user_equipo) AS cantidad_equipos
            FROM usuarios u
            LEFT JOIN cargos c ON u.id_cargo = c.id_cargo
            LEFT JOIN estado e ON u.estado = e.id_estado
            LEFT JOIN usuario_equipos ue ON u.id_user = ue.id_usuario
            WHERE u.id_user = ?
            GROUP BY u.id_user";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo json_encode($usuario);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
