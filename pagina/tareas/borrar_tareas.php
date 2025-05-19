<?php
session_start(); // Esto debe estar en la parte superior de todas las páginas que usan sesión
?>
<?php
// borrar_tareas.php

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
    die("ID de tarea inválido.");
}
$borrar = (int) $_GET['id'];

// Ejecutar la consulta DELETE
$sql = "DELETE FROM tareas WHERE id = $borrar";
if ($conexion->query($sql) === TRUE) {
    // Redirigir al listado de tareas
    header("Location: consultar_tareas.php");
    exit();
} else {
    echo "Error al borrar la tarea: " . $conexion->error;
}

$conexion->close();
?>
