-- Datos iniciales para gestion_documental
USE gestion_documental;

-- Insertar roles
INSERT INTO roles (id, nombre, descripcion) VALUES
(1, 'Super Administrador', 'Acceso total al sistema'),
(2, 'Administrador', 'Gestión de usuarios y configuración'),
(3, 'Validador', 'Validación y aprobación de documentos'),
(4, 'Gestor', 'Carga y gestión de documentos');

-- Insertar usuario administrador
-- Password: admin123 (hash bcrypt)
-- IMPORTANTE: Si el login no funciona, ejecutar: public/arreglar-password.php
INSERT INTO usuarios (rol_id, nombre, apellidos, cargo, email, password, estado) VALUES
(1, 'Super', 'Administrador', 'Administrador del Sistema', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insertar categorías predeterminadas
INSERT INTO categorias (nombre, descripcion, estado) VALUES
('Contratos', 'Contratos y acuerdos legales', 1),
('Facturas', 'Facturas y comprobantes fiscales', 1),
('Informes', 'Informes y reportes', 1),
('Certificados', 'Certificados y diplomas', 1),
('Actas', 'Actas de reuniones', 1),
('Otros', 'Documentos varios', 1);
