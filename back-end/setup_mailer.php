<?php
// Configuración para manejar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar si Composer está instalado
$composerTest = shell_exec('composer --version');
if (!$composerTest) {
    die("Composer no está instalado. Por favor, instala Composer primero.");
}

// Crear composer.json si no existe
if (!file_exists('composer.json')) {
    $composerJson = [
        "require" => [
            "phpmailer/phpmailer" => "^6.8"
        ]
    ];
    file_put_contents('composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));
}

// Intentar instalar las dependencias
$output = shell_exec('composer install');
echo $output;

echo "Si no hay errores arriba, PHPMailer se instaló correctamente.\n";
?>