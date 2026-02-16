# 📁 Sistema de Gestión Documental

Sistema completo de gestión documental con validación de documentos, timbres digitales, reportes y auditoría.

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## 🚀 Características

### 📄 Gestión de Documentos
- ✅ Subida de múltiples formatos (PDF, DOC, XLS, imágenes)
- ✅ Visualización en modal (PDFs e imágenes)
- ✅ Sistema de validación (Aprobar/Rechazar)
- ✅ Estados: Pendiente, Aprobado, Rechazado
- ✅ Categorización de documentos
- ✅ Observaciones en validaciones

### 🔐 Timbres Digitales
- ✅ **Opción A:** Timbre visual flotante al ver documentos
- ✅ **Opción B:** Timbre permanente en PDF (modifica el archivo)
- ✅ Certificado digital HTML con código QR
- ✅ Código único de validación
- ✅ Datos: Validador, cargo, fecha, hora

### 👥 Gestión de Usuarios
- ✅ 4 roles: Super Admin, Admin, Validador, Gestor
- ✅ Permisos granulares por rol
- ✅ Nombre, apellidos, cargo, foto de perfil
- ✅ CRUD completo de usuarios

### 📊 Reportes Avanzados
- ✅ 6 reportes con gráficos (Chart.js)
- ✅ Por estado, mes, usuario, categoría
- ✅ Tiempos de validación
- ✅ Exportación a Excel (.xlsx)

### 🔍 Auditoría
- ✅ Registro automático de 7 acciones
- ✅ Login/Logout, CRUD usuarios, validaciones
- ✅ IP, User Agent, datos antes/después (JSON)
- ✅ Filtros por fecha, usuario, acción

### 📈 Dashboard
- ✅ Estadísticas en tiempo real
- ✅ Gráfico de documentos por mes
- ✅ Documentos recientes
- ✅ Dashboards personalizados por rol

### ⚙️ Configuración
- ✅ Backup de base de datos
- ✅ Limpieza de caché
- ✅ Optimización de sistema

## 🛠️ Tecnologías

- **Backend:** PHP 7.4+ (MVC personalizado)
- **Base de Datos:** MySQL 5.7+
- **Frontend:** AdminLTE 3.2, Bootstrap 4, jQuery
- **Gráficos:** Chart.js 3.9
- **Tablas:** DataTables 1.13
- **PDF:** FPDF + FPDI (opcional)

## 📦 Instalación

### Requisitos Previos

- XAMPP / WAMP / MAMP (Apache + MySQL + PHP 7.4+)
- Navegador moderno (Chrome, Firefox, Edge)

### Paso 1: Clonar el Repositorio

```bash
cd C:\xampp\htdocs
git clone https://github.com/TU-USUARIO/sistema-gestion-documental.git
```

### Paso 2: Configurar Base de Datos

1. Crear base de datos en phpMyAdmin:
```sql
CREATE DATABASE gestion_documental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importar esquema:
```bash
# En phpMyAdmin, importar en este orden:
database/schema.sql
database/data.sql
```

### Paso 3: Configurar Archivos

```bash
# Copiar archivo de configuración
cp app/config/database.php.example app/config/database.php

# Editar y ajustar valores:
# - DB_HOST (normalmente 'localhost')
# - DB_NAME (gestion_documental)
# - DB_USER (root)
# - DB_PASS (vacío en XAMPP)
```

### Paso 4: Crear Carpetas de Upload

```bash
mkdir -p public/uploads/documentos
mkdir -p public/uploads/usuarios
mkdir -p public/uploads/certificados
mkdir -p public/uploads/temporal
```

O ejecutar:
```
http://localhost/sistema-gestion-documental/public/crear-carpetas.php
```

### Paso 5: (Opcional) Instalar Librerías para Timbres PDF

Para modificar PDFs permanentemente:

```
http://localhost/sistema-gestion-documental/public/instalar-timbre-automatico.php
```

### Paso 6: Acceder al Sistema

```
URL: http://localhost/sistema-gestion-documental
Usuario: admin@sistema.com
Contraseña: admin123
```

## 📁 Estructura del Proyecto

```
sistema-gestion-documental/
├── app/
│   ├── config/           # Configuración
│   ├── controllers/      # Controladores MVC
│   ├── models/          # Modelos de datos
│   ├── views/           # Vistas
│   ├── helpers/         # Clases auxiliares
│   └── libraries/       # Librerías externas (no versionadas)
├── core/                # Núcleo MVC
├── database/            # Scripts SQL
├── public/              # Archivos públicos
│   ├── css/
│   ├── js/
│   ├── uploads/        # Archivos subidos (no versionados)
│   └── index.php       # Punto de entrada
├── .gitignore
├── .htaccess
└── README.md
```

## 🔒 Seguridad

- ✅ SQL preparado (previene inyección SQL)
- ✅ Sesiones seguras
- ✅ Validación de archivos subidos
- ✅ Permisos por rol
- ✅ Hashing de contraseñas
- ✅ Protección CSRF (tokens)
- ✅ Auditoría completa

## 👥 Roles y Permisos

| Funcionalidad | Super Admin | Admin | Validador | Gestor |
|---------------|-------------|-------|-----------|---------|
| Gestión usuarios | ✅ | ✅ | ❌ | ❌ |
| Gestión categorías | ✅ | ✅ | ❌ | ❌ |
| Subir documentos | ✅ | ✅ | ✅ | ✅ |
| Ver todos los docs | ✅ | ✅ | ✅ | ❌ |
| Validar documentos | ✅ | ✅ | ✅ | ❌ |
| Ver reportes | ✅ | ✅ | ✅ | ❌ |
| Ver auditoría | ✅ | ✅ | ❌ | ❌ |
| Configuración | ✅ | ❌ | ❌ | ❌ |

## 📊 Usuarios por Defecto

| Email | Contraseña | Rol |
|-------|------------|-----|
| admin@sistema.com | admin123 | Super Admin |
| admin2@sistema.com | admin123 | Admin |
| validador@sistema.com | validador123 | Validador |
| gestor@sistema.com | gestor123 | Gestor |

**⚠️ Cambiar contraseñas en producción**

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

## 👨‍💻 Autor

Desarrollado con ❤️

---

⭐ Si te gusta este proyecto, dale una estrella en GitHub
