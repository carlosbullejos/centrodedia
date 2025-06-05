<?php
// trabajadores2.php
session_start();

// 1) Recoger datos del formulario
$nombre              = $_POST['nombre'] ?? '';
$apellidos           = $_POST['apellidos'] ?? '';
$dni                 = $_POST['dni'] ?? '';
$telefono            = $_POST['telefono'] ?? '';
$puesto              = $_POST['puesto'] ?? '';
$fecha_contratacion  = $_POST['fecha_contratacion'] ?? '';
$especialidad        = $_POST['especialidad'] ?? '';
$fecha_nacimiento    = $_POST['fecha_nacimiento'] ?? '';

// 2) Validación de longitud de DNI (seguridad servidor)
$maxDni = 9;  // <- si cambias el tamaño de columna en BD, ajusta este valor
if (mb_strlen($dni) > $maxDni) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El DNI «{$dni}» es demasiado grande. Máximo {$maxDni} caracteres.",
        'tipo'  => 'error'
    ];
    header("Location: trabajadores.php");
    exit;
}

// 3) Completar si solo vino el año
if (preg_match('/^\d{4}$/', $fecha_contratacion)) {
    $fecha_contratacion .= '-01-01';
}
if (preg_match('/^\d{4}$/', $fecha_nacimiento)) {
    $fecha_nacimiento .= '-01-01';
}

// 4) Validar formatos YYYY-MM-DD
function validar_fecha($fecha) {
    $dt = DateTime::createFromFormat('Y-m-d', $fecha);
    $errs = DateTime::getLastErrors();
    return $dt && !$errs['warning_count'] && !$errs['error_count'];
}

if (!validar_fecha($fecha_contratacion) || !validar_fecha($fecha_nacimiento)) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El formato de fecha debe ser YYYY-MM-DD.",
        'tipo'  => 'error'
    ];
    header("Location: trabajadores.php");
    exit;
}

// 5) Conexión a la base de datos
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 6) Preparar y ejecutar inserción
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
    "ssssssss",
    $nombre, $apellidos, $dni,
    $telefono, $puesto, $fecha_contratacion,
    $especialidad, $fecha_nacimiento
);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Trabajador guardado correctamente.",
        'tipo'  => 'success'
    ];
} else {
    // Duplicado de DNI
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI «{$dni}» ya está registrado.",
            'tipo'  => 'error'
        ];
        $stmt->close();
        $conexion->close();
        header("Location: trabajadores.php");
        exit;
    }
    // Otros errores
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al guardar: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
        'tipo'  => 'error'
    ];
}

$stmt->close();
$conexion->close();

// 7) Redirigir a la lista de trabajadores
header("Location: consultar_trabajadores.php");
exit;
