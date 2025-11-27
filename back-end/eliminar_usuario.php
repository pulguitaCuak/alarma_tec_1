<?php
header('Content-Type: application/json');
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;

    if (!$id_user) {
        echo json_encode(['ok' => false, 'mensaje' => 'ID de usuario no recibido']);
        exit;
    }

    try {
        // Obtener cargo del usuario actual (de sesión)
        $idUsuarioActual = $_SESSION['id_user'] ?? null;
        $idCargoActual = 3; // Por defecto Cliente
        
        if ($idUsuarioActual) {
            $stmt = $pdo->prepare("SELECT id_cargo FROM usuarios WHERE id_user = ?");
            $stmt->execute([$idUsuarioActual]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $idCargoActual = $result['id_cargo'];
            }
        }

        // Obtener cargo del usuario a dar de baja
        $stmt = $pdo->prepare("SELECT id_cargo FROM usuarios WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $usuarioBaja = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuarioBaja) {
            echo json_encode(['ok' => false, 'mensaje' => 'Usuario no encontrado']);
            exit;
        }

        $idCargoUsuarioBaja = $usuarioBaja['id_cargo'];

        // Validar permisos: Técnico (2) solo puede dar de baja a Clientes (3)
        if ($idCargoActual === 2) {
            if ($idCargoUsuarioBaja !== 3) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'mensaje' => 'No tienes permisos para dar de baja a este usuario']);
                exit;
            }
        }

        $stmt = $pdo->prepare("UPDATE usuarios SET estado = 2 WHERE id_user = ?");
        $stmt->execute([$id_user]);

        $log = $pdo->prepare("
            INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, fecha, descripcion)
            VALUES (?, 'BAJA', 'usuarios', ?, NOW(), 'Usuario dado de baja')
        ");
        $log->execute([$idUsuarioActual, $id_user]);

        echo json_encode(['ok' => true, 'mensaje' => 'Usuario dado de baja correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
}
?>
