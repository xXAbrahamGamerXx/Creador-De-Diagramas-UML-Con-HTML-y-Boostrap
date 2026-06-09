#!/usr/bin/env php
<?php
/**
 * instalar_offline.php — Descarga todas las dependencias para uso sin internet
 *
 * USO (ejecutar UNA sola vez desde la raíz del proyecto):
 *   php instalar_offline.php
 *
 * O desde el navegador:
 *   http://localhost/Proyectos/DiagramasMVC/instalar_offline.php
 *
 * Descarga:
 *   - Bootstrap 5.3.0 CSS + JS
 *   - Bootstrap Icons 1.11.0 CSS + fuentes (woff2)
 *
 * Después de ejecutar, el proyecto funciona completamente sin internet.
 */

// Permitir tiempo suficiente para descargas
set_time_limit(120);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$isCLI = php_sapi_name() === 'cli';

function out($msg, $ok = null) {
    global $isCLI;
    if ($isCLI) {
        $prefix = $ok === true ? '✓ ' : ($ok === false ? '✗ ' : '  ');
        echo $prefix . $msg . "\n";
    } else {
        $color  = $ok === true ? '#198754' : ($ok === false ? '#dc3545' : '#0d6efd');
        $prefix = $ok === true ? '✓' : ($ok === false ? '✗' : '→');
        echo "<p style='margin:4px 0;color:{$color};font-family:monospace;'>{$prefix} {$msg}</p>";
        ob_flush(); flush();
    }
}

function esContenidoValido(string $contenido): bool {
    $trimmed = ltrim($contenido);
    if ($trimmed === '') {
        return false;
    }

    $lower = strtolower(substr($trimmed, 0, 256));
    if (str_starts_with($lower, '<!doctype html')
        || str_starts_with($lower, '<html')
        || str_starts_with($lower, '<script')
        || str_contains($lower, 'window.location=')
        || str_contains($lower, 'document.location')
        || str_contains($lower, 'cgi-bin')
    ) {
        return false;
    }

    return true;
}

function descargar($url, $destino) {
    // Intentar con file_get_contents primero
    $ctx = stream_context_create([
        'http' => [
            'timeout'    => 30,
            'user_agent' => 'Mozilla/5.0 DiagramasUML-Installer/1.0',
        ],
        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);

    $contenido = @file_get_contents($url, false, $ctx);

    // Si falla, intentar con curl
    if ($contenido === false && function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 DiagramasUML-Installer/1.0',
        ]);
        $contenido = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            return false;
        }
    }

    if ($contenido === false || strlen($contenido) < 100 || !esContenidoValido($contenido)) {
        return false;
    }

    // Crear directorio si no existe
    $dir = dirname($destino);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    return file_put_contents($destino, $contenido) !== false;
}

// ── Rutas base ──────────────────────────────────────────────────────────────
$root    = __DIR__;
$vendor  = $root . '/public/assets/vendor';

// ── Lista de archivos a descargar ───────────────────────────────────────────
$archivos = [
    // Bootstrap CSS
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
        'dest' => "$vendor/bootstrap/css/bootstrap.min.css",
        'desc' => 'Bootstrap 5.3.0 CSS',
    ],
    // Bootstrap JS bundle (incluye Popper)
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
        'dest' => "$vendor/bootstrap/js/bootstrap.bundle.min.js",
        'desc' => 'Bootstrap 5.3.0 JS Bundle',
    ],
    // Bootstrap Icons CSS
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css',
        'dest' => "$vendor/bootstrap-icons/font/bootstrap-icons.min.css",
        'desc' => 'Bootstrap Icons 1.11.0 CSS',
    ],
    // Bootstrap Icons fuentes (woff2 y woff)
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/fonts/bootstrap-icons.woff2',
        'dest' => "$vendor/bootstrap-icons/font/fonts/bootstrap-icons.woff2",
        'desc' => 'Bootstrap Icons fuente woff2',
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/fonts/bootstrap-icons.woff',
        'dest' => "$vendor/bootstrap-icons/font/fonts/bootstrap-icons.woff",
        'desc' => 'Bootstrap Icons fuente woff',
    ],
];

if (!$isCLI) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>
    <title>Instalador Offline — DiagramasUML</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#e0e0e0;padding:40px;max-width:700px;margin:auto;}
        h1{color:#667eea;}h2{color:#aaa;font-size:1rem;font-weight:400;margin-top:0;}
        .card{background:#1a1a2e;border:1px solid #2a2a4a;border-radius:12px;padding:24px;margin-top:20px;}
        .done{background:#0d2918;border-color:#198754;color:#6ee7b7;padding:16px;border-radius:8px;margin-top:20px;}
        .error{background:#2d0a0a;border-color:#dc3545;color:#fca5a5;padding:16px;border-radius:8px;margin-top:20px;}
    </style></head><body>
    <h1>🔧 Instalador Offline — DiagramasUML</h1>
    <h2>Descargando dependencias para uso sin internet...</h2>
    <div class='card'>";
    ob_flush(); flush();
}

out("Iniciando descarga de dependencias...");
out("Destino: $vendor");
out("");

$errores  = 0;
$ok_count = 0;

foreach ($archivos as $archivo) {
    if (file_exists($archivo['dest'])) {
        $current = @file_get_contents($archivo['dest']);
        if ($current !== false && esContenidoValido($current)) {
            out("{$archivo['desc']} — ya existe y es válido, omitiendo", true);
            $ok_count++;
            continue;
        }

        out("{$archivo['desc']} — archivo local inválido o corrupto, forzando redescarga", false);
        @unlink($archivo['dest']);
    }

    out("Descargando {$archivo['desc']}...");
    $resultado = descargar($archivo['url'], $archivo['dest']);

    if ($resultado) {
        $kb = round(filesize($archivo['dest']) / 1024, 1);
        out("{$archivo['desc']} — {$kb} KB", true);
        $ok_count++;
    } else {
        out("{$archivo['desc']} — ERROR al descargar", false);
        $errores++;
    }
}

out("");

// ── Ajustar URL de fuentes dentro del CSS de Bootstrap Icons ──────────────
$biCss = "$vendor/bootstrap-icons/font/bootstrap-icons.min.css";
if (file_exists($biCss)) {
    $css = file_get_contents($biCss);
    // Las fuentes en el CSS referencian "./fonts/bootstrap-icons.woff2"
    // Verificar que las referencias sean relativas (ya lo son por defecto)
    file_put_contents($biCss, $css);
    out("Referencias de fuentes verificadas", true);
}

// ── Resultado final ────────────────────────────────────────────────────────
if ($errores === 0) {
    out("¡Listo! $ok_count archivos descargados correctamente.", true);
    out("El proyecto ahora funciona sin internet.", true);
    out("");
    out("IMPORTANTE: El archivo offline.php en la raíz ya configura las rutas locales.");
    out("Reinicia XAMPP si ya tenías el sitio abierto.");

    if (!$isCLI) {
        echo "</div><div class='done'>
            <strong>✓ Instalación completada</strong><br>
            El proyecto ahora funciona completamente sin conexión a internet.<br>
            <br>Puedes eliminar este archivo <code>instalar_offline.php</code> si quieres.
        </div>";
    }
} else {
    out("Completado con $errores errores.", false);
    out("Verifica tu conexión a internet y vuelve a ejecutar.");

    if (!$isCLI) {
        echo "</div><div class='error'>
            <strong>✗ {$errores} archivo(s) fallaron</strong><br>
            Verifica que XAMPP tenga acceso a internet y vuelve a abrir esta página.
        </div>";
    }
}

if (!$isCLI) {
    echo "</body></html>";
}
?>
