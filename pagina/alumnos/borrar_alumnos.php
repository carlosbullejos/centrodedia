<?php
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";
    $conexion = new mysqli($servidor, $usuario, $password, $basedatos);

    // Obtener el ID del alumno a borrar
    $borrar = $_GET['id'];

    // Consulta para borrar el alumno de la tabla
    $consultar = "DELETE FROM alumnos WHERE id = $borrar";
    
    // Ejecutar la consulta
    $registros = mysqli_query($conexion, $consultar);

    // Redirigir a la página de consulta de alumnos después de borrar
    header("Location: consultar_alumnos.php");
?>

