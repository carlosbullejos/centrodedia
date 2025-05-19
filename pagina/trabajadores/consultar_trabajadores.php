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
    <title>Consultar Trabajadores</title>
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<?php include('../header2.php'); ?>
<body>
    <center>
        <h1>Lista de Trabajadores</h1>

        <?php
        // Conexión
        $servidor = "mysql-service:3306";
        $usuario  = "root";
        $password = "my_passwd";
        $basedatos= "mi_basedatos";
        $conexion = new mysqli($servidor, $usuario, $password, $basedatos);
        if ($conexion->connect_error) {
            die("❌ Conexión fallida: " . $conexion->connect_error);
        }

        // Procesar búsqueda
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        if ($search !== '') {
            $like = "%{$search}%";
            $stmt = $conexion->prepare("SELECT * FROM trabajadores WHERE nombre LIKE ?"); // adjust column
            $stmt->bind_param("s", $like);
            $stmt->execute();
            $registros = $stmt->get_result();
        } else {
            $registros = $conexion->query("SELECT * FROM trabajadores");
        }
        ?>

        <!-- Formulario de búsqueda -->
        <div style="margin:20px;">
            <form method="get" class="formulario" style="display:inline-block;">
                <input type="text" name="search" placeholder="Buscar por nombre" value="<?= htmlspecialchars($search) ?>">
                <input type="submit" value="Buscar" class="btn">
                <?php if ($search): ?>
                <tr>
                    <a href="consultar_trabajadores.php" class="btn btn-reset">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($registros && mysqli_num_rows($registros) > 0): ?>
                <tr>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>DNI</th>
                    <th>Teléfono</th>
                    <th>Puesto</th>
                    <th>Fecha de Contratación</th>
                    <th>Especialidad</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Borrar</th>
                    <th>Actualizar</th>
                </tr>
            </thead>
            <tbody>
                <?php while($registro = mysqli_fetch_row($registros)): ?>
                <tr>
                        <td><?= htmlspecialchars($registro[0]) ?></td>
                        <td><?= htmlspecialchars($registro[1]) ?></td>
                        <td><?= htmlspecialchars($registro[2]) ?></td>
                        <td><?= htmlspecialchars($registro[3]) ?></td>
                        <td><?= htmlspecialchars($registro[4]) ?></td>
                        <td><?= htmlspecialchars($registro[5]) ?></td>
                        <td><?= htmlspecialchars($registro[6]) ?></td>
                        <td><?= htmlspecialchars($registro[7]) ?></td>
                        <td><?= htmlspecialchars($registro[8]) ?></td>
                    <td><a href="borrar_trabajadores.php?id=<?= $registro[0] ?>"><img src="../img/borrar.png" alt="Borrar" width="20"></a></td>
                    <td><a href="actualizar_trabajadores.php?id=<?= $registro[0] ?>"><img src="../img/actualizar.png" alt="Actualizar" width="20"></a></td>
                                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No hay registros en el inventario actualmente.</p>
        <?php endif; ?>

        <table>
            <tr>
                <td colspan="11" style="text-align: center;">
                    <a href="trabajadores.php" class="add-button">Agregar Nuevo Registro</a>
                </td>
            </tr>
        </table>

    </center>
    <?php include('../footer.php'); ?>
</body>
</html>
