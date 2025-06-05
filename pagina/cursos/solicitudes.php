<?php
require '../login/database.php';
session_start();

// 1) Control de acceso: solo trabajador o admin
if (!isset($_SESSION['user']['rol']) || !in_array($_SESSION['user']['rol'], ['trabajador','admin'])) {
    header('Location: /index.php');
    exit;
}

// 2) Obtener el ID de curso desde GET
$idCurso = intval($_GET['curso'] ?? 0);
if ($idCurso <= 0) {
    die("⚠️ Curso no válido.");
}

// 2.1) Obtener el nombre del curso
$stmt2 = $conexion->prepare("SELECT nombre FROM cursos WHERE id = ?");
if (!$stmt2) {
    die("Error al preparar nombre de curso: " . $conexion->error);
}
$stmt2->bind_param('i', $idCurso);
$stmt2->execute();
$stmt2->bind_result($cursoNombre);
$stmt2->fetch();
$stmt2->close();

// 3) Traer solicitudes pendientes de ese curso
$stmt = $conexion->prepare("
  SELECT
    s.id,
    al.id   AS aid,
    al.nombre,
    al.apellidos,
    s.estado
  FROM solicitudes s
  JOIN alumnos al ON al.id = s.alumno_id
  WHERE s.curso_id = ? 
    AND s.estado = 'pendiente'
");
if (!$stmt) {
    die("Error en la consulta de solicitudes: " . $conexion->error);
}
$stmt->bind_param('i', $idCurso);
$stmt->execute();
$sol = $stmt->get_result();
$stmt->close();

include '../header.php';
?>
<style>
  <?php include '../css/estilos.css'; ?>
</style>

<main style="padding:80px 20px">
  <h2>Solicitudes del curso “<?= htmlspecialchars($cursoNombre) ?>”</h2>

  <?php if ($sol->num_rows === 0): ?>
    <p>No hay solicitudes pendientes para este curso.</p>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>Alumno</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($r = $sol->fetch_assoc()): ?>
          <tr>
            <td>
              <?= htmlspecialchars($r['nombre'] . ' ' . $r['apellidos']) ?>
            </td>
            <td>
              <a href="aprobar.php?sid=<?= $r['id'] ?>&curso=<?= $idCurso ?>"
                 class="btn btn-success">
                ✔️ Aprobar
              </a>
              <a href="rechazar.php?sid=<?= $r['id'] ?>&curso=<?= $idCurso ?>"
                 class="btn btn-reset">
                ✖️ Rechazar
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div style="margin-top:1rem;">
    <a href="/cursos/cursos.php" class="btn btn-secondary">↩️ Volver a Cursos</a>
  </div>
</main>
