-- ============================================================
-- DiagramasUML — Base de datos MASTER v33
-- Script limpio: crea todo desde cero
-- Contraseña de usuarios de prueba: "password"
-- ============================================================

CREATE DATABASE IF NOT EXISTS diagramas_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE diagramas_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS auditoria_accesos;
DROP TABLE IF EXISTS sistema_config;
DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS proyecto_bitacora;
DROP TABLE IF EXISTS proyecto_observaciones;
DROP TABLE IF EXISTS proyecto_tareas_entregas;
DROP TABLE IF EXISTS proyecto_tareas;
DROP TABLE IF EXISTS proyecto_archivos;
DROP TABLE IF EXISTS proyecto_diagramas;
DROP TABLE IF EXISTS proyecto_miembros;
DROP TABLE IF EXISTS proyectos;
DROP TABLE IF EXISTS entregas;
DROP TABLE IF EXISTS tareas;
DROP TABLE IF EXISTS diagramas_api_historial;
DROP TABLE IF EXISTS diagramas_api;
DROP TABLE IF EXISTS diagramas_compartidos;
DROP TABLE IF EXISTS versiones_diagrama;
DROP TABLE IF EXISTS diagramas;
DROP TABLE IF EXISTS grupo_alumnos;
DROP TABLE IF EXISTS grupos;
DROP TABLE IF EXISTS user_config;
DROP TABLE IF EXISTS admin_permisos;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;

