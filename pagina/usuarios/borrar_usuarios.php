<?php
// borrar_usuarios.php
// Conexión a la base de datos
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";

$conexion = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Validar que venga un id válido por GET
if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("ID inválido.");
}
$borrar = (int) $_GET['id'];

// Preparar y ejecutar el DELETE
$sql = "DELETE FROM usuarios WHERE id = $borrar";
if ($conexion->query($sql) === TRUE) {
    // Redirigir al listado
    header("Location: consultar_usuarios.php");
    exit();
} else {
    echo "Error al borrar el usuario: " . $conexion->error;
}

$conexion->close();
?>
