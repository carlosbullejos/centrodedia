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
    <title>Centro de Día - Página Mejorada</title>
  <style>
    <?php include('css/estilos.css'); ?>

  </style>
</head>
<body>
  
<?php include('header.php'); ?>
    <section class="intro">
        <div class="intro-image">
            <img src="img/imagen-central.webp" alt="Centro de Día">
            <div class="intro-text">
                <h2>
                    “Cuidamos con cariño, 
                </h2>
                <h2>
                fortalecemos con dedicación”
                </h2>
            </div>
        </div>
    </section>

    <section class="services">
        <h3>Nuestros Servicios</h3>
        <div class="activity-grid">
         
          <a href="contenido_paginas/actividades_recreativas.php" class="activity-item">
              <img src="img/actividades.jpg" alt="Recreación">
              <p>Actividades recreativas para un día pleno.</p>
        </a>

   
           <a href="contenido_paginas/integracion_social.php" class="activity-item">
               <img src="img/apoyo_emocional.jpg" alt="Integración Social">
               <p>Apoyo emocional e integración social.</p>
           </a>


           <a href="contenido_paginas/talleres_formacion.php" class="activity-item">
               <img src="img/talleres.jpg" alt="Formación">
              <p>Talleres y clases para desarrollo profesional.</p>
           </a>
        </div>
    </section>


    <section class="image-gallery">
  <h3>Galería de Actividades</h3>

  <div class="slideshow-container">

    <!-- Slide 1 -->
    <div class="mySlides fade">
      <img src="img/actividad4.jpeg" alt="Actividad 1">
    </div>

    <!-- Slide 2 -->
    <div class="mySlides fade">
      <img src="img/actividad5.jpeg" alt="Actividad 2">
    </div>

    <!-- Slide 3 -->
    <div class="mySlides fade">
      <img src="img/actividad3.jpg" alt="Actividad 3">
    </div>

    <!-- Slide 4 -->
    <div class="mySlides fade">
      <img src="img/actividad2.jpg" alt="Actividad 4">
    </div>

    <!-- Slide 5 -->
    <div class="mySlides fade">
      <img src="img/actividad1.jpg" alt="Actividad 5">
    </div>

    <!-- Flechas -->
    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>
  </div>

  <!-- Puntos de navegación -->
  <div class="dots-container">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>
    <span class="dot" onclick="currentSlide(4)"></span>
    <span class="dot" onclick="currentSlide(5)"></span>
  </div>
</section>

<script>
let slideIndex = 1;
showSlides(slideIndex);

// Avanza/retrasa en el carrusel
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Ir a un slide específico
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  const slides = document.getElementsByClassName("mySlides");
  const dots   = document.getElementsByClassName("dot");
  if (n > slides.length) { slideIndex = 1; }
  if (n < 1)             { slideIndex = slides.length; }
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (let i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex - 1].style.display = "block";
  dots[slideIndex - 1].className += " active";
}
</script>


    <section class="about-us">
        <h3>¿Quiénes Somos?</h3>
        <p>
            Trabajamos con dedicación para crear un ambiente de aprendizaje, apoyo y desarrollo personal. Nuestra misión es contribuir al bienestar emocional y profesional de nuestros usuarios.
        </p>
    </section>

    <!-- Formulario de Reserva -->
    <section class="reservation-form">
        <center><h3>Formulario de Reserva</h3></center>
        <form action="usuarios_reserva/usuarios_reserva.php" method="post" class="formulario">
             <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea>
            </div>

            <div class="form-actions">
                <input type="submit" value="Enviar" class="btn">
                <input type="reset" value="Restablecer" class="btn btn-reset">
            </div>
        </form>
    </section>

    <section class="location">
        <h3>Ubicación</h3>
        <p>Nos encontramos en Calle Santa Rosa, Maracena.</p>
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3177.7227848532707!2d-3.6353676246775315!3d37.20681334467432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd71fdb1ca4db5b9%3A0xecd337b186341926!2sC.%20Sta.%20Rosa%2C%2018200%20Maracena%2C%20Granada%2C%20Espa%C3%B1a!5e0!3m2!1ses!2sar!4v1733749574168!5m2!1ses!2sar" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <p>© 2024 Centro de Día. Todos los derechos reservados.</p>
            <p>Dirección: Calle Santa Rosa, Maracena</p>
        </div>
    </footer>
</body>
</html>
