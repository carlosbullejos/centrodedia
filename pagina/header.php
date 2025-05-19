<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Centro de Día</title>
  <link rel="stylesheet" href="/css/estilos.css">
  <style>
    /* Estilos básicos para el menú desplegable */
    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 20px;
        align-items: center;
    }

    nav ul li {
        position: relative;
    }

    nav ul li a {
        text-decoration: none;
        padding: 10px;
        display: block;
    }

    /* Submenú oculto por defecto */
    .submenu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: #00796b;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .submenu li {
        white-space: nowrap;
    }

    /* Mostrar el submenú al pasar el ratón */
    nav ul li:hover .submenu {
        display: block;
    }
  </style>
</head>
<body>
<header>
  <div class="logo">
    <a href="/index.php"><img src="/img/logo.png" alt="Centro de Día"></a>
  </div>

  <nav>
    <ul>
      <li><a href="/index.php">Inicio</a></li>

      <?php if (!isset($_SESSION['user'])): ?>
        <li><a href="/login/login.php">Iniciar sesión</a></li>

      <?php else: 
        $rol = $_SESSION['user']['rol'];
      ?>

        <?php if ($rol === 'admin'): ?>
          <!-- Menú desplegable para administración -->
          <li>
            <a href="#">Administración ▾</a>
            <ul class="submenu">
              <li><a href="/usuarios/consultar_usuarios.php">Usuarios</a></li>
              <li><a href="/usuarios_reserva/consultar_usuarios_reserva.php">Usuarios Preregistrados</a></li>
              <li><a href="/tareas/consultar_tareas.php">Tareas</a></li>
              <li><a href="/alumnos/consultar_alumnos.php">Alumnos</a></li>
              <li><a href="/trabajadores/consultar_trabajadores.php">Trabajadores</a></li>
              <li><a href="/inventario/consultar_inventario.php">Inventario</a></li>
            </ul>
          </li>
          <li><a href="/ftp.php">Documentación</a></li>
          <li><a href="/admin_usuarios.php">Panel Usuarios</a></li>


        <?php elseif ($rol === 'trabajador' || $rol === 'profesor'): ?>
          <li><a href="ftp.php">Documentación</a></li>

        <?php else: ?>
          <li><a href="ftp.php">Documentación</a></li>
        <?php endif; ?>

        <li><a href="/login/logout.php">Cerrar sesión (<?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>)</a></li>

      <?php endif; ?>
    </ul>
  </nav>
</header>
