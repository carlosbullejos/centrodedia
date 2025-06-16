<?php
session_start();
require 'login/database.php';


if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Cambiar rol si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_rol') {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];

    $roles_validos = ['usuario', 'trabajador', 'alumno', 'admin'];
    if (in_array($new_role, $roles_validos)) {
        $stmt = $conexion->prepare("UPDATE users SET rol = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION['mensaje'] = "Rol actualizado correctamente.";
}

// Eliminar usuario si se solicitó
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] === 'eliminar_usuario') {
    $user_id = intval($_POST['user_id']);

    if ($_SESSION['user']['id'] != $user_id) {
        $stmt = $conexion->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
    } else {
        $_SESSION['mensaje'] = "No puedes eliminar tu propia cuenta.";
    }
}

// Obtener todos los usuarios
$usuarios = [];
$stmt = $conexion->prepare("SELECT id, nombre, email, rol FROM users");
$stmt->execute();
$resultado = $stmt->get_result();
while ($row = $resultado->fetch_assoc()) {
    $usuarios[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <style>
        <?php include('css/estilos.css'); ?>
        
       
    </style>
    
</head>
<?php include('header.php'); ?>

<body>
    
    <center>
    <h2>Gestión de Usuarios</h2>
    </center>
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div style="text-align: center;">
    <a class="enlace-agregar" href="agregar_usuario.php">➕ Añadir nuevo usuario</a>
</div>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol actual</th>
            <th>Cambiar rol</th>
            <th>Eliminar</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['rol']) ?></td>
                <td>
                    <?php if ($usuario['id'] != $_SESSION['user']['id']): ?>
                        <form method="POST">
                            <input type="hidden" name="accion" value="cambiar_rol">
                            <input type="hidden" name="user_id" value="<?= $usuario['id'] ?>">
                            <select name="new_role">
                                <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                                <option value="trabajador" <?= $usuario['rol'] == 'trabajador' ? 'selected' : '' ?>>Trabajador</option>
                                <option value="alumno" <?= $usuario['rol'] == 'alumno' ? 'selected' : '' ?>>Alumno</option>
                                <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit">Cambiar</button>
                        </form>
                    <?php else: ?>
                        (Yo)
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($usuario['id'] != $_SESSION['user']['id']): ?>
                        <form method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">
                            <input type="hidden" name="accion" value="eliminar_usuario">
                            <input type="hidden" name="user_id" value="<?= $usuario['id'] ?>">
                            <button type="submit" class="boton-rojo">Eliminar</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
