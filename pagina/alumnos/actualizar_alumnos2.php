<?php
// actualizar_alumnos2.php
session_start();

// Recoger y sanitizar datos
$id               = intval($_POST['id'] ?? 0);
$nombre           = $_POST['nombre']            ?? '';
$apellidos        = $_POST['apellidos']         ?? '';
$email            = trim($_POST['email'] ?? '');
$dni              = $_POST['dni']               ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento']  ?? '';
$telefono         = $_POST['telefono']          ?? '';
$direccion        = $_POST['direccion']         ?? '';
$localidad        = $_POST['localidad']         ?? '';
$fecha_ingreso    = $_POST['fecha_ingreso']     ?? '';
$profesor_id      = intval($_POST['profesor_id'] ?? 0);

// 1) Validación longitud DNI (servidor)
$maxDni = 9;
if (mb_strlen($dni) > $maxDni) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El DNI «{$dni}» es demasiado grande. Máximo {$maxDni} caracteres.",
        'tipo'  => 'error'
    ];
    header("Location: actualizar_alumnos.php?id={$id}");
    exit;
}

// 2) Conexión BD
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 3) Verificar profesor existe
$stmt = $conexion->prepare("SELECT id FROM trabajadores WHERE id = ?");
$stmt->bind_param("i", $profesor_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El ID de profesor «{$profesor_id}» no existe.",
        'tipo'  => 'error'
    ];
    $stmt->close();
    $conexion->close();
    header("Location: actualizar_alumnos.php?id={$id}");
    exit;
}
$stmt->close();

// 4) Preparar UPDATE
$sql = "
    UPDATE alumnos SET
        nombre           = ?,
        apellidos        = ?,
        email            = ?,
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
$stmt->bind_param(
    "sssssssssii",
    $nombre, $apellidos, $email, $dni,
    $fecha_nacimiento, $telefono, $direccion,
    $localidad, $fecha_ingreso, $profesor_id,
    $id
);

// 5) Ejecutar y manejar errores
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Alumno actualizado correctamente.",
        'tipo'  => 'success'
    ];
    header("Location: consultar_alumnos.php");
    exit;
} else {
    // Duplicados
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI «{$dni}» o el email «{$email}» ya está registrado.",
            'tipo'  => 'error'
        ];
    } else {
        $_SESSION['mensaje'] = [
            'texto' => "❌ Error al actualizar alumno: ({$stmt->errno}) " . htmlspecialchars($stmt->error),
            'tipo'  => 'error'
        ];
    }
    $stmt->close();
    $conexion->close();
    header("Location: actualizar_alumnos.php?id={$id}");
    exit;
}
