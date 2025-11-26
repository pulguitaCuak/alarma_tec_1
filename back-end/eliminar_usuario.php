<?php
header('Content-Type: application/json');
require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;

    if (!$id_user) {
        echo json_encode(['ok' => false, 'mensaje' => 'ID de usuario no recibido']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET estado = 2 WHERE id_user = ?");
        $stmt->execute([$id_user]);

        $log = $pdo->prepare("
            INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, fecha, descripcion)
            VALUES (?, 'BAJA', 'usuarios', ?, NOW(), 'Usuario dado de baja')
        ");
        $log->execute([$id_user, $id_user]);

        echo json_encode(['ok' => true, 'mensaje' => 'Usuario dado de baja correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
}
?>
