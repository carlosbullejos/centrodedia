<?php
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";

$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
