<?php
// inventario2.php
session_start();

// 1. Procesar la imagen
$upload_dir = __DIR__ . '/imagenes/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$imagen_path = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $imagen_tmp    = $_FILES['imagen']['tmp_name'];
    $imagen_nombre = basename($_FILES['imagen']['name']);
    $destination   = $upload_dir . $imagen_nombre;

    if (move_uploaded_file($imagen_tmp, $destination)) {
        // Guardamos ruta absoluta para la URL
        $imagen_path = '/inventario/imagenes/' . $imagen_nombre;
    } else {
        $_SESSION['error'] = "Error al subir la imagen.";
    }
}

// 2. Conectar a la base de datos
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";
$conexion  = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// 3. Sanear y preparar la inserción
$nombre        = $conexion->real_escape_string($_POST['nombre']);
$ubicacion     = $conexion->real_escape_string($_POST['ubicacion']);
$unidades      = (int) $_POST['unidades'];
$fecha_ingreso = $conexion->real_escape_string($_POST['fecha_ingreso']);
$proveedor     = $conexion->real_escape_string($_POST['proveedor']);
$estado        = $conexion->real_escape_string($_POST['estado']);
$imagen_sql    = $imagen_path
    ? "'" . $conexion->real_escape_string($imagen_path) . "'"
    : "NULL";

$sql = "INSERT INTO inventario (
            nombre,
            ubicacion,
            unidades,
            fecha_ingreso,
            proveedor,
            estado,
            imagen
        ) VALUES (
            '$nombre',
            '$ubicacion',
            $unidades,
            '$fecha_ingreso',
            '$proveedor',
            '$estado',
            $imagen_sql
        )";

if (!$conexion->query($sql)) {
    $_SESSION['error'] = "Error en la base de datos: " . $conexion->error;
}

$conexion->close();

// 4. Redirigir al listado
header("Location: consultar_inventario.php");
exit();
?>
