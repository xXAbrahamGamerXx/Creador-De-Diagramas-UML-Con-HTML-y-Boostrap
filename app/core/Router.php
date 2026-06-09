<?php
/**
 * app/core/Router.php — Enrutador del sistema
 */
class Router {

    private array $rutas = [];

    public function get(string $ruta, string $controlador, string $metodo): void {
        $this->rutas['GET'][$ruta] = [$controlador, $metodo];
    }

    public function post(string $ruta, string $controlador, string $metodo): void {
        $this->rutas['POST'][$ruta] = [$controlador, $metodo];
    }

    public function despachar(): void {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Eliminar el prefijo BASE_URL si existe
        $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Quitar /index.php del inicio si viene así (acceso directo en local)
        $uri = preg_replace('#^/index\.php#', '', $uri);

        // Normalizar: siempre con / al inicio, sin / al final (excepto '/')
        $uri = '/' . ltrim($uri, '/');
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Si queda vacío (ej: accedieron a /index.php sin nada más), tratar como '/'
        if ($uri === '' || $uri === '/index.php') {
            $uri = '/';
        }

        if (isset($this->rutas[$metodo][$uri])) {
            [$clase, $accion] = $this->rutas[$metodo][$uri];
            $controlador = new $clase();
            $controlador->$accion();
        } else {
            http_response_code(404);
            echo "<h1>404 - Página no encontrada</h1><p>Ruta: $uri</p>";
        }
    }
}
?>
