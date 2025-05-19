<?php
session_start();

// Recoger datos
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

// 1) Validar existencia del trabajador
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
    header("Location: usuarios.php");
    exit;
}
$stmt->close();

// 2) Insertar usuario con todos los campos
$sql = "
  INSERT INTO usuarios 
    (nombre, apellidos, dni, fecha_nacimiento, telefono, direccion, localidad, fecha_ingreso, dependencia, patologias, trabajador_id)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Prepare failed (insert): (" . $conexion->errno . ") " . $conexion->error);
}
$stmt->bind_param(
    "ssssssssssi",
    $nombre, $apellidos, $dni, $fecha_nacimiento,
    $telefono, $direccion, $localidad,
    $fecha_ingreso, $dependencia, $patologias,
    $trabajador_id
);

// 3) Ejecutar y manejar errores
if ($stmt->execute()) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Usuario guardado correctamente.",
        'tipo'  => 'success'
    ];
    header("Location: consultar_usuarios.php");
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
        header("Location: usuarios.php");
        exit;
    }
    // Otro error
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al guardar usuario: (" . $stmt->errno . ") " . htmlspecialchars($stmt->error),
        'tipo'  => 'error'
    ];
    header("Location: usuarios.php");
    exit;
}

$stmt->close();
$conexion->close();
