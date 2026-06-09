<?php
/**
 * app/core/Assets.php — Gestor de assets offline/online
 *
 * Detecta si las dependencias locales están instaladas.
 * Si sí → sirve archivos locales (funciona sin internet).
 * Si no → cae de vuelta al CDN (requiere internet).
 *
 * Para instalar las dependencias locales, ejecuta UNA VEZ:
 *   php instalar_offline.php
 * O abre en el navegador:
 *   http://localhost/Proyectos/DiagramasMVC/instalar_offline.php
 */
class Assets {

    /** Ruta física de la carpeta vendor */
    private static string $vendorPath = '';

    /** URL pública de la carpeta vendor */
    private static string $vendorUrl  = '';

    /** Indica si los assets locales están disponibles */
    private static ?bool $localOk = null;

    public static function init(): void {
        self::$vendorPath = PUBLIC_PATH . '/assets/vendor';
        self::$vendorUrl  = BASE_URL   . '/public/assets/vendor';
    }

    /** Verifica que un archivo local sea válido y no esté corrompido */
    private static function archivoValido(string $path): bool {
        if (!file_exists($path)) {
            return false;
        }

        $size = filesize($path);
        if ($size === false || $size < 3000) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        $header = strtolower(fread($handle, 256));
        fclose($handle);

        if (str_starts_with($header, '<!doctype html')
            || str_starts_with($header, '<html')
            || str_starts_with($header, '<script')
            || str_contains($header, 'window.location=')
            || str_contains($header, 'document.location')
        ) {
            return false;
        }

        return true;
    }

    /** Devuelve true si los assets locales están instalados.
     * Se cachea en la primera llamada.
     */
    public static function localDisponible(): bool {
        if (self::$localOk !== null) return self::$localOk;

        self::$localOk = self::archivoValido(self::$vendorPath . '/bootstrap/css/bootstrap.min.css')
                      && self::archivoValido(self::$vendorPath . '/bootstrap/js/bootstrap.bundle.min.js')
                      && self::archivoValido(self::$vendorPath . '/bootstrap-icons/font/bootstrap-icons.min.css');

        return self::$localOk;
    }

    /** URL del CSS de Bootstrap */
    public static function bootstrapCss(): string {
        if (self::localDisponible()) {
            return self::$vendorUrl . '/bootstrap/css/bootstrap.min.css';
        }
        return 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
    }

    /** URL del JS Bundle de Bootstrap */
    public static function bootstrapJs(): string {
        if (self::localDisponible()) {
            return self::$vendorUrl . '/bootstrap/js/bootstrap.bundle.min.js';
        }
        return 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
    }

    /** URL del CSS de Bootstrap Icons */
    public static function bootstrapIcons(): string {
        if (self::localDisponible()) {
            return self::$vendorUrl . '/bootstrap-icons/font/bootstrap-icons.min.css';
        }
        return 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css';
    }

    /** URL de un asset propio del proyecto */
    public static function url(string $path): string {
        return BASE_URL . '/public/assets/' . ltrim($path, '/');
    }
}
?>
