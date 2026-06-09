<?php
/**
 * app/bootstrap.php — Arranque del sistema MVC
 *
 * Define constantes globales y carga todas las clases del núcleo,
 * modelos y controladores mediante autoload manual.
 *
 * Constantes disponibles en todo el proyecto:
 *   ROOT_PATH   → Ruta absoluta a la raíz del proyecto  (/ruta/DiagramasMVC)
 *   APP_PATH    → Ruta absoluta a app/
 *   PUBLIC_PATH → Ruta absoluta a public/
 *   BASE_URL    → URL base del sitio (sin trailing slash)
 */

// ── PHP config override para archivos grandes ───────────────────────────
@ini_set('memory_limit', '256M');
@ini_set('max_execution_time', '60');

// ── Constantes de ruta ────────────────────────────────────────────────
define('ROOT_PATH',   dirname(__DIR__));
define('BASE_PATH',   ROOT_PATH);
define('APP_PATH',    ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// ── BASE_URL — detectada automáticamente ─────────────────────────────
// Detecta si el proyecto está en la raíz o en una subcarpeta (incluso anidada).
// Ejemplos:
//   http://localhost/Proyectos/DiagramasMVC → BASE_URL = 'http://localhost/Proyectos/DiagramasMVC'
//   http://localhost/DiagramasMVC           → BASE_URL = 'http://localhost/DiagramasMVC'
//   http://localhost                        → BASE_URL = 'http://localhost'
//
// Si la detección automática falla, descomenta y ajusta la línea siguiente:
// define('BASE_URL', 'http://localhost/Proyectos/DiagramasMVC');

if (!defined('BASE_URL')) {
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $selfDir = rtrim(str_replace('\\', '/', realpath(ROOT_PATH)), '/');
    $subPath = str_replace($docRoot, '', $selfDir);
    define('BASE_URL', $scheme . '://' . $host . $subPath);
}

// ── Iniciar sesión ────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Autoloader manual ─────────────────────────────────────────────────
// Orden de carga: core → models → controllers
$clases = [
    // Núcleo
    APP_PATH . '/core/Database.php',
    APP_PATH . '/core/Session.php',
    APP_PATH . '/core/FileManager.php',
    APP_PATH . '/core/Model.php',
    APP_PATH . '/core/Controller.php',
    APP_PATH . '/core/Router.php',
    APP_PATH . '/core/Assets.php',
    // Modelos
    APP_PATH . '/models/UserModel.php',
    APP_PATH . '/models/DiagramModel.php',
    // Controladores
    APP_PATH . '/controllers/AuthController.php',
    APP_PATH . '/controllers/DashboardController.php',
    APP_PATH . '/controllers/EditorController.php',
    APP_PATH . '/controllers/MaestroController.php',
    APP_PATH . '/controllers/AdminController.php',
    APP_PATH . '/controllers/AlumnoController.php',
    APP_PATH . '/controllers/ProyectoController.php',
    APP_PATH . '/controllers/NotificacionController.php',
    APP_PATH . '/controllers/ChatController.php',
];

foreach ($clases as $archivo) {
    if (file_exists($archivo)) {
        require_once $archivo;
    } else {
        error_log("Bootstrap: archivo no encontrado — $archivo");
    }
}

// ── Inicializar gestor de assets (offline/online) ─────────────────────────
if (class_exists('Assets')) {
    Assets::init();
}
?>
