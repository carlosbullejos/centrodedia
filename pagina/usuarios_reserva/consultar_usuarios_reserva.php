<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Usuarios Reserva</title>
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<?php include('../header.php'); ?>
<body>
    <center>
        <h1>Lista de Usuarios Reserva</h1>

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
            $stmt = $conexion->prepare("SELECT * FROM usuarios_reserva WHERE nombre LIKE ?");
            $stmt->bind_param("s", $like);
            $stmt->execute();
            $registros = $stmt->get_result();
            $stmt->close();
        } else {
            $registros = $conexion->query("SELECT * FROM usuarios_reserva");
        }
        ?>

        <!-- Formulario de búsqueda -->
        <div style="margin:20px;">
            <form method="get" class="formulario" style="display:inline-block;">
                <input type="text" name="search" placeholder="Buscar por nombre"
                       value="<?= htmlspecialchars($search) ?>">
                <input type="submit" value="Buscar" class="btn">
                <?php if ($search): ?>
                    <a href="consultar_usuarios_reserva.php" class="btn btn-reset">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($registros && mysqli_num_rows($registros) > 0): ?>
        <table border="1" class="tabla-usuarios-reserva">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Descripción</th>
                    <th>Borrar</th>
                    <th>Actualizar</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($registros)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td>
                        <a href="borrar_usuario_reserva.php?id=<?= $row['id'] ?>"
                           onclick="return confirm('¿Borrar usuario reserva <?= htmlspecialchars($row['nombre']) ?>?')">
                           <img src="../img/borrar.png" alt="Borrar" width="20">
                        </a>
                    </td>
                    <td>
                        <a href="actualizar_usuario_reserva.php?id=<?= $row['id'] ?>">
                           <img src="../img/actualizar.png" alt="Actualizar" width="20">
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No hay registros en usuarios_reserva actualmente.</p>
        <?php endif; ?>

        <div style="margin-top:20px;">
            <a href="usuarios_reserva.php" class="add-button">Agregar Nuevo Registro</a>
        </div>

    </center>
    <?php
    $conexion->close();
    include('../footer.php');
    ?>
</body>
</html>
