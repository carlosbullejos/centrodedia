<?php
session_start();
require 'login/database.php';  // tu conexión

// Solo permitir si el usuario es admin
if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Validar entrada
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"])) {
    $user_id = intval($_POST["user_id"]);

    // Evitar que un admin se borre a sí mismo
    if ($_SESSION["user"]["id"] == $user_id) {
        $_SESSION['mensaje'] = "No puedes eliminar tu propia cuenta.";
    } else {
        $stmt = $conexion->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el usuario.";
        }
        $stmt->close();
    }
}

header("Location: admin_panel.php");
exit();
?>
