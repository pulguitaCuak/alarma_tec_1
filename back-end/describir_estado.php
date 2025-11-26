<?php
require_once '../back-end/db.php';

$stmt = $pdo->query("DESCRIBE estado");
$estructura = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($estructura);
