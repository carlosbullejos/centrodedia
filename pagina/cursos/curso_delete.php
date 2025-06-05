<?php
require '../login/database.php';
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $conexion->query("DELETE FROM cursos WHERE id=$id");
}
header("Location: cursos.php");
