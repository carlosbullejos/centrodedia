<?php
session_start();

// Conexión
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// Recoger datos
$id                = intval($_POST['id']);
$nombre            = $_POST['nombre'] ?? '';
$apellidos         = $_POST['apellidos'] ?? '';
$dni               = $_POST['dni'] ?? '';
$fecha_nacimiento  = $_POST['fecha_nacimiento'] ?? '';
$telefono          = $_POST['telefono'] ?? '';
$direccion         = $_POST['direccion'] ?? '';
$localidad         = $_POST['localidad'] ?? '';
$fecha_ingreso     = $_POST['fecha_ingreso'] ?? '';
$profesor_id       = intval($_POST['profesor_id']);

// 1) Validar existencia del trabajador
$stmt = $conexion->prepare("SELECT id FROM trabajadores WHERE id = ?");
if (!$stmt) {
    die("❌ Prepare failed (trabajador check): (" . $conexion->errno . ") " . $conexion->error);
}
$stmt->bind_param("i", $profesor_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El ID de trabajador “{$profesor_id}” no existe.",
        'tipo'  => 'error'
    ];
    $stmt->close();
    $conexion->close();
    header("Location: actualizar_alumnos.php?id={$id}");
    exit;
}
$stmt->close();

// 2) Prepare de la actualización
$sql = "
    UPDATE alumnos SET
        nombre           = ?,
        apellidos        = ?,
        dni              = ?,
        fecha_nacimiento = ?,
        telefono         = ?,
        direccion        = ?,
        localidad        = ?,
        fecha_ingreso    = ?,
        profesor_id      = ?
    WHERE id = ?
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Prepare failed (update): (" . $conexion->errno . ") " . $conexion->error);
}

// 3) Bind de parámetros
$stmt->bind_param(
    'ssssssssii',
    $nombre, $apellidos, $dni, $fecha_nacimiento,
    $telefono, $direccion, $localidad,
    $fecha_ingreso, $profesor_id, $id
);

// 4) Ejecutar y manejar errores
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Alumno actualizado correctamente.",
        'tipo'  => 'success'
    ];
    header("Location: consultar_alumnos.php");
    exit;
} else {
    // DNI duplicado
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI “{$dni}” ya está registrado.",
            'tipo'  => 'error'
        ];
        $stmt->close();
        $conexion->close();
        header("Location: actualizar_alumnos.php?id={$id}");
        exit;
    }
    // Otro error
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al actualizar el alumno: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
        'tipo'  => 'error'
    ];
    header("Location: actualizar_alumnos.php?id={$id}");
    exit;
}

$stmt->close();
$conexion->close();
