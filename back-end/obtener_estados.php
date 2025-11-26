<?php
require_once '../back-end/db.php';

$stmt = $pdo->query("SELECT id_estado, nombre FROM estado");
$estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($estados);
