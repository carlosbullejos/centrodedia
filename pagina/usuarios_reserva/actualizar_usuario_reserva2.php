<?php
// Obtener los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$descripcion = $_POST['descripcion'];

// Conectar a la base de datos
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";
$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta SQL para actualizar los datos del usuario
$consultar = "UPDATE usuarios_reserva SET 
    nombre = '$nombre',
    email = '$email',
    telefono = '$telefono',
    descripcion = '$descripcion'
    WHERE id = $id";

// Ejecutar la consulta de actualización
$conexion->query($consultar);

// Redirigir después de la actualización
header("Location: consultar_usuarios_reserva.php");
exit;
?>
