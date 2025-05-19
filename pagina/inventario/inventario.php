<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include('../header.php'); ?>
    <style>
    <?php include('../css/estilos.css'); ?>

  </style>
  <center>
    <h1>Formulario de Inventario</h1>
    <form class="formulario" action="inventario2.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <input type="text" id="ubicacion" name="ubicacion">
        </div>

        <div class="form-group">
            <label for="unidades">Unidades:</label>
            <input type="number" id="unidades" name="unidades" required>
        </div>

        <div class="form-group">
            <label for="fecha_ingreso">Fecha de Ingreso:</label>
            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
        </div>

        <div class="form-group">
            <label for="proveedor">Proveedor:</label>
            <input type="text" id="proveedor" name="proveedor">
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado">
        </div>

        <div class="form-group">
            <label for="imagen">Imagen del Producto:</label>
            <input type="file" name="imagen" id="imagen" accept="image/*" required><br><br>
        </div>

        <div class="form-actions">
            <input type="submit" value="Enviar">
            <input type="reset" value="Restablecer">
        </div>
    </form>
    </center>
    <footer>
        <p>&copy; 2024 Centro de Día. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
