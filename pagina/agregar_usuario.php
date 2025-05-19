<?php
session_start();
require 'login/database.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre        = $_POST['nombre'];
    $email         = $_POST['email'];
    $password      = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ftp_password  = $_POST['ftp_password'];
    $rol           = $_POST['rol'];

    $stmt = $conexion->prepare("INSERT INTO users (nombre, email, password, ftp_password, rol) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $email, $password, $ftp_password, $rol);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✅ Usuario añadido correctamente.";
        header("Location: admin_usuarios.php");
        exit();
    } else {
        $error = "❌ Error al añadir el usuario: " . $stmt->error;
    }

    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Usuario</title>
    <style>
     <?php include('css/estilos.css'); ?>
  </style>
</head>
<body>
    <div class="formulario-container">
        <h2 style="text-align:center; color:#00796b;">Añadir Nuevo Usuario</h2>
        <?php if (isset($error)) echo "<div class='mensaje'>$error</div>"; ?>
        <form method="POST" class="formulario">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Contraseña FTP</label>
                <input type="password" name="ftp_password" required>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" required>
                    <option value="usuario">Usuario</option>
                    <option value="trabajador">Trabajador</option>
                    <option value="alumno">Alumno</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-actions">
                <input type="submit" value="Crear Usuario">
                <input type="reset" value="Limpiar">
            </div>
        
            <div style="text-align: center; margin-top: 20px;">
                <a href="admin_usuarios.php" class="add-button">← Volver al panel</a>
            </div>

        </form>
    </div>
</body>
</html>
