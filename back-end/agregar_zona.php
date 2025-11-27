<?php
header('Content-Type: application/json');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Validar datos
$descripcion = trim($_POST['descripcion'] ?? '');
$estado = intval($_POST['estado'] ?? 1);

if (!$descripcion) {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit;
}

try {
    // Verificar si la zona ya existe
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM zonas WHERE descripcion = ?");
    $checkStmt->execute([$descripcion]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'La zona ya existe']);
        exit;
    }

    // Insertar nueva zona
    $sql = "INSERT INTO zonas (descripcion, estado) 
            VALUES (?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$descripcion, $estado]);

    echo json_encode([
        'ok' => true, 
        'mensaje' => 'Zona agregada correctamente'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false, 
        'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>
