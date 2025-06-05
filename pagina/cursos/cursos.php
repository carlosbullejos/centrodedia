<?php
require '../login/database.php'; 
include '../header.php';

// Obtenemos todos los campos (nombre, descripción, fechas, etc.)
$result = $conexion->query("SELECT * FROM cursos ORDER BY creado_at DESC");
?>
<style>
    <?php include('../css/estilos.css'); ?>
</style>
<main style="padding:80px 20px">
  <center>
    <h2>Cursos</h2>
    <a href="curso_form.php" class="btn">➕ Nuevo curso</a>
  </center>

  <table border="1" class="tabla-listado">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Descripción</th>  <!-- Nueva columna -->
        <th>Inicio</th>
        <th>Fin</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while($curso = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($curso['nombre']) ?></td>
          <td><?= nl2br(htmlspecialchars($curso['descripcion'])) ?></td> <!-- Aquí mostramos descripción -->
          <td><?= htmlspecialchars($curso['fecha_inicio']) ?></td>
          <td><?= htmlspecialchars($curso['fecha_fin']) ?></td>
          <td>
            <a href="curso_form.php?id=<?= $curso['id'] ?>">✏️</a>
            <a href="curso_delete.php?id=<?= $curso['id'] ?>" onclick="return confirm('¿Borrar?')">🗑️</a>
            <a href="curso_asignaturas.php?curso=<?= $curso['id'] ?>">📚 Asignaturas</a>
            <a href="curso_matricula.php?curso=<?= $curso['id'] ?>">👩‍🎓 Matricular</a>
            <a href="curso_notas.php?curso=<?= $curso['id'] ?>">📝 Notas</a>
            <a href="solicitudes.php?curso=<?= $curso['id'] ?>">📨 Solicitudes</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>
