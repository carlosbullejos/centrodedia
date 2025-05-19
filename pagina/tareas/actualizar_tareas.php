<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario Actualización de Tarea</title>
    <style>
        <?php include('../css/estilos.css'); ?>
    </style>
</head>
<body>
    <?php include('../header.php'); ?>

    <?php
    // Mostrar mensaje si existe
    if (isset($_SESSION['mensaje'])):
        $clase = $_SESSION['mensaje']['tipo'] === 'success' ? 'mensaje-success' : 'mensaje-error';
    ?>
        <div class="mensaje <?= $clase ?>">
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
    <?php unset($_SESSION['mensaje']); endif; ?>

    <?php
    // Conexión a la BD
    $servidor  = "mysql-service:3306";
    $usuario   = "root";
    $password  = "my_passwd";
    $basedatos = "mi_basedatos";
    $conexion  = new mysqli($servidor, $usuario, $password, $basedatos);
    if ($conexion->connect_error) {
        die("❌ Conexión fallida: " . $conexion->connect_error);
    }

    // Obtener tarea por ID
    $id = intval($_GET['id']);
    $stmt = $conexion->prepare("SELECT nombre, descripcion, tipo FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre, $descripcion, $tipo);
    $stmt->fetch();
    $stmt->close();

    // Obtener todos los usuarios para el multiselect
    $res_users = $conexion->query("SELECT id, nombre, apellidos FROM usuarios");

    // Obtener usuarios asignados
    $assigned = [];
    $stmt2 = $conexion->prepare("SELECT usuario_id FROM tarea_usuario WHERE tarea_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->bind_result($uid);
    while ($stmt2->fetch()) {
        $assigned[] = $uid;
    }
    $stmt2->close();
    ?>

    <center>
        <h1>Actualizar Tarea</h1>
        <form action="actualizar_tareas2.php" method="post" class="formulario">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($nombre) ?>">
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($descripcion) ?></textarea>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo" required value="<?= htmlspecialchars($tipo) ?>">
            </div>
            <div class="form-group">
                <label for="usuarios">Asignar Usuarios:</label>
                <select name="usuarios[]" id="usuarios" multiple required>
                    <?php while ($u = $res_users->fetch_assoc()): ?>
                        <option value="<?= $u['id'] ?>" <?= in_array($u['id'], $assigned) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <input type="submit" value="Actualizar" class="btn">
                <input type="reset" value="Restablecer" class="btn btn-reset">
            </div>
        </form>
    </center>

    <?php
    $conexion->close();
    include('../footer.php');
    ?>
</body>
</html>
