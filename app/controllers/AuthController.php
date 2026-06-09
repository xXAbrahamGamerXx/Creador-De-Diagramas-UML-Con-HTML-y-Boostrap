<?php
/**
 * app/controllers/AuthController.php — Controlador de Autenticación
 *
 * Maneja: login, logout, registro.
 * Migrado desde: login.php, register.php, api/login.php, api/register.php
 */
class AuthController extends Controller {

    // Código de invitación para maestros (cámbialo por uno seguro)
    const CODIGO_MAESTRO = 'MAESTRO2024';

    // ── Vistas ────────────────────────────────────────────────────────

    /** GET /login */
    public function loginView() {
        if (SessionManager::estaLogueado()) {
            SessionManager::redirigirPorRol();
        }
        $this->render('auth/login');
    }

    /** GET /register */
    public function registerView() {
        if (SessionManager::estaLogueado()) {
            SessionManager::redirigirPorRol();
        }
        $this->render('auth/register');
    }

    // ── API JSON ──────────────────────────────────────────────────────

    /** POST /api/login */
    public function login() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        $data = $this->getJsonInput();

        if (!isset($data['username']) || !isset($data['password'])) {
            $this->json(['success' => false, 'error' => 'Usuario y contraseña requeridos']);
        }

        try {
            $userModel = new UserModel();
            $usuario   = $userModel->findByUsernameOrEmail($data['username']);

            if ($usuario && password_verify($data['password'], $usuario['password'])) {
                SessionManager::iniciarSesion($usuario);
                $userModel->updateLastAccess($usuario['id']);
                try { FileManager::crearCarpetaUsuarioAlta($usuario['id']); } catch (Exception $fe) {
                    error_log('AuthController::login FileManager error: ' . $fe->getMessage());
                }

                $destinos = ['admin' => 'admin', 'maestro' => 'maestro', 'alumno' => 'dashboard'];
                $rol      = $usuario['rol'] ?? 'alumno';
                $redirect = BASE_URL . '/' . ($destinos[$rol] ?? 'dashboard');

                $this->json([
                    'success'  => true,
                    'rol'      => $rol,
                    'redirect' => $redirect,
                    'user'     => [
                        'id'       => $usuario['id'],
                        'username' => $usuario['username'],
                        'nombre'   => $usuario['nombre_completo'],
                        'email'    => $usuario['email'],
                        'rol'      => $rol,
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'error' => 'Usuario o contraseña incorrectos']);
            }
        } catch (Exception $e) {
            error_log("AuthController::login — " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
        }
    }

    /** POST /api/register */
    public function register() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        $data = $this->getJsonInput();

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            $this->json(['success' => false, 'error' => 'Todos los campos son requeridos']);
        }

        // Determinar rol
        $rolSolicitado = $data['rol'] ?? 'alumno';
        if ($rolSolicitado === 'maestro') {
            $codigoEnviado = $data['codigo_maestro'] ?? '';
            if ($codigoEnviado !== self::CODIGO_MAESTRO) {
                $this->json(['success' => false, 'error' => 'Código de maestro incorrecto']);
            }
            $rol = 'maestro';
        } elseif ($rolSolicitado === 'admin') {
            $this->json(['success' => false, 'error' => 'No puedes registrarte como admin']);
        } else {
            $rol = 'alumno';
        }

