<?php
require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_user = $data['id_user'] ?? null;

    if (!$id_user) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no recibido']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET estado = 1 WHERE id_user = :id");
        $stmt->execute([':id' => $id_user]);

        $log = $pdo->prepare("
            INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, fecha, descripcion)
            VALUES (:id_usuario, 'ALTA', 'usuarios', :id_afectado, NOW(), 'Usuario dado de alta')
        ");
        $log->execute([
            ':id_usuario' => $id_user, 
            ':id_afectado' => $id_user
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Usuario dado de alta correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