-- ─────────────────────────────────────────────────────────────
-- 1. USUARIOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE usuarios (
    id                INT PRIMARY KEY AUTO_INCREMENT,
    username          VARCHAR(50)  UNIQUE NOT NULL,
    email             VARCHAR(100) UNIQUE NOT NULL,
    password          VARCHAR(255) NOT NULL,
    nombre_completo   VARCHAR(100),
    rol               ENUM('alumno','maestro','admin') DEFAULT 'alumno',
    es_admin_junior   BOOLEAN      DEFAULT FALSE,
    creado_por        INT          NULL,
    espacio_limite_mb INT          NOT NULL DEFAULT 100,
    fecha_registro    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso     TIMESTAMP    NULL,
    activo            BOOLEAN      DEFAULT TRUE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_rol    (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 2. PERMISOS DE ADMIN JUNIOR
-- ─────────────────────────────────────────────────────────────
CREATE TABLE admin_permisos (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    admin_id     INT NOT NULL,
    permiso      VARCHAR(50) NOT NULL,
    otorgado_por INT NOT NULL,
    fecha        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_permiso (admin_id, permiso),
    FOREIGN KEY (admin_id)     REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (otorgado_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 3. CONFIGURACIÓN DE TEMA POR USUARIO
-- ─────────────────────────────────────────────────────────────
CREATE TABLE user_config (
    user_id        INT         NOT NULL PRIMARY KEY,
    theme          VARCHAR(10) NOT NULL DEFAULT 'dark',
    primary_color  VARCHAR(7)  NOT NULL DEFAULT '#667eea',
    primary2_color VARCHAR(7)  NOT NULL DEFAULT '#764ba2',
    sidebar_color  VARCHAR(7)  NULL,          -- color personalizado del sidebar (NULL = usar primary)
    updated_at     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 4. GRUPOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE grupos (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    nombre         VARCHAR(100) NOT NULL,
    descripcion    TEXT,
    maestro_id     INT          NOT NULL,
    codigo         VARCHAR(10)  UNIQUE NOT NULL,
    activo         BOOLEAN      DEFAULT TRUE,
    fecha_creacion TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maestro_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_maestro (maestro_id),
    INDEX idx_codigo  (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 5. ALUMNOS EN GRUPOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE grupo_alumnos (
    grupo_id    INT NOT NULL,
    alumno_id   INT NOT NULL,
    fecha_union TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (grupo_id, alumno_id),
    FOREIGN KEY (grupo_id)  REFERENCES grupos(id)   ON DELETE CASCADE,
    FOREIGN KEY (alumno_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 6. DIAGRAMAS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE diagramas (
    id                 INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id         INT          NOT NULL,
    titulo             VARCHAR(200) NOT NULL,
    descripcion        TEXT,
    tipo_diagrama      VARCHAR(50)  DEFAULT 'usecase',
    contenido_json     LONGTEXT     NULL,
    archivo_ruta       VARCHAR(500) NULL,
    archivo_tamano     INT          DEFAULT 0,
    version            INT          DEFAULT 1,
    tamano             INT          DEFAULT 0,
    etiquetas          VARCHAR(500),
    compartido         BOOLEAN      DEFAULT FALSE,
    fecha_creacion     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario    (usuario_id),
    INDEX idx_tipo       (tipo_diagrama),
    INDEX idx_fecha      (fecha_modificacion),
    INDEX idx_compartido (compartido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 7. VERSIONES DE DIAGRAMA
-- ─────────────────────────────────────────────────────────────
CREATE TABLE versiones_diagrama (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_id    INT          NOT NULL,
    version        INT          NOT NULL,
    archivo_ruta   VARCHAR(500) NULL,
    contenido_json LONGTEXT     NULL,
    comentario     VARCHAR(255),
    fecha_guardado TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_diag_version (diagrama_id, version),
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 8. DIAGRAMAS COMPARTIDOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE diagramas_compartidos (
    diagrama_id      INT NOT NULL,
    usuario_id       INT NOT NULL,
    permiso          ENUM('ver','editar') DEFAULT 'ver',
    fecha_compartido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (diagrama_id, usuario_id),
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 9. DIAGRAMAS API (exclusiva de la API REST Node.js)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE diagramas_api (
    id                   INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_original_id INT          NULL,
    usuario_id           INT          NOT NULL,
    titulo               VARCHAR(200) NOT NULL,
    descripcion          TEXT,
    tipo_diagrama        VARCHAR(50)  DEFAULT 'usecase',
    contenido_json       LONGTEXT     NOT NULL,
    hash_contenido       VARCHAR(64)  NULL,
    version              INT          DEFAULT 1,
    etiquetas            VARCHAR(500),
    compartido           BOOLEAN      DEFAULT FALSE,
    fecha_creacion       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id)           REFERENCES usuarios(id)  ON DELETE CASCADE,
    FOREIGN KEY (diagrama_original_id) REFERENCES diagramas(id) ON DELETE SET NULL,
    INDEX idx_api_usuario    (usuario_id),
    INDEX idx_api_fecha      (fecha_modificacion),
    INDEX idx_api_compartido (compartido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 10. HISTORIAL DE DIAGRAMAS API
-- ─────────────────────────────────────────────────────────────
CREATE TABLE diagramas_api_historial (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_api_id INT          NOT NULL,
    version         INT          NOT NULL,
    contenido_json  LONGTEXT     NOT NULL,
    hash_contenido  VARCHAR(64)  NULL,
    comentario      VARCHAR(255) NULL,
    guardado_en     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_api_version (diagrama_api_id, version),
    FOREIGN KEY (diagrama_api_id) REFERENCES diagramas_api(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 11. TAREAS (por grupo o por proyecto, vía MaestroController)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE tareas (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    grupo_id       INT          NULL,
    proyecto_id    INT          NULL,
    alumno_id      INT          NULL,
    maestro_id     INT          NOT NULL,
    titulo         VARCHAR(200) NOT NULL,
    descripcion    TEXT,
    tipo_diagrama  VARCHAR(50)  DEFAULT 'usecase',
    fecha_entrega  DATETIME     NULL,
    activa         BOOLEAN      DEFAULT TRUE,
    fecha_creacion TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maestro_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tareas_grupo    (grupo_id),
    INDEX idx_tareas_proyecto (proyecto_id),
    INDEX idx_tareas_alumno   (alumno_id),
    INDEX idx_tareas_activa   (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 12. ENTREGAS (alumno entrega diagrama a tarea de grupo)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE entregas (
    id                 INT PRIMARY KEY AUTO_INCREMENT,
    tarea_id           INT          NOT NULL,
    alumno_id          INT          NOT NULL,
    diagrama_id        INT          NULL,
    archivo_adjunto    VARCHAR(500) NULL,
    comentario_alumno  TEXT         NULL,
    calificacion       DECIMAL(5,2) NULL,
    comentario         TEXT         NULL,
    fecha_entrega      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    fecha_calificacion TIMESTAMP    NULL,
    UNIQUE KEY uq_entrega (tarea_id, alumno_id),
    FOREIGN KEY (tarea_id)    REFERENCES tareas(id)    ON DELETE CASCADE,
    FOREIGN KEY (alumno_id)   REFERENCES usuarios(id)  ON DELETE CASCADE,
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 13. PROYECTOS COLABORATIVOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyectos (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    nombre         VARCHAR(200) NOT NULL,
    descripcion    TEXT,
    codigo         VARCHAR(12)  UNIQUE NOT NULL,
    creador_id     INT          NOT NULL,
    activo         BOOLEAN      DEFAULT TRUE,
    fecha_creacion TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_proy_codigo  (codigo),
    INDEX idx_proy_creador (creador_id),
    INDEX idx_proy_activo  (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 14. MIEMBROS DE PROYECTO
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_miembros (
    proyecto_id    INT NOT NULL,
    usuario_id     INT NOT NULL,
    rol            ENUM('owner','editor','viewer') DEFAULT 'editor',
    puede_subir    BOOLEAN DEFAULT TRUE,
    puede_editar   BOOLEAN DEFAULT TRUE,
    puede_eliminar BOOLEAN DEFAULT FALSE,
    solo_lectura   BOOLEAN DEFAULT FALSE,
    puede_invitar  BOOLEAN DEFAULT FALSE,
    fecha_union    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (proyecto_id, usuario_id),
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 15. DIAGRAMAS DE PROYECTO (con calificación por diagrama)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_diagramas (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    proyecto_id    INT          NOT NULL,
    diagrama_id    INT          NOT NULL,
    agregado_por   INT          NOT NULL,
    calificacion   DECIMAL(4,2) NULL,
    comentario_cal TEXT         NULL,
    calificado_por INT          NULL,
    fecha_cal      DATETIME     NULL,
    fecha_agregado TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_proy_diagrama (proyecto_id, diagrama_id),
    FOREIGN KEY (proyecto_id)  REFERENCES proyectos(id)  ON DELETE CASCADE,
    FOREIGN KEY (diagrama_id)  REFERENCES diagramas(id)  ON DELETE CASCADE,
    FOREIGN KEY (agregado_por) REFERENCES usuarios(id)   ON DELETE CASCADE,
    INDEX idx_pd_fecha (fecha_agregado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 16. ARCHIVOS DE PROYECTO
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_archivos (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    proyecto_id     INT           NOT NULL,
    subido_por      INT           NOT NULL,
    nombre_original VARCHAR(500)  NOT NULL,
    nombre_disco    VARCHAR(1024) NOT NULL,
    mime_type       VARCHAR(120),
    tamano          INT UNSIGNED  DEFAULT 0,
    extension       VARCHAR(20),
    fecha_subida    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (subido_por)  REFERENCES usuarios(id)  ON DELETE CASCADE,
    INDEX idx_arch_proyecto (proyecto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 17. TAREAS DE PROYECTO (gestionadas por ProyectoController)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_tareas (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    proyecto_id    INT          NOT NULL,
    creador_id     INT          NOT NULL,
    titulo         VARCHAR(300) NOT NULL,
    descripcion    TEXT,
    asignado_a     INT          NULL,
    fecha_limite   DATE         NULL,
    estado         ENUM('pendiente','en_progreso','entregada','calificada') DEFAULT 'pendiente',
    calificacion   DECIMAL(4,2) NULL,
    comentario_cal TEXT         NULL,
    fecha_creacion DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (creador_id)  REFERENCES usuarios(id)  ON DELETE CASCADE,
    FOREIGN KEY (asignado_a)  REFERENCES usuarios(id)  ON DELETE SET NULL,
    INDEX idx_pt_proyecto (proyecto_id),
    INDEX idx_pt_asignado (asignado_a),
    INDEX idx_pt_estado   (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 18. ENTREGAS DE TAREAS DE PROYECTO
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_tareas_entregas (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    tarea_id    INT      NOT NULL,
    usuario_id  INT      NOT NULL,
    texto       TEXT     NULL,
    diagrama_id INT      NULL,
    fecha       DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pte (tarea_id, usuario_id),
    FOREIGN KEY (tarea_id)    REFERENCES proyecto_tareas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)        ON DELETE CASCADE,
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id)       ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 19. OBSERVACIONES DE PROYECTO
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_observaciones (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    proyecto_id    INT        NULL,
    diagrama_id    INT        NULL,
    autor_id       INT        NOT NULL,
    texto          TEXT       NOT NULL,
    padre_id       INT        NULL DEFAULT NULL,
    tipo_obs       ENUM('observacion','reporte_error') NOT NULL DEFAULT 'observacion',
    leida          TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion DATETIME   DEFAULT CURRENT_TIMESTAMP,
    fecha_edicion  DATETIME   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (diagrama_id) REFERENCES diagramas(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id)    REFERENCES usuarios(id)  ON DELETE CASCADE,
    INDEX idx_obs_proyecto (proyecto_id),
    INDEX idx_obs_diagrama (diagrama_id),
    INDEX idx_obs_autor    (autor_id),
    INDEX idx_obs_leida    (leida),
    INDEX idx_obs_padre    (padre_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 20. BITÁCORA DE PROYECTO
-- ─────────────────────────────────────────────────────────────
CREATE TABLE proyecto_bitacora (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    proyecto_id INT          NOT NULL,
    usuario_id  INT          NOT NULL,
    accion      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha       DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE,
    INDEX idx_bit_proyecto (proyecto_id),
    INDEX idx_bit_fecha    (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 21. NOTIFICACIONES
-- ─────────────────────────────────────────────────────────────
CREATE TABLE notificaciones (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT          NOT NULL,
    tipo       VARCHAR(50)  NOT NULL DEFAULT 'info',
    titulo     VARCHAR(200) NOT NULL,
    mensaje    TEXT,
    url        VARCHAR(500),
    leida      TINYINT(1)   NOT NULL DEFAULT 0,
    fecha      DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_noti_usuario (usuario_id),
    INDEX idx_noti_leida   (leida),
    INDEX idx_noti_fecha   (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 22. CONFIGURACIÓN DEL SISTEMA
-- ─────────────────────────────────────────────────────────────
CREATE TABLE sistema_config (
    clave   VARCHAR(100) PRIMARY KEY,
    valor   TEXT,
    tipo    VARCHAR(20)  DEFAULT 'string',
    updated DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sistema_config (clave, valor, tipo) VALUES
    ('app_nombre',         'DiagramasUML', 'string'),
    ('modo_mantenimiento', '0',            'boolean'),
    ('smtp_host',          '',             'string'),
    ('smtp_port',          '587',          'integer'),
    ('smtp_user',          '',             'string'),
    ('smtp_pass',          '',             'string'),
    ('smtp_from',          '',             'string'),
    ('smtp_from_name',     'DiagramasUML', 'string');

-- ─────────────────────────────────────────────────────────────
-- 23. AUDITORÍA DE ACCESOS
-- ─────────────────────────────────────────────────────────────
CREATE TABLE auditoria_accesos (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT          NULL,
    username   VARCHAR(100),
    accion     VARCHAR(100) NOT NULL,
    ip         VARCHAR(45),
    detalle    TEXT,
    fecha      DATETIME     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_aud_fecha  (fecha),
    INDEX idx_aud_accion (accion),
    INDEX idx_aud_user   (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- USUARIOS DE PRUEBA  (contraseña: password)
-- ─────────────────────────────────────────────────────────────
INSERT INTO usuarios (username, email, password, nombre_completo, rol, espacio_limite_mb) VALUES
    ('admin',    'admin@ejemplo.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin',   0),
    ('maestro1', 'maestro@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Prof. Garcia',  'maestro', 500),
    ('alumno1',  'alumno@ejemplo.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Perez',    'alumno',  100);

INSERT INTO user_config (user_id, theme, primary_color, primary2_color)
SELECT id, 'light', '#667eea', '#764ba2' FROM usuarios;
