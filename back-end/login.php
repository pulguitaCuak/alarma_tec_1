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
        $usuario=$_POST('usuario');
        $contrasenia=$_POST('contrasenia');
        $idUsuario=mysqli_query($conexion,"select idUser from user where user='$usuario' and password='$contrasenia'");

        //SIN TERMINAR 16/9
    ?>
</body>
</html>