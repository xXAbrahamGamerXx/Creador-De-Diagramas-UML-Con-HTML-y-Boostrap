-- ============================================================
-- diagramas_api_tabla.sql
-- Tabla paralela exclusiva para la API REST
-- NO modifica ni afecta el sistema MVC existente
--
-- Proyecto: DiagramasMVC v16 + API REST Node.js
-- ============================================================

USE diagramas_db;

-- ─────────────────────────────────────────────────────────────
-- TABLA PRINCIPAL: diagramas_api
-- Almacena el JSON completo del diagrama (sin depender de archivos)
-- El campo diagrama_original_id conecta opcionalmente con
-- la tabla 'diagramas' del sistema local (puede ser NULL)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS diagramas_api (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_original_id INT NULL        COMMENT 'ID en tabla diagramas del sistema MVC (puede ser NULL)',
    usuario_id          INT NOT NULL     COMMENT 'FK a usuarios.id',
    titulo              VARCHAR(200) NOT NULL,
    descripcion         TEXT,
    tipo_diagrama       VARCHAR(50) DEFAULT 'usecase',
    contenido_json      LONGTEXT NOT NULL COMMENT 'JSON completo del diagrama',
    hash_contenido      VARCHAR(64) NULL  COMMENT 'SHA-256 del JSON — detectar cambios',
    version             INT DEFAULT 1,
    etiquetas           VARCHAR(500),
    compartido          BOOLEAN DEFAULT FALSE COMMENT 'TRUE = aparece en /api/diagramas/publicos',
    fecha_creacion      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE CASCADE,

    FOREIGN KEY (diagrama_original_id)
        REFERENCES diagramas(id) ON DELETE SET NULL,

    INDEX idx_usuario   (usuario_id),
    INDEX idx_fecha     (fecha_modificacion),
    INDEX idx_compartido (compartido)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────────────────────
-- TABLA OPCIONAL: diagramas_api_historial
-- Guarda versiones anteriores para posible versionado
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS diagramas_api_historial (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    diagrama_api_id INT NOT NULL     COMMENT 'FK a diagramas_api.id',
    version         INT NOT NULL,
    contenido_json  LONGTEXT NOT NULL,
    hash_contenido  VARCHAR(64) NULL,
    guardado_en     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comentario      VARCHAR(255) NULL,

    FOREIGN KEY (diagrama_api_id)
        REFERENCES diagramas_api(id) ON DELETE CASCADE,

    UNIQUE KEY unique_version_api (diagrama_api_id, version)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────────────────────
-- Verificar que las tablas se crearon correctamente
-- ─────────────────────────────────────────────────────────────
-- SHOW TABLES LIKE 'diagramas_api%';
-- DESCRIBE diagramas_api;
