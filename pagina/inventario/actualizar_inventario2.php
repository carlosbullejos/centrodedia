<?php
// actualizar_inventario2.php
session_start();

// 1. Conexión a la base de datos
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";
$conexion  = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 2. Validar y sanear el ID
if (empty($_POST['id']) || !ctype_digit($_POST['id'])) {
    die("ID de inventario inválido.");
}
$id = (int) $_POST['id'];

// 3. Obtener y sanear campos de formulario
$nombre        = $conexion->real_escape_string($_POST['nombre']);
$ubicacion     = $conexion->real_escape_string($_POST['ubicacion']);
$unidades      = (int) $_POST['unidades'];
$fecha_ingreso = $conexion->real_escape_string($_POST['fecha_ingreso']);
$proveedor     = $conexion->real_escape_string($_POST['proveedor']);
$estado        = $conexion->real_escape_string($_POST['estado']);

// 4. Procesar la imagen
$upload_dir = __DIR__ . '/imagenes/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&
    is_uploaded_file($_FILES['imagen']['tmp_name'])
) {
    // Subida de nueva imagen
    $tmp_name      = $_FILES['imagen']['tmp_name'];
    // Sanitizar el nombre (opcional podría aplicar más reglas)
    $imagen_nombre = basename($_FILES['imagen']['name']);
    $destino       = $upload_dir . $imagen_nombre;

    if (!move_uploaded_file($tmp_name, $destino)) {
        die("Error al mover la nueva imagen.");
    }

    // Ruta ABSOLUTA para la URL
    $imagen_path = '/inventario/imagenes/' . $conexion->real_escape_string($imagen_nombre);

} else {
    // No se subió nueva imagen: usamos la ruta actual pasada como campo oculto
    if (empty($_POST['imagen_actual'])) {
        die("Falta la ruta de la imagen actual.");
    }
    // Ya es una ruta absoluta guardada en la BD
    $imagen_path = $conexion->real_escape_string($_POST['imagen_actual']);
}

// 5. Preparar y ejecutar la consulta UPDATE
$sql = "
    UPDATE inventario SET
      nombre        = '$nombre',
      ubicacion     = '$ubicacion',
      unidades      = $unidades,
      fecha_ingreso = '$fecha_ingreso',
      proveedor     = '$proveedor',
      estado        = '$estado',
      imagen        = '$imagen_path'
    WHERE id = $id
";

if (!$conexion->query($sql)) {
    die("❌ Error al actualizar inventario: " . $conexion->error);
}

// 6. Redirigir al listado
header("Location: consultar_inventario.php");
exit();
?>
