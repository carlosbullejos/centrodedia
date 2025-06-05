<?php
// curso_notas.php

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

// 2) POST: guardo las notas **antes** de enviar cualquier HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nota']) && is_array($_POST['nota'])) {
        foreach ($_POST['nota'] as $mid => $datos) {
            $mid = intval($mid);
            foreach ($datos as $aid => $valor) {
                $aid = intval($aid);
                // fuerza a número y lo recorta entre 0 y 10
                $v = floatval($valor);
                if ($v < 0)  { $v = 0; }
                if ($v > 10) { $v = 10; }

                $conexion->query("
                    INSERT INTO notas (matricula_id, asignatura_id, nota)
                    VALUES ($mid, $aid, $v)
                    ON DUPLICATE KEY UPDATE nota = $v, fecha_registro = NOW()
                ");
            }
        }
    }
    header("Location: curso_notas.php?curso=$cid");
    exit;
}


// 3) Incluyo header y estilos **después** de procesar el POST
include '../header.php';
?>
<style>
  <?php include '../css/estilos.css'; ?>
</style>

<main style="padding:80px 20px">
  <center><h2>Notas del curso “<?= htmlspecialchars($cursoNombre) ?>”</h2></center>

  <?php
    // Obtengo asignaturas de este curso
    $asig = $conexion->query("
      SELECT a.id, a.nombre
      FROM asignaturas a
      JOIN cursos_asig ca ON ca.asignatura_id = a.id
      WHERE ca.curso_id = $cid
    ");
    $asignaturas = $asig->fetch_all(MYSQLI_ASSOC);

    // Obtengo alumnos matriculados
    $alum = $conexion->query("
      SELECT m.id AS mid, u.nombre, u.apellidos
      FROM matriculas m
      JOIN alumnos u ON u.id = m.alumno_id
      WHERE m.curso_id = $cid
    ");
  ?>

  <?php if (empty($asignaturas)): ?>
    <p>No hay asignaturas asignadas a este curso. Ve a “Asignaturas del curso” para agregar.</p>
  <?php elseif ($alum->num_rows === 0): ?>
    <p>No hay alumnos matriculados en este curso.</p>
  <?php else: ?>
    <form method="post" action="?curso=<?= $cid ?>">
      <table class="table">
        <thead>
          <tr>
            <th>Alumno</th>
            <?php foreach ($asignaturas as $col): ?>
              <th><?= htmlspecialchars($col['nombre']) ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $alum->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars("{$row['nombre']} {$row['apellidos']}") ?></td>
              <?php foreach ($asignaturas as $col):
                // Obtengo nota si existe
                $res = $conexion->query("
                  SELECT nota FROM notas
                  WHERE matricula_id = {$row['mid']}
                    AND asignatura_id = {$col['id']}
                ")->fetch_assoc()['nota'] ?? '';
              ?>
                <td>
                  <input
                    type="number"
                    step="0.1"
                    min="0"
                    max="10"
                    name="nota[<?= $row['mid'] ?>][<?= $col['id'] ?>]"
                    value="<?= htmlspecialchars($res) ?>"
                  >
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <center>
      <div style="margin-top:12px;">
        <button type="submit" class="btn btn-success">💾 Guardar Notas</button>
        <a href="cursos.php" class="btn btn-secondary" style="margin-left:10px;">↩️ Volver</a>
      </div>
      </center>
    </form>
  <?php endif; ?>
</main>
