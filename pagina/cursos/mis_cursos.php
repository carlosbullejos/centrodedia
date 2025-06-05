<?php
require '../login/database.php';
session_start();
// 1) Sólo rol alumno
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'alumno') {
    header('Location: /index.php'); exit;
}
// 2) Obtengo alumno_id mediante email
$email = $_SESSION['user']['email'];
$stmt = $conexion->prepare("SELECT id FROM alumnos WHERE email = ?");
$stmt->bind_param('s',$email);
$stmt->execute();
$stmt->bind_result($alumnoId);
if (!$stmt->fetch()) { die("Alumno no encontrado."); }
$stmt->close();

// 3) Cursos disponibles
$cursos = $conexion->query("SELECT id,nombre FROM cursos ORDER BY nombre");

// 4) Solicitudes previas
$stmt = $conexion->prepare("
  SELECT curso_id, estado 
    FROM solicitudes 
   WHERE alumno_id = ?
");
$stmt->bind_param('i',$alumnoId);
$stmt->execute();
$res = $stmt->get_result();
$solic = [];
while($r=$res->fetch_assoc()){
  $solic[$r['curso_id']] = $r['estado'];
}
$stmt->close();

// 5) Si viene POST, guardamos/actualizamos solicitudes
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $seleccion = array_map('intval', $_POST['curso'] ?? []);
  foreach($seleccion as $cid) {
    // Insertar nueva o ignorar si ya existía
    $stmt = $conexion->prepare("
      INSERT INTO solicitudes (alumno_id,curso_id)
      VALUES (?,?)
      ON DUPLICATE KEY UPDATE estado='pendiente'
    ");
    $stmt->bind_param('ii',$alumnoId,$cid);
    $stmt->execute();
    $stmt->close();
  }
  header("Location: mis_cursos.php"); exit;
}

include '../header.php';
?>
<style><?php include '../css/estilos.css'; ?></style>
<main style="padding:80px 20px">
    <center>
  <h2>Solicitar Matrícula</h2>
  </center>
  <form method="post">
    <table class="table">
      <thead>
        <tr><th>Curso</th><th>Solicitado</th><th>Estado</th></tr>
      </thead>
      <tbody>
      <?php while($c=$cursos->fetch_assoc()): 
        $cid = $c['id'];
        $est = $solic[$cid] ?? '';
      ?>
        <tr>
          <td><?= htmlspecialchars($c['nombre']) ?></td>
          <td>
            <input type="checkbox" name="curso[]" value="<?= $cid ?>"
              <?= $est ? 'checked disabled' : '' ?>
            >
          </td>
          <td>
            <?= $est 
                ? ucfirst($est) 
                : '<span style="color:#999">no solicitado</span>' 
            ?>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <center>
    <button class="btn btn-success">💾 Enviar Solicitudes</button>
    <a href="/index.php" class="btn btn-secondary">↩️ Volver</a>
    </center>
  </form>
</main>
