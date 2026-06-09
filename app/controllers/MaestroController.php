<?php
/**
 * app/controllers/MaestroController.php — Controlador del Panel Maestro
 *
 * Maneja: vista del panel maestro y toda la API del maestro.
 * Migrado desde: maestro.php y api/maestro_api.php
 */
class MaestroController extends Controller {

    /** GET /maestro */
    public function index() {
        SessionManager::verificarMaestro();
        try {
            FileManager::crearCarpetaUsuarioAlta(SessionManager::usuarioId());
        } catch (Exception $e) {
            error_log('MaestroController::index FileManager error: ' . $e->getMessage());
        }
        $maestroId = SessionManager::usuarioId();
        $this->render('maestro/index', ['maestroId' => $maestroId]);
    }

    /**
     * GET /api/maestro — Despacha todas las acciones de la API del maestro.
     * Parámetro: ?action=nombre_accion
     */
    public function api() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        // Verificar sesión y rol
        if (!SessionManager::estaLogueado()) {
            echo json_encode(['success' => false, 'error' => 'No autenticado']); exit();
        }
        SessionManager::verificarMaestro();

        $maestroId = SessionManager::usuarioId();
        $action    = $_GET['action'] ?? '';
        $body      = json_decode(file_get_contents('php://input'), true) ?? [];

