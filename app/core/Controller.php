<?php
/**
 * app/core/Controller.php — Controlador base
 *
 * Todos los controladores extienden esta clase.
 * Proporciona métodos comunes: renderizar vistas, responder JSON,
 * redirigir y acceder a la sesión.
 */
class Controller {

    /**
     * Renderiza una vista pasándole variables.
     *
     * @param string $vista   Ruta relativa dentro de app/views/ (sin .php)
     *                        Ej: 'auth/login', 'dashboard/index'
     * @param array  $datos   Variables que estarán disponibles en la vista
     */
    protected function render(string $vista, array $datos = []) {
        // Extrae las variables para que la vista las use directamente
        extract($datos);
        $rutaVista = APP_PATH . '/views/' . $vista . '.php';
        if (!file_exists($rutaVista)) {
            http_response_code(404);
            die("Vista no encontrada: $vista");
        }
        require $rutaVista;
    }

    /**
     * Envía una respuesta JSON y termina la ejecución.
     *
     * @param array $datos
     * @param int   $httpCode
     */
    protected function json(array $datos, int $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Redirige a una URL (relativa a BASE_URL o absoluta).
     *
     * @param string $url
     */
    protected function redirigir(string $url) {
        // Si no empieza con http, añadir BASE_URL
        if (strpos($url, 'http') !== 0) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header("Location: $url");
        exit();
    }

    /** Obtiene el cuerpo JSON del request */
    protected function getJsonInput(): array {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    /** Verifica que el método HTTP sea POST */
    protected function soloPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Método no permitido'], 405);
        }
    }

    /** Verifica que el método HTTP sea GET */
    protected function soloGet() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['success' => false, 'error' => 'Método no permitido'], 405);
        }
    }
}
?>
