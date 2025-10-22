<?php
require('db.php');

$stmt = $pdo->prepare("insert into usuarios(nombre,apellido,contrasena,id_cargo,fecha_creacion,estado) values (:nombre,:apellido,:contrasena,:id_cargo,:fecha_creacion,:estado);");

$usuario = $_POST['usuario'];
$apellido = $_POST['apellido'];
$contrasenia = password_hash($_POST['contrasenia'],PASSWORD_DEFAULT);
$fecha_creacion = date('Y-m-d H:i:s');
$estado = 1; // activo
$cargo = $_POST['cargo'];
//$dni=$_POST['dni'];
//$telefono = $_POST['telefono'];
//$nacimiento=$_POST['nacimiento'];
//$mail = $_POST['mail'];

$stmt->execute([
    ':nombre' => $usuario,
    ':apellido' => $apellido,
    ':contrasena' => $contrasenia,
    ':id_cargo' => $cargo,
    ':fecha_creacion' => $fecha_creacion,
    ':estado' => $estado
]);
echo "Registro exitoso";
$pdo = null;

header("Location: ../frontend/estructuraAdministrarUsuarios.php");
exit;

