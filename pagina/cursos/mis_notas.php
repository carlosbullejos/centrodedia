<?php

require '../login/database.php';
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'alumno') {
    header('Location: /index.php'); exit;
}
$email = $_SESSION['user']['email'];


$sql = "
  SELECT
    c.nombre      AS curso,
    a.nombre      AS asignatura,
    n.nota        AS nota,
    DATE_FORMAT(n.fecha_registro, '%Y-%m-%d') AS fecha
  FROM notas n
  JOIN matriculas m   ON m.id = n.matricula_id
  JOIN alumnos al     ON al.id = m.alumno_id
  JOIN cursos c       ON c.id = m.curso_id
  JOIN asignaturas a  ON a.id = n.asignatura_id
  WHERE al.email = ?
  ORDER BY c.nombre, a.nombre
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s',$email);
$stmt->execute();
$res = $stmt->get_result();


$notasPorCurso = [];
while($row = $res->fetch_assoc()){
    $notasPorCurso[$row['curso']][] = $row;
}

include '../header.php';
?>
<style>
  <?php include '../css/estilos.css'; ?>

  .curso-header {
    background: #f0f0f0;
    font-weight: bold;
    text-align: left;
  }
</style>

<main style="padding:80px 20px">
  <center>
  <h2>Mis Notas</h2>
  </center>
  <?php if (empty($notasPorCurso)): ?>
    <p>Aún no tienes notas registradas.</p>
  <?php else: ?>
   <table class="table">
  <thead>
    <tr>
      <th>Curso</th>
      <th>Asignatura</th>
      <th>Nota</th>
      <th>Fecha</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($notasPorCurso as $curso => $notas): ?>
      <?php $cantidad = count($notas); ?>
      <?php foreach($notas as $idx => $n): ?>
        <tr>
          <?php if ($idx === 0): ?>
            <!-- Sólo en la primera fila del grupo -->
            <td rowspan="<?= $cantidad ?>" class="curso-header">
              <center>
              <?= htmlspecialchars($curso) ?>
              </center>
            </td>
          <?php endif; ?>
          <td><?= htmlspecialchars($n['asignatura']) ?></td>
          <td><?= htmlspecialchars($n['nota']) ?></td>
          <td><?= htmlspecialchars($n['fecha']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </tbody>
</table>

  <?php endif; ?>

  <div style="margin-top:1rem;">
    <center>
    <a href="/index.php" class="btn btn-secondary">↩️ Volver al inicio</a>
    </center>
  </div>
</main>
