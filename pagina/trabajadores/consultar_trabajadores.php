<?php
// consultar_trabajadores.php
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

// Parámetros de conexión
$servidor  = "mysql-service:3306";
$usuario   = "root";
$password  = "my_passwd";
$basedatos = "mi_basedatos";

// Conexión a la base de datos
$conexion = new mysqli($servidor, $usuario, $password, $basedatos);
if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

// Procesar búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $like   = "%{$search}%";
    $stmt   = $conexion->prepare("SELECT * FROM trabajadores WHERE nombre LIKE ?");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $registros = $stmt->get_result();
    $stmt->close();
} else {
    $registros = $conexion->query("SELECT * FROM trabajadores");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Trabajadores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<body>
<?php include('../header.php'); ?>

<center>
    <h1>Lista de Trabajadores</h1>

    <!-- Mensaje inmediato de éxito o error -->
    <?= $mensajeHtml ?>

    <!-- Formulario de búsqueda -->
    <div style="margin:20px;">
        <form method="get" class="formulario" style="display:inline-block;">
            <input
              type="text"
              name="search"
              placeholder="Buscar por nombre"
              value="<?= htmlspecialchars($search) ?>"
            >
            <input type="submit" value="Buscar" class="btn">
            <?php if ($search !== ''): ?>
                <a href="consultar_trabajadores.php" class="btn btn-reset">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabla de resultados -->
    <?php if ($registros && $registros->num_rows > 0): ?>
    <table border="1" class="tabla-listado">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>DNI</th>
                <th>Teléfono</th>
                <th>Puesto</th>
                <th>Fecha Contratación</th>
                <th>Especialidad</th>
                <th>Fecha Nacimiento</th>
                <th>Borrar</th>
                <th>Actualizar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($registro = $registros->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($registro['id']) ?></td>
                <td><?= htmlspecialchars($registro['nombre']) ?></td>
                <td><?= htmlspecialchars($registro['apellidos']) ?></td>
                <td><?= htmlspecialchars($registro['dni']) ?></td>
                <td><?= htmlspecialchars($registro['telefono']) ?></td>
                <td><?= htmlspecialchars($registro['puesto']) ?></td>
                <td><?= htmlspecialchars($registro['fecha_contratacion']) ?></td>
                <td><?= htmlspecialchars($registro['especialidad']) ?></td>
                <td><?= htmlspecialchars($registro['fecha_nacimiento']) ?></td>
                <td>
                  <a href="borrar_trabajadores.php?id=<?= $registro['id'] ?>">
                    <img src="../img/borrar.png" alt="Borrar" width="20">
                  </a>
                </td>
                <td>
                  <a href="actualizar_trabajadores.php?id=<?= $registro['id'] ?>">
                    <img src="../img/actualizar.png" alt="Actualizar" width="20">
                  </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No hay registros en el inventario actualmente.</p>
    <?php endif; ?>

    <!-- Botón para añadir nuevo -->
    <div style="margin-top: 20px;">
        <a href="trabajadores.php" class="add-button btn">Agregar Nuevo Registro</a>
    </div>
</center>

<?php include('../footer.php'); ?>
</body>
</html>
