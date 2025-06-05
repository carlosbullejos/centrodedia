<?php
// curso_matricula.php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Traer los IDs de alumno ya matriculados
    $rows = $conexion
      ->query("SELECT alumno_id FROM matriculas WHERE curso_id = $cid")
      ->fetch_all(MYSQLI_ASSOC);
    $actual = array_column($rows, 'alumno_id');

    // 2) Los que el usuario envía:
    $seleccionados = array_map('intval', $_POST['alumno'] ?? []);

    // 3) Calcular diferencias
    $a_eliminar = array_diff($actual, $seleccionados);
    $a_insertar = array_diff($seleccionados, $actual);

    // 4) Eliminar solo los desmarcados
    foreach ($a_eliminar as $aid) {
        $conexion->query("
            DELETE FROM matriculas
             WHERE curso_id = $cid
               AND alumno_id = $aid
        ");
        // sus notas se borrarán automáticamente por ON DELETE CASCADE
    }

    // 5) Insertar solo los nuevos
    foreach ($a_insertar as $aid) {
        $conexion->query("
            INSERT INTO matriculas (alumno_id, curso_id)
            VALUES ($aid, $cid)
        ");
    }

    // 6) Redirigir a la misma página
    header("Location: curso_matricula.php?curso=$cid");
    exit;
}


// 3) Incluyo cabecera y estilos
include '../header.php';
?>
<style>
  <?php include '../css/estilos.css'; ?>
</style>

<main style="padding:80px 20px">
  <center>
  <h2>Matricular alumnos en “<?= htmlspecialchars($cursoNombre) ?>”</h2>
</center>


  <?php
    // Traigo todos los alumnos
    $alumnos = $conexion->query("
      SELECT id, nombre, apellidos
      FROM alumnos
      ORDER BY apellidos, nombre
    ");
    // IDs ya matriculados
    $rows = $conexion
      ->query("SELECT alumno_id FROM matriculas WHERE curso_id = $cid")
      ->fetch_all(MYSQLI_ASSOC);
    $matriculados = array_column($rows, 'alumno_id');
  ?>

  <?php if ($alumnos->num_rows === 0): ?>
    <p>No hay alumnos dados de alta. Ve a “Gestionar alumnos” para añadirlos.</p>
  <?php else: ?>
    <form method="post" action="?curso=<?= $cid ?>">
      <table class="table">
        <thead>
          <tr>
            <th style="width:1rem;"></th>
            <th>Nombre</th>
            <th>Apellidos</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($a = $alumnos->fetch_assoc()): ?>
            <tr>
              <td>
                <input
                  type="checkbox"
                  name="alumno[]"
                  value="<?= $a['id'] ?>"
                  <?= in_array($a['id'], $matriculados) ? 'checked' : '' ?>
                >
              </td>
              <td><?= htmlspecialchars($a['nombre']) ?></td>
              <td><?= htmlspecialchars($a['apellidos']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <center>
      <div style="margin-top:1rem;">
        <button type="submit" class="btn btn-success">
          💾 Guardar Matrículas
        </button>
        <a href="cursos.php" class="btn btn-secondary">
          ↩️ Volver
        </a>
      </div>
      </center>
    </form>
  <?php endif; ?>
</main>
