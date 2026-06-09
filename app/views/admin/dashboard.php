<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de Trabajo — Admin — DiagramasUML</title>
<link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
<style>
:root {
    --primary:     #667eea;
    --primary2:    #764ba2;
    --primary-rgb: 102,126,234;
    --sidebar:     265px;
    --bg-deep:     #0d0d1a;
    --bg-card:     #1a1a2e;
    --bg-hover:    #13132a;
    --bd-color:    #2a2a4a;
    --txt-main:    #e8eaff;
    --txt-muted:   #8888aa;
    --c-success:   #10b981;
    --c-danger:    #ef4444;
    --c-warning:   #f59e0b;
    --c-info:      #3b82f6;
}
* { box-sizing:border-box; }
body { background:var(--bg-deep); color:#e0e0e0; font-family:'Segoe UI',sans-serif; margin:0; display:flex; height:100vh; overflow:hidden; }
body.light-theme { --bg-deep:#f0f2f8; --bg-card:#fff; --bg-hover:#f8f9ff; --bd-color:#e8eaf0; --txt-main:#1a1a2e; --txt-muted:#666; color:#1e1e2e; }

/* ── Sidebar ───────────────────────────────────────────── */
.sidebar { width:var(--sidebar); min-width:var(--sidebar); background:var(--bg-card); border-right:1px solid var(--bd-color); display:flex; flex-direction:column; height:100vh; overflow-y:auto; }
.sidebar-brand { padding:18px 20px 12px; border-bottom:1px solid var(--bd-color); }
.sidebar-brand h4 { margin:0; font-size:1rem; font-weight:700; color:var(--primary); display:flex; align-items:center; gap:8px; }
.sidebar-brand small { color:var(--txt-muted); font-size:.72rem; }
.admin-badge { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; border-radius:6px; padding:2px 8px; font-size:.63rem; font-weight:700; letter-spacing:.05em; margin-left:auto; }
.sidebar-user { padding:14px 20px; border-bottom:1px solid var(--bd-color); display:flex; align-items:center; gap:10px; }
.avatar { width:38px; height:38px; background:linear-gradient(135deg,var(--primary),var(--primary2)); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1rem; flex-shrink:0; }
.user-info { min-width:0; }
.user-name { font-weight:600; font-size:.85rem; color:var(--txt-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.user-role { font-size:.7rem; color:var(--c-danger); font-weight:600; }
nav { flex:1; padding:8px 0; }
.nav-section { padding:12px 18px 4px; font-size:.67rem; text-transform:uppercase; letter-spacing:.1em; color:var(--txt-muted); opacity:.6; }
.nav-btn { display:flex; align-items:center; gap:10px; width:100%; padding:9px 18px; background:none; border:none; color:rgba(255,255,255,.6); font-size:.84rem; cursor:pointer; transition:all .18s; text-align:left; border-left:3px solid transparent; text-decoration:none; }
body.light-theme .nav-btn { color:rgba(30,30,46,.7); }
body.light-theme [style*="color:rgba(255,255,255"] { color:#1e1e2e !important; }
body.light-theme .sidebar-footer .nav-btn,
body.light-theme .sidebar-footer a,
body.light-theme .sidebar-footer button { color:#1e1e2e !important; }
body.light-theme .text-muted,
body.light-theme .form-text,
body.light-theme small,
body.light-theme .empty-state p { color:#5d5d5d !important; }
body.light-theme .form-label,
body.light-theme .page-header h2,
body.light-theme .t th,
body.light-theme .t td,
body.light-theme .modal-content,
body.light-theme .modal-body .form-control,
body.light-theme .modal-body .form-select { color:#1d1d28 !important; }
body.light-theme .modal-header .btn-close { filter:none; }
body.light-theme .btn-outline-a { color:var(--primary) !important; border-color:rgba(var(--primary-rgb),.35) !important; }
body.light-theme .btn-outline-a:hover { background:rgba(var(--primary-rgb),.12) !important; }
body.light-theme .btn-primary-a { color:#fff !important; }
.nav-btn:hover { background:rgba(var(--primary-rgb),.1); color:var(--txt-main); }
.nav-btn.active { background:rgba(var(--primary-rgb),.18); color:var(--primary); border-left-color:var(--primary); font-weight:600; }
.nav-btn i { width:18px; text-align:center; font-size:.95rem; }
.nav-btn.danger-btn { color:rgba(239,68,68,.7); }
.nav-btn.danger-btn:hover { background:rgba(239,68,68,.1); color:#ef4444; }
.sidebar-footer { padding:12px 0; border-top:1px solid var(--bd-color); }

/* ── Main ─────────────────────────────────────────────── */
.main { flex:1; display:flex; flex-direction:column; height:100vh; overflow:hidden; }
.page-header { background:var(--bg-card); border-bottom:1px solid var(--bd-color); padding:14px 24px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-shrink:0; }
.page-header h2 { margin:0; font-size:1.1rem; font-weight:700; color:var(--txt-main); }
.content-area { flex:1; overflow-y:auto; padding:22px 24px; }

/* ── Cards ─────────────────────────────────────────────── */
.sec-card { background:var(--bg-card); border:1px solid var(--bd-color); border-radius:12px; overflow:hidden; margin-bottom:18px; }
.sec-header { background:var(--bg-hover); border-bottom:1px solid var(--bd-color); padding:12px 18px; display:flex; align-items:center; gap:10px; }
.sec-header h5 { margin:0; font-size:.9rem; font-weight:600; color:var(--txt-main); }
.sec-body { padding:18px; }

/* ── Stat cards ─────────────────────────────────────────── */
.stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:14px; margin-bottom:20px; }
.stat-card { background:var(--bg-card); border:1px solid var(--bd-color); border-radius:12px; padding:16px; text-align:center; transition:all .18s; cursor:default; }
.stat-card:hover { border-color:var(--primary); transform:translateY(-2px); }
.stat-num { font-size:1.8rem; font-weight:800; color:var(--primary); line-height:1; }
.stat-label { font-size:.72rem; color:var(--txt-muted); margin-top:4px; }

/* ── Table ──────────────────────────────────────────────── */
.t { width:100%; border-collapse:collapse; font-size:.82rem; }
.t thead tr { background:var(--bg-hover); }
.t th { padding:9px 12px; text-align:left; color:var(--txt-muted); font-weight:500; border-bottom:1px solid var(--bd-color); }
.t td { padding:9px 12px; border-bottom:1px solid var(--bd-color); color:var(--txt-main); vertical-align:middle; }
.t tr:last-child td { border-bottom:none; }
.t tr:hover td { background:rgba(var(--primary-rgb),.05); }

/* ── Badges & buttons ───────────────────────────────────── */
.badge-tipo { background:rgba(var(--primary-rgb),.15); color:var(--primary); border-radius:20px; padding:2px 9px; font-size:.7rem; font-weight:600; white-space:nowrap; }
.badge-admin { background:rgba(239,68,68,.15); color:#ef4444; border-radius:20px; padding:2px 9px; font-size:.7rem; font-weight:600; }
.badge-maestro { background:rgba(245,158,11,.15); color:#f59e0b; border-radius:20px; padding:2px 9px; font-size:.7rem; font-weight:600; }
.badge-alumno { background:rgba(16,185,129,.15); color:#10b981; border-radius:20px; padding:2px 9px; font-size:.7rem; font-weight:600; }
.btn-primary-a { background:linear-gradient(135deg,var(--primary),var(--primary2)); color:#fff; border:none; border-radius:8px; padding:7px 16px; font-size:.82rem; font-weight:600; cursor:pointer; transition:all .18s; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.btn-primary-a:hover { opacity:.88; transform:translateY(-1px); }
.btn-outline-a { background:none; border:1.5px solid var(--primary); color:var(--primary); border-radius:8px; padding:5px 13px; font-size:.78rem; font-weight:500; cursor:pointer; transition:all .18s; display:inline-flex; align-items:center; gap:5px; text-decoration:none; }
.btn-outline-a:hover { background:rgba(var(--primary-rgb),.1); }
.btn-danger-sm { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#ef4444; border-radius:6px; padding:3px 9px; font-size:.72rem; cursor:pointer; transition:all .18s; }
.btn-danger-sm:hover { background:rgba(239,68,68,.25); }
.btn-cancel { background:var(--bg-hover); border:1px solid var(--bd-color); color:var(--txt-muted); border-radius:8px; padding:7px 16px; font-size:.82rem; cursor:pointer; }

/* ── Search ─────────────────────────────────────────────── */
.search-bar { display:flex; gap:8px; margin-bottom:14px; }
.search-bar input { flex:1; background:var(--bg-hover); border:1px solid var(--bd-color); border-radius:8px; padding:7px 12px; color:var(--txt-main); font-size:.83rem; }
.search-bar input:focus { outline:none; border-color:var(--primary); }
.search-bar input::placeholder { color:var(--txt-muted); }

/* ── Modal ──────────────────────────────────────────────── */
.modal-content { background:var(--bg-card)!important; color:var(--txt-main)!important; border:1px solid var(--bd-color)!important; border-radius:14px!important; }
.modal-header { background:linear-gradient(135deg,var(--primary),var(--primary2)); color:#fff!important; border-radius:14px 14px 0 0!important; padding:16px 20px; }
.modal-header .btn-close { filter:brightness(0) invert(1); }
.modal-body .form-control, .modal-body .form-select { background:var(--bg-hover)!important; border-color:var(--bd-color)!important; color:var(--txt-main)!important; }
.modal-body .form-label { color:var(--txt-main); font-size:.85rem; }
body.light-theme .modal-content { background:#fff!important; color:#1a1a2e!important; }
body.light-theme .modal-body .form-control, body.light-theme .modal-body .form-select { background:#f8f9ff!important; border-color:#dde0f0!important; color:#1a1a2e!important; }

/* ── Misc ───────────────────────────────────────────────── */
.empty-state { text-align:center; padding:50px 20px; }
.empty-state i { font-size:3rem; opacity:.25; display:block; margin-bottom:12px; }
.empty-state p { color:var(--txt-muted); }
.t-msg { background:var(--bg-card); border:1px solid var(--bd-color); border-radius:10px; padding:10px 16px; margin-bottom:8px; font-size:.83rem; display:flex; align-items:center; animation:fadein .25s; }
.t-ok  { border-left:3px solid var(--c-success); color:var(--c-success); }
.t-err { border-left:3px solid var(--c-danger);  color:var(--c-danger); }
.t-info{ border-left:3px solid var(--c-info);    color:var(--c-info); }
#toast-c { position:fixed; bottom:20px; right:20px; z-index:9999; min-width:260px; }
@keyframes fadein { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
</style>
</head>
<body>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-shield-fill-check"></i>Panel Admin <span class="admin-badge">ADMIN</span></h4>
        <small>DiagramasUML · Área de Trabajo</small>
    </div>
    <div class="sidebar-user">
        <div class="avatar"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['username'] ?? 'Admin') ?></div>
            <div class="user-role">⚡ Administrador</div>
        </div>
    </div>
    <nav>
        <div class="nav-section">General</div>
        <button class="nav-btn active" id="nav-inicio" onclick="showSection('inicio')"><i class="bi bi-speedometer2"></i> Inicio</button>

        <div class="nav-section">Mis Diagramas</div>
        <button class="nav-btn" id="nav-mis-diagramas" onclick="showSection('mis-diagramas')"><i class="bi bi-diagram-3"></i> Mis Diagramas</button>

        <div class="nav-section">Gestión Global</div>
        <button class="nav-btn" id="nav-todos-diagramas" onclick="showSection('todos-diagramas')"><i class="bi bi-eye"></i> Todos los Diagramas</button>
        <button class="nav-btn" id="nav-grupos" onclick="showSection('grupos')"><i class="bi bi-collection"></i> Todos los Grupos</button>
        <button class="nav-btn" id="nav-proyectos" onclick="showSection('proyectos')"><i class="bi bi-kanban"></i> Todos los Proyectos</button>

        <div class="nav-section">Contenido</div>
        <button class="nav-btn" id="nav-plantillas" onclick="showSection('plantillas')"><i class="bi bi-layout-text-sidebar-reverse"></i> Plantillas</button>
        <button class="nav-btn" id="nav-code-editor" onclick="showSection('code-editor')"><i class="bi bi-code-slash"></i> Editor de Código</button>

        <div class="nav-section">Sistema</div>
        <button class="nav-btn" id="nav-espacio" onclick="showSection('espacio')"><i class="bi bi-hdd"></i> Espacio de Usuarios</button>
        <button class="nav-btn" id="nav-svgs" onclick="showSection('svg-resources')"><i class="bi bi-file-earmark-image"></i> SVGs</button>
        <button class="nav-btn" id="nav-api" onclick="showSection('api')"><i class="bi bi-braces"></i> API REST</button>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/admin" class="nav-btn text-decoration-none" style="color:rgba(255,255,255,.7)">
            <i class="bi bi-shield-fill-check"></i> Panel Admin Principal
        </a>
        <button class="nav-btn" onclick="abrirModalNuevoDiagrama()"><i class="bi bi-plus-square"></i> Nuevo Diagrama</button>
        <button class="nav-btn" onclick="toggleTheme()" style="color:rgba(255,255,255,.7)"><i class="bi bi-palette"></i> Cambiar Tema</button>
        <a href="<?= BASE_URL ?>/logout" class="nav-btn text-decoration-none danger-btn"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
    </div>
</aside>

<!-- ══ MAIN ══════════════════════════════════════════════════ -->
<div class="main">
    <div class="page-header">
        <h2 id="pageTitle"><i class="bi bi-speedometer2 me-2" style="color:var(--primary)"></i>Inicio</h2>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="badge-admin"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            <button class="btn-primary-a" onclick="abrirModalNuevoDiagrama()"><i class="bi bi-plus-lg"></i>Nuevo Diagrama</button>
        </div>
    </div>
    <div class="content-area" id="contentArea"></div>
</div>

<div id="toast-c"></div>

<!-- ══ MODAL NUEVO DIAGRAMA ══════════════════════════════════ -->
<div class="modal fade" id="modalNuevoDiagrama" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiagTitulo"><i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="mEditId" value="">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-type me-1"></i>Título</label>
                    <input type="text" class="form-control" id="mTitulo" placeholder="Ej: Diagrama de login">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-diagram-3 me-1"></i>Tipo de Diagrama</label>
                    <select class="form-select" id="mTipo">
                        <option value="usecase">Diagrama de Casos de Uso</option>
                        <option value="class">Diagrama de Clases</option>
                        <option value="sequence">Diagrama de Secuencia / Interacción</option>
                        <option value="activity">Diagrama de Actividades</option>
                        <option value="state">Diagrama de Estados</option>
                        <option value="component">Diagrama de Componentes</option>
                        <option value="deployment">Diagrama de Despliegue</option>
                        <option value="object">Diagrama de Objetos</option>
                        <option value="communication">Diagrama de Comunicación</option>
                        <option value="timing">Diagrama de Tiempo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-text-paragraph me-1"></i>Descripción <span style="color:var(--txt-muted);font-weight:normal">(opcional)</span></label>
                    <textarea class="form-control" id="mDescripcion" rows="2" placeholder="Breve descripción..."></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label"><i class="bi bi-tags me-1"></i>Etiquetas <span style="color:var(--txt-muted);font-weight:normal">(opcional)</span></label>
                    <input type="text" class="form-control" id="mEtiquetas" placeholder="proyecto, trabajo, personal">
                    <div style="color:var(--txt-muted);font-size:.75rem;margin-top:4px"><i class="bi bi-info-circle me-1"></i>Separa con comas</div>
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-primary-a" id="btnAccionDiag" onclick="accionModalDiagrama()">
                    <i class="bi bi-pencil-square me-1"></i>Ir al Editor
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= Assets::bootstrapJs() ?>"></script>
<script>window.BASE_URL = "<?= BASE_URL ?>";</script>
<script src="<?= Assets::url('js/user-theme.js') ?>"></script>
<script>
const ADMIN_ID = <?= $adminId ?>;
const TIPOS = { usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia', activity:'Actividades',
    state:'Estados', component:'Componentes', deployment:'Despliegue', object:'Objetos',
    communication:'Comunicación', timing:'Tiempo' };
const TIPOS_I = { usecase:'bi-person-circle', class:'bi-diagram-3', sequence:'bi-arrow-left-right',
    activity:'bi-activity', state:'bi-toggles', component:'bi-puzzle', deployment:'bi-cloud',
    object:'bi-box', communication:'bi-chat-dots', timing:'bi-clock' };

// ── Utilidades ──────────────────────────────────────────────
function toast(msg, type='ok') {
    const el = document.createElement('div');
    el.className = `t-msg t-${type}`;
    el.innerHTML = `<i class="bi bi-${type==='ok'?'check-circle-fill':type==='err'?'x-circle-fill':'info-circle-fill'} me-2"></i>${msg}`;
    document.getElementById('toast-c').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
async function api(url, opts={}) {
    const res  = await fetch(url, opts);
    const text = await res.text();
    try { return JSON.parse(text); } catch { throw new Error('Respuesta inválida: '+text.substring(0,80)); }
}
function loading() {
    document.getElementById('contentArea').innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><div style="color:var(--txt-muted);margin-top:10px;font-size:.83rem">Cargando…</div></div>`;
}
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function formatBytes(b) { if(!b||b===0)return'0 B';const k=1024,s=['B','KB','MB','GB'],i=Math.floor(Math.log(b)/Math.log(k));return parseFloat((b/Math.pow(k,i)).toFixed(1))+' '+s[i]; }
function rolBadge(r) { return `<span class="badge-${r}">${r}</span>`; }
function toggleTheme() {
    document.body.classList.toggle('light-theme');
    localStorage.setItem('adm_dash_theme', document.body.classList.contains('light-theme') ? 'light' : 'dark');
}

// ── Navegación ──────────────────────────────────────────────
const titles = { inicio:'Inicio', 'mis-diagramas':'Mis Diagramas', 'todos-diagramas':'Todos los Diagramas', grupos:'Todos los Grupos', proyectos:'Todos los Proyectos', plantillas:'Plantillas del Sistema', 'code-editor':'Editor de Código', espacio:'Espacio de Usuarios', 'svg-resources':'Recursos SVG', api:'API REST — Documentación' };
const views  = { inicio:renderInicio, 'mis-diagramas':renderMisDiagramas, 'todos-diagramas':renderTodosDiagramas, grupos:renderGrupos, proyectos:renderProyectos, plantillas:renderPlantillas, 'code-editor':renderCodeEditor, espacio:renderEspacio, 'svg-resources':renderSvgResources, api:renderApiDocs };

function showSection(id) {
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('nav-'+id)?.classList.add('active');
    document.getElementById('pageTitle').innerHTML = `<i class="bi bi-${
        {inicio:'speedometer2','mis-diagramas':'diagram-3','todos-diagramas':'eye',grupos:'collection',proyectos:'kanban','plantillas':'layout-text-sidebar-reverse','code-editor':'code-slash','espacio':'hdd','api':'braces','svg-resources':'file-earmark-image'}[id]||'diagram-3'
    } me-2" style="color:var(--primary)"></i>${titles[id]||id}`;
    views[id]?.();
}

// ── INICIO ──────────────────────────────────────────────────
async function renderInicio() {
    loading();
    try {
        const s = await api('<?= BASE_URL ?>/api/admin-dashboard?action=stats');
        document.getElementById('contentArea').innerHTML = `
        <div class="stat-grid">
            <div class="stat-card" onclick="showSection('todos-diagramas')" style="cursor:pointer">
                <div class="stat-num">${s.diagramas||0}</div>
                <div class="stat-label"><i class="bi bi-diagram-3 me-1"></i>Diagramas totales</div>
            </div>
            <div class="stat-card" onclick="showSection('mis-diagramas')" style="cursor:pointer">
                <div class="stat-num" style="color:var(--c-success)">${s.mis_diagramas||0}</div>
                <div class="stat-label"><i class="bi bi-person me-1"></i>Mis diagramas</div>
            </div>
            <div class="stat-card" onclick="showSection('grupos')" style="cursor:pointer">
                <div class="stat-num" style="color:var(--c-warning)">${s.grupos||0}</div>
                <div class="stat-label"><i class="bi bi-collection me-1"></i>Grupos activos</div>
            </div>
            <div class="stat-card" onclick="showSection('proyectos')" style="cursor:pointer">
                <div class="stat-num" style="color:var(--c-info)">${s.proyectos||0}</div>
                <div class="stat-label"><i class="bi bi-kanban me-1"></i>Proyectos</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" style="color:#f59e0b">${s.maestros||0}</div>
                <div class="stat-label"><i class="bi bi-person-badge me-1"></i>Maestros</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" style="color:#10b981">${s.alumnos||0}</div>
                <div class="stat-label"><i class="bi bi-people me-1"></i>Alumnos</div>
            </div>
        </div>
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-lightning-fill" style="color:var(--primary)"></i><h5>Acciones Rápidas</h5></div>
            <div class="sec-body" style="display:flex;flex-wrap:wrap;gap:10px">
                <button class="btn-primary-a" onclick="abrirModalNuevoDiagrama()"><i class="bi bi-plus-circle"></i>Nuevo Diagrama</button>
                <button class="btn-outline-a" onclick="showSection('todos-diagramas')"><i class="bi bi-eye"></i>Ver Todos los Diagramas</button>
                <button class="btn-outline-a" onclick="showSection('grupos')"><i class="bi bi-collection"></i>Gestionar Grupos</button>
                <button class="btn-outline-a" onclick="showSection('proyectos')"><i class="bi bi-kanban"></i>Ver Proyectos</button>
                <a href="<?= BASE_URL ?>/admin" class="btn-outline-a"><i class="bi bi-shield-fill-check"></i>Panel Admin Principal</a>
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ── MIS DIAGRAMAS ───────────────────────────────────────────
let _misDiagFiltro='', _misDiagPag=1;
async function renderMisDiagramas() {
    loading();
    try {
        const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=mis_diagramas&filtro=${encodeURIComponent(_misDiagFiltro)}&pagina=${_misDiagPag}`);
        const diags = data.diagramas||[];
        const e = data.estadisticas||{};
        document.getElementById('contentArea').innerHTML = `
        <div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(110px,1fr));margin-bottom:16px">
            ${Object.entries({total:(e.total||0),usecase:(e.usecase||0),class:(e.class||0),sequence:(e.sequence||0)}).map(([k,v])=>`
            <div class="stat-card"><div class="stat-num" style="font-size:1.4rem">${v}</div><div class="stat-label">${k==='total'?'Total':TIPOS[k]||k}</div></div>`).join('')}
        </div>
        <div class="sec-card">
            <div class="sec-header" style="justify-content:space-between">
                <div style="display:flex;align-items:center;gap:8px"><i class="bi bi-diagram-3" style="color:var(--primary)"></i><h5>Mis Diagramas</h5></div>
                <button class="btn-primary-a" onclick="abrirModalNuevoDiagrama()"><i class="bi bi-plus-lg"></i>Nuevo</button>
            </div>
            <div class="sec-body">
                <div class="search-bar">
                    <input type="text" id="filtroMisDiag" placeholder="Buscar por título…" value="${esc(_misDiagFiltro)}"
                           onkeyup="if(event.key==='Enter'){_misDiagFiltro=this.value;_misDiagPag=1;renderMisDiagramas()}">
                    <button class="btn-outline-a" onclick="_misDiagFiltro=document.getElementById('filtroMisDiag').value;_misDiagPag=1;renderMisDiagramas()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                ${diags.length===0
                    ? '<div class="empty-state"><i class="bi bi-diagram-3"></i><p>No tienes diagramas aún</p><button class="btn-primary-a" onclick="abrirModalNuevoDiagrama()"><i class="bi bi-plus-lg me-1"></i>Crear primero</button></div>'
                    : `<div style="overflow-x:auto"><table class="t">
                        <thead><tr><th>Título</th><th>Tipo</th><th>Versión</th><th>Modificado</th><th></th></tr></thead>
                        <tbody>
                        ${diags.map(d=>`<tr>
                            <td><strong style="font-size:.83rem">${esc(d.titulo||'Sin título')}</strong></td>
                            <td><span class="badge-tipo"><i class="bi ${TIPOS_I[d.tipo_diagrama]||'bi-diagram-3'} me-1"></i>${TIPOS[d.tipo_diagrama]||d.tipo_diagrama}</span></td>
                            <td><span class="badge-tipo">v${d.version||1}</span></td>
                            <td style="font-size:.76rem;color:var(--txt-muted)">${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</td>
                            <td>
                                <button class="btn-outline-a me-1" type="button" onclick="openDiagramInAdminPanel(${d.id})" style="font-size:.72rem;padding:3px 10px">
                                    <i class="bi bi-layout-text-window-reverse me-1"></i>Abrir
                                </button>
                                <a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank" class="btn-outline-a me-1" style="font-size:.72rem;padding:3px 10px">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <button class="btn-danger-sm" onclick="eliminarMiDiagrama(${d.id},'${esc(d.titulo)}')"><i class="bi bi-trash3"></i></button>
                            </td>
                        </tr>`).join('')}
                        </tbody></table></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px">
                        <span style="color:var(--txt-muted);font-size:.78rem">${data.total||0} diagrama${(data.total||0)!==1?'s':''} en total</span>
                        <div style="display:flex;gap:6px">
                            ${_misDiagPag>1?`<button class="btn-outline-a" onclick="_misDiagPag--;renderMisDiagramas()"><i class="bi bi-chevron-left"></i></button>`:''}
                            <span style="color:var(--txt-muted);font-size:.78rem;padding:5px 8px">Pág. ${_misDiagPag}</span>
                            ${diags.length===15?`<button class="btn-outline-a" onclick="_misDiagPag++;renderMisDiagramas()"><i class="bi bi-chevron-right"></i></button>`:''}
                        </div>
                    </div>`
                }
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

async function eliminarMiDiagrama(id, titulo) {
    if (!confirm(`¿Eliminar el diagrama "${titulo}"?\nEsta acción no se puede deshacer.`)) return;
    try {
        const d = await api('<?= BASE_URL ?>/api/admin-dashboard?action=eliminar_diagrama', {
            method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id})
        });
        if (d.success) { toast('Diagrama eliminado','ok'); renderMisDiagramas(); }
        else throw new Error(d.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

// ── TODOS LOS DIAGRAMAS (vista global admin) ────────────────
let _allDiagFiltro='', _allDiagPag=1;
async function renderTodosDiagramas() {
    loading();
    try {
        const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=todos_diagramas&filtro=${encodeURIComponent(_allDiagFiltro)}&pagina=${_allDiagPag}`);
        const diags = data.diagramas||[];
        document.getElementById('contentArea').innerHTML = `
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-eye" style="color:var(--primary)"></i><h5>Todos los Diagramas del Sistema</h5>
                <span class="badge-tipo ms-auto">${data.total||0} total</span>
            </div>
            <div class="sec-body">
                <div class="search-bar">
                    <input type="text" id="filtroAllDiag" placeholder="Buscar por título o usuario…" value="${esc(_allDiagFiltro)}"
                           onkeyup="if(event.key==='Enter'){_allDiagFiltro=this.value;_allDiagPag=1;renderTodosDiagramas()}">
                    <button class="btn-outline-a" onclick="_allDiagFiltro=document.getElementById('filtroAllDiag').value;_allDiagPag=1;renderTodosDiagramas()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                ${diags.length===0
                    ? '<div class="empty-state"><i class="bi bi-diagram-3"></i><p>Sin resultados</p></div>'
                    : `<div style="overflow-x:auto"><table class="t">
                        <thead><tr><th>Título</th><th>Tipo</th><th>Propietario</th><th>Rol</th><th>Ver.</th><th>Modificado</th><th></th></tr></thead>
                        <tbody>
                        ${diags.map(d=>`<tr>
                            <td><strong style="font-size:.82rem">${esc(d.titulo||'Sin título')}</strong></td>
                            <td><span class="badge-tipo">${TIPOS[d.tipo_diagrama]||d.tipo_diagrama}</span></td>
                            <td style="font-size:.78rem">${esc(d.nombre_completo||d.username)}<br><span style="color:var(--txt-muted);font-size:.7rem">@${esc(d.username)}</span></td>
                            <td>${rolBadge(d.rol||'alumno')}</td>
                            <td><span class="badge-tipo">v${d.version||1}</span></td>
                            <td style="font-size:.74rem;color:var(--txt-muted)">${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</td>
                            <td>
                                <button class="btn-outline-a me-1" type="button" onclick="openDiagramInAdminPanel(${d.id})" style="font-size:.7rem;padding:3px 9px"><i class="bi bi-layout-text-window-reverse me-1"></i>Abrir</button>
                                <a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank" class="btn-outline-a" style="font-size:.7rem;padding:3px 9px"><i class="bi bi-arrow-up-right-square me-1"></i>Editor</a>
                            </td>
                        </tr>`).join('')}
                        </tbody></table></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px">
                        <span style="color:var(--txt-muted);font-size:.78rem">${data.total||0} diagrama${(data.total||0)!==1?'s':''}</span>
                        <div style="display:flex;gap:6px">
                            ${_allDiagPag>1?`<button class="btn-outline-a" onclick="_allDiagPag--;renderTodosDiagramas()"><i class="bi bi-chevron-left"></i></button>`:''}
                            <span style="color:var(--txt-muted);font-size:.78rem;padding:5px 8px">Pág. ${_allDiagPag}</span>
                            ${diags.length===15?`<button class="btn-outline-a" onclick="_allDiagPag++;renderTodosDiagramas()"><i class="bi bi-chevron-right"></i></button>`:''}
                        </div>
                    </div>`
                }
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ── GRUPOS (vista global) ────────────────────────────────────
async function renderGrupos() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/admin-dashboard?action=todos_grupos');
        const grupos = data.grupos||[];
        document.getElementById('contentArea').innerHTML = `
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-collection" style="color:var(--primary)"></i><h5>Todos los Grupos</h5>
                <span class="badge-tipo ms-auto">${grupos.length} grupos</span>
            </div>
            <div class="sec-body" ${grupos.length===0?'':'style="padding:0"'}>
                ${grupos.length===0
                    ? '<div class="empty-state"><i class="bi bi-collection"></i><p>No hay grupos activos</p></div>'
                    : `<table class="t">
                        <thead><tr><th>Nombre</th><th>Código</th><th>Maestro</th><th>Alumnos</th><th>Creado</th><th></th></tr></thead>
                        <tbody>
                        ${grupos.map(g=>`<tr>
                            <td><strong style="font-size:.83rem">${esc(g.nombre)}</strong>${g.descripcion?`<br><span style="font-size:.72rem;color:var(--txt-muted)">${esc(g.descripcion)}</span>`:''}</td>
                            <td><code style="background:rgba(var(--primary-rgb),.1);color:var(--primary);border-radius:4px;padding:2px 6px;font-size:.75rem">${esc(g.codigo||'—')}</code></td>
                            <td style="font-size:.78rem">${esc(g.maestro_nombre||'—')}</td>
                            <td><span class="badge-tipo">${g.num_alumnos||0}</span></td>
                            <td style="font-size:.74rem;color:var(--txt-muted)">${new Date(g.fecha_creacion).toLocaleDateString('es-MX')}</td>
                            <td><button class="btn-outline-a" style="font-size:.7rem;padding:3px 9px" onclick="verAlumnosGrupo(${g.id},'${esc(g.nombre)}')"><i class="bi bi-people me-1"></i>Alumnos</button></td>
                        </tr>`).join('')}
                        </tbody></table>`
                }
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

async function verAlumnosGrupo(gid, nombre) {
    try {
        const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=alumnos_grupo&grupo_id=${gid}`);
        const alumnos = data.alumnos||[];
        document.getElementById('contentArea').innerHTML = `
        <button class="btn-outline-a mb-3" onclick="renderGrupos()"><i class="bi bi-arrow-left me-1"></i>Volver a Grupos</button>
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-people" style="color:var(--primary)"></i><h5>Alumnos: ${esc(nombre)}</h5>
                <span class="badge-tipo ms-auto">${alumnos.length} alumnos</span>
            </div>
            <div class="sec-body" ${alumnos.length===0?'':'style="padding:0"'}>
                ${alumnos.length===0
                    ? '<div class="empty-state"><i class="bi bi-people"></i><p>Sin alumnos en este grupo</p></div>'
                    : `<table class="t">
                        <thead><tr><th>Nombre</th><th>Usuario</th><th>Email</th><th>Diagramas</th><th></th></tr></thead>
                        <tbody>
                        ${alumnos.map(a=>`<tr>
                            <td><strong style="font-size:.83rem">${esc(a.nombre_completo||a.username)}</strong></td>
                            <td style="font-size:.78rem;color:var(--txt-muted)">@${esc(a.username)}</td>
                            <td style="font-size:.78rem">${esc(a.email||'—')}</td>
                            <td><span class="badge-tipo">${a.num_diagramas||0}</span></td>
                            <td><button class="btn-outline-a" style="font-size:.7rem;padding:3px 9px"
                                onclick="verDiagramasUsuario(${a.id},'${escH(a.nombre_completo||a.username)}')"><i class="bi bi-diagram-3 me-1"></i>Ver diagramas</button></td>
                        </tr>`).join('')}
                        </tbody></table>`
                }
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

async function verDiagramasUsuario(uid, nombre) {
    try {
        const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=todos_diagramas&filtro=${encodeURIComponent(nombre)}`);
        const diags = (data.diagramas||[]).filter(d => d.usuario_id == uid);
        document.getElementById('contentArea').innerHTML = `
        <button class="btn-outline-a mb-3" onclick="renderGrupos()"><i class="bi bi-arrow-left me-1"></i>Volver</button>
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-diagram-3" style="color:var(--primary)"></i><h5>Diagramas de ${esc(nombre)}</h5></div>
            <div class="sec-body" ${diags.length===0?'':'style="padding:0"'}>
                ${diags.length===0
                    ? '<div class="empty-state"><i class="bi bi-diagram-3"></i><p>Sin diagramas</p></div>'
                    : `<table class="t">
                        <thead><tr><th>Título</th><th>Tipo</th><th>Versión</th><th>Modificado</th><th></th></tr></thead>
                        <tbody>
                        ${diags.map(d=>`<tr>
                            <td><strong style="font-size:.82rem">${esc(d.titulo||'Sin título')}</strong></td>
                            <td><span class="badge-tipo">${TIPOS[d.tipo_diagrama]||d.tipo_diagrama}</span></td>
                            <td><span class="badge-tipo">v${d.version||1}</span></td>
                            <td style="font-size:.74rem;color:var(--txt-muted)">${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</td>
                            <td><button class="btn-outline-a" type="button" onclick="openDiagramInAdminPanel(${d.id})" style="font-size:.7rem;padding:3px 9px"><i class="bi bi-layout-text-window-reverse me-1"></i>Abrir</button></td>
                        </tr>`).join('')}
                        </tbody></table>`
                }
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ── PROYECTOS (vista global) ─────────────────────────────────
async function renderProyectos() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/admin-dashboard?action=todos_proyectos');
        const proyectos = data.proyectos||[];
        document.getElementById('contentArea').innerHTML = `
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-kanban" style="color:var(--primary)"></i><h5>Todos los Proyectos Colaborativos</h5>
                <span class="badge-tipo ms-auto">${proyectos.length} proyectos</span>
            </div>
            <div class="sec-body" ${proyectos.length===0?'':'style="padding:0"'}>
                ${proyectos.length===0
                    ? '<div class="empty-state"><i class="bi bi-kanban"></i><p>No hay proyectos colaborativos</p></div>'
                    : `<table class="t">
                        <thead><tr><th>Nombre</th><th>Propietario</th><th>Miembros</th><th>Diagramas</th><th>Creado</th><th></th></tr></thead>
                        <tbody>
                        ${proyectos.map(p=>`<tr>
                            <td>
                                <strong style="font-size:.83rem">${esc(p.nombre)}</strong>
                                ${p.descripcion?`<br><span style="font-size:.72rem;color:var(--txt-muted)">${esc(p.descripcion)}</span>`:''}
                            </td>
                            <td style="font-size:.78rem">${esc(p.owner_nombre||'—')}</td>
                            <td><span class="badge-tipo">${p.num_miembros||1}</span></td>
                            <td><span class="badge-tipo">${p.num_diagramas||0}</span></td>
                            <td style="font-size:.74rem;color:var(--txt-muted)">${new Date(p.fecha_creacion).toLocaleDateString('es-MX')}</td>
                            <td><button class="btn-outline-a" type="button" onclick="verDiagramasProyecto(${p.id},'${escH(p.nombre)}')" style="font-size:.7rem;padding:3px 9px"><i class="bi bi-diagram-3 me-1"></i>Diagramas</button></td>
                        </tr>`).join('')}
                        </tbody></table>`
                }
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

async function verDiagramasProyecto(pid, nombre) {
    loading();
    try {
        const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=proyecto_diagramas&proyecto_id=${pid}`);
        const diags = data.diagramas || [];
        document.getElementById('contentArea').innerHTML = `
        <button class="btn-outline-a mb-3" onclick="renderProyectos()"><i class="bi bi-arrow-left me-1"></i>Volver a Proyectos</button>
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-diagram-3" style="color:var(--primary)"></i><h5>Diagramas del proyecto: ${esc(nombre)}</h5>
                <span class="badge-tipo ms-auto">${diags.length} diagramas</span>
            </div>
            <div class="sec-body">
                ${diags.length === 0 ? '<div class="empty-state"><i class="bi bi-diagram-3"></i><p>Este proyecto no tiene diagramas ligados.</p></div>' : `
                <div style="overflow-x:auto"><table class="t">
                    <thead><tr><th>Título</th><th>Tipo</th><th>Propietario</th><th>Modificado</th><th></th></tr></thead>
                    <tbody>
                    ${diags.map(d=>`<tr>
                        <td><strong style="font-size:.82rem">${esc(d.titulo||'Sin título')}</strong></td>
                        <td><span class="badge-tipo">${TIPOS[d.tipo_diagrama]||d.tipo_diagrama}</span></td>
                        <td style="font-size:.78rem">${esc(d.nombre_completo||d.username||'—')}</td>
                        <td style="font-size:.74rem;color:var(--txt-muted)">${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</td>
                        <td><button class="btn-outline-a" type="button" onclick="openDiagramInAdminPanel(${d.id})" style="font-size:.7rem;padding:3px 9px"><i class="bi bi-layout-text-window-reverse me-1"></i>Abrir</button></td>
                    </tr>`).join('')}
                    </tbody></table></div>`}
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ── PLANTILLAS ────────────────────────────────────────────────
let _plantillasData = [];

async function renderPlantillas() {
    const contentArea = document.getElementById('contentArea');
    contentArea.innerHTML = `<div style="text-align:center;padding:40px;color:var(--txt-muted)"><div class="spinner-border" style="color:var(--primary)"></div><p class="mt-3">Cargando plantillas...</p></div>`;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=plantillas');
        _plantillasData = r.plantillas || [];
        contentArea.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px">
            <p style="color:var(--txt-muted);font-size:.83rem;margin:0">
                Diagramas marcados como plantilla del sistema (compartidos). Los usuarios pueden usarlos como base.
            </p>
            <button onclick="abrirModalNuevaPlantilla()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:10px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
                <i class="bi bi-plus-lg"></i> Nueva Plantilla
            </button>
        </div>
        <div id="plantillas-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">
            ${_plantillasData.length === 0
                ? `<div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--txt-muted)">
                    <i class="bi bi-layout-text-sidebar-reverse" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px"></i>
                    <p style="margin:0">No hay plantillas aún. Crea la primera.</p>
                  </div>`
                : _plantillasData.map(p => `
                <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:10px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
                        <div>
                            <div style="font-weight:700;color:var(--txt-main);font-size:.9rem">${escH(p.titulo)}</div>
                            <div style="font-size:.73rem;color:var(--primary);margin-top:2px;text-transform:uppercase;letter-spacing:.05em">${escH(p.tipo_diagrama)}</div>
                        </div>
                        <span style="background:rgba(16,185,129,.15);color:#10b981;font-size:.7rem;padding:2px 8px;border-radius:20px;white-space:nowrap">Plantilla</span>
                    </div>
                    ${p.descripcion ? `<div style="font-size:.78rem;color:var(--txt-muted);line-height:1.4">${escH(p.descripcion)}</div>` : ''}
                    <div style="font-size:.72rem;color:var(--txt-muted)">
                        <i class="bi bi-person me-1"></i>${escH(p.username || p.nombre_completo || 'Admin')} &nbsp;·&nbsp;
                        <i class="bi bi-calendar2 me-1"></i>${new Date(p.fecha_modificacion).toLocaleDateString('es-MX')}
                    </div>
                    <div style="display:flex;gap:8px;margin-top:4px;flex-wrap:wrap">
                        <button type="button" class="btn-outline-a" onclick="openDiagramInAdminPanel(${p.id})"
                            style="flex:1 1 120px;text-align:center;padding:6px 0;font-size:.78rem;border:1.5px solid var(--primary);color:var(--primary);border-radius:8px;background:none;cursor:pointer;font-weight:600">
                            <i class="bi bi-layout-text-window-reverse me-1"></i>Abrir
                        </button>
                        <a href="<?= BASE_URL ?>/editor?id=${p.id}" target="_blank"
                            style="flex:1 1 120px;text-align:center;padding:6px 0;font-size:.78rem;border:1.5px solid var(--primary);color:var(--primary);border-radius:8px;text-decoration:none;font-weight:600">
                            <i class="bi bi-pencil-square me-1"></i>Editar
                        </a>
                        <button onclick="descargarPlantilla(${p.id},'${escH(p.titulo)}')"
                            style="flex:1 1 120px;padding:6px 0;font-size:.78rem;border:1.5px solid rgba(16,185,129,.3);color:#10b981;border-radius:8px;background:none;cursor:pointer;font-weight:600">
                            <i class="bi bi-download me-1"></i>Respaldar
                        </button>
                        <button onclick="editarMetaPlantilla(${p.id})"
                            style="flex:1 1 120px;padding:6px 0;font-size:.78rem;border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;background:none;cursor:pointer;font-weight:600">
                            <i class="bi bi-info-circle me-1"></i>Info
                        </button>
                        <button onclick="eliminarPlantilla(${p.id},'${escH(p.titulo)}')"
                            style="flex:1 1 120px;padding:6px 10px;font-size:.78rem;border:1.5px solid rgba(239,68,68,.3);color:#ef4444;border-radius:8px;background:none;cursor:pointer">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>`).join('')
            }
        </div>`;
    } catch(e) { toast(e.message, 'err'); }
}

function escH(s) { const d = document.createElement('div'); d.textContent = s||''; return d.innerHTML; }

function abrirModalNuevaPlantilla() {
    document.getElementById('_modalNewPlantilla')?.remove();
    const m = document.createElement('div');
    m.id = '_modalNewPlantilla'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:18px 22px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-layout-text-sidebar-reverse me-2"></i>Nueva Plantilla</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px;display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:5px">Titulo *</label>
                    <input id="_plTitulo" type="text" class="form-control" placeholder="Ej: Plantilla Login UML"
                        style="background:var(--bg-deep);color:var(--txt-main);border-color:var(--bd-color)">
                </div>
                <div>
                    <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:5px">Tipo de diagrama</label>
                    <select id="_plTipo" class="form-select" style="background:var(--bg-deep);color:var(--txt-main);border-color:var(--bd-color)">
                        <option value="usecase">Caso de Uso</option>
                        <option value="class">Clases</option>
                        <option value="sequence">Secuencia</option>
                        <option value="activity">Actividades</option>
                        <option value="state">Estados</option>
                        <option value="component">Componentes</option>
                        <option value="deployment">Despliegue</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:5px">Descripcion</label>
                    <textarea id="_plDesc" class="form-control" rows="2" placeholder="Para que sirve esta plantilla..."
                        style="background:var(--bg-deep);color:var(--txt-main);border-color:var(--bd-color);resize:none"></textarea>
                </div>
                <div id="_plErr" class="text-danger small d-none"></div>
                <div style="background:rgba(102,126,234,.08);border:1px solid rgba(102,126,234,.2);border-radius:10px;padding:12px;font-size:.78rem;color:var(--txt-muted)">
                    <i class="bi bi-info-circle me-1" style="color:var(--primary)"></i>
                    Se creara un diagrama vacio como plantilla. Despues podras editarlo en el editor.
                </div>
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">Cancelar</button>
                <button onclick="confirmarNuevaPlantilla()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-plus-lg me-1"></i>Crear Plantilla
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
    m.addEventListener('shown.bs.modal', () => document.getElementById('_plTitulo')?.focus());
}

async function confirmarNuevaPlantilla() {
    const titulo = document.getElementById('_plTitulo')?.value.trim();
    const tipo = document.getElementById('_plTipo')?.value;
    const descripcion = document.getElementById('_plDesc')?.value.trim() || '';
    const errEl = document.getElementById('_plErr');
    if (!titulo) { errEl.textContent = 'El titulo es obligatorio'; errEl.classList.remove('d-none'); return; }
    errEl.classList.add('d-none');
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=crear_plantilla', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ titulo, tipo, descripcion })
        });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalNewPlantilla'))?.hide();
            toast('Plantilla creada. Ahora puedes editarla en el editor.', 'ok');
            renderPlantillas();
        } else { errEl.textContent = r.error||'Error al crear'; errEl.classList.remove('d-none'); }
    } catch(e) { errEl.textContent = e.message; errEl.classList.remove('d-none'); }
}

function editarMetaPlantilla(id) {
    const p = _plantillasData.find(x => x.id == id);
    if (!p) return;
    document.getElementById('_modalEditPlantilla')?.remove();
    const m = document.createElement('div');
    m.id = '_modalEditPlantilla'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:18px 22px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-pencil-square me-2"></i>Editar Info</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px;display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:5px">Titulo</label>
                    <input id="_epTitulo" type="text" class="form-control" value="${escH(p.titulo)}"
                        style="background:var(--bg-deep);color:var(--txt-main);border-color:var(--bd-color)">
                </div>
                <div>
                    <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:5px">Descripcion</label>
                    <textarea id="_epDesc" class="form-control" rows="2"
                        style="background:var(--bg-deep);color:var(--txt-main);border-color:var(--bd-color);resize:none">${escH(p.descripcion||'')}</textarea>
                </div>
                <div id="_epErr" class="text-danger small d-none"></div>
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">Cancelar</button>
                <button onclick="guardarMetaPlantilla(${id})" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check2 me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
}

async function guardarMetaPlantilla(id) {
    const titulo = document.getElementById('_epTitulo')?.value.trim();
    const descripcion = document.getElementById('_epDesc')?.value.trim() || '';
    const errEl = document.getElementById('_epErr');
    if (!titulo) { errEl.textContent = 'El titulo es obligatorio'; errEl.classList.remove('d-none'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=editar_plantilla', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ id, titulo, descripcion })
        });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalEditPlantilla'))?.hide();
            toast('Plantilla actualizada', 'ok');
            renderPlantillas();
        } else { errEl.textContent = r.error||'Error'; errEl.classList.remove('d-none'); }
    } catch(e) { errEl.textContent = e.message; errEl.classList.remove('d-none'); }
}

async function eliminarPlantilla(id, nombre) {
    if (!confirm(`Eliminar la plantilla "${nombre}"? Esta accion no se puede deshacer.`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=eliminar_plantilla', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ id })
        });
        if (r.success) { toast('Plantilla eliminada', 'ok'); renderPlantillas(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message, 'err'); }
}

async function descargarPlantilla(id, titulo) {
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=backup_plantilla', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ id })
        });
        if (!r.success) throw new Error(r.error||'Error al respaldar');
        const filename = r.filename || `${titulo.replace(/[^a-zA-Z0-9_-]/g,'_')}.json`;
        const blob = new Blob([r.content], { type:'application/json;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
        toast('Plantilla respaldada', 'ok');
    } catch(e) { toast(e.message, 'err'); }
}


// ── MODAL NUEVO DIAGRAMA ─────────────────────────────────────
let _modalNuevoDiag = null;
function abrirModalNuevoDiagrama(editId=null, d=null) {
    if (!_modalNuevoDiag) _modalNuevoDiag = new bootstrap.Modal(document.getElementById('modalNuevoDiagrama'));
    document.getElementById('mEditId').value      = editId||'';
    document.getElementById('mTitulo').value      = d?d.titulo:'';
    document.getElementById('mTipo').value        = d?d.tipo_diagrama:'usecase';
    document.getElementById('mDescripcion').value = d?(d.descripcion||''):'';
    document.getElementById('mEtiquetas').value   = d?(d.etiquetas||''):'';
    document.getElementById('modalDiagTitulo').innerHTML = editId
        ? '<i class="bi bi-pencil me-2"></i>Editar Diagrama'
        : '<i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama';
    document.getElementById('btnAccionDiag').innerHTML = editId
        ? '<i class="bi bi-save me-1"></i>Guardar Cambios'
        : '<i class="bi bi-pencil-square me-1"></i>Ir al Editor';
    _modalNuevoDiag.show();
}

async function accionModalDiagrama() {
    const titulo = document.getElementById('mTitulo').value.trim();
    if (!titulo) { toast('El título no puede estar vacío','info'); return; }
    const tipo        = document.getElementById('mTipo').value;
    const descripcion = document.getElementById('mDescripcion').value;
    const etiquetas   = document.getElementById('mEtiquetas').value;
    const editId      = document.getElementById('mEditId').value;
    if (editId) {
        try {
            const r = await api('<?= BASE_URL ?>/api/diagramas/save', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({id:editId, titulo, tipo, descripcion, etiquetas, contenido:[]})
            });
            if (r.success) { toast('Diagrama actualizado','ok'); _modalNuevoDiag.hide(); renderMisDiagramas(); }
            else throw new Error(r.error||'Error al guardar');
        } catch(e) { toast(e.message,'err'); }
    } else {
        sessionStorage.setItem('nuevoDiagrama', JSON.stringify({titulo, tipo, descripcion, etiquetas}));
        _modalNuevoDiag.hide();
        window.location.href = '<?= BASE_URL ?>/editor?tipo=' + tipo;
    }
}

// ── ESPACIO DE USUARIOS ───────────────────────────────────────
async function renderEspacio() {
    const contentArea = document.getElementById('contentArea');
    contentArea.innerHTML = `<div style="text-align:center;padding:40px"><div class="spinner-border" style="color:var(--primary)"></div><p class="mt-3" style="color:var(--txt-muted)">Cargando datos de espacio...</p></div>`;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=espacio_usuarios');
        const us = r.usuarios || [];
        const totalBytes = us.reduce((a,u) => a + parseInt(u.espacio_usado_bytes||0), 0);
        const totalLimitBytes = us.reduce((a,u) => a + ((parseInt(u.espacio_limite_mb||0) || 0) * 1024 * 1024), 0);
        const unlimitedCount = us.filter(u => parseInt(u.espacio_limite_mb||0) === 0).length;
        const fmtBytes = b => b < 1024*1024 ? (b/1024).toFixed(1)+'KB' : (b/1024/1024).toFixed(2)+'MB';

        contentArea.innerHTML = `
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px">
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:12px;padding:14px 20px;flex:1;min-width:160px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase">Total Usuarios</div>
                <div style="font-size:1.6rem;font-weight:700;color:var(--txt-main)">${us.length}</div>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:12px;padding:14px 20px;flex:1;min-width:160px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase">Espacio Total Usado</div>
                <div style="font-size:1.6rem;font-weight:700;color:var(--primary)">${fmtBytes(totalBytes)}</div>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:12px;padding:14px 20px;flex:1;min-width:160px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase">Límite Total Calculado</div>
                <div style="font-size:1.6rem;font-weight:700;color:var(--txt-main)">${totalLimitBytes === 0 ? 'Ilimitado' : fmtBytes(totalLimitBytes)}</div>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:12px;padding:14px 20px;flex:1;min-width:160px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase">Usuarios Sin Límite</div>
                <div style="font-size:1.6rem;font-weight:700;color:var(--primary)">${unlimitedCount}</div>
            </div>
        </div>

        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;margin-bottom:18px;padding:18px">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px">
                <h6 style="margin:0;color:var(--txt-main);font-weight:700"><i class="bi bi-globe me-2" style="color:var(--primary)"></i>Límite Global</h6>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    <input type="number" id="_globalLim" min="0" value="100" placeholder="MB (0=ilimitado)"
                        style="width:130px;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:6px 10px;font-size:.82rem">
                    <button onclick="cambiarLimiteGlobal()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:7px 16px;font-size:.82rem;font-weight:600;cursor:pointer">
                        <i class="bi bi-check2 me-1"></i>Aplicar a Todos
                    </button>
                </div>
            </div>
            <p style="font-size:.78rem;color:var(--txt-muted);margin:0">Pon 0 para ilimitado. Esta acción actualiza el límite de todos los usuarios (excepto admin).</p>
        </div>

        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px">
                <h6 style="margin:0;color:var(--txt-main);font-weight:700"><i class="bi bi-people me-2" style="color:var(--primary)"></i>Espacio por Usuario</h6>
            </div>
            <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead><tr style="background:var(--bg-deep)">
                    <th style="padding:10px 14px;text-align:left;font-size:.75rem;color:var(--txt-muted);font-weight:600">Usuario</th>
                    <th style="padding:10px 14px;text-align:left;font-size:.75rem;color:var(--txt-muted);font-weight:600">Rol</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.75rem;color:var(--txt-muted);font-weight:600">Diagramas</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.75rem;color:var(--txt-muted);font-weight:600">Usado</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.75rem;color:var(--txt-muted);font-weight:600">Límite</th>
                    <th style="padding:10px 14px;text-align:center;font-size:.75rem;color:var(--txt-muted);font-weight:600">Uso</th>
                    <th style="padding:10px 14px;text-align:center;font-size:.75rem;color:var(--txt-muted);font-weight:600">Acción</th>
                </tr></thead>
                <tbody>
                ${us.map(u => {
                    const usado = parseInt(u.espacio_usado_bytes||0);
                    const lim   = parseInt(u.espacio_limite_mb||100);
                    const limB  = lim * 1024 * 1024;
                    const pct   = lim === 0 ? 0 : Math.min(100, (usado/limB*100));
                    const barClr= pct > 90 ? '#ef4444' : pct > 70 ? '#f59e0b' : '#10b981';
                    const rolBadge = u.rol === 'admin' ? '#ef4444' : u.rol === 'maestro' ? '#8b5cf6' : '#3b82f6';
                    return `<tr style="border-bottom:1px solid var(--bd-color)">
                        <td style="padding:10px 14px">
                            <div style="font-weight:600;color:var(--txt-main);font-size:.83rem">${u.username}</div>
                            <div style="font-size:.72rem;color:var(--txt-muted)">${u.nombre_completo||''}</div>
                        </td>
                        <td style="padding:10px 14px">
                            <span style="background:${rolBadge}22;color:${rolBadge};border:1px solid ${rolBadge}44;border-radius:20px;padding:2px 10px;font-size:.72rem;font-weight:600">${u.rol}</span>
                        </td>
                        <td style="padding:10px 14px;text-align:right;color:var(--txt-main);font-size:.83rem">${u.num_diagramas}</td>
                        <td style="padding:10px 14px;text-align:right;color:var(--txt-main);font-size:.83rem;font-weight:600">${fmtBytes(usado)}</td>
                        <td style="padding:10px 14px;text-align:right;color:var(--txt-muted);font-size:.83rem">${lim === 0 ? '∞' : lim+'MB'}</td>
                        <td style="padding:10px 14px;min-width:100px">
                            <div style="background:var(--bg-deep);border-radius:4px;height:6px;overflow:hidden">
                                <div style="height:100%;width:${pct}%;background:${barClr};border-radius:4px;transition:width .3s"></div>
                            </div>
                            <div style="font-size:.68rem;color:var(--txt-muted);text-align:center;margin-top:2px">${lim===0?'ilimitado':pct.toFixed(1)+'%'}</div>
                        </td>
                        <td style="padding:10px 14px;text-align:center">
                            ${u.rol !== 'admin' ? `<button onclick="editarLimiteEspacio(${u.id},'${u.username}',${lim})"
                                style="background:none;border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:4px 12px;font-size:.75rem;cursor:pointer">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </button>` : '<span style="font-size:.72rem;color:var(--txt-muted)">—</span>'}
                        </td>
                    </tr>`;
                }).join('')}
                </tbody>
            </table>
            </div>
        </div>`;
    } catch(e) { toast(e.message,'err'); }
}

async function cambiarLimiteGlobal() {
    const lim = parseInt(document.getElementById('_globalLim')?.value || '100');
    if (isNaN(lim) || lim < 0) { toast('Ingresa un número válido (0 = ilimitado)','err'); return; }
    if (!confirm(`¿Aplicar límite de ${lim === 0 ? 'ILIMITADO' : lim+'MB'} a TODOS los usuarios?`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=cambiar_limite_global', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ limite_mb: lim })
        });
        if (r.success) { toast(`Límite de ${lim===0?'ilimitado':lim+'MB'} aplicado a todos`,'ok'); renderEspacio(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

function editarLimiteEspacio(uid, username, limActual) {
    document.getElementById('_modalEditLim')?.remove();
    const m = document.createElement('div');
    m.id = '_modalEditLim'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:380px">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:18px 22px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-hdd me-2"></i>Límite — ${username}</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px">
                <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:5px">Límite en MB (0 = ilimitado)</label>
                <input id="_editLimVal" type="number" min="0" value="${limActual}" class="form-control"
                    style="background:var(--bg-deep);color:var(--txt-main);border-color:var(--bd-color);font-size:1rem;font-weight:600">
                <p style="font-size:.75rem;color:var(--txt-muted);margin-top:8px;margin-bottom:0">Pon 0 para dar espacio ilimitado a este usuario.</p>
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">Cancelar</button>
                <button onclick="guardarLimiteEspacio(${uid})" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check2 me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
    m.addEventListener('shown.bs.modal', () => document.getElementById('_editLimVal')?.focus());
}

async function guardarLimiteEspacio(uid) {
    const lim = parseInt(document.getElementById('_editLimVal')?.value || '100');
    if (isNaN(lim) || lim < 0) { toast('Valor inválido','err'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=cambiar_limite_espacio', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ usuario_id: uid, limite_mb: lim })
        });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalEditLim'))?.hide();
            toast('Límite actualizado','ok');
            renderEspacio();
        } else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

// ── API REST — DOCUMENTACIÓN ──────────────────────────────────
function renderApiDocs() {
    const BASE = window.BASE_URL;
    const contentArea = document.getElementById('contentArea');
    contentArea.innerHTML = `
    <div style="display:flex;flex-direction:column;gap:18px">

        <!-- Estado y acciones -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px">
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:18px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase;margin-bottom:12px"><i class="bi bi-database me-1"></i>Paso 1 — Tabla BD</div>
                <p style="font-size:.78rem;color:var(--txt-muted);margin-bottom:12px">Instala las tablas <code>diagramas_api</code> y <code>diagramas_api_historial</code> en tu base de datos.</p>
                <button onclick="instalarTablaApi()" style="width:100%;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:9px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-database-add me-1"></i>Instalar Tabla API
                </button>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:18px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase;margin-bottom:12px"><i class="bi bi-filetype-js me-1"></i>Paso 2 — Node.js</div>
                <p style="font-size:.78rem;color:var(--txt-muted);margin-bottom:12px">Instala las dependencias del servidor API (<code>npm install</code> en la carpeta <code>editor-api/</code>).</p>
                <button onclick="instalarNodeApi()" style="width:100%;background:linear-gradient(135deg,#10b981,#059669);border:none;color:#fff;border-radius:8px;padding:9px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-terminal me-1"></i>Ver instrucciones Node.js
                </button>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:18px">
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase;margin-bottom:12px"><i class="bi bi-activity me-1"></i>Paso 3 — Probar API</div>
                <p style="font-size:.78rem;color:var(--txt-muted);margin-bottom:12px">Verifica que el servidor Node.js esté corriendo en el puerto 3000.</p>
                <button onclick="probarApi()" style="width:100%;background:linear-gradient(135deg,#f59e0b,#d97706);border:none;color:#fff;border-radius:8px;padding:9px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-wifi me-1"></i>Probar conexión API
                </button>
            </div>
        </div>

        <!-- Endpoints -->
        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);background:var(--bg-deep)">
                <h6 style="margin:0;color:var(--txt-main);font-weight:700"><i class="bi bi-braces me-2" style="color:var(--primary)"></i>Endpoints disponibles</h6>
                <p style="margin:4px 0 0;font-size:.75rem;color:var(--txt-muted)">Base URL: <code>http://localhost:3000</code></p>
            </div>
            <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead><tr style="background:var(--bg-deep)">
                    <th style="padding:8px 14px;font-size:.72rem;color:var(--txt-muted);font-weight:600;text-align:left">Método</th>
                    <th style="padding:8px 14px;font-size:.72rem;color:var(--txt-muted);font-weight:600;text-align:left">Ruta</th>
                    <th style="padding:8px 14px;font-size:.72rem;color:var(--txt-muted);font-weight:600;text-align:left">Auth</th>
                    <th style="padding:8px 14px;font-size:.72rem;color:var(--txt-muted);font-weight:600;text-align:left">Descripción</th>
                </tr></thead>
                <tbody>
                ${[
                    ['GET',    '/',                            'No',  'Verificar que funciona'],
                    ['POST',   '/api/auth/register',          'No',  'Registrar usuario'],
                    ['POST',   '/api/auth/login',             'No',  'Login — obtener token JWT'],
                    ['GET',    '/api/auth/perfil',            'Sí',  'Ver mi perfil'],
                    ['GET',    '/api/diagramas/publicos',     'No',  'Diagramas compartidos'],
                    ['GET',    '/api/diagramas',              'Sí',  'Mis diagramas (tabla paralela)'],
                    ['GET',    '/api/diagramas/:id',          'Sí',  'Un diagrama por ID'],
                    ['POST',   '/api/diagramas',              'Sí',  'Crear diagrama'],
                    ['PUT',    '/api/diagramas/:id',          'Sí',  'Actualizar diagrama'],
                    ['DELETE', '/api/diagramas/:id',          'Sí',  'Eliminar diagrama'],
                    ['POST',   '/api/diagramas/:id/duplicar', 'Sí',  'Duplicar diagrama'],
                ].map(([m,r,a,d]) => {
                    const color = m==='GET'?'#3b82f6':m==='POST'?'#10b981':m==='PUT'?'#f59e0b':'#ef4444';
                    return `<tr style="border-bottom:1px solid var(--bd-color)">
                        <td style="padding:8px 14px"><span style="background:${color}22;color:${color};border:1px solid ${color}44;border-radius:4px;padding:2px 8px;font-size:.72rem;font-weight:700;font-family:monospace">${m}</span></td>
                        <td style="padding:8px 14px;font-family:monospace;font-size:.78rem;color:var(--txt-main)">${r}</td>
                        <td style="padding:8px 14px"><span style="font-size:.72rem;color:${a==='Sí'?'#ef4444':'#10b981'}">${a==='Sí'?'🔒 JWT':'🌐 Público'}</span></td>
                        <td style="padding:8px 14px;font-size:.78rem;color:var(--txt-muted)">${d}</td>
                    </tr>`;
                }).join('')}
                </tbody>
            </table>
            </div>
        </div>

        <!-- Ejemplo de uso -->
        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:18px">
            <h6 style="color:var(--txt-main);font-weight:700;margin-bottom:14px"><i class="bi bi-code-slash me-2" style="color:var(--primary)"></i>Ejemplo de uso</h6>
            <div style="background:var(--bg-deep);border-radius:10px;padding:14px;font-family:monospace;font-size:.78rem;line-height:1.7;color:var(--txt-main);overflow-x:auto">
<pre style="margin:0;white-space:pre-wrap;color:var(--txt-main)"><span style="color:#8b5cf6"># 1. Login</span>
POST http://localhost:3000/api/auth/login
Body: { "username": "alumno1", "password": "password" }
→ Responde: { "token": "eyJ..." }

<span style="color:#8b5cf6"># 2. Usar el token en siguientes peticiones</span>
GET http://localhost:3000/api/diagramas
Header: Authorization: Bearer eyJ...

<span style="color:#8b5cf6"># 3. Crear un diagrama</span>
POST http://localhost:3000/api/diagramas
Header: Authorization: Bearer eyJ...
Body: { "titulo": "Mi diagrama", "tipo_diagrama": "usecase", "contenido_json": "{}" }</pre>
            </div>
        </div>

        <!-- Config .env -->
        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:18px">
            <h6 style="color:var(--txt-main);font-weight:700;margin-bottom:14px"><i class="bi bi-gear me-2" style="color:var(--primary)"></i>Configuración <code>.env</code></h6>
            <div style="background:var(--bg-deep);border-radius:10px;padding:14px;font-family:monospace;font-size:.78rem;line-height:1.9;overflow-x:auto">
<pre style="margin:0;white-space:pre-wrap;color:var(--txt-main)">PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=           <span style="color:var(--txt-muted)">← tu contraseña MySQL (vacío si no tienes)</span>
DB_NAME=diagramas_db
DB_PORT=3306
JWT_SECRET=escribe_cualquier_texto_largo_aqui_12345
JWT_EXPIRES_IN=7d</pre>
            </div>
            <p style="font-size:.75rem;color:var(--txt-muted);margin-top:10px;margin-bottom:0">
                Archivo ubicado en: <code>DiagramasMVC/editor-api/.env</code> (copia <code>.env.example</code> y edítalo)
            </p>
        </div>
    </div>`;
}

async function instalarTablaApi() {
    if (!confirm('¿Instalar las tablas del Editor API?\n\nEsto creará:\n• api_editor_tokens\n• api_editor_logs\n• diagram_versions')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=instalar_tabla_api', {
            method:'POST', headers:{'Content-Type':'application/json'}, body:'{}'
        });
        if (r.success) toast('¡Tablas del Editor API instaladas correctamente!', 'ok');
        else throw new Error(r.error || 'Error al instalar');
    } catch(e) { toast(e.message, 'err'); }
}

function instalarNodeApi() {
    document.getElementById('_modalNodeInst')?.remove();
    const m = document.createElement('div');
    m.id = '_modalNodeInst'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,#10b981,#059669);padding:18px 22px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-terminal me-2"></i>Instalar y levantar la API Node.js</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px;display:flex;flex-direction:column;gap:14px">
                <div style="background:var(--bg-deep);border-radius:10px;padding:16px">
                    <div style="font-size:.75rem;color:#10b981;font-weight:700;margin-bottom:8px">Paso 1 — Requisito: Node.js instalado</div>
                    <p style="font-size:.78rem;color:var(--txt-muted);margin:0">Descarga e instala Node.js desde <a href="https://nodejs.org" target="_blank" style="color:var(--primary)">nodejs.org</a> (versión LTS recomendada).</p>
                </div>
                <div style="background:var(--bg-deep);border-radius:10px;padding:16px">
                    <div style="font-size:.75rem;color:#10b981;font-weight:700;margin-bottom:8px">Paso 2 — Abre CMD o terminal en la carpeta del proyecto</div>
                    <div style="font-family:monospace;font-size:.82rem;background:#000;color:#0f0;padding:12px;border-radius:8px;line-height:1.8">
                        cd C:\\xampp\\htdocs\\Proyectos\\DiagramasMVC\\editor-api<br>
                        copy .env.example .env<br>
                        <span style="color:#ff0"># Edita .env con tus datos de MySQL</span><br>
                        npm install<br>
                        npm run dev
                    </div>
                </div>
                <div style="background:var(--bg-deep);border-radius:10px;padding:16px">
                    <div style="font-size:.75rem;color:#10b981;font-weight:700;margin-bottom:8px">Paso 3 — Verificar</div>
                    <p style="font-size:.78rem;color:var(--txt-muted);margin:0">Deberías ver: <code>Servidor corriendo en http://localhost:3000</code><br>
                    Luego usa el botón "Probar conexión API" para confirmar.</p>
                </div>
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end">
                <button data-bs-dismiss="modal" style="background:linear-gradient(135deg,#10b981,#059669);border:none;color:#fff;border-radius:8px;padding:8px 22px;font-size:.82rem;font-weight:600;cursor:pointer">Entendido</button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
}

async function renderSvgResources() {
    loading();
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=check_svgs');
        const grupos = r.grupos || [];
        const missingCount = grupos.reduce((sum, grupo) => sum + grupo.archivos.filter(a => !a.existe).length, 0);
        document.getElementById('contentArea').innerHTML = `
        <div class="sec-card">
            <div class="sec-header" style="justify-content:space-between;align-items:center">
                <div style="display:flex;align-items:center;gap:8px"><i class="bi bi-file-earmark-image" style="color:var(--primary)"></i><h5>Recursos SVG</h5></div>
                <button class="btn-outline-a" onclick="generarSvgTodos()"><i class="bi bi-arrow-repeat me-1"></i>Regenerar faltantes</button>
            </div>
            <div class="sec-body">
                <p style="color:var(--txt-muted);margin-bottom:16px">
                    Estado de los recursos SVG del editor. Usa los botones para regenerar los iconos faltantes en cada carpeta.
                </p>
                ${grupos.length === 0 ? '<div class="empty-state"><i class="bi bi-file-earmark-image"></i><p>No se encontraron grupos de SVG</p></div>' : ''}
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px">
                    ${grupos.map(grupo => {
                        const faltantes = grupo.archivos.filter(a => !a.existe).length;
                        return `
                        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:12px">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
                                <div>
                                    <div style="font-weight:700;color:var(--txt-main);font-size:.92rem">${esc(grupo.carpeta)}</div>
                                    <div style="font-size:.78rem;color:var(--txt-muted)">Archivos: ${grupo.archivos.length} · Faltantes: ${faltantes}</div>
                                </div>
                                <button class="btn-outline-a" onclick="generarSvgCarpeta('${esc(grupo.carpeta)}')"><i class="bi bi-arrow-repeat me-1"></i>Regenerar</button>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:8px">
                                ${grupo.archivos.map(a => `
                                    <div style="background:var(--bg-hover);border:1px solid ${a.existe ? '#10b98122' : '#ef444422'};border-radius:12px;padding:12px;font-size:.78rem;color:${a.existe ? '#10b981' : '#ef4444'};display:flex;flex-direction:column;gap:10px">
                                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                                            <span style="font-weight:700;">${esc(a.nombre)}</span>
                                            <span style="font-size:.72rem;color:${a.existe ? '#10b981' : '#ef4444'}">${a.existe ? 'OK' : 'Falta'}</span>
                                        </div>
                                        <div style="min-height:90px;display:flex;align-items:center;justify-content:center;background:${a.existe ? 'rgba(16,185,129,.05)' : 'rgba(239,68,68,.05)'};border-radius:10px;overflow:hidden;">
                                            ${a.existe ? `<img src="${BASE_URL}/assets/img/${esc(grupo.carpeta)}/${esc(a.nombre)}" alt="${esc(a.nombre)}" style="max-width:100%;max-height:100%;display:block">` : `<div style="color:var(--txt-muted);font-size:.75rem;text-align:center;padding:12px">SVG no disponible</div>`}
                                        </div>
                                    </div>`).join('')}
                            </div>
                        </div>`;
                    }).join('')}
                </div>
            </div>
        </div>`;
        if (missingCount === 0 && grupos.length > 0) toast('Todos los SVG están presentes.', 'ok');
    } catch (e) { toast(e.message, 'err'); }
}

let codeEditorState = { resource: 'diagram', loadedDiagramId: null, loadedSvgPath: null };

function updateCodeMode() {
    const mode = document.getElementById('codeResourceType').value;
    codeEditorState.resource = mode;
    document.getElementById('codeResourceDiagram').style.display = mode === 'diagram' ? 'block' : 'none';
    document.getElementById('codeResourceSvg').style.display = mode === 'svg' ? 'block' : 'none';
    document.getElementById('codeEditorMetadata').style.display = mode === 'diagram' ? 'block' : 'none';
    document.getElementById('codeEditorOpenEditorBtn').style.display = (mode === 'diagram' && codeEditorState.loadedDiagramId) ? 'inline-flex' : 'none';
    document.getElementById('codeEditorPreviewLabel').textContent = mode === 'diagram' ? 'Previsualización de diagrama' : 'Previsualización de SVG';
    document.getElementById('codeEditorTextarea').placeholder = mode === 'diagram' ? 'JSON del diagrama...' : 'Código SVG...';
    document.getElementById('codeEditorMessage').style.display = 'none';
    document.getElementById('codeEditorSvgPreview').style.display = 'none';
    document.getElementById('codeEditorIframe').style.display = 'none';
}

async function renderCodeEditor() {
    document.getElementById('contentArea').innerHTML = `
        <div class="sec-card">
            <div class="sec-header" style="justify-content:space-between;align-items:center">
                <div style="display:flex;align-items:center;gap:8px"><i class="bi bi-code-slash" style="color:var(--primary)"></i><h5>Editor de Código</h5></div>
                <button class="btn-outline-a" onclick="loadCodeEditorResource()"><i class="bi bi-arrow-clockwise me-1"></i>Recargar recurso</button>
            </div>
            <div class="sec-body">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Tipo de recurso</label>
                        <select id="codeResourceType" class="form-select form-control-dark" onchange="updateCodeMode()">
                            <option value="diagram">Diagrama JSON</option>
                            <option value="svg">SVG</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4" id="codeResourceDiagram">
                        <label class="form-label">ID de diagrama</label>
                        <div class="input-group">
                            <input type="number" class="form-control form-control-dark" id="codeDiagId" placeholder="123" min="1">
                            <button class="btn-outline-a" type="button" onclick="loadCodeEditorResource()">Cargar</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4" id="codeResourceSvg" style="display:none">
                        <label class="form-label">Ruta SVG</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-dark" id="codeSvgPath" placeholder="assets/img/DiagramadeCasosdeUso/actor.svg">
                            <button class="btn-outline-a" type="button" onclick="loadCodeEditorResource()">Cargar</button>
                        </div>
                    </div>
                </div>
                <div id="codeEditorMetadata" style="display:block">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-5">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control form-control-dark" id="codeDiagTitle" placeholder="Título del diagrama">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Tipo de diagrama</label>
                            <select id="codeDiagTipo" class="form-select form-control-dark">
                                ${Object.entries(TIPOS).map(([k,v])=>`<option value="${k}">${v}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Abrir en editor</label>
                            <button class="btn-admin btn-admin-outline" id="codeEditorOpenEditorBtn" type="button" style="width:100%;display:none" onclick="openEditorFromCode()"><i class="bi bi-box-arrow-up-right me-1"></i>Editor</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" class="form-control form-control-dark" id="codeDiagDescription" placeholder="Descripción corta...">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contenido</label>
                    <textarea id="codeEditorTextarea" class="form-control form-control-dark" style="min-height:300px;font-family:Consolas,monospace;white-space:pre;" placeholder="Carga un diagrama o SVG para iniciar"></textarea>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:10px">
                    <button class="btn-primary-a" type="button" onclick="previewCodeResource()"><i class="bi bi-eye me-1"></i>Vista previa</button>
                    <button class="btn-outline-a" type="button" onclick="saveCodeResource()"><i class="bi bi-save me-1"></i>Guardar cambios</button>
                </div>
            </div>
        </div>
        <div class="sec-card">
            <div class="sec-header" style="justify-content:space-between;align-items:center">
                <div style="display:flex;align-items:center;gap:8px"><i class="bi bi-display-fill" style="color:var(--primary)"></i><h5 id="codeEditorPreviewLabel">Previsualización</h5></div>
            </div>
            <div class="sec-body">
                <div id="codeEditorMessage" class="t-msg t-info" style="display:none"></div>
                <div id="codeEditorPreviewContainer">
                    <iframe id="codeEditorIframe" style="width:100%;min-height:420px;border:1px solid var(--bd-color);border-radius:12px;display:none"></iframe>
                    <div id="codeEditorSvgPreview" style="width:100%;min-height:420px;border:1px solid var(--bd-color);border-radius:12px;padding:16px;display:none;background:var(--bg-hover);overflow:auto"></div>
                </div>
            </div>
        </div>`;
    updateCodeMode();
}

function setCodeEditorMessage(message, type='info') {
    const el = document.getElementById('codeEditorMessage');
    el.className = `t-msg t-${type}`;
    el.textContent = message;
    el.style.display = 'flex';
}

async function loadCodeEditorResource() {
    try {
        const resource = document.getElementById('codeResourceType').value;
        if (resource === 'diagram') {
            const id = parseInt(document.getElementById('codeDiagId').value, 10);
            if (!id) throw new Error('Ingresa un ID de diagrama válido');
            const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=load_code_editor&resource=diagram&id=${id}`);
            if (!data.success) throw new Error(data.error || 'No se pudo cargar el diagrama');
            document.getElementById('codeDiagTitle').value = data.titulo || '';
            document.getElementById('codeDiagTipo').value = data.tipo_diagrama || 'usecase';
            document.getElementById('codeDiagDescription').value = data.descripcion || '';
            document.getElementById('codeEditorTextarea').value = JSON.stringify(data.contenido || {}, null, 2);
            codeEditorState.loadedDiagramId = id;
            codeEditorState.loadedSvgPath = null;
            document.getElementById('codeEditorOpenEditorBtn').style.display = 'inline-flex';
            setCodeEditorMessage(`Diagrama ${id} cargado. Usa Guardar para actualizarlo o Vista previa para abrirlo.`,'ok');
        } else {
            const path = document.getElementById('codeSvgPath').value.trim();
            if (!path) throw new Error('Ingresa la ruta del SVG');
            const data = await api(`<?= BASE_URL ?>/api/admin-dashboard?action=load_code_editor&resource=svg&path=${encodeURIComponent(path)}`);
            if (!data.success) throw new Error(data.error || 'No se pudo cargar el SVG');
            document.getElementById('codeEditorTextarea').value = data.content || '';
            codeEditorState.loadedDiagramId = null;
            codeEditorState.loadedSvgPath = path;
            document.getElementById('codeEditorOpenEditorBtn').style.display = 'none';
            setCodeEditorMessage(`SVG cargado desde ${path}. Ahora puedes editar y guardar el archivo.`,'ok');
        }
        document.getElementById('codeEditorIframe').style.display = 'none';
        document.getElementById('codeEditorSvgPreview').style.display = 'none';
    } catch (e) { setCodeEditorMessage(e.message,'err'); }
}

async function saveCodeResource() {
    try {
        const resource = document.getElementById('codeResourceType').value;
        if (resource === 'diagram') {
            const id = parseInt(document.getElementById('codeDiagId').value, 10);
            const titulo = document.getElementById('codeDiagTitle').value.trim();
            const tipo = document.getElementById('codeDiagTipo').value;
            const descripcion = document.getElementById('codeDiagDescription').value.trim();
            const contenido = document.getElementById('codeEditorTextarea').value;
            if (!id || !titulo) throw new Error('ID y título del diagrama son requeridos');
            const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=guardar_codigo', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ resource:'diagram', id, titulo, tipo, descripcion, contenido })
            });
            if (!r.success) throw new Error(r.error || 'No se pudo guardar el diagrama');
            codeEditorState.loadedDiagramId = id;
            document.getElementById('codeEditorOpenEditorBtn').style.display = 'inline-flex';
            setCodeEditorMessage(`Diagrama ${id} guardado correctamente. Recarga la vista previa para ver los cambios.`,'ok');
            if (document.getElementById('codeEditorIframe').style.display === 'block') {
                document.getElementById('codeEditorIframe').src = `${BASE_URL}/editor?id=${id}&t=${Date.now()}`;
            }
        } else {
            const path = document.getElementById('codeSvgPath').value.trim();
            const content = document.getElementById('codeEditorTextarea').value;
            if (!path) throw new Error('Ruta SVG requerida');
            const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=guardar_codigo', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body:JSON.stringify({ resource:'svg', path, content })
            });
            if (!r.success) throw new Error(r.error || 'No se pudo guardar el SVG');
            codeEditorState.loadedSvgPath = path;
            setCodeEditorMessage(`SVG guardado en ${path}`, 'ok');
            if (document.getElementById('codeEditorSvgPreview').style.display === 'block') {
                previewCodeResource();
            }
        }
    } catch (e) { setCodeEditorMessage(e.message,'err'); }
}

function openEditorFromCode() {
    if (!codeEditorState.loadedDiagramId) return toast('Carga un diagrama válido primero','err');
    const iframe = document.getElementById('codeEditorIframe');
    iframe.src = `${BASE_URL}/editor?id=${codeEditorState.loadedDiagramId}&t=${Date.now()}`;
    iframe.style.display = 'block';
    document.getElementById('codeEditorSvgPreview').style.display = 'none';
    setCodeEditorMessage('Abriendo el editor en el panel de vista previa...', 'info');
}

function previewCodeResource() {
    const resource = document.getElementById('codeResourceType').value;
    if (resource === 'diagram') {
        if (!codeEditorState.loadedDiagramId) return setCodeEditorMessage('Carga primero un diagrama para ver la vista previa.', 'err');
        const iframe = document.getElementById('codeEditorIframe');
        iframe.src = `${BASE_URL}/editor?id=${codeEditorState.loadedDiagramId}&t=${Date.now()}`;
        iframe.style.display = 'block';
        document.getElementById('codeEditorSvgPreview').style.display = 'none';
        setCodeEditorMessage('Previsualizando el diagrama en el panel derecho.', 'ok');
    } else {
        const svgText = document.getElementById('codeEditorTextarea').value;
        const preview = document.getElementById('codeEditorSvgPreview');
        preview.innerHTML = svgText || '<div style="color:var(--txt-muted)">No hay contenido SVG para mostrar.</div>';
        preview.style.display = 'block';
        document.getElementById('codeEditorIframe').style.display = 'none';
        setCodeEditorMessage('Vista previa SVG actualizada.', 'ok');
    }
}

function openDiagramInAdminPanel(id) {
    showSection('code-editor');
    setTimeout(() => {
        const typeSelect = document.getElementById('codeResourceType');
        if (!typeSelect) return;
        typeSelect.value = 'diagram';
        updateCodeMode();
        const inputId = document.getElementById('codeDiagId');
        if (inputId) inputId.value = id;
        loadCodeEditorResource();
    }, 40);
}

async function generarSvgCarpeta(carpeta) {
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=generar_svgs', {
            method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ carpeta })
        });
        if (!r.success) throw new Error(r.error || 'No se pudo generar');
        toast(`Se generaron ${r.generados || 0} SVG(s) en ${carpeta}`, 'ok');
        renderSvgResources();
    } catch (e) { toast(e.message, 'err'); }
}

