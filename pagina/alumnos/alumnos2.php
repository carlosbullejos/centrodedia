<?php
session_start();

// 1) Recoger datos
$nombre           = $_POST['nombre']            ?? '';
$apellidos        = $_POST['apellidos']         ?? '';
$email            = trim($_POST['email'] ?? '');
$dni              = $_POST['dni']               ?? '';
$telefono         = $_POST['telefono']          ?? '';
$direccion        = $_POST['direccion']         ?? '';
$localidad        = $_POST['localidad']         ?? '';
$fecha_ingreso    = $_POST['fecha_ingreso']     ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento']  ?? '';
$profesor_id      = intval($_POST['profesor_id'] ?? 0);

// 2) Validación de longitud de DNI (servidor)
$maxDni = 9;
if (mb_strlen($dni) > $maxDni) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El DNI «{$dni}» es demasiado grande. Máximo {$maxDni} caracteres.",
        'tipo'  => 'error'
    ];
    header("Location: alumnos.php");
    exit;
}

// 3) Conexión
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 4) Comprobar que el profesor existe
$stmt = $conexion->prepare("SELECT id FROM trabajadores WHERE id = ?");
$stmt->bind_param("i", $profesor_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El ID de profesor “{$profesor_id}” no existe.",
        'tipo'  => 'error'
    ];
    $stmt->close();
    $conexion->close();
    header("Location: alumnos.php");
    exit;
}
$stmt->close();

// 5) Insertar alumno
$sql = "
    INSERT INTO alumnos
      (nombre, apellidos, email, dni, fecha_nacimiento, telefono, direccion, localidad, fecha_ingreso, profesor_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssssssssi",
    $nombre, $apellidos, $email, $dni,
    $fecha_nacimiento, $telefono, $direccion,
    $localidad, $fecha_ingreso, $profesor_id
);
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Alumno guardado correctamente.",
        'tipo'  => 'success'
    ];
    header("Location: consultar_alumnos.php");
    exit;
} else {
    // DNI o email duplicado
    if ($stmt->errno === 1062) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI «{$dni}» o el email «{$email}» ya está registrado.",
            'tipo'  => 'error'
        ];
    } else {
        $_SESSION['mensaje'] = [
            'texto' => "❌ Error al guardar alumno: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
            'tipo'  => 'error'
        ];
    }
    $stmt->close();
    $conexion->close();
    header("Location: alumnos.php");
    exit;
}
