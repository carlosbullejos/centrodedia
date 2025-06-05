<?php
// actualizar_trabajadores2.php
session_start();

// Recoger datos
$id                 = intval($_POST['id']);
$nombre             = $_POST['nombre']             ?? '';
$apellidos          = $_POST['apellidos']          ?? '';
$dni                = $_POST['dni']                ?? '';
$telefono           = $_POST['telefono']           ?? '';
$puesto             = $_POST['puesto']             ?? '';
$fecha_contratacion = $_POST['fecha_contratacion'] ?? '';
$especialidad       = $_POST['especialidad']       ?? '';
$fecha_nacimiento   = $_POST['fecha_nacimiento']   ?? '';

// 1) Validación de longitud de DNI (servidor)
$maxDni = 9;
if (mb_strlen($dni) > $maxDni) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El DNI «{$dni}» es demasiado grande. Máximo {$maxDni} caracteres.",
        'tipo'  => 'error'
    ];
    header("Location: actualizar_trabajadores.php?id={$id}");
    exit;
}

// Conexión
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 2) Preparar actualización
$sql = "
  UPDATE trabajadores SET
    nombre              = ?,
    apellidos           = ?,
    dni                 = ?,
    telefono            = ?,
    puesto              = ?,
    fecha_contratacion  = ?,
    especialidad        = ?,
    fecha_nacimiento    = ?
  WHERE id = ?
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Prepare failed: (" . $conexion->errno . ") " . $conexion->error);
}

$stmt->bind_param(
    "ssssssssi",
    $nombre, $apellidos, $dni,
    $telefono, $puesto, $fecha_contratacion,
    $especialidad, $fecha_nacimiento,
    $id
);

// 3) Ejecutar y manejar errores
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Trabajador actualizado correctamente.",
        'tipo'  => 'success'
    ];
} else {
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI «{$dni}» ya está registrado.",
            'tipo'  => 'error'
        ];
        $stmt->close();
        $conexion->close();
        header("Location: actualizar_trabajadores.php?id={$id}");
        exit;
    }
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al actualizar: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
        'tipo'  => 'error'
    ];
}

$stmt->close();
$conexion->close();

// 4) Redirigir al listado
header("Location: consultar_trabajadores.php");
exit;
