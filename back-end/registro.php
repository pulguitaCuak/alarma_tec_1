<?php
header('Content-Type: application/json');
require('db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Validar datos
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$dni = trim($_POST['dni'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fechaNacimiento = $_POST['fechaNacimiento'] ?? '';

if (!$nombre || !$apellido || !$email || !$password) {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['ok' => false, 'mensaje' => 'Las contraseñas no coinciden']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['ok' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

try {
    // Verificar si el email ya existe
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE mail = ?");
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'El email ya está registrado']);
        exit;
    }

    // Insertar nuevo usuario
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $fecha_creacion = date('Y-m-d H:i:s');
    $estado = 1; // activo
    $id_cargo = 3; // cliente por defecto

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, contrasena, id_cargo, fecha_creacion, estado, mail) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([$nombre, $apellido, $hashedPassword, $id_cargo, $fecha_creacion, $estado, $email]);

    echo json_encode([
        'ok' => true, 
        'mensaje' => 'Registro exitoso. Por favor inicia sesión'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false, 
        'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}

