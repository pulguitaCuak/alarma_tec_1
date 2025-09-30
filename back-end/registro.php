<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <?php
        header('Location: ../frontend/index.html');
        include('conexion.php');

        //$stmt = $conexion->prepare("insert into user(user,password,idCharge,DNI,birthdate,email,phone) values (?,?,?,?,?,?,?);");
        //$stmt->bind_param('ssiissi',$usuario,$contrasenia,$cargo,$dni,$nacimiento,$mail,$telefono);
        
        $usuario=$_POST['usuario'];
        $contrasenia=$_POST['contrasenia'];
        //$dni=$_POST['dni'];
        $telefono=$_POST['telefono'];
        //$nacimiento=$_POST['nacimiento'];
        $mail=$_POST['mail'];
        $cargo=4;

        mysqli_query($conexion,"insert into user(user,password,idCharge,email,phone) values ('$usuario','$contrasenia',$cargo,'$email',$telefono);") or die("error en el insert");
        
        //$stmt->execute();

        mysqli_close($conexion);


        exit;


    ?>

</body>
</html>