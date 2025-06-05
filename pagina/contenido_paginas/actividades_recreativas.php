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
    <title>Actividades Recreativas</title>
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
        <h1>Actividades Recreativas</h1>
        <p>En nuestro Centro de Día ofrecemos un completo programa de actividades recreativas diseñado para fomentar el bienestar físico, emocional y social de nuestros usuarios. Con profesionales especializados, cada jornada incluye:</p>
        <div class="features">
            <p><strong>Gimnasia y ejercicio adaptado</strong> para mantener la movilidad.</p>
            <p><strong>Terapia de movimiento</strong> (baile, tai‑chi) que potencia la coordinación.</p>
            <p><strong>Juegos de memoria</strong> y estimulación cognitiva.</p>
            <p><strong>Salida al aire libre</strong> y paseos guiados por parques.</p>
            <p><strong>Manualidades</strong> y talleres de arte para estimular la creatividad.</p>
        </div>
        <h2>Horario semanal</h2>
        <table>
            <thead>
                <tr><th>Día</th><th>Actividad Principal</th><th>Hora</th></tr>
            </thead>
            <tbody>
                <tr><td>Lunes</td><td>Gimnasia suave</td><td>10:00 – 11:00</td></tr>
                <tr><td>Martes</td><td>Taller de pintura</td><td>11:30 – 13:00</td></tr>
                <tr><td>Miércoles</td><td>Salida al parque</td><td>10:00 – 12:00</td></tr>
                <tr><td>Jueves</td><td>Baile terapia</td><td>11:00 – 12:00</td></tr>
                <tr><td>Viernes</td><td>Juegos de memoria</td><td>10:30 – 11:30</td></tr>
            </tbody>
        </table>
        <h2>Galería de Fotos</h2>
        <div class="photo-gallery">
            
            <img src="../img/ejercicios1.jpg" alt="Foto 1">
            <img src="../img/ejercicios2.jpg" alt="Foto 2">
            <img src="../img/ejercicios3.jpg" alt="Foto 3">
            
        </div>
        <div class="back-btn">
      <a href="../index.php" class="add-button">← Volver al inicio</a>
    </div>
    </center>
<?php include('../footer.php'); ?>
</body>
</html>
