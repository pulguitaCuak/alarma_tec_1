<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT id_cargo, nombre FROM cargos ORDER BY nombre ASC");
    $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cargos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener cargos: ' . $e->getMessage()]);
}
