<?php
// process_register.php
require 'database.php';
session_start();

// 1) Sólo vía POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}

// 2) Recoger y sanear
$nombre       = trim($_POST["nombre"]    ?? '');
$email        = trim($_POST["email"]     ?? '');
$password     = password_hash($_POST["password"] ?? '', PASSWORD_BCRYPT);
$ftp_password = trim($_POST["ftp_password"] ?? '');

// 3) Validar obligatorios
if ($nombre === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Nombre y email válidos son obligatorios.";
    header("Location: register.php");
    exit();
}

// 4) Comprobar duplicados
$stmt = $conexion->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "Este correo ya está registrado.";
    $stmt->close();
    header("Location: register.php");
    exit();
}
$stmt->close();

// 5) Insertar sólo en users
$stmt = $conexion->prepare("
    INSERT INTO users (nombre, email, password, ftp_password, rol)
    VALUES (?, ?, ?, ?, 'alumno')
");
$stmt->bind_param("ssss", $nombre, $email, $password, $ftp_password);
$stmt->execute();
$stmt->close();

// 6) Redirigir al login
header("Location: login.php?registro=exitoso");
exit();
