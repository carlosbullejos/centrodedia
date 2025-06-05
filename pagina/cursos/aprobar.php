<?php
require '../login/database.php';
session_start();

// Control de acceso
if (!in_array($_SESSION['user']['rol'], ['trabajador','admin'])) {
    header('Location: /index.php');
    exit;
}

// 1) Capturar correctamente los parámetros
$sid    = intval($_GET['sid']    ?? 0);
$idCurso = intval($_GET['curso'] ?? 0);

if (!$sid || !$idCurso) {
    die("Solicitud o Curso no válidos.");
}

// 2) Marcar solicitud como aprobada
$conexion->query("
    UPDATE solicitudes 
       SET estado='aprobada' 
     WHERE id=$sid
");

// 3) Insertar la matrícula real
$conexion->query("
    INSERT INTO matriculas (alumno_id, curso_id)
    SELECT alumno_id, curso_id
      FROM solicitudes
     WHERE id=$sid
");

// 4) Redirigir de nuevo a la lista de solicitudes para ese curso
header("Location: solicitudes.php?curso={$idCurso}");
exit;
