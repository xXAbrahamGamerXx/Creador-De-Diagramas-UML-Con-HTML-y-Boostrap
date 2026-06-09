<?php
/**
 * ProyectoController — Proyectos colaborativos
 *
 * GET  /api/proyectos?action=mis_proyectos   → proyectos del usuario
 * GET  /api/proyectos?action=detalle&id=N    → miembros + diagramas de un proyecto
 * POST /api/proyectos?action=crear           → {nombre, descripcion}
 * POST /api/proyectos?action=unirse          → {codigo}
 * POST /api/proyectos?action=salir           → {proyecto_id}
 * POST /api/proyectos?action=agregar_diagrama→ {proyecto_id, diagrama_id}
 * POST /api/proyectos?action=quitar_diagrama → {proyecto_id, diagrama_id}
 * POST /api/proyectos?action=eliminar        → {proyecto_id}  (solo owner)
 */
class ProyectoController extends Controller {

    public function index() {
        SessionManager::verificarAcceso();
        $this->redirigir('dashboard');
    }

    public function api() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);
        SessionManager::verificarAcceso();

        $uid    = (int) SessionManager::usuarioId();
        $action = $_GET['action'] ?? '';
        // V46 fix: robustecer lectura del body — acepta JSON y también form-encoded
        $raw    = file_get_contents('php://input');
        $body   = json_decode($raw, true) ?? [];
        if (empty($body) && !empty($_POST)) {
            $body = $_POST; // fallback para form-encoded
        }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            switch ($action) {

                // ── Listar proyectos del usuario ───────────────────
                case 'mis_proyectos':
                    $st = $conn->prepare(
                        "SELECT p.id, p.nombre, p.descripcion, p.codigo, p.fecha_creacion,
                                pm.rol,
                                u.nombre_completo AS creador,
                                (SELECT COUNT(*) FROM proyecto_miembros WHERE proyecto_id=p.id) AS num_miembros,
                                (SELECT COUNT(*) FROM proyecto_diagramas WHERE proyecto_id=p.id) AS num_diagramas
                         FROM proyectos p
                         JOIN proyecto_miembros pm ON pm.proyecto_id=p.id AND pm.usuario_id=:uid
                         JOIN usuarios u ON u.id=p.creador_id
                         WHERE p.activo=1
                         ORDER BY p.fecha_creacion DESC"
                    );
                    $st->execute([':uid' => $uid]);
                    echo json_encode(['success' => true, 'proyectos' => $st->fetchAll(PDO::FETCH_ASSOC)]);
                    break;

                // ── Detalle: miembros + diagramas ──────────────────
                case 'detalle':
                    $pid = (int)($_GET['id'] ?? 0);
                    if (!$pid) throw new Exception('ID requerido');

                    // Admin puede ver cualquier proyecto sin ser miembro
                    $esAdmin = SessionManager::usuarioRol() === 'admin';

                    if (!$esAdmin) {
                        $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:pid AND usuario_id=:uid");
                        $chk->execute([':pid' => $pid, ':uid' => $uid]);
                        $memb = $chk->fetch(PDO::FETCH_ASSOC);
                        if (!$memb) throw new Exception('No eres miembro de este proyecto');
                    } else {
                        // Admin: rol virtual de owner para tener acceso total
                        $memb = ['rol' => 'owner', 'solo_lectura' => 0, 'puede_subir' => 1, 'puede_eliminar' => 1];
                    }

                    // Info del proyecto
                    $stP = $conn->prepare("SELECT p.*, u.nombre_completo AS creador FROM proyectos p JOIN usuarios u ON u.id=p.creador_id WHERE p.id=:pid");
                    $stP->execute([':pid' => $pid]);
                    $proyecto = $stP->fetch(PDO::FETCH_ASSOC);

                    // Miembros
                    $stM = $conn->prepare(
                        "SELECT u.id, u.username, u.nombre_completo, u.rol AS rol_sistema, pm.rol AS rol_proyecto
                         FROM proyecto_miembros pm JOIN usuarios u ON u.id=pm.usuario_id
                         WHERE pm.proyecto_id=:pid ORDER BY pm.rol DESC, u.nombre_completo"
                    );
                    $stM->execute([':pid' => $pid]);
                    $miembros = $stM->fetchAll(PDO::FETCH_ASSOC);

                    // Diagramas con info del autor
                    $stD = $conn->prepare(
                        "SELECT d.id, d.titulo, d.tipo_diagrama, d.version, d.fecha_modificacion,
                                d.usuario_id, u.nombre_completo AS autor, u.username AS autor_username,
                                pd.agregado_por, pd.fecha_agregado
                         FROM proyecto_diagramas pd
                         JOIN diagramas d ON d.id=pd.diagrama_id
                         JOIN usuarios u ON u.id=d.usuario_id
                         WHERE pd.proyecto_id=:pid
                         ORDER BY pd.fecha_agregado DESC"
                    );
                    $stD->execute([':pid' => $pid]);
                    $diagramas = $stD->fetchAll(PDO::FETCH_ASSOC);

                    // Archivos del proyecto
                    $stA = $conn->prepare(
                        "SELECT pa.id, pa.nombre_original, pa.mime_type, pa.tamano, pa.extension,
                                pa.fecha_subida, u.nombre_completo AS autor, u.username AS autor_username
                         FROM proyecto_archivos pa
                         JOIN usuarios u ON u.id=pa.subido_por
                         WHERE pa.proyecto_id=:pid
                         ORDER BY pa.fecha_subida DESC"
                    );
                    $stA->execute([':pid' => $pid]);
                    $archivos = $stA->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode([
                        'success'  => true,
                        'proyecto' => $proyecto,
                        'rol'      => $memb['rol'],
                        'permisos' => $memb,
                        'miembros' => $miembros,
                        'diagramas'=> $diagramas,
                        'archivos' => $archivos,
                    ]);
                    break;

                // ── Crear proyecto ─────────────────────────────────
                case 'crear':
                    $nombre = trim($body['nombre'] ?? '');
                    $desc   = trim($body['descripcion'] ?? '');
                    if (!$nombre) throw new Exception('Nombre requerido');

                    // Código único de 8 chars
                    do {
                        $codigo = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
                        $ex = $conn->prepare("SELECT 1 FROM proyectos WHERE codigo=:c");
                        $ex->execute([':c' => $codigo]);
                    } while ($ex->fetch());

                    $conn->prepare(
                        "INSERT INTO proyectos (nombre, descripcion, codigo, creador_id) VALUES (:n,:d,:c,:u)"
                    )->execute([':n'=>$nombre,':d'=>$desc,':c'=>$codigo,':u'=>$uid]);
                    $pid = $conn->lastInsertId();

                    // Auto-unirse como owner
                    $conn->prepare(
                        "INSERT INTO proyecto_miembros (proyecto_id, usuario_id, rol) VALUES (:p,:u,'owner')"
                    )->execute([':p'=>$pid,':u'=>$uid]);

                    echo json_encode(['success'=>true,'id'=>$pid,'codigo'=>$codigo]);
                    break;

                // ── Unirse por código ───────────────────────────────
                case 'unirse':
                    $codigo = strtoupper(trim($body['codigo'] ?? ''));
                    if (!$codigo) throw new Exception('Código requerido');

                    $stP = $conn->prepare("SELECT id, nombre FROM proyectos WHERE codigo=:c AND activo=1");
                    $stP->execute([':c' => $codigo]);
                    $p = $stP->fetch(PDO::FETCH_ASSOC);
                    if (!$p) throw new Exception('Código no válido o proyecto inactivo');

                    // Ya miembro?
                    $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$p['id'],':u'=>$uid]);
                    if ($chk->fetch()) throw new Exception('Ya eres miembro de este proyecto');

                    $conn->prepare(
                        "INSERT INTO proyecto_miembros (proyecto_id, usuario_id, rol) VALUES (:p,:u,'editor')"
                    )->execute([':p'=>$p['id'],':u'=>$uid]);

                    echo json_encode(['success'=>true,'nombre'=>$p['nombre'],'id'=>$p['id']]);
                    break;

                // ── Salir de proyecto ───────────────────────────────
                case 'salir':
                    $pid = (int)($body['proyecto_id'] ?? 0);
                    if (!$pid) throw new Exception('proyecto_id requerido');

                    $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    $m = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$m) throw new Exception('No eres miembro');

                    // El owner no puede salir si hay más miembros (debe transferir o eliminar)
                    $cnt = $conn->prepare("SELECT COUNT(*) FROM proyecto_miembros WHERE proyecto_id=:p");
                    $cnt->execute([':p'=>$pid]);
                    if ($m['rol']==='owner' && $cnt->fetchColumn() > 1)
                        throw new Exception('Eres el owner. Elimina el proyecto o transfiere la propiedad primero.');

                    $conn->prepare("DELETE FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u")
                         ->execute([':p'=>$pid,':u'=>$uid]);

                    // Si el owner se va solo, eliminar el proyecto
                    if ($m['rol']==='owner') {
                        $conn->prepare("DELETE FROM proyectos WHERE id=:p")->execute([':p'=>$pid]);
                    }
                    echo json_encode(['success'=>true]);
                    break;

                // ── Agregar diagrama al proyecto ────────────────────
                case 'agregar_diagrama':
                    $pid = (int)($body['proyecto_id'] ?? 0);
                    $did = (int)($body['diagrama_id'] ?? 0);
                    if (!$pid || !$did) throw new Exception('proyecto_id y diagrama_id requeridos');

                    // Verificar membresía
                    $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    if (!$chk->fetch()) throw new Exception('No eres miembro de este proyecto');

                    // Verificar que el diagrama existe (de cualquier miembro del proyecto)
                    $chkD = $conn->prepare("SELECT 1 FROM diagramas WHERE id=:d");
                    $chkD->execute([':d'=>$did]);
                    if (!$chkD->fetch()) throw new Exception('Diagrama no encontrado');

                    $conn->prepare(
                        "INSERT IGNORE INTO proyecto_diagramas (proyecto_id, diagrama_id, agregado_por) VALUES (:p,:d,:u)"
                    )->execute([':p'=>$pid,':d'=>$did,':u'=>$uid]);
                    echo json_encode(['success'=>true]);
                    break;

                // ── Quitar diagrama del proyecto ────────────────────
                case 'quitar_diagrama':
                    $pid = (int)($body['proyecto_id'] ?? 0);
                    $did = (int)($body['diagrama_id'] ?? 0);
                    if (!$pid || !$did) throw new Exception('Datos requeridos');

                    $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    $m = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$m) throw new Exception('Sin permiso');

                    // Solo el owner o quien lo agregó puede quitarlo
                    $chkAg = $conn->prepare("SELECT agregado_por FROM proyecto_diagramas WHERE proyecto_id=:p AND diagrama_id=:d");
                    $chkAg->execute([':p'=>$pid,':d'=>$did]);
                    $ag = $chkAg->fetch(PDO::FETCH_ASSOC);
                    if ($m['rol']!=='owner' && ($ag['agregado_por']??null) != $uid)
                        throw new Exception('Solo el owner o quien lo agregó puede quitarlo');

                    $conn->prepare("DELETE FROM proyecto_diagramas WHERE proyecto_id=:p AND diagrama_id=:d")
                         ->execute([':p'=>$pid,':d'=>$did]);
                    echo json_encode(['success'=>true]);
                    break;

                // ── Eliminar proyecto (solo owner) ──────────────────
                case 'eliminar':
                    $pid = (int)($body['proyecto_id'] ?? 0);
                    if (!$pid) throw new Exception('proyecto_id requerido');

                    $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    $m = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$m || $m['rol']!=='owner') throw new Exception('Solo el owner puede eliminar el proyecto');

                    $conn->prepare("DELETE FROM proyectos WHERE id=:p")->execute([':p'=>$pid]);
                    echo json_encode(['success'=>true]);
                    break;

                // ── Crear diagrama directamente en proyecto ─────────
                case 'crear_diagrama':
                    $pid    = (int)($body['proyecto_id'] ?? 0);
                    $titulo = trim($body['titulo'] ?? '');
                    $tipo   = trim($body['tipo']   ?? 'usecase');
                    if (!$pid) throw new Exception('proyecto_id requerido');
                    if (!$titulo) $titulo = 'Nuevo Diagrama';

                    $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    if (!$chk->fetch()) throw new Exception('No eres miembro de este proyecto');

                    echo json_encode(['success'=>true,'redirect'=>'/editor?tipo='.$tipo.'&proyecto='.$pid,'titulo'=>$titulo,'tipo'=>$tipo]);
                    break;

                // ── Actualizar permisos de un miembro (solo owner) ──
                case 'actualizar_permisos':
                    $pid        = (int)($body['proyecto_id'] ?? 0);
                    $miembro_id = (int)($body['miembro_id']  ?? 0);
                    if (!$pid || !$miembro_id) throw new Exception('Datos requeridos');

                    $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    $m = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$m || $m['rol']!=='owner') throw new Exception('Solo el owner puede cambiar permisos');

                    $chkM = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:m");
                    $chkM->execute([':p'=>$pid,':m'=>$miembro_id]);
                    $mTarget = $chkM->fetch(PDO::FETCH_ASSOC);
                    if (!$mTarget) throw new Exception('Miembro no encontrado');
                    if ($mTarget['rol']==='owner') throw new Exception('No puedes cambiar los permisos del owner');

                    $soloLec   = isset($body['solo_lectura'])   ? (int)(bool)$body['solo_lectura']   : 0;
                    $puedeSubir = isset($body['puede_subir'])   ? (int)(bool)$body['puede_subir']    : 1;
                    $puedeElim  = isset($body['puede_eliminar']) ? (int)(bool)$body['puede_eliminar'] : 0;

                    // Auto-patch columns if not exist
                    try { $conn->exec("ALTER TABLE proyecto_miembros ADD COLUMN IF NOT EXISTS solo_lectura TINYINT(1) NOT NULL DEFAULT 0"); } catch(Exception $ex) {}
                    try { $conn->exec("ALTER TABLE proyecto_miembros ADD COLUMN IF NOT EXISTS puede_subir TINYINT(1) NOT NULL DEFAULT 1"); } catch(Exception $ex) {}
                    try { $conn->exec("ALTER TABLE proyecto_miembros ADD COLUMN IF NOT EXISTS puede_eliminar TINYINT(1) NOT NULL DEFAULT 0"); } catch(Exception $ex) {}

                    $conn->prepare(
                        "UPDATE proyecto_miembros SET solo_lectura=:sl, puede_subir=:ps, puede_eliminar=:pe
                         WHERE proyecto_id=:pid AND usuario_id=:mid"
                    )->execute([':sl'=>$soloLec,':ps'=>$puedeSubir,':pe'=>$puedeElim,':pid'=>$pid,':mid'=>$miembro_id]);

                    echo json_encode(['success'=>true]);
                    break;

                // ── Expulsar miembro (solo owner) ───────────────────
                case 'expulsar_miembro':
                    $pid        = (int)($body['proyecto_id'] ?? 0);
                    $miembro_id = (int)($body['miembro_id']  ?? 0);
                    if (!$pid || !$miembro_id) throw new Exception('Datos requeridos');

                    $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                    $chk->execute([':p'=>$pid,':u'=>$uid]);
                    $m = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$m || $m['rol']!=='owner') throw new Exception('Solo el owner puede expulsar miembros');
                    if ($miembro_id === $uid) throw new Exception('No puedes expulsarte a ti mismo');

                    $conn->prepare("DELETE FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:m")
                         ->execute([':p'=>$pid,':m'=>$miembro_id]);
                    // Notificar al miembro expulsado
                    try {
                        if (class_exists('NotificacionController')) {
                            $stNom = $conn->prepare("SELECT nombre FROM proyectos WHERE id=:p");
                            $stNom->execute([':p'=>$pid]);
                            $nomProy = $stNom->fetchColumn() ?: 'un proyecto';
                            NotificacionController::crear($conn, $miembro_id, 'sistema',
                                'Te han removido de un proyecto',
                                "Ya no tienes acceso al proyecto: {$nomProy}",
                                '/dashboard');
                        }
                    } catch(Exception $exN) {}
                    echo json_encode(['success'=>true]);
                    break;

                // ── Listar miembros de un proyecto (para modal de tarea) ──
                case 'miembros':
                    $pid = (int)($_GET['proyecto_id'] ?? 0);
                    if (!$pid) throw new Exception('proyecto_id requerido');

                    // Verificar acceso — debe ser miembro o admin
                    $esAdmin = SessionManager::usuarioRol() === 'admin';
                    if (!$esAdmin) {
                        $chkM = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                        $chkM->execute([':p'=>$pid,':u'=>$uid]);
                        if (!$chkM->fetch()) throw new Exception('Sin acceso al proyecto');
                    }

                    $stM = $conn->prepare(
                        "SELECT u.id, u.username, u.nombre_completo, pm.rol AS rol_proyecto
                         FROM proyecto_miembros pm
                         JOIN usuarios u ON u.id=pm.usuario_id
                         WHERE pm.proyecto_id=:pid
                         ORDER BY pm.rol DESC, u.nombre_completo"
                    );
                    $stM->execute([':pid' => $pid]);
                    echo json_encode(['miembros' => $stM->fetchAll(PDO::FETCH_ASSOC)]);
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
    // PERMISOS DE PROYECTO (endpoint REST directo)
    // ══════════════════════════════════════════════════════════════════

    public function updatePermisos() {
        // Alias: delega a api() con action=actualizar_permisos
        $_GET['action'] = 'actualizar_permisos';
        $this->api();
    }

    // ══════════════════════════════════════════════════════════════════
    // ARCHIVOS DE PROYECTO
    // ══════════════════════════════════════════════════════════════════

    public function uploadFile() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();

        $pid = (int)($_POST['proyecto_id'] ?? 0);
        if (!$pid) { echo json_encode(['success'=>false,'error'=>'proyecto_id requerido']); exit(); }
        if (empty($_FILES['archivo'])) { echo json_encode(['success'=>false,'error'=>'No se recibió archivo']); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            // Verify membership
            $chk = $conn->prepare("SELECT puede_subir, solo_lectura FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
            $chk->execute([':p'=>$pid,':u'=>$uid]);
            $m = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$m) throw new Exception('No eres miembro de este proyecto');
            if ($m['solo_lectura'] || !$m['puede_subir']) throw new Exception('No tienes permiso para subir archivos');

            $file    = $_FILES['archivo'];
            $maxSize = 25 * 1024 * 1024; // 25 MB
            if ($file['size'] > $maxSize) throw new Exception('El archivo supera 25 MB');
            if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Error al subir el archivo');

            $origName  = basename($file['name']);
            $ext       = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed   = ['pdf','doc','docx','xls','xlsx','ppt','pptx','txt','md','sql','csv',
                          'json','xml','html','htm','zip','rar','png','jpg','jpeg','gif','svg'];
            if (!in_array($ext, $allowed)) throw new Exception("Extensión .$ext no permitida");

            // UUID filename (security by encryption)
            $uuid = bin2hex(random_bytes(16));
            $uuid = sprintf('%s-%s-%s-%s-%s',
                substr($uuid,0,8), substr($uuid,8,4), substr($uuid,12,4),
                substr($uuid,16,4), substr($uuid,20));

            $dir = PUBLIC_PATH . '/proyectos/proyecto_' . $pid . '/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $diskName = hash('sha256', $uuid . $pid . $uid . time()) . '.' . $ext;

            if (!move_uploaded_file($file['tmp_name'], $dir . $diskName)) {
                throw new Exception('Error al guardar el archivo en disco');
            }

            // Ruta relativa legible (evita corrupción al cifrar en TEXT)
            $pathToStore = 'proyectos/proyecto_' . $pid . '/' . $diskName;

            $conn->prepare(
                "INSERT INTO proyecto_archivos (proyecto_id, subido_por, nombre_original, nombre_disco, mime_type, tamano, extension)
                 VALUES (:p, :u, :no, :nd, :mt, :sz, :ex)"
            )->execute([
                ':p'  => $pid,
                ':u'  => $uid,
                ':no' => $origName,
                ':nd' => $pathToStore,
                ':mt' => $file['type'] ?: 'application/octet-stream',
                ':sz' => $file['size'],
                ':ex' => $ext,
            ]);

            echo json_encode(['success'=>true,'nombre'=>$origName,'ext'=>$ext]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function downloadFile() {
        $this->serveFile('attachment');
    }

    public function viewFile() {
        $this->serveFile('inline');
    }

    /** Resuelve ruta en disco desde nombre_disco (plano o cifrado legacy) */
    private function resolveArchivoRuta(string $stored, int $proyectoId = 0): string {
        $stored = trim($stored);
        if ($stored === '') return '';

        if (str_starts_with($stored, 'proyectos/')) {
            return $stored;
        }

        $appKey = defined('APP_KEY') ? APP_KEY : 'diagramasMVC_secret_key_2026';
        $dec = @openssl_decrypt(
            $stored,
            'AES-256-CBC',
            hash('sha256', $appKey, true),
            0,
            substr(hash('sha256', $appKey), 0, 16)
        );
        if (is_string($dec) && $dec !== '' && str_contains($dec, 'proyectos/')) {
            return $dec;
        }

        if (preg_match('/^[a-f0-9]{64}\.[a-z0-9]+$/i', $stored) && $proyectoId > 0) {
            return 'proyectos/proyecto_' . $proyectoId . '/' . $stored;
        }

        return $stored;
    }

    private function serveFile(string $disposition) {
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        $fid = (int)($_GET['file_id'] ?? 0);
        if (!$fid) { http_response_code(400); echo 'ID requerido'; exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            $rol = SessionManager::usuarioRol();
            if ($rol === 'admin') {
                $st = $conn->prepare("SELECT * FROM proyecto_archivos WHERE id=:f");
                $st->execute([':f'=>$fid]);
            } else {
                $st = $conn->prepare(
                    "SELECT pa.* FROM proyecto_archivos pa
                     JOIN proyecto_miembros pm ON pm.proyecto_id=pa.proyecto_id AND pm.usuario_id=:u
                     WHERE pa.id=:f"
                );
                $st->execute([':u'=>$uid,':f'=>$fid]);
            }
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (!$row) { http_response_code(403); echo 'Sin acceso'; exit(); }

            $relPath = $this->resolveArchivoRuta((string)$row['nombre_disco'], (int)$row['proyecto_id']);

            $candidates = [
                PUBLIC_PATH . '/' . ltrim($relPath, '/'),
                ROOT_PATH . '/public/' . ltrim($relPath, '/'),
                $relPath,
            ];
            $fullPath = null;
            foreach ($candidates as $c) {
                if (file_exists($c)) { $fullPath = $c; break; }
            }

            if (!$fullPath) {
                http_response_code(404);
                echo 'Archivo no encontrado en disco. Ruta: ' . basename($relPath);
                exit();
            }

            $mime = $row['mime_type'] ?: (function_exists('mime_content_type') ? mime_content_type($fullPath) : 'application/octet-stream');
            header('Content-Type: ' . $mime);
            header('Content-Disposition: ' . $disposition . '; filename="' . addslashes($row['nombre_original']) . '"');
            header('Content-Length: ' . filesize($fullPath));
            header('Cache-Control: private, max-age=3600');
            header('X-Content-Type-Options: nosniff');
            readfile($fullPath);
        } catch (Exception $e) {
            http_response_code(500); echo 'Error: ' . $e->getMessage();
        }
        exit();
    }

    public function deleteFile() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $body = $this->getJsonInput();
        $fid  = (int)($body['file_id'] ?? 0);
        if (!$fid) { echo json_encode(['success'=>false,'error'=>'file_id requerido']); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            $st = $conn->prepare("SELECT pa.*, pm.puede_eliminar, pm.rol FROM proyecto_archivos pa
                JOIN proyecto_miembros pm ON pm.proyecto_id=pa.proyecto_id AND pm.usuario_id=:u
                WHERE pa.id=:f");
            $st->execute([':u'=>$uid,':f'=>$fid]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (!$row) throw new Exception('Sin acceso');
            if (!$row['puede_eliminar'] && $row['subido_por'] != $uid && $row['rol'] !== 'owner')
                throw new Exception('Sin permiso para eliminar');

            $relPath = $this->resolveArchivoRuta((string)$row['nombre_disco'], (int)$row['proyecto_id']);
            if ($relPath) {
                @unlink(PUBLIC_PATH . '/' . ltrim($relPath, '/'));
                @unlink(ROOT_PATH . '/public/' . ltrim($relPath, '/'));
            }
            $conn->prepare("DELETE FROM proyecto_archivos WHERE id=:f")->execute([':f'=>$fid]);
            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    // ══════════════════════════════════════════════════════════════════
    // OBSERVACIONES — maestro comenta diagramas de un proyecto
    // ══════════════════════════════════════════════════════════════════

    public function getObservaciones() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid        = (int)SessionManager::usuarioId();
        $diagramaId = (int)($_GET['diagrama_id'] ?? 0);
        $proyectoId = (int)($_GET['proyecto_id'] ?? 0);
        if (!$diagramaId && !$proyectoId) { echo json_encode(['observaciones'=>[]]); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            // Auto-crear tabla si no existe
            $conn->exec("CREATE TABLE IF NOT EXISTS proyecto_observaciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                proyecto_id INT NOT NULL,
                diagrama_id INT NOT NULL,
                autor_id INT NOT NULL,
                texto TEXT NOT NULL,
                tipo_obs ENUM('observacion','reporte_error') NOT NULL DEFAULT 'observacion',
                fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_edicion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (proyecto_id), INDEX (diagrama_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            // Auto-add column if missing (for existing installations)
            try { $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS tipo_obs ENUM('observacion','reporte_error') NOT NULL DEFAULT 'observacion'"); } catch(\Exception $ex2) {}
            // Auto-add padre_id for threaded replies (V46)
            try { $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS padre_id INT NULL DEFAULT NULL"); } catch(\Exception $ex3) {}

            if ($diagramaId) {
                $st = $conn->prepare(
                    "SELECT o.*,
                            u.nombre_completo AS autor_nombre, u.username AS autor_username,
                            u.rol AS autor_rol, u.rol AS rol_autor,
                            d.titulo AS diagrama_titulo,
                            (SELECT COUNT(*) FROM proyecto_observaciones r WHERE r.padre_id = o.id) AS num_respuestas
                     FROM proyecto_observaciones o
                     JOIN usuarios u ON u.id = o.autor_id
                     LEFT JOIN diagramas d ON d.id = o.diagrama_id
                     WHERE o.diagrama_id = :d AND (o.padre_id IS NULL OR o.padre_id = 0)
                     ORDER BY o.fecha_creacion ASC"
                );
                $st->execute([':d' => $diagramaId]);
            } else {
                $st = $conn->prepare(
                    "SELECT o.*,
                            u.nombre_completo AS autor_nombre, u.username AS autor_username,
                            u.rol AS autor_rol, u.rol AS rol_autor,
                            d.titulo AS diagrama_titulo,
                            (SELECT COUNT(*) FROM proyecto_observaciones r WHERE r.padre_id = o.id) AS num_respuestas
                     FROM proyecto_observaciones o
                     JOIN usuarios u ON u.id = o.autor_id
                     LEFT JOIN diagramas d ON d.id = o.diagrama_id
                     WHERE o.proyecto_id = :p AND (o.padre_id IS NULL OR o.padre_id = 0)
                     ORDER BY o.diagrama_id, o.fecha_creacion ASC"
                );
                $st->execute([':p' => $proyectoId]);
            }
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['observaciones' => $rows]);
        } catch (Exception $e) {
            echo json_encode(['observaciones'=>[], 'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function saveObservacion() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $body = $this->getJsonInput();

        $pid  = (int)($body['proyecto_id']  ?? 0);
        $did  = (int)($body['diagrama_id']  ?? 0);
        $txt  = trim($body['texto']         ?? '');
        $oid  = (int)($body['obs_id']       ?? 0); // si viene, editar

        if (!$pid || !$did || !$txt) { echo json_encode(['success'=>false,'error'=>'Datos incompletos']); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            // Solo miembros del proyecto pueden comentar
            $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
            $chk->execute([':p'=>$pid, ':u'=>$uid]);
            $mb = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$mb) throw new Exception('No eres miembro de este proyecto');

            $conn->exec("CREATE TABLE IF NOT EXISTS proyecto_observaciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                proyecto_id INT NOT NULL,
                diagrama_id INT NOT NULL,
                autor_id INT NOT NULL,
                texto TEXT NOT NULL,
                tipo_obs ENUM('observacion','reporte_error') NOT NULL DEFAULT 'observacion',
                fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_edicion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (proyecto_id), INDEX (diagrama_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            // Auto-add column if missing (for existing installations)
            try { $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS tipo_obs ENUM('observacion','reporte_error') NOT NULL DEFAULT 'observacion'"); } catch(\Exception $ex2) {}

            if ($oid) {
                // Editar — solo el autor puede editar su observación
                $conn->prepare("UPDATE proyecto_observaciones SET texto=:t WHERE id=:o AND autor_id=:u")
                     ->execute([':t'=>$txt, ':o'=>$oid, ':u'=>$uid]);
                echo json_encode(['success'=>true, 'obs_id'=>$oid]);
            } else {
                $exist = $conn->prepare(
                    "SELECT id FROM proyecto_observaciones WHERE proyecto_id=:p AND diagrama_id=:d AND autor_id=:u ORDER BY id DESC LIMIT 1"
                );
                $exist->execute([':p'=>$pid, ':d'=>$did, ':u'=>$uid]);
                $prev = $exist->fetch(PDO::FETCH_ASSOC);
                if ($prev) {
                    $conn->prepare("UPDATE proyecto_observaciones SET texto=:t WHERE id=:o")
                         ->execute([':t'=>$txt, ':o'=>$prev['id']]);
                    $newId = (int)$prev['id'];
                } else {
                    $tipoObs = in_array($body['tipo_obs'] ?? '', ['reporte_error']) ? 'reporte_error' : 'observacion';
                    $conn->prepare("INSERT INTO proyecto_observaciones (proyecto_id,diagrama_id,autor_id,texto,tipo_obs) VALUES (:p,:d,:u,:t,:tp)")
                         ->execute([':p'=>$pid, ':d'=>$did, ':u'=>$uid, ':t'=>$txt, ':tp'=>$tipoObs]);
                    $newId = (int)$conn->lastInsertId();
                }
                // Notificar al autor del diagrama y/o al maestro
                try {
                    if (class_exists('NotificacionController')) {
                        // Obtener dueño del diagrama y maestros del proyecto
                        $stDue = $conn->prepare(
                            "SELECT d.usuario_id AS dueno_id,
                                    u.nombre_completo AS dueno_nombre,
                                    u.rol AS dueno_rol
                             FROM diagramas d
                             JOIN usuarios u ON u.id = d.usuario_id
                             WHERE d.id = :d"
                        );
                        $stDue->execute([':d' => $did]);
                        $rowDue = $stDue->fetch(PDO::FETCH_ASSOC);

                        // Obtener maestros del proyecto
                        $stMaestros = $conn->prepare(
                            "SELECT pm.usuario_id FROM proyecto_miembros pm
                             JOIN usuarios u ON u.id = pm.usuario_id
                             WHERE pm.proyecto_id = :p AND u.rol IN ('maestro','admin')"
                        );
                        $stMaestros->execute([':p' => $pid]);
                        $maestrosProyecto = $stMaestros->fetchAll(PDO::FETCH_COLUMN);

                        $urlBase = '/dashboard?open_proyecto=' . $pid . '&open_diagrama=' . $did . '&open_obs=' . $newId . '#observaciones';
                        $urlMaestro = '/maestro?open_proyecto=' . $pid . '&open_diagrama=' . $did . '&open_obs=' . $newId . '#observaciones';

                        if ($tipoObs === 'reporte_error') {
                            // Reporte de error → notificar a TODOS los maestros del proyecto
                            foreach ($maestrosProyecto as $mId) {
                                if ((int)$mId !== $uid) {
                                    NotificacionController::crear($conn, (int)$mId, 'reporte_error',
                                        'Reporte de problema en diagrama',
                                        'Un alumno reportó un problema. Haz clic para revisarlo.',
                                        $urlMaestro
                                    );
                                }
                            }
                        } elseif (!empty($rowDue['dueno_id']) && (int)$rowDue['dueno_id'] !== $uid) {
                            // Observación normal → notificar al dueño del diagrama
                            $rolActual = SessionManager::usuarioRol();
                            $esMaestroObs = in_array($rolActual, ['maestro','admin']);
                            $tituloNoti = $esMaestroObs
                                ? 'Tienes una nueva observación del maestro'
                                : 'Hay una nueva observación en tu diagrama';
                            NotificacionController::crear($conn, (int)$rowDue['dueno_id'], 'observacion',
                                $tituloNoti,
                                'Haz clic para abrir el diagrama y responder.',
                                $urlBase
                            );
                        }
                    }
                } catch (Exception $ex) {}
                echo json_encode(['success'=>true, 'obs_id'=>$newId]);
            }
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function deleteObservacion() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $body = $this->getJsonInput();
        $oid  = (int)($body['obs_id'] ?? 0);
        $rol  = SessionManager::usuarioRol();

        if (!$oid) { echo json_encode(['success'=>false,'error'=>'obs_id requerido']); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();
            // Solo el autor o un maestro/admin puede eliminar
            if ($rol === 'admin' || $rol === 'maestro') {
                $conn->prepare("DELETE FROM proyecto_observaciones WHERE id=:o")->execute([':o'=>$oid]);
            } else {
                $conn->prepare("DELETE FROM proyecto_observaciones WHERE id=:o AND autor_id=:u")->execute([':o'=>$oid,':u'=>$uid]);
            }
            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    /**
     * GET /api/proyectos/buscar-usuarios?q=...&proyecto_id=X
     * Busca usuarios registrados para invitar al proyecto (excluye ya miembros).
     */
    public function buscarUsuarios() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $q   = trim($_GET['q'] ?? '');
        $pid = (int)($_GET['proyecto_id'] ?? 0);
        if (strlen($q) < 2) { echo json_encode(['usuarios'=>[]]); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();
            $like = '%' . $q . '%';
            $st   = $conn->prepare(
                "SELECT u.id, u.username, u.nombre_completo, u.email, u.rol
                 FROM usuarios u
                 WHERE u.activo = 1
                   AND (u.username LIKE :q OR u.nombre_completo LIKE :q2 OR u.email LIKE :q3)
                   AND u.id NOT IN (
                       SELECT usuario_id FROM proyecto_miembros WHERE proyecto_id = :pid
                   )
                 ORDER BY u.nombre_completo, u.username
                 LIMIT 10"
            );
            $st->execute([':q'=>$like,':q2'=>$like,':q3'=>$like,':pid'=>$pid]);
            echo json_encode(['usuarios' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['usuarios'=>[], 'error'=>$e->getMessage()]);
        }
        exit();
    }

    /**
     * POST /api/proyectos/invitar
     * Agrega un usuario al proyecto directamente (sin necesidad de código).
     * Solo pueden hacerlo: owner del proyecto, admin global, o miembro con puede_invitar=1.
     */
    public function invitarUsuario() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $rol  = SessionManager::usuarioRol();
        $body = $this->getJsonInput();

        $pid        = (int)($body['proyecto_id'] ?? 0);
        $invitadoId = (int)($body['usuario_id']  ?? 0);
        $rolAsig    = in_array($body['rol'] ?? '', ['editor','viewer']) ? $body['rol'] : 'editor';

        if (!$pid || !$invitadoId) {
            echo json_encode(['success'=>false,'error'=>'proyecto_id y usuario_id requeridos']);
            exit();
        }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            // Auto-add puede_invitar column if missing
            try { $conn->exec("ALTER TABLE proyecto_miembros ADD COLUMN IF NOT EXISTS puede_invitar TINYINT(1) NOT NULL DEFAULT 0"); } catch(\Exception $ex) {}

            // Verificar permisos para invitar
            if ($rol !== 'admin') {
                $stMe = $conn->prepare(
                    "SELECT rol, puede_invitar FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u"
                );
                $stMe->execute([':p'=>$pid,':u'=>$uid]);
                $me = $stMe->fetch(PDO::FETCH_ASSOC);
                if (!$me) throw new Exception('No eres miembro de este proyecto');
                if ($me['rol'] !== 'owner' && !$me['puede_invitar']) {
                    throw new Exception('No tienes permisos para invitar usuarios a este proyecto');
                }
            }

            // No invitarse a sí mismo
            if ($invitadoId === $uid) throw new Exception('No puedes invitarte a ti mismo');

            // ¿Ya es miembro?
            $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
            $chk->execute([':p'=>$pid,':u'=>$invitadoId]);
            if ($chk->fetch()) throw new Exception('Este usuario ya es miembro del proyecto');

            // ¿Existe el usuario?
            $stU = $conn->prepare("SELECT id, nombre_completo, username FROM usuarios WHERE id=:u AND activo=1");
            $stU->execute([':u'=>$invitadoId]);
            $user = $stU->fetch(PDO::FETCH_ASSOC);
            if (!$user) throw new Exception('Usuario no encontrado');

            // Obtener nombre del proyecto
            $stP = $conn->prepare("SELECT nombre FROM proyectos WHERE id=:p");
            $stP->execute([':p'=>$pid]);
            $proj = $stP->fetch(PDO::FETCH_ASSOC);

            // Insertar como miembro
            $canEdit = ($rolAsig === 'editor') ? 1 : 0;
            $conn->prepare(
                "INSERT INTO proyecto_miembros (proyecto_id, usuario_id, rol, puede_subir, puede_editar, puede_eliminar, solo_lectura)
                 VALUES (:p, :u, :r, :cs, :ce, 0, :sl)"
            )->execute([
                ':p'=>$pid, ':u'=>$invitadoId, ':r'=>$rolAsig,
                ':cs'=>$canEdit, ':ce'=>$canEdit, ':sl'=>($rolAsig==='viewer'?1:0)
            ]);

            // Notificar al usuario invitado
            try {
                if (class_exists('NotificacionController')) {
                    NotificacionController::crear(
                        $conn, $invitadoId, 'proyecto',
                        'Te agregaron a un proyecto',
                        'Fuiste agregado al proyecto "' . ($proj['nombre'] ?? 'sin nombre') . '"',
                        '/dashboard#proyectos'
                    );
                }
            } catch (\Exception $ex) {}

            echo json_encode([
                'success'  => true,
                'nombre'   => $user['nombre_completo'] ?: $user['username'],
                'username' => $user['username'],
            ]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    /**
     * Crea hilo: padre_id → observación original.
     */
    public function replyObservacion() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $body = $this->getJsonInput();

        $obsId = (int)($body['obs_id']     ?? 0);
        $txt   = trim($body['texto']        ?? '');

        if (!$obsId || !$txt) {
            echo json_encode(['success'=>false,'error'=>'obs_id y texto requeridos']);
            exit();
        }

        try {
            $db   = new Database();
            $conn = $db->getConnection();

            // Auto-add padre_id column if missing
            try { $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS padre_id INT NULL DEFAULT NULL"); } catch(\Exception $ex) {}

            // Obtener la observación padre para verificar pertenencia al proyecto
            $stPadre = $conn->prepare(
                "SELECT o.*, u.rol AS autor_rol FROM proyecto_observaciones o
                 JOIN usuarios u ON u.id = o.autor_id
                 WHERE o.id = :oid"
            );
            $stPadre->execute([':oid' => $obsId]);
            $padre = $stPadre->fetch(PDO::FETCH_ASSOC);

            if (!$padre) throw new Exception('Observación no encontrada');

            $pid = (int)$padre['proyecto_id'];
            $did = (int)$padre['diagrama_id'];

            // Verificar que el usuario es miembro del proyecto
            $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
            $chk->execute([':p'=>$pid, ':u'=>$uid]);
            if (!$chk->fetch()) throw new Exception('No eres miembro de este proyecto');

            // Insertar reply
            $conn->prepare(
                "INSERT INTO proyecto_observaciones (proyecto_id, diagrama_id, autor_id, texto, padre_id)
                 VALUES (:p, :d, :u, :t, :pid)"
            )->execute([':p'=>$pid, ':d'=>$did, ':u'=>$uid, ':t'=>$txt, ':pid'=>$obsId]);

            $newId = (int)$conn->lastInsertId();

            // Notificar al autor de la observación padre
            try {
                $autorPadre = (int)$padre['autor_id'];
                if ($autorPadre !== $uid && class_exists('NotificacionController')) {
                    $rolActual = SessionManager::usuarioRol();
                    $esRolMaestro = ($rolActual === 'maestro' || $rolActual === 'admin');
                    $titulo = $esRolMaestro
                        ? 'El maestro respondió tu observación'
                        : 'Un alumno respondió tu observación';
                    // URL directa con parámetros de proyecto, diagrama y observación
                    $urlBase = $esRolMaestro ? '/dashboard' : '/maestro';
                    $urlNotif = $urlBase . '?open_proyecto=' . $pid
                                . '&open_diagrama=' . $did
                                . '&open_obs=' . $obsId
                                . '#observaciones';
                    NotificacionController::crear(
                        $conn, $autorPadre, 'observacion',
                        $titulo, substr($txt, 0, 100),
                        $urlNotif
                    );
                }
            } catch (\Exception $ex) {}

            // Devolver el reply completo para renderizarlo sin recargar
            $stNew = $conn->prepare(
                "SELECT o.*, u.nombre_completo AS autor_nombre, u.username AS autor_username,
                        u.rol AS rol_autor, d.titulo AS diagrama_titulo
                 FROM proyecto_observaciones o
                 JOIN usuarios u ON u.id = o.autor_id
                 LEFT JOIN diagramas d ON d.id = o.diagrama_id
                 WHERE o.id = :id"
            );
            $stNew->execute([':id' => $newId]);
            $reply = $stNew->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success'=>true, 'reply'=>$reply, 'obs_id'=>$newId]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    /**
     * GET /api/observaciones/thread?obs_id=X
     * Devuelve el hilo completo de respuestas de una observación.
     */
    public function getThread() {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $obsId = (int)($_GET['obs_id'] ?? 0);
        if (!$obsId) { echo json_encode(['replies'=>[]]); exit(); }

        try {
            $db   = new Database();
            $conn = $db->getConnection();
            try { $conn->exec("ALTER TABLE proyecto_observaciones ADD COLUMN IF NOT EXISTS padre_id INT NULL DEFAULT NULL"); } catch(\Exception $ex) {}

            $st = $conn->prepare(
                "SELECT o.*, u.nombre_completo AS autor_nombre, u.username AS autor_username,
                        u.rol AS rol_autor, d.titulo AS diagrama_titulo
                 FROM proyecto_observaciones o
                 JOIN usuarios u ON u.id = o.autor_id
                 LEFT JOIN diagramas d ON d.id = o.diagrama_id
                 WHERE o.padre_id = :oid
                 ORDER BY o.fecha_creacion ASC"
            );
            $st->execute([':oid' => $obsId]);
            echo json_encode(['replies' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['replies'=>[], 'error'=>$e->getMessage()]);
        }
        exit();
    }

    // ══════════════════════════════════════════════════════════════════
    // TAREAS DE PROYECTO
    // ══════════════════════════════════════════════════════════════════

    private function ensureTareasTable(PDO $conn): void {
        $conn->exec("CREATE TABLE IF NOT EXISTS proyecto_tareas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            proyecto_id INT NOT NULL, creador_id INT NOT NULL,
            titulo VARCHAR(300) NOT NULL, descripcion TEXT,
            asignado_a INT DEFAULT NULL,
            fecha_limite DATE,
            estado ENUM('pendiente','en_progreso','entregada','calificada') DEFAULT 'pendiente',
            calificacion DECIMAL(4,2), comentario_cal TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX(proyecto_id),
            INDEX(asignado_a)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        try { $conn->exec("ALTER TABLE proyecto_tareas ADD COLUMN asignado_a INT DEFAULT NULL"); } catch(Exception $ex) {}
        try { $conn->exec("ALTER TABLE proyecto_tareas ADD COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP"); } catch(Exception $ex) {}
        $conn->exec("CREATE TABLE IF NOT EXISTS proyecto_tareas_entregas (
            id INT AUTO_INCREMENT PRIMARY KEY, tarea_id INT NOT NULL,
            usuario_id INT NOT NULL, texto TEXT, diagrama_id INT,
            calificacion DECIMAL(4,2) NULL, comentario_cal TEXT NULL,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq(tarea_id,usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        try { $conn->exec("ALTER TABLE proyecto_tareas_entregas ADD COLUMN calificacion DECIMAL(4,2) NULL"); } catch (Exception $ex) {}
        try { $conn->exec("ALTER TABLE proyecto_tareas_entregas ADD COLUMN comentario_cal TEXT NULL"); } catch (Exception $ex) {}
    }

    /** GET /api/tareas-proyecto/entregas?tarea_id=N — entregas por alumno (maestro) */
    public function getEntregasTarea(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        $tid = (int)($_GET['tarea_id'] ?? 0);
        if (!$tid) { echo json_encode(['success'=>false,'error'=>'tarea_id requerido']); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureTareasTable($conn);
            $stT = $conn->prepare(
                "SELECT t.*, p.nombre AS proyecto_nombre
                 FROM proyecto_tareas t
                 JOIN proyectos p ON p.id=t.proyecto_id
                 WHERE t.id=:t"
            );
            $stT->execute([':t'=>$tid]);
            $tarea = $stT->fetch(PDO::FETCH_ASSOC);
            if (!$tarea) throw new Exception('Tarea no encontrada');

            $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
            $chk->execute([':p'=>$tarea['proyecto_id'], ':u'=>$uid]);
            if (!$chk->fetch() && SessionManager::usuarioRol() !== 'admin') {
                throw new Exception('Sin acceso');
            }

            $asignado = (int)($tarea['asignado_a'] ?? 0);
            if ($asignado > 0) {
                $stM = $conn->prepare(
                    "SELECT u.id AS usuario_id, u.nombre_completo, u.username, u.rol AS rol_sistema
                     FROM usuarios u WHERE u.id=:a"
                );
                $stM->execute([':a'=>$asignado]);
            } else {
                $stM = $conn->prepare(
                    "SELECT u.id AS usuario_id, u.nombre_completo, u.username, u.rol AS rol_sistema
                     FROM proyecto_miembros pm
                     JOIN usuarios u ON u.id=pm.usuario_id
                     WHERE pm.proyecto_id=:p AND u.rol='alumno'
                     ORDER BY u.nombre_completo"
                );
                $stM->execute([':p'=>$tarea['proyecto_id']]);
            }
            $miembros = $stM->fetchAll(PDO::FETCH_ASSOC);

            $entregas = [];
            foreach ($miembros as $m) {
                $muid = (int)$m['usuario_id'];
                $stE = $conn->prepare(
                    "SELECT e.id AS entrega_id, e.texto, e.diagrama_id, e.fecha AS fecha_entrega,
                            e.calificacion, e.comentario_cal,
                            d.titulo AS diagrama_titulo, d.tipo_diagrama AS diagrama_tipo
                     FROM proyecto_tareas_entregas e
                     LEFT JOIN diagramas d ON d.id=e.diagrama_id
                     WHERE e.tarea_id=:t AND e.usuario_id=:u"
                );
                $stE->execute([':t'=>$tid, ':u'=>$muid]);
                $ent = $stE->fetch(PDO::FETCH_ASSOC) ?: [];
                $entregas[] = array_merge($m, [
                    'alumno_id' => $muid,
                    'entrega_id' => $ent['entrega_id'] ?? null,
                    'texto' => $ent['texto'] ?? null,
                    'diagrama_id' => $ent['diagrama_id'] ?? null,
                    'diagrama_titulo' => $ent['diagrama_titulo'] ?? null,
                    'diagrama_tipo' => $ent['diagrama_tipo'] ?? null,
                    'fecha_entrega' => $ent['fecha_entrega'] ?? null,
                    'calificacion' => $ent['calificacion'] ?? null,
                    'comentario_cal' => $ent['comentario_cal'] ?? null,
                    'comentario_alumno' => $ent['texto'] ?? null,
                ]);
            }

            echo json_encode(['success'=>true, 'tarea'=>$tarea, 'entregas'=>$entregas]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false, 'error'=>$e->getMessage(), 'entregas'=>[]]);
        }
        exit();
    }

    /** POST /api/tareas-proyecto/calificar — calificar entrega de un alumno */
    public function calificarEntregaTarea(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $rol = SessionManager::usuarioRol();
        if (!in_array($rol, ['maestro', 'admin'], true)) {
            echo json_encode(['success'=>false,'error'=>'Solo maestros pueden calificar']); exit();
        }
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $tid = (int)($body['tarea_id'] ?? 0);
        $alumnoId = (int)($body['alumno_id'] ?? 0);
        $cal = isset($body['calificacion']) ? (float)$body['calificacion'] : null;
        if (!$tid || !$alumnoId || $cal === null) {
            echo json_encode(['success'=>false,'error'=>'Datos incompletos']); exit();
        }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureTareasTable($conn);
            $conn->prepare(
                "UPDATE proyecto_tareas_entregas SET calificacion=:c, comentario_cal=:cc
                 WHERE tarea_id=:t AND usuario_id=:u"
            )->execute([
                ':c'=>$cal,
                ':cc'=>$body['comentario'] ?? '',
                ':t'=>$tid,
                ':u'=>$alumnoId,
            ]);
            $conn->prepare("UPDATE proyecto_tareas SET estado='calificada', calificacion=:c, comentario_cal=:cc WHERE id=:t")
                 ->execute([':c'=>$cal, ':cc'=>$body['comentario'] ?? '', ':t'=>$tid]);
            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function getTareas(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        $pid = (int)($_GET['proyecto_id'] ?? 0);
        if (!$pid) { echo json_encode(['tareas'=>[]]); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureTareasTable($conn);
            $esAdmin = SessionManager::usuarioRol() === 'admin';
            if (!$esAdmin) {
                $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                $chk->execute([':p'=>$pid,':u'=>$uid]);
                if (!$chk->fetch()) throw new Exception('Sin acceso');
            }
            $st = $conn->prepare(
                "SELECT t.*, u.nombre_completo AS creador_nombre, u2.nombre_completo AS asignado_nombre,
                 (SELECT COUNT(*) FROM proyecto_tareas_entregas WHERE tarea_id=t.id) AS num_entregas,
                 (SELECT texto FROM proyecto_tareas_entregas WHERE tarea_id=t.id AND usuario_id=:uid_ent) AS mi_entrega,
                 (SELECT diagrama_id FROM proyecto_tareas_entregas WHERE tarea_id=t.id AND usuario_id=:uid_diag) AS mi_diagrama_id,
                 (SELECT fecha FROM proyecto_tareas_entregas WHERE tarea_id=t.id AND usuario_id=:uid_fec) AS mi_fecha_entrega
                 FROM proyecto_tareas t
                 JOIN usuarios u ON u.id=t.creador_id
                 LEFT JOIN usuarios u2 ON u2.id=t.asignado_a
                 WHERE t.proyecto_id=:p ORDER BY t.fecha_creacion DESC"
            );
            $st->execute([
                ':p' => $pid,
                ':uid_ent' => $uid,
                ':uid_diag' => $uid,
                ':uid_fec' => $uid,
            ]);
            echo json_encode(['tareas'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['tareas'=>[],'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function saveTarea(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $rol  = SessionManager::usuarioRol();
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $pid  = (int)($body['proyecto_id'] ?? 0);
        $tid  = (int)($body['tarea_id']    ?? 0);
        if (!$pid) { echo json_encode(['success'=>false,'error'=>'proyecto_id requerido']); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureTareasTable($conn);
            $chk = $conn->prepare("SELECT rol FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
            $chk->execute([':p'=>$pid,':u'=>$uid]);
            $m = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$m && $rol !== 'admin') throw new Exception('Sin acceso');

            $asignadoA = null;
            if (isset($body['asignado_a']) && $body['asignado_a'] !== '') {
                $asignadoA = (int)$body['asignado_a'];
                $chk2 = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                $chk2->execute([':p'=>$pid,':u'=>$asignadoA]);
                if (!$chk2->fetch()) throw new Exception('Usuario asignado no es miembro del proyecto');
            }

            if ($tid) {
                if (isset($body['calificacion'])) {
                    $conn->prepare("UPDATE proyecto_tareas SET calificacion=:c,comentario_cal=:cc,estado='calificada' WHERE id=:id")
                         ->execute([':c'=>$body['calificacion'],':cc'=>$body['comentario_cal']??'',':id'=>$tid]);
                } else {
                    $conn->prepare("UPDATE proyecto_tareas SET titulo=:t,descripcion=:d,asignado_a=:aa,fecha_limite=:fl WHERE id=:id AND proyecto_id=:p")
                         ->execute([':t'=>$body['titulo']??'',':d'=>$body['descripcion']??'',':aa'=>$asignadoA,':fl'=>$body['fecha_limite']??null,':id'=>$tid,':p'=>$pid]);
                }
                echo json_encode(['success'=>true,'tarea_id'=>$tid]);
            } else {
                $conn->prepare("INSERT INTO proyecto_tareas (proyecto_id,creador_id,titulo,descripcion,asignado_a,fecha_limite) VALUES (:p,:u,:t,:d,:aa,:fl)")
                     ->execute([':p'=>$pid,':u'=>$uid,':t'=>$body['titulo']??'Tarea',':d'=>$body['descripcion']??'',':aa'=>$asignadoA,':fl'=>$body['fecha_limite']??null]);
                $newId = (int)$conn->lastInsertId();
                $miembros = $conn->prepare("SELECT usuario_id FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id!=:u");
                $miembros->execute([':p'=>$pid,':u'=>$uid]);
                if (class_exists('NotificacionController')) {
                    foreach ($miembros->fetchAll(PDO::FETCH_COLUMN) as $muid) {
                        NotificacionController::crear($conn,(int)$muid,'tarea','Nueva tarea asignada',$body['titulo']??'','/dashboard');
                    }
                }
                echo json_encode(['success'=>true,'tarea_id'=>$newId]);
            }
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function deleteTarea(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $rol  = SessionManager::usuarioRol();
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $tid  = (int)($body['tarea_id'] ?? 0);
        if (!$tid) { echo json_encode(['success'=>false,'error'=>'tarea_id requerido']); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            if ($rol === 'admin') {
                $conn->prepare("DELETE FROM proyecto_tareas WHERE id=:t")->execute([':t'=>$tid]);
            } else {
                $conn->prepare("DELETE FROM proyecto_tareas WHERE id=:t AND creador_id=:u")->execute([':t'=>$tid,':u'=>$uid]);
            }
            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function entregarTarea(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $tid  = (int)($body['tarea_id'] ?? 0);
        if (!$tid) { echo json_encode(['success'=>false,'error'=>'tarea_id requerido']); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $this->ensureTareasTable($conn);
            $chk = $conn->prepare("SELECT asignado_a FROM proyecto_tareas WHERE id=:t");
            $chk->execute([':t'=>$tid]);
            $t = $chk->fetch(PDO::FETCH_ASSOC);
            if ($t && $t['asignado_a'] && (int)$t['asignado_a'] !== $uid) {
                throw new Exception('Esta tarea está asignada a otro miembro');
            }
            $conn->prepare("INSERT INTO proyecto_tareas_entregas (tarea_id,usuario_id,texto,diagrama_id)
                            VALUES (:t,:u,:tx,:d)
                            ON DUPLICATE KEY UPDATE texto=:tx2,diagrama_id=:d2,fecha=NOW()")
                 ->execute([':t'=>$tid,':u'=>$uid,':tx'=>$body['texto']??'',':d'=>$body['diagrama_id']??null,
                            ':tx2'=>$body['texto']??'',':d2'=>$body['diagrama_id']??null]);
            $conn->prepare("UPDATE proyecto_tareas SET estado='entregada' WHERE id=:t AND estado='pendiente'")->execute([':t'=>$tid]);
            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    // ══════════════════════════════════════════════════════════════════
    // CALIFICACIONES
    // ══════════════════════════════════════════════════════════════════

    public function saveCalificacion(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $rol  = SessionManager::usuarioRol();
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $pid  = (int)($body['proyecto_id'] ?? 0);
        $did  = (int)($body['diagrama_id'] ?? 0);
        $cal  = (float)($body['calificacion'] ?? 0);
        if (!$pid || !$did) { echo json_encode(['success'=>false,'error'=>'Datos incompletos']); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            if ($rol !== 'maestro' && $rol !== 'admin') throw new Exception('Solo maestros pueden calificar');
            try { $conn->exec("ALTER TABLE proyecto_diagramas ADD COLUMN IF NOT EXISTS calificacion DECIMAL(4,2)"); } catch(Exception $ex){}
            try { $conn->exec("ALTER TABLE proyecto_diagramas ADD COLUMN IF NOT EXISTS comentario_cal TEXT"); } catch(Exception $ex){}
            try { $conn->exec("ALTER TABLE proyecto_diagramas ADD COLUMN IF NOT EXISTS calificado_por INT"); } catch(Exception $ex){}
            try { $conn->exec("ALTER TABLE proyecto_diagramas ADD COLUMN IF NOT EXISTS fecha_cal DATETIME"); } catch(Exception $ex){}
            $conn->prepare("UPDATE proyecto_diagramas SET calificacion=:c,comentario_cal=:cc,calificado_por=:u,fecha_cal=NOW()
                            WHERE proyecto_id=:p AND diagrama_id=:d")
                 ->execute([':c'=>$cal,':cc'=>$body['comentario']??'',':u'=>$uid,':p'=>$pid,':d'=>$did]);
            $dueño = $conn->prepare("SELECT usuario_id FROM diagramas WHERE id=:d");
            $dueño->execute([':d'=>$did]);
            $dRow = $dueño->fetch(PDO::FETCH_ASSOC);
            if ($dRow) {
                NotificacionController::crear($conn,(int)$dRow['usuario_id'],'calificacion',
                    'Tu diagrama fue calificado',"Calificación: {$cal}/10",'/dashboard');
            }
            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
        exit();
    }

    public function getCalificaciones(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $pid = (int)($_GET['proyecto_id'] ?? 0);
        if (!$pid) { echo json_encode(['calificaciones'=>[]]); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $st = $conn->prepare(
                "SELECT pd.diagrama_id,pd.calificacion,pd.comentario_cal,pd.fecha_cal,
                        u.nombre_completo AS calificado_por_nombre, d.titulo, d.tipo_diagrama
                 FROM proyecto_diagramas pd
                 JOIN diagramas d ON d.id=pd.diagrama_id
                 LEFT JOIN usuarios u ON u.id=pd.calificado_por
                 WHERE pd.proyecto_id=:p AND pd.calificacion IS NOT NULL"
            );
            $st->execute([':p'=>$pid]);
            echo json_encode(['calificaciones'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['calificaciones'=>[]]);
        }
        exit();
    }

    // ══════════════════════════════════════════════════════════════════
    // BITÁCORA DE ACTIVIDAD
    // ══════════════════════════════════════════════════════════════════

    public function getBitacora(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        $pid = (int)($_GET['proyecto_id'] ?? 0);
        if (!$pid) { echo json_encode(['eventos'=>[]]); exit(); }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $conn->exec("CREATE TABLE IF NOT EXISTS proyecto_bitacora (
                id INT AUTO_INCREMENT PRIMARY KEY, proyecto_id INT NOT NULL,
                usuario_id INT NOT NULL, accion VARCHAR(100) NOT NULL,
                descripcion TEXT, fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX(proyecto_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $esAdmin = SessionManager::usuarioRol() === 'admin';
            if (!$esAdmin) {
                $chk = $conn->prepare("SELECT 1 FROM proyecto_miembros WHERE proyecto_id=:p AND usuario_id=:u");
                $chk->execute([':p'=>$pid,':u'=>$uid]);
                if (!$chk->fetch()) throw new Exception('Sin acceso');
            }
            $st = $conn->prepare(
                "SELECT b.*, u.nombre_completo AS autor, u.username
                 FROM proyecto_bitacora b JOIN usuarios u ON u.id=b.usuario_id
                 WHERE b.proyecto_id=:p ORDER BY b.fecha DESC LIMIT 100"
            );
            $st->execute([':p'=>$pid]);
            echo json_encode(['eventos'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(['eventos'=>[],'error'=>$e->getMessage()]);
        }
        exit();
    }

    public static function registrarEvento(PDO $conn, int $pid, int $uid, string $accion, string $desc = ''): void {
        try {
            $conn->prepare("INSERT IGNORE INTO proyecto_bitacora (proyecto_id,usuario_id,accion,descripcion) VALUES (:p,:u,:a,:d)")
                 ->execute([':p'=>$pid,':u'=>$uid,':a'=>$accion,':d'=>$desc]);
        } catch (Exception $e) {}
    }
}
?>
