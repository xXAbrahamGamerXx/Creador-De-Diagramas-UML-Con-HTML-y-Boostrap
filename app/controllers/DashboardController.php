<?php
/**
 * app/controllers/DashboardController.php — Controlador del Dashboard (Alumno)
 * V45: verificarAlumno → verificarAcceso para que maestros/admins también puedan
 *      usar las APIs de diagramas sin ser rechazados.
 */
class DashboardController extends Controller {

    /** GET /dashboard */
    public function index() {
        // V45 FIX: usar verificarAcceso en lugar de verificarAlumno
        // para que maestros/admins también puedan acceder al dashboard si llegan aquí.
        SessionManager::verificarAcceso();
        try {
            FileManager::crearCarpetaUsuarioAlta(SessionManager::usuarioId());
        } catch (Exception $e) {
            error_log('DashboardController::index FileManager error: ' . $e->getMessage());
        }
        $this->render('dashboard/index');
    }

    /** GET /api/diagramas */
    public function getDiagramas() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        $response = ['success' => false, 'error' => 'Error desconocido'];

        try {
            if (!SessionManager::estaLogueado()) {
                throw new Exception('No hay sesión activa');
            }

            $usuario_id = SessionManager::usuarioId();
            $model      = new DiagramModel();

            $filtro   = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';
            $pagina   = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $tipo     = isset($_GET['tipo'])   ? trim($_GET['tipo'])   : '';

            $diagramas    = $model->listar($usuario_id, $filtro, $pagina, 12);
            $total        = $model->contar($usuario_id, $filtro);
            $estadisticas = $model->estadisticas($usuario_id);

            // Agregar límite de espacio (con auto-patch de columna)
            try {
                $db2  = new Database(); $conn2 = $db2->getConnection();
                try { $conn2->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS espacio_limite_mb INT NOT NULL DEFAULT 100"); } catch (Exception $ex) {}
                $stLim = $conn2->prepare("SELECT COALESCE(espacio_limite_mb, 100) FROM usuarios WHERE id = :u");
                $stLim->execute([':u' => $usuario_id]);
                $estadisticas['espacio_limite_mb'] = (int)$stLim->fetchColumn();
            } catch (Exception $e) { $estadisticas['espacio_limite_mb'] = 100; }

            // Filtrar por tipo en PHP si se indicó
            if (!empty($tipo)) {
                $diagramas = array_values(array_filter($diagramas, fn($d) => $d['tipo_diagrama'] === $tipo));
            }

            $response = [
                'success'      => true,
                'diagramas'    => $diagramas,
                'total'        => $total,
                'estadisticas' => $estadisticas,
            ];
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }

    /** POST /api/diagramas/delete */
    public function delete() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        $response = ['success' => false, 'error' => 'Error desconocido'];

        try {
            if (!SessionManager::estaLogueado()) {
                throw new Exception('No hay sesión activa');
            }

            $data = $this->getJsonInput();

            if (!isset($data['id'])) {
                throw new Exception('ID requerido');
            }

            $model      = new DiagramModel();
            $usuario_id = SessionManager::usuarioId();
            $response   = $model->eliminar($data['id'], $usuario_id);

        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);
        exit();
    }

    /** POST /api/diagramas/duplicate */
    public function duplicate() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        $response = ['success' => false, 'error' => 'Error desconocido'];

