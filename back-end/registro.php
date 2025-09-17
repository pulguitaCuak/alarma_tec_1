<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        include('conexion.php');

        $usuario=$_POST('usuario');
        $contrasenia=$_POST('contrasenia');
        $dni=$_POST('dni');
        $telefono=$_POST('telefono');
        $nacimiento=$_POST('nacimiento');
        $mail=$_POST('mail');

        mysqli_query($conexion,"call insertar_usuario('$usuario','$contrasenia',$nacimiento,$dni,'$email',$telefono);

        mysqli_close($conexion);

    ?>

</body>
</html>