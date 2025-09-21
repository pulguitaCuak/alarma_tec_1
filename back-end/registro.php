<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <?php
        
        include('conexion.php');

        $stmt = $conexion->prepare("call insertar_usuario(?, ?, ?, ?, ?, ?);");
        $stmt->bind_param('ssiisi',$usuario,$contrasenia,$nacimiento,$dni,$mail,$telefono);
        
        $usuario=$_POST['usuario'];
        $contrasenia=$_POST['contrasenia'];
        $dni=$_POST['dni'];
        $telefono=$_POST['telefono'];
        $nacimiento=$_POST['nacimiento'];
        $mail=$_POST['mail'];
        
        $stmt->execute();

        mysqli_close($conexion);

        header('Location: ..frontend/index.html');
        exit;


    ?>

</body>
</html>