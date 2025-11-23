<?php
// Activar la salida de errores al navegador para debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Asegurarse de que solo se envíe JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

try {
    // Incluir la conexión a la base de datos
    require_once 'db.php';

    // Obtener y validar los datos de entrada
    $jsonInput = file_get_contents('php://input');
    if (empty($jsonInput)) {
        throw new Exception('No se recibieron datos');
    }

    $data = json_decode($jsonInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos de entrada inválidos');
    }

    // Validar el email
    if (!isset($data['email'])) {
        throw new Exception('El correo electrónico es requerido');
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Correo electrónico inválido');
    }

    // Generar token único
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Obtener información del usuario y actualizar el token
    $stmt = $pdo->prepare("SELECT id_user, nombre, apellido FROM usuarios WHERE mail = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception('No se encontró ninguna cuenta con ese correo electrónico');
    }
    
    // Guardar el token en la base de datos
    $stmt = $pdo->prepare("UPDATE usuarios SET token_recuperacion = ?, token_recuperacion_expirado = ? WHERE id_user = ?");
    $stmt->execute([$token, $expiry, $usuario['id_user']]);
    
    // Configurar headers CORS
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // Construir el enlace de recuperación dinámicamente
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $resetLink = $protocol . "://" . $host . "/alarma_tec_1/frontend/reset_contra.html?token=" . $token;
    
    // Preparar el correo
    $to = $email;
    $subject = "Recuperación de contraseña - Sistema de Alarma";
    $message = "
    <html>
    <head>
        <title>Recuperación de contraseña</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .button { background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
            .header { color: #333; }
            .footer { margin-top: 20px; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2 class='header'>Recuperación de contraseña</h2>
            <p>Hola <strong>{$usuario['nombre']} {$usuario['apellido']}</strong>,</p>
            <p>Has solicitado restablecer tu contraseña en el Sistema de Alarma. Para continuar con el proceso, haz clic en el siguiente enlace:</p>
            <p style='text-align: center;'>
                <a href='{$resetLink}' class='button'>Restablecer mi contraseña</a>
            </p>
            <p><strong>Importante:</strong> Este enlace expirará en 1 hora por razones de seguridad.</p>
            <p>Si no solicitaste este cambio, puedes ignorar este correo. Tu cuenta permanece segura.</p>
            <div class='footer'>
                <p>Saludos,<br>Equipo de Sistema de Alarma</p>
                <small>Este es un correo automático, por favor no respondas a este mensaje.</small>
            </div>
        </div>
    </body>
    </html>";
    
    // Usar PHPMailer para enviar el correo
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configuración del servidor
        $remitente = 'alejobotttt@gmail.com';
        
        // Habilitar debug de SMTP
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP DEBUG: $str");
        };

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $remitente;
        $mail->Password = 'gnui zmdq zcsq qasc';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // Cambiado a SMTPS
        $mail->Port = 465; // Puerto para SMTPS
        
        // Configuración del correo
        $mail->setFrom($remitente, 'sistema_alarma');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        $mail->send();
        echo json_encode([
            'success' => true,
            'message' => 'Se ha enviado un enlace de recuperación a tu correo electrónico'
        ]);
    } catch (Exception $e) {
        $errorMsg = 'Error al enviar el correo electrónico: ' . $mail->ErrorInfo;
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
} catch (Exception $e) {
    error_log("Error en test.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>