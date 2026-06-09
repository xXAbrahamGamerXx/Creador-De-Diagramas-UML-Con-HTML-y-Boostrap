-- ============================================================
-- diagramas_v2.sql — DiagramasUML v5.0
-- Roles: alumno | maestro | admin
-- Admin puede crear usuarios y asignar permisos a admins junior
-- ============================================================

CREATE DATABASE IF NOT EXISTS diagramas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE diagramas_db;

-- ─────────────────────────────────────────────────────────────
-- USUARIOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    username         VARCHAR(50)  UNIQUE NOT NULL,
    email            VARCHAR(100) UNIQUE NOT NULL,
    password         VARCHAR(255) NOT NULL,
    nombre_completo  VARCHAR(100),
    rol              ENUM('alumno','maestro','admin') DEFAULT 'alumno',
    es_admin_junior  BOOLEAN DEFAULT FALSE,
    creado_por       INT NULL,
    fecha_registro   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso    TIMESTAMP NULL,
    activo           BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- PERMISOS DE ADMIN JUNIOR
-- Cada fila = un permiso que el admin principal otorgó a un admin junior
-- Permisos disponibles:
--   ver_usuarios        → puede listar usuarios
--   crear_alumnos       → puede crear alumnos
--   crear_maestros      → puede crear maestros
--   editar_usuarios     → puede editar cualquier usuario
--   desactivar_usuarios → puede activar/desactivar usuarios
--   ver_diagramas       → puede ver todos los diagramas
--   eliminar_diagramas  → puede eliminar diagramas
--   ver_grupos          → puede ver grupos y tareas
--   setup_db            → puede ejecutar mantenimiento de BD
--   ver_svgs            → puede verificar/generar SVGs
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_permisos (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    admin_id    INT NOT NULL,
    permiso     VARCHAR(50) NOT NULL,
    otorgado_por INT NOT NULL,
    fecha       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_permiso (admin_id, permiso),
    FOREIGN KEY (admin_id)     REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (otorgado_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- GRUPOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS grupos (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    nombre         VARCHAR(100) NOT NULL,
    descripcion    TEXT,
    maestro_id     INT NOT NULL,
    codigo         VARCHAR(10) UNIQUE NOT NULL,
    activo         BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maestro_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- ALUMNOS EN GRUPOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS grupo_alumnos (
    grupo_id    INT NOT NULL,
    alumno_id   INT NOT NULL,
    fecha_union TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (grupo_id, alumno_id),
    FOREIGN KEY (grupo_id)  REFERENCES grupos(id)   ON DELETE CASCADE,
    FOREIGN KEY (alumno_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- DIAGRAMAS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS diagramas (
    id                 INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id         INT NOT NULL,
    titulo             VARCHAR(200) NOT NULL,
    descripcion        TEXT,
    tipo_diagrama      VARCHAR(50) DEFAULT 'usecase',
    contenido_json     LONGTEXT NULL,
    archivo_ruta       VARCHAR(500) NULL,
    archivo_tamano     INT DEFAULT 0,
    fecha_creacion     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    version            INT DEFAULT 1,
    tamano             INT DEFAULT 0,
    etiquetas          VARCHAR(500),
    compartido         BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha   (fecha_modificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- VERSIONES DE DIAGRAMAS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS versiones_diagrama (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_id    INT NOT NULL,
    version        INT NOT NULL,
    archivo_ruta   VARCHAR(500) NULL,
    contenido_json LONGTEXT NULL,
    fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comentario     VARCHAR(255),
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_version (diagrama_id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- DIAGRAMAS COMPARTIDOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS diagramas_compartidos (
    diagrama_id      INT NOT NULL,
    usuario_id       INT NOT NULL,
    permiso          ENUM('ver','editar') DEFAULT 'ver',
    fecha_compartido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (diagrama_id, usuario_id),
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- TAREAS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tareas (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    grupo_id       INT NOT NULL,
    maestro_id     INT NOT NULL,
    titulo         VARCHAR(200) NOT NULL,
    descripcion    TEXT,
    tipo_diagrama  VARCHAR(50) DEFAULT 'usecase',
    fecha_entrega  DATETIME NULL,
    activa         BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id)   REFERENCES grupos(id)   ON DELETE CASCADE,
    FOREIGN KEY (maestro_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- ENTREGAS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS entregas (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    tarea_id            INT NOT NULL,
    alumno_id           INT NOT NULL,
    diagrama_id         INT NULL,
    comentario_alumno   TEXT NULL COMMENT 'Nota del alumno al entregar',
    calificacion        DECIMAL(5,2) NULL,
    comentario          TEXT NULL COMMENT 'Retroalimentación del maestro',
    fecha_entrega       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_calificacion  TIMESTAMP NULL,
    UNIQUE KEY unique_entrega (tarea_id, alumno_id),
    FOREIGN KEY (tarea_id)    REFERENCES tareas(id)    ON DELETE CASCADE,
    FOREIGN KEY (alumno_id)   REFERENCES usuarios(id)  ON DELETE CASCADE,
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migración: añadir columnas nuevas si la tabla ya existe
ALTER TABLE entregas ADD COLUMN IF NOT EXISTS comentario_alumno TEXT NULL AFTER diagrama_id;
ALTER TABLE entregas ADD COLUMN IF NOT EXISTS fecha_calificacion TIMESTAMP NULL AFTER comentario;

-- ─────────────────────────────────────────────────────────────
-- DATOS DE PRUEBA  (contraseña: "password")
-- ─────────────────────────────────────────────────────────────
INSERT IGNORE INTO usuarios (username, email, password, nombre_completo, rol) VALUES
('admin',    'admin@ejemplo.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin'),
('maestro1', 'maestro@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Prof. García',  'maestro'),
('alumno1',  'alumno@ejemplo.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Pérez',    'alumno');

-- ─────────────────────────────────────────────────────────────
-- MIGRACIÓN DESDE v4 (si ya tienes la BD levantada)
-- Ejecuta este bloque en phpMyAdmin si vienes de la versión anterior
-- ─────────────────────────────────────────────────────────────
/*
ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS es_admin_junior BOOLEAN DEFAULT FALSE AFTER rol,
    ADD COLUMN IF NOT EXISTS creado_por INT NULL AFTER es_admin_junior,
    ADD CONSTRAINT fk_creado_por FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS admin_permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    permiso VARCHAR(50) NOT NULL,
    otorgado_por INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_permiso (admin_id, permiso),
    FOREIGN KEY (admin_id)     REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (otorgado_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

-- ─────────────────────────────────────────────────────────────
-- TABLA: user_config  (tema y colores por usuario)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS user_config (
    user_id    INT          NOT NULL PRIMARY KEY,
    theme      VARCHAR(10)  NOT NULL DEFAULT 'dark',
    primary_color  VARCHAR(7)   NOT NULL DEFAULT '#667eea',
    primary2_color VARCHAR(7)   NOT NULL DEFAULT '#764ba2',
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- Agregar columnas a entregas para soporte de calificación y archivo adjunto
-- ─────────────────────────────────────────────────────────────
ALTER TABLE entregas
    ADD COLUMN IF NOT EXISTS archivo_adjunto VARCHAR(500) NULL COMMENT 'Ruta de archivo JSON adjunto externo',
    ADD COLUMN IF NOT EXISTS comentario_alumno TEXT NULL COMMENT 'Comentario del alumno al entregar',
    ADD COLUMN IF NOT EXISTS fecha_calificacion TIMESTAMP NULL;

-- ─────────────────────────────────────────────────────────────
-- PROYECTOS COLABORATIVOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS proyectos (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    nombre      VARCHAR(200) NOT NULL,
    descripcion TEXT,
    codigo      VARCHAR(12) UNIQUE NOT NULL,
    creador_id  INT NOT NULL,
    activo      BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS proyecto_miembros (
    proyecto_id INT NOT NULL,
    usuario_id  INT NOT NULL,
    rol         ENUM('owner','editor') DEFAULT 'editor',
    fecha_union TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (proyecto_id, usuario_id),
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS proyecto_diagramas (
    proyecto_id INT NOT NULL,
    diagrama_id INT NOT NULL,
    agregado_por INT NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (proyecto_id, diagrama_id),
    FOREIGN KEY (proyecto_id)  REFERENCES proyectos(id)  ON DELETE CASCADE,
    FOREIGN KEY (diagrama_id)  REFERENCES diagramas(id)  ON DELETE CASCADE,
    FOREIGN KEY (agregado_por) REFERENCES usuarios(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- ARCHIVOS DE PROYECTOS COLABORATIVOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS proyecto_archivos (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    proyecto_id     INT NOT NULL,
    subido_por      INT NOT NULL,
    nombre_original VARCHAR(500) NOT NULL,
    nombre_disco    VARCHAR(64)  NOT NULL COMMENT 'UUID v4 — ruta real en disco',
    mime_type       VARCHAR(120),
    tamano          INT UNSIGNED DEFAULT 0,
    extension       VARCHAR(20),
    fecha_subida    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (subido_por)  REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permisos de usuarios en proyectos (granular)
ALTER TABLE proyecto_miembros
    ADD COLUMN IF NOT EXISTS puede_subir    BOOLEAN DEFAULT TRUE  COMMENT 'Puede subir archivos',
    ADD COLUMN IF NOT EXISTS puede_editar   BOOLEAN DEFAULT TRUE  COMMENT 'Puede editar diagramas',
    ADD COLUMN IF NOT EXISTS puede_eliminar BOOLEAN DEFAULT FALSE COMMENT 'Puede eliminar archivos/diagramas',
    ADD COLUMN IF NOT EXISTS solo_lectura   BOOLEAN DEFAULT FALSE COMMENT 'Solo puede ver';

-- Migración tabla tareas: asegurar columna activa existe
ALTER TABLE tareas ADD COLUMN IF NOT EXISTS activa BOOLEAN DEFAULT TRUE AFTER fecha_entrega;
