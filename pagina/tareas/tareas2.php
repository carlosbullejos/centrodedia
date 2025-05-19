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

$nombre      = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$tipo        = $_POST['tipo'];
$usuarios    = isset($_POST['usuarios']) ? $_POST['usuarios'] : [];

// Insertar tarea
$stmt = $conexion->prepare("INSERT INTO tareas (nombre, descripcion, tipo) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $descripcion, $tipo);
$stmt->execute();
$tarea_id = $conexion->insert_id;
$stmt->close();

// Insertar relaciones en la tabla pivote
if (!empty($usuarios)) {
    $stmt2 = $conexion->prepare("INSERT INTO tarea_usuario (tarea_id, usuario_id) VALUES (?, ?)");
    foreach ($usuarios as $uid) {
        $stmt2->bind_param("ii", $tarea_id, $uid);
        $stmt2->execute();
    }
    $stmt2->close();
}

$_SESSION['mensaje'] = ['texto'=>"✅ Tarea creada correctamente.", 'tipo'=>'success'];
header("Location: consultar_tareas.php");
exit;
?>
