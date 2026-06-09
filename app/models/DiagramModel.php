<?php
/**
 * app/models/DiagramModel.php — Modelo de Diagramas
 *
 * Contiene toda la lógica de base de datos relacionada con diagramas.
 * Migrado desde includes/functions.php (clase DiagramFunctions).
 * Ahora extiende Model y usa $this->conn centralizado.
 */
class DiagramModel extends Model {

    // ─────────────────────────────────────────────────────────────────
    // GUARDAR (nuevo diagrama)
    // Flujo: INSERT en BD → obtener ID → escribir JSON fijo → UPDATE ruta
    // ─────────────────────────────────────────────────────────────────
    public function guardar($usuario_id, $titulo, $tipo, $contenido, $descripcion = '', $etiquetas = '', $nombreArchivo = null) {
        try {
            $this->conn->beginTransaction();

            // Contenido siempre como array
            if (is_string($contenido)) {
                $contenido = json_decode($contenido, true) ?: [];
            }

            // ── Anti-duplicado: si ya existe un diagrama con el mismo archivo
            // para este usuario (por si el JS perdió el id), redirigir a actualizar.
            if ($nombreArchivo) {
                $rutaBuscar = 'uploads/usuario_' . $usuario_id . '/' . $nombreArchivo . '.json';
                $stmtCheck  = $this->conn->prepare(
                    "SELECT id FROM diagramas WHERE usuario_id = :uid AND archivo_ruta = :ruta LIMIT 1"
                );
                $stmtCheck->execute([':uid' => $usuario_id, ':ruta' => $rutaBuscar]);
                $existeId = $stmtCheck->fetchColumn();
                if ($existeId) {
                    // Ya existe — delegar a actualizar en vez de duplicar
                    if ($this->conn->inTransaction()) $this->conn->rollBack();
                    return $this->actualizar(
                        (int)$existeId, $usuario_id, $titulo, $tipo, $contenido,
                        $descripcion, $etiquetas, $nombreArchivo
                    );
                }
            }

            // 1a. Validar que no exista ya un diagrama con mismo titulo+tipo para este usuario
            $stmtDup = $this->conn->prepare(
                "SELECT COUNT(*) FROM diagramas WHERE usuario_id=:uid AND titulo=:titulo AND tipo_diagrama=:tipo"
            );
            $stmtDup->execute([':uid'=>$usuario_id, ':titulo'=>$titulo, ':tipo'=>$tipo]);
            if ((int)$stmtDup->fetchColumn() > 0) {
                $this->conn->rollBack();
                // Return a distinct error key so the editor can offer "open existing"
                $stExist = $this->conn->prepare("SELECT id FROM diagramas WHERE usuario_id=:uid AND titulo=:titulo AND tipo_diagrama=:tipo LIMIT 1");
                $stExist->execute([':uid'=>$usuario_id, ':titulo'=>$titulo, ':tipo'=>$tipo]);
                $existId = (int)$stExist->fetchColumn();
                return ['success'=>false, 'duplicate'=>true, 'existing_id'=>$existId,
                        'error'=>'Ya tienes un diagrama "' . $titulo . '" de tipo "' . $tipo . '". Usa un nombre diferente.'];
            }

            // 1. Insertar registro para obtener el ID
            $stmt = $this->conn->prepare(
                "INSERT INTO diagramas
                   (usuario_id, titulo, tipo_diagrama, descripcion, etiquetas,
                    fecha_creacion, fecha_modificacion, version)
                 VALUES
                   (:uid, :titulo, :tipo, :desc, :tags, NOW(), NOW(), 1)"
            );
            $stmt->bindParam(':uid',   $usuario_id);
            $stmt->bindParam(':titulo',$titulo);
            $stmt->bindParam(':tipo',  $tipo);
            $stmt->bindParam(':desc',  $descripcion);
            $stmt->bindParam(':tags',  $etiquetas);
            $stmt->execute();
            $id = (int) $this->conn->lastInsertId();

            // 2. Escribir archivo JSON con nombre basado en título sanitizado
            $fm             = new FileManager($usuario_id);
            $archNombre     = $nombreArchivo ?: $this->sanitizarNombreArchivo($titulo, $id);
            $result         = $fm->guardarDiagrama($contenido, $titulo, $id, $archNombre);
            if (!$result['success']) {
                throw new Exception("Error al escribir archivo: " . ($result['error'] ?? ''));
            }

            // 3. Guardar ruta en BD
            $stmt2 = $this->conn->prepare(
                "UPDATE diagramas SET archivo_ruta = :ruta, archivo_tamano = :tam WHERE id = :id"
            );
            $stmt2->bindParam(':ruta', $result['ruta']);
            $stmt2->bindParam(':tam',  $result['tamano']);
            $stmt2->bindParam(':id',   $id);
            $stmt2->execute();

            $this->conn->commit();
            return ['success' => true, 'id' => $id];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("DiagramModel::guardar — " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // ACTUALIZAR (diagrama existente)
    // Solo sobreescribe el mismo archivo JSON — no genera archivos nuevos
    // ─────────────────────────────────────────────────────────────────
    public function actualizar($diagrama_id, $usuario_id, $titulo, $tipo, $contenido, $descripcion = '', $etiquetas = '', $nombreArchivo = null) {
        try {
            if (!$this->verificarPropiedad($diagrama_id, $usuario_id)) {
                throw new Exception('Sin permiso para editar este diagrama');
            }

            if (is_string($contenido)) {
                $contenido = json_decode($contenido, true) ?: [];
            }

            // 1. Sobreescribir el archivo JSON (nombre basado en título)
            // Si hay archivo viejo con nombre diferente, borrarlo para evitar huérfanos
            $fm         = new FileManager($usuario_id);
            $archNombre = $nombreArchivo ?: $this->sanitizarNombreArchivo($titulo, $diagrama_id);

            // Borrar archivo viejo si el nombre cambió
            $stmtOld = $this->conn->prepare("SELECT archivo_ruta FROM diagramas WHERE id = :id");
            $stmtOld->bindParam(':id', $diagrama_id);
            $stmtOld->execute();
            $rutaVieja = $stmtOld->fetchColumn();
            $rutaNueva = 'uploads/usuario_' . $usuario_id . '/' . $archNombre . '.json';
            if ($rutaVieja && $rutaVieja !== $rutaNueva && file_exists(PUBLIC_PATH . '/' . $rutaVieja)) {
                @unlink(PUBLIC_PATH . '/' . $rutaVieja);
            }

            $result = $fm->guardarDiagrama($contenido, $titulo, $diagrama_id, $archNombre);
            if (!$result['success']) {
                throw new Exception("Error al escribir archivo: " . ($result['error'] ?? ''));
            }

            // 2. Actualizar metadatos en BD
            $stmt = $this->conn->prepare(
                "UPDATE diagramas
                 SET titulo = :titulo, tipo_diagrama = :tipo,
                     descripcion = :desc, etiquetas = :tags,
                     archivo_ruta = :ruta, archivo_tamano = :tam,
                     version = version + 1, fecha_modificacion = NOW()
                 WHERE id = :id AND usuario_id = :uid"
            );
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':tipo',   $tipo);
            $stmt->bindParam(':desc',   $descripcion);
            $stmt->bindParam(':tags',   $etiquetas);
            $stmt->bindParam(':ruta',   $result['ruta']);
            $stmt->bindParam(':tam',    $result['tamano']);
            $stmt->bindParam(':id',     $diagrama_id);
            $stmt->bindParam(':uid',    $usuario_id);
            $stmt->execute();

            // Obtener versión actual para devolverla
            $stmtV = $this->conn->prepare("SELECT version FROM diagramas WHERE id = :id");
            $stmtV->bindParam(':id', $diagrama_id);
            $stmtV->execute();
            $version = (int) $stmtV->fetchColumn();

            return ['success' => true, 'version' => $version];

        } catch (Exception $e) {
            error_log("DiagramModel::actualizar — " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // OBTENER un diagrama con su contenido
    // ─────────────────────────────────────────────────────────────────
    /** Obtiene un diagrama sin verificar propietario (para maestros/admins) */
    private function cargarContenidoDiagrama($row) {
        if (!$row) return null;
        if (!empty($row['archivo_ruta'])) {
            $fm  = new FileManager($row['usuario_id']);
            $res = $fm->cargarDiagrama($row['archivo_ruta']);
            $row['contenido'] = $res['success'] ? $res['contenido'] : [];
        } elseif (!empty($row['contenido_json'])) {
            $row['contenido'] = json_decode($row['contenido_json'], true) ?: [];
        } else {
            $row['contenido'] = [];
        }
        unset($row['contenido_json']);
        return $row;
    }

    public function obtenerCualquiera($diagrama_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM diagramas WHERE id = :id");
            $stmt->bindParam(':id', $diagrama_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->cargarContenidoDiagrama($row);
        } catch (Exception $e) {
            error_log("DiagramModel::obtenerCualquiera — " . $e->getMessage());
            return null;
        }
    }

    public function obtenerAccesible($diagrama_id, $usuario_id, $proyecto_id = null) {
        try {
            if ($proyecto_id) {
                $stmt = $this->conn->prepare(
                    "SELECT d.* FROM diagramas d
                     JOIN proyecto_diagramas pd ON pd.diagrama_id = d.id
                     JOIN proyecto_miembros pm ON pm.proyecto_id = pd.proyecto_id AND pm.usuario_id = :uid
                     WHERE d.id = :id AND pd.proyecto_id = :pid LIMIT 1"
                );
                $stmt->bindParam(':uid', $usuario_id);
                $stmt->bindParam(':id',  $diagrama_id);
                $stmt->bindParam(':pid', $proyecto_id);
            } else {
                $stmt = $this->conn->prepare(
                    "SELECT d.* FROM diagramas d
                     WHERE d.id = :id AND (
                        d.usuario_id = :uid OR EXISTS (
                            SELECT 1 FROM proyecto_diagramas pd
                            JOIN proyecto_miembros pm ON pm.proyecto_id = pd.proyecto_id AND pm.usuario_id = :uid2
                            WHERE pd.diagrama_id = d.id
                        )
                     ) LIMIT 1"
                );
                $stmt->bindParam(':uid',  $usuario_id);
                $stmt->bindParam(':uid2', $usuario_id);
                $stmt->bindParam(':id',   $diagrama_id);
            }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->cargarContenidoDiagrama($row);
        } catch (Exception $e) {
            error_log("DiagramModel::obtenerAccesible — " . $e->getMessage());
            return null;
        }
    }

    public function obtener($diagrama_id, $usuario_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM diagramas WHERE id = :id AND usuario_id = :uid"
            );
            $stmt->bindParam(':id',  $diagrama_id);
            $stmt->bindParam(':uid', $usuario_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->cargarContenidoDiagrama($row);
        } catch (Exception $e) {
            error_log("DiagramModel::obtener — " . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // ELIMINAR
    // ─────────────────────────────────────────────────────────────────
    public function eliminar($diagrama_id, $usuario_id) {
        try {
            if (!$this->verificarPropiedad($diagrama_id, $usuario_id)) {
                throw new Exception('Sin permiso para eliminar');
            }

            // Obtener ruta antes de borrar
            $stmt = $this->conn->prepare("SELECT archivo_ruta FROM diagramas WHERE id = :id");
            $stmt->bindParam(':id', $diagrama_id);
            $stmt->execute();
            $ruta = $stmt->fetchColumn();

            // Borrar de BD
            $stmt2 = $this->conn->prepare(
                "DELETE FROM diagramas WHERE id = :id AND usuario_id = :uid"
            );
            $stmt2->bindParam(':id',  $diagrama_id);
            $stmt2->bindParam(':uid', $usuario_id);
            $stmt2->execute();

            // Borrar archivo físico
            if ($ruta) {
                $fm = new FileManager($usuario_id);
                $fm->eliminarDiagrama($ruta);
            }

            return ['success' => true];

        } catch (Exception $e) {
            error_log("DiagramModel::eliminar — " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // RENOMBRAR
    // ─────────────────────────────────────────────────────────────────
    public function renombrar($diagrama_id, $usuario_id, $nuevo_titulo) {
        try {
            if (!$this->verificarPropiedad($diagrama_id, $usuario_id)) {
                throw new Exception('Sin permiso para renombrar este diagrama');
            }
            $titulo = trim($nuevo_titulo);
            if (empty($titulo)) throw new Exception('El título no puede estar vacío');
            $stmt = $this->conn->prepare(
                "UPDATE diagramas SET titulo=:t, fecha_modificacion=NOW() WHERE id=:id AND usuario_id=:uid"
            );
            $stmt->bindParam(':t',   $titulo);
            $stmt->bindParam(':id',  $diagrama_id);
            $stmt->bindParam(':uid', $usuario_id);
            $stmt->execute();
            return ['success' => true, 'titulo' => $titulo];
        } catch (Exception $e) {
            error_log("DiagramModel::renombrar — " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // DUPLICAR
    // ─────────────────────────────────────────────────────────────────
    public function duplicar($diagrama_id, $usuario_id) {
        $original = $this->obtener($diagrama_id, $usuario_id);
        if (!$original) return ['success' => false, 'error' => 'Diagrama no encontrado'];

        // Generar nombre de archivo único para que guardar() no lo detecte
        // como duplicado por nombre y lo rediriga a actualizar
        $tituloBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $original['titulo']);
        $tituloBase = trim(substr($tituloBase, 0, 40), '_');
        $nombreArchivo = $tituloBase . '_copia_' . time();

        return $this->guardar(
            $usuario_id,
            $original['titulo'] . ' (copia)',
            $original['tipo_diagrama'],
            $original['contenido'] ?? [],
            $original['descripcion'] ?? '',
            $original['etiquetas'] ?? '',
            $nombreArchivo
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // LISTAR
    // ─────────────────────────────────────────────────────────────────
    public function listar($usuario_id, $filtro = '', $pagina = 1, $por_pagina = 12) {
        try {
            $offset = ($pagina - 1) * $por_pagina;
            $q = "SELECT id, titulo, descripcion, tipo_diagrama, etiquetas,
                         fecha_creacion, fecha_modificacion, version, archivo_tamano,
                         archivo_ruta, usuario_id,
                         LEFT(contenido_json, 4000) AS contenido_json_preview,
                         (SELECT COUNT(*) FROM versiones_diagrama WHERE diagrama_id = d.id) AS num_versiones
                  FROM diagramas d
                  WHERE usuario_id = :uid";

            if (!empty($filtro)) $q .= " AND (titulo LIKE :f OR descripcion LIKE :f OR etiquetas LIKE :f)";
            $q .= " ORDER BY fecha_modificacion DESC LIMIT :offset, :pp";

            $stmt = $this->conn->prepare($q);
            $stmt->bindParam(':uid', $usuario_id);
            if (!empty($filtro)) { $fp = "%$filtro%"; $stmt->bindParam(':f', $fp); }
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':pp',     $por_pagina, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("DiagramModel::listar — " . $e->getMessage());
            return [];
        }
    }

    public function contar($usuario_id, $filtro = '') {
        try {
            $q = "SELECT COUNT(*) FROM diagramas WHERE usuario_id = :uid";
            if (!empty($filtro)) $q .= " AND (titulo LIKE :f OR descripcion LIKE :f OR etiquetas LIKE :f)";
            $stmt = $this->conn->prepare($q);
            $stmt->bindParam(':uid', $usuario_id);
            if (!empty($filtro)) { $fp = "%$filtro%"; $stmt->bindParam(':f', $fp); }
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (Exception $e) { return 0; }
    }

    // ─────────────────────────────────────────────────────────────────
    // ESTADÍSTICAS del usuario
    // ─────────────────────────────────────────────────────────────────
    public function estadisticas($usuario_id) {
        try {
            $s = [];
            foreach ([
                'total_diagramas'     => "SELECT COUNT(*) FROM diagramas WHERE usuario_id=:u",
                'espacio_usado'       => "SELECT COALESCE(SUM(archivo_tamano),0) FROM diagramas WHERE usuario_id=:u",
                'ultima_modificacion' => "SELECT MAX(fecha_modificacion) FROM diagramas WHERE usuario_id=:u"
            ] as $key => $sql) {
                $st = $this->conn->prepare($sql);
                $st->bindParam(':u', $usuario_id);
                $st->execute();
                $s[$key] = $st->fetchColumn();
            }

            $st = $this->conn->prepare(
                "SELECT tipo_diagrama, COUNT(*) AS count FROM diagramas WHERE usuario_id=:u GROUP BY tipo_diagrama ORDER BY count DESC"
            );
            $st->bindParam(':u', $usuario_id);
            $st->execute();
            $s['por_tipo'] = $st->fetchAll(PDO::FETCH_ASSOC);

            return $s;
        } catch (Exception $e) {
            return ['total_diagramas'=>0,'por_tipo'=>[],'ultima_modificacion'=>null,'espacio_usado'=>0];
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Verificar propiedad
    // ─────────────────────────────────────────────────────────────────
    /**
     * Genera un nombre de archivo seguro basado en el título del diagrama.
     * Solo letras, números y guiones bajos. Máx 60 chars.
     * Ej: "Mi Diagrama de Clases" → "mi_diagrama_de_clases_42"
     */
    private function sanitizarNombreArchivo(string $titulo, int $id): string {
        // Convertir a minúsculas, quitar acentos básicos
        $nombre = mb_strtolower($titulo, 'UTF-8');
        $nombre = str_replace(
            ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù'],
            ['a','e','i','o','u','u','n','a','e','i','o','u'],
            $nombre
        );
        // Solo alfanuméricos y espacios/guiones
        $nombre = preg_replace('/[^a-z0-9\s_\-]/', '', $nombre);
        // Espacios y guiones múltiples → guion bajo
        $nombre = preg_replace('/[\s\-_]+/', '_', $nombre);
        // Quitar _ al inicio/fin y limitar longitud
        $nombre = trim($nombre, '_');
        $nombre = substr($nombre, 0, 60);
        // Si queda vacío, usar fallback
        if (empty($nombre)) {
            $nombre = 'diagrama_' . $id;
        } else {
            // Añadir ID para que sea único incluso si dos diagramas tienen el mismo título
            $nombre = $nombre . '_' . $id;
        }
        return $nombre;
    }

    public function verificarPropiedad($diagrama_id, $usuario_id) {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM diagramas WHERE id=:id AND usuario_id=:uid");
            $stmt->bindParam(':id', $diagrama_id);
            $stmt->bindParam(':uid',$usuario_id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) { return false; }
    }

    // ─────────────────────────────────────────────────────────────────
    // ADMIN: listar todos los diagramas
    // ─────────────────────────────────────────────────────────────────
    public function listarTodos($filtro = '') {
        try {
            $q = "SELECT d.id, d.titulo, d.tipo_diagrama, d.fecha_modificacion, d.version,
                         u.username, u.nombre_completo
                  FROM diagramas d
                  JOIN usuarios u ON u.id = d.usuario_id";
            if (!empty($filtro)) {
                $q .= " WHERE d.titulo LIKE :f OR u.username LIKE :f";
            }
            $q .= " ORDER BY d.fecha_modificacion DESC LIMIT 200";
            $stmt = $this->conn->prepare($q);
            if (!empty($filtro)) { $fp = "%$filtro%"; $stmt->bindParam(':f', $fp); }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("DiagramModel::listarTodos — " . $e->getMessage());
            return [];
        }
    }
}
?>
