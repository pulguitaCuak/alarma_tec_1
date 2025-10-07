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
        $id=mysqli_query($conexion,"select user.idUser from user join charge on charge.idCharge=user.idCharge where user.user='$usuario' and user.password='$contrasenia' and charge.idCharge=4;") or die("error en el select".mysqli_error($conexion));
        
        //$stmt=mysqli->prepare("select user.idUser from user where user=? and password=?");
        //$stmt=->bind_param('ss',$usuario,$contrasenia);
        //$stmt->execute();

        if($id[idUser]>=1){
            header('Location: ../frontend/equipos-usuario.html');
            exit;
        }
        else{
            echo'
            <script>
                alert("Usuario o contraseña incorrectos");
                window.location="../frontend/login.html";
            </script>
            ';
            exit;
        }
    ?>
</body>
</html>