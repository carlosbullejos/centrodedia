<?php
session_start();

// Recoger datos
$id               = intval($_POST['id']);
$nombre           = $_POST['nombre']            ?? '';
$apellidos        = $_POST['apellidos']         ?? '';
$dni              = $_POST['dni']               ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento']  ?? '';
$telefono         = $_POST['telefono']          ?? '';
$direccion        = $_POST['direccion']         ?? '';
$localidad        = $_POST['localidad']         ?? '';
$fecha_ingreso    = $_POST['fecha_ingreso']     ?? '';
$dependencia      = $_POST['dependencia']       ?? '';
$patologias       = $_POST['patologias']        ?? '';
$trabajador_id    = intval($_POST['trabajador_id']);

// Conexión
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 1) Validar trabajador
$stmt = $conexion->prepare("SELECT id FROM trabajadores WHERE id = ?");
if (!$stmt) {
    die("❌ Prepare failed (trabajador check): (" . $conexion->errno . ") " . $conexion->error);
}
$stmt->bind_param("i", $trabajador_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El ID de trabajador “{$trabajador_id}” no existe.",
        'tipo'  => 'error'
    ];
    $stmt->close();
    $conexion->close();
    header("Location: actualizar_usuarios.php?id={$id}");
    exit;
}
$stmt->close();

// 2) Prepare actualización
$sql = "
  UPDATE usuarios SET
    nombre           = ?,
    apellidos        = ?,
    dni              = ?,
    fecha_nacimiento = ?,
    telefono         = ?,
    direccion        = ?,
    localidad        = ?,
    fecha_ingreso    = ?,
    dependencia      = ?,
    patologias       = ?,
    trabajador_id    = ?
  WHERE id = ?
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Prepare failed (update): (" . $conexion->errno . ") " . $conexion->error);
}
$stmt->bind_param(
    "ssssssssssii",
    $nombre, $apellidos, $dni, $fecha_nacimiento,
    $telefono, $direccion, $localidad,
    $fecha_ingreso, $dependencia, $patologias,
    $trabajador_id, $id
);

// 3) Ejecutar y manejar errores
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Usuario actualizado correctamente.",
        'tipo'  => 'success'
    ];
    header("Location: consultar_usuarios.php");
    exit;
} else {
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI “{$dni}” ya está registrado.",
            'tipo'  => 'error'
        ];
        $stmt->close();
        $conexion->close();
        header("Location: actualizar_usuarios.php?id={$id}");
        exit;
    }
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al actualizar usuario: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
        'tipo'  => 'error'
    ];
    header("Location: actualizar_usuarios.php?id={$id}");
    exit;
}

$stmt->close();
$conexion->close();
