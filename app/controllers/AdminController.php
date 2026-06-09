<?php
/**
 * app/controllers/AdminController.php — Controlador del Panel de Administración
 *
 * Maneja: vista del panel admin y toda la API de administración.
 * Migrado desde: admin.php y api/admin_api.php
 */
class AdminController extends Controller {

    /** GET /admin */
    public function index() {
        SessionManager::verificarAdmin();

        $dbOK    = false;
        $dbError = '';
        $conn    = null;
        try {
            $db   = new Database();
            $conn = $db->getConnection();
            $dbOK = true;
        } catch (Exception $e) {
            $dbError = $e->getMessage();
        }

        // Auto-patch: agregar columna espacio_limite_mb si no existe
        if ($conn) {
            try {
                $conn->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS espacio_limite_mb INT NOT NULL DEFAULT 100 COMMENT 'Límite de espacio en MB (0 = ilimitado)'");
            } catch (Exception $e) { /* columna ya existe */ }
        }

        $this->render('admin/index', [
            'dbOK'    => $dbOK,
            'dbError' => $dbError,
            'conn'    => $conn,
        ]);
    }

    /**
     * GET+POST /api/admin — Despacha todas las acciones del panel admin.
     * Parámetro: ?action=nombre_accion
     */
    public function api() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        if (!SessionManager::estaLogueado()) {
            echo json_encode(['success' => false, 'error' => 'No autenticado']); exit();
        }

        // Verificar rol admin directamente desde BD
        $roleCheck = null;
        try {
            $dbRole = new Database();
            $cRole  = $dbRole->getConnection();
            $sRole  = $cRole->prepare("SELECT rol FROM usuarios WHERE id=:id");
            $sRole->bindParam(':id', $_SESSION['user_id']);
            $sRole->execute();
            $roleCheck = $sRole->fetchColumn();
        } catch (Exception $e) { /* si BD no conecta, dejamos pasar para configurar BD */ }

        if ($roleCheck !== null && $roleCheck !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Se requiere rol admin']); exit();
        }

