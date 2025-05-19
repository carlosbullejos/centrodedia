<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumnos</title>
    <style>
     <?php include('../css/estilos.css'); ?>
  </style>
</head>
<?php include('../header.php'); ?>
<body>
    <center>
        <h1>Formulario de Alumnos</h1>

        <!-- Mostrar mensaje de sesión si existe -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert <?= htmlspecialchars($_SESSION['mensaje']['tipo']) ?>">
                <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <form action="alumnos2.php" method="post" class="formulario">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>

            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required>
            </div>

            <div class="form-group">
                <label for="localidad">Localidad:</label>
                <input type="text" id="localidad" name="localidad" required>
            </div>

            <div class="form-group">
                <label for="fecha_ingreso">Fecha de ingreso:</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
            </div>

            <div class="form-group">
                <label for="profesor_id">ID del Profesor:</label>
                <input type="number" id="profesor_id" name="profesor_id" required>
            </div>

            <div class="form-actions">
                <input type="submit" value="Enviar" class="btn">
                <input type="reset" value="Restablecer" class="btn btn-reset">
            </div>
        </form>
    </center>
    <?php include('../footer.php'); ?>
</body>
</html>
