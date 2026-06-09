<?php
/**
 * app/controllers/EditorController.php — Controlador del Editor de Diagramas
 *
 * Maneja: vista del editor, guardar diagrama, cargar diagrama.
 * Migrado desde: editor.php, api/save_diagram.php, api/load_diagram.php
 */
class EditorController extends Controller {

    /** GET /editor */
    public function index() {
        SessionManager::verificarAcceso();

        $diagrama_id   = $_GET['id'] ?? null;
        $proyecto_id   = $_GET['proyecto'] ?? null;   // ← nuevo: proyecto de contexto
        $diagrama_data = null;
        $tipo_diagrama = 'usecase';

        if ($diagrama_id) {
            try {
                $model   = new DiagramModel();
                $uid     = SessionManager::usuarioId();
                $rol     = SessionManager::usuarioRol();

                $diagrama_data = $model->obtenerAccesible($diagrama_id, $uid, $proyecto_id);
                if (!$diagrama_data && in_array($rol, ['maestro','admin'])) {
                    $diagrama_data = $model->obtenerCualquiera($diagrama_id);
                }

                if (!$diagrama_data) {
                    $this->redirigir('dashboard');
                }
                $tipo_diagrama = $diagrama_data['tipo_diagrama'];
            } catch (RuntimeException $e) {
                // BD no disponible — abrir editor en blanco
                error_log('EditorController::index BD error: ' . $e->getMessage());
                $diagrama_data = null;
                $tipo_diagrama = $_GET['tipo'] ?? 'usecase';
            }
        } else {
            $tipo_diagrama = $_GET['tipo'] ?? 'usecase';
        }

        try {
            $this->render('editor/index', [
                'diagrama_data' => $diagrama_data,
                'tipo_diagrama' => $tipo_diagrama,
                'diagrama_id'   => $diagrama_id,
                'proyecto_id'   => $proyecto_id,
            ]);
        } catch (Throwable $t) {
            http_response_code(200);
            // Render a minimal fallback editor without pre-loaded data
            $diagrama_data = null;
            error_log("EditorController::index render error: " . $t->getMessage());
            $this->render('editor/index', [
                'diagrama_data' => null,
                'tipo_diagrama' => $tipo_diagrama,
                'diagrama_id'   => $diagrama_id,
                'proyecto_id'   => $proyecto_id,
            ]);
        }
    }

