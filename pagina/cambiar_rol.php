<?php
session_start();

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    die("❌ No tienes permiso para acceder a esta página.");
}

// Conectar a la base de datos
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";

$conn = new mysqli($servidor, $usuario, $password, $basedatos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['cambiar_rol'])) {
    $user_id = $_POST['user_id'];
    $nuevo_rol = $_POST['rol'];

    // Verificar que el rol no es el mismo que el actual
    $sql_rol = "SELECT rol FROM users WHERE id = $user_id";
    $result_rol = $conn->query($sql_rol);
    $row_rol = $result_rol->fetch_assoc();

    if ($row_rol['rol'] !== $nuevo_rol) {
        $sql_update = "UPDATE users SET rol = '$nuevo_rol' WHERE id = $user_id";
        if ($conn->query($sql_update) === TRUE) {
            echo "✅ Rol actualizado exitosamente.";
            // Redirigir al administrador a la página de gestión de usuarios
            header("Location: admin_usuarios.php");
            exit();
        } else {
            echo "❌ Error al actualizar el rol: " . $conn->error;
        }
    }
}

$conn->close();
?>
