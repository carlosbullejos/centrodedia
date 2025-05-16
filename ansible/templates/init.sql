-- init.sql

-- 1) Crear la base de datos
CREATE DATABASE IF NOT EXISTS mi_basedatos;
USE mi_basedatos;

-- 2) Crear las tablas
CREATE TABLE trabajadores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    dni VARCHAR(9) NOT NULL UNIQUE,
    telefono VARCHAR(15) NOT NULL,
    puesto VARCHAR(50) NOT NULL,
    fecha_contratacion DATE NOT NULL,
    especialidad VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL
);

CREATE TABLE usuarios (
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
    FOREIGN KEY(trabajador_id)
      REFERENCES trabajadores(id)
      ON DELETE SET NULL
);

CREATE TABLE inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(100),
    unidades INT,
    fecha_ingreso DATE,
    proveedor VARCHAR(100),
    estado VARCHAR(50),
    imagen VARCHAR(255)
);

CREATE TABLE tareas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(150),
    tipo VARCHAR(50)
);

CREATE TABLE tarea_usuario (
    tarea_id   INT NOT NULL,
    usuario_id INT NOT NULL,
    PRIMARY KEY (tarea_id, usuario_id),
    FOREIGN KEY (tarea_id)
      REFERENCES tareas(id)
      ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)
      REFERENCES usuarios(id)
      ON DELETE CASCADE
);

CREATE TABLE usuarios_reserva (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200),
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    descripcion VARCHAR(250)
);

CREATE TABLE alumnos (
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
    FOREIGN KEY(profesor_id)
      REFERENCES trabajadores(id)
      ON DELETE CASCADE
);

CREATE TABLE usuarios_tareas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    tarea_id INT,
    fecha_asignacion DATE,
    estado VARCHAR(20),
    FOREIGN KEY(usuario_id)
      REFERENCES usuarios(id)
      ON DELETE CASCADE,
    FOREIGN KEY(tarea_id)
      REFERENCES tareas(id)
      ON DELETE CASCADE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    ftp_password VARCHAR(255),
    rol ENUM('usuario','trabajador','alumno','admin') DEFAULT 'usuario'
);

