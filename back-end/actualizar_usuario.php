<?php
require 'db.php';
session_start();

$id = $_POST['editId'] ?? 0;
$nombre = $_POST['editNombre'] ?? '';
$apellido = $_POST['editApellido'] ?? '';
$cargo = $_POST['editCargo'] ?? '';
$estado = $_POST['editEstado'] ?? '';

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

  // Obtener cargo del usuario a modificar
  $stmt = $pdo->prepare("SELECT id_cargo FROM usuarios WHERE id_user = ?");
  $stmt->execute([$id]);
  $usuarioModificar = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$usuarioModificar) {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
  }

  $idCargoUsuarioModificar = $usuarioModificar['id_cargo'];

  // Validar permisos: Técnico (2) solo puede modificar Clientes (3)
  if ($idCargoActual === 2) {
    if ($idCargoUsuarioModificar !== 3) {
      http_response_code(403);
      echo json_encode(["ok" => false, "mensaje" => "No tienes permisos para modificar este usuario"]);
      exit;
    }
  }

  // Obtener id_cargo si existe
  $idCargo = null;
  if ($cargo !== '') {
    $stmt = $pdo->prepare("SELECT id_cargo FROM cargos WHERE nombre = ?");
    $stmt->execute([$cargo]);
    $idCargo = $stmt->fetchColumn();

    if (!$idCargo) {
      $stmt = $pdo->prepare("INSERT INTO cargos (nombre) VALUES (?)");
      $stmt->execute([$cargo]);
      $idCargo = $pdo->lastInsertId();
    }
  }

  $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, id_cargo=?, estado=? WHERE id_user=?");
  $stmt->execute([$nombre, $apellido, $idCargo, $estado, $id]);

  echo json_encode(["ok" => true, "mensaje" => "Usuario actualizado correctamente"]);
} catch (Exception $e) {
  echo json_encode(["ok" => false, "mensaje" => "Error: " . $e->getMessage()]);
}
?>