<?php
require 'database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre       = $_POST["nombre"];
    $email        = $_POST["email"];
    $password     = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $ftp_password = $_POST["ftp_password"];

    // Comprobar si el usuario ya existe
    $stmt = $conexion->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Usuario ya existe
        $_SESSION['error'] = "Este correo ya está registrado. Intenta iniciar sesión o usa otro.";
        header("Location: register.php");
        exit();
    }

    $stmt->close();

    // Insertar nuevo usuario
    $stmt = $conexion->prepare("
        INSERT INTO users (nombre, email, password, ftp_password)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $nombre, $email, $password, $ftp_password);
    $stmt->execute();
    $stmt->close();

    header("Location: login.php?registro=exitoso");
    exit();
}
?>
