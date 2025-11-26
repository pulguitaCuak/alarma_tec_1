<?php
require 'db.php';

$id = $_POST['editId'] ?? 0;
$nombre = $_POST['editNombre'] ?? '';
$apellido = $_POST['editApellido'] ?? '';
$cargo = $_POST['editCargo'] ?? '';
$estado = $_POST['editEstado'] ?? '';

try {
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