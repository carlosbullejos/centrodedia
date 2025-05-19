<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario de Trabajadores</title>
  <style>
    <?php include('../css/estilos.css'); ?>
  </style>
</head>
<body>
<?php include('../header.php'); ?>

<center>
  <h1>Alta de Trabajador</h1>

  <!-- Mensaje de sesión -->
  <?php if (!empty($_SESSION['mensaje'])):
      $clase = $_SESSION['mensaje']['tipo'] === 'success' ? 'mensaje-success' : 'mensaje-error';
  ?>
    <div class="mensaje <?= htmlspecialchars($clase) ?>">
      <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
  <?php endif; ?>

  <form action="trabajadores2.php" method="post" class="formulario">
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
      <label for="telefono">Teléfono:</label>
      <input type="text" id="telefono" name="telefono" required>
    </div>
    <div class="form-group">
      <label for="puesto">Puesto:</label>
      <input type="text" id="puesto" name="puesto" required>
    </div>
    <div class="form-group">
      <label for="fecha_contratacion">Fecha de contratación:</label>
      <input type="date" id="fecha_contratacion" name="fecha_contratacion" required>
    </div>
    <div class="form-group">
      <label for="especialidad">Especialidad:</label>
      <input type="text" id="especialidad" name="especialidad" required>
    </div>
    <div class="form-group">
      <label for="fecha_nacimiento">Fecha de nacimiento:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
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
