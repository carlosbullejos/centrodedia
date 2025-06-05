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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/estilos.css">
    <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap"
    rel="stylesheet"
    >
</head>
<body>
<header>
  <div class="logo">
    <a href="/index.php">
      <img src="/img/logo.png" alt="Centro de Día" style="height:70px;">
    </a>
  </div>

  <!-- Botón hamburguesa -->
  <button class="hamburger" id="hamburger" aria-label="Abrir menú">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </button>

  <!-- Menú desplegable en móvil y estático en escritorio -->
  <nav class="nav-menu" id="nav-menu">
    <ul>
      <li><a href="/index.php">Inicio</a></li>

      <?php if (!isset($_SESSION['user'])): ?>
        <li><a href="/login/login.php">Iniciar sesión</a></li>
      <?php else:
        $rol = $_SESSION['user']['rol'];
      ?>

        <?php if ($rol === 'admin'): ?>
          <li>
            <a href="#" class="has-sub">Administración ▾</a>
            <ul class="submenu">
              <li><a href="/usuarios/consultar_usuarios.php">Usuarios</a></li>
              <li><a href="/usuarios_reserva/consultar_usuarios_reserva.php">Preregistrados</a></li>
              <li><a href="/tareas/consultar_tareas.php">Tareas</a></li>
              <li><a href="/alumnos/consultar_alumnos.php">Alumnos</a></li>
              <li><a href="/cursos/cursos.php">Cursos</a></li>
              <li><a href="/trabajadores/consultar_trabajadores.php">Trabajadores</a></li>
              <li><a href="/inventario/consultar_inventario.php">Inventario</a></li>
            </ul>
          </li>
          <li><a href="/ftp.php">Documentación</a></li>
          <li><a href="/admin_usuarios.php">Panel Usuarios</a></li>

        <?php elseif ($rol === 'alumno'): ?>
          <li>
            <a href="#" class="has-sub">Cursos ▾</a>
            <ul class="submenu">
              <li><a href="/cursos/mis_cursos.php">Solicitudes</a></li>
              <li><a href="/cursos/mis_notas.php">Mis Notas</a></li>
            </ul>
          </li>
          <li><a href="/ftp.php">Documentación</a></li>

        <?php else: ?>
          <li><a href="/ftp.php">Documentación</a></li>
        <?php endif; ?>

        <li>
          <a href="/login/logout.php">
            Cerrar sesión (<?= htmlspecialchars($_SESSION['user']['nombre']) ?>)
          </a>
        </li>

      <?php endif; ?>
    </ul>
  </nav>
</header>

<script>
  const hamburger = document.getElementById('hamburger');
  const navMenu   = document.getElementById('nav-menu');

  hamburger.addEventListener('click', () => {
    navMenu.classList.toggle('open');
    hamburger.setAttribute(
      'aria-label',
      navMenu.classList.contains('open') ? 'Cerrar menú' : 'Abrir menú'
    );
  });
</script>

</body>
</html>
