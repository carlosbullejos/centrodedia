<?php
require '../login/database.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$nombre = $codigo = '';

// Si POST: crear o actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $codigo = $conexion->real_escape_string($_POST['codigo']);

    if ($id) {
        $sql = "
          UPDATE asignaturas
          SET nombre='$nombre', codigo='$codigo'
          WHERE id = $id
        ";
        $resultado = $conexion->query($sql);
    } else {
        $sql = "
          INSERT INTO asignaturas (nombre, codigo)
          VALUES ('$nombre','$codigo')
        ";
        $resultado = $conexion->query($sql);
    }

    if ($resultado) {
        $_SESSION['mensaje'] = [
            'texto' => "✅ Asignatura guardada correctamente.",
            'tipo' => 'success'
        ];
        header('Location: asignaturas.php');
        exit;
    } else {
        if ($conexion->errno === 1062) {
            // Código duplicado
            $_SESSION['mensaje'] = [
                'texto' => "❌ El código «{$codigo}» ya existe. Por favor, elige otro.",
                'tipo'  => 'error'
            ];
        } else {
            $_SESSION['mensaje'] = [
                'texto' => "❌ Error al guardar la asignatura: (" . $conexion->errno . ") " . htmlspecialchars($conexion->error),
                'tipo'  => 'error'
            ];
        }
        header('Location: asignatura_form.php' . ($id ? "?id=$id" : ''));
        exit;
    }
}

// Si EDIT, cargar datos existentes
if ($id) {
    $r = $conexion->query("SELECT * FROM asignaturas WHERE id = $id");
    if ($row = $r->fetch_assoc()) {
        $nombre = $row['nombre'];
        $codigo = $row['codigo'];
    }
}

include '../header.php';
?>
<style>
<?php include '../css/estilos.css'; ?>
</style>

<main style="padding:80px 20px">
  <h2><?= $id ? 'Editar' : 'Crear' ?> Asignatura</h2>

  <!-- Mostrar mensaje si existe -->
  <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="mensaje <?= $_SESSION['mensaje']['tipo'] ?>">
        <?= $_SESSION['mensaje']['texto'] ?>
        <?php unset($_SESSION['mensaje']); ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <label>
      Nombre:<br>
      <input type="text" name="nombre"
             value="<?= htmlspecialchars($nombre) ?>" required>
    </label><br><br>
    <label>
      Código:<br>
      <input type="text" name="codigo"
             value="<?= htmlspecialchars($codigo) ?>" required>
    </label><br><br>
    <button type="submit">Guardar</button>
    <a href="asignaturas.php">Cancelar</a>
  </form>
</main>
