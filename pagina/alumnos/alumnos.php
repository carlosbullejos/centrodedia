<?php
// alumnos.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alta de Alumnos</title>
  <style>
     <?php include('../css/estilos.css'); ?>
  </style>
</head>
<body>
<?php include('../header.php'); ?>
<center>
    <h1>Formulario de Alumnos</h1>

    <!-- Mensaje de sesión -->
    <?php if (!empty($_SESSION['mensaje'])):
        $tipo  = $_SESSION['mensaje']['tipo'] === 'success' ? 'mensaje-success' : 'mensaje-error';
        $texto = htmlspecialchars($_SESSION['mensaje']['texto']);
    ?>
      <div class="mensaje <?= $tipo ?>">
        <?= $texto ?>
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
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="dni">DNI:</label>
            <input
              type="text"
              id="dni"
              name="dni"
              required
              maxlength="9"
              oninvalid="this.setCustomValidity('❌ El DNI es demasiado grande. Máximo 9 caracteres.')"
              oninput="this.setCustomValidity('')"
            >
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
