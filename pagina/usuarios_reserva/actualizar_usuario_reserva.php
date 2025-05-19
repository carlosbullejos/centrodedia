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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Usuario Reserva</title>
    <style>
    <?php include('../css/estilos.css'); ?>

  </style>
</head>
<body>
    <?php include('../header.php'); ?>

    <?php
    $servidor = "mysql-service:3306";
    $usuario = "root";
    $password = "my_passwd";
    $basedatos = "mi_basedatos";
    $conexion = new mysqli($servidor, $usuario, $password, $basedatos);

    // Obtener el ID del usuario a actualizar
    $id = $_GET['id'];

    // Consultar el usuario a actualizar
    $consultar = "SELECT * FROM usuarios_reserva WHERE id='$id'";
    $registros = mysqli_query($conexion, $consultar);
    $usuario = mysqli_fetch_row($registros);
    ?>

    <center>
        <h1>Actualizar Usuario Reserva</h1>
        <form action="actualizar_usuario_reserva2.php" method="post" class="formulario">
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="number" id="id" name="id" value="<?php echo $usuario[0]; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $usuario[1]; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $usuario[2]; ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo $usuario[3]; ?>">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $usuario[4]; ?></textarea>
            </div>

            <div class="form-actions">
                <input type="submit" value="Actualizar" class="btn">
                <input type="reset" value="Restablecer" class="btn btn-reset">
            </div>
        </form>
    </center>

    <?php include('../footer.php'); ?>
</body>
</html>
