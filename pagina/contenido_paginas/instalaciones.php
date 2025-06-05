<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nuestras Instalaciones | Centro de Día</title>
  <style>
    <?php include('../css/estilos.css'); ?>
    /* ===== Sección Instalaciones ===== */
    .instalaciones-page {
      padding: 40px 20px;
      background-color: #f5f5f5;
      min-height: 100vh;
    }
    .instalaciones-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .instalaciones-header h1 {
      color: #00796b;
      font-size: 2.5em;
      margin: 0;
    }
    .instalaciones-header p {
      color: #555;
      font-size: 1.1em;
    }
    .instalaciones-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .instalacion-card {
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .instalacion-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .instalacion-info {
      padding: 15px;
      flex: 1;
    }
    .instalacion-info h3 {
      margin: 0 0 10px;
      color: #004d40;
      font-size: 1.4em;
    }
    .instalacion-info p {
      margin: 0;
      color: #555;
      font-size: 1em;
      line-height: 1.5;
    }
    .instalacion-img {
      width: 100%;
      height: 180px;
      background-size: cover;
      background-position: center;
      border-radius: 0 0 8px 8px;
      transition: transform 0.3s ease;
    }
    .instalacion-img:hover {
      transform: scale(1.05) translateY(-5px);
    }
    @media (max-width: 800px) {
      .instalaciones-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 500px) {
      .instalaciones-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <?php include('../header.php'); ?>
  <main class="instalaciones-page">
    <div class="instalaciones-grid">
      <div class="instalacion-card">
        <div class="instalacion-info">
          <h3>Sala de Terapias</h3>
          <p>Un espacio tranquilo equipado con camillas y luz natural para sesiones.</p>
        </div>
        <div class="instalacion-img" style="background-image:url('../img/sala-terapias.jpg')"></div>
      </div>

      <div class="instalacion-card">
        <div class="instalacion-info">
          <h3>Salón de Actividades</h3>
          <p>Amplio salón con mesas modulares y proyector para talleres y eventos.</p>
        </div>
        <div class="instalacion-img" style="background-image:url('../img/salon-actividades.jpg')"></div>
      </div>

      <div class="instalacion-card">
        <div class="instalacion-info">
          <h3>Biblioteca Multimedia</h3>
          <p>Zona de lectura y estudio con estanterías y equipos digitales.</p>
        </div>
        <div class="instalacion-img" style="background-image:url('../img/biblioteca.jpg')"></div>
      </div>

      <div class="instalacion-card">
        <div class="instalacion-info">
          <h3>Zonas de Descanso</h3>
          <p>Ambientes relajantes con sillones y áreas verdes interiores.</p>
        </div>
        <div class="instalacion-img" style="background-image:url('../img/zona-descanso.jpg')"></div>
      </div>

      <div class="instalacion-card">
        <div class="instalacion-info">
          <h3>Comedor y Cafetería</h3>
          <p>Espacio culinario con menús saludables y vista al jardín.</p>
        </div>
        <div class="instalacion-img" style="background-image:url('../img/comedor.jpg')"></div>
      </div>

      <div class="instalacion-card">
        <div class="instalacion-info">
          <h3>Jardín Exterior</h3>
          <p>Área al aire libre con senderos, bancos y zona de horticultura.</p>
        </div>
        <div class="instalacion-img" style="background-image:url('../img/jardin.jpg')"></div>
      </div>
    </div>
  </main>

  <?php include('../footer.php'); ?>
</body>
</html>
