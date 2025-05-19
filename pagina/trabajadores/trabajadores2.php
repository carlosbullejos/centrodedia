<?php
session_start();

// Recoger datos del formulario
$nombre              = $_POST['nombre'] ?? '';
$apellidos           = $_POST['apellidos'] ?? '';
$dni                 = $_POST['dni'] ?? '';
$telefono            = $_POST['telefono'] ?? '';
$puesto              = $_POST['puesto'] ?? '';
$fecha_contratacion  = $_POST['fecha_contratacion'] ?? '';
$especialidad        = $_POST['especialidad'] ?? '';
$fecha_nacimiento    = $_POST['fecha_nacimiento'] ?? '';

// Conexión
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// Preparar inserción
$sql = "
  INSERT INTO trabajadores
    (nombre, apellidos, dni, telefono, puesto, fecha_contratacion, especialidad, fecha_nacimiento)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Prepare failed: (" . $conexion->errno . ") " . $conexion->error);
}

$stmt->bind_param(
    "sssssiss",
    $nombre, $apellidos, $dni,
    $telefono, $puesto, $fecha_contratacion,
    $especialidad, $fecha_nacimiento
);

// Ejecutar y manejar errores
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Trabajador guardado correctamente.",
        'tipo'  => 'success'
    ];
} else {
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI “{$dni}” ya está registrado.",
            'tipo'  => 'error'
        ];
        $stmt->close();
        $conexion->close();
        header("Location: trabajadores.php");
        exit;
    }
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al guardar: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
        'tipo'  => 'error'
    ];
}

$stmt->close();
$conexion->close();
header("Location: consultar_trabajadores.php");
exit;
