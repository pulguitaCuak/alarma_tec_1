<?php
header('Content-Type: application/json');
include 'db.php';

// Obtener todos los equipos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] == 'obtener_equipos') {
    $stmt = $pdo->query("SELECT e.*, es.descripcion as estado_desc FROM equipo e LEFT JOIN estado es ON e.estado = es.id_estado");
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($equipos);
    exit;
}

// Obtener usuarios para asignar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] == 'obtener_usuarios') {
    $stmt = $pdo->query("SELECT u.id_user, u.nombre, u.apellido, c.nombre as cargo 
                         FROM usuarios u 
                         LEFT JOIN cargos c ON u.id_cargo = c.id_cargo 
                         WHERE u.estado = 1");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
    exit;
}

// Crear nuevo equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] == 'crear_equipo') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
        exit;
    }

    // Encriptar contraseña
    $contrasenaHash = !empty($contrasena) ? password_hash($contrasena, PASSWORD_DEFAULT) : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO equipo (nombre, descripcion, contrasena, estado) VALUES (?, ?, ?, 1)");
        $stmt->execute([$nombre, $descripcion, $contrasenaHash]);
        
        echo json_encode(['success' => true, 'message' => 'Equipo creado correctamente', 'id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear el equipo: ' . $e->getMessage()]);
    }
    exit;
}

// Asignar equipo a usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] == 'asignar_equipo') {
    $id_usuario = $_POST['id_usuario'] ?? '';
    $id_equipo = $_POST['id_equipo'] ?? '';

    if (empty($id_usuario) || empty($id_equipo)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
        exit;
    }

    try {
        // Verificar si ya existe la asignación
        $stmt = $pdo->prepare("SELECT * FROM usuario_equipos WHERE id_usuario = ? AND id_equipo = ?");
        $stmt->execute([$id_usuario, $id_equipo]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El equipo ya está asignado a este usuario']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO usuario_equipos (id_usuario, id_equipo) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $id_equipo]);
        
        echo json_encode(['success' => true, 'message' => 'Equipo asignado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al asignar el equipo: ' . $e->getMessage()]);
    }
    exit;
}

// Obtener equipos asignados a un usuario
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] == 'equipos_usuario') {
    $id_usuario = $_GET['id_usuario'] ?? '';
    
    $stmt = $pdo->prepare("SELECT e.*, ue.fecha_asignacion 
                          FROM equipo e 
                          JOIN usuario_equipos ue ON e.id_equipo = ue.id_equipo 
                          WHERE ue.id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($equipos);
    exit;
}
?>