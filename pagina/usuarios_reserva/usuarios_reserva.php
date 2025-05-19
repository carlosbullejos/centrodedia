<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../login/database.php'; // if you have a database include

$error = '';
$success = '';

// Procesar si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos
    $nombre      = trim($_POST['nombre'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $telefono    = trim($_POST['telefono'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    // Validar campos
    if ($nombre === '' || $email === '' || $telefono === '' || $descripcion === '') {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Conexión
        $servidor   = "mysql-service:3306";
        $usuario    = "root";
        $password   = "my_passwd";
        $basedatos  = "mi_basedatos";
        $conexion   = new mysqli($servidor, $usuario, $password, $basedatos);
        if ($conexion->connect_error) {
            $error = "Conexión fallida: " . $conexion->connect_error;
        } else {
            // Inserción preparada
            $stmt = $conexion->prepare("INSERT INTO usuarios_reserva (nombre, email, telefono, descripcion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $telefono, $descripcion);
            if ($stmt->execute()) {
                $success = "Reserva registrada correctamente.";
                // Limpiar campos
                $nombre = $email = $telefono = $descripcion = '';
            } else {
                $error = "Error al enviar la reserva: " . $stmt->error;
            }
            $stmt->close();
            $conexion->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Reserva</title>
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<body>
    <?php include('../header.php'); ?>
    <div class="formulario-container">
        <h2 style="text-align:center; color:#00796b;">Realizar Reserva</h2>
        <?php if ($error): ?>
            <div class="mensaje mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="mensaje mensaje-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" class="formulario">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($telefono ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="4" required><?= htmlspecialchars($descripcion ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <input type="submit" value="Enviar Reserva">
                <input type="reset" value="Limpiar">
            </div>
        </form>
    </div>
    <?php include('../footer.php'); ?>
</body>
</html>
