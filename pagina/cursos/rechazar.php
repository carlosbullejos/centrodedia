<?php
require 'login/database.php'; session_start();
$sid = intval($_GET['sid']);
$conexion->query("UPDATE solicitudes SET estado='rechazada' WHERE id=$sid");
header("Location: solicitudes.php?curso=".intval($_GET['c']));
