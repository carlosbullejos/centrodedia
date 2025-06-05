<?php
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";
    $conexion = new mysqli($servidor, $usuario, $password, $basedatos);

    
    $borrar = $_GET['id'];

   
    $consultar = "DELETE FROM alumnos WHERE id = $borrar";
    
  
    $registros = mysqli_query($conexion, $consultar);

    
    header("Location: consultar_alumnos.php");
?>

