<?php
// consultar_alumnos.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Capturar mensaje de sesión (si existe) y luego borrarlo
$mensajeHtml = '';
if (!empty($_SESSION['mensaje'])) {
    $tipo  = $_SESSION['mensaje']['tipo'] === 'success' ? 'mensaje-success' : 'mensaje-error';
    $texto = htmlspecialchars($_SESSION['mensaje']['texto']);
    $mensajeHtml = "<div class=\"mensaje {$tipo}\">{$texto}</div>";
    unset($_SESSION['mensaje']);
}

// Conexión
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";
$conexion  = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// Procesar búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $like   = "%{$search}%";
    $stmt   = $conexion->prepare("SELECT * FROM alumnos WHERE nombre LIKE ? OR apellidos LIKE ?");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $registros = $stmt->get_result();
    $stmt->close();
} else {
    $registros = $conexion->query("SELECT * FROM alumnos");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Alumnos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<body>
<?php include('../header.php'); ?>
<center>
    <h1>Lista de Alumnos</h1>

    <!-- Mensaje inmediato -->
    <?= $mensajeHtml ?>

    <!-- Formulario de búsqueda -->
    <div style="margin:20px;">
        <form method="get" class="formulario" style="display:inline-block;">
            <input
              type="text"
              name="search"
              placeholder="Buscar por nombre o apellido"
              value="<?= htmlspecialchars($search) ?>"
            >
            <input type="submit" value="Buscar" class="btn">
            <?php if ($search !== ''): ?>
                <a href="consultar_alumnos.php" class="btn btn-reset">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabla de resultados -->
    <?php if ($registros && $registros->num_rows > 0): ?>
    <table border="1" class="tabla-listado">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Apellidos</th><th>Email</th>
                <th>DNI</th><th>Fecha Nac.</th><th>Teléfono</th><th>Dirección</th>
                <th>Localidad</th><th>Fecha Ingreso</th><th>ID Prof.</th>
                <th>Borrar</th><th>Actualizar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $registros->fetch_row()): ?>
            <tr>
                <?php foreach ($r as $idx => $valor): 
                    // id=0...profesor_id=10
                    if ($idx <= 10): ?>
                        <td><?= htmlspecialchars($valor) ?></td>
                <?php endif; endforeach; ?>
                <td>
                  <a href="borrar_alumnos.php?id=<?= $r[0] ?>">
                    <img src="../img/borrar.png" alt="Borrar" width="20">
                  </a>
                </td>
                <td>
                  <a href="actualizar_alumnos.php?id=<?= $r[0] ?>">
                    <img src="../img/actualizar.png" alt="Actualizar" width="20">
                  </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No hay alumnos registrados actualmente.</p>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <a href="alumnos.php" class="add-button btn">Agregar Nuevo Alumno</a>
    </div>
</center>
<?php
$conexion->close();
include('../footer.php');
?>
</body>
</html>
