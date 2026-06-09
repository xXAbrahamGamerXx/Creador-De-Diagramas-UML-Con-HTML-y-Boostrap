<?php
/**
 * NotificacionController — sistema de notificaciones en tiempo real (polling)
 * Tabla auto-creada: notificaciones
 */
class NotificacionController extends Controller {

    private function ensureTable(PDO $conn): void {
        $conn->exec("CREATE TABLE IF NOT EXISTS notificaciones (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id    INT NOT NULL,
            tipo          VARCHAR(50) NOT NULL DEFAULT 'info',
            titulo        VARCHAR(200) NOT NULL,
            mensaje       TEXT,
            url           VARCHAR(500),
            leida         TINYINT(1) NOT NULL DEFAULT 0,
            fecha         DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX(usuario_id), INDEX(leida)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    /** GET /api/notificaciones */
    public function getAll(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        try {
            $db   = new Database(); $conn = $db->getConnection();
            $this->ensureTable($conn);
            $st = $conn->prepare(
                "SELECT * FROM notificaciones WHERE usuario_id=:u ORDER BY fecha DESC LIMIT 50"
            );
            $st->execute([':u' => $uid]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            $noLeidas = array_sum(array_map(fn($r) => (int)!$r['leida'], $rows));
            echo json_encode(['notificaciones' => $rows, 'no_leidas' => $noLeidas]);
        } catch (Exception $e) {
            echo json_encode(['notificaciones' => [], 'no_leidas' => 0]);
        }
        exit();
    }

    /** POST /api/notificaciones/leer */
    public function marcarLeida(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid  = (int)SessionManager::usuarioId();
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $nid  = (int)($body['id'] ?? 0);
        try {
            $db   = new Database(); $conn = $db->getConnection();
            $this->ensureTable($conn);
            $conn->prepare("UPDATE notificaciones SET leida=1 WHERE id=:n AND usuario_id=:u")
                 ->execute([':n' => $nid, ':u' => $uid]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
        exit();
    }

    /** POST /api/notificaciones/leer-todas */
    public function marcarTodasLeidas(): void {
        header('Content-Type: application/json');
        SessionManager::verificarAcceso();
        $uid = (int)SessionManager::usuarioId();
        try {
            $db   = new Database(); $conn = $db->getConnection();
            $this->ensureTable($conn);
            $conn->prepare("UPDATE notificaciones SET leida=1 WHERE usuario_id=:u")
                 ->execute([':u' => $uid]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
        exit();
    }

    /**
     * Helper estático para crear notificaciones desde otros controladores.
     * Uso: NotificacionController::crear($conn, $usuarioId, 'observacion', 'Título', 'Mensaje', '/url');
     */
    public static function crear(PDO $conn, int $uid, string $tipo, string $titulo, string $mensaje = '', string $url = ''): void {
        try {
            $conn->exec("CREATE TABLE IF NOT EXISTS notificaciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL, tipo VARCHAR(50) NOT NULL DEFAULT 'info',
                titulo VARCHAR(200) NOT NULL, mensaje TEXT, url VARCHAR(500),
                leida TINYINT(1) NOT NULL DEFAULT 0, fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX(usuario_id), INDEX(leida)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $conn->prepare("INSERT INTO notificaciones (usuario_id,tipo,titulo,mensaje,url) VALUES (:u,:t,:ti,:m,:url)")
                 ->execute([':u'=>$uid, ':t'=>$tipo, ':ti'=>$titulo, ':m'=>$mensaje, ':url'=>$url]);
        } catch (Exception $e) {
            // silencioso — las notificaciones son opcionales
        }
    }
}
?>
