<?php
if (session_status()===PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Talleres y Formación – Centro de Día</title>
  <style>
    <?php include '../css/estilos.css'; ?>

    /* Quitar listas y centrar párrafos */
    .services p {
      font-size: 1.1em;
      margin: 1em 0;
      text-align: center;
      max-width: 800px;
    }
    .features p {
      margin: 0.75em 0;
    }
    .photo-gallery {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 10px;
      margin: 2em 0;
    }
    .photo-gallery img {
      width: calc(25% - 10px);
      border-radius: 8px;
    }
    .back-btn {
      text-align: center;
      margin-top: 2em;
    }
  </style>
</head>
<body>
  <?php include '../header.php'; ?>
  <main class="services">
    <h1>Talleres y Clases</h1>
    <center>
      Nuestro objetivo es ofrecer formación práctica y enriquecedora.  
      Contamos con:
    </p>
    
    <div class="features">
      <p><strong>Taller de tecnología básica</strong> (uso de móvil, tablet y manejo de redes sociales).</p>
      <p><strong>Clases de manualidades avanzadas</strong> (cerámica, costura creativa y técnicas mixtas).</p>
      <p><strong>Espacios de lectura y escritura</strong> para estimular la mente y fomentar la expresión.</p>
      <p><strong>Orientación laboral</strong> con talleres prácticos de elaboración de CV y simulación de entrevistas.</p>
      <p><strong>Sesiones de pintura y música</strong> para potenciar la creatividad y el bienestar emocional.</p>
    </div>

    <h2>Próximos Talleres</h2>

    <div class="features">
      <p><strong>Introducción a la fotografía digital</strong> — 12 de Mayo.</p>
      <p><strong>Curso de Manualidades con Material Reciclado</strong> — 19 de Mayo.</p>
      <p><strong>Redes Sociales para Mayores</strong> — 26 de Mayo.</p>
    </div>

    <h2>Galería de Fotos</h2>
    </center>
    <div class="photo-gallery">
      <!-- Sustituye estos placeholders con tus imágenes -->
      <img src="../img/taller1.jpg" alt="Taller 1">
      <img src="../img/taller2.jpg" alt="Taller 2">
      <img src="../img/taller3.jpg" alt="Taller 3">
      
    </div>

    <div class="back-btn">
      <a href="../index.php" class="add-button">← Volver al inicio</a>
    </div>
  </main>
  <?php include '../footer.php'; ?>
</body>
</html>
