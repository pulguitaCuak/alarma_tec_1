<?php
require 'db.php';

$id = $_GET['id'] ?? 0;

$sql = "SELECT u.*, c.nombre AS cargo, e.descripcion AS estado_descripcion
        FROM usuarios u
        LEFT JOIN cargos c ON u.id_cargo = c.id_cargo
        LEFT JOIN estado e ON u.estado = e.id_estado
        WHERE u.id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($usuario);