async function generarSvgTodos() {
    loading();
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=check_svgs');
        const grupos = r.grupos || [];
        let total = 0;
        for (const grupo of grupos) {
            const faltantes = grupo.archivos.filter(a => !a.existe).length;
            if (!faltantes) continue;
            const res = await api('<?= BASE_URL ?>/api/admin-dashboard?action=generar_svgs', {
                method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ carpeta: grupo.carpeta })
            });
            if (res.success) total += res.generados || 0;
        }
        toast(total === 0 ? 'No había SVG faltantes' : `Se generaron ${total} SVG(s) faltantes`, 'ok');
        renderSvgResources();
    } catch (e) { toast(e.message, 'err'); }
}

async function probarApi() {
    const btn = event.target;
    btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass me-1"></i>Probando...';
    try {
        const r = await fetch('http://localhost:3000/', { signal: AbortSignal.timeout(4000) });
        if (r.ok) { toast('✅ API corriendo correctamente en localhost:3000', 'ok'); }
        else throw new Error('Respuesta inesperada: ' + r.status);
    } catch(e) {
        toast('❌ API no disponible. Asegúrate de que npm run dev esté corriendo.', 'err');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-wifi me-1"></i>Probar conexión API';
    }
}


// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('adm_dash_theme') === 'light') document.body.classList.add('light-theme');
    showSection('inicio');
});
</script>
</body>
</html>
