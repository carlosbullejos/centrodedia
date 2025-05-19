<?php
session_start();

$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";
$conexion  = new mysqli($servidor, $usuario, $password, $basedatos);

if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

$id          = intval($_POST['id']);
$nombre      = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$tipo        = $_POST['tipo'];
$usuarios    = isset($_POST['usuarios']) ? $_POST['usuarios'] : [];

// Actualizar datos de tarea
$stmt = $conexion->prepare("
    UPDATE tareas SET nombre = ?, descripcion = ?, tipo = ? WHERE id = ?
");
$stmt->bind_param("sssi", $nombre, $descripcion, $tipo, $id);
$stmt->execute();
$stmt->close();

// Eliminar asignaciones previas
$stmtDel = $conexion->prepare("DELETE FROM tarea_usuario WHERE tarea_id = ?");
$stmtDel->bind_param("i", $id);
$stmtDel->execute();
$stmtDel->close();

// Insertar nuevas asignaciones
if (!empty($usuarios)) {
    $stmt2 = $conexion->prepare("INSERT INTO tarea_usuario (tarea_id, usuario_id) VALUES (?, ?)");
    foreach ($usuarios as $uid) {
        $stmt2->bind_param("ii", $id, $uid);
        $stmt2->execute();
    }
    $stmt2->close();
}

// Mensaje y redirección
$_SESSION['mensaje'] = [
    'texto' => "✅ Tarea actualizada correctamente.",
    'tipo'  => 'success'
];
header("Location: consultar_tareas.php");
exit;
?>
