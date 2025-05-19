<?php
// Configuración de la conexión
$servidor = "mysql-service:3306";
$usuario = "root";
$password = "my_passwd";
$basedatos = "mi_basedatos";

try {
    // Conexión inicial para crear la base de datos
    $conexion = new mysqli($servidor, $usuario, $password);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    echo "Conexión al servidor exitosa.<br>";

    // Crear la base de datos
    $sql_crear_bd = "CREATE DATABASE IF NOT EXISTS $basedatos";
    if (!$conexion->query($sql_crear_bd)) {
        throw new Exception("Error creando la base de datos: " . $conexion->error);
    }
    echo "La base de datos '$basedatos' está lista.<br>";

    // Seleccionar la base de datos
    $conexion->select_db($basedatos);

    // Sentencias para crear las tablas
    $sql_insercion = [
        "CREATE TABLE trabajadores (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL,
            apellidos VARCHAR(50) NOT NULL,
            dni VARCHAR(9) NOT NULL UNIQUE,
            telefono VARCHAR(15) NOT NULL,
            puesto VARCHAR(50) NOT NULL,
            fecha_contratacion DATE NOT NULL,
            especialidad VARCHAR(50) NOT NULL,
            fecha_nacimiento DATE NOT NULL
        )",
        "CREATE TABLE usuarios (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL,
            apellidos VARCHAR(50) NOT NULL,
            dni VARCHAR(9) NOT NULL UNIQUE,
            fecha_nacimiento DATE NOT NULL,
            telefono VARCHAR(15),
            direccion VARCHAR(50) NOT NULL,
            localidad VARCHAR(50) NOT NULL,
            fecha_ingreso DATE NOT NULL,
            dependencia VARCHAR(50),
            patologias VARCHAR(50) NOT NULL,
            trabajador_id INT,
            FOREIGN KEY(trabajador_id) REFERENCES trabajadores(id) ON DELETE SET NULL
        )",
        "CREATE TABLE inventario (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL,
            ubicacion VARCHAR(100),
            unidades INT,
            fecha_ingreso DATE,
            proveedor VARCHAR(100),
            estado VARCHAR(50),
            imagen VARCHAR(255)
        )",
        "CREATE TABLE tarea_usuario (
            tarea_id   INT NOT NULL,
            usuario_id INT NOT NULL,
            PRIMARY KEY (tarea_id, usuario_id),
            FOREIGN KEY (tarea_id)   REFERENCES tareas(id)   ON DELETE CASCADE,
            FOREIGN KEY (usuario_id)  REFERENCES usuarios(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE tareas (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            descripcion VARCHAR(150),
            tipo VARCHAR(50)
        )",
           "CREATE TABLE usuarios_reserva (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(200),
            email VARCHAR(100) NOT NULL,
            telefono VARCHAR(20),
            descripcion VARCHAR(250)
        )",
        "CREATE TABLE alumnos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL,
            apellidos VARCHAR(50) NOT NULL,
            dni VARCHAR(15) UNIQUE NOT NULL,
            fecha_nacimiento DATE NOT NULL,
            telefono VARCHAR(15),
            direccion VARCHAR(100),
            localidad VARCHAR(50),
            fecha_ingreso DATE,
            profesor_id INT,
            FOREIGN KEY(profesor_id) REFERENCES trabajadores(id) ON DELETE CASCADE
        )",
        "CREATE TABLE usuarios_tareas (
            id INT PRIMARY KEY AUTO_INCREMENT,
            usuario_id INT,
            tarea_id INT,
            fecha_asignacion DATE,
            estado VARCHAR(20),
            FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY(tarea_id) REFERENCES tareas(id) ON DELETE CASCADE
        )",
        "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100),
            email VARCHAR(100) UNIQUE,
            password VARCHAR(255),
            ftp_password VARCHAR(255), 
            rol ENUM('usuario', 'trabajador', 'alumno', 'admin') DEFAULT 'usuario'
        )",

    ];

    // Crear las tablas
    foreach ($sql_insercion as $query) {
        if ($conexion->query($query)) {
            echo "Tabla creada correctamente: " . explode(' ', trim($query), 4)[2] . "<br>";
        } else {
            throw new Exception("Error creando tabla: " . $conexion->error);
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Cerrar la conexión
    $conexion->close();
}
?>
