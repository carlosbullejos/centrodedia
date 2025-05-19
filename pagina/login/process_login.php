<?php
// process_login.php
session_start();
require 'database.php'; // Ajusta la ruta si hace falta

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Recoger y sanear datos
    $email    = trim($_POST["email"]    ?? '');
    $password = $_POST["password"]      ?? '';

    // 2. Preparar consulta
    $stmt = $conexion->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        die("Error en la consulta: " . $conexion->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $user = $resultado->fetch_assoc();
    $stmt->close();

    // 3. Verificar credenciales
    if ($user && password_verify($password, $user["password"])) {
        // Login correcto: guardamos datos en sesión
        $_SESSION["user"]     = $user;
        $_SESSION["username"] = $user["nombre"];
        $_SESSION["rol"]      = $user["rol"];

        header("Location: ../index.php");
        exit();
    } else {
        // Login fallido: guardamos mensaje de error y redirigimos
        $_SESSION['error'] = "Credenciales incorrectas.";
        header("Location: login.php");
        exit();
    }
} else {
    // Si alguien accede por GET, lo mandamos de nuevo al formulario
    header("Location: login.php");
    exit();
}
