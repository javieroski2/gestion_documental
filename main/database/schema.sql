-- Base de Datos: gestion_documental
-- Sistema de Gestión Documental

CREATE DATABASE IF NOT EXISTS gestion_documental;
USE gestion_documental;

-- Tabla de roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rol_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100),
    cargo VARCHAR(100),
    foto VARCHAR(255),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de documentos
CREATE TABLE documentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categoria_id INT NOT NULL,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    extension VARCHAR(10),
    tamano INT,
    estado_validacion ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    validador_id INT,
    fecha_validacion DATETIME,
    observaciones TEXT,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (validador_id) REFERENCES usuarios(id)
);

-- Tabla de historial de validaciones
CREATE TABLE historial_validaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    documento_id INT NOT NULL,
    validador_id INT NOT NULL,
    accion ENUM('aprobado', 'rechazado') NOT NULL,
    observaciones TEXT,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (documento_id) REFERENCES documentos(id),
    FOREIGN KEY (validador_id) REFERENCES usuarios(id)
);

-- Tabla de auditoría
CREATE TABLE auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla VARCHAR(50),
    registro_id INT,
    datos_anteriores TEXT,
    datos_nuevos TEXT,
    ip VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Índices para mejor rendimiento
CREATE INDEX idx_documentos_estado ON documentos(estado_validacion);
CREATE INDEX idx_documentos_usuario ON documentos(usuario_id);
CREATE INDEX idx_documentos_categoria ON documentos(categoria_id);
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_auditoria_fecha ON auditoria(fecha);
