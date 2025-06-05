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
    <title>Formulario de Tareas</title>
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
    // Conexión a la base de datos para obtener usuarios
    $servidor = "mysql-service:3306";
    $usuario  = "root";
    $password = "my_passwd";
    $basedatos= "mi_basedatos";
    $conexion = new mysqli($servidor, $usuario, $password, $basedatos);
    if ($conexion->connect_error) {
        die("❌ Conexión fallida: " . $conexion->connect_error);
    }
    // Obtener lista de usuarios
    $sql_users = "SELECT id, nombre, apellidos FROM usuarios";
    $res_users = mysqli_query($conexion, $sql_users);
    ?>

    <center>
    <h1>Formulario de Tareas</h1>
    <form class="formulario" action="tareas2.php" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo:</label>
            <input type="text" id="tipo" name="tipo" required>
        </div>

        <div class="form-group">
            <label for="usuarios">Asignar Usuarios:</label>
            <select name="usuarios[]" id="usuarios" multiple required>
                <?php while($u = mysqli_fetch_assoc($res_users)): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre'].' '.$u['apellidos']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-actions">
            <input type="submit" value="Enviar" class="button">
            <input type="reset" value="Restablecer" class="button">
        </div>
    </form>
    </center>

    <?php
    $conexion->close();
    include('../footer.php');
    ?>
</body>
</html>
