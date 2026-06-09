<?php
/**
 * app/controllers/AlumnoController.php — API del Alumno
 *
 * Gestiona: grupos del alumno (unirse, salir) y tareas asignadas.
 * Migrado desde: api/alumno_api.php
 *
 * Endpoints:
 *   GET  /api/alumno?action=mis_grupos
 *   POST /api/alumno?action=unirse_grupo
 *   POST /api/alumno?action=salir_grupo
 *   GET  /api/alumno?action=mis_tareas
 *   POST /api/alumno?action=entregar_tarea
 */
class AlumnoController extends Controller {

    public function api() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        SessionManager::verificarAcceso();

        $alumnoId = SessionManager::usuarioId();
        $action   = $_GET['action'] ?? '';
        $body     = json_decode(file_get_contents('php://input'), true) ?? [];

        try {
            $db   = new Database();
            $conn = $db->getConnection();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'BD no conectada: ' . $e->getMessage()]);
            exit();
        }

        try {
            switch ($action) {

                // ── Grupos del alumno ─────────────────────────────────
                case 'mis_grupos':
                    $stmt = $conn->prepare(
                        "SELECT g.id, g.nombre, g.descripcion, g.codigo,
                                u.nombre_completo AS maestro_nombre,
                                (SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=g.id) AS num_alumnos
                         FROM grupo_alumnos ga
                         JOIN grupos g ON ga.grupo_id=g.id
                         JOIN usuarios u ON g.maestro_id=u.id
                         WHERE ga.alumno_id=:a AND g.activo=1
                         ORDER BY ga.fecha_union DESC"
                    );
                    $stmt->execute([':a' => $alumnoId]);
                    echo json_encode(['grupos' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Unirse a grupo por código ─────────────────────────
                case 'unirse_grupo':
                    $codigo = strtoupper(trim($body['codigo'] ?? ''));
                    if (!$codigo) throw new Exception('Código requerido');

                    $stmt = $conn->prepare("SELECT id, nombre FROM grupos WHERE codigo=:c AND activo=1");
                    $stmt->execute([':c' => $codigo]);
                    $grupo = $stmt->fetch();
                    if (!$grupo) throw new Exception('Código incorrecto o grupo inactivo');

                    $chk = $conn->prepare("SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=:gid AND alumno_id=:a");
                    $chk->execute([':gid' => $grupo['id'], ':a' => $alumnoId]);
                    if ($chk->fetchColumn() > 0) throw new Exception('Ya estás en este grupo');

                    $ins = $conn->prepare("INSERT INTO grupo_alumnos (grupo_id, alumno_id) VALUES (:gid, :a)");
                    $ins->execute([':gid' => $grupo['id'], ':a' => $alumnoId]);
                    echo json_encode(['success' => true, 'grupo' => $grupo]);
                    break;

                // ── Salir de un grupo ─────────────────────────────────
                case 'salir_grupo':
                    $grupoId = (int)($body['grupo_id'] ?? 0);
                    if (!$grupoId) throw new Exception('grupo_id requerido');
                    $del = $conn->prepare("DELETE FROM grupo_alumnos WHERE grupo_id=:gid AND alumno_id=:a");
                    $del->execute([':gid' => $grupoId, ':a' => $alumnoId]);
                    echo json_encode(['success' => true]);
                    break;

                // ── Tareas asignadas al alumno ────────────────────────
                case 'mis_tareas':
                    // Tareas por grupo + tareas por proyecto donde el alumno es miembro/asignado
                    $baseSql = "SELECT t.id, t.titulo, t.descripcion, t.tipo_diagrama, t.fecha_entrega,
                                g.nombre AS grupo_nombre,
                                p.nombre AS proyecto_nombre,
                                u.nombre_completo AS maestro_nombre,
                                e.diagrama_id, e.calificacion,
                                e.comentario AS comentario_maestro,
                                e.comentario_alumno, e.fecha_calificacion";
                    $fromSql = " FROM tareas t
                         LEFT JOIN grupos g ON t.grupo_id=g.id
                         LEFT JOIN proyectos p ON t.proyecto_id=p.id
                         JOIN usuarios u ON t.maestro_id=u.id
                         LEFT JOIN entregas e ON e.tarea_id=t.id AND e.alumno_id=:a
                         WHERE t.activa=1
                         AND (
                             -- Tarea por grupo al que pertenece el alumno
                             (t.grupo_id IS NOT NULL AND t.grupo_id IN (
                                 SELECT grupo_id FROM grupo_alumnos WHERE alumno_id=:a2
                             ))
                             OR
                             -- Tarea por proyecto y asignada al alumno o a todo el equipo
                             (t.proyecto_id IS NOT NULL AND (
                                 t.alumno_id = :a3
                                 OR (t.alumno_id IS NULL AND t.proyecto_id IN (
                                     SELECT proyecto_id FROM proyecto_miembros WHERE usuario_id=:a4
                                 ))
                             ))
                         )
                         ORDER BY t.fecha_entrega ASC, t.fecha_creacion DESC";
                    try {
                        $stmt = $conn->prepare($baseSql . $fromSql);
                        $stmt->execute([':a' => $alumnoId, ':a2' => $alumnoId, ':a3' => $alumnoId, ':a4' => $alumnoId]);
                        $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $ex) {
                        // Fallback si columnas nuevas no existen
                        $baseSqlFallback = "SELECT t.id, t.titulo, t.descripcion, t.tipo_diagrama, t.fecha_entrega,
                                g.nombre AS grupo_nombre, NULL AS proyecto_nombre,
                                u.nombre_completo AS maestro_nombre,
                                e.diagrama_id, e.calificacion,
                                e.comentario AS comentario_maestro";
                        $fromSqlFallback = " FROM tareas t
                             JOIN grupos g ON t.grupo_id=g.id
                             JOIN usuarios u ON t.maestro_id=u.id
                             LEFT JOIN entregas e ON e.tarea_id=t.id AND e.alumno_id=:a
                             WHERE t.grupo_id IN (SELECT grupo_id FROM grupo_alumnos WHERE alumno_id=:a2)
                             AND t.activa=1 ORDER BY t.fecha_entrega ASC, t.fecha_creacion DESC";
                        $stmt = $conn->prepare($baseSqlFallback . $fromSqlFallback);
                        $stmt->execute([':a' => $alumnoId, ':a2' => $alumnoId]);
                        $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tareas as &$t) {
                            $t['comentario_alumno']  = null;
                            $t['fecha_calificacion'] = null;
                        }
                    }
                    echo json_encode(['tareas' => $tareas]);
                    break;

                // ── Entregar tarea ───────────────────────────────────
                case 'entregar_tarea':
                    $tareaId          = (int)($body['tarea_id']         ?? 0);
                    $diagramaId       = (int)($body['diagrama_id']       ?? 0) ?: null;
                    $comentarioAlumno = trim($body['comentario_alumno']  ?? '');
                    if (!$tareaId) throw new Exception('Tarea requerida');

                    // Validar que el alumno tiene acceso a esa tarea (por grupo O por proyecto)
                    $chk = $conn->prepare(
                        "SELECT t.id FROM tareas t
                         WHERE t.id=:tid AND t.activa=1
                         AND (
                             -- Por grupo
                             (t.grupo_id IS NOT NULL AND t.grupo_id IN (
                                 SELECT grupo_id FROM grupo_alumnos WHERE alumno_id=:a
                             ))
                             OR
                             -- Por proyecto individual
                             (t.alumno_id = :a2)
                             OR
                             -- Por proyecto para todo el equipo
                             (t.proyecto_id IS NOT NULL AND t.alumno_id IS NULL AND t.proyecto_id IN (
                                 SELECT proyecto_id FROM proyecto_miembros WHERE usuario_id=:a3
                             ))
                         )"
                    );
                    $chk->execute([':tid' => $tareaId, ':a' => $alumnoId, ':a2' => $alumnoId, ':a3' => $alumnoId]);
                    if (!$chk->fetch()) throw new Exception('Tarea no válida o no tienes acceso');

                    // Validar diagrama si se envió
                    if ($diagramaId) {
                        $chkD = $conn->prepare("SELECT id FROM diagramas WHERE id=:did AND usuario_id=:a");
                        $chkD->execute([':did' => $diagramaId, ':a' => $alumnoId]);
                        if (!$chkD->fetch()) throw new Exception('El diagrama no te pertenece');
                    }

                    $stmt = $conn->prepare(
                        "INSERT INTO entregas (tarea_id, alumno_id, diagrama_id, comentario_alumno, fecha_entrega)
                         VALUES (:tid,:a,:did,:com,NOW())
                         ON DUPLICATE KEY UPDATE
                            diagrama_id=:did2, comentario_alumno=:com2, fecha_entrega=NOW(),
                            calificacion=NULL, comentario=NULL, fecha_calificacion=NULL"
                    );
                    $stmt->execute([
                        ':tid' => $tareaId, ':a' => $alumnoId,
                        ':did' => $diagramaId, ':did2' => $diagramaId,
                        ':com' => $comentarioAlumno, ':com2' => $comentarioAlumno
                    ]);
                    echo json_encode(['success' => true]);
                    break;

                // ── Mis diagramas para adjuntar en tarea ─────────────
                // ── Proyectos del grupo (diagramas compartidos de compañeros) ──
                case 'proyectos_grupo':
                    $grupoId = (int)($_GET['grupo_id'] ?? 0);
                    // Verificar que el alumno pertenece al grupo pedido (o traer todos)
                    if ($grupoId) {
                        $chk = $conn->prepare("SELECT 1 FROM grupo_alumnos WHERE grupo_id=:g AND alumno_id=:a");
                        $chk->execute([':g'=>$grupoId,':a'=>$alumnoId]);
                        if (!$chk->fetch()) throw new Exception('No perteneces a ese grupo');
                        $grupos = [['id'=>$grupoId]];
                    } else {
                        $stG = $conn->prepare("SELECT g.id, g.nombre, u.nombre_completo AS maestro FROM grupo_alumnos ga JOIN grupos g ON g.id=ga.grupo_id JOIN usuarios u ON u.id=g.maestro_id WHERE ga.alumno_id=:a AND g.activo=1 ORDER BY g.nombre");
                        $stG->execute([':a'=>$alumnoId]);
                        $grupos = $stG->fetchAll(PDO::FETCH_ASSOC);
                    }
                    $resultado = [];
                    foreach ($grupos as $g) {
                        // Info del grupo
                        $stInfo = $conn->prepare("SELECT g.id,g.nombre,g.descripcion,u.nombre_completo AS maestro FROM grupos g JOIN usuarios u ON u.id=g.maestro_id WHERE g.id=:gid");
                        $stInfo->execute([':gid'=>$g['id']]);
                        $info = $stInfo->fetch(PDO::FETCH_ASSOC);
                        if (!$info) continue;
                        // Miembros y sus diagramas
                        $stM = $conn->prepare("SELECT u.id,u.username,u.nombre_completo,u.rol FROM grupo_alumnos ga JOIN usuarios u ON u.id=ga.alumno_id WHERE ga.grupo_id=:gid ORDER BY u.nombre_completo");
                        $stM->execute([':gid'=>$g['id']]);
                        $miembros = $stM->fetchAll(PDO::FETCH_ASSOC);
                        // Diagramas de cada miembro
                        foreach ($miembros as &$m) {
                            $stD = $conn->prepare("SELECT id,titulo,tipo_diagrama,fecha_modificacion,version FROM diagramas WHERE usuario_id=:uid ORDER BY fecha_modificacion DESC LIMIT 20");
                            $stD->execute([':uid'=>$m['id']]);
                            $m['diagramas'] = $stD->fetchAll(PDO::FETCH_ASSOC);
                        }
                        // También los diagramas del maestro (del grupo)
                        $stG2 = $conn->prepare("SELECT id,titulo,tipo_diagrama,fecha_modificacion FROM diagramas d WHERE usuario_id=:mid ORDER BY fecha_modificacion DESC LIMIT 10");
                        $stG2->execute([':mid'=>$info['maestro']??0]);
                        $resultado[] = ['grupo'=>$info,'miembros'=>$miembros];
                    }
                    echo json_encode(['success'=>true,'proyectos'=>$resultado]);
                    break;

                case 'mis_diagramas_tarea':
                    $tipo = trim($_GET['tipo'] ?? '');
                    $sql  = "SELECT id, titulo, tipo_diagrama, fecha_modificacion FROM diagramas WHERE usuario_id=:a";
                    $params = [':a' => $alumnoId];
                    if ($tipo) { $sql .= " AND tipo_diagrama=:t"; $params[':t'] = $tipo; }
                    $sql .= " ORDER BY fecha_modificacion DESC LIMIT 50";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    echo json_encode(['success' => true, 'diagramas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
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
