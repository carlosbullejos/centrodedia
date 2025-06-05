<?php
// actualizar_inventario.php
session_start();

// 1. Conexión (igual que en los otros scripts)
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";
$conexion  = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// 2. Validar y obtener ID
if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("ID inválido.");
}
$id = (int) $_GET['id'];

// 3. Cargar datos existentes
$stmt = $conexion->prepare("SELECT * FROM inventario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result   = $stmt->get_result();
$registro = $result->fetch_assoc();
$stmt->close();

if (!$registro) {
    die("Registro no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Inventario</title>
    <style>
        <?php include('../css/estilos.css'); ?>
        .formulario { width: 50%; margin:20px auto; }
        .form-group { margin-bottom:15px; }
        label { display:block; font-weight:bold; margin-bottom:5px; }
        input, select { width:100%; padding:8px; box-sizing:border-box; }
        .actions { text-align:center; margin-top:20px; }
    </style>
</head>
<body>
    <h2 style="text-align:center">Actualizar Registro #<?= $registro['id'] ?></h2>

    <form 
      action="actualizar_inventario2.php" 
      method="post" 
      enctype="multipart/form-data"
      class="formulario"
    >
        <!-- Campo oculto con el ID -->
        <input type="hidden" name="id" value="<?= $registro['id'] ?>">

        <!-- Campo oculto con la ruta actual de la imagen -->
        <input 
          type="hidden" 
          name="imagen_actual" 
          value="<?= htmlspecialchars($registro['imagen']) ?>"
        >

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input 
              type="text" 
              name="nombre" 
              id="nombre" 
              value="<?= htmlspecialchars($registro['nombre']) ?>" 
              required
            >
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación</label>
            <input 
              type="text" 
              name="ubicacion" 
              id="ubicacion" 
              value="<?= htmlspecialchars($registro['ubicacion']) ?>" 
              required
            >
        </div>

        <div class="form-group">
            <label for="unidades">Unidades</label>
            <input 
              type="number" 
              name="unidades" 
              id="unidades" 
              value="<?= (int)$registro['unidades'] ?>" 
              required
            >
        </div>

        <div class="form-group">
            <label for="fecha_ingreso">Fecha de Ingreso</label>
            <input 
              type="date" 
              name="fecha_ingreso" 
              id="fecha_ingreso" 
              value="<?= htmlspecialchars($registro['fecha_ingreso']) ?>" 
              required
            >
        </div>

        <div class="form-group">
            <label for="proveedor">Proveedor</label>
            <input 
              type="text" 
              name="proveedor" 
              id="proveedor" 
              value="<?= htmlspecialchars($registro['proveedor']) ?>" 
              required
            >
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <?php foreach (['nuevo','usado','dañado'] as $opt): ?>
                    <option 
                      value="<?= $opt ?>" 
                      <?= $registro['estado'] === $opt ? 'selected' : '' ?>
                    >
                      <?= ucfirst($opt) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Imagen actual</label>
            <?php if (!empty($registro['imagen'])): ?>
                <div style="margin-bottom:10px;">
                    <img 
                      src="<?= htmlspecialchars($registro['imagen']) ?>" 
                      alt="Actual" 
                      style="max-width:150px; height:auto; border:1px solid #ccc;"
                    >
                </div>
            <?php else: ?>
                <div>No hay imagen subida.</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="imagen">Cambiar imagen </label>
            <input type="file" name="imagen" id="imagen" accept="image/*">
        </div>

        <div class="actions">
            <button type="submit">Guardar Cambios</button>
            <a href="consultar_inventario.php">Cancelar</a>
        </div>
    </form>
</body>
</html>
