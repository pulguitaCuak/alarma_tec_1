<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $sql = "SELECT u.id_user, u.nombre, u.apellido, u.id_cargo, u.fecha_creacion, u.estado, c.nombre AS cargo
            FROM usuarios u
            LEFT JOIN cargos c ON u.id_cargo = c.id_cargo
            ORDER BY u.nombre ASC";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener usuarios: ' . $e->getMessage()]);
}
