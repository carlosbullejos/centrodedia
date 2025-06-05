<?php
// process_login.php
session_start();
require 'database.php'; // ajusta la ruta si es otro directorio

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// 1) Recoger y sanear datos
$email    = trim($_POST["email"]    ?? '');
$password = $_POST["password"]      ?? '';

// 2) Preparar y ejecutar consulta
$stmt = $conexion->prepare("
    SELECT id, nombre, email, password, rol
    FROM users
    WHERE email = ?
");
if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $nombre, $emailDB, $hash, $rol);

if ($stmt->fetch() && password_verify($password, $hash)) {
    // 3) Login correcto: guardamos datos en sesión
    $_SESSION['user']     = [
        'id'     => $id,
        'nombre' => $nombre,
        'email'  => $emailDB,
        'rol'    => $rol
    ];
    $_SESSION['rol']      = $rol;            // <— Añadido para permisos
    $_SESSION['username'] = $nombre;         // <— Opcional: para compatibilidad

    $stmt->close();
    header("Location: ../index.php");
    exit();
} else {
    // Login fallido
    $_SESSION['error'] = "Credenciales incorrectas.";
    $stmt->close();
    header("Location: login.php");
    exit();
}
