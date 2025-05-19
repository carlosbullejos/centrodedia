<?php
session_start();

// 1) Recoger datos del formulario
$nombre           = $_POST['nombre'] ?? '';
$apellidos        = $_POST['apellidos'] ?? '';
$dni              = $_POST['dni'] ?? '';
$telefono         = $_POST['telefono'] ?? '';
$direccion        = $_POST['direccion'] ?? '';
$localidad        = $_POST['localidad'] ?? '';
$fecha_ingreso    = $_POST['fecha_ingreso'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$profesor_id      = $_POST['profesor_id'] ?? '';  // sigue llamándose así en el formulario

// 2) Conexión
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ( $conexion->connect_error ) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 3) Comprobar que el trabajador existe (tabla 'trabajadores')
$stmt = $conexion->prepare("SELECT id FROM trabajadores WHERE id = ?");
if ( ! $stmt ) {
    die("❌ Prepare failed (trabajador check): (" . $conexion->errno . ") " . $conexion->error);
}
$stmt->bind_param("i", $profesor_id);
$stmt->execute();
$stmt->store_result();
if ( $stmt->num_rows === 0 ) {
    $_SESSION['mensaje'] = [
        'texto' => "❌ El ID de trabajador “{$profesor_id}” no existe.",
        'tipo'  => 'error'
    ];
    $stmt->close();
    $conexion->close();
    header("Location: alumnos.php");
    exit;
}
$stmt->close();

// 4) Preparar inserción
$sql = "
    INSERT INTO alumnos 
      (nombre, apellidos, dni, fecha_nacimiento, telefono, direccion, localidad, fecha_ingreso, profesor_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
";
$stmt = $conexion->prepare($sql);
if ( ! $stmt ) {
    die("❌ Prepare failed (insert): (" . $conexion->errno . ") " . $conexion->error);
}

$stmt->bind_param(
    "ssssssssi",
    $nombre, $apellidos, $dni, $fecha_nacimiento,
    $telefono, $direccion, $localidad,
    $fecha_ingreso, $profesor_id
);

// 5) Ejecutar y manejar errores
if ( $stmt->execute() ) {
    $_SESSION['mensaje'] = [
        'texto' => "✅ Alumno guardado correctamente.",
        'tipo'  => 'success'
    ];
    header("Location: consultar_alumnos.php");
    exit;

} else {
    if ( $stmt->errno === 1062 ) {
        $_SESSION['mensaje'] = [
            'texto' => "❌ El DNI “{$dni}” ya está registrado.",
            'tipo'  => 'error'
        ];
        header("Location: alumnos.php");
        exit;
    }
    $_SESSION['mensaje'] = [
        'texto' => "❌ Error al guardar alumno: (" . $stmt->errno . ") " . $stmt->error,
        'tipo'  => 'error'
    ];
    header("Location: alumnos.php");
    exit;
}

// 6) Cerrar conexión
$conexion->close();
