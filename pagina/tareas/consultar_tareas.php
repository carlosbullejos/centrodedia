<?php
// header2.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Tareas</title>
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<?php include('../header2.php'); ?>
<body>
    <center>
        <h1>Lista de Tareas</h1>

        <?php
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
            $like = "%{$search}%";
            $stmt = $conexion->prepare("
                SELECT 
                    t.id, t.nombre, t.descripcion, t.tipo,
                    GROUP_CONCAT(CONCAT(u.nombre,' ',u.apellidos) SEPARATOR ', ') AS usuarios
                FROM tareas t
                LEFT JOIN tarea_usuario tu ON t.id = tu.tarea_id
                LEFT JOIN usuarios u ON tu.usuario_id = u.id
                WHERE t.nombre LIKE ?
                GROUP BY t.id
            ");
            $stmt->bind_param("s", $like);
            $stmt->execute();
            $registros = $stmt->get_result();
        } else {
            $sql = "
                SELECT 
                    t.id, t.nombre, t.descripcion, t.tipo,
                    GROUP_CONCAT(CONCAT(u.nombre,' ',u.apellidos) SEPARATOR ', ') AS usuarios
                FROM tareas t
                LEFT JOIN tarea_usuario tu ON t.id = tu.tarea_id
                LEFT JOIN usuarios u ON tu.usuario_id = u.id
                GROUP BY t.id
            ";
            $registros = $conexion->query($sql);
        }
        ?>

        <!-- Formulario de búsqueda -->
        <div style="margin:20px;">
            <form method="get" class="formulario" style="display:inline-block;">
                <input 
                    type="text" name="search" placeholder="Buscar por nombre de tarea"
                    value="<?= htmlspecialchars($search) ?>"
                >
                <input type="submit" value="Buscar" class="btn">
                <?php if ($search): ?>
                    <a href="consultar_tareas.php" class="btn btn-reset">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($registros && mysqli_num_rows($registros) > 0): ?>
        <table border="1" class="tabla-tareas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Usuarios</th>
                    <th>Borrar</th>
                    <th>Actualizar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = mysqli_fetch_assoc($registros)): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['id']) ?></td>
                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                    <td><?= htmlspecialchars($fila['tipo']) ?></td>
                    <td><?= htmlspecialchars($fila['usuarios'] ?: '—') ?></td>
                    <td>
                        <a href="borrar_tareas.php?id=<?= $fila['id'] ?>"
                           onclick="return confirm('¿Borrar tarea <?= htmlspecialchars($fila['nombre']) ?>?')">
                           <img src="../img/borrar.png" alt="Borrar" width="20">
                        </a>
                    </td>
                    <td>
                        <a href="actualizar_tareas.php?id=<?= $fila['id'] ?>">
                           <img src="../img/actualizar.png" alt="Actualizar" width="20">
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No hay tareas registradas actualmente.</p>
        <?php endif; ?>

        <table>
            <tr>
                <td colspan="7" style="text-align: center;">
                    <a href="tareas.php" class="add-button">Agregar Nueva Tarea</a>
                </td>
            </tr>
        </table>
    </center>

    <?php
    $conexion->close();
    include('../footer.php');
    ?>
</body>
</html>
