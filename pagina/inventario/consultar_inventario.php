<?php
// consultar_inventario.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Inventario</title>
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>

<body>
    <?php include('../header.php'); ?>
    <center>
        <h1>Lista de Inventario</h1>

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
            $stmt = $conexion->prepare("SELECT * FROM inventario WHERE nombre LIKE ?");
            $stmt->bind_param("s", $like);
            $stmt->execute();
            $registros = $stmt->get_result();
        } else {
            $registros = $conexion->query("SELECT * FROM inventario");
        }
        ?>

        <!-- Formulario de búsqueda -->
        <div style="margin:20px;">
            <form method="get" class="formulario" style="display:inline-block;">
                <input type="text" name="search" placeholder="Buscar por nombre" value="<?= htmlspecialchars($search) ?>">
                <input type="submit" value="Buscar" class="btn">
                <?php if ($search !== ''): ?>
                    <a href="consultar_inventario.php" class="btn btn-reset">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($registros && $registros->num_rows > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Producto</th>
                        <th>Ubicación</th>
                        <th>Unidades</th>
                        <th>Fecha de Ingreso</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <th>Imagen</th>
                        <th>Borrar</th>
                        <th>Actualizar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $registros->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                            <td><?= htmlspecialchars($row['unidades']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_ingreso']) ?></td>
                            <td><?= htmlspecialchars($row['proveedor']) ?></td>
                            <td><?= htmlspecialchars($row['estado']) ?></td>
                            <td>
                                <?php if (!empty($row['imagen'])): ?>
                                    <img
                                      src="<?= htmlspecialchars($row['imagen']) ?>"
                                      alt="Img <?= htmlspecialchars($row['nombre']) ?>"
                                      style="max-width:100px; height:auto;"
                                    >
                                <?php else: ?>
                                    <span>No hay imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="borrar_inventario.php?id=<?= $row['id'] ?>">
                                    <img src="../img/borrar.png" alt="Borrar" width="20">
                                </a>
                            </td>
                            <td>
                                <a href="actualizar_inventario.php?id=<?= $row['id'] ?>">
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

        <div style="margin-top:20px;">
            <a href="inventario.php" class="add-button">Agregar Nuevo Registro</a>
        </div>

    </center>
    <?php include('../footer.php'); ?>
</body>
</html>