        try {
            if (!SessionManager::estaLogueado()) {
                throw new Exception('No hay sesión activa');
            }

            $data = $this->getJsonInput();

            if (!isset($data['id'])) {
                throw new Exception('ID requerido');
            }

            $model      = new DiagramModel();
            $usuario_id = SessionManager::usuarioId();
            $response   = $model->duplicar((int)$data['id'], $usuario_id);

        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);
        exit();
    }

    /** POST /api/diagramas/rename */
    public function rename() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);
        $response = ['success' => false, 'error' => 'Error desconocido'];
        try {
            if (!SessionManager::estaLogueado()) throw new Exception('No hay sesión activa');
            $data = $this->getJsonInput();
            if (!isset($data['id']) || empty($data['titulo'])) throw new Exception('ID y título requeridos');
            $model      = new DiagramModel();
            $usuario_id = SessionManager::usuarioId();
            $response   = $model->renombrar((int)$data['id'], $usuario_id, $data['titulo']);
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }
        echo json_encode($response);
        exit();
    }

    /** GET /api/diagramas/preview?id=X — Devuelve contenido del diagrama para previsualización */
    public function previewDiagrama() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);
        try {
            if (!SessionManager::estaLogueado()) {
                echo json_encode(['success' => false]); exit();
            }
            $id  = (int)($_GET['id'] ?? 0);
            $uid = (int)SessionManager::usuarioId();
            $rol = SessionManager::usuarioRol();
            if (!$id) { echo json_encode(['success' => false]); exit(); }

            $model = new DiagramModel();
            $diag  = ($rol === 'admin' || $rol === 'maestro')
                ? $model->obtenerCualquiera($id)
                : $model->obtenerAccesible($id, $uid);

            if (!$diag) { echo json_encode(['success' => false]); exit(); }

            $content = $diag['contenido'] ?? [];
            echo json_encode([
                'success' => true,
                'tipo'    => $diag['tipo_diagrama'] ?? 'class',
                'content' => $content
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
        exit();
    }

    /** GET /api/user-config */
    public function getUserConfig() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int) SessionManager::usuarioId();
        try {
            $db   = new Database();
            $conn = $db->getConnection();
            $st   = $conn->prepare("SELECT theme, primary_color, primary2_color, sidebar_color FROM user_config WHERE user_id = :uid");
            $st->execute([':uid' => $uid]);
            $row  = $st->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'config' => $row ?: [
                'theme' => 'light', 'primary_color' => '#667eea', 'primary2_color' => '#764ba2'
            ]]);
        } catch (Exception $e) {
            echo json_encode(['success' => true, 'config' => [
                'theme' => 'light', 'primary_color' => '#667eea', 'primary2_color' => '#764ba2'
            ]]);
        }
        exit();
    }

    /** POST /api/user-config */
    public function saveUserConfig() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int) SessionManager::usuarioId();
        $data = $this->getJsonInput();

        $theme   = in_array($data['theme'] ?? '', ['light', 'dark']) ? $data['theme'] : 'dark';
        $primary  = preg_match('/^#[0-9a-fA-F]{6}$/', $data['primary_color']  ?? '') ? $data['primary_color']  : '#667eea';
        $primary2 = preg_match('/^#[0-9a-fA-F]{6}$/', $data['primary2_color'] ?? '') ? $data['primary2_color'] : '#764ba2';

        try {
            $db   = new Database();
            $conn = $db->getConnection();
            try { $conn->exec("ALTER TABLE user_config ADD COLUMN IF NOT EXISTS sidebar_color VARCHAR(7) NULL"); } catch(Exception $ea) {}
            $sidebar = preg_match('/^#[0-9a-fA-F]{6}$/', $data['sidebar_color'] ?? '') ? $data['sidebar_color'] : null;
            $conn->prepare(
                "INSERT INTO user_config (user_id, theme, primary_color, primary2_color, sidebar_color)
                 VALUES (:uid, :t, :p1, :p2, :sb)
                 ON DUPLICATE KEY UPDATE theme=:t, primary_color=:p1, primary2_color=:p2, sidebar_color=:sb"
            )->execute([':uid' => $uid, ':t' => $theme, ':p1' => $primary, ':p2' => $primary2, ':sb' => $sidebar]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    /** GET /api/plantillas-sistema */
    public function getPlantillasSistema() {
        header('Content-Type: application/json');
        if (!SessionManager::estaLogueado()) { echo json_encode(['success'=>false,'plantillas'=>[]]); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $st = $conn->prepare(
                "SELECT d.id, d.titulo, d.descripcion, d.tipo_diagrama, d.contenido_json,
                        u.username, u.nombre_completo
                 FROM diagramas d LEFT JOIN usuarios u ON d.usuario_id=u.id
                 WHERE d.compartido=1 ORDER BY d.tipo_diagrama, d.titulo"
            );
            $st->execute();
            echo json_encode(['success'=>true,'plantillas'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) { echo json_encode(['success'=>false,'plantillas'=>[],'error'=>$e->getMessage()]); }
        exit();
    }

    /** GET /api/busqueda?q=... */
    public function busquedaGlobal(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        $q   = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) { echo json_encode(['resultados'=>[]]); exit(); }
        $like = '%' . $q . '%';
        try {
            $db = new Database(); $conn = $db->getConnection();
            $resultados = [];

            $st = $conn->prepare(
                "SELECT 'diagrama' AS tipo, d.id, d.titulo AS nombre, d.tipo_diagrama AS subtipo, d.fecha_modificacion AS fecha
                 FROM diagramas d
                 WHERE d.usuario_id=:u AND (d.titulo LIKE :q OR d.descripcion LIKE :q2)
                 ORDER BY d.fecha_modificacion DESC LIMIT 10"
            );
            $st->execute([':u'=>$uid,':q'=>$like,':q2'=>$like]);
            $resultados = array_merge($resultados, $st->fetchAll(PDO::FETCH_ASSOC));

            $st = $conn->prepare(
                "SELECT 'proyecto' AS tipo, p.id, p.nombre AS nombre, NULL AS subtipo, p.fecha_creacion AS fecha
                 FROM proyectos p
                 JOIN proyecto_miembros pm ON pm.proyecto_id=p.id AND pm.usuario_id=:u
                 WHERE p.nombre LIKE :q OR p.descripcion LIKE :q2
                 ORDER BY p.fecha_creacion DESC LIMIT 10"
            );
            $st->execute([':u'=>$uid,':q'=>$like,':q2'=>$like]);
            $resultados = array_merge($resultados, $st->fetchAll(PDO::FETCH_ASSOC));

            $st = $conn->prepare(
                "SELECT 'archivo' AS tipo, pa.id, pa.nombre_original AS nombre, pa.extension AS subtipo, pa.fecha_subida AS fecha
                 FROM proyecto_archivos pa
                 JOIN proyecto_miembros pm ON pm.proyecto_id=pa.proyecto_id AND pm.usuario_id=:u
                 WHERE pa.nombre_original LIKE :q
                 ORDER BY pa.fecha_subida DESC LIMIT 10"
            );
            $st->execute([':u'=>$uid,':q'=>$like]);
            $resultados = array_merge($resultados, $st->fetchAll(PDO::FETCH_ASSOC));

            $st = $conn->prepare(
                "SELECT 'observacion' AS tipo, o.id, o.texto AS nombre, NULL AS subtipo, o.fecha_creacion AS fecha
                 FROM proyecto_observaciones o
                 WHERE o.autor_id=:u AND o.texto LIKE :q
                 ORDER BY o.fecha_creacion DESC LIMIT 5"
            );
            $st->execute([':u'=>$uid,':q'=>$like]);
            $resultados = array_merge($resultados, $st->fetchAll(PDO::FETCH_ASSOC));

            echo json_encode(['resultados'=>$resultados,'query'=>$q]);
        } catch (Exception $e) {
            echo json_encode(['resultados'=>[],'error'=>$e->getMessage()]);
        }
        exit();
    }
}
?>
