<?php
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";
$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

$id = $_GET['id'];  // Obtener el ID de la URL

// Consulta SQL para eliminar el registro de la tabla usuarios_reserva
$consultar = "DELETE FROM usuarios_reserva WHERE id='$id'";

// Ejecutar la consulta
if ($conexion->query($consultar) === TRUE) {
    header("Location: consultar_usuarios_reserva.php");
} else {
    echo "Error al eliminar el usuario: " . $conexion->error;
}

$conexion->close();

exit;
?>