        try {
            $db   = new Database();
            $conn = $db->getConnection();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'BD no conectada: ' . $e->getMessage()]);
            exit();
        }

        try {
            switch ($action) {

                // ── Stats para el inicio ─────────────────────────────
                case 'stats':
                    $grupos = $conn->prepare("SELECT COUNT(*) FROM grupos WHERE maestro_id=:m AND activo=1");
                    $grupos->execute([':m' => $maestroId]);

                    $alumnos = $conn->prepare(
                        "SELECT COUNT(DISTINCT ga.alumno_id) FROM grupo_alumnos ga
                         JOIN grupos g ON ga.grupo_id=g.id WHERE g.maestro_id=:m"
                    );
                    $alumnos->execute([':m' => $maestroId]);

                    $misD = $conn->prepare("SELECT COUNT(*) FROM diagramas WHERE usuario_id=:m");
                    $misD->execute([':m' => $maestroId]);

                    $tareas = $conn->prepare("SELECT COUNT(*) FROM tareas WHERE maestro_id=:m AND activa=1");
                    $tareas->execute([':m' => $maestroId]);

                    $listaG = $conn->prepare(
                        "SELECT g.id,g.nombre,g.codigo,(SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=g.id) AS num_alumnos
                         FROM grupos g WHERE g.maestro_id=:m AND g.activo=1 ORDER BY g.fecha_creacion DESC LIMIT 5"
                    );
                    $listaG->execute([':m' => $maestroId]);

                    $stEsp = $conn->prepare("SELECT COALESCE(SUM(archivo_tamano),0) FROM diagramas WHERE usuario_id=:m");
                    $stEsp->execute([':m' => $maestroId]);
                    $espacioUsado = (int)$stEsp->fetchColumn();

                    try { $conn->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS espacio_limite_mb INT NOT NULL DEFAULT 100"); } catch (Exception $ex2) {}
                    $stLim = $conn->prepare("SELECT COALESCE(espacio_limite_mb, 100) FROM usuarios WHERE id=:m");
                    $stLim->execute([':m' => $maestroId]);
                    $espacioLimite = (int)$stLim->fetchColumn();

                    echo json_encode([
                        'grupos'             => (int)$grupos->fetchColumn(),
                        'alumnos'            => (int)$alumnos->fetchColumn(),
                        'mis_diagramas'      => (int)$misD->fetchColumn(),
                        'tareas'             => (int)$tareas->fetchColumn(),
                        'lista_grupos'       => $listaG->fetchAll(PDO::FETCH_ASSOC),
                        'espacio_usado_bytes'=> $espacioUsado,
                        'espacio_limite_mb'  => $espacioLimite,
                    ]);
                    break;

                // ── Actividad reciente ───────────────────────────────
                case 'actividad_reciente':
                    $stmt = $conn->prepare(
                        "SELECT d.id,d.titulo,d.tipo_diagrama,d.version,d.fecha_modificacion,
                                u.username,u.nombre_completo AS nombre_alumno
                         FROM diagramas d
                         JOIN usuarios u ON d.usuario_id=u.id
                         WHERE u.id IN (
                             SELECT DISTINCT ga.alumno_id FROM grupo_alumnos ga
                             JOIN grupos g ON ga.grupo_id=g.id WHERE g.maestro_id=:m
                         )
                         ORDER BY d.fecha_modificacion DESC LIMIT 10"
                    );
                    $stmt->execute([':m' => $maestroId]);
                    echo json_encode(['diagramas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Grupos del maestro ───────────────────────────────
                case 'grupos':
                    $stmt = $conn->prepare(
                        "SELECT g.*,(SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=g.id) AS num_alumnos
                         FROM grupos g WHERE g.maestro_id=:m AND g.activo=1 ORDER BY g.fecha_creacion DESC"
                    );
                    $stmt->execute([':m' => $maestroId]);
                    echo json_encode(['grupos' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                case 'crear_grupo':
                    $nombre = trim($body['nombre'] ?? '');
                    if (!$nombre) throw new Exception('Nombre requerido');
                    do {
                        $codigo = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
                        $chk    = $conn->prepare("SELECT COUNT(*) FROM grupos WHERE codigo=:c");
                        $chk->execute([':c' => $codigo]);
                    } while ($chk->fetchColumn() > 0);

                    $stmt = $conn->prepare(
                        "INSERT INTO grupos (nombre,descripcion,maestro_id,codigo) VALUES (:n,:d,:m,:c)"
                    );
                    $stmt->execute([':n' => $nombre, ':d' => $body['descripcion'] ?? '', ':m' => $maestroId, ':c' => $codigo]);
                    echo json_encode(['success' => true, 'id' => $conn->lastInsertId(), 'codigo' => $codigo]);
                    break;

                case 'eliminar_grupo':
                    $id  = (int)($body['id'] ?? 0);
                    $chk = $conn->prepare("SELECT maestro_id FROM grupos WHERE id=:id");
                    $chk->execute([':id' => $id]);
                    $row = $chk->fetch();
                    if (!$row || $row['maestro_id'] != $maestroId) throw new Exception('Sin permiso');
                    $conn->prepare("UPDATE grupos SET activo=0 WHERE id=:id")->execute([':id' => $id]);
                    echo json_encode(['success' => true]);
                    break;

                // ── Alumnos de un grupo ──────────────────────────────
                case 'alumnos_grupo':
                    $grupoId = (int)($_GET['grupo_id'] ?? 0);
                    $chk     = $conn->prepare("SELECT id FROM grupos WHERE id=:id AND maestro_id=:m");
                    $chk->execute([':id' => $grupoId, ':m' => $maestroId]);
                    if (!$chk->fetch()) throw new Exception('Grupo no encontrado');

                    $stmt = $conn->prepare(
                        "SELECT u.id,u.username,u.nombre_completo,u.email,u.ultimo_acceso,
                                (SELECT COUNT(*) FROM diagramas WHERE usuario_id=u.id) AS num_diagramas
                         FROM grupo_alumnos ga JOIN usuarios u ON ga.alumno_id=u.id
                         WHERE ga.grupo_id=:gid ORDER BY u.nombre_completo"
                    );
                    $stmt->execute([':gid' => $grupoId]);
                    echo json_encode(['alumnos' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                case 'todos_alumnos':
                    $stmt = $conn->prepare(
                        "SELECT DISTINCT u.id,u.username,u.nombre_completo,u.email,u.ultimo_acceso,
                                g.nombre AS grupo_nombre,
                                (SELECT COUNT(*) FROM diagramas WHERE usuario_id=u.id) AS num_diagramas
                         FROM grupo_alumnos ga
                         JOIN usuarios u ON ga.alumno_id=u.id
                         JOIN grupos g ON ga.grupo_id=g.id
                         WHERE g.maestro_id=:m AND g.activo=1
                         ORDER BY u.nombre_completo"
                    );
                    $stmt->execute([':m' => $maestroId]);
                    echo json_encode(['alumnos' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Diagramas de alumnos ─────────────────────────────
                case 'diagramas_alumno':
                    $alumnoId = (int)($_GET['alumno_id'] ?? 0);
                    $chk      = $conn->prepare(
                        "SELECT COUNT(*) FROM grupo_alumnos ga JOIN grupos g ON ga.grupo_id=g.id
                         WHERE ga.alumno_id=:a AND g.maestro_id=:m"
                    );
                    $chk->execute([':a' => $alumnoId, ':m' => $maestroId]);
                    if (!$chk->fetchColumn()) throw new Exception('Alumno no pertenece a tus grupos');

                    $stmt = $conn->prepare(
                        "SELECT d.*,u.username,u.nombre_completo AS nombre_alumno
                         FROM diagramas d JOIN usuarios u ON d.usuario_id=u.id
                         WHERE d.usuario_id=:a ORDER BY d.fecha_modificacion DESC"
                    );
                    $stmt->execute([':a' => $alumnoId]);
                    echo json_encode(['diagramas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                case 'todos_diagramas_alumnos':
                    $stmt = $conn->prepare(
                        "SELECT d.id,d.titulo,d.tipo_diagrama,d.version,d.fecha_modificacion,
                                u.username,u.nombre_completo AS nombre_alumno
                         FROM diagramas d
                         JOIN usuarios u ON d.usuario_id=u.id
                         WHERE u.id IN (
                             SELECT DISTINCT ga.alumno_id FROM grupo_alumnos ga
                             JOIN grupos g ON ga.grupo_id=g.id WHERE g.maestro_id=:m
                         )
                         ORDER BY d.fecha_modificacion DESC"
                    );
                    $stmt->execute([':m' => $maestroId]);
                    echo json_encode(['diagramas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Tareas ───────────────────────────────────────────
                case 'tareas':
                    // Incluye tareas por grupo Y tareas por proyecto
                    $stmt = $conn->prepare(
                        "SELECT t.*,
                                g.nombre AS grupo_nombre,
                                p.nombre AS proyecto_nombre,
                                u.nombre_completo AS alumno_nombre,
                                (SELECT COUNT(*) FROM entregas WHERE tarea_id=t.id) AS num_entregas,
                                CASE
                                    WHEN t.alumno_id IS NOT NULL THEN 1
                                    WHEN t.proyecto_id IS NOT NULL THEN
                                        (SELECT COUNT(*) FROM proyecto_miembros WHERE proyecto_id=t.proyecto_id)
                                    ELSE
                                        (SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=t.grupo_id)
                                END AS total_alumnos
                         FROM tareas t
                         LEFT JOIN grupos g ON t.grupo_id=g.id
                         LEFT JOIN proyectos p ON t.proyecto_id=p.id
                         LEFT JOIN usuarios u ON t.alumno_id=u.id
                         WHERE t.maestro_id=:m ORDER BY t.fecha_creacion DESC"
                    );
                    $stmt->execute([':m' => $maestroId]);
                    echo json_encode(['tareas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                case 'crear_tarea':
                    $titulo = trim($body['titulo'] ?? '');
                    $modo   = $body['modo'] ?? 'grupo';
                    if (!$titulo) throw new Exception('El título es obligatorio');

                    $fecha = !empty($body['fecha_entrega']) ? $body['fecha_entrega'] : null;
                    $tipo  = $body['tipo_diagrama'] ?? 'usecase';
                    $desc  = $body['descripcion'] ?? '';

                    if ($modo === 'proyecto') {
                        $proyId   = (int)($body['proyecto_id'] ?? 0);
                        $alumnoId = !empty($body['alumno_id']) ? (int)$body['alumno_id'] : null;
                        if (!$proyId) throw new Exception('Proyecto requerido');

                        // Verificar que el maestro pertenece al proyecto
                        $chk = $conn->prepare("SELECT id FROM proyectos WHERE id=:id AND creador_id=:m");
                        $chk->execute([':id' => $proyId, ':m' => $maestroId]);
                        if (!$chk->fetch()) {
                            // También puede ser miembro del proyecto
                            $chk2 = $conn->prepare("SELECT pm.proyecto_id FROM proyecto_miembros pm JOIN proyectos p ON p.id=pm.proyecto_id WHERE pm.proyecto_id=:id AND pm.usuario_id=:m");
                            $chk2->execute([':id' => $proyId, ':m' => $maestroId]);
                            if (!$chk2->fetch()) throw new Exception('Proyecto no válido');
                        }

                        $stmt = $conn->prepare(
                            "INSERT INTO tareas (proyecto_id,alumno_id,maestro_id,titulo,descripcion,tipo_diagrama,fecha_entrega)
                             VALUES (:pid,:aid,:m,:titulo,:desc,:tipo,:fecha)"
                        );
                        $stmt->execute([
                            ':pid' => $proyId, ':aid' => $alumnoId, ':m' => $maestroId,
                            ':titulo' => $titulo, ':desc' => $desc, ':tipo' => $tipo, ':fecha' => $fecha,
                        ]);
                    } else {
                        $grupoId = (int)($body['grupo_id'] ?? 0);
                        if (!$grupoId) throw new Exception('Grupo requerido');

                        $chk = $conn->prepare("SELECT id FROM grupos WHERE id=:id AND maestro_id=:m");
                        $chk->execute([':id' => $grupoId, ':m' => $maestroId]);
                        if (!$chk->fetch()) throw new Exception('Grupo no válido');

                        $stmt = $conn->prepare(
                            "INSERT INTO tareas (grupo_id,maestro_id,titulo,descripcion,tipo_diagrama,fecha_entrega)
                             VALUES (:gid,:m,:titulo,:desc,:tipo,:fecha)"
                        );
                        $stmt->execute([
                            ':gid' => $grupoId, ':m' => $maestroId,
                            ':titulo' => $titulo, ':desc' => $desc, ':tipo' => $tipo, ':fecha' => $fecha,
                        ]);
                    }
                    echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
                    break;

                // ── Ver entregas de una tarea (con nombres) ──────────
                case 'ver_entregas':
                    $tareaId = (int)($_GET['tarea_id'] ?? 0);
                    if (!$tareaId) throw new Exception('tarea_id requerido');

                    // Verificar que la tarea pertenece a este maestro (grupo O proyecto)
                    $chk = $conn->prepare(
                        "SELECT t.*,
                                g.nombre AS grupo_nombre,
                                p.nombre AS proyecto_nombre
                         FROM tareas t
                         LEFT JOIN grupos g ON g.id=t.grupo_id
                         LEFT JOIN proyectos p ON p.id=t.proyecto_id
                         WHERE t.id=:tid AND t.maestro_id=:mid"
                    );
                    $chk->execute([':tid' => $tareaId, ':mid' => $maestroId]);
                    $tarea = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$tarea) throw new Exception('Tarea no encontrada');

                    // Construir lista de alumnos según modo
                    if ($tarea['proyecto_id']) {
                        // Tarea de proyecto: todos los miembros (o solo el asignado)
                        if ($tarea['alumno_id']) {
                            $stmt = $conn->prepare(
                                "SELECT u.id AS alumno_id, u.nombre_completo, u.username,
                                        e.id AS entrega_id, e.diagrama_id, e.comentario_alumno,
                                        e.calificacion, e.comentario AS comentario_maestro,
                                        e.fecha_entrega, e.fecha_calificacion,
                                        d.titulo AS diagrama_titulo, d.tipo_diagrama, d.archivo_ruta
                                 FROM usuarios u
                                 LEFT JOIN entregas e ON e.tarea_id=:tid AND e.alumno_id=u.id
                                 LEFT JOIN diagramas d ON d.id=e.diagrama_id
                                 WHERE u.id=:aid ORDER BY u.nombre_completo ASC"
                            );
                            $stmt->execute([':tid' => $tareaId, ':aid' => $tarea['alumno_id']]);
                        } else {
                            $stmt = $conn->prepare(
                                "SELECT u.id AS alumno_id, u.nombre_completo, u.username,
                                        e.id AS entrega_id, e.diagrama_id, e.comentario_alumno,
                                        e.calificacion, e.comentario AS comentario_maestro,
                                        e.fecha_entrega, e.fecha_calificacion,
                                        d.titulo AS diagrama_titulo, d.tipo_diagrama, d.archivo_ruta
                                 FROM proyecto_miembros pm
                                 JOIN usuarios u ON u.id=pm.usuario_id
                                 LEFT JOIN entregas e ON e.tarea_id=:tid AND e.alumno_id=u.id
                                 LEFT JOIN diagramas d ON d.id=e.diagrama_id
                                 WHERE pm.proyecto_id=:pid ORDER BY u.nombre_completo ASC"
                            );
                            $stmt->execute([':tid' => $tareaId, ':pid' => $tarea['proyecto_id']]);
                        }
                    } else {
                        // Tarea de grupo — comportamiento original
                        $stmt = $conn->prepare(
                            "SELECT u.id AS alumno_id, u.nombre_completo, u.username,
                                    e.id AS entrega_id, e.diagrama_id, e.comentario_alumno,
                                    e.calificacion, e.comentario AS comentario_maestro,
                                    e.fecha_entrega, e.fecha_calificacion,
                                    d.titulo AS diagrama_titulo, d.tipo_diagrama, d.archivo_ruta
                             FROM grupo_alumnos ga
                             JOIN usuarios u ON u.id=ga.alumno_id
                             LEFT JOIN entregas e ON e.tarea_id=:tid AND e.alumno_id=u.id
                             LEFT JOIN diagramas d ON d.id=e.diagrama_id
                             WHERE ga.grupo_id=:gid ORDER BY u.nombre_completo ASC"
                        );
                        $stmt->execute([':tid' => $tareaId, ':gid' => $tarea['grupo_id']]);
                    }
                    $entregas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'tarea' => $tarea, 'entregas' => $entregas]);
                    break;

                // ── Calificar entrega ─────────────────────────────────
                case 'calificar_entrega':
                    $tareaId    = (int)($body['tarea_id']   ?? 0);
                    $alumnoId2  = (int)($body['alumno_id']  ?? 0);
                    $calific    = isset($body['calificacion']) ? (float)$body['calificacion'] : null;
                    $comentario = trim($body['comentario'] ?? '');
                    if (!$tareaId || !$alumnoId2) throw new Exception('Datos incompletos');
                    if ($calific !== null && ($calific < 0 || $calific > 100)) throw new Exception('Calificación debe estar entre 0 y 100');

                    // Verificar que la tarea es de este maestro
                    $chk = $conn->prepare("SELECT id FROM tareas WHERE id=:tid AND maestro_id=:mid");
                    $chk->execute([':tid' => $tareaId, ':mid' => $maestroId]);
                    if (!$chk->fetch()) throw new Exception('Sin permisos');

                    $stmt = $conn->prepare(
                        "UPDATE entregas SET calificacion=:cal, comentario=:com, fecha_calificacion=NOW()
                         WHERE tarea_id=:tid AND alumno_id=:aid"
                    );
                    $stmt->execute([':cal' => $calific, ':com' => $comentario, ':tid' => $tareaId, ':aid' => $alumnoId2]);
                    if ($stmt->rowCount() === 0) {
                        // No existe entrega — crear registro vacío con calificación
                        $conn->prepare(
                            "INSERT INTO entregas (tarea_id, alumno_id, calificacion, comentario, fecha_calificacion)
                             VALUES (:tid,:aid,:cal,:com,NOW())
                             ON DUPLICATE KEY UPDATE calificacion=:cal2, comentario=:com2, fecha_calificacion=NOW()"
                        )->execute([':tid'=>$tareaId,':aid'=>$alumnoId2,':cal'=>$calific,':com'=>$comentario,':cal2'=>$calific,':com2'=>$comentario]);
                    }
                    echo json_encode(['success' => true]);
                    break;

                case 'eliminar_tarea':
                    $id  = (int)($body['id'] ?? 0);
                    $chk = $conn->prepare("SELECT maestro_id FROM tareas WHERE id=:id");
                    $chk->execute([':id' => $id]);
                    $row = $chk->fetch();
                    if (!$row || $row['maestro_id'] != $maestroId) throw new Exception('Sin permiso');
                    $conn->prepare("DELETE FROM tareas WHERE id=:id")->execute([':id' => $id]);
                    echo json_encode(['success' => true]);
                    break;

                // ── Mis diagramas (del maestro) ──────────────────────
                case 'mis_diagramas':
                    $stmt = $conn->prepare(
                        "SELECT id, titulo, tipo_diagrama, version, fecha_modificacion, archivo_ruta,
                                LEFT(contenido_json, 4000) AS contenido_json_preview
                         FROM diagramas WHERE usuario_id=:m ORDER BY fecha_modificacion DESC"
                    );
                    $stmt->execute([':m' => $maestroId]);
                    echo json_encode(['diagramas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Respuestas recientes de alumnos ──────────────────
                case 'respuestas_recientes':
                    // Obtiene las respuestas más recientes de alumnos a observaciones del maestro
                    try {
                        $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS padre_id INT NULL DEFAULT NULL");
                        $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS tipo_obs ENUM('observacion','reporte_error') NOT NULL DEFAULT 'observacion'");
                    } catch (Exception $ex2) {}

                    $stmt = $conn->prepare(
                        "SELECT r.id, r.texto, r.fecha_creacion, r.padre_id, r.tipo_obs,
                                r.proyecto_id, r.diagrama_id,
                                ua.nombre_completo AS alumno_nombre, ua.username AS alumno_username,
                                d.titulo AS diagrama_titulo,
                                p.nombre AS proyecto_nombre,
                                orig.texto AS obs_original
                         FROM proyecto_observaciones r
                         JOIN usuarios ua ON ua.id = r.autor_id
                         LEFT JOIN diagramas d ON d.id = r.diagrama_id
                         LEFT JOIN proyectos p ON p.id = r.proyecto_id
                         LEFT JOIN proyecto_observaciones orig ON orig.id = r.padre_id
                         WHERE r.padre_id IS NOT NULL
                           AND r.autor_id != :m
                           AND (ua.rol = 'alumno' OR ua.rol = 'user')
                           AND orig.autor_id = :m2
                         ORDER BY r.fecha_creacion DESC
                         LIMIT 20"
                    );
                    $stmt->execute([':m' => $maestroId, ':m2' => $maestroId]);
                    echo json_encode(['respuestas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Conversaciones (hilos con actividad reciente) ────
                case 'conversaciones':
                    try {
                        $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS padre_id INT NULL DEFAULT NULL");
                    } catch (Exception $ex2) {}

                    // Trae todas las observaciones raíz del maestro con sus respuestas
                    $stmt = $conn->prepare(
                        "SELECT o.id, o.texto, o.fecha_creacion, o.proyecto_id, o.diagrama_id, o.tipo_obs,
                                d.titulo AS diagrama_titulo,
                                p.nombre AS proyecto_nombre,
                                (SELECT COUNT(*) FROM proyecto_observaciones r WHERE r.padre_id = o.id) AS num_respuestas,
                                (SELECT r2.fecha_creacion FROM proyecto_observaciones r2 WHERE r2.padre_id = o.id ORDER BY r2.fecha_creacion DESC LIMIT 1) AS ultima_respuesta,
                                (SELECT r3.texto FROM proyecto_observaciones r3 WHERE r3.padre_id = o.id ORDER BY r3.fecha_creacion DESC LIMIT 1) AS ultimo_texto,
                                (SELECT u2.nombre_completo FROM proyecto_observaciones r4 JOIN usuarios u2 ON u2.id=r4.autor_id WHERE r4.padre_id = o.id ORDER BY r4.fecha_creacion DESC LIMIT 1) AS ultimo_autor
                         FROM proyecto_observaciones o
                         JOIN proyectos p ON p.id = o.proyecto_id
                         JOIN proyecto_miembros pm ON pm.proyecto_id = p.id AND pm.usuario_id = :m
                         LEFT JOIN diagramas d ON d.id = o.diagrama_id
                         WHERE o.padre_id IS NULL
                           AND o.autor_id = :m2
                         ORDER BY COALESCE((SELECT MAX(r5.fecha_creacion) FROM proyecto_observaciones r5 WHERE r5.padre_id = o.id), o.fecha_creacion) DESC
                         LIMIT 30"
                    );
                    $stmt->execute([':m' => $maestroId, ':m2' => $maestroId]);
                    echo json_encode(['conversaciones' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                default:
                    echo json_encode(['success' => false, 'error' => "Acción '$action' no reconocida"]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }
}
?>
