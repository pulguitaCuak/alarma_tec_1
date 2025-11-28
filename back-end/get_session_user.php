<?php
header('Content-Type: application/json');
session_start();

// Devolver el ID del usuario de la sesión
if (isset($_SESSION['id_user'])) {
    echo json_encode([
        'id_user' => $_SESSION['id_user'],
        'nombre' => $_SESSION['nombre'] ?? null,
        'apellido' => $_SESSION['apellido'] ?? null,
        'cargo' => $_SESSION['cargo'] ?? null
    ]);
} else {
    echo json_encode(['id_user' => null]);
}
