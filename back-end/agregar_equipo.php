<?php
header('Content-Type: application/json');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Validar datos
$nombre = trim($_POST['nombre'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$estado = intval($_POST['estado'] ?? 1);

if (!$nombre || !$contrasena) {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit;
}

try {
    // Verificar si el equipo ya existe
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM equipo WHERE nombre = ?");
    $checkStmt->execute([$nombre]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'El equipo ya existe']);
        exit;
    }

    // Insertar nuevo equipo
    $sql = "INSERT INTO equipo (nombre, contrasena, descripcion, estado) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $contrasena, $descripcion, $estado]);

    echo json_encode([
        'ok' => true, 
        'mensaje' => 'Equipo agregado correctamente'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false, 
        'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
