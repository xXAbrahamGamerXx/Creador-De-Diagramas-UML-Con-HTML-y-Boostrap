<?php
/**
 * app/core/FileManager.php — Gestor de archivos de diagramas (núcleo MVC)
 *
 * FileManager v4
 * - Nombre fijo: diagrama_{id}.json  (sin timestamp)
 * - Verifica existencia del archivo ANTES de guardar la ruta en BD
 * - Crea carpetas para todos los usuarios de la BD al inicializar
 *
 * En la arquitectura MVC los archivos se guardan en public/uploads/usuario_N/
 */
class FileManager {
    private $basePath;
    private $usuarioId;

    public function __construct($usuarioId) {
        $this->usuarioId = $usuarioId;
        // En MVC los uploads están en public/uploads/
        $this->basePath  = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $this->crearCarpetaUsuario($usuarioId);
    }

    /* ── Carpeta de un usuario ──────────────────────────────── */
    private function crearCarpetaUsuario($uid) {
        $dir = $this->basePath . 'usuario_' . $uid;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            file_put_contents($dir . DIRECTORY_SEPARATOR . '.htaccess', "Order Deny,Allow\nDeny from all\n");
        }
        return $dir;
    }

    /* Crea carpeta al registrar o al login (estático) */
    public static function crearCarpetaUsuarioAlta($usuarioId) {
        $base = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $dir  = $base . 'usuario_' . $usuarioId;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) return false;
            file_put_contents($dir . DIRECTORY_SEPARATOR . '.htaccess', "Order Deny,Allow\nDeny from all\n");
        }
        return true;
    }

    /**
     * Crea carpetas para TODOS los usuarios de la BD.
     * Se llama desde scripts de inicialización o al arrancar.
     */
    public static function inicializarTodasLasCarpetas($conn) {
        try {
            $stmt = $conn->query("SELECT id FROM usuarios WHERE activo = 1");
            $ids  = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $base = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
            $creadas = 0;
            foreach ($ids as $uid) {
                $dir = $base . 'usuario_' . $uid;
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                    file_put_contents($dir . DIRECTORY_SEPARATOR . '.htaccess', "Order Deny,Allow\nDeny from all\n");
                    $creadas++;
                }
            }
            return ['total' => count($ids), 'creadas' => $creadas];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Limpia referencias huérfanas en BD (archivo_ruta apunta a archivo inexistente).
     * Pone archivo_ruta = NULL donde el archivo no existe.
     */
    public static function limpiarReferenciasHuerfanas($conn) {
        try {
            $stmt = $conn->query("SELECT id, usuario_id, archivo_ruta FROM diagramas WHERE archivo_ruta IS NOT NULL");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $base = PUBLIC_PATH . DIRECTORY_SEPARATOR;
            $limpiados = 0;
            foreach ($rows as $row) {
                $ruta = $base . str_replace('/', DIRECTORY_SEPARATOR, $row['archivo_ruta']);
                if (!file_exists($ruta)) {
                    $upd = $conn->prepare("UPDATE diagramas SET archivo_ruta = NULL, archivo_tamano = 0 WHERE id = :id");
                    $upd->bindParam(':id', $row['id']);
                    $upd->execute();
                    $limpiados++;
                }
            }
            return ['revisados' => count($rows), 'limpiados' => $limpiados];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* ── Guardar / actualizar ────────────────────────────────── */
    /**
     * Guarda contenido en public/uploads/usuario_N/diagrama_{id}.json
     * SOLO devuelve success=true si el archivo realmente existe tras escribir.
     */
    public function guardarDiagrama($contenido, $titulo, $diagramaId, $nombreArchivo = null) {
        try {
            if (!$diagramaId) throw new Exception('diagramaId obligatorio');

            $dir  = $this->basePath . 'usuario_' . $this->usuarioId;
            if (!is_dir($dir)) $this->crearCarpetaUsuario($this->usuarioId);

            // Nombre del archivo: usar nombre sanitizado si se provee, si no diagrama_{id}
            $nombreBase = isset($nombreArchivo) && $nombreArchivo
                ? $nombreArchivo
                : 'diagrama_' . $diagramaId;
            $file = $nombreBase . '.json';
            $path = $dir . DIRECTORY_SEPARATOR . $file;

            $data = [
                'diagrama_id'    => (int)$diagramaId,
                'titulo'         => $titulo,
                'fecha_guardado' => date('Y-m-d H:i:s'),
                'version_app'    => '4.0',
                'contenido'      => $contenido
            ];

            $bytes = file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

            if ($bytes === false) throw new Exception("No se pudo escribir: $path");

            // Verificación: el archivo debe existir y tener contenido
            if (!file_exists($path) || filesize($path) < 10) {
                throw new Exception("Verificación post-escritura falló: $path");
            }

            // La ruta relativa guarda "uploads/usuario_N/..." para compatibilidad con FileManager::cargarDiagrama
            $ruta = 'uploads/usuario_' . $this->usuarioId . '/' . $file;
            return ['success' => true, 'ruta' => $ruta, 'tamano' => (int)$bytes];

        } catch (Exception $e) {
            error_log("FileManager::guardarDiagrama — " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /* ── Cargar ──────────────────────────────────────────────── */
    public function cargarDiagrama($ruta) {
        try {
            $prefijo = 'uploads/usuario_' . $this->usuarioId . '/';
            if (strpos($ruta, $prefijo) !== 0) {
                throw new Exception("Ruta no válida para usuario {$this->usuarioId}");
            }

            $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta);

            if (!file_exists($path)) throw new Exception("Archivo no encontrado: $ruta");

            $raw  = file_get_contents($path);
            $data = json_decode($raw, true);
            if ($data === null) throw new Exception("JSON inválido: $ruta");

            return ['success' => true, 'contenido' => $data['contenido'] ?? []];

        } catch (Exception $e) {
            error_log("FileManager::cargarDiagrama — " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /* ── Eliminar ────────────────────────────────────────────── */
    public function eliminarDiagrama($ruta) {
        try {
            $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta);
            if (file_exists($path)) unlink($path);
            return true;
        } catch (Exception $e) {
            error_log("FileManager::eliminarDiagrama — " . $e->getMessage());
            return false;
        }
    }

    public function rutaParaDiagrama($diagramaId) {
        return 'uploads/usuario_' . $this->usuarioId . '/diagrama_' . $diagramaId . '.json';
    }
}
?>
