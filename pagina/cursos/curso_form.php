<?php
// curso_form.php

require '../login/database.php';
session_start();

// 1) Capturo el ID (si viene para edición)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// 2) Lógica de creación/edición: debe ir antes de cualquier include/echo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = $conexion->real_escape_string($_POST['nombre']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $fecha_i     = $_POST['fecha_inicio'];
    $fecha_f     = $_POST['fecha_fin'];

    if ($id) {
        // Editar curso existente
        $sql = "
          UPDATE cursos 
          SET nombre='$nombre',
              descripcion='$descripcion',
              fecha_inicio='$fecha_i',
              fecha_fin='$fecha_f'
          WHERE id = $id
        ";
    } else {
        // Crear nuevo curso
        $sql = "
          INSERT INTO cursos (nombre, descripcion, fecha_inicio, fecha_fin)
          VALUES ('$nombre','$descripcion','$fecha_i','$fecha_f')
        ";
    }

    if (!$conexion->query($sql)) {
        die("Error al guardar el curso: " . $conexion->error);
    }

    // Redirijo a la lista **antes** de enviar cualquier HTML
    header('Location: cursos.php');
    exit;
}

// 3) Solo si no es POST cargamos datos para el formulario
$nombre = $descripcion = $fecha_i = $fecha_f = '';
if ($id) {
    $res = $conexion->query("SELECT * FROM cursos WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $nombre      = $row['nombre'];
        $descripcion = $row['descripcion'];
        $fecha_i     = $row['fecha_inicio'];
        $fecha_f     = $row['fecha_fin'];
    }
}

// 4) Ahora sí podemos incluir el header y empezar a enviar HTML
include '../header.php';
?>
<style>
  <?php include '../css/estilos.css'; ?>
</style>
<main style="padding:80px 20px">
  <h2><?= $id ? 'Editar' : 'Crear' ?> Curso</h2>
  <form method="post">
    <label>
      Nombre:<br>
      <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
    </label><br><br>

    <label>
      Descripción:<br>
      <textarea name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
    </label><br><br>

    <label>
      Fecha Inicio:<br>
      <input type="date" name="fecha_inicio" value="<?= $fecha_i ?>">
    </label><br><br>

    <label>
      Fecha Fin:<br>
      <input type="date" name="fecha_fin" value="<?= $fecha_f ?>">
    </label><br><br>

    <button type="submit">Guardar</button>
    <a href="cursos.php">Cancelar</a>
  </form>
</main>
