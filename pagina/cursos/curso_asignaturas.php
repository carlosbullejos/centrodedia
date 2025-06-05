<?php
// curso_asignaturas.php

require '../login/database.php';
session_start();

// 1) Capturo el ID y nombre del curso
$cid = intval($_GET['curso'] ?? 0);
if ($cid <= 0) {
    die("⚠️ Curso no válido.");
}
$stmt = $conexion->prepare("SELECT nombre FROM cursos WHERE id = ?");
$stmt->bind_param("i", $cid);
$stmt->execute();
$stmt->bind_result($cursoNombre);
if (!$stmt->fetch()) {
    die("⚠️ Curso no encontrado.");
}
$stmt->close();

// 2) POST: guardo asignaciones **antes** de imprimir nada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpio y meto las seleccionadas
    $conexion->query("DELETE FROM cursos_asig WHERE curso_id = $cid");
    foreach ($_POST['asig'] ?? [] as $aid) {
        $aid = intval($aid);
        $conexion->query("
          INSERT INTO cursos_asig (curso_id, asignatura_id)
          VALUES ($cid, $aid)
        ");
    }
    header("Location: curso_asignaturas.php?curso=$cid");
    exit;
}

// 3) Incluyo cabecera y estilos
include '../header.php';
?>
<style>
  <?php include '../css/estilos.css'; ?>
</style>
<center>
<main style="padding:80px 20px">
  <h2>Asignaturas del curso “<?= htmlspecialchars($cursoNombre) ?>”</h2>
</center>
 <center>
  <p>
    <a href="asignaturas.php" class="btn btn-primary">
      ➕ Gestionar asignaturas
    </a>
  </p>
</center>
  <?php
    // Traigo todas las asignaturas y las ya asignadas
    $all = $conexion->query("SELECT id, nombre, codigo FROM asignaturas ORDER BY nombre");
    $rows = $conexion
      ->query("SELECT asignatura_id FROM cursos_asig WHERE curso_id = $cid")
      ->fetch_all(MYSQLI_ASSOC);
    $assigned = array_column($rows, 'asignatura_id');
  ?>

  <?php if ($all->num_rows === 0): ?>
    <p>No hay asignaturas definidas. Ve a “Gestionar asignaturas” para crear nuevas.</p>
  <?php else: ?>
    <form method="post" action="?curso=<?= $cid ?>">
      <table class="table">
        <thead>
          <tr>
            <th style="width:1rem;"></th>
            <th>Nombre</th>
            <th>Código</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($r = $all->fetch_assoc()): ?>
            <tr>
              <td>
                <input
                  type="checkbox"
                  name="asig[]"
                  value="<?= $r['id'] ?>"
                  <?= in_array($r['id'], $assigned) ? 'checked' : '' ?>
                >
              </td>
              <td><?= htmlspecialchars($r['nombre']) ?></td>
              <td><?= htmlspecialchars($r['codigo']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <center>
      <div style="margin-top: 1rem;">
        <button type="submit" class="btn btn-success">💾 Guardar asignaciones</button>
        <a href="cursos.php" class="btn btn-secondary">↩️ Volver</a>
      </div>
          </center>
    </form>
  <?php endif; ?>
</main>