        // Validaciones
        if (strlen($data['username']) < 3) {
            $this->json(['success' => false, 'error' => 'El usuario debe tener al menos 3 caracteres']);
        }
        if (strlen($data['password']) < 6) {
            $this->json(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
        }

        try {
            $userModel = new UserModel();

            if ($userModel->existsUsernameOrEmail($data['username'], $data['email'])) {
                $this->json(['success' => false, 'error' => 'El usuario o email ya existe']);
            }

            $nuevoId = $userModel->crear([
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'nombre'   => $data['nombre'] ?? $data['username'],
                'rol'      => $rol,
            ]);

            FileManager::crearCarpetaUsuarioAlta($nuevoId);

            $destinos = ['alumno' => 'dashboard', 'maestro' => 'maestro'];
            $redirect = BASE_URL . '/' . ($destinos[$rol] ?? 'dashboard');

            $this->json([
                'success'  => true,
                'message'  => 'Cuenta creada correctamente',
                'rol'      => $rol,
                'redirect' => $redirect,
            ]);
        } catch (Exception $e) {
            error_log("AuthController::register — " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
        }
    }

    /** GET /logout */
    public function logout() {
        SessionManager::cerrarSesion();
        $this->redirigir('login');
    }

    /** GET / — punto de entrada */
    public function index() {
        if (SessionManager::estaLogueado()) {
            SessionManager::redirigirPorRol();
        } else {
            $this->redirigir('login');
        }
    }

    // ── Login de Emergencia (sin BD) ──────────────────────────────────

    /**
     * POST /api/emergency-login
     * Permite al superadmin entrar al panel aunque la BD esté caída.
     * Las credenciales se validan contra un hash local almacenado en data/emergency.dat
     * que se configura desde el Panel de Administración.
     */
    public function emergencyLogin() {
        header('Content-Type: application/json');
        error_reporting(0);
        ini_set('display_errors', 0);

        // Rate limiting básico por IP (máx 5 intentos por IP por ventana de 10 min)
        $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $lockFile = ROOT_PATH . '/data/el_' . md5($ip) . '.tmp';
        $now      = time();

        if (file_exists($lockFile)) {
            $data = json_decode(file_get_contents($lockFile), true) ?? ['count' => 0, 'first' => $now];
            if ($data['count'] >= 5 && ($now - $data['first']) < 600) {
                $restante = 600 - ($now - $data['first']);
                echo json_encode(['success' => false, 'error' => "Demasiados intentos. Espera {$restante}s."]);
                exit();
            }
            if (($now - $data['first']) >= 600) {
                $data = ['count' => 0, 'first' => $now]; // reiniciar ventana
            }
        } else {
            $data = ['count' => 0, 'first' => $now];
        }

        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        // Registrar intento antes de verificar
        $data['count']++;
        file_put_contents($lockFile, json_encode($data), LOCK_EX);

        // Cargar hash de emergencia
        $emergencyFile = ROOT_PATH . '/data/emergency.dat';
        if (!file_exists($emergencyFile)) {
            $this->logEmergency($ip, 'FAIL_NO_SETUP', $username);
            echo json_encode(['success' => false, 'error' => 'Acceso de emergencia no configurado. Contacta al administrador del servidor.']);
            exit();
        }

        $stored = json_decode(file_get_contents($emergencyFile), true);
        if (!isset($stored['username_hash'], $stored['password_hash'])) {
            $this->logEmergency($ip, 'FAIL_CORRUPT', $username);
            echo json_encode(['success' => false, 'error' => 'Archivo de emergencia corrupto.']);
            exit();
        }

        // Verificar credenciales con timing constante (evitar timing attacks)
        $usernameOk = hash_equals($stored['username_hash'], hash('sha256', $username . $stored['salt']));
        $passwordOk = password_verify($password . $stored['pepper'], $stored['password_hash']);

        if (!$usernameOk || !$passwordOk) {
            $this->logEmergency($ip, 'FAIL_WRONG', $username);
            echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas.']);
            exit();
        }

        // Éxito — iniciar sesión de emergencia limitada
        session_start();
        $_SESSION['emergency_mode']    = true;
        $_SESSION['emergency_ip']      = $ip;
        $_SESSION['emergency_time']    = $now;
        $_SESSION['user_id']           = 0;
        $_SESSION['user_rol']          = 'admin';
        $_SESSION['user_nombre']       = 'Superadmin (Emergencia)';
        $_SESSION['user_username']     = 'emergency_admin';
        $_SESSION['session_created']   = $now;
        // Sesión caduca en 30 minutos
        $_SESSION['emergency_expires'] = $now + 1800;

        $this->logEmergency($ip, 'SUCCESS', $username);

        // Limpiar lockfile al entrar bien
        @unlink($lockFile);

        echo json_encode([
            'success'  => true,
            'redirect' => BASE_URL . '/admin',
            'message'  => 'Sesión de emergencia iniciada. Expira en 30 minutos.',
        ]);
        exit();
    }

    /**
     * POST /api/emergency-unlock
     * Primer factor: verifica que quien intenta usar el acceso de emergencia
     * conoce la contraseña. Si pasa, el frontend muestra el formulario real.
     * Usa la misma contraseña del archivo emergency.dat para no tener dos secretos.
     */
    public function emergencyUnlock() {
        header('Content-Type: application/json');
        error_reporting(0); ini_set('display_errors', 0);

        // Rate limiting por IP
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $lockFile= ROOT_PATH . '/data/eu_' . md5($ip) . '.tmp';
        $now     = time();
        $data    = file_exists($lockFile) ? (json_decode(file_get_contents($lockFile), true) ?? ['c'=>0,'t'=>$now]) : ['c'=>0,'t'=>$now];
        if (($now - $data['t']) >= 300) { $data = ['c'=>0,'t'=>$now]; }
        if ($data['c'] >= 5) {
            echo json_encode(['success'=>false,'error'=>'Demasiados intentos. Espera '.max(0,300-($now-$data['t'])).'s.']); exit();
        }

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $pass   = $body['unlock_password'] ?? '';

        $data['c']++;
        file_put_contents($lockFile, json_encode($data), LOCK_EX);

        $emergencyFile = ROOT_PATH . '/data/emergency.dat';
        if (!file_exists($emergencyFile)) {
            echo json_encode(['success'=>false,'error'=>'Acceso técnico no configurado.']); exit();
        }
        $stored = json_decode(file_get_contents($emergencyFile), true);
        if (!isset($stored['password_hash'])) {
            echo json_encode(['success'=>false,'error'=>'Archivo corrupto.']); exit();
        }

        // Verificar con la misma contraseña de emergencia
        if (!password_verify($pass . ($stored['pepper'] ?? ''), $stored['password_hash'])) {
            echo json_encode(['success'=>false,'error'=>'Contraseña incorrecta.']); exit();
        }

        @unlink($lockFile);
        echo json_encode(['success'=>true]);
        exit();
    }

    /**
     * POST /api/setup-emergency
     * Solo accesible desde el panel admin (con BD activa).
     * Genera/actualiza las credenciales de emergencia.
     */
    public function setupEmergency() {
        header('Content-Type: application/json');

        if (!SessionManager::estaLogueado() || ($_SESSION['user_rol'] ?? '') !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']); exit();
        }

        // Solo el admin que NO sea junior puede hacer esto
        if (!empty($_SESSION['emergency_mode'])) {
            echo json_encode(['success' => false, 'error' => 'No disponible en modo emergencia']); exit();
        }

        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        if (strlen($username) < 3) { echo json_encode(['success' => false, 'error' => 'Usuario mínimo 3 caracteres']); exit(); }
        if (strlen($password) < 10) { echo json_encode(['success' => false, 'error' => 'Contraseña mínimo 10 caracteres']); exit(); }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
            echo json_encode(['success' => false, 'error' => 'La contraseña debe tener mayúscula, número y símbolo']); exit();
        }

        $salt   = bin2hex(random_bytes(16));
        $pepper = bin2hex(random_bytes(16));

        $stored = [
            'username_hash' => hash('sha256', $username . $salt),
            'password_hash' => password_hash($password . $pepper, PASSWORD_ARGON2ID ?? PASSWORD_BCRYPT, ['cost' => 12]),
            'salt'          => $salt,
            'pepper'        => $pepper,
            'created_at'    => date('Y-m-d H:i:s'),
            'created_by'    => $_SESSION['user_id'] ?? 0,
        ];

        $dir = ROOT_PATH . '/data';
        if (!is_dir($dir)) mkdir($dir, 0700, true);

        if (file_put_contents($dir . '/emergency.dat', json_encode($stored), LOCK_EX) === false) {
            echo json_encode(['success' => false, 'error' => 'No se pudo escribir el archivo de emergencia. Verifica permisos de /data/']); exit();
        }

        // Asegurar que el archivo no sea legible por otros usuarios del servidor
        chmod($dir . '/emergency.dat', 0600);

        $this->logEmergency($_SERVER['REMOTE_ADDR'] ?? 'unknown', 'SETUP', $_SESSION['user_username'] ?? 'admin');
        echo json_encode(['success' => true, 'message' => 'Credenciales de emergencia configuradas correctamente.']);
        exit();
    }

    /** Escribe en el log de accesos de emergencia */
    private function logEmergency(string $ip, string $event, string $username): void {
        $logFile = ROOT_PATH . '/data/emergency_log.txt';
        $line    = date('Y-m-d H:i:s') . " | {$event} | IP:{$ip} | USER:{$username}\n";
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        // Rotar log si supera 100KB
        if (file_exists($logFile) && filesize($logFile) > 102400) {
            rename($logFile, $logFile . '.bak');
        }
    }
}
?>
