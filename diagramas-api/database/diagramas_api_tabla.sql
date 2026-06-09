-- ============================================================
-- diagramas_api_tabla.sql
-- 
-- INSTRUCCIONES:
--   1. Abre phpMyAdmin
--   2. Selecciona la base de datos: diagramas_db
--   3. Ve a la pestaña "SQL"
--   4. Pega todo este contenido y ejecuta
--
-- Este script crea las tablas exclusivas para la API.
-- NO modifica ni afecta el sistema MVC existente.
-- ============================================================

USE diagramas_db;

-- ─────────────────────────────────────────────────────────────
-- TABLA PRINCIPAL: diagramas_api
-- Guarda el JSON completo del diagrama (sin depender de archivos locales)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS diagramas_api (
    id                   INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_original_id INT NULL        COMMENT 'Conexion opcional con tabla diagramas del MVC',
    usuario_id           INT NOT NULL    COMMENT 'FK a usuarios.id',
    titulo               VARCHAR(200) NOT NULL,
    descripcion          TEXT,
    tipo_diagrama        VARCHAR(50) DEFAULT 'usecase',
    contenido_json       LONGTEXT NOT NULL COMMENT 'JSON completo del diagrama',
    hash_contenido       VARCHAR(64) NULL  COMMENT 'SHA-256 del JSON para detectar cambios',
    version              INT DEFAULT 1,
    etiquetas            VARCHAR(500),
    compartido           BOOLEAN DEFAULT FALSE COMMENT 'TRUE = visible en /api/diagramas/publicos',
    fecha_creacion       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                         ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE CASCADE,

    FOREIGN KEY (diagrama_original_id)
        REFERENCES diagramas(id) ON DELETE SET NULL,

    INDEX idx_usuario    (usuario_id),
    INDEX idx_fecha      (fecha_modificacion),
    INDEX idx_compartido (compartido)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────────────────────
-- TABLA OPCIONAL: diagramas_api_historial
-- Guarda versiones anteriores de cada diagrama
-- (útil si en el futuro quieres ver el historial de cambios)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS diagramas_api_historial (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_api_id INT NOT NULL     COMMENT 'FK a diagramas_api.id',
    version         INT NOT NULL,
    contenido_json  LONGTEXT NOT NULL,
    guardado_en     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comentario      VARCHAR(255) NULL,

    FOREIGN KEY (diagrama_api_id)
        REFERENCES diagramas_api(id) ON DELETE CASCADE,

    UNIQUE KEY unique_version_api (diagrama_api_id, version)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Verificar que se crearon bien (opcional, puedes descomentar):
-- SHOW TABLES LIKE 'diagramas_api%';
-- DESCRIBE diagramas_api;
