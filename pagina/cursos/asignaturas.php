<?php
// asignaturas.php
require '../login/database.php';
session_start();

// Si viene DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conexion->query("DELETE FROM asignaturas WHERE id = $id");
    header('Location: asignaturas.php');
    exit;
}

// Cargo todas
$res = $conexion->query("SELECT * FROM asignaturas ORDER BY nombre");
include '../header.php';
?>
<style>
<?php include '../css/estilos.css'; ?>
</style>

<main style="padding:80px 20px">
    <center>
  <h2>Asignaturas</h2>
  <a href="asignatura_form.php" class="btn">➕ Nueva Asignatura</a>
  </center>
  <table>
    <tr><th>Nombre</th><th>Código</th><th>Acciones</th></tr>
    <?php while($a = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($a['nombre']) ?></td>
        <td><?= htmlspecialchars($a['codigo']) ?></td>
        <td>
          <a href="asignatura_form.php?id=<?= $a['id'] ?>">✏️</a>
          <a href="asignaturas.php?delete=<?= $a['id'] ?>"
             onclick="return confirm('¿Borrar asignatura?')">🗑️</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <center>
      <div style="margin-top: 1rem;">
        <a href="cursos.php" class="btn btn-secondary">↩️ Volver</a>
      </div>
          </center>
</main>