    /** POST /api/diagramas/save */
    public function save() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);
        ob_start();

        $response = ['success' => false, 'error' => 'Error desconocido'];

        try {
            if (!SessionManager::estaLogueado()) {
                throw new Exception('Sesión expirada. Recarga la página e inicia sesión de nuevo.');
            }

            $raw  = file_get_contents('php://input');
            $data = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON inválido: ' . json_last_error_msg());
            }
            if (empty($data)) {
                throw new Exception('No se recibieron datos');
            }

            if (empty($data['titulo'])) $data['titulo'] = 'Diagrama sin título';
            if (empty($data['tipo']))   $data['tipo']   = 'usecase';

            $contenido = $data['contenido'] ?? [];
            if (is_string($contenido)) {
                $contenido = json_decode($contenido, true) ?: [];
            }

            $model      = new DiagramModel();
            $usuario_id = SessionManager::usuarioId();
            $nombreArchivo = $data['nombre_archivo'] ?? null;
            $proyecto_id   = isset($data['proyecto_id']) ? (int)$data['proyecto_id'] : 0;

            if (!empty($data['id'])) {
                $resultado = $model->actualizar(
                    (int)$data['id'], $usuario_id,
                    $data['titulo'], $data['tipo'], $contenido,
                    $data['descripcion'] ?? '', $data['etiquetas'] ?? '',
                    $nombreArchivo
                );
                if ($resultado['success']) {
                    $resultado['id']            = (int)$data['id'];
                    $resultado['nueva_version'] = $resultado['version'] ?? 1;
                }
            } else {
                $resultado = $model->guardar(
                    $usuario_id, $data['titulo'], $data['tipo'], $contenido,
                    $data['descripcion'] ?? '', $data['etiquetas'] ?? '',
                    $nombreArchivo
                );
            }

            // ── Auto-ligar al proyecto si se indicó proyecto_id ──────────
            if ($resultado['success'] && $proyecto_id > 0) {
                $diagrama_id_nuevo = $resultado['id'] ?? (isset($data['id']) ? (int)$data['id'] : 0);
                if ($diagrama_id_nuevo > 0) {
                    try {
                        $db2   = new Database();
                        $conn2 = $db2->getConnection();
                        // Verificar membresía antes de ligar
                        $chk = $conn2->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                        $chk->execute([':p'=>$proyecto_id, ':u'=>$usuario_id]);
                        if ($chk->fetch()) {
                            $conn2->prepare(
                                "INSERT IGNORE INTO proyecto_diagramas (proyecto_id, diagrama_id, agregado_por)
                                 VALUES (:p, :d, :u)"
                            )->execute([':p'=>$proyecto_id, ':d'=>$diagrama_id_nuevo, ':u'=>$usuario_id]);
                            $resultado['proyecto_ligado'] = $proyecto_id;
                        } else {
                            // Si el autor creó el diagrama desde el contexto del proyecto pero no está en miembros,
                            // auto-unir como 'editor' y ligar el diagrama. Esto evita que el diagrama "desaparezca"
                            // del proyecto cuando se creó desde el editor con ?proyecto=ID.
                            try {
                                $conn2->prepare("INSERT INTO proyecto_miembros (proyecto_id,usuario_id,rol) VALUES (:p,:u,'editor')")
                                      ->execute([':p'=>$proyecto_id, ':u'=>$usuario_id]);
                                $conn2->prepare(
                                    "INSERT IGNORE INTO proyecto_diagramas (proyecto_id, diagrama_id, agregado_por)
                                     VALUES (:p, :d, :u)"
                                )->execute([':p'=>$proyecto_id, ':d'=>$diagrama_id_nuevo, ':u'=>$usuario_id]);
                                $resultado['proyecto_ligado'] = $proyecto_id;
                                $resultado['auto_unido_a_proyecto'] = true;
                            } catch (Exception $ex) { /* silencioso */ }
                        }
                    } catch (Exception $ex) { /* silencioso */ }
                }
            }

            $response = $resultado;

        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Unknown column') !== false) {
                $response['error']   = 'La BD no tiene las columnas necesarias. Ejecuta el script de migración en phpMyAdmin.';
                $response['detalle'] = $msg;
            } elseif (strpos($msg, 'Table') !== false && strpos($msg, "doesn't exist") !== false) {
                $response['error']   = 'Tabla no encontrada. Ejecuta diagramas_v2.sql en phpMyAdmin.';
                $response['detalle'] = $msg;
            } else {
                $response['error'] = 'Error de base de datos: ' . $msg;
            }
            error_log('EditorController::save PDOException: ' . $msg);
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            error_log('EditorController::save Exception: ' . $e->getMessage());
        }

        ob_end_clean();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /** GET /api/diagramas/load */
    public function load() {
        header('Content-Type: application/json');

        try {
            SessionManager::verificarAcceso();

            if (!isset($_GET['id'])) {
                throw new Exception('ID requerido');
            }

            $model       = new DiagramModel();
            $usuario_id  = SessionManager::usuarioId();
            $diagrama_id = (int)$_GET['id'];
            $proyecto_id = isset($_GET['proyecto']) ? (int)$_GET['proyecto'] : null;

            $diagrama = $model->obtenerAccesible($diagrama_id, $usuario_id, $proyecto_id);
            // Maestros/admins can load any diagram to view it
            if (!$diagrama && in_array(SessionManager::usuarioRol(), ['maestro','admin'])) {
                $diagrama = $model->obtenerCualquiera($diagrama_id);
            }
            if (!$diagrama) {
                throw new Exception('Diagrama no encontrado o sin permiso');
            }

            echo json_encode(['success' => true, 'diagrama' => $diagrama]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    /** GET /editor-api — Vista del editor para uso via API externa */
    public function apiView() {
        SessionManager::verificarAcceso();
        $diagrama_id   = $_GET['id']      ?? null;
        $tipo_diagrama = $_GET['tipo']    ?? 'usecase';
        $proyecto_id   = $_GET['proyecto']?? null;
        $diagrama_data = null;

        if ($diagrama_id) {
            $model  = new DiagramModel();
            $uid    = SessionManager::usuarioId();
            $rol    = SessionManager::usuarioRol();
            $diagrama_data = $model->obtenerAccesible($diagrama_id, $uid, $proyecto_id);
            if (!$diagrama_data && in_array($rol, ['maestro','admin'])) {
                $diagrama_data = $model->obtenerCualquiera($diagrama_id);
            }
            if ($diagrama_data) $tipo_diagrama = $diagrama_data['tipo_diagrama'];
        }

        $this->render('editor/api_view', [
            'diagrama_data' => $diagrama_data,
            'tipo_diagrama' => $tipo_diagrama,
            'diagrama_id'   => $diagrama_id,
            'proyecto_id'   => $proyecto_id,
        ]);
    }
}
?>