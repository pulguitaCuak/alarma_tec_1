<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Verificar permisos: solo administrador (id_cargo == 1)
$cargoSesion = $_SESSION['cargo'] ?? null;
if ($cargoSesion == null || (int)$cargoSesion !== 1) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'mensaje' => 'Acceso denegado: permisos insuficientes']);
    exit;
}

// Recibir id_user via POST (form data or JSON)
$id_user = null;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $body = json_decode(file_get_contents('php://input'), true);
    $id_user = $body['id_user'] ?? null;
} else {
    $id_user = $_POST['id_user'] ?? null;
}

if (!$id_user) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de usuario no recibido']);
    exit;
}

try {
    // Optional: prevent deleting yourself
    if ($id_user == $_SESSION['id_user']) {
        echo json_encode(['ok' => false, 'mensaje' => 'No puede eliminarse a sí mismo']);
        exit;
    }

    // Check for dependent rows in tables that reference usuarios
    $dependantTables = [];
    $tablesToCheck = [
        'usuario_equipos' => 'id_usuario',
        'usuario_suscripcion' => 'id_usuario',
        'registro_sensor_estado' => 'id_usuario',
        'log_sensores' => 'id_usuario',
        'log_eventos' => 'id_usuario'
    ];

    foreach ($tablesToCheck as $table => $col) {
        $q = $pdo->prepare("SELECT COUNT(*) AS cnt FROM $table WHERE $col = :id");
        $q->execute([':id' => $id_user]);
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['cnt'] > 0) {
            $dependantTables[] = $table;
        }
    }

    if (!empty($dependantTables)) {
        echo json_encode(['ok' => false, 'mensaje' => 'No se puede eliminar el usuario. Existen dependencias: ' . implode(', ', $dependantTables) . '. Elimine primero los registros relacionados.']);
        exit;
    }

    // Llamar a DELETE
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_user = :id");
    $stmt->execute([':id' => $id_user]);

    // Registrar evento
    $stmtLog = $pdo->prepare(
        "INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, fecha, descripcion)
         VALUES (:id_usuario, 'DELETE', 'usuarios', :id_afectado, NOW(), 'Usuario eliminado permanentemente')"
    );
    $stmtLog->execute([
        ':id_usuario' => $_SESSION['id_user'] ?? null,
        ':id_afectado' => $id_user
    ]);

    echo json_encode(['ok' => true, 'mensaje' => 'Usuario eliminado permanentemente.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
?>