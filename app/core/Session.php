<?php
/**
 * app/core/Session.php — Gestión de sesiones (núcleo MVC)
 *
 * Roles: alumno | maestro | admin
 * - alumno  → /dashboard
 * - maestro → /maestro
 * - admin   → /admin
 *
 * Soporta sesiones de emergencia (sin BD) iniciadas por AuthController::emergencyLogin().
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class SessionManager {

    /**
     * Normaliza claves de sesión de emergencia al formato estándar.
     * La sesión de emergencia usa 'user_rol'/'user_nombre' por separación
     * explícita; aquí las unificamos para que el resto del código funcione igual.
     */
    private static function normalizarEmergencia(): void {
        if (!empty($_SESSION['emergency_mode'])) {
            // Verificar expiración de sesión de emergencia
            if (!empty($_SESSION['emergency_expires']) && time() > $_SESSION['emergency_expires']) {
                session_destroy();
                session_start();
                return;
            }
            // Normalizar claves si aún no se ha hecho
            if (!isset($_SESSION['rol'])    && isset($_SESSION['user_rol']))    $_SESSION['rol']    = $_SESSION['user_rol'];
            if (!isset($_SESSION['nombre']) && isset($_SESSION['user_nombre'])) $_SESSION['nombre'] = $_SESSION['user_nombre'];
            if (!isset($_SESSION['username']) && isset($_SESSION['user_username'])) $_SESSION['username'] = $_SESSION['user_username'];
        }
    }

    public static function iniciarSesion($usuario) {
        $_SESSION['user_id']    = $usuario['id'];
        $_SESSION['username']   = $usuario['username'];
        $_SESSION['nombre']     = $usuario['nombre_completo'];
        $_SESSION['email']      = $usuario['email'];
        $_SESSION['rol']        = $usuario['rol'] ?? 'alumno';
        $_SESSION['login_time'] = time();
    }

    /** Devuelve true si hay sesión válida (normal o de emergencia) */
    public static function estaLogueado(): bool {
        self::normalizarEmergencia();
        return isset($_SESSION['user_id']);
    }

    /** Devuelve true si la sesión actual es de emergencia (sin BD) */
    public static function esEmergencia(): bool {
        return !empty($_SESSION['emergency_mode']);
    }

    /** Segundos restantes de la sesión de emergencia, 0 si no aplica */
    public static function minutosEmergenciaRestantes(): int {
        if (empty($_SESSION['emergency_expires'])) return 0;
        return max(0, (int)(($_SESSION['emergency_expires'] - time()) / 60));
    }

    public static function usuarioId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function usuarioNombre() {
        return $_SESSION['nombre'] ?? 'Invitado';
    }

    public static function usuarioRol() {
        return $_SESSION['rol'] ?? 'alumno';
    }

    public static function esAdmin() {
        return ($_SESSION['rol'] ?? '') === 'admin';
    }

    public static function esMaestro() {
        return in_array($_SESSION['rol'] ?? '', ['maestro', 'admin']);
    }

    public static function esAlumno() {
        return ($_SESSION['rol'] ?? 'alumno') === 'alumno';
    }

    /** Redirige al panel correcto según el rol */
    public static function redirigirPorRol() {
        $destinos = [
            'admin'   => BASE_URL . '/admin',
            'maestro' => BASE_URL . '/maestro',
            'alumno'  => BASE_URL . '/dashboard',
        ];
        $rol  = $_SESSION['rol'] ?? 'alumno';
        $dest = $destinos[$rol] ?? BASE_URL . '/dashboard';
        header("Location: $dest");
        exit();
    }

    /** Verifica acceso y redirige a login si no está logueado */
    private static function esSolicitudApi(): bool {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
        $xhr = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
        // Soportar BASE_URL con subdirectorio (ej: /Proyectos/DiagramasMVC/api/...)
        $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        $pathRelativo = ($basePath && str_starts_with($path, $basePath))
            ? substr($path, strlen($basePath))
            : $path;
        return str_starts_with($path, '/api/') || str_starts_with($pathRelativo, '/api/')
            || str_contains($accept, 'application/json') || $xhr;
    }

    public static function verificarAcceso() {
        if (!self::estaLogueado()) {
            if (self::esSolicitudApi()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                exit();
            }
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    /** Verifica que sea alumno, redirige si no */
    public static function verificarAlumno() {
        self::verificarAcceso();
        if (!self::esAlumno()) {
            if (self::esSolicitudApi()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
                exit();
            }
            self::redirigirPorRol();
        }
    }

    /** Verifica que sea maestro o admin, redirige si no */
    public static function verificarMaestro() {
        self::verificarAcceso();
        if (!self::esMaestro()) {
            if (self::esSolicitudApi()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
                exit();
            }
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }

    /** Verifica que sea admin, redirige si no */
    public static function verificarAdmin() {
        self::verificarAcceso();
        if (!self::esAdmin()) {
            if (self::esSolicitudApi()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
                exit();
            }
            self::redirigirPorRol();
        }
    }

    public static function cerrarSesion() {
        session_destroy();
    }
}
?>
