<?php
$host = "localhost";
$dbname = "sistema_alarma";
$user = "root";
$pass = "";
//yo uso pdo porque soy re piola y los piola somos piola
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
