-- Actualización de la tabla usuarios
-- Agregar campos: apellidos, cargo, foto

USE gestion_documental;

-- Agregar nuevos campos a la tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN apellidos VARCHAR(100) AFTER nombre,
ADD COLUMN cargo VARCHAR(100) AFTER apellidos,
ADD COLUMN foto VARCHAR(255) AFTER cargo;

-- Actualizar el usuario admin con datos completos
UPDATE usuarios 
SET apellidos = 'Sistema',
    cargo = 'Administrador del Sistema',
    foto = NULL
WHERE id = 1;

-- Verificar los cambios
DESCRIBE usuarios;
