<?php
// login.php
session_start();

// Recuperamos y limpiamos el posible mensaje de error
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        <?php include('../css/estilos.css'); ?>
        .mensaje {
            color: #b00020;
            background: #fdd;
            padding: 10px;
            border: 1px solid #b00020;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="formulario-container">
        <h2 style="text-align:center; color:#00796b;">Iniciar Sesión</h2>

        <?php if ($error): ?>
            <div class="mensaje"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="process_login.php" method="POST" class="formulario">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-actions">
                <input type="submit" value="Entrar">
                <input type="reset" value="Limpiar">
            </div>
        </form>

        <p style="text-align:center; margin-top: 20px;">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a><br>
            <a href="../index.php">← Volver al inicio</a>
        </p>
    </div>
</body>
</html>
