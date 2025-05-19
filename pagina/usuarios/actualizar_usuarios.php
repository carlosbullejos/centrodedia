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
  <title>Actualizar Usuario</title>
  <style>
    <?php include('../css/estilos.css'); ?>
  </style>
</head>
<body>
<?php include('../header.php'); ?>

<?php
// Cargar datos existentes
$conexion = new mysqli("mysql-service:3306", "root", "my_passwd", "mi_basedatos");
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}
$id = intval($_GET['id']);
$stmt = $conexion->prepare("
  SELECT nombre, apellidos, dni, fecha_nacimiento, telefono, direccion, localidad, fecha_ingreso, dependencia, patologias, trabajador_id
    FROM usuarios WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $apellidos, $dni, $fecha_nacimiento, $telefono, $direccion, $localidad, $fecha_ingreso, $dependencia, $patologias, $trabajador_id);
$stmt->fetch();
$stmt->close();
$conexion->close();
?>

<center>
  <h1>Actualizar Usuario</h1>

  <!-- Mensaje de sesión -->
  <?php if (!empty($_SESSION['mensaje'])):
    $clase = $_SESSION['mensaje']['tipo'] === 'success' ? 'mensaje-success' : 'mensaje-error';
  ?>
    <div class="mensaje <?= htmlspecialchars($clase) ?>">
      <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
  <?php endif; ?>

  <form action="actualizar_usuarios2.php" method="post" class="formulario">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-group">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($nombre) ?>">
    </div>

    <div class="form-group">
      <label for="apellidos">Apellidos:</label>
      <input type="text" id="apellidos" name="apellidos" required value="<?= htmlspecialchars($apellidos) ?>">
    </div>

    <div class="form-group">
      <label for="dni">DNI:</label>
      <input type="text" id="dni" name="dni" required value="<?= htmlspecialchars($dni) ?>">
    </div>

    <div class="form-group">
      <label for="fecha_nacimiento">Fecha de nacimiento:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="<?= htmlspecialchars($fecha_nacimiento) ?>">
    </div>

    <div class="form-group">
      <label for="telefono">Teléfono:</label>
      <input type="text" id="telefono" name="telefono" required value="<?= htmlspecialchars($telefono) ?>">
    </div>

    <div class="form-group">
      <label for="direccion">Dirección:</label>
      <input type="text" id="direccion" name="direccion" required value="<?= htmlspecialchars($direccion) ?>">
    </div>

    <div class="form-group">
      <label for="localidad">Localidad:</label>
      <input type="text" id="localidad" name="localidad" required value="<?= htmlspecialchars($localidad) ?>">
    </div>

    <div class="form-group">
      <label for="fecha_ingreso">Fecha de ingreso:</label>
      <input type="date" id="fecha_ingreso" name="fecha_ingreso" required value="<?= htmlspecialchars($fecha_ingreso) ?>">
    </div>

    <div class="form-group">
      <label for="dependencia">Dependencia:</label>
      <input type="text" id="dependencia" name="dependencia" required value="<?= htmlspecialchars($dependencia) ?>">
    </div>

    <div class="form-group">
      <label for="patologias">Patologías:</label>
      <input type="text" id="patologias" name="patologias" value="<?= htmlspecialchars($patologias) ?>">
    </div>

    <div class="form-group">
      <label for="trabajador_id">ID de Trabajador:</label>
      <input type="number" id="trabajador_id" name="trabajador_id" required value="<?= htmlspecialchars($trabajador_id) ?>">
    </div>

    <div class="form-actions">
      <input type="submit" value="Actualizar" class="btn">
      <input type="reset" value="Restablecer" class="btn btn-reset">
    </div>
  </form>
</center>

<?php include('../footer.php'); ?>
</body>
</html>
