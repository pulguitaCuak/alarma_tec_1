<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        
        include('conexion.php');

        $usuario=$_POST['usuario'];
        $contrasenia=$_POST['contrasenia'];

        $stmt=mysqli->prepare("select idUser from user where user=? and password=?");
        $stmt=->bind_param('ss',$usuario,$contrasenia);
        $stmt->execute();

        if($stmt>=1){
            header('Location: ../frontend/equipos-usuario.html');
            exit;
        }
        else{
            echo('usuario no registrado');
        }
    ?>
</body>
</html>