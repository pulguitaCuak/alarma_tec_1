<?php
header('Content-Type: application/json');
require_once 'db.php';

// Recibir datos
$data = json_decode(file_get_contents('php://input'), true);
$token = isset($data['token']) ? filter_var($data['token'], FILTER_SANITIZE_STRING) : null;
$password = isset($data['password']) ? $data['password'] : null;

if (!$token || !$password) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Verificar si el token existe y no ha expirado
    $stmt = $pdo->prepare("SELECT id_user FROM usuarios WHERE token_recuperacion = ? AND token_recuperacion_expirado >= CURDATE()");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['error' => 'El enlace de recuperación es inválido o ha expirado']);
        $pdo->rollBack();
        exit;
    }

    // Hash de la nueva contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Actualizar la contraseña del usuario y limpiar el token
    $stmt = $pdo->prepare("UPDATE usuarios SET contrasena = ?, token_recuperacion = NULL, token_recuperacion_expirado = NULL WHERE id_user = ?");
    $stmt->execute([$hashedPassword, $usuario['id_user']]);

    // Registrar en el log
    $stmt = $pdo->prepare("
        INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
        VALUES (?, 'UPDATE', 'usuarios', ?, 'Cambio de contraseña por recuperación')
    ");
    $stmt->execute([$usuario['id_user'], $usuario['id_user']]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    echo json_encode(['error' => 'Ha ocurrido un error. Por favor, intenta más tarde.']);
}
?>