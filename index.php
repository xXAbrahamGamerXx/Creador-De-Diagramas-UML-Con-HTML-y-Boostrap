<?php
/**
 * index.php — Punto de entrada único (Front Controller)
 *
 * Todo el tráfico HTTP pasa por este archivo gracias al .htaccess.
 * Carga el bootstrap, registra las rutas y despacha la petición.
 *
 * Flujo:
 *   1. bootstrap.php  → define constantes, inicia sesión, carga clases
 *   2. routes.php     → registra rutas en el Router
 *   3. Router::despachar() → instancia el controlador y llama al método
 */

// Suprimir errores PHP en la respuesta (se loguean en error_log)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Handler de último recurso para errores fatales no capturados
set_exception_handler(function(Throwable $e) {
    error_log('UNCAUGHT EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    $isApi = (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false)
          || (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest')
          || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    if ($isApi) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error 500</title></head><body>';
        echo '<h2 style="font-family:sans-serif;color:#c00">Error interno del servidor</h2>';
        echo '<p style="font-family:sans-serif">El sistema encontró un problema. Revisa la conexión a la base de datos en el Panel de Administración.</p>';
        echo '</body></html>';
    }
    exit();
});

// Cargar bootstrap (constantes + clases)
require_once __DIR__ . '/app/bootstrap.php';

// Registrar rutas y despachar
$router = require_once __DIR__ . '/app/routes.php';
$router->despachar();
?>
