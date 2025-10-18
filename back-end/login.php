<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $contrasenia = trim($_POST["contrasenia"]);

    if (empty($usuario) || empty($contrasenia)) {
        echo "<script>alert('Por favor complete todos los campos.'); window.history.back();</script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = :usuario OR apellido = :usuario");
        $stmt->execute(["usuario" => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificamos que el usuario esté activo (estado = 1)
            if ($user["estado"] != 1) {
                echo "<script>alert('Usuario inactivo. No puede iniciar sesión.'); window.history.back();</script>";
                exit;
            }

            // recordatorio: aplicar password hash después para mayor seguridad
            if ($contrasenia === $user["contrasena"]) {
                $_SESSION["id_user"] = $user["id_user"];
                $_SESSION["nombre"] = $user["nombre"];
                $_SESSION["apellido"] = $user["apellido"];
                $_SESSION["cargo"] = $user["id_cargo"];

                header("Location: ../frontend/estructuraFinal.php");
                exit;
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
