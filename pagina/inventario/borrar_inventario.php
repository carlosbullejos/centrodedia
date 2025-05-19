<?php
// borrar_inventario.php

// 1. Conexión a la base de datos
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";
$conexion  = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// 2. Validar el parámetro id
if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("ID de inventario inválido.");
}
$borrar = (int) $_GET['id'];

// 3. Ejecutar DELETE
$sql = "DELETE FROM inventario WHERE id = $borrar";
if ($conexion->query($sql) === TRUE) {
    // 4. Redirigir al listado
    header("Location: consultar_inventario.php");
    exit();
} else {
    echo "Error al borrar el elemento de inventario: " . $conexion->error;
}

// 5. Cerrar conexión
$conexion->close();
?>
