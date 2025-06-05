<?php
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";

// Conexión
$conexion = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtenemos el ID a borrar y lo escapamos
$borrar = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($borrar <= 0) {
    die("ID de trabajador inválido.");
}

// Preparamos y ejecutamos el DELETE
$consultar = "DELETE FROM trabajadores WHERE id = $borrar";
if ($conexion->query($consultar) === TRUE) {
    // Redirigimos al listado
    header("Location: consultar_trabajadores.php");
    exit();
} else {
    echo "Error al borrar el registro: " . $conexion->error;
}

$conexion->close();
?>
