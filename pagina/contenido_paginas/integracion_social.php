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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integración Social</title>
    <style>
        <?php include('../css/estilos.css'); ?>
        .features p { font-size: 1.1em; margin: 10px 0; }
        .photo-gallery { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin: 20px 0; }
        .photo-gallery img { width: calc(25% - 10px); border-radius: 8px; }
        .back-button { margin-top: 20px; }
    </style>
</head>
<body>
<?php include('../header.php'); ?>
    <center>
        <h1>Integración Social</h1>
        <p>Nuestro programa de integración social promueve la participación activa y el sentido de comunidad. A través de actividades grupales y eventos especiales, fomentamos relaciones positivas y habilidades interpersonales.</p>
        <div class="features">
            <p><strong>Talleres de comunicación</strong> para mejorar la expresión oral y la escucha activa.</p>
            <p><strong>Grupos de discusión</strong> sobre temas de actualidad y cultura.</p>
            <p><strong>Actividades lúdicas colaborativas</strong> (juegos de equipo, retos creativos).</p>
            <p><strong>Eventos comunitarios</strong> como deportes adaptados y festivales internos.</p>
            <p><strong>Voluntariado</strong> y salidas conjuntas a centros vecinales y culturales.</p>
        </div>
        <h2>Momento Destacado</h2>
        <p>Cada mes organizamos una salida grupal a un espacio cultural o recreativo para reforzar la convivencia y el intercambio intergeneracional.</p>
        <h2>Galería de Fotos</h2>
        <div class="photo-gallery">
            <!-- Aquí puedes poner hasta 4 fotos -->
            <img src="../img/apoyo1.jpeg" alt="Foto 1">
            <img src="../img/apoyo2.png" alt="Foto 2">
            <img src="../img/apoyo3.jpg" alt="Foto 3">
           
        </div>
        <div class="back-button">
            <a href="../index.php" class="btn">← Volver al inicio</a>
        </div>
    </center>
<?php include('../footer.php'); ?>
</body>
</html>
