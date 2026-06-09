<?php
/**
 * Vista: Editor API
 * URL: /editor-api[?id=X][?tipo=usecase]
 * Uso externo: integra el editor en otras aplicaciones vía iframe o acceso directo.
 * Muestra panel lateral con listado de diagramas del usuario y los iconos SVG de cada tipo.
 */
if (!defined('BASE_URL')) exit('Acceso denegado');
$uid      = SessionManager::usuarioId();
$diagId   = $diagrama_id ?? null;
$tipoDiag = $tipo_diagrama ?? 'usecase';
?>
<!DOCTYPE html>
<html lang="es" class="dark-mode">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Editor API — DiagramasUML</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/editor.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{display:flex;height:100vh;overflow:hidden;font-family:'Inter',system-ui,sans-serif;background:#0d0d1a;color:#e2e8f0}
.api-sidebar{width:240px;min-width:200px;background:#12122a;border-right:1px solid #2a2a4a;display:flex;flex-direction:column;overflow:hidden}
.api-sidebar-header{padding:14px 16px;border-bottom:1px solid #2a2a4a;display:flex;align-items:center;gap:10px}
.api-sidebar-header h2{font-size:13px;font-weight:600;color:#a5b4fc;letter-spacing:.04em}
.api-sidebar-body{overflow-y:auto;flex:1;padding:8px 0}
.diag-item{display:flex;align-items:center;gap:9px;padding:8px 14px;cursor:pointer;border-left:3px solid transparent;transition:all .15s}
.diag-item:hover{background:rgba(102,126,234,.12)}
.diag-item.active{background:rgba(102,126,234,.18);border-left-color:#667eea}
.diag-item .diag-icon{width:28px;height:28px;flex-shrink:0}
.diag-item .diag-info{flex:1;min-width:0}
.diag-item .diag-title{font-size:.8rem;font-weight:600;color:#e2e8f0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.diag-item .diag-tipo{font-size:.68rem;color:#8899bb;margin-top:1px}
.section-label{font-size:.65rem;font-weight:700;color:#4a5568;letter-spacing:.08em;text-transform:uppercase;padding:10px 14px 4px}
.editor-main{flex:1;overflow:hidden;display:flex;flex-direction:column}
.api-topbar{height:44px;background:#12122a;border-bottom:1px solid #2a2a4a;display:flex;align-items:center;gap:10px;padding:0 14px;flex-shrink:0}
.api-topbar h3{font-size:.88rem;font-weight:600;color:#e2e8f0;flex:1}
.api-topbar .tag{font-size:.68rem;padding:2px 8px;border-radius:10px;background:rgba(102,126,234,.2);color:#a5b4fc}
#editorContainer{flex:1;overflow:hidden}
.new-btn{display:flex;align-items:center;gap:6px;margin:10px 10px 4px;padding:8px 12px;background:linear-gradient(135deg,#667eea,#764ba2);border:none;border-radius:8px;color:#fff;font-size:.78rem;font-weight:600;cursor:pointer;width:calc(100% - 20px);justify-content:center}
.new-btn:hover{opacity:.9}
</style>
</head>
<body>

<nav class="api-sidebar">
    <div class="api-sidebar-header">
        <img src="<?= BASE_URL ?>/public/assets/img/iconos-uml/overview.svg" width="22" height="22" alt="">
        <h2>Editor API</h2>
    </div>
    <div class="api-sidebar-body">
        <button class="new-btn" onclick="crearNuevoDiagramaApi()">
            <i class="bi bi-plus-lg"></i> Nuevo diagrama
        </button>
        <div class="section-label">Mis diagramas</div>
        <div id="diagList"><div style="padding:16px 14px;font-size:.78rem;color:#8899bb">Cargando…</div></div>
    </div>
</nav>

<div class="editor-main">
    <div class="api-topbar">
        <div id="currentTitle" class="api-topbar" style="all:unset;display:flex;align-items:center;gap:8px;flex:1">
            <span id="topTitle" style="font-size:.88rem;font-weight:600;color:#e2e8f0">
                <?= htmlspecialchars($diagrama_data['titulo'] ?? 'Nuevo diagrama') ?>
            </span>
            <span id="topTipo" class="tag">
                <?= htmlspecialchars($tipoDiag) ?>
            </span>
        </div>
        <a href="<?= BASE_URL ?>/dashboard" style="font-size:.75rem;color:#8899bb;text-decoration:none">
            <i class="bi bi-house me-1"></i>Inicio
        </a>
    </div>
    <div id="editorContainer">
        <!-- The editor iframe loads the standard editor -->
        <iframe id="editorFrame"
            src="<?= BASE_URL ?>/editor<?= $diagId ? '?id='.$diagId : '?tipo='.$tipoDiag ?>"
            style="width:100%;height:100%;border:none"
            allow="fullscreen">
        </iframe>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const ICON_BASE = BASE_URL + '/public/assets/img/iconos-uml/';
const TIPO_LABELS = {
    usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',
    activity:'Actividades', state:'Máquina de Estado', component:'Componentes',
    deployment:'Despliegue', object:'Objetos', communication:'Comunicación',
    timing:'Tiempos', package:'Paquetes', composite:'Estructura Compuesta',
    profile:'Perfiles', overview:'Descripción General'
};
const TIPO_GROUPS = {
    'Estructurales': ['class','object','package','composite','component','deployment','profile'],
    'Comportamiento': ['usecase','activity','state'],
    'Interacción': ['sequence','communication','timing','overview']
};

let _diagramas = [];
let _activoId = <?= $diagId ? (int)$diagId : 'null' ?>;

async function cargarDiagramas() {
    try {
        const r = await fetch(BASE_URL + '/api/diagramas?filtro=&pagina=1');
        const d = await r.json();
        _diagramas = d.diagramas || [];
        renderListado();
    } catch(e) {
        document.getElementById('diagList').innerHTML =
            '<div style="padding:12px 14px;font-size:.75rem;color:#ef4444">Error al cargar</div>';
    }
}

function iconSVG(tipo, size=26) {
    return `<img src="${ICON_BASE}${tipo}.svg" width="${size}" height="${size}" style="object-fit:contain">`;
}

function renderListado() {
    const list = document.getElementById('diagList');
    if (!_diagramas.length) {
        list.innerHTML = '<div style="padding:14px;font-size:.78rem;color:#8899bb">Sin diagramas</div>';
        return;
    }
    const byTipo = {};
    _diagramas.forEach(d => {
        if (!byTipo[d.tipo_diagrama]) byTipo[d.tipo_diagrama] = [];
        byTipo[d.tipo_diagrama].push(d);
    });
    let html = '';
    Object.entries(TIPO_GROUPS).forEach(([grupo, tipos]) => {
        const diags = tipos.flatMap(t => byTipo[t] || []);
        if (!diags.length) return;
        html += `<div class="section-label">${grupo}</div>`;
        diags.forEach(d => {
            const active = d.id === _activoId ? ' active' : '';
            html += `<div class="diag-item${active}" onclick="abrirDiagrama(${d.id},'${escHtml(d.titulo)}','${d.tipo_diagrama}')">
                <div class="diag-icon">${iconSVG(d.tipo_diagrama, 26)}</div>
                <div class="diag-info">
                    <div class="diag-title" title="${escHtml(d.titulo)}">${escHtml(d.titulo)}</div>
                    <div class="diag-tipo">${TIPO_LABELS[d.tipo_diagrama]||d.tipo_diagrama}</div>
                </div>
            </div>`;
        });
    });
    list.innerHTML = html;
}

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function abrirDiagrama(id, titulo, tipo) {
    _activoId = id;
    document.getElementById('topTitle').textContent = titulo;
    document.getElementById('topTipo').textContent = TIPO_LABELS[tipo]||tipo;
    document.getElementById('editorFrame').src = BASE_URL + '/editor?id=' + id;
    renderListado();
}

function crearNuevoDiagramaApi() {
    const tipo = prompt('Tipo de diagrama:\n' + Object.values(TIPO_LABELS).join(', ').substring(0,200) + '\n\nEscribe el tipo (ej: class, usecase, sequence):') || 'usecase';
    document.getElementById('editorFrame').src = BASE_URL + '/editor?tipo=' + tipo.trim();
    document.getElementById('topTitle').textContent = 'Nuevo diagrama';
    document.getElementById('topTipo').textContent = TIPO_LABELS[tipo]||tipo;
    _activoId = null;
}

cargarDiagramas();
</script>
</body>
</html>