        $action = $_GET['action'] ?? '';
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];

        $conn = null;
        try {
            $db   = new Database();
            $conn = $db->getConnection();
        } catch (Exception $e) {
            if (!in_array($action, ['test_conexion', 'guardar_config_db', 'check_svgs', 'generar_svgs'])) {
                echo json_encode(['success' => false, 'error' => 'BD no conectada: ' . $e->getMessage()]); exit();
            }
        }

        try {
            switch ($action) {

                // ── Documentación ────────────────────────────────────
                case 'generar_documentacion':
                    $formato = $_GET['formato'] ?? 'pdf';
                    $baseDir = ROOT_PATH . '/basededatos+info/';
                    if ($formato === 'word') {
                        $file = $baseDir . 'Documentacion_Sistema.docx';
                        $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                        $name = 'Documentacion_DiagramasUML.docx';
                    } else {
                        $file = $baseDir . 'Documentacion_Sistema.pdf';
                        $mime = 'application/pdf';
                        $name = 'Documentacion_DiagramasUML.pdf';
                    }
                    if (!file_exists($file)) {
                        echo json_encode(['success' => false, 'error' => "Archivo no encontrado: $name"]);
                        break;
                    }
                    header('Content-Type: ' . $mime);
                    header('Content-Disposition: attachment; filename="' . $name . '"');
                    header('Content-Length: ' . filesize($file));
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    ob_clean(); flush();
                    readfile($file);
                    exit();

                // ── Grupos (admin) ────────────────────────────────────
                case 'grupos_admin':
                    $totalG = $conn->query("SELECT COUNT(*) FROM grupos WHERE activo=1")->fetchColumn();
                    $totalI = $conn->query("SELECT COUNT(*) FROM grupo_alumnos")->fetchColumn();
                    $totalT = $conn->query("SELECT COUNT(*) FROM tareas WHERE activa=1")->fetchColumn();
                    $grupos = $conn->query(
                        "SELECT g.*,u.nombre_completo AS maestro_nombre,
                                (SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=g.id) AS num_alumnos,
                                (SELECT COUNT(*) FROM tareas WHERE grupo_id=g.id) AS num_tareas
                         FROM grupos g JOIN usuarios u ON g.maestro_id=u.id ORDER BY g.fecha_creacion DESC"
                    )->fetchAll(PDO::FETCH_ASSOC);
                    $tareas = $conn->query(
                        "SELECT t.titulo,t.tipo_diagrama,t.fecha_entrega,
                                g.nombre AS grupo_nombre,u.nombre_completo AS maestro_nombre,
                                (SELECT COUNT(*) FROM entregas WHERE tarea_id=t.id) AS num_entregas,
                                (SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=t.grupo_id) AS total_alumnos
                         FROM tareas t
                         JOIN grupos g ON t.grupo_id=g.id
                         JOIN usuarios u ON t.maestro_id=u.id
                         ORDER BY t.fecha_creacion DESC LIMIT 50"
                    )->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode([
                        'total_grupos'          => (int)$totalG,
                        'total_inscripciones'   => (int)$totalI,
                        'total_tareas'          => (int)$totalT,
                        'grupos'                => $grupos,
                        'tareas'                => $tareas,
                    ]);
                    break;

                // ── Estadísticas ─────────────────────────────────────
                case 'stats_usuarios':
                    $total   = $conn->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
                    $activos = $conn->query("SELECT COUNT(*) FROM usuarios WHERE activo=1")->fetchColumn();
                    $admins  = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='admin'")->fetchColumn();
                    echo json_encode(['total' => (int)$total, 'activos' => (int)$activos, 'admins' => (int)$admins]);
                    break;

                case 'stats_diagramas':
                    $total    = $conn->query("SELECT COUNT(*) FROM diagramas")->fetchColumn();
                    $espacio  = $conn->query("SELECT COALESCE(SUM(archivo_tamano),0) FROM diagramas")->fetchColumn();
                    $porTipo  = $conn->query("SELECT tipo_diagrama, COUNT(*) as count FROM diagramas GROUP BY tipo_diagrama ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);
                    $recientes = $conn->query(
                        "SELECT d.id,d.titulo,d.tipo_diagrama,d.version,d.fecha_modificacion,u.username
                         FROM diagramas d JOIN usuarios u ON d.usuario_id=u.id
                         ORDER BY d.fecha_modificacion DESC LIMIT 10"
                    )->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['total' => (int)$total, 'espacio' => (int)$espacio, 'por_tipo' => $porTipo, 'recientes' => $recientes]);
                    break;

                // ── Proyectos (admin) ─────────────────────────────────
                case 'proyectos':
                    $rows = $conn->query(
                        "SELECT p.id, p.nombre, p.descripcion, p.codigo, p.fecha_creacion,
                                u.username AS owner_username, u.nombre_completo AS owner_nombre,
                                (SELECT COUNT(*) FROM proyecto_miembros WHERE proyecto_id=p.id) AS num_miembros,
                                (SELECT COUNT(*) FROM proyecto_diagramas WHERE proyecto_id=p.id) AS num_diagramas
                         FROM proyectos p
                         JOIN proyecto_miembros pm ON pm.proyecto_id=p.id AND pm.rol='owner'
                         JOIN usuarios u ON u.id=pm.usuario_id
                         ORDER BY p.fecha_creacion DESC"
                    )->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['proyectos' => $rows]);
                    break;

                case 'eliminar_proyecto':
                    $pid = (int)($body['proyecto_id'] ?? 0);
                    if (!$pid) throw new Exception('proyecto_id requerido');
                    $conn->prepare("DELETE FROM proyectos WHERE id=:p")->execute([':p' => $pid]);
                    echo json_encode(['success' => true]);
                    break;

                case 'guardar_svg':
                    $ruta      = trim($body['ruta'] ?? '');
                    $contenido = $body['contenido'] ?? '';
                    if (!$ruta || strpos($ruta,'..') !== false) throw new Exception('Ruta inválida');
                    $fullPath = BASE_PATH . '/' . ltrim($ruta, '/');
                    if (!file_put_contents($fullPath, $contenido)) throw new Exception('No se pudo escribir el archivo');
                    echo json_encode(['success' => true]);
                    break;

                // ── Usuarios ─────────────────────────────────────────
                case 'usuarios':
                    $rows = $conn->query(
                        "SELECT u.id, u.username, u.email, u.nombre_completo, u.rol,
                                COALESCE(u.es_admin_junior, 0) AS es_admin_junior,
                                u.activo, u.fecha_registro, u.ultimo_acceso,
                                c.nombre_completo AS creador_nombre,
                                (SELECT COUNT(*) FROM diagramas WHERE usuario_id=u.id) AS num_diagramas
                         FROM usuarios u
                         LEFT JOIN usuarios c ON c.id = u.creado_por
                         ORDER BY u.fecha_registro DESC"
                    )->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as &$r) unset($r['password']);
                    echo json_encode($rows);
                    break;

                case 'crear_usuario':
                    $rolSolicitado = $body['rol'] ?? 'alumno';
                    if (!in_array($rolSolicitado, ['alumno', 'maestro', 'admin'])) throw new Exception('Rol inválido');
                    $username = trim($body['username'] ?? '');
                    $email    = trim($body['email']    ?? '');
                    $nombre   = trim($body['nombre']   ?? $username);
                    $password = $body['password'] ?? '';
                    $activo   = (int)($body['activo'] ?? 1);
                    $esJunior = (int)($body['es_admin_junior'] ?? 0);
                    if (!$username || !$email || strlen($password) < 6) throw new Exception('Datos insuficientes');
                    $chk = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE username=:u OR email=:e");
                    $chk->execute([':u' => $username, ':e' => $email]);
                    if ($chk->fetchColumn() > 0) throw new Exception('El usuario o email ya existe');
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare(
                        "INSERT INTO usuarios (username, email, password, nombre_completo, rol, activo, es_admin_junior, creado_por)
                         VALUES (:u, :e, :p, :n, :r, :a, :j, :cp)"
                    );
                    $stmt->execute([':u' => $username, ':e' => $email, ':p' => $hash, ':n' => $nombre,
                                    ':r' => $rolSolicitado, ':a' => $activo, ':j' => $esJunior, ':cp' => $_SESSION['user_id']]);
                    $nuevoId = $conn->lastInsertId();
                    FileManager::crearCarpetaUsuarioAlta($nuevoId);
                    echo json_encode(['success' => true, 'id' => $nuevoId]);
                    break;

                case 'editar_usuario':
                    $id = (int)($body['id'] ?? 0);
                    if (!$id) throw new Exception('ID requerido');
                    if ($id == $_SESSION['user_id'] && ($body['rol'] ?? '') !== 'admin') {
                        throw new Exception('No puedes cambiar tu propio rol');
                    }
                    $fields = [];
                    $params = [':id' => $id];
                    if (!empty($body['nombre']))   { $fields[] = 'nombre_completo=:nombre'; $params[':nombre'] = trim($body['nombre']); }
                    if (!empty($body['email']))     { $fields[] = 'email=:email';            $params[':email']  = trim($body['email']); }
                    if (!empty($body['username']))  { $fields[] = 'username=:username';      $params[':username'] = trim($body['username']); }
                    if (!empty($body['password']) && strlen($body['password']) >= 6) {
                        $fields[] = 'password=:password';
                        $params[':password'] = password_hash($body['password'], PASSWORD_DEFAULT);
                    }
                    if (isset($body['rol']) && in_array($body['rol'], ['alumno', 'maestro', 'admin'])) {
                        $fields[] = 'rol=:rol';
                        $params[':rol'] = $body['rol'];
                    }
                    if (isset($body['activo']))         { $fields[] = 'activo=:activo';        $params[':activo'] = (int)$body['activo']; }
                    if (isset($body['es_admin_junior'])) { $fields[] = 'es_admin_junior=:junior'; $params[':junior'] = (int)$body['es_admin_junior']; }
                    if (empty($fields)) throw new Exception('Nada que actualizar');
                    $stmt = $conn->prepare("UPDATE usuarios SET " . implode(',', $fields) . " WHERE id=:id");
                    $stmt->execute($params);
                    echo json_encode(['success' => true]);
                    break;

                case 'get_permisos':
                    $adminId = (int)($_GET['admin_id'] ?? 0);
                    if (!$adminId) throw new Exception('admin_id requerido');
                    try {
                        $stmt    = $conn->prepare("SELECT permiso FROM admin_permisos WHERE admin_id=:id");
                        $stmt->execute([':id' => $adminId]);
                        $permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    } catch (Exception $e) {
                        $permisos = [];
                    }
                    echo json_encode(['success' => true, 'permisos' => $permisos]);
                    break;

                case 'set_permisos':
                    $adminId  = (int)($body['admin_id'] ?? 0);
                    $permisos = $body['permisos'] ?? [];
                    if (!$adminId) throw new Exception('admin_id requerido');
                    $permitidos = ['ver_usuarios','crear_alumnos','crear_maestros','editar_usuarios',
                                   'desactivar_usuarios','ver_diagramas','eliminar_diagramas',
                                   'ver_grupos','setup_db','ver_svgs'];
                    $permisos = array_filter($permisos, fn($p) => in_array($p, $permitidos));
                    $conn->prepare("DELETE FROM admin_permisos WHERE admin_id=:id")->execute([':id' => $adminId]);
                    if (!empty($permisos)) {
                        $stmt = $conn->prepare("INSERT INTO admin_permisos (admin_id, permiso, otorgado_por) VALUES (:aid,:p,:op)");
                        foreach ($permisos as $p) {
                            $stmt->execute([':aid' => $adminId, ':p' => $p, ':op' => $_SESSION['user_id']]);
                        }
                    }
                    $esJunior = count($permisos) > 0 ? 1 : 0;
                    $conn->prepare("UPDATE usuarios SET es_admin_junior=:j WHERE id=:id")->execute([':j' => $esJunior, ':id' => $adminId]);
                    echo json_encode(['success' => true]);
                    break;

                case 'set_rol':
                    $id  = (int)($body['id']  ?? 0);
                    $rol = $body['rol'] ?? '';
                    if (!$id || !in_array($rol, ['admin', 'maestro', 'alumno'])) throw new Exception('Datos inválidos');
                    if ($id == $_SESSION['user_id']) throw new Exception('No puedes cambiar tu propio rol');
                    $st = $conn->prepare("UPDATE usuarios SET rol=:rol WHERE id=:id");
                    $st->execute([':rol' => $rol, ':id' => $id]);
                    echo json_encode(['success' => true]);
                    break;

                case 'set_activo':
                    $id     = (int)($body['id']     ?? 0);
                    $activo = (int)($body['activo'] ?? 0);
                    if (!$id) throw new Exception('ID inválido');
                    if ($id == $_SESSION['user_id']) throw new Exception('No puedes desactivarte a ti mismo');
                    $st = $conn->prepare("UPDATE usuarios SET activo=:a WHERE id=:id");
                    $st->execute([':a' => $activo, ':id' => $id]);
                    echo json_encode(['success' => true]);
                    break;

                // ── Diagramas ─────────────────────────────────────────
                case 'diagramas':
                    $filtro = '%' . trim($_GET['filtro'] ?? '') . '%';
                    $stmt   = $conn->prepare(
                        "SELECT d.id,d.titulo,d.tipo_diagrama,d.version,d.archivo_ruta,d.archivo_tamano,
                                d.fecha_modificacion,u.username,u.nombre_completo
                         FROM diagramas d JOIN usuarios u ON d.usuario_id=u.id
                         WHERE d.titulo LIKE :f1 OR u.username LIKE :f2 OR u.nombre_completo LIKE :f3
                         ORDER BY d.fecha_modificacion DESC"
                    );
                    $stmt->execute([':f1' => $filtro, ':f2' => $filtro, ':f3' => $filtro]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $base = PUBLIC_PATH . DIRECTORY_SEPARATOR;
                    foreach ($rows as &$r) {
                        $r['archivo_existe'] = !empty($r['archivo_ruta']) &&
                            file_exists($base . str_replace('/', DIRECTORY_SEPARATOR, $r['archivo_ruta']));
                    }
                    $total = $conn->query("SELECT COUNT(*) FROM diagramas")->fetchColumn();
                    echo json_encode(['total' => (int)$total, 'diagramas' => $rows]);
                    break;

                case 'eliminar_usuario':
                    // Solo superadmin puede eliminar — los juniors no
                    $targetId = (int)($body['id'] ?? 0);
                    if (!$targetId) throw new Exception('ID requerido');
                    // Verificar que no es el superadmin principal
                    $super = $conn->query("SELECT id FROM usuarios WHERE rol='admin' AND (es_admin_junior=0 OR es_admin_junior IS NULL) ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                    if ($super && $targetId == (int)$super['id']) throw new Exception('No se puede eliminar al administrador principal');
                    // Verificar que el usuario que pide no es junior
                    $caller = $conn->prepare("SELECT es_admin_junior FROM usuarios WHERE id=:id");
                    $caller->execute([':id'=>(int)SessionManager::usuarioId()]);
                    $callerRow = $caller->fetch(PDO::FETCH_ASSOC);
                    if ($callerRow && $callerRow['es_admin_junior']) throw new Exception('Los admins junior no pueden eliminar usuarios');
                    // Eliminar archivos del usuario
                    $userDir = PUBLIC_PATH . '/uploads/usuario_' . $targetId;
                    if (is_dir($userDir)) {
                        $files = glob($userDir . '/*') ?: [];
                        foreach ($files as $file) { if (is_file($file)) unlink($file); }
                        rmdir($userDir);
                    }
                    // Eliminar de BD (cascade borra diagramas, grupos etc.)
                    $conn->prepare("DELETE FROM usuarios WHERE id=:id")->execute([':id'=>$targetId]);
                    echo json_encode(['success' => true]);
                    break;

                case 'eliminar_diagrama':

                    $id = (int)($body['id'] ?? 0);
                    if (!$id) throw new Exception('ID inválido');
                    $st = $conn->prepare("SELECT archivo_ruta,usuario_id FROM diagramas WHERE id=:id");
                    $st->execute([':id' => $id]);
                    $row = $st->fetch();
                    $conn->prepare("DELETE FROM diagramas WHERE id=:id")->execute([':id' => $id]);
                    if ($row && $row['archivo_ruta']) {
                        $fm = new FileManager($row['usuario_id']);
                        $fm->eliminarDiagrama($row['archivo_ruta']);
                    }
                    echo json_encode(['success' => true]);
                    break;

                // ── BD ────────────────────────────────────────────────
                case 'test_conexion':
                    try {
                        $dbT  = new Database();
                        $cT   = $dbT->getConnection();
                        $ver  = $cT->query("SELECT VERSION()")->fetchColumn();
                        $info = $dbT->getInfo();
                        echo json_encode(['success' => true, 'info' => "MySQL $ver · {$info['db']}@{$info['host']}"]);
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    }
                    break;

                case 'guardar_config_db':
                    $host = trim($body['host'] ?? 'localhost');
                    $db2  = trim($body['db']   ?? 'diagramas_db');
                    $user = trim($body['user'] ?? 'root');
                    $pass = $body['pass'] ?? '';
                    try {
                        new PDO("mysql:host=$host;dbname=$db2;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'error' => 'Conexión fallida: ' . $e->getMessage()]); break;
                    }
                    $passEsc  = addslashes($pass);
                    $content  = "<?php\n/**\n * app/core/Database.php — Generado por Panel de Administración\n */\nclass Database {\n";
                    $content .= "    private \$host     = '$host';\n";
                    $content .= "    private \$db_name  = '$db2';\n";
                    $content .= "    private \$username = '$user';\n";
                    $content .= "    private \$password = '$passEsc';\n";
                    $content .= "    public  \$conn;\n\n";
                    $content .= "    public function getConnection() {\n";
                    $content .= "        \$this->conn = null;\n";
                    $content .= "        \$dsn = \"mysql:host={\$this->host};dbname={\$this->db_name};charset=utf8mb4\";\n";
                    $content .= "        \$this->conn = new PDO(\$dsn, \$this->username, \$this->password, [\n";
                    $content .= "            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,\n";
                    $content .= "            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
                    $content .= "            PDO::ATTR_EMULATE_PREPARES   => false,\n";
                    $content .= "        ]);\n        return \$this->conn;\n    }\n\n";
                    $content .= "    public function getInfo() {\n";
                    $content .= "        return ['host'=>\$this->host,'db'=>\$this->db_name,'user'=>\$this->username];\n    }\n}\n?>\n";
                    // En MVC la config está en app/core/Database.php
                    if (file_put_contents(APP_PATH . '/core/Database.php', $content) === false) {
                        echo json_encode(['success' => false, 'error' => 'No se pudo escribir app/core/Database.php']); break;
                    }
                    echo json_encode(['success' => true]);
                    break;

                case 'tablas':
                    $tablas = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    $result = [];
                    foreach ($tablas as $t) {
                        $filas    = $conn->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
                        $result[] = ['nombre' => $t, 'filas' => (int)$filas];
                    }
                    echo json_encode(['tablas' => $result]);
                    break;

                case 'setup_db':
                    $log = [];
                    $sql = file_get_contents(ROOT_PATH . '/basededatos+info/Base/diagramas_MASTER_v33.sql');
                    if (!$sql) $sql = $this->getSchemaSQL();
                    $stmts = array_filter(array_map('trim', explode(';', $sql)));
                    $ok = 0; $err = 0;
                    foreach ($stmts as $s) {
                        if (empty($s) || strpos($s, '--') === 0) continue;
                        try {
                            $conn->exec($s);
                            if (stripos($s, 'CREATE TABLE') !== false) {
                                preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $s, $m);
                                $log[] = '✓ Tabla ' . ($m[1] ?? '') . ' verificada/creada';
                                $ok++;
                            }
                        } catch (Exception $e) {
                            if (stripos($e->getMessage(), 'already exists') !== false || stripos($e->getMessage(), 'Duplicate') !== false) {
                                $log[] = '⚠ ' . substr($s, 0, 60) . '… (ya existe, omitido)';
                            } else {
                                $log[] = '✗ Error: ' . $e->getMessage();
                                $err++;
                            }
                        }
                    }
                    $log[] = "─────────────────────────────";
                    $log[] = "✓ Completado: $ok tablas · $err errores";
                    echo json_encode(['success' => $err === 0, 'log' => $log, 'errores' => $err]);
                    break;

                // ── Carpetas ──────────────────────────────────────────
                case 'crear_carpetas':
                    $r = FileManager::inicializarTodasLasCarpetas($conn);
                    if (isset($r['error'])) echo json_encode(['success' => false, 'error' => $r['error']]);
                    else echo json_encode(['success' => true, 'total' => $r['total'], 'creadas' => $r['creadas']]);
                    break;

                case 'proyectos_info':
                    // Lista de proyectos con diagramas y archivos para mantenimiento
                    $proyList = $conn->query(
                        "SELECT p.id, p.nombre, p.codigo,
                                u.nombre_completo AS creador,
                                (SELECT COUNT(*) FROM proyecto_miembros WHERE proyecto_id=p.id) AS num_miembros,
                                (SELECT COUNT(*) FROM proyecto_diagramas WHERE proyecto_id=p.id) AS num_diagramas,
                                (SELECT COUNT(*) FROM proyecto_archivos   WHERE proyecto_id=p.id) AS num_archivos
                         FROM proyectos p JOIN usuarios u ON u.id=p.creador_id
                         WHERE p.activo=1 ORDER BY p.fecha_creacion DESC"
                    )->fetchAll(PDO::FETCH_ASSOC);

                    $totalDiag  = 0; $totalArch = 0;
                    foreach ($proyList as &$pr) {
                        // Diagramas del proyecto
                        $stD = $conn->prepare(
                            "SELECT d.id, d.titulo, u.nombre_completo AS autor
                             FROM proyecto_diagramas pd
                             JOIN diagramas d ON d.id=pd.diagrama_id
                             JOIN usuarios u ON u.id=d.usuario_id
                             WHERE pd.proyecto_id=:pid ORDER BY pd.fecha_agregado DESC LIMIT 10"
                        );
                        $stD->execute([':pid'=>$pr['id']]);
                        $pr['diagramas'] = $stD->fetchAll(PDO::FETCH_ASSOC);

                        // Archivos del proyecto
                        $stA = $conn->prepare(
                            "SELECT pa.id, pa.nombre_original, pa.tamano, u.nombre_completo AS autor
                             FROM proyecto_archivos pa JOIN usuarios u ON u.id=pa.subido_por
                             WHERE pa.proyecto_id=:pid ORDER BY pa.fecha_subida DESC LIMIT 20"
                        );
                        $stA->execute([':pid'=>$pr['id']]);
                        $pr['archivos'] = $stA->fetchAll(PDO::FETCH_ASSOC);

                        $totalDiag += $pr['num_diagramas'];
                        $totalArch += $pr['num_archivos'];
                    }
                    echo json_encode(['success'=>true,'proyectos'=>$proyList,'total_diagramas'=>$totalDiag,'total_archivos'=>$totalArch]);
                    break;

                case 'ver_log_emergencia':
                    $logFile = ROOT_PATH . '/data/emergency_log.txt';
                    $log = file_exists($logFile) ? file_get_contents($logFile) : '(sin entradas de acceso de emergencia)';
                    echo json_encode(['success' => true, 'log' => $log]);
                    break;

                case 'limpiar_huerfanos':
                    $r = FileManager::limpiarReferenciasHuerfanas($conn);
                    if (isset($r['error'])) echo json_encode(['success' => false, 'error' => $r['error']]);
                    else echo json_encode(['success' => true, 'revisados' => $r['revisados'], 'limpiados' => $r['limpiados']]);
                    break;

                // ── Mantenimiento ─────────────────────────────────────
                case 'optimizar_bd':
                    // OPTIMIZE TABLE en todas las tablas del sistema
                    $tablas = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    $resultados = [];
                    foreach ($tablas as $t) {
                        try {
                            $conn->exec("OPTIMIZE TABLE `$t`");
                            $resultados[] = ['tabla' => $t, 'ok' => true];
                        } catch (Exception $ex) {
                            $resultados[] = ['tabla' => $t, 'ok' => false, 'error' => $ex->getMessage()];
                        }
                    }
                    echo json_encode(['success' => true, 'tablas' => $resultados, 'total' => count($tablas)]);
                    break;

                case 'vaciar_carpetas_huerfanas':
                    // Eliminar carpetas uploads/usuario_N que no tienen usuario en BD
                    $uploadsDir = PUBLIC_PATH . '/uploads';
                    $carpetas   = glob($uploadsDir . '/usuario_*', GLOB_ONLYDIR) ?: [];
                    $uids       = $conn->query("SELECT id FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);
                    $eliminadas = [];
                    foreach ($carpetas as $cp) {
                        preg_match('/usuario_(\d+)$/', $cp, $m);
                        $uid = $m[1] ?? null;
                        if ($uid && !in_array($uid, $uids)) {
                            $files = glob($cp . '/*');
                            foreach ($files as $f) @unlink($f);
                            @rmdir($cp);
                            $eliminadas[] = 'usuario_' . $uid;
                        }
                    }
                    echo json_encode(['success' => true, 'eliminadas' => $eliminadas, 'total' => count($eliminadas)]);
                    break;

                case 'disk_usage':
                    // Uso de disco por carpeta uploads
                    $uploadsDir = PUBLIC_PATH . '/uploads';
                    $total = 0;
                    $detalle = [];
                    $carpetas = glob($uploadsDir . '/usuario_*', GLOB_ONLYDIR) ?: [];
                    foreach ($carpetas as $cp) {
                        $files = glob($cp . '/*.json') ?: [];
                        $size  = array_sum(array_map('filesize', $files));
                        $total += $size;
                        preg_match('/usuario_(\d+)$/', $cp, $m);
                        $detalle[] = ['carpeta' => basename($cp), 'uid' => $m[1]??'?', 'archivos' => count($files), 'bytes' => $size];
                    }
                    // Espacio libre en disco
                    $libre = @disk_free_space(PUBLIC_PATH) ?: 0;
                    $total_disco = @disk_total_space(PUBLIC_PATH) ?: 0;
                    echo json_encode(['success'=>true,'bytes_uploads'=>$total,'bytes_libres'=>$libre,'bytes_disco'=>$total_disco,'detalle'=>$detalle]);
                    break;

                case 'mantenimiento_info':

                    $uploadsDir = PUBLIC_PATH . '/uploads';
                    $carpetas   = glob($uploadsDir . '/usuario_*', GLOB_ONLYDIR) ?: [];
                    $estructura = [];
                    $totalJSON  = 0;
                    // Obtener info de usuarios de BD
                    $usersMap = [];
                    try {
                        $uRows = $conn->query("SELECT id, username, nombre_completo, rol FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($uRows as $ur) $usersMap[$ur['id']] = $ur;
                    } catch(Exception $e) {}
                    foreach ($carpetas as $cv) {
                        $files = glob($cv . '/*.json') ?: [];
                        $size  = array_sum(array_map('filesize', $files));
                        preg_match('/usuario_(\d+)$/', $cv, $m);
                        $uid = $m[1] ?? '?';
                        $u   = $usersMap[$uid] ?? null;
                        $estructura[] = [
                            'id'       => $uid,
                            'archivos' => count($files),
                            'tamano'   => $size,
                            'username' => $u['username']       ?? null,
                            'nombre'   => $u['nombre_completo'] ?? null,
                            'rol'      => $u['rol']            ?? null,
                        ];
                        $totalJSON += count($files);
                    }
                    $stH2     = $conn->query("SELECT archivo_ruta FROM diagramas WHERE archivo_ruta IS NOT NULL");
                    $huerfanos = 0;
                    foreach ($stH2->fetchAll(PDO::FETCH_COLUMN) as $ruta) {
                        if (!file_exists(PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta))) $huerfanos++;
                    }
                    echo json_encode(['carpetas_usuario' => count($carpetas), 'archivos_json' => $totalJSON, 'huerfanos' => $huerfanos, 'estructura' => $estructura]);
                    break;

                case 'archivos_usuario':
                    // Lista archivos JSON de un usuario con metadata de BD
                    $uid = (int)($_GET['uid'] ?? 0);
                    if (!$uid) throw new Exception('uid requerido');
                    $dir   = PUBLIC_PATH . '/uploads/usuario_' . $uid;
                    $files = glob($dir . '/*.json') ?: [];
                    $resultado = [];
                    // Obtener títulos/tipos de BD
                    $stDiag = $conn->prepare("SELECT archivo_ruta, titulo, tipo_diagrama, version, fecha_modificacion FROM diagramas WHERE usuario_id=:uid");
                    $stDiag->execute([':uid' => $uid]);
                    $diagMap = [];
                    foreach ($stDiag->fetchAll(PDO::FETCH_ASSOC) as $d) {
                        $base = basename($d['archivo_ruta'] ?? '');
                        if ($base) $diagMap[$base] = $d;
                    }
                    foreach ($files as $fp) {
                        $nombre = basename($fp);
                        $info   = $diagMap[$nombre] ?? null;
                        $resultado[] = [
                            'nombre'       => $nombre,
                            'tamano'       => filesize($fp),
                            'modificado'   => date('Y-m-d H:i', filemtime($fp)),
                            'titulo'       => $info['titulo']          ?? null,
                            'tipo'         => $info['tipo_diagrama']   ?? null,
                            'version'      => $info['version']         ?? null,
                            'en_bd'        => $info !== null,
                        ];
                    }
                    echo json_encode(['success' => true, 'archivos' => $resultado]);
                    break;

                case 'eliminar_archivo_usuario':
                    $uid     = (int)($body['uid']    ?? 0);
                    $nombre  = basename($body['nombre'] ?? '');
                    if (!$uid || !$nombre || !preg_match('/\.json$/', $nombre)) throw new Exception('Datos inválidos');
                    $ruta = PUBLIC_PATH . '/uploads/usuario_' . $uid . '/' . $nombre;
                    if (!file_exists($ruta)) throw new Exception('Archivo no encontrado');
                    // Eliminar de BD si existe
                    $rutaRel = 'uploads/usuario_' . $uid . '/' . $nombre;
                    $conn->prepare("DELETE FROM diagramas WHERE archivo_ruta=:r AND usuario_id=:uid")
                         ->execute([':r' => $rutaRel, ':uid' => $uid]);
                    unlink($ruta);
                    echo json_encode(['success' => true]);
                    break;

                case 'renombrar_archivo_usuario':
                    $uid      = (int)($body['uid']      ?? 0);
                    $nombre   = basename($body['nombre']   ?? '');
                    $nuevo    = basename($body['nuevo']    ?? '');
                    $titulo   = trim($body['titulo'] ?? '');
                    if (!$uid || !$nombre || !$nuevo) throw new Exception('Datos inválidos');
                    if (!preg_match('/\.json$/', $nuevo)) $nuevo .= '.json';
                    $dir  = PUBLIC_PATH . '/uploads/usuario_' . $uid . '/';
                    $orig = $dir . $nombre;
                    $dest = $dir . $nuevo;
                    if (!file_exists($orig)) throw new Exception('Archivo no encontrado');
                    if (file_exists($dest) && $nombre !== $nuevo) throw new Exception('Ya existe un archivo con ese nombre');
                    if ($nombre !== $nuevo) rename($orig, $dest);
                    // Actualizar BD
                    $rutaOld = 'uploads/usuario_' . $uid . '/' . $nombre;
                    $rutaNueva = 'uploads/usuario_' . $uid . '/' . $nuevo;
                    $stmt = $conn->prepare("UPDATE diagramas SET archivo_ruta=:rn" . ($titulo ? ", titulo=:t" : "") . " WHERE archivo_ruta=:ro AND usuario_id=:uid");
                    $params = [':rn' => $rutaNueva, ':ro' => $rutaOld, ':uid' => $uid];
                    if ($titulo) $params[':t'] = $titulo;
                    $stmt->execute($params);
                    echo json_encode(['success' => true]);
                    break;

                // ── SVGs ──────────────────────────────────────────────
                case 'check_svgs':
                    $imgDir   = PUBLIC_PATH . '/assets/img';
                    $carpetas = [
                        'DiagramadeCasosdeUso'   => ['actor','caso-uso','sistema','asociacion','include','extend','generalizacion'],
                        'DiagramadeClases'        => ['clase','clase-abstracta','interfaz','enumeracion','asociacion','asociacion-unidireccional','asociacion-bidireccional','herencia','agregacion','composicion','dependencia','realizacion','autoasociacion'],
                        'DiagramasdeInteraccion'  => ['actor','objeto','activacion','mensaje-sincrono','mensaje-asincrono','mensaje-retorno','destruccion'],
                        'DiagramadeActividades'   => ['inicio','actividad','decision','bifurcacion','union','fin','fin-flujo','flujo'],
                        'DiagramadeEstados'       => ['estado-inicial','estado','estado-final','decision','historia','transicion'],
                        'DiagramadeComponentes'   => ['componente','interfaz','interfaz-requerida','puerto','dependencia'],
                        'DiagramadeDespliegue'    => ['nodo','dispositivo','artefacto','interfaz','asociacion'],
                        'DiagramadeObjetos'       => ['objeto','valor','enlace'],
                        'DiagramadeComunicacion'  => ['objeto','mensaje','enlace'],
                        'DiagramadeTiempo'        => ['linea-vida','evento','restriccion','estado','linea-tiempo'],
                    ];
                    $grupos = [];
                    foreach ($carpetas as $carpeta => $archivos) {
                        $dir   = "$imgDir/$carpeta";
                        $grupo = ['carpeta' => $carpeta, 'archivos' => []];
                        foreach ($archivos as $archivo) {
                            $grupo['archivos'][] = ['nombre' => "$archivo.svg", 'existe' => file_exists("$dir/$archivo.svg")];
                        }
                        $grupos[] = $grupo;
                    }
                    echo json_encode(['grupos' => $grupos]);
                    break;

                case 'generar_svgs':
                    $carpeta = $body['carpeta'] ?? '';
                    if (!$carpeta) { echo json_encode(['success' => false, 'error' => 'Carpeta requerida']); break; }
                    $imgDir = PUBLIC_PATH . '/assets/img/' . $carpeta;
                    if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);
                    $generados = 0;
                    $esperados = $this->getSVGsEsperados($carpeta);
                    foreach ($esperados as $nombre => $svgContent) {
                        $path = "$imgDir/$nombre.svg";
                        if (!file_exists($path)) { file_put_contents($path, $svgContent); $generados++; }
                    }
                    echo json_encode(['success' => true, 'generados' => $generados]);
                    break;

                default:
                    echo json_encode(['success' => false, 'error' => "Acción '$action' no reconocida"]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    // ── Helpers privados ──────────────────────────────────────────────

    private function getSchemaSQL(): string {
        $sqlFile = ROOT_PATH . '/basededatos+info/Base/diagramas_MASTER_v33.sql';
        if (file_exists($sqlFile)) return file_get_contents($sqlFile);
        return "CREATE DATABASE IF NOT EXISTS diagramas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    }

    private function getSVGsEsperados(string $carpeta): array {
        $base = [
            'actor'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="7" y1="11" x2="17" y2="11"/><line x1="12" y1="16" x2="8" y2="22"/><line x1="12" y1="16" x2="16" y2="22"/></svg>',
            'caso-uso' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="6"/></svg>',
            'clase'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="1"/><line x1="2" y1="8" x2="22" y2="8"/><line x1="2" y1="14" x2="22" y2="14"/></svg>',
            'inicio'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#198754"/></svg>',
            'fin'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6" fill="#dc3545"/></svg>',
            'decision' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12,2 22,12 12,22 2,12"/></svg>',
        ];
        $map = [
            'DiagramadeCasosdeUso'   => ['actor','caso-uso','sistema','asociacion','include','extend','generalizacion'],
            'DiagramadeClases'       => ['clase','clase-abstracta','interfaz','enumeracion','asociacion','herencia','agregacion','composicion','dependencia','realizacion'],
            'DiagramasdeInteraccion' => ['actor','objeto','activacion','mensaje-sincrono','mensaje-asincrono','mensaje-retorno','destruccion'],
            'DiagramadeActividades'  => ['inicio','actividad','decision','bifurcacion','union','fin','fin-flujo','flujo'],
            'DiagramadeEstados'      => ['estado-inicial','estado','estado-final','decision','historia','transicion'],
            'DiagramadeComponentes'  => ['componente','interfaz','interfaz-requerida','puerto','dependencia'],
            'DiagramadeDespliegue'   => ['nodo','dispositivo','artefacto','interfaz','asociacion'],
            'DiagramadeObjetos'      => ['objeto','valor','enlace'],
            'DiagramadeComunicacion' => ['objeto','mensaje','enlace'],
            'DiagramadeTiempo'       => ['linea-vida','evento','restriccion','estado','linea-tiempo'],
        ];
        $result   = [];
        $archivos = $map[$carpeta] ?? [];
        foreach ($archivos as $nombre) {
            $result[$nombre] = $base[$nombre] ?? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/></svg>';
        }
        return $result;
    }
    /** GET|POST /api/admin-dashboard */
    public function dashboardApi() {
        header('Content-Type: application/json');
        error_reporting(0); ini_set('display_errors', 0);
        if (!SessionManager::estaLogueado() || !SessionManager::esAdmin()) {
            echo json_encode(['success'=>false,'error'=>'No autorizado']); exit();
        }
        $action = $_GET['action'] ?? '';
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $db = new Database(); $conn = $db->getConnection();
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>'BD no conectada']); exit();
        }
        $adminId = SessionManager::usuarioId();
        try {
            switch ($action) {
                case 'stats':
                    $total = $conn->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
                    $alumnos = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='alumno'")->fetchColumn();
                    $maestros = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='maestro'")->fetchColumn();
                    $diags = $conn->query("SELECT COUNT(*) FROM diagramas")->fetchColumn();
                    $grupos = $conn->query("SELECT COUNT(*) FROM grupos WHERE activo=1")->fetchColumn();
                    $proyectos = $conn->query("SELECT COUNT(*) FROM proyectos")->fetchColumn();
                    $misD = $conn->prepare("SELECT COUNT(*) FROM diagramas WHERE usuario_id=:id");
                    $misD->execute([':id'=>$adminId]); 
                    echo json_encode(['total_usuarios'=>(int)$total,'alumnos'=>(int)$alumnos,'maestros'=>(int)$maestros,
                        'diagramas'=>(int)$diags,'grupos'=>(int)$grupos,'proyectos'=>(int)$proyectos,
                        'mis_diagramas'=>(int)$misD->fetchColumn()]);
                    break;
                case 'todos_diagramas':
                    $page = max(1,(int)($_GET['pagina']??1));
                    $filtro = '%'.($_GET['filtro']??'').'%';
                    $offset = ($page-1)*15;
                    $st = $conn->prepare("SELECT d.*,u.username,u.nombre_completo,u.rol FROM diagramas d
                        JOIN usuarios u ON d.usuario_id=u.id
                        WHERE d.titulo LIKE :f OR u.username LIKE :f OR u.nombre_completo LIKE :f
                        ORDER BY d.fecha_modificacion DESC LIMIT 15 OFFSET :off");
                    $st->execute([':f'=>$filtro,':off'=>$offset]);
                    $total = $conn->prepare("SELECT COUNT(*) FROM diagramas d JOIN usuarios u ON d.usuario_id=u.id WHERE d.titulo LIKE :f OR u.username LIKE :f OR u.nombre_completo LIKE :f");
                    $total->execute([':f'=>$filtro]);
                    echo json_encode(['success'=>true,'diagramas'=>$st->fetchAll(PDO::FETCH_ASSOC),'total'=>(int)$total->fetchColumn()]);
                    break;
                case 'mis_diagramas':
                    $model = new DiagramModel();
                    $filtro = $_GET['filtro']??''; $pagina = max(1,(int)($_GET['pagina']??1));
                    echo json_encode(['success'=>true,'diagramas'=>$model->listar($adminId,$filtro,$pagina,15),
                        'total'=>$model->contar($adminId,$filtro),'estadisticas'=>$model->estadisticas($adminId)]);
                    break;
                case 'todos_grupos':
                    $st = $conn->prepare("SELECT g.*,u.nombre_completo AS maestro_nombre,
                        (SELECT COUNT(*) FROM grupo_alumnos WHERE grupo_id=g.id) AS num_alumnos
                        FROM grupos g JOIN usuarios u ON g.maestro_id=u.id
                        WHERE g.activo=1 ORDER BY g.fecha_creacion DESC");
                    $st->execute();
                    echo json_encode(['success'=>true,'grupos'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;
                case 'alumnos_grupo':
                    $gid = (int)($_GET['grupo_id']??0);
                    $st = $conn->prepare("SELECT u.*,(SELECT COUNT(*) FROM diagramas WHERE usuario_id=u.id) AS num_diagramas
                        FROM usuarios u JOIN grupo_alumnos ga ON u.id=ga.alumno_id WHERE ga.grupo_id=:g");
                    $st->execute([':g'=>$gid]);
                    echo json_encode(['success'=>true,'alumnos'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;
                case 'todos_proyectos':
                    $st = $conn->prepare("SELECT p.*,u.nombre_completo AS owner_nombre,
                        (SELECT COUNT(*) FROM proyecto_miembros WHERE proyecto_id=p.id) AS num_miembros,
                        (SELECT COUNT(*) FROM proyecto_diagramas WHERE proyecto_id=p.id) AS num_diagramas
                        FROM proyectos p JOIN usuarios u ON p.creador_id=u.id ORDER BY p.fecha_creacion DESC");
                    $st->execute();
                    echo json_encode(['success'=>true,'proyectos'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;
                case 'proyecto_diagramas':
                    $pid = (int)($_GET['proyecto_id'] ?? 0);
                    if (!$pid) throw new Exception('proyecto_id requerido');
                    $st = $conn->prepare("SELECT d.*, u.username, u.nombre_completo
                        FROM proyecto_diagramas pd
                        JOIN diagramas d ON pd.diagrama_id=d.id
                        JOIN usuarios u ON d.usuario_id=u.id
                        WHERE pd.proyecto_id=:pid
                        ORDER BY d.fecha_modificacion DESC");
                    $st->execute([':pid' => $pid]);
                    echo json_encode(['success'=>true,'diagramas'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;
                case 'instalar_tabla_api':
                    // Intentar primero el nuevo editor-api schema (V47)
                    $sqlFile = dirname(__DIR__, 2) . '/editor-api/database/editor_api_schema.sql';
                    if (!file_exists($sqlFile)) {
                        // Fallback al schema antiguo
                        $sqlFile = dirname(__DIR__, 2) . '/diagramas-api/database/diagramas_api_tabla.sql';
                    }
                    $sql = $sqlFile ? @file_get_contents($sqlFile) : false;
                    if (!$sql) throw new Exception('No se encontró el archivo SQL de la API');
                    // Quitar sentencia USE para no cambiar de BD
                    $sql = preg_replace('/^USE\s+\w+;/mi', '', $sql);
                    // Quitar comentarios de línea para evitar fallos en split
                    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    $instaladas = 0;
                    foreach ($statements as $stmt) {
                        if ($stmt) { $conn->exec($stmt); $instaladas++; }
                    }
                    echo json_encode(['success' => true, 'sentencias' => $instaladas]);
                    break;
                case 'espacio_usuarios':
                    // Lista todos los usuarios con espacio usado y límite
                    $st = $conn->query("SELECT u.id, u.username, u.nombre_completo, u.rol, u.activo,
                        COALESCE(u.espacio_limite_mb, 100) AS espacio_limite_mb,
                        COALESCE(SUM(d.archivo_tamano),0) AS espacio_usado_bytes,
                        COUNT(d.id) AS num_diagramas
                        FROM usuarios u
                        LEFT JOIN diagramas d ON d.usuario_id = u.id
                        GROUP BY u.id ORDER BY espacio_usado_bytes DESC");
                    echo json_encode(['success'=>true,'usuarios'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;
                case 'cambiar_limite_espacio':
                    $uid2 = (int)($body['usuario_id'] ?? 0);
                    $lim  = (int)($body['limite_mb'] ?? 100);
                    if (!$uid2) throw new Exception('usuario_id requerido');
                    if ($lim < 0) throw new Exception('Límite inválido');
                    $conn->prepare("UPDATE usuarios SET espacio_limite_mb=:l WHERE id=:u")
                         ->execute([':l'=>$lim,':u'=>$uid2]);
                    echo json_encode(['success'=>true]);
                    break;
                case 'cambiar_limite_global':
                    $lim = (int)($body['limite_mb'] ?? 100);
                    if ($lim < 0) throw new Exception('Límite inválido');
                    $conn->prepare("UPDATE usuarios SET espacio_limite_mb=:l WHERE rol != 'admin'")
                         ->execute([':l'=>$lim]);
                    echo json_encode(['success'=>true]);
                    break;
                case 'plantillas':
                    $st = $conn->prepare("SELECT d.*, u.username, u.nombre_completo FROM diagramas d
                        JOIN usuarios u ON d.usuario_id = u.id
                        WHERE d.compartido = 1 ORDER BY d.fecha_modificacion DESC");
                    $st->execute();
                    echo json_encode(['success'=>true, 'plantillas'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;
                case 'crear_plantilla':
                    $titulo = trim($body['titulo'] ?? '');
                    $tipo   = trim($body['tipo'] ?? 'usecase');
                    $desc   = trim($body['descripcion'] ?? '');
                    if (!$titulo) throw new Exception('Titulo requerido');
                    $st = $conn->prepare("INSERT INTO diagramas (usuario_id, titulo, descripcion, tipo_diagrama, compartido, contenido_json) VALUES (:uid, :t, :d, :tipo, 1, :c)");
                    $emptyContent = json_encode(['nodes'=>[], 'connections'=>[], 'diagramType'=>$tipo]);
                    $st->execute([':uid'=>$adminId, ':t'=>$titulo, ':d'=>$desc, ':tipo'=>$tipo, ':c'=>$emptyContent]);
                    $newId = $conn->lastInsertId();
                    // Create physical file
                    $dir = dirname(__DIR__, 2) . '/public/uploads/usuario_' . $adminId;
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $titulo) . '_plantilla.json';
                    file_put_contents($dir . '/' . $filename, $emptyContent);
                    $conn->prepare("UPDATE diagramas SET archivo_ruta=:r WHERE id=:id")
                         ->execute([':r'=>'uploads/usuario_'.$adminId.'/'.$filename, ':id'=>$newId]);
                    echo json_encode(['success'=>true, 'id'=>$newId]);
                    break;
                case 'editar_plantilla':
                    $id     = (int)($body['id'] ?? 0);
                    $titulo = trim($body['titulo'] ?? '');
                    $desc   = trim($body['descripcion'] ?? '');
                    if (!$id || !$titulo) throw new Exception('Datos incompletos');
                    $conn->prepare("UPDATE diagramas SET titulo=:t, descripcion=:d WHERE id=:id AND compartido=1")
                         ->execute([':t'=>$titulo, ':d'=>$desc, ':id'=>$id]);
                    echo json_encode(['success'=>true]);
                    break;
                case 'eliminar_plantilla':
                    $id = (int)($body['id'] ?? 0);
                    if (!$id) throw new Exception('ID requerido');
                    // Only delete if marked as compartido (plantilla)
                    $st = $conn->prepare("SELECT archivo_ruta FROM diagramas WHERE id=:id AND compartido=1");
                    $st->execute([':id'=>$id]);
                    $row = $st->fetch(PDO::FETCH_ASSOC);
                    if (!$row) throw new Exception('Plantilla no encontrada');
                    if ($row['archivo_ruta']) {
                        $path = dirname(__DIR__, 2) . '/public/' . $row['archivo_ruta'];
                        if (file_exists($path)) unlink($path);
                    }
                    $conn->prepare("DELETE FROM diagramas WHERE id=:id")
                         ->execute([':id'=>$id]);
                    echo json_encode(['success'=>true]);
                    break;
                case 'backup_plantilla':
                    $id = (int)($body['id'] ?? 0);
                    if (!$id) throw new Exception('ID requerido');
                    $st = $conn->prepare("SELECT titulo, contenido_json, archivo_ruta FROM diagramas WHERE id=:id AND compartido=1");
                    $st->execute([':id'=>$id]);
                    $row = $st->fetch(PDO::FETCH_ASSOC);
                    if (!$row) throw new Exception('Plantilla no encontrada');
                    $content = $row['contenido_json'] ?? '';
                    if (!$content && $row['archivo_ruta']) {
                        $path = dirname(__DIR__, 2) . '/public/' . $row['archivo_ruta'];
                        if (file_exists($path)) {
                            $content = file_get_contents($path);
                        }
                    }
                    if (!$content) throw new Exception('No hay contenido disponible para respaldar');
                    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $row['titulo']) . '_backup.json';
                    echo json_encode(['success'=>true,'filename'=>$filename,'content'=>$content]);
                    break;
                case 'eliminar_diagrama':
                    $id = (int)($body['id']??0);
                    if (!$id) throw new Exception('ID requerido');
                    $conn->prepare("DELETE FROM diagramas WHERE id=:id")->execute([':id'=>$id]);
                    echo json_encode(['success'=>true]);
                    break;
                case 'load_code_editor':
                    $resource = trim($_GET['resource'] ?? 'diagram');
                    if ($resource === 'diagram') {
                        $id = (int)($_GET['id'] ?? 0);
                        if (!$id) throw new Exception('ID de diagrama requerido');
                        $model = new DiagramModel();
                        $diagrama = $model->obtenerCualquiera($id);
                        if (!$diagrama) throw new Exception('Diagrama no encontrado');
                        echo json_encode([
                            'success' => true,
                            'id' => $diagrama['id'],
                            'titulo' => $diagrama['titulo'],
                            'tipo_diagrama' => $diagrama['tipo_diagrama'],
                            'descripcion' => $diagrama['descripcion'],
                            'etiquetas' => $diagrama['etiquetas'] ?? '',
                            'contenido' => $diagrama['contenido'] ?? []
                        ]);
                    } elseif ($resource === 'svg') {
                        $path = trim($_GET['path'] ?? '');
                        if (!$path) throw new Exception('Ruta SVG requerida');
                        $rel = str_replace('\\', '/', ltrim($path, '/'));
                        if (strpos($rel, '..') !== false) throw new Exception('Ruta inválida');
                        if (!preg_match('#^(assets|uploads)/#', $rel)) throw new Exception('Solo se permiten rutas dentro de assets/ o uploads/');
                        $abs = PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
                        if (!file_exists($abs) || strtolower(pathinfo($abs, PATHINFO_EXTENSION)) !== 'svg') {
                            throw new Exception('SVG no encontrado o ruta no válida');
                        }
                        $content = file_get_contents($abs);
                        echo json_encode(['success' => true, 'path' => $rel, 'content' => $content]);
                    } else {
                        throw new Exception('Recurso inválido');
                    }
                    break;
                case 'guardar_codigo':
                    $resource = trim($body['resource'] ?? '');
                    if ($resource === 'diagram') {
                        $id = (int)($body['id'] ?? 0);
                        $titulo = trim($body['titulo'] ?? '');
                        $tipo_diagrama = trim($body['tipo'] ?? 'usecase');
                        $descripcion = trim($body['descripcion'] ?? '');
                        $contenido = $body['contenido'] ?? null;
                        if (!$id || !$titulo) throw new Exception('ID y título requeridos');
                        if (is_string($contenido)) {
                            $contenido = json_decode($contenido, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                throw new Exception('JSON inválido: ' . json_last_error_msg());
                            }
                        }
                        $st = $conn->prepare("SELECT usuario_id, archivo_ruta FROM diagramas WHERE id=:id");
                        $st->execute([':id' => $id]);
                        $row = $st->fetch(PDO::FETCH_ASSOC);
                        if (!$row) throw new Exception('Diagrama no encontrado');
                        $usuarioId = (int)$row['usuario_id'];
                        $oldRuta = $row['archivo_ruta'] ?? '';
                        $fm = new FileManager($usuarioId);
                        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $titulo) . '_' . $id;
                        $result = $fm->guardarDiagrama($contenido, $titulo, $id, $filename);
                        if (!$result['success']) throw new Exception($result['error'] ?? 'No se pudo guardar el diagrama');
                        if ($oldRuta && $oldRuta !== $result['ruta']) {
                            $oldPath = PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldRuta);
                            if (file_exists($oldPath)) @unlink($oldPath);
                        }
                        $stmt = $conn->prepare("UPDATE diagramas SET titulo=:titulo, tipo_diagrama=:tipo, descripcion=:desc, archivo_ruta=:ruta, archivo_tamano=:tam, fecha_modificacion=NOW(), version=version+1 WHERE id=:id");
                        $stmt->execute([':titulo' => $titulo, ':tipo' => $tipo_diagrama, ':desc' => $descripcion, ':ruta' => $result['ruta'], ':tam' => $result['tamano'], ':id' => $id]);
                        echo json_encode(['success' => true, 'id' => $id, 'ruta' => $result['ruta']]);
                    } elseif ($resource === 'svg') {
                        $path = trim($body['path'] ?? '');
                        $content = $body['content'] ?? '';
                        if (!$path) throw new Exception('Ruta SVG requerida');
                        $rel = str_replace('\\', '/', ltrim($path, '/'));
                        if (strpos($rel, '..') !== false) throw new Exception('Ruta inválida');
                        if (!preg_match('#^(assets|uploads)/#', $rel)) throw new Exception('Solo se permiten rutas dentro de assets/ o uploads/');
                        if (strtolower(pathinfo($rel, PATHINFO_EXTENSION)) !== 'svg') throw new Exception('Solo archivos SVG pueden guardarse');
                        $abs = PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
                        $dir = dirname($abs);
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $bytes = file_put_contents($abs, $content);
                        if ($bytes === false) throw new Exception('No se pudo escribir el SVG');
                        echo json_encode(['success' => true, 'path' => $rel, 'bytes' => $bytes]);
                    } else {
                        throw new Exception('Recurso inválido');
                    }
                    break;
                default:
                    throw new Exception("Acción '$action' no reconocida");
            }
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    // ══════════════════════════════════════════════════════════════════
    // CONFIGURACIÓN GLOBAL, SMTP, AUDITORÍA, BACKUP, MANTENIMIENTO
    // ══════════════════════════════════════════════════════════════════

    private function ensureConfigTable(PDO $conn): void {
        $conn->exec("CREATE TABLE IF NOT EXISTS sistema_config (
            clave   VARCHAR(100) PRIMARY KEY,
            valor   TEXT,
            tipo    VARCHAR(20) DEFAULT 'string',
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function ensureAuditoriaTable(PDO $conn): void {
        $conn->exec("CREATE TABLE IF NOT EXISTS auditoria_accesos (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            username   VARCHAR(100),
            accion     VARCHAR(100) NOT NULL,
            ip         VARCHAR(45),
            detalle    TEXT,
            fecha      DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX(fecha), INDEX(accion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function getConfig(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAdmin();
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureConfigTable($conn);
            $rows = $conn->query("SELECT * FROM sistema_config")->fetchAll(PDO::FETCH_KEY_PAIR);
            // Defaults
            $defaults = [
                'espacio_limite_alumno_mb' => '100',
                'espacio_limite_maestro_mb' => '500',
                'max_proyectos_alumno'      => '10',
                'max_miembros_proyecto'     => '20',
                'tipos_archivo_permitidos'  => 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,md,sql,csv,json,xml,png,jpg,jpeg,gif,svg',
                'tamano_max_archivo_mb'     => '25',
                'smtp_host'                 => '',
                'smtp_port'                 => '587',
                'smtp_user'                 => '',
                'smtp_pass'                 => '',
                'smtp_from'                 => '',
                'smtp_from_name'            => 'DiagramasUML',
                'modo_mantenimiento'        => '0',
                'mensaje_mantenimiento'     => 'El sistema está en mantenimiento. Vuelve pronto.',
            ];
            echo json_encode(['config' => array_merge($defaults, $rows)]);
        } catch (Exception $e) {
            echo json_encode(['config' => [], 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function saveConfig(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAdmin();
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureConfigTable($conn);
            $st = $conn->prepare("INSERT INTO sistema_config (clave,valor) VALUES (:k,:v) ON DUPLICATE KEY UPDATE valor=:v2");
            foreach ($body as $k => $v) {
                if (preg_match('/^[a-z0-9_]+$/', $k)) {
                    $st->execute([':k'=>$k, ':v'=>$v, ':v2'=>$v]);
                }
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function testSmtp(): void {
        header('Content-Type: application/json');
        // V46: SMTP eliminado
        echo json_encode(['success'=>false,'error'=>'SMTP no disponible en V46']);
        exit();
    }

    
    public function getAuditoria(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAdmin();
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureAuditoriaTable($conn);
            $limite = min(200, (int)($_GET['limite'] ?? 100));
            $accion = $_GET['accion'] ?? '';
            if ($accion) {
                $st = $conn->prepare("SELECT * FROM auditoria_accesos WHERE accion=:a ORDER BY fecha DESC LIMIT {$limite}");
                $st->execute([':a'=>$accion]);
            } else {
                $st = $conn->prepare("SELECT * FROM auditoria_accesos ORDER BY fecha DESC LIMIT {$limite}");
                $st->execute();
            }
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            // Tipos distintos de acciones para el filtro
            $tipos = $conn->query("SELECT DISTINCT accion FROM auditoria_accesos ORDER BY accion")->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['eventos'=>$rows,'tipos'=>$tipos]);
        } catch (Exception $e) {
            echo json_encode(['eventos'=>[],'tipos'=>[],'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function backupDB(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAdmin();
        try {
            $db = new Database(); $conn = $db->getConnection();
            $tablas = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $sql = "-- DiagramasUML Backup\n-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            foreach ($tablas as $tabla) {
                $sql .= "-- Tabla: {$tabla}\n";
                $create = $conn->query("SHOW CREATE TABLE `{$tabla}`")->fetch(PDO::FETCH_NUM);
                $sql .= $create[1] . ";\n\n";
                $rows = $conn->query("SELECT * FROM `{$tabla}`")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $vals = array_map(fn($v) => $v === null ? 'NULL' : $conn->quote($v), array_values($row));
                    $sql .= "INSERT INTO `{$tabla}` VALUES (" . implode(',', $vals) . ");\n";
                }
                $sql .= "\n";
            }
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            // Guardar en data/
            $dataDir = BASE_PATH . '/data';
            if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
            $filename = 'backup_' . date('Ymd_His') . '.sql';
            file_put_contents($dataDir . '/' . $filename, $sql);
            echo json_encode(['success'=>true,'filename'=>$filename,'size'=>strlen($sql),'tablas'=>count($tablas)]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function toggleMantenimiento(): void {
        header('Content-Type: application/json');
        // V46: Modo mantenimiento eliminado
        echo json_encode(['success'=>false,'error'=>'Modo mantenimiento no disponible en V46']);
        exit();
    }

    
}
?>
