<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Maestro — DiagramasUML</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/diagram-ui.css">
<link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
<style>
:root {
    --primary:     #667eea;
    --primary2:    #764ba2;
    --primary-rgb: 102,126,234;
    --sidebar:     260px;
    --bg-deep:     #0d0d1a;
    --bg-card:     #1a1a2e;
    --bg-hover:    #13132a;
    --bd-color:    #2a2a4a;
    --txt-main:    #e8eaff;
    --txt-muted:   #8888aa;
    /* Colores semánticos de acción (UX) */
    --c-success: #10b981;
    --c-danger:  #ef4444;
    --c-warning: #f59e0b;
    --c-info:    #3b82f6;
    --c-neutral: #6b7280;
}
* { box-sizing:border-box; }
body { background:var(--bg-deep); color:#e0e0e0; font-family:'Segoe UI',sans-serif; margin:0; }
body.light-theme { --bg-deep:#f0f2f8; --bg-card:#fff; --bg-hover:#f8f9ff; --bd-color:#e8eaf0; --txt-main:#1a1a2e; --txt-muted:#666; color:#1e1e2e; }
body.light-theme .sec-card { background:#fff; border-color:#e8eaf0; }
body.light-theme .sec-header { background:#f8f9ff; border-color:#e8eaf0; }
body.light-theme .t th { background:#f8f9ff; color:#666; border-color:#e8eaf0; }
body.light-theme .t td { color:#1a1a2e; border-color:#e8eaf0; }
body.light-theme .t tr:hover td { background:rgba(var(--primary-rgb),.05); }
body.light-theme .stat-num { color:#1e1e2e; }
body.light-theme .sec-header h5 { color:#1e1e2e; }
body.light-theme .page-header h2 { color:#1e1e2e; }
body.light-theme .sidebar { background:linear-gradient(160deg,#f8f9ff,#eef0ff); color:#1e1e2e; }
body.light-theme .sidebar-brand h4 { color:#1e1e2e; }
body.light-theme .sidebar-user h6 { color:#1e1e2e; }
body.light-theme .sidebar-user small { color:#666; }
body.light-theme .nav-section { color:#888; }
body.light-theme .nav-btn { color:rgba(30,30,46,.75); }
body.light-theme .nav-btn:hover { background:rgba(var(--primary-rgb),.1); color:var(--primary); }
body.light-theme .sidebar-footer .nav-btn { color: rgba(30,30,46,.75) !important; }
body.light-theme #themeDrawer { background:#f8f9ff !important; border-color:#dde0f0 !important; }
body.light-theme #themeDrawer [style*="color:#fff"] { color:#1e1e2e !important; }
body.light-theme [style*="color:rgba(255,255,255"] { color:#1e1e2e !important; }
body.light-theme #themeDrawer [style*="background:linear-gradient"][style*="color:#fff"] { color:#fff !important; }
body.light-theme .text-muted,
body.light-theme .form-text,
body.light-theme small,
body.light-theme .sidebar-user small,
body.light-theme .nav-section { color:#5d5d5d !important; }
body.light-theme .form-label,
body.light-theme .sec-header h5,
body.light-theme .page-header h2,
body.light-theme .stat-num,
body.light-theme .sidebar-user h6,
body.light-theme .nav-btn,
body.light-theme .t th,
body.light-theme .t td,
body.light-theme .modal-content,
body.light-theme .form-control,
body.light-theme .form-select { color:#1d1d28 !important; }

/* Panel de entregas - tema claro */
body.light-theme #panelEntregas [style*="background:var(--bg-card)"],
body.light-theme #panelEntregas [style*="background:var(--bg-hover)"] { background-color: var(--bg-card) !important; }
body.light-theme #panelEntregas [style*="color:var(--txt-main)"] { color: var(--txt-main) !important; }
body.light-theme #panelEntregas [style*="color:var(--txt-muted)"] { color: var(--txt-muted) !important; }
body.light-theme #panelEntregas input { background: var(--bg-card) !important; color: var(--txt-main) !important; border-color: var(--bd-color) !important; }


/* Sidebar */
.sidebar { position:fixed; top:0; left:0; width:var(--sidebar); height:100vh; background:linear-gradient(160deg,var(--bg-card),var(--bg-hover)); color:#fff; display:flex; flex-direction:column; z-index:100; overflow-y:auto; box-shadow:4px 0 20px rgba(0,0,0,.2); }
.sidebar-brand { padding:24px 20px 18px; border-bottom:1px solid rgba(255,255,255,.15); }
.sidebar-brand h4 { margin:0; font-size:1rem; font-weight:700; }
.sidebar-brand small { opacity:.65; font-size:.72rem; }
.sidebar-user { padding:16px 20px; border-bottom:1px solid rgba(255,255,255,.15); display:flex; align-items:center; gap:12px; }
.sidebar-user .avatar { width:42px; height:42px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.sidebar-user h6 { margin:0; font-size:.88rem; font-weight:600; }
.sidebar-user small { opacity:.7; font-size:.73rem; }
.nav-section { padding:14px 20px 4px; font-size:.68rem; text-transform:uppercase; letter-spacing:.1em; opacity:.5; }
.nav-btn { display:flex; align-items:center; gap:10px; width:100%; padding:11px 20px; background:none; border:none; color:rgba(255,255,255,.75); font-size:.86rem; cursor:pointer; transition:all .2s; text-align:left; border-left:3px solid transparent; }
.nav-btn:hover { background:rgba(255,255,255,.1); color:#fff; }
.nav-btn.active { background:rgba(255,255,255,.18); color:#fff; border-left-color:#fff; }
.nav-btn i { width:18px; text-align:center; }
.sidebar-footer { margin-top:auto; padding:14px 12px; border-top:1px solid rgba(255,255,255,.15); }

/* Main */
.main { margin-left:var(--sidebar); min-height:100vh; padding:28px 32px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.page-header h2 { margin:0; font-size:1.35rem; font-weight:700; color:var(--txt-main); }
.badge-maestro { background:linear-gradient(135deg,var(--primary),var(--primary2)); color:#fff; font-size:.7rem; padding:3px 12px; border-radius:20px; }

/* Stats */
.stat-card { background:var(--bg-card); border-radius:14px; padding:22px 20px; box-shadow:0 2px 12px rgba(0,0,0,.07); transition:all .2s; border:1px solid transparent; }
.stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(var(--primary-rgb),.2); border-color:rgba(var(--primary-rgb),.4); }

/* Section card */
.sec-card { background:var(--bg-card); border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; margin-bottom:20px; border:1px solid var(--bd-color); transition:border-color .2s,box-shadow .2s; }
.sec-card:hover { border-color:rgba(var(--primary-rgb),.45); box-shadow:0 4px 18px rgba(var(--primary-rgb),.12); }

/* Table */
.t { width:100%; border-collapse:collapse; font-size:.84rem; }
.t th { background:var(--bg-hover); color:var(--txt-muted); font-weight:500; padding:10px 14px; text-align:left; border-bottom:1px solid var(--bd-color); }
.t td { padding:10px 14px; border-bottom:1px solid var(--bd-color); color:var(--txt-main); vertical-align:middle; transition:background .15s,color .15s; }
.t tr:hover td { background:rgba(var(--primary-rgb),.1); color:#fff; }
.t tr:last-child td { border-bottom:none; }
/* Filas de listas/carpetas */
.list-row { transition:background .18s,border-color .2s; cursor:pointer; }
.list-row:hover { background:rgba(var(--primary-rgb),.12) !important; border-color:rgba(var(--primary-rgb),.5) !important; }

/* Badges */
.badge-tipo { font-size:.7rem; padding:3px 9px; border-radius:20px; background:linear-gradient(135deg,var(--primary),var(--primary2)); color:#fff; }
.badge-ok { background:#d1fae5; color:#065f46; border-radius:20px; padding:2px 9px; font-size:.72rem; }
.badge-pending { background:#fef3c7; color:#92400e; border-radius:20px; padding:2px 9px; font-size:.72rem; }

/* Buttons */
.btn-primary-m { background:linear-gradient(135deg,var(--primary),var(--primary2)); border:none; color:#fff; padding:10px 22px; border-radius:10px; font-size:.87rem; font-weight:600; cursor:pointer; transition:all .2s; }
.btn-primary-m:hover { transform:translateY(-1px); box-shadow:0 5px 15px rgba(var(--primary-rgb),.35); }
.btn-outline-m { background:none; border:1.5px solid var(--primary); color:var(--primary); padding:7px 16px; border-radius:8px; font-size:.82rem; cursor:pointer; transition:all .2s; }
.btn-outline-m:hover { background:rgba(var(--primary-rgb),.08); }
.btn-sm-danger { background:none; border:1px solid #dc3545; color:#dc3545; padding:4px 10px; border-radius:6px; font-size:.75rem; cursor:pointer; }
.btn-sm-danger:hover { background:rgba(220,53,69,.08); }

/* Modal */
.modal-content { border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal-header { background:linear-gradient(135deg,var(--primary),var(--primary2)); color:#fff; border-radius:16px 16px 0 0; padding:18px 22px; }
.modal-header .btn-close { filter:brightness(0) invert(1); }
.form-control { border-radius:10px; padding:10px 14px; border:1.5px solid var(--bd-color); font-size:.87rem; }
.form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(102,126,234,.12); outline:none; }
.form-label { font-weight:600; font-size:.83rem; color:var(--txt-muted); margin-bottom:5px; }
.form-select { border-radius:10px; padding:10px 14px; border:1.5px solid var(--bd-color); font-size:.87rem; }

/* Toast */
#toast-c { position:fixed; bottom:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
.t-msg { padding:11px 18px; border-radius:10px; font-size:.85rem; font-weight:500; box-shadow:0 4px 16px rgba(0,0,0,.15); animation:tIn .3s ease; max-width:300px; }
.t-ok { background:#d1fae5; color:#065f46; border-left:4px solid #10b981; }
.t-err { background:#fee2e2; color:#991b1b; border-left:4px solid #ef4444; }
.t-info { background:#dbeafe; color:#1e40af; border-left:4px solid #3b82f6; }
@keyframes tIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }

/* Código de grupo */
.code-badge {
    background: rgba(102,126,234,.12);
    border: 1.5px solid rgba(102,126,234,.3);
    color: var(--primary);
    font-size: .82rem;
    font-weight: 700;
    font-family: monospace;
    padding: 5px 12px;
    border-radius: 8px;
    cursor: pointer;
    letter-spacing: .1em;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    user-select: text;
    -webkit-user-select: text;
    transition: all .15s;
}
.code-badge:hover { background: rgba(102,126,234,.22); border-color: var(--primary); }

/* Empty */
.empty-state { text-align:center; padding:48px 20px; color:#bbb; }

/* Light/dark theme adaptation */
body.light-theme .sec-card,
body.light-theme .stat-card { background:#fff; }
body.light-theme .sec-header { border-bottom-color:#e8eaf0; background:#f8f9ff; }
body.light-theme .t td { border-bottom-color:#f0f2f8; color:#1a1a2e; }
body.light-theme .t th { background:#f8f9ff; color:var(--txt-muted); border-color:#e8eaf0; }
body.light-theme .page-header h2 { color:#1a1a2e; }
body.light-theme .stat-num { color:#1a1a2e; }
body.light-theme .main { background:#f0f2f8; }
body.light-theme .modal-content { background:#fff !important; color:#1a1a2e !important; }
body.light-theme #themeDrawer { background:#f8f9ff !important; border-color:#dde0f0 !important; }
body.light-theme [style*="color:#fff"] { color:#1a1e2e !important; }
body.light-theme #themeDrawer [style*="color:#fff"] { color:#1e1e2e !important; }
body.light-theme #themeDrawer [style*="background:linear-gradient"][style*="color:#fff"] { color:#fff !important; }
body.light-theme [style*="background:linear-gradient"][style*="color:#1a1e2e"] { color:#fff !important; }
body.light-theme [style*="background:var(--primary)"][style*="color:#1a1e2e"] { color:#fff !important; }
body.light-theme [style*="background:var(--primary2)"][style*="color:#1a1e2e"] { color:#fff !important; }

body.light-theme div[style*="background:#1a1a2e"] { background:#fff !important; border-color:#e8eaf0 !important; }
body.light-theme div[style*="background:#0d0d1a"] { background:#f8f9ff !important; border-color:#e8eaf0 !important; }
body.light-theme div[style*="background:#13132a"] { background:#f0f2ff !important; }
body.light-theme div[style*="background:#080812"] { background:#f8f9ff !important; border-color:#e8eaf0 !important; color:#1a1a2e !important; }
body.light-theme div[style*="border:1px solid #2a2a4a"] { border-color:#e8eaf0 !important; }
body.light-theme code[style*="background:#0d0d1a"] { background:#f0f2ff !important; color:var(--primary) !important; }
body.light-theme div[style*="color:#fff;font-weight"] { color:#1a1e2e !important; }
.empty-state i { font-size:3.5rem; opacity:.3; display:block; margin-bottom:12px; }

        /* ── LUCIDCHART-STYLE CARDS (Maestro) ── */
        .m-diagram-card {
            background: var(--bg-card); border-radius: 12px;
            border: 1.5px solid var(--bd-color);
            overflow: visible; transition: all .22s;
            box-shadow: 0 2px 8px rgba(0,0,0,.06); position: relative;
        }
        .m-diagram-card:hover { border-color: var(--primary); box-shadow: 0 6px 24px rgba(102,126,234,.18); transform: translateY(-2px); }
        .m-lc-preview {
            height: 140px; border-radius: 10px 10px 0 0;
            background: var(--bg-deep); border-bottom: 1px solid var(--bd-color);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; cursor: pointer; position: relative;
        }
        .m-lc-preview:hover::after {
            content: 'Abrir'; position: absolute; inset: 0;
            background: rgba(102,126,234,.13);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; color: var(--primary);
            border-radius: 10px 10px 0 0; pointer-events: none;
        }
        .m-lc-body { padding: 10px 12px 8px; }
        .m-lc-title { font-weight: 600; font-size: .88rem; color: var(--txt-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .m-lc-meta  { font-size: .7rem; color: var(--txt-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .m-lc-footer { display: flex; align-items: center; gap: 6px; padding: 8px 12px; border-top: 1px solid var(--bd-color); }
        .m-lc-btn-open { background: var(--primary); color: #fff; border: none; border-radius: 7px; padding: 5px 14px; font-size: .76rem; font-weight: 600; cursor: pointer; transition: opacity .15s; }
        .m-lc-btn-open:hover { opacity: .88; }
        .m-lc-icon-btn { background: none; border: 1px solid var(--bd-color); color: var(--txt-muted); border-radius: 7px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .85rem; transition: all .15s; flex-shrink: 0; }
        .m-lc-icon-btn:hover { border-color: var(--primary); color: var(--primary); background: rgba(102,126,234,.08); }
        .m-lc-dots-wrap { position: relative; margin-left: auto; }
        .m-lc-dropdown {
            position: absolute; bottom: calc(100% + 6px); right: 0;
            background: var(--bg-card); border: 1.5px solid var(--bd-color);
            border-radius: 10px; box-shadow: 0 8px 28px rgba(0,0,0,.18);
            min-width: 190px; z-index: 9999; overflow: hidden;
            animation: fadeInUpM .15s ease;
        }
        @keyframes fadeInUpM { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .m-dd-item { display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:.82rem;color:var(--txt-main);cursor:pointer;transition:background .1s;white-space:nowrap; }
        .m-dd-item:hover { background: var(--bg-hover); }
        .m-dd-item i { font-size:.95rem;width:16px;text-align:center;color:var(--txt-muted); }
        .m-dd-item.danger { color:#ef4444; }
        .m-dd-item.danger i { color:#ef4444; }
        .m-dd-sep { height:1px;background:var(--bd-color);margin:4px 0; }
        /* ── Picker de tipo de diagrama (maestro) ── */
        ._mTipoOpt, ._ndpMOpt {
            display:flex; align-items:center; gap:10px; padding:8px 10px;
            border-radius:8px; cursor:pointer;
            border:1.5px solid var(--bd-color); background:var(--bg-card);
            margin-bottom:5px; transition:border-color .15s, background .15s, box-shadow .15s;
        }
        ._mTipoOpt:hover, ._ndpMOpt:hover {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb),.06);
        }
        ._mTipoOpt.activo, ._ndpMOpt.activo {
            border-color: var(--primary) !important;
            background: rgba(var(--primary-rgb),.13) !important;
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb),.15);
        }
</style>
</head>
<body>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-person-badge-fill me-2"></i>Panel Maestro</h4>
        <small>DiagramasUML</small>
    </div>
    <div class="sidebar-user">
        <div class="avatar"><i class="bi bi-person-badge"></i></div>
        <div>
            <h6><?= htmlspecialchars(SessionManager::usuarioNombre()) ?></h6>
            <small><?= htmlspecialchars($_SESSION['email'] ?? '') ?></small>
        </div>
    </div>
    <nav>
        <div class="nav-section">Principal</div>
        <button class="nav-btn active" id="nav-inicio" onclick="showSection('inicio')"><i class="bi bi-speedometer2"></i> Inicio</button>
        <button class="nav-btn" id="nav-diagramas" onclick="showSection('diagramas')"><i class="bi bi-diagram-3"></i> Mis Diagramas</button>
        <button class="nav-btn" id="nav-proyectos" onclick="showSection('proyectos')"><i class="bi bi-folder2-open"></i> Proyectos</button>
        <button class="nav-btn" id="nav-observaciones" onclick="showSection('observaciones')"><i class="bi bi-chat-left-text"></i> Observaciones</button>
    </nav>
    <div class="sidebar-footer">
        <?php if (SessionManager::esAdmin()): ?>
        <a href="<?= BASE_URL ?>/admin" class="nav-btn text-decoration-none" style="color:rgba(255,255,255,.7)">
            <i class="bi bi-shield-fill-check"></i> Panel Admin
        </a>
        <?php endif; ?>
        <button class="nav-btn" onclick="abrirModalNuevoDiagrama()"><i class="bi bi-plus-square"></i> Nuevo Diagrama</button>
        <button class="nav-btn" onclick="toggleThemeDrawer()" style="color:rgba(255,255,255,.7)"><i class="bi bi-palette"></i> Colores &amp; Tema</button>
        <a href="<?= BASE_URL ?>/logout" class="nav-btn text-decoration-none" style="color:rgba(255,255,255,.7)"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
    </div>
</aside>
<!-- ══ THEME DRAWER ══ -->
<div id="themeDrawer" style="position:fixed;top:0;right:-340px;width:320px;height:100vh;background:var(--bg-card);border-left:1px solid var(--bd-color);z-index:9000;overflow-y:auto;transition:right .3s ease;padding:20px 16px;box-shadow:-6px 0 24px rgba(0,0,0,.5)">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <span style="color:var(--txt-main);font-weight:700;font-size:.95rem"><i class="bi bi-palette me-2" style="color:var(--primary)"></i>Apariencia</span>
        <button onclick="toggleThemeDrawer()" style="background:none;border:none;color:var(--txt-muted);font-size:1.2rem;cursor:pointer;padding:4px"><i class="bi bi-x-lg"></i></button>
    </div>
    <div id="maestroThemeContainer"></div>
</div>
<div id="themeOverlay" onclick="toggleThemeDrawer()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:8999"></div>

<!-- ══ MAIN ═════════════════════════════════════════════════ -->
<main class="main">
    <div class="page-header">
        <h2 id="pageTitle">Inicio</h2>
        <div class="d-flex align-items-center gap-3">
            <span class="badge-maestro"><i class="bi bi-person-badge-fill me-1"></i>Maestro</span>
            <!-- ── Campana de notificaciones (Maestro) ── -->
            <div style="position:relative">
                <button id="btnNotifM" onclick="toggleNotifPanelM()" title="Notificaciones"
                    style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:10px;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;transition:background .2s">
                    <i class="bi bi-bell" style="color:var(--txt-main);font-size:.95rem"></i>
                    <span id="notifBadgeM" style="display:none;position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;border-radius:50%;width:16px;height:16px;font-size:.6rem;font-weight:700;align-items:center;justify-content:center;line-height:1"></span>
                </button>
                <div id="notifPanelM" style="display:none;position:absolute;top:calc(100% + 8px);right:0;width:320px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.4);z-index:1001;max-height:440px;overflow-y:auto">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--bd-color);position:sticky;top:0;background:var(--bg-card)">
                        <span style="font-weight:700;font-size:.88rem;color:var(--txt-main)"><i class="bi bi-bell me-2" style="color:var(--primary)"></i>Notificaciones</span>
                        <button onclick="marcarTodasLeidasM()" style="background:none;border:none;color:var(--primary);font-size:.72rem;cursor:pointer">Marcar todas leídas</button>
                    </div>
                    <div id="notifListaM"><div style="text-align:center;padding:20px;color:var(--txt-muted);font-size:.82rem">Cargando...</div></div>
                </div>
            </div>
            <button class="btn-primary-m" onclick="abrirModalNuevoDiagrama()">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Diagrama
            </button>
        </div>
    </div>
    <div id="contentArea"></div>
</main>

<div id="toast-c"></div>

<!-- Modal Nuevo/Editar Grupo -->
<div class="modal fade" id="modalGrupo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-collection me-2"></i>Crear Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="gId">
                <div class="mb-3">
                    <label class="form-label">Nombre del grupo</label>
                    <input type="text" class="form-control" id="gNombre" placeholder="Ej: Programación 3A">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="gDescripcion" rows="2" placeholder="Breve descripción del grupo..."></textarea>
                </div>
                <div class="alert alert-info small py-2">
                    <i class="bi bi-info-circle me-1"></i> El código de acceso se genera automáticamente y los alumnos lo usan para unirse.
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-primary-m" onclick="guardarGrupo()"><i class="bi bi-check me-1"></i>Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Diagrama (Maestro) -->
<div class="modal fade" id="modalNuevoDiagrama" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiagramaTitulo"><i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama</h5>
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
                    <input type="hidden" id="mTipo" value="usecase">
                    <div id="mTipoPickerGrid" style="max-height:280px;overflow-y:auto;padding-right:4px">
                        <div style="text-align:center;padding:16px;color:var(--txt-muted);font-size:.82rem">
                            <div class="spinner-border spinner-border-sm me-2"></div>Cargando tipos...
                        </div>
                    </div>
                </div>
                <!-- Proyecto requerido -->
                <div class="mb-3" id="mProyectoWrap">
                    <label class="form-label"><i class="bi bi-diagram-3 me-1" style="color:var(--primary)"></i>Ligar a un proyecto <span class="text-danger fw-semibold">(requerido)</span></label>
                    <select class="form-select" id="mProyecto" onchange="document.getElementById('btnAccionDiagrama').disabled = !this.value;">
                        <option value="">— Selecciona un proyecto —</option>
                    </select>
                    <div class="form-text text-muted mt-1"><i class="bi bi-info-circle"></i>Selecciona un proyecto existente para crear el diagrama. Los diagramas libres no están permitidos.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-text-paragraph me-1"></i>Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="mDescripcion" rows="2" placeholder="Breve descripción..."></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label"><i class="bi bi-tags me-1"></i>Etiquetas <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="text" class="form-control" id="mEtiquetas" placeholder="proyecto, trabajo, clase">
                    <div class="form-text text-muted mt-1"><i class="bi bi-info-circle"></i> Separa con comas</div>
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-primary-m" id="btnAccionDiagrama" onclick="accionModalDiagrama()">
                    <i class="bi bi-pencil-square me-1"></i>Ir al Editor
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clipboard-plus me-2"></i>Asignar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Modo: grupo o proyecto -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Asignar a</label>
                    <div style="display:flex;gap:8px">
                        <button id="modoGrupoBtn" onclick="setModoTarea('grupo')" type="button"
                            style="flex:1;padding:10px;border-radius:10px;border:2px solid var(--primary);background:rgba(102,126,234,.15);color:var(--primary);font-weight:600;font-size:.85rem;cursor:pointer;transition:all .2s">
                            <i class="bi bi-collection me-2"></i>Grupo completo
                        </button>
                        <button id="modoProyBtn" onclick="setModoTarea('proyecto')" type="button"
                            style="flex:1;padding:10px;border-radius:10px;border:2px solid var(--bd-color);background:transparent;color:var(--txt-muted);font-weight:600;font-size:.85rem;cursor:pointer;transition:all .2s">
                            <i class="bi bi-diagram-3 me-2"></i>Proyecto específico
                        </button>
                    </div>
                </div>

                <!-- Selector grupo -->
                <div id="bloqueGrupo" class="mb-3">
                    <label class="form-label">Grupo</label>
                    <select class="form-select" id="tGrupo"></select>
                </div>

                <!-- Selector proyecto + miembro -->
                <div id="bloqueProyecto" class="mb-3" style="display:none">
                    <label class="form-label">Proyecto</label>
                    <select class="form-select" id="tProyecto" onchange="cargarMiembrosProyecto()"></select>
                    <div class="mt-2">
                        <label class="form-label">Asignar a <span class="text-muted fw-normal">(opcional — vacío = todo el equipo)</span></label>
                        <select class="form-select" id="tMiembro">
                            <option value="">— Todo el equipo —</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Título de la tarea</label>
                    <input type="text" class="form-control" id="tTitulo" placeholder="Ej: Diagrama de Clases del Sistema">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo de diagrama</label>
                    <select class="form-select" id="tTipo">
                        <optgroup label="── Estructurales ──────────────">
                        <option value="class">📦 Clases</option>
                        <option value="object">🗂️ Objetos</option>
                        <option value="package">📂 Paquetes</option>
                        <option value="composite">🔲 Estructura Compuesta</option>
                        <option value="component">🧩 Componentes</option>
                        <option value="deployment">🖥️ Despliegue</option>
                        <option value="profile">🏷️ Perfiles</option>
                        </optgroup>
                        <optgroup label="── Comportamiento ─────────────">
                        <option value="usecase">👤 Casos de Uso</option>
                        <option value="activity">⚡ Actividades</option>
                        <option value="state">🔄 Máquina de Estado</option>
                        </optgroup>
                        <optgroup label="── Interacción ────────────────">
                        <option value="sequence">↔️ Secuencia</option>
                        <option value="communication">💬 Comunicación</option>
                        <option value="timing">⏱️ Tiempos</option>
                        <option value="overview">🗺️ Descripción General</option>
                        </optgroup>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción / Instrucciones <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="tDescripcion" rows="3" placeholder="Explica qué debe hacer el alumno, qué elementos incluir, referencias, etc."></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">Fecha de entrega <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="datetime-local" class="form-control" id="tFechaEntrega">
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-primary-m" onclick="guardarTarea()"><i class="bi bi-check me-1"></i>Asignar tarea</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tarea de Proyecto -->
<div class="modal fade" id="modalTareaProyectoM" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clipboard-plus me-2"></i>Asignar tarea a proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="tTareaIdM" value="">
                <div class="mb-3">
                    <label class="form-label">Título de la tarea</label>
                    <input type="text" class="form-control" id="tTituloM" placeholder="Ej: Revisa el diagrama de clases">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="tDescripcionM" rows="3" placeholder="Instrucciones para el alumno o el equipo..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Asignar a</label>
                    <select class="form-select" id="tAsignadoM"></select>
                    <div class="form-text text-muted mt-1"><i class="bi bi-info-circle"></i>Selecciona un miembro para una tarea individual o deja en blanco para el equipo completo.</div>
                </div>
                <div class="mb-1">
                    <label class="form-label">Fecha de entrega <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="date" class="form-control" id="tFechaEntregaM">
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-primary-m" onclick="guardarTareaProyectoM()"><i class="bi bi-check me-1"></i>Guardar tarea</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= Assets::bootstrapJs() ?>"></script>
<script>window.BASE_URL = "<?= BASE_URL ?>";</script>
<script src="<?= Assets::url('js/user-theme.js') ?>"></script>
<script>
const MAESTRO_ID = <?= json_encode(SessionManager::usuarioId()) ?>;
const MAESTRO_ROL = <?= json_encode(SessionManager::usuarioRol()) ?>;
const esAdminG = MAESTRO_ROL === 'admin'; // admin global puede invitar en cualquier proyecto

// ── Iconos SVG por tipo de diagrama ──────────────────────────
const ICON_BASE = '<?= BASE_URL ?>/public/assets/img/iconos-uml/';
const TIPOS_SVG = {
    usecase:'usecase.svg', class:'class.svg', sequence:'sequence.svg',
    activity:'activity.svg', state:'state.svg', component:'component.svg',
    deployment:'deployment.svg', object:'object.svg', package:'package.svg',
    composite:'composite.svg', profile:'profile.svg', communication:'communication.svg',
    timing:'timing.svg', overview:'overview.svg'
};
function getTipoIconoSVG(tipo, size=40) {
    const file = TIPOS_SVG[tipo];
    if (!file) return `<span style="font-size:${size*0.06}rem;opacity:.4">📄</span>`;
    return `<img src="${ICON_BASE}${file}" width="${size}" height="${size}" style="object-fit:contain;display:block" alt="${tipo}">`;
}

const TIPOS = {usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',activity:'Actividades', state:'Máquina de Estado', component:'Componentes',deployment:'Despliegue', object:'Objetos', communication:'Comunicación',timing:'Tiempos', package:'Paquetes', composite:'Estructura Compuesta',profile:'Perfiles', overview:'Descripción General'};

// ── Toast ─────────────────────────────────────────────────────
function toast(msg, type='ok') {
    const el = document.createElement('div');
    el.className = `t-msg t-${type}`;
    el.innerHTML = `<i class="bi bi-${type==='ok'?'check-circle-fill':type==='err'?'x-circle-fill':'info-circle-fill'} me-2"></i>${msg}`;
    document.getElementById('toast-c').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

async function api(url, body=null) {
    const opts = body ? {
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
        credentials:'same-origin',
        body:JSON.stringify(body)
    } : {
        credentials:'same-origin',
        headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
    };
    const res  = await fetch(url, opts);
    const text = await res.text();
    if (!text.trim()) {
        if (res.redirected || res.url.includes('/login')) throw new Error('Sesión expirada. Vuelve a iniciar sesión.');
        throw new Error('Sin respuesta del servidor');
    }
    try { return JSON.parse(text); } catch { throw new Error('Respuesta inválida: '+text.substring(0,80)); }
}

function loading() {
    document.getElementById('contentArea').innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>`;
}

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function formatBytes(b) { if(!b||b===0)return'0 B';const k=1024,s=['B','KB','MB','GB'],i=Math.floor(Math.log(b)/Math.log(k));return parseFloat((b/Math.pow(k,i)).toFixed(1))+' '+s[i]; }

// ── Navegación ────────────────────────────────────────────────
const titles = { inicio:'Inicio', diagramas:'Mis Diagramas', proyectos:'Proyectos', observaciones:'Observaciones' };
const views  = { inicio:renderInicio, diagramas:renderDiagramas, proyectos:renderProyectosMaestro, observaciones:renderObservaciones };

function showSection(id) {
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('nav-'+id)?.classList.add('active');
    document.getElementById('pageTitle').textContent = titles[id] || id;
    if (views[id]) views[id]();
}

// ════════════════════════════════════════════════════════════
// INICIO — resumen orientado a proyectos
// ════════════════════════════════════════════════════════════
async function renderInicio() {
    loading();
    try {
        const [dataP, dataResp] = await Promise.all([
            api('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos'),
            api('<?= BASE_URL ?>/api/maestro?action=respuestas_recientes').catch(()=>({respuestas:[]}))
        ]);
        const proyectos = dataP.proyectos || [];
        const respRecientes = (dataResp.respuestas || []).slice(0, 5);

        // Contar diagramas totales y últimas actualizaciones
        const totalDiags = proyectos.reduce((s,p) => s + (p.num_diagramas||0), 0);
        const totalMiembros = proyectos.reduce((s,p) => s + (p.num_miembros||0), 0);

        // Widget de respuestas recientes
        const respWidget = respRecientes.length > 0 ? `
            <div class="sec-card mb-3">
                <div class="sec-header">
                    <i class="bi bi-chat-dots text-success"></i>
                    <h5>Respuestas recientes de alumnos</h5>
                    <span style="margin-left:6px;background:rgba(16,185,129,.15);color:#10b981;border-radius:10px;padding:1px 9px;font-size:.7rem;font-weight:700">${respRecientes.length}</span>
                    <button class="ms-auto btn-outline-m" style="font-size:.75rem" onclick="showSection('observaciones')">
                        <i class="bi bi-arrow-right me-1"></i>Ver todo
                    </button>
                </div>
                <div class="sec-body p-0">
                ${respRecientes.map(r => `
                    <div style="display:flex;gap:10px;padding:10px 16px;border-bottom:1px solid var(--bd-color);cursor:pointer;transition:background .15s"
                         onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background=''"
                         onclick="navegarAObservacionM(${r.proyecto_id},${r.diagrama_id||0},${r.padre_id||0})">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.75rem;color:#fff;flex-shrink:0">
                            ${esc((r.alumno_nombre||r.alumno_username||'?')[0].toUpperCase())}
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.8rem;font-weight:600;color:var(--txt-main)">${esc(r.alumno_nombre||r.alumno_username)}</div>
                            <div style="font-size:.73rem;color:var(--txt-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(r.texto)}</div>
                            <div style="font-size:.65rem;color:var(--txt-muted)">${esc(r.proyecto_nombre||'—')} · ${new Date(r.fecha_creacion).toLocaleString('es-MX',{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})}</div>
                        </div>
                        <i class="bi bi-chevron-right" style="color:var(--txt-muted);align-self:center;flex-shrink:0"></i>
                    </div>`).join('')}
                </div>
            </div>` : '';

        document.getElementById('contentArea').innerHTML = `
            <!-- Stats rápidos -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4"><div class="stat-card">
                    <div class="stat-icon" style="color:var(--primary)"><i class="bi bi-folder2-open"></i></div>
                    <div class="stat-num">${proyectos.length}</div>
                    <div class="stat-label">Proyectos activos</div>
                </div></div>
                <div class="col-6 col-md-4"><div class="stat-card">
                    <div class="stat-icon" style="color:#10b981"><i class="bi bi-diagram-3-fill"></i></div>
                    <div class="stat-num">${totalDiags}</div>
                    <div class="stat-label">Diagramas en proyectos</div>
                </div></div>
                <div class="col-6 col-md-4"><div class="stat-card">
                    <div class="stat-icon" style="color:#f59e0b"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-num">${totalMiembros}</div>
                    <div class="stat-label">Miembros en total</div>
                </div></div>
            </div>

            ${respWidget}

            <!-- Lista de proyectos recientes -->
            <div class="sec-card">
                <div class="sec-header">
                    <i class="bi bi-folder2-open text-primary"></i>
                    <h5>Mis Proyectos</h5>
                    <button class="ms-auto btn-outline-m" style="font-size:.75rem" onclick="showSection('proyectos')">
                        <i class="bi bi-arrow-right me-1"></i>Ver todos
                    </button>
                </div>
                <div class="sec-body p-0">
                ${proyectos.length === 0
                    ? `<div class="empty-state"><i class="bi bi-folder2-open"></i>
                        <p>No tienes proyectos aún</p>
                        <button class="btn-primary-m" onclick="showSection('proyectos')">Ir a Proyectos</button>
                       </div>`
                    : proyectos.map(p => `
                        <div class="list-row" style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-bottom:1px solid var(--bd-color);"
                             onclick="showSection('proyectos');setTimeout(()=>abrirProyectoM(${p.id}),300)">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="bi bi-folder2-open" style="color:#fff;font-size:1rem"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-weight:700;font-size:.9rem;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(p.nombre)}</div>
                                <div style="font-size:.73rem;color:var(--txt-muted)">
                                    <i class="bi bi-people me-1"></i>${p.num_miembros} miembro${p.num_miembros!=1?'s':''}
                                    &nbsp;·&nbsp;<i class="bi bi-diagram-3 me-1"></i>${p.num_diagramas} diagrama${p.num_diagramas!=1?'s':''}
                                    &nbsp;·&nbsp;<code style="font-size:.65rem;color:var(--primary)">${esc(p.codigo)}</code>
                                </div>
                            </div>
                            <span style="background:${p.rol==='owner'?'rgba(102,126,234,.15)':'rgba(16,185,129,.1)'};color:${p.rol==='owner'?'var(--primary)':'#10b981'};border-radius:8px;padding:2px 8px;font-size:.65rem;font-weight:700;flex-shrink:0">${p.rol==='owner'?'OWNER':'MIEMBRO'}</span>
                            <i class="bi bi-chevron-right" style="color:var(--txt-muted);flex-shrink:0"></i>
                        </div>`).join('')}
                </div>
            </div>`;
    } catch(e) {
        document.getElementById('contentArea').innerHTML = `<div class="sec-card"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>${esc(e.message)}</p></div></div>`;
    }
}

// ════════════════════════════════════════════════════════════
// GRUPOS
// ════════════════════════════════════════════════════════════
async function renderGrupos() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/maestro?action=grupos');
        document.getElementById('contentArea').innerHTML = `
            <div class="row g-3">
            ${(data.grupos||[]).length === 0
                ? `<div class="col-12"><div class="sec-card"><div class="empty-state">
                    <i class="bi bi-collection"></i><p>No tienes grupos aún</p>
                    <button class="btn-primary-m" onclick="abrirModalGrupo()"><i class="bi bi-plus-lg me-1"></i>Crear primer grupo</button>
                   </div></div></div>`
                : (data.grupos||[]).map(g => `
                    <div class="col-md-6 col-lg-4">
                        <div class="sec-card">
                            <div class="sec-header" style="justify-content:space-between">
                                <div><h5>${esc(g.nombre)}</h5><small class="text-muted">${g.num_alumnos} alumno${g.num_alumnos!=1?'s':''}</small></div>
                                <span class="code-badge" onclick="copiarCodigo('${esc(g.codigo)}')" title="Click para copiar">${esc(g.codigo)}</span>
                            </div>
                            <div class="sec-body">
                                ${g.descripcion ? `<p class="text-muted small mb-3">${esc(g.descripcion)}</p>` : ''}
                                <div class="d-flex gap-2">
                                    <button class="btn-outline-m flex-grow-1" onclick="verAlumnosGrupo(${g.id},'${esc(g.nombre)}')"><i class="bi bi-people me-1"></i>Alumnos</button>
                                    <button class="btn-sm-danger" onclick="eliminarGrupo(${g.id},'${esc(g.nombre)}')"><i class="bi bi-trash3"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>`).join('')}
            </div>`;
    } catch(e) { toast(e.message,'err'); }
}

async function verAlumnosGrupo(grupoId, nombre) {
    try {
        const data = await api(`<?= BASE_URL ?>/api/maestro?action=alumnos_grupo&grupo_id=${grupoId}`);
        document.getElementById('contentArea').innerHTML = `
            <button class="btn-outline-m mb-3" onclick="renderGrupos()"><i class="bi bi-arrow-left me-1"></i>Volver a Grupos</button>
            <div class="sec-card">
                <div class="sec-header"><i class="bi bi-people text-primary"></i><h5>Alumnos en: ${esc(nombre)}</h5>
                    <span class="ms-auto badge-tipo">${(data.alumnos||[]).length}</span>
                </div>
                <div class="sec-body p-0">
                    ${(data.alumnos||[]).length === 0
                        ? '<div class="empty-state"><i class="bi bi-people"></i><p>Ningún alumno en este grupo</p><small class="text-muted">Comparte el código del grupo para que se unan</small></div>'
                        : `<table class="t">
                            <thead><tr><th>Nombre</th><th>Usuario</th><th>Diagramas</th><th>Último acceso</th><th></th></tr></thead>
                            <tbody>
                            ${(data.alumnos||[]).map(a => `
                                <tr>
                                    <td><strong>${esc(a.nombre_completo||a.username)}</strong></td>
                                    <td class="text-muted">@${esc(a.username)}</td>
                                    <td><span class="badge-tipo">${a.num_diagramas||0}</span></td>
                                    <td class="text-muted" style="font-size:.78rem">${a.ultimo_acceso?new Date(a.ultimo_acceso).toLocaleDateString('es-MX'):'Nunca'}</td>
                                    <td><button class="btn-outline-m" style="font-size:.72rem;padding:3px 9px" onclick="verDiagramasAlumno(${a.id},'${esc(a.nombre_completo||a.username)}')">Ver diagramas</button></td>
                                </tr>`).join('')}
                            </tbody></table>`
                    }
                </div>
            </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// ALUMNOS
// ════════════════════════════════════════════════════════════
async function renderAlumnos() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/maestro?action=todos_alumnos');
        document.getElementById('contentArea').innerHTML = `
            <div class="sec-card">
                <div class="sec-header"><i class="bi bi-people text-primary"></i><h5>Todos mis Alumnos (${(data.alumnos||[]).length})</h5></div>
                <div class="sec-body p-0">
                    ${(data.alumnos||[]).length === 0
                        ? '<div class="empty-state"><i class="bi bi-people"></i><p>Aún no tienes alumnos en ningún grupo</p></div>'
                        : `<table class="t">
                            <thead><tr><th>Nombre</th><th>Usuario</th><th>Email</th><th>Grupo</th><th>Diagramas</th><th>Último acceso</th><th></th></tr></thead>
                            <tbody>
                            ${(data.alumnos||[]).map(a => `
                                <tr>
                                    <td><strong>${esc(a.nombre_completo||a.username)}</strong></td>
                                    <td class="text-muted">@${esc(a.username)}</td>
                                    <td class="text-muted">${esc(a.email)}</td>
                                    <td><span class="badge-tipo">${esc(a.grupo_nombre||'—')}</span></td>
                                    <td>${a.num_diagramas||0}</td>
                                    <td class="text-muted" style="font-size:.78rem">${a.ultimo_acceso?new Date(a.ultimo_acceso).toLocaleDateString('es-MX'):'Nunca'}</td>
                                    <td><button class="btn-outline-m" style="font-size:.72rem;padding:3px 9px" onclick="verDiagramasAlumno(${a.id},'${esc(a.nombre_completo||a.username)}')">Ver diagramas</button></td>
                                </tr>`).join('')}
                            </tbody></table>`
                    }
                </div>
            </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// MIS DIAGRAMAS (propios del maestro)
// ════════════════════════════════════════════════════════════
async function eliminarDiagramaMaestro(id, titulo) {
    if (!confirm(`¿Eliminar el diagrama "${titulo}"? Esta acción no se puede deshacer.`)) return;
    try {
        const r = await fetch('<?= BASE_URL ?>/api/diagramas/delete', {
            method:'POST', credentials:'same-origin',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({id})
        });
        const d = await r.json();
        if (d.success) { toast('Diagrama eliminado', 'ok'); renderDiagramas(); }
        else throw new Error(d.error||'Error al eliminar');
    } catch(e) { toast(e.message, 'error'); }
}

async function renderDiagramas() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/diagramas');
        const diagramas = data.diagramas || [];
        document.getElementById('contentArea').innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">${diagramas.length} diagrama${diagramas.length!=1?'s':''}</span>
                <button onclick="abrirModalNuevoDiagrama()" class="btn-primary-m" style="font-size:.85rem"><i class="bi bi-plus-lg me-1"></i>Nuevo Diagrama</button>
            </div>
            ${diagramas.length === 0
                ? '<div class="sec-card"><div class="empty-state"><i class="bi bi-diagram-3"></i><p>No tienes diagramas aún</p></div></div>'
                : `<div class="row g-3">
                    ${diagramas.map(d => renderMaestroCard(d)).join('')}
                   </div>`
            }`;
        // Reactivar dropdowns tras renderizar
        document.addEventListener('click', cerrarMDropdowns, {once:false});
        if (window.DiagramMiniRenderer) DiagramMiniRenderer.observeAll(document.getElementById('contentArea'));
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// DIAGRAMAS DE ALUMNOS
// ════════════════════════════════════════════════════════════
async function renderAlumnosDiagramas(alumnoId=null, alumnoNombre=null) {
    document.getElementById('pageTitle').textContent = alumnoNombre ? `Diagramas de ${alumnoNombre}` : 'Diagramas de Alumnos';
    loading();
    try {
        const url = alumnoId
            ? `<?= BASE_URL ?>/api/maestro?action=diagramas_alumno&alumno_id=${alumnoId}`
            : '<?= BASE_URL ?>/api/maestro?action=todos_diagramas_alumnos';
        const data = await api(url);
        const diagramas = data.diagramas || [];

        document.getElementById('contentArea').innerHTML = `
            ${alumnoId ? `<button class="btn-outline-m mb-3" onclick="renderAlumnosDiagramas()"><i class="bi bi-arrow-left me-1"></i>Todos los alumnos</button>` : ''}
            <div class="sec-card">
                <div class="sec-header"><i class="bi bi-diagram-3 text-primary"></i>
                    <h5>${alumnoNombre ? 'Diagramas de '+esc(alumnoNombre) : 'Todos los Diagramas de Alumnos'}</h5>
                    <span class="ms-auto text-muted small">${diagramas.length} diagrama${diagramas.length!=1?'s':''}</span>
                </div>
                <div class="sec-body p-0">
                    ${diagramas.length === 0
                        ? '<div class="empty-state"><i class="bi bi-diagram-3"></i><p>No hay diagramas que mostrar</p></div>'
                        : `<table class="t">
                            <thead><tr><th>Título</th>${!alumnoId?'<th>Alumno</th>':''}<th>Tipo</th><th>Versión</th><th>Modificado</th><th></th></tr></thead>
                            <tbody>
                            ${diagramas.map(d => `
                                <tr>
                                    <td><strong>${esc(d.titulo)}</strong></td>
                                    ${!alumnoId?`<td style="color:var(--primary)">${esc(d.nombre_alumno||d.username)}</td>`:''}
                                    <td><span class="badge-tipo">${TIPOS[d.tipo_diagrama]||d.tipo_diagrama}</span></td>
                                    <td>v${d.version}</td>
                                    <td class="text-muted" style="font-size:.78rem">${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</td>
                                    <td><a href="<?= BASE_URL ?>/editor?id=${d.id}" class="btn-outline-m text-decoration-none" style="font-size:.72rem;padding:3px 9px">Ver/Editar</a></td>
                                </tr>`).join('')}
                            </tbody></table>`
                    }
                </div>
            </div>`;
    } catch(e) { toast(e.message,'err'); }
}

function verDiagramasAlumno(id, nombre) {
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('nav-alumnos-diagramas')?.classList.add('active');
    renderAlumnosDiagramas(id, nombre);
}

// ════════════════════════════════════════════════════════════
// TAREAS
// ════════════════════════════════════════════════════════════
async function renderTareas() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/maestro?action=tareas');
        const tareas = data.tareas || [];

        document.getElementById('contentArea').innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3" style="flex-wrap:wrap;gap:10px">
                <div>
                    <h5 style="color:var(--txt-main);margin:0"><i class="bi bi-clipboard-check me-2" style="color:var(--primary)"></i>Tareas Asignadas <span style="color:var(--txt-muted);font-size:.8rem;font-weight:400">(${tareas.length})</span></h5>
                    <p style="color:var(--txt-muted);font-size:.75rem;margin:3px 0 0">Clic en una tarea para ver entregas y calificar alumnos</p>
                </div>
                <button class="btn-primary-m" onclick="abrirModalTarea()"><i class="bi bi-clipboard-plus me-1"></i>Nueva Tarea</button>
            </div>
            ${tareas.length === 0
                ? '<div class="empty-state"><i class="bi bi-clipboard-check"></i><p>No has asignado tareas aún</p></div>'
                : tareas.map(t => {
                    const vencida = t.fecha_entrega && new Date(t.fecha_entrega) < new Date();
                    const pct = t.total_alumnos > 0 ? Math.round((t.num_entregas||0)/t.total_alumnos*100) : 0;
                    const barColor = pct === 100 ? '#10b981' : pct > 50 ? '#f59e0b' : '#ef4444';
                    const esProyecto = !!t.proyecto_id;
                    const destino = esProyecto
                        ? `<span style="font-size:.73rem;color:var(--txt-muted)"><i class="bi bi-diagram-3 me-1"></i>${esc(t.proyecto_nombre||'Proyecto')}</span>`
                        : `<span style="font-size:.73rem;color:var(--txt-muted)"><i class="bi bi-collection me-1"></i>${esc(t.grupo_nombre||'—')}</span>`;
                    return `<div class="sec-card mb-2" style="cursor:pointer"
                                 onclick="verEntregasTarea(${t.id},'${esc(t.titulo)}')">
                        <div style="padding:14px 16px;display:flex;align-items:flex-start;gap:14px">
                            <div style="width:44px;height:44px;border-radius:10px;background:${esProyecto?'rgba(59,130,246,.15)':'rgba(102,126,234,.15)'};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="bi bi-clipboard" style="font-size:1.2rem;color:${esProyecto?'#60a5fa':'var(--primary)'}"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="color:var(--txt-main);font-weight:700;font-size:.92rem;margin-bottom:4px">${esc(t.titulo)}</div>
                                <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin-bottom:8px">
                                    ${destino}
                                    <span style="background:rgba(102,126,234,.15);color:#aab8ff;border-radius:10px;padding:1px 8px;font-size:.68rem">${TIPOS[t.tipo_diagrama]||t.tipo_diagrama}</span>
                                    ${t.alumno_nombre ? `<span style="font-size:.7rem;color:#a78bfa"><i class="bi bi-person me-1"></i>${esc(t.alumno_nombre)}</span>` : ''}
                                    ${t.fecha_entrega ? `<span style="font-size:.72rem;color:${vencida?'#fca5a5':'var(--txt-muted)'}"><i class="bi bi-calendar-event me-1"></i>${new Date(t.fecha_entrega).toLocaleDateString('es-MX')}</span>` : ''}
                                </div>
                                ${t.descripcion ? `<div style="color:var(--txt-muted);font-size:.73rem;margin-bottom:8px;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">${esc(t.descripcion)}</div>` : ''}
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="flex:1;background:#2a2a4a;border-radius:4px;height:5px;overflow:hidden">
                                        <div style="width:${pct}%;background:${barColor};height:100%;transition:width .4s"></div>
                                    </div>
                                    <span style="font-size:.72rem;color:${barColor};white-space:nowrap;font-weight:600">${t.num_entregas||0}/${t.total_alumnos||0} entregas</span>
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;align-items:flex-end">
                                <button class="btn-primary-m" style="font-size:.72rem;padding:5px 12px" onclick="event.stopPropagation();verEntregasTarea(${t.id},'${esc(t.titulo)}')">
                                    <i class="bi bi-eye me-1"></i>Ver entregas
                                </button>
                                <button class="btn-sm-danger" style="font-size:.72rem;padding:3px 8px" onclick="event.stopPropagation();eliminarTarea(${t.id},'${esc(t.titulo)}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
                }).join('')
            }`;
    } catch(e) { toast(e.message,'err'); }
}

async function verEntregasTarea(tareaId, titulo) {
    // Panel lateral de entregas
    document.getElementById('panelEntregas')?.remove();
    const panel = document.createElement('div');
    panel.id = 'panelEntregas';
    panel.innerHTML = `
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:8000" onclick="document.getElementById('panelEntregas').remove()"></div>
    <div style="position:fixed;top:0;right:0;width:min(600px,100vw);height:100vh;background:var(--bg-card);border-left:1px solid var(--bd-color);z-index:8001;overflow-y:auto;display:flex;flex-direction:column">
        <div style="position:sticky;top:0;background:var(--bg-card);border-bottom:1px solid var(--bd-color);padding:16px 20px;display:flex;align-items:center;gap:12px;z-index:1">
            <div style="flex:1;min-width:0">
                <div style="color:var(--txt-main);font-weight:700;font-size:.95rem">${esc(titulo)}</div>
                <div id="panelEntregasSubtitulo" style="color:var(--txt-muted);font-size:.75rem">Cargando…</div>
            </div>
            <button onclick="document.getElementById('panelEntregas').remove()" style="background:none;border:none;color:var(--txt-muted);font-size:1.2rem;cursor:pointer"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="entregasContent" style="padding:16px;flex:1">
            <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>`;
    document.body.appendChild(panel);

    try {
        const r = await api(`<?= BASE_URL ?>/api/maestro?action=ver_entregas&tarea_id=${tareaId}`);
        const entregas = r.entregas || [];
        const tarea    = r.tarea   || {};

        // Actualizar subtítulo con fuente (grupo o proyecto)
        const sub = document.getElementById('panelEntregasSubtitulo');
        if (sub) {
            sub.innerHTML = tarea.proyecto_nombre
                ? `<i class="bi bi-diagram-3 me-1"></i>Proyecto: <strong>${esc(tarea.proyecto_nombre)}</strong>${tarea.alumno_id ? ' · individual' : ' · equipo completo'}`
                : `<i class="bi bi-collection me-1"></i>Grupo: <strong>${esc(tarea.grupo_nombre||'—')}</strong>`;
        }

        const entregadas = entregas.filter(e => e.entrega_id);
        const sin = entregas.filter(e => !e.entrega_id);
        const calificadas = entregadas.filter(e=>e.calificacion!=null);
        const pendientesCalif = entregadas.filter(e=>e.calificacion==null);

        let currentTab = 'todas';
        
        const renderContent = (tab) => {
            const entregas_a_mostrar = {
                todas: entregas,
                entregadas: entregadas,
                calificadas: calificadas,
                pendientes: sin
            }[tab] || entregas;
            
            return `
        <!-- Tabs -->
        <div style="display:flex;gap:4px;margin-bottom:12px;border-bottom:1px solid var(--bd-color);padding-bottom:8px">
            <button onclick="window._tabEntregasActual='todas';updateEntregasTab(${tareaId},'todas')"
                id="tabEnt_todas"
                style="flex:1;padding:8px 10px;border-radius:0;background:none;border:none;border-bottom:3px solid ${tab==='todas'?'var(--primary)':'transparent'};color:${tab==='todas'?'var(--primary)':'var(--txt-muted)'};cursor:pointer;font-size:.8rem;font-weight:600;transition:all .2s">
                Todas (${entregas.length})
            </button>
            <button onclick="window._tabEntregasActual='entregadas';updateEntregasTab(${tareaId},'entregadas')"
                id="tabEnt_entregadas"
                style="flex:1;padding:8px 10px;border-radius:0;background:none;border:none;border-bottom:3px solid ${tab==='entregadas'?'var(--primary)':'transparent'};color:${tab==='entregadas'?'var(--primary)':'var(--txt-muted)'};cursor:pointer;font-size:.8rem;font-weight:600;transition:all .2s">
                Entregadas (${entregadas.length})
            </button>
            <button onclick="window._tabEntregasActual='calificadas';updateEntregasTab(${tareaId},'calificadas')"
                id="tabEnt_calificadas"
                style="flex:1;padding:8px 10px;border-radius:0;background:none;border:none;border-bottom:3px solid ${tab==='calificadas'?'var(--primary)':'transparent'};color:${tab==='calificadas'?'var(--primary)':'var(--txt-muted)'};cursor:pointer;font-size:.8rem;font-weight:600;transition:all .2s">
                Calificas (${calificadas.length})
            </button>
            <button onclick="window._tabEntregasActual='pendientes';updateEntregasTab(${tareaId},'pendientes')"
                id="tabEnt_pendientes"
                style="flex:1;padding:8px 10px;border-radius:0;background:none;border:none;border-bottom:3px solid ${tab==='pendientes'?'var(--primary)':'transparent'};color:${tab==='pendientes'?'var(--primary)':'var(--txt-muted)'};cursor:pointer;font-size:.8rem;font-weight:600;transition:all .2s">
                Pendientes (${sin.length})
            </button>
        </div>

        <!-- Stats -->
        <div style="display:flex;gap:8px;margin-bottom:16px">
            <div style="flex:1;background:var(--bg-hover);border-radius:8px;padding:10px;text-align:center;border:1px solid var(--bd-color)">
                <div style="font-size:1.3rem;font-weight:700;color:#10b981">${entregadas.length}</div>
                <div style="font-size:.7rem;color:var(--txt-muted)">Entregadas</div>
            </div>
            <div style="flex:1;background:var(--bg-hover);border-radius:8px;padding:10px;text-align:center;border:1px solid var(--bd-color)">
                <div style="font-size:1.3rem;font-weight:700;color:#ef4444">${sin.length}</div>
                <div style="font-size:.7rem;color:var(--txt-muted)">Sin entregar</div>
            </div>
            <div style="flex:1;background:var(--bg-hover);border-radius:8px;padding:10px;text-align:center;border:1px solid var(--bd-color)">
                <div style="font-size:1.3rem;font-weight:700;color:#f59e0b">${calificadas.length}</div>
                <div style="font-size:.7rem;color:var(--txt-muted)">Calificadas</div>
            </div>
        </div>

        <!-- Lista de alumnos -->
        ${entregas_a_mostrar.length === 0 ? `
            <div style="text-align:center;padding:40px 20px;color:var(--txt-muted)">
                <i class="bi bi-inbox" style="font-size:2rem;opacity:.5;display:block;margin-bottom:10px"></i>
                <p>No hay entregas en esta categoría</p>
            </div>
        ` : entregas_a_mostrar.map(e => {
            const entregada = !!e.entrega_id;
            const calif = e.calificacion != null;
            const color = !entregada ? '#ef4444' : calif ? '#10b981' : '#f59e0b';
            const icono = !entregada ? 'x-circle' : calif ? 'check-circle-fill' : 'clock';
            return `<div style="background:var(--bg-hover);border:1px solid ${color}33;border-radius:10px;padding:14px 16px;margin-bottom:10px">
                <div style="display:flex;align-items:flex-start;gap:12px">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary2));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.9rem;flex-shrink:0">
                        ${(e.nombre_completo||e.username||'?')[0].toUpperCase()}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;color:var(--txt-main);font-size:.88rem">${esc(e.nombre_completo||e.username)}</div>
                        <div style="font-size:.72rem;color:var(--txt-muted);margin-bottom:6px">@${esc(e.username)}</div>
                        ${entregada ? `
                            ${e.diagrama_titulo ? `<div style="font-size:.75rem;color:#aab8ff;margin-bottom:4px"><i class="bi bi-diagram-3 me-1"></i>${esc(e.diagrama_titulo)}</div>` : ''}
                            ${e.comentario_alumno ? `<div style="font-size:.73rem;color:var(--txt-main);background:rgba(255,255,255,.04);border-radius:6px;padding:4px 8px;margin-bottom:6px;border-left:3px solid var(--primary)"><i class="bi bi-chat-left me-1"></i>${esc(e.comentario_alumno)}</div>` : ''}
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                ${e.diagrama_id ? `<a href="<?= BASE_URL ?>/editor?id=${e.diagrama_id}" target="_blank" style="font-size:.73rem;color:var(--primary);text-decoration:none"><i class="bi bi-box-arrow-up-right me-1"></i>Abrir diagrama</a>` : ''}
                                <span style="font-size:.7rem;color:var(--txt-muted)">${e.fecha_entrega ? new Date(e.fecha_entrega).toLocaleDateString('es-MX',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : ''}</span>
                            </div>
                            <!-- Calificación -->
                            <div style="margin-top:10px;background:var(--bg-card);border-radius:8px;padding:10px;border:1px solid var(--bd-color)">
                                <div style="display:flex;gap:8px;align-items:flex-end">
                                    <div style="flex:1">
                                        <label style="font-size:.7rem;color:var(--txt-muted);display:block;margin-bottom:3px">Calificación (0–100)</label>
                                        <input type="number" id="cal_${e.alumno_id}" min="0" max="100" step="0.5"
                                            value="${e.calificacion ?? ''}" placeholder="—"
                                            style="width:100%;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:6px;color:var(--txt-main);padding:6px 10px;font-size:.85rem">
                                    </div>
                                    <div style="flex:2">
                                        <label style="font-size:.7rem;color:var(--txt-muted);display:block;margin-bottom:3px">Comentario</label>
                                        <input type="text" id="com_${e.alumno_id}"
                                            value="${esc(e.comentario_maestro||'')}" placeholder="Retroalimentación..."
                                            style="width:100%;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:6px;color:var(--txt-main);padding:6px 10px;font-size:.82rem">
                                    </div>
                                    <button onclick="calificarEntrega(${tareaId},${e.alumno_id})"
                                        style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:6px;padding:7px 12px;font-size:.78rem;cursor:pointer;white-space:nowrap;flex-shrink:0">
                                        <i class="bi bi-check me-1"></i>Guardar
                                    </button>
                                </div>
                            </div>
                        ` : `<div style="font-size:.75rem;color:var(--c-danger);padding:4px 0"><i class="bi bi-x-circle me-1"></i>No ha entregado</div>`}
                    </div>
                    <i class="bi bi-${icono}" style="color:${color};font-size:1rem;flex-shrink:0;margin-top:2px"></i>
                </div>
            </div>`;
        }).join('')}`;
        };
        
        document.getElementById('entregasContent').innerHTML = renderContent('todas');
        window._entregasData = {tareaId, entregas, entregadas, sin, calificadas, pendientesCalif};
        window._renderEntregasContent = renderContent;
        
    } catch(e) {
        document.getElementById('entregasContent').innerHTML = `<p class="text-danger">${esc(e.message)}</p>`;
    }
}

function updateEntregasTab(tareaId, tab) {
    if (window._renderEntregasContent) {
        document.getElementById('entregasContent').innerHTML = window._renderEntregasContent(tab);
    }
}

async function calificarEntrega(tareaId, alumnoId) {
    const cal = document.getElementById('cal_'+alumnoId)?.value;
    const com = document.getElementById('com_'+alumnoId)?.value || '';
    if (cal === '' || cal === null) { toast('Ingresa una calificación','info'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/maestro?action=calificar_entrega', {
            tarea_id: tareaId, alumno_id: alumnoId,
            calificacion: parseFloat(cal), comentario: com.trim()
        });
        if (r.success) toast(`Calificación guardada: ${parseFloat(cal).toFixed(1)}`,'ok');
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

// ── CRUD Grupos ───────────────────────────────────────────────
// ════════════════════════════════════════════════════════════
// MODAL NUEVO DIAGRAMA (igual que alumno)
// ════════════════════════════════════════════════════════════
let _modalNuevoDiag = null;
async function abrirModalNuevoDiagrama(editId = null, d = null, proyectoPreseleccionado = null) {
    if (!_modalNuevoDiag) _modalNuevoDiag = new bootstrap.Modal(document.getElementById('modalNuevoDiagrama'));
    document.getElementById('mEditId').value      = editId || '';
    document.getElementById('mTitulo').value      = d ? d.titulo : '';
    document.getElementById('mTipo').value        = d ? d.tipo_diagrama : 'usecase';
    document.getElementById('mDescripcion').value = d ? (d.descripcion||'') : '';
    document.getElementById('mEtiquetas').value   = d ? (d.etiquetas||'') : '';
    document.getElementById('modalDiagramaTitulo').innerHTML = editId
        ? '<i class="bi bi-pencil me-2"></i>Editar Diagrama'
        : '<i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama';
    document.getElementById('btnAccionDiagrama').innerHTML = editId
        ? '<i class="bi bi-save me-1"></i>Guardar Cambios'
        : '<i class="bi bi-pencil-square me-1"></i>Ir al Editor';

    // ── Renderizar picker visual SVG igual que alumno ──────────────────
    const tipoActual = d ? d.tipo_diagrama : 'usecase';
    const mGrid = document.getElementById('mTipoPickerGrid');
    if (mGrid) {
        const grupos = [
            { cat: 'Estructurales',  tipos: ['class','object','package','composite','component','deployment','profile'] },
            { cat: 'Comportamiento', tipos: ['usecase','activity','state'] },
            { cat: 'Interacción',    tipos: ['sequence','communication','timing','overview'] },
        ];
        const TIPO_LABELS = {class:'Clases',object:'Objetos',package:'Paquetes',composite:'Estructura Compuesta',component:'Componentes',deployment:'Despliegue',profile:'Perfiles',usecase:'Casos de Uso',activity:'Actividades',state:'Máquina de Estado',sequence:'Secuencia',communication:'Comunicación',timing:'Tiempos',overview:'Descripción General'};
        let pickerHtml = '';
        grupos.forEach(function(g) {
            pickerHtml += '<div style="font-size:.67rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.07em;padding:7px 0 3px">' + g.cat + '</div>';
            g.tipos.forEach(function(tipo) {
                const icon = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(tipo, 32) : '';
                pickerHtml += '<div class="_mTipoOpt" data-tipo="' + tipo + '"'
                    + ' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;'
                    + 'border:1.5px solid var(--bd-color);background:var(--bg-card);margin-bottom:5px;transition:all .15s">'
                    + '<div style="width:32px;height:32px;flex-shrink:0">' + icon + '</div>'
                    + '<span style="font-size:.81rem;font-weight:600;color:var(--txt-main)">' + (TIPO_LABELS[tipo]||tipo) + '</span>'
                    + '</div>';
            });
        });
        mGrid.innerHTML = pickerHtml;
        mGrid.querySelectorAll('._mTipoOpt').forEach(function(el) {
            el.addEventListener('click', function() { seleccionarTipoMaestro(this.dataset.tipo); });
        });
        seleccionarTipoMaestro(tipoActual);
    }

    // Cargar proyectos disponibles en el selector (solo para nuevo diagrama)
    const sel  = document.getElementById('mProyecto');
    const wrap = document.getElementById('mProyectoWrap');
    if (sel && !editId) {
        sel.innerHTML = '<option value="">— Selecciona un proyecto —</option>';
        try {
            const data = await api('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos');
            const proyectos = data.proyectos || [];
            if (proyectos.length > 0) {
                proyectos.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nombre;
                    sel.appendChild(opt);
                });
                sel.disabled = false;
                if (wrap) wrap.style.display = '';
            } else {
                sel.innerHTML = '<option value="">— No hay proyectos disponibles —</option>';
                sel.disabled = true;
                if (wrap) wrap.style.display = '';
            }
        } catch(_) {
            sel.innerHTML = '<option value="">— No se pudo cargar proyectos —</option>';
            sel.disabled = true;
            if (wrap) wrap.style.display = '';
        }
        if (proyectoPreseleccionado) sel.value = proyectoPreseleccionado;
    } else if (wrap) {
        // Al editar no mostramos el selector de proyecto
        wrap.style.display = 'none';
    }

    if (document.getElementById('btnAccionDiagrama')) {
        document.getElementById('btnAccionDiagrama').disabled = editId ? false : (!sel?.value || sel?.disabled || false);
    }

    _modalNuevoDiag.show();
}

// ── Selector de tipo para modal maestro ──────────────────────────────
function seleccionarTipoMaestro(val) {
    const el = document.getElementById('mTipo');
    if (el) el.value = val;
    document.querySelectorAll('._mTipoOpt').forEach(function(e) {
        e.classList.toggle('activo', e.dataset.tipo === val);
    });
}

async function accionModalDiagrama() {
    const titulo = document.getElementById('mTitulo').value.trim();
    if (!titulo) { toast('El título no puede estar vacío','info'); return; }
    const tipo        = document.getElementById('mTipo').value;
    const descripcion = document.getElementById('mDescripcion').value;
    const etiquetas   = document.getElementById('mEtiquetas').value;
    const editId      = document.getElementById('mEditId').value;
    const proyectoId  = document.getElementById('mProyecto')?.value || '';

    if (editId) {
        try {
            const data = await api('<?= BASE_URL ?>/api/diagramas/save', { id:editId, titulo, tipo, descripcion, etiquetas, contenido:[] });
            if (data.success) { toast('Diagrama actualizado','ok'); _modalNuevoDiag.hide(); renderDiagramas(); }
            else throw new Error(data.error||'Error al guardar');
        } catch(e) { toast(e.message,'err'); }
    } else {
        if (!proyectoId) {
            toast('Debes seleccionar un proyecto antes de crear el diagrama','info');
            return;
        }
        const diagramaData = { titulo, tipo, descripcion, etiquetas };
        if (proyectoId) diagramaData._projectId = proyectoId;
        sessionStorage.setItem('nuevoDiagrama', JSON.stringify(diagramaData));
        _modalNuevoDiag.hide();
        const url = proyectoId
            ? '<?= BASE_URL ?>/editor?tipo=' + tipo + '&proyecto=' + proyectoId
            : '<?= BASE_URL ?>/editor?tipo=' + tipo;
        window.location.href = url;
    }
}

function abrirModalGrupo() {
    document.getElementById('gId').value = '';
    document.getElementById('gNombre').value = '';
    document.getElementById('gDescripcion').value = '';
    new bootstrap.Modal(document.getElementById('modalGrupo')).show();
}

async function guardarGrupo() {
    const nombre = document.getElementById('gNombre').value.trim();
    if (!nombre) { toast('El nombre del grupo es obligatorio','info'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/maestro?action=crear_grupo', {
            nombre, descripcion: document.getElementById('gDescripcion').value.trim()
        });
        if (r.success) {
            toast(`Grupo "${nombre}" creado. Código: ${r.codigo}`,'ok');
            bootstrap.Modal.getInstance(document.getElementById('modalGrupo')).hide();
            renderGrupos();
        } else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

async function eliminarGrupo(id, nombre) {
    if (!confirm(`¿Eliminar el grupo "${nombre}"? Los alumnos no serán eliminados.`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/maestro?action=eliminar_grupo', { id });
        if (r.success) { toast('Grupo eliminado','ok'); renderGrupos(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

// ── CRUD Tareas ───────────────────────────────────────────────
// ── Estado del modal de tarea ──────────────────────────────
let _modoTarea = 'grupo'; // 'grupo' | 'proyecto'

function setModoTarea(modo) {
    _modoTarea = modo;
    const esGrupo = modo === 'grupo';
    document.getElementById('bloqueGrupo').style.display    = esGrupo ? '' : 'none';
    document.getElementById('bloqueProyecto').style.display = esGrupo ? 'none' : '';
    // estilos botones
    const primStyle  = 'border:2px solid var(--primary);background:rgba(102,126,234,.15);color:var(--primary)';
    const secStyle   = 'border:2px solid var(--bd-color);background:transparent;color:var(--txt-muted)';
    document.getElementById('modoGrupoBtn').style.cssText += ';' + (esGrupo ? primStyle : secStyle);
    document.getElementById('modoProyBtn').style.cssText  += ';' + (esGrupo ? secStyle : primStyle);
}

async function cargarMiembrosProyecto() {
    const proyId = document.getElementById('tProyecto').value;
    const sel = document.getElementById('tMiembro');
    sel.innerHTML = '<option value="">— Todo el equipo —</option>';
    if (!proyId) return;
    try {
        const r = await api(`<?= BASE_URL ?>/api/proyectos?action=miembros&proyecto_id=${proyId}`);
        (r.miembros || []).forEach(m => {
            const o = document.createElement('option');
            o.value = m.id;
            o.textContent = `${m.nombre_completo || m.username} (@${m.username})`;
            sel.appendChild(o);
        });
    } catch(e) {}
}

async function abrirModalTarea() {
    // Cargar grupos
    const data = await api('<?= BASE_URL ?>/api/maestro?action=grupos');
    const sel  = document.getElementById('tGrupo');
    sel.innerHTML = (data.grupos||[]).map(g => `<option value="${g.id}">${esc(g.nombre)}</option>`).join('') || '<option disabled>Sin grupos</option>';
    // Cargar proyectos
    try {
        const dp = await api('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos');
        const sp = document.getElementById('tProyecto');
        sp.innerHTML = '<option value="">— Selecciona un proyecto —</option>' +
            (dp.proyectos||[]).map(p => `<option value="${p.id}">${esc(p.nombre)}</option>`).join('');
    } catch(e) {}
    // Reset form
    document.getElementById('tTitulo').value = '';
    document.getElementById('tDescripcion').value = '';
    document.getElementById('tFechaEntrega').value = '';
    document.getElementById('tMiembro').innerHTML = '<option value="">— Todo el equipo —</option>';
    setModoTarea('grupo');
    new bootstrap.Modal(document.getElementById('modalTarea')).show();
}

async function guardarTarea() {
    const titulo = document.getElementById('tTitulo').value.trim();
    if (!titulo) { toast('El título es obligatorio','info'); return; }

    const payload = {
        titulo,
        tipo_diagrama: document.getElementById('tTipo').value,
        descripcion: document.getElementById('tDescripcion').value.trim(),
        fecha_entrega: document.getElementById('tFechaEntrega').value,
        modo: _modoTarea,
    };

    if (_modoTarea === 'grupo') {
        payload.grupo_id = document.getElementById('tGrupo').value;
        if (!payload.grupo_id) { toast('Selecciona un grupo','info'); return; }
    } else {
        payload.proyecto_id = document.getElementById('tProyecto').value;
        payload.alumno_id   = document.getElementById('tMiembro').value || null;
        if (!payload.proyecto_id) { toast('Selecciona un proyecto','info'); return; }
    }

    try {
        const r = await api('<?= BASE_URL ?>/api/maestro?action=crear_tarea', payload);
        if (r.success) {
            toast('Tarea asignada correctamente','ok');
            bootstrap.Modal.getInstance(document.getElementById('modalTarea')).hide();
            renderTareas();
        } else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

async function eliminarTarea(id, titulo) {
    if (!confirm(`¿Eliminar la tarea "${titulo}"?`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/maestro?action=eliminar_tarea', { id });
        if (r.success) { toast('Tarea eliminada','ok'); renderTareas(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

function copiarCodigo(codigo) {
    navigator.clipboard.writeText(codigo).then(() => toast('Código copiado: '+codigo,'ok'));
}

function getTipoEmoji(tipo) {
    const map = {
        usecase:'👤', class:'📦', sequence:'↔️', activity:'⚡',
        state:'🔄', component:'🧩', deployment:'🖥️', object:'🗂️',
        communication:'💬', timing:'⏱️', package:'📂',
        composite:'🔲', profile:'🏷️', overview:'🗺️'
    };
    return map[tipo] || '📄';
}

// ════════════════════════════════════════════════════════════
// PROYECTOS COLABORATIVOS (igual que alumno, pero vista maestro)
// ════════════════════════════════════════════════════════════
// ── Constantes de tipos de diagrama para proyectos ─────────────────────
// ══════════════════════════════════════════════
// MAESTRO — LucidChart-style card + dropdown
// ══════════════════════════════════════════════
let _mActiveDD = null;
function cerrarMDropdowns(e) {
    if (_mActiveDD && !_mActiveDD.contains(e.target)) {
        _mActiveDD.querySelector('.m-lc-dropdown')?.remove();
        _mActiveDD = null;
    }
}
document.addEventListener('click', cerrarMDropdowns);

function toggleMMaestroDD(e, id, titulo) {
    e.stopPropagation();
    const wrap = e.currentTarget.closest('.m-lc-dots-wrap');
    if (_mActiveDD === wrap) {
        wrap.querySelector('.m-lc-dropdown')?.remove();
        _mActiveDD = null;
        return;
    }
    if (_mActiveDD) { _mActiveDD.querySelector('.m-lc-dropdown')?.remove(); }
    _mActiveDD = wrap;
    const tEsc = titulo.replace(/'/g, "\'");
    const dd = document.createElement('div');
    dd.className = 'm-lc-dropdown';
    dd.innerHTML = `
        <div class="m-dd-item" onclick="saveMNavState();window.location.href='<?= BASE_URL ?>/editor?id=${id}'">
            <i class="bi bi-pencil-square"></i> Abrir en editor
        </div>
        <div class="m-dd-item" onclick="duplicarDiagramaMaestro(${id}, '${tEsc}')">
            <i class="bi bi-files"></i> Hacer una copia
        </div>
        <div class="m-dd-item" onclick="renombrarDiagramaMaestro(${id}, '${tEsc}')">
            <i class="bi bi-cursor-text"></i> Renombrar
        </div>
        <div class="m-dd-sep"></div>
        <div class="m-dd-item danger" onclick="eliminarDiagramaMaestro(${id}, '${tEsc}')">
            <i class="bi bi-trash3"></i> Mover a la papelera
        </div>`;
    wrap.appendChild(dd);
}

async function renombrarDiagramaMaestro(id, tituloActual) {
    if (_mActiveDD) { _mActiveDD.querySelector('.m-lc-dropdown')?.remove(); _mActiveDD = null; }
    const nuevoTitulo = prompt('Nuevo nombre del diagrama:', tituloActual);
    if (!nuevoTitulo || nuevoTitulo.trim() === tituloActual) return;
    try {
        const data = await api('<?= BASE_URL ?>/api/diagramas/rename', { id, titulo: nuevoTitulo.trim() });
        if (data.success) { toast(`Renombrado a "${data.titulo}"`, 'ok'); renderDiagramas(); }
        else throw new Error(data.error || 'Error al renombrar');
    } catch(e) { toast(e.message, 'err'); }
}

async function duplicarDiagramaMaestro(id, titulo) {
    if (_mActiveDD) { _mActiveDD.querySelector('.m-lc-dropdown')?.remove(); _mActiveDD = null; }
    toast('Duplicando…', 'info');
    try {
        const data = await api('<?= BASE_URL ?>/api/diagramas/duplicate', { id });
        if (data.success) { toast(`Copia de "${titulo}" creada`, 'ok'); renderDiagramas(); }
        else throw new Error(data.error || 'Error al duplicar');
    } catch(e) { toast(e.message, 'err'); }
}

function renderMaestroCard(d) {
    const fecha    = new Date(d.fecha_modificacion || d.fecha_creacion).toLocaleDateString('es-MX');
    const tipoLabel = TIPOS_P_M[d.tipo_diagrama] || d.tipo_diagrama;
    return `
        <div class="col-sm-6 col-lg-4">
            <div class="m-diagram-card">
                <div class="m-lc-preview" data-preview-id="${d.id}" onclick="saveMNavState();window.location.href='<?= BASE_URL ?>/editor?id=${d.id}'" title="Abrir en editor">
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;opacity:0.3">
                        ${getTipoIconoSVG(d.tipo_diagrama, 44)}
                    </div>
                </div>
                <div class="m-lc-body">
                    <div class="m-lc-title" title="${esc(d.titulo)}">${esc(d.titulo)}</div>
                    <div class="m-lc-meta">
                        <span style="display:inline-flex;align-items:center;gap:4px">${getTipoIconoSVG(d.tipo_diagrama,12)}&nbsp;${tipoLabel}</span>
                        &nbsp;·&nbsp;${fecha}
                    </div>
                </div>
                <div class="m-lc-footer">
                    <button class="m-lc-btn-open" onclick="saveMNavState();window.location.href='<?= BASE_URL ?>/editor?id=${d.id}'">Abrir</button>
                    <div class="m-lc-dots-wrap" onclick="event.stopPropagation()">
                        <button class="m-lc-icon-btn" title="Más opciones" onclick="toggleMMaestroDD(event, ${d.id}, '${esc(d.titulo)}')">
                            <i class="bi bi-three-dots"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
}

function renderMaestroPreview(d) {
    let nodes = [], arrows = [];
    try {
        const raw = d.contenido_json_preview || '';
        if (raw) { const p = JSON.parse(raw); nodes = p.nodes||p.nodos||[]; arrows = p.arrows||p.flechas||p.connections||[]; }
    } catch(_) {}
    if (!nodes.length) return getTipoIconoSVG(d.tipo_diagrama, 52);
    const W=280,H=110,pad=12;
    let minX=Infinity,minY=Infinity,maxX=-Infinity,maxY=-Infinity;
    nodes.forEach(n=>{const x=parseFloat(n.x||n.left||0),y=parseFloat(n.y||n.top||0),w=parseFloat(n.width||80),h=parseFloat(n.height||40);if(x<minX)minX=x;if(y<minY)minY=y;if(x+w>maxX)maxX=x+w;if(y+h>maxY)maxY=y+h;});
    const scale=Math.min((W-pad*2)/Math.max(1,maxX-minX),(H-pad*2)/Math.max(1,maxY-minY),1.4);
    const offX=pad-minX*scale,offY=pad-minY*scale;
    const col={usecase:'#6366f1',class:'#0891b2',sequence:'#8b5cf6',activity:'#059669',state:'#d97706',component:'#db2777',deployment:'#7c3aed',object:'#0284c7',communication:'#16a34a',timing:'#ca8a04',package:'#9333ea',composite:'#0f766e',profile:'#b45309',overview:'#1d4ed8'}[d.tipo_diagrama]||'#6366f1';
    let ns='';
    nodes.forEach(n=>{
        const x=parseFloat(n.x||n.left||0)*scale+offX,y=parseFloat(n.y||n.top||0)*scale+offY;
        const w=Math.max(parseFloat(n.width||80)*scale,16),h=Math.max(parseFloat(n.height||40)*scale,10);
        const fs=Math.max(5,Math.min(9,h*0.35)),lbl=(n.text||n.label||'').substring(0,15).replace(/</g,'&lt;');
        const tipo=n.type||n.tipo||d.tipo_diagrama;
        if(tipo==='actor'){const cx=x+w/2,r=Math.max(h*.18,4);ns+=`<circle cx='${cx.toFixed(1)}' cy='${(y+r).toFixed(1)}' r='${r.toFixed(1)}' fill='none' stroke='${col}' stroke-width='1.2'/><line x1='${cx.toFixed(1)}' y1='${(y+r*2).toFixed(1)}' x2='${cx.toFixed(1)}' y2='${(y+h*.72).toFixed(1)}' stroke='${col}' stroke-width='1.2'/><line x1='${(cx-w*.22).toFixed(1)}' y1='${(y+h*.4).toFixed(1)}' x2='${(cx+w*.22).toFixed(1)}' y2='${(y+h*.4).toFixed(1)}' stroke='${col}' stroke-width='1.2'/><line x1='${cx.toFixed(1)}' y1='${(y+h*.72).toFixed(1)}' x2='${(cx-w*.22).toFixed(1)}' y2='${(y+h).toFixed(1)}' stroke='${col}' stroke-width='1.2'/><line x1='${cx.toFixed(1)}' y1='${(y+h*.72).toFixed(1)}' x2='${(cx+w*.22).toFixed(1)}' y2='${(y+h).toFixed(1)}' stroke='${col}' stroke-width='1.2'/>`;
        } else if(tipo==='usecase'||tipo==='ellipse'){ns+=`<ellipse cx='${(x+w/2).toFixed(1)}' cy='${(y+h/2).toFixed(1)}' rx='${(w/2).toFixed(1)}' ry='${(h/2).toFixed(1)}' fill='${col}22' stroke='${col}' stroke-width='1.2'/><text x='${(x+w/2).toFixed(1)}' y='${(y+h/2+fs*.35).toFixed(1)}' text-anchor='middle' font-size='${fs}' fill='${col}' font-family='system-ui'>${lbl}</text>`;}
        else if(tipo==='decision'){const cx=x+w/2,cy=y+h/2;ns+=`<polygon points='${cx.toFixed(1)},${y.toFixed(1)} ${(x+w).toFixed(1)},${cy.toFixed(1)} ${cx.toFixed(1)},${(y+h).toFixed(1)} ${x.toFixed(1)},${cy.toFixed(1)}' fill='${col}22' stroke='${col}' stroke-width='1.2'/>`;}
        else if(tipo==='start'){ns+=`<circle cx='${(x+w/2).toFixed(1)}' cy='${(y+h/2).toFixed(1)}' r='${(Math.min(w,h)/2).toFixed(1)}' fill='${col}'/>`;}
        else{ns+=`<rect x='${x.toFixed(1)}' y='${y.toFixed(1)}' width='${w.toFixed(1)}' height='${h.toFixed(1)}' fill='${col}18' stroke='${col}' stroke-width='1.2' rx='3'/><text x='${(x+w/2).toFixed(1)}' y='${(y+h/2+fs*.35).toFixed(1)}' text-anchor='middle' font-size='${fs}' fill='${col}' font-family='system-ui'>${lbl}</text>`;}
    });
    return `<svg viewBox='0 0 ${W} ${H}' xmlns='http://www.w3.org/2000/svg' style='width:100%;height:100%;display:block'>${ns}</svg>`;
}

const TIPOS_P_M = {usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',activity:'Actividades', state:'Máquina de Estado', component:'Componentes',deployment:'Despliegue', object:'Objetos', communication:'Comunicación',timing:'Tiempos', package:'Paquetes', composite:'Estructura Compuesta',profile:'Perfiles', overview:'Descripción General'};
const TIPOS_ICON_M = {usecase:'bi-person-circle', class:'bi-box', sequence:'bi-arrow-left-right',activity:'bi-activity', state:'bi-diagram-3', component:'bi-cpu',deployment:'bi-hdd-network', object:'bi-table', communication:'bi-chat-dots',timing:'bi-clock', package:'bi-folder2', composite:'bi-layout-three-columns',profile:'bi-tag', overview:'bi-map'};

async function renderProyectosMaestro() {
    const main = document.getElementById('contentArea');
    main.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;padding:60px 20px;gap:12px"><div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div><span style="color:var(--txt-muted)">Cargando proyectos…</span></div>`;
    try {
        const data = await api('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos');
        const proyectos = data.proyectos || [];

        main.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
            <div>
                <h4 style="margin:0;font-size:1.05rem;font-weight:700;color:var(--txt-main)"><i class="bi bi-diagram-3 me-2" style="color:var(--primary)"></i>Proyectos Colaborativos</h4>
                <p style="color:var(--txt-muted);font-size:.78rem;margin:3px 0 0">Espacios compartidos donde todos los miembros pueden ver y editar diagramas</p>
            </div>
            <div style="display:flex;gap:8px">
                <button onclick="modalNuevoProyectoM()"
                    style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:10px;padding:9px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-plus-circle me-1"></i>Nuevo Proyecto
                </button>
                <button onclick="modalUnirseProyectoM()"
                    style="background:var(--bg-card);border:2px solid var(--primary);color:var(--primary);border-radius:10px;padding:9px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-key me-1"></i>Unirse con código
                </button>
            </div>
        </div>

        ${proyectos.length === 0
            ? `<div style="text-align:center;padding:60px 20px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:16px">
                <i class="bi bi-diagram-3" style="font-size:3rem;color:var(--txt-muted);opacity:.3"></i>
                <h5 style="margin-top:12px;color:var(--txt-muted)">Sin proyectos aún</h5>
                <p style="color:var(--txt-muted);font-size:.82rem">Crea un proyecto nuevo o únete con un código de invitación</p>
               </div>`
            : `<div class="row g-3">
                ${proyectos.map(p => `
                <div class="col-md-6 col-lg-4">
                    <div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;overflow:hidden;cursor:pointer;transition:all .2s"
                         onclick="abrirProyectoM(${p.id})"
                         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(var(--primary-rgb),.2)'"
                         onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 18px">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                <span style="background:rgba(255,255,255,.2);color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px">${p.rol==='owner'?'OWNER':'EDITOR'}</span>
                                <code style="background:rgba(255,255,255,.15);color:#fff;font-size:.7rem;padding:2px 8px;border-radius:6px">${esc(p.codigo)}</code>
                            </div>
                            <div style="font-weight:700;color:#fff;font-size:.92rem">${esc(p.nombre)}</div>
                            ${p.descripcion ? `<div style="color:rgba(255,255,255,.7);font-size:.73rem;margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(p.descripcion)}</div>` : ''}
                        </div>
                        <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center">
                            <div style="display:flex;gap:12px">
                                <span style="font-size:.75rem;color:var(--txt-muted)"><i class="bi bi-people me-1" style="color:var(--primary)"></i>${p.num_miembros} miembros</span>
                                <span style="font-size:.75rem;color:var(--txt-muted)"><i class="bi bi-diagram-3 me-1" style="color:var(--primary)"></i>${p.num_diagramas} diagramas</span>
                            </div>
                            <i class="bi bi-chevron-right" style="color:var(--txt-muted)"></i>
                        </div>
                    </div>
                </div>`).join('')}
               </div>`}`;
    } catch(e) {
        main.innerHTML = `<div style="text-align:center;padding:60px"><i class="bi bi-exclamation-triangle" style="font-size:2.5rem;color:#f59e0b"></i><p style="color:var(--txt-muted);margin-top:10px">${esc(e.message)}</p></div>`;
    }
    setTimeout(() => { if (window.DiagramMiniRenderer) DiagramMiniRenderer.observeAll(document.getElementById('panelDiagramasM')); }, 100);
}

async function abrirProyectoM(pid) {
    const main = document.getElementById('contentArea');
    main.innerHTML = `<div id="proyDetM"><div style="display:flex;align-items:center;justify-content:center;padding:60px;gap:12px"><div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div></div></div>`;
    try {
        const data = await api(`<?= BASE_URL ?>/api/proyectos?action=detalle&id=${pid}`);
        if (!data || !data.proyecto) throw new Error(data?.error || 'No se pudo cargar el proyecto');
        const p = data.proyecto;
        const yo = data.rol;
        const diags = data.diagramas || [];
        const miembros = data.miembros || [];
        const archivos = data.archivos || [];
        window._proyectoDetalleIdM = pid;
        window._proyectoDetalleDiagsM = diags;
        window._proyectoDetalleMiembrosM = miembros;

        document.getElementById('proyDetM').innerHTML = `
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
            <button onclick="renderProyectosMaestro()" style="background:none;border:1.5px solid var(--bd-color);color:var(--primary);border-radius:8px;padding:6px 14px;cursor:pointer;font-size:.82rem">
                <i class="bi bi-arrow-left me-1"></i>Proyectos
            </button>
            <div style="flex:1;min-width:0">
                <h4 style="margin:0;font-size:1rem;font-weight:700;color:var(--txt-main)">${esc(p.nombre)}</h4>
                <span style="font-size:.73rem;color:var(--txt-muted)">${esc(p.descripcion||'Sin descripción')}</span>
            </div>
            <code style="background:rgba(var(--primary-rgb),.1);color:var(--primary);font-size:.82rem;font-weight:700;padding:5px 13px;border-radius:8px;cursor:pointer"
                  onclick="navigator.clipboard?.writeText('${p.codigo}');toast('Código copiado','ok')" title="Clic para copiar">
                ${esc(p.codigo)} <i class="bi bi-copy" style="font-size:.7rem"></i>
            </code>
        </div>

        <div class="row g-3">
            <!-- TABS -->
            <div class="col-12 mb-1">
                <div style="display:flex;gap:4px;border-bottom:2px solid var(--bd-color)">
                    <button id="tabMPD" onclick="setProyTabM('diagramas')"
                        style="background:none;border:none;border-bottom:3px solid var(--primary);color:var(--primary);padding:8px 16px;font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:-2px">
                        <i class="bi bi-diagram-3 me-1"></i>Diagramas (${diags.length})
                    </button>
                    <button id="tabMPA" onclick="setProyTabM('archivos')"
                        style="background:none;border:none;border-bottom:3px solid transparent;color:var(--txt-muted);padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                        <i class="bi bi-folder2-open me-1"></i>Archivos (${archivos.length})
                    </button>
                    <button id="tabMPO" onclick="setProyTabM('observaciones')"
                        style="background:none;border:none;border-bottom:3px solid transparent;color:var(--txt-muted);padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                        <i class="bi bi-chat-left-text me-1"></i>Observaciones
                    </button>
                    <button id="tabMPT" onclick="setProyTabM('tareas')"
                        style="background:none;border:none;border-bottom:3px solid transparent;color:var(--txt-muted);padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                        <i class="bi bi-clipboard-check me-1"></i>Tareas
                    </button>
                </div>
            </div>

            <!-- Panel Diagramas -->
            <div class="col-md-8" id="panelDiagramasM">
                <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <i class="bi bi-diagram-3" style="color:var(--primary)"></i>
                        <span style="font-weight:600;font-size:.88rem;color:var(--txt-main)">Diagramas del Proyecto</span>
                        <div style="margin-left:auto">
                            <button onclick="crearDiagramaParaProyectoM(${pid})"
                                style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 14px;font-size:.8rem;cursor:pointer;font-weight:600">
                                <i class="bi bi-plus-circle me-1"></i>Nuevo diagrama
                            </button>
                        </div>
                    </div>
                    <div style="padding:14px 16px">
                    ${diags.length === 0
                        ? `<div style="text-align:center;padding:30px 20px">
                               <i class="bi bi-file-earmark-plus" style="font-size:2.5rem;color:var(--txt-muted);opacity:.4"></i>
                               <p style="color:var(--txt-muted);font-size:.82rem;margin-top:8px">No hay diagramas aún. Agrega uno o crea uno nuevo.</p>
                           </div>`
                        : `<div class="row g-3">
                            ${diags.map(d => {
                                const esMio = d.usuario_id == <?= SessionManager::usuarioId() ?>;
                                const fecha = new Date(d.fecha_modificacion||d.fecha_creacion).toLocaleDateString('es-MX');
                                return `<div class="col-sm-6 col-lg-4">
                                    <div class="m-diagram-card" style="position:relative">
                                        ${(esMio||yo==='owner') ? `<button onclick="quitarDiagramaProyectoM(${pid},${d.id})" title="Quitar del proyecto" style="position:absolute;top:6px;right:6px;z-index:10;background:rgba(0,0,0,.45);border:none;color:#fca5a5;border-radius:6px;width:22px;height:22px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.8rem"><i class="bi bi-x-circle"></i></button>` : ''}
                                        <div class="m-lc-preview" data-preview-id="${d.id}" onclick="saveMNavState(${pid});window.location.href='<?= BASE_URL ?>/editor?id=${d.id}'" title="Abrir en editor">
                                            <div style="display:flex;align-items:center;justify-content:center;height:100%;opacity:0.3">
                                                ${getTipoIconoSVG(d.tipo_diagrama, 44)}
                                            </div>
                                        </div>
                                        <div class="m-lc-body">
                                            <div class="m-lc-title" title="${esc(d.titulo||'Sin título')}">${esc(d.titulo||'Sin título')}</div>
                                            <div class="m-lc-meta">
                                                <span style="display:inline-flex;align-items:center;gap:3px">${getTipoIconoSVG(d.tipo_diagrama,11)}&nbsp;${TIPOS_P_M[d.tipo_diagrama]||d.tipo_diagrama}</span>
                                                &nbsp;·&nbsp;por ${esc(d.autor||d.username||'?')}${esMio?' (tú)':''}
                                            </div>
                                        </div>
                                        <div class="m-lc-footer">
                                            <button class="m-lc-btn-open" onclick="saveMNavState(${pid});window.location.href='<?= BASE_URL ?>/editor?id=${d.id}'">Abrir</button>
                                            <span style="margin-left:auto;font-size:.65rem;color:var(--txt-muted)">${fecha}</span>
                                        </div>
                                    </div>
                                </div>`;
                            }).join('')}
                           </div>`}
                    </div>
                </div>
            </div>

            <!-- Panel Archivos (hidden) -->
            <div class="col-md-8" id="panelArchivosM" style="display:none">
                <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px">
                        <i class="bi bi-folder2-open" style="color:var(--primary)"></i>
                        <span style="font-weight:600;font-size:.88rem;color:var(--txt-main)">Archivos del Proyecto</span>
                        <div style="margin-left:auto">
                            <label style="background:linear-gradient(135deg,var(--primary),var(--primary2));color:#fff;border-radius:8px;padding:6px 14px;font-size:.75rem;font-weight:600;cursor:pointer">
                                <i class="bi bi-upload me-1"></i>Subir archivo
                                <input type="file" id="proyFileInputM" style="display:none" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.md,.sql,.csv,.json,.xml,.png,.jpg,.jpeg,.gif,.svg"
                                    onchange="subirArchivosProyectoM(${pid}, this.files)">
                            </label>
                        </div>
                    </div>
                    <div id="archivosListaM" style="padding:14px 16px">
                    ${archivos.length === 0
                        ? `<div style="text-align:center;padding:30px 20px">
                               <i class="bi bi-folder-plus" style="font-size:2rem;color:var(--txt-muted);opacity:.4"></i>
                               <p style="color:var(--txt-muted);font-size:.82rem;margin-top:8px">Sin archivos. Sube documentos, SQL, presentaciones y más.</p>
                           </div>`
                        : renderArchivosListaM(archivos, pid)}
                    </div>
                </div>
            </div>

            <!-- Panel Observaciones del proyecto -->
            <div class="col-md-8" id="panelObservacionesM" style="display:none">
                <div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px">
                        <i class="bi bi-chat-left-text" style="color:var(--primary)"></i>
                        <span style="font-weight:600;font-size:.88rem;color:var(--txt-main)">Observaciones por diagrama</span>
                    </div>
                    <div id="proyObsPanelM" style="padding:14px 16px"></div>
                </div>
            </div>

            <!-- Panel Tareas del proyecto -->
            <div class="col-md-8" id="panelTareasM" style="display:none">
                <div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <i class="bi bi-clipboard-check" style="color:var(--primary)"></i>
                        <span style="font-weight:600;font-size:.88rem;color:var(--txt-main)">Tareas del proyecto</span>
                        <button onclick="abrirModalTareaProyectoM()"
                            style="margin-left:auto;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 14px;font-size:.8rem;cursor:pointer">
                            <i class="bi bi-plus-lg me-1"></i>Asignar tarea
                        </button>
                    </div>
                    <div id="proyTareasPanelM" style="padding:14px 16px"></div>
                </div>
            </div>

            <!-- Miembros -->
            <div class="col-md-4">
                <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;justify-content:space-between">
                        <span style="font-weight:600;font-size:.88rem;color:var(--txt-main)"><i class="bi bi-people me-2" style="color:var(--primary)"></i>Miembros (${miembros.length})</span>
                        <!-- Código copiable V46 -->
                        <span class="copy-code-badge" onclick="copiarCodigoProyecto('${esc(p.codigo)}')" title="Clic para copiar código">
                            ${esc(p.codigo)} <i class="bi bi-copy"></i>
                        </span>
                    </div>
                    <!-- Buscador de usuarios para invitar — solo owner/admin -->
                    ${(yo==='owner'||esAdminG) ? `
                    <div style="padding:10px 14px;border-bottom:1px solid var(--bd-color)">
                        <div style="font-size:.7rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px">
                            <i class="bi bi-person-plus me-1" style="color:var(--primary)"></i>Invitar usuario
                        </div>
                        <div class="invite-search-wrap" id="mInviteWrap_${pid}">
                            <i class="bi bi-search si"></i>
                            <input type="text" placeholder="Nombre, usuario o correo..."
                                oninput="buscarParaInvitar(this.value,${pid},'mInviteResults_${pid}','mRolInv_${pid}')"
                                onfocus="document.getElementById('mInviteResults_${pid}').style.display='block'"
                                onblur="setTimeout(()=>{const r=document.getElementById('mInviteResults_${pid}');if(r)r.style.display='none'},200)">
                            <div class="invite-results" id="mInviteResults_${pid}" style="display:none"></div>
                        </div>
                        <div style="display:flex;gap:6px;margin-top:7px;align-items:center">
                            <select id="mRolInv_${pid}" style="flex:1;background:var(--bg-deep);border:1.5px solid var(--bd-color);border-radius:8px;color:var(--txt-main);padding:5px 8px;font-size:.78rem;outline:none">
                                <option value="editor">Editor — puede crear y editar sus diagramas</option>
                                <option value="viewer">Visualizador — solo lectura</option>
                            </select>
                            <button onclick="invitarSeleccionadoM(${pid})"
                                style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:6px 14px;font-size:.75rem;font-weight:600;cursor:pointer;white-space:nowrap">
                                <i class="bi bi-person-plus me-1"></i>Agregar
                            </button>
                        </div>
                        <!-- Botón copiar invitación completa -->
                        <button onclick="copiarInvitacionM('${esc(p.nombre)}','${esc(p.codigo)}')"
                            class="copy-invite-btn" style="width:100%;margin-top:7px;justify-content:center">
                            <i class="bi bi-share"></i> Copiar invitación completa
                        </button>
                    </div>` : ''}
                    <div style="padding:10px 14px">
                    ${miembros.map(m => {
                        const esYo = m.id == <?= SessionManager::usuarioId() ?>;
                        return `<div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--bd-color)">
                            <div style="width:34px;height:34px;background:${m.rol_proyecto==='owner'?'linear-gradient(135deg,var(--primary),var(--primary2))':'var(--bg-hover)'};border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:${m.rol_proyecto==='owner'?'#fff':'var(--primary)'};flex-shrink:0">
                                ${esc((m.nombre_completo||m.username||'?')[0].toUpperCase())}
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:.82rem;font-weight:600;color:var(--txt-main)">
                                    ${esc(m.nombre_completo||m.username)}
                                    ${esYo?'<span style="font-size:.62rem;color:var(--primary);margin-left:4px">(tú)</span>':''}
                                </div>
                                <div style="font-size:.68rem;color:var(--txt-muted)">
                                    @${esc(m.username)}
                                    <span style="margin-left:4px;background:${m.rol_proyecto==='owner'?'rgba(var(--primary-rgb),.12)':'var(--bg-hover)'};color:${m.rol_proyecto==='owner'?'var(--primary)':'var(--txt-muted)'};border-radius:6px;padding:0 5px;font-size:.6rem">${m.rol_proyecto}</span>
                                </div>
                            </div>
                        </div>`;
                    }).join('')}
                    ${yo==='owner'
                        ? `<div style="margin-top:10px"><button onclick="confirmarEliminarProyectoM(${pid})"
                                style="width:100%;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:8px;padding:7px;font-size:.75rem;cursor:pointer">
                                <i class="bi bi-trash3 me-1"></i>Eliminar proyecto</button></div>`
                        : `<div style="margin-top:10px"><button onclick="confirmarSalirProyectoM(${pid})"
                                style="width:100%;background:var(--bg-hover);border:1px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:7px;font-size:.75rem;cursor:pointer">
                                <i class="bi bi-box-arrow-right me-1"></i>Salir del proyecto</button></div>`}
                    </div>
                </div>
            </div>
        </div>`;
    } catch(e) {
        main.innerHTML = `<div style="text-align:center;padding:60px"><i class="bi bi-exclamation-triangle" style="font-size:2rem;color:#f59e0b"></i><p style="color:var(--txt-muted);margin-top:10px">${esc(e.message)}</p></div>`;
    }
}

function setProyTabM(tab) {
    const showD = tab === 'diagramas';
    const showA = tab === 'archivos';
    const showO = tab === 'observaciones';
    const showT = tab === 'tareas';
    document.getElementById('panelDiagramasM').style.display = showD ? '' : 'none';
    document.getElementById('panelArchivosM').style.display  = showA ? '' : 'none';
    document.getElementById('panelObservacionesM').style.display = showO ? '' : 'none';
    document.getElementById('panelTareasM').style.display = showT ? '' : 'none';
    document.getElementById('tabMPD').style.borderBottomColor = showD ? 'var(--primary)' : 'transparent';
    document.getElementById('tabMPD').style.color = showD ? 'var(--primary)' : 'var(--txt-muted)';
    document.getElementById('tabMPA').style.borderBottomColor = showA ? 'var(--primary)' : 'transparent';
    document.getElementById('tabMPA').style.color = showA ? 'var(--primary)' : 'var(--txt-muted)';
    document.getElementById('tabMPO').style.borderBottomColor = showO ? 'var(--primary)' : 'transparent';
    document.getElementById('tabMPO').style.color = showO ? 'var(--primary)' : 'var(--txt-muted)';
    document.getElementById('tabMPT').style.borderBottomColor = showT ? 'var(--primary)' : 'transparent';
    document.getElementById('tabMPT').style.color = showT ? 'var(--primary)' : 'var(--txt-muted)';
    if (showO) {
        cargarObservacionesEnProyectoM(window._proyectoDetalleIdM, window._proyectoDetalleDiagsM);
    }
    if (showT) {
        renderTareasProyectoM(window._proyectoDetalleIdM);
    }
    if (showD && window.DiagramMiniRenderer) {
        setTimeout(() => DiagramMiniRenderer.observeAll(document.getElementById('panelDiagramasM')), 50);
    }
}

async function renderObservacionesProyectoM(pid, diags) {
    const panel = document.getElementById('proyObsPanelM');
    if (!panel) return;
    panel.innerHTML = `<div style="text-align:center;padding:40px;color:var(--txt-muted)"><div class="spinner-border text-primary" style="width:1.4rem;height:1.4rem"></div></div>`;

    if (!pid || !Array.isArray(diags)) {
        panel.innerHTML = `<div style="text-align:center;padding:30px;color:var(--txt-muted)">No se encontraron datos del proyecto.</div>`;
        return;
    }

    const myDiagramIds = new Set(diags.filter(d => Number(d.usuario_id) === Number(MAESTRO_ID)).map(d => d.id));
    if (myDiagramIds.size === 0) {
        panel.innerHTML = `<div style="text-align:center;padding:30px;color:var(--txt-muted)">Este proyecto no contiene diagramas tuyos.</div>`;
        return;
    }

    try {
        const data = await api(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}`);
        const obs = data.observaciones || [];
        const relevant = obs.filter(o => myDiagramIds.has(Number(o.diagrama_id)) && Number(o.autor_id) !== Number(MAESTRO_ID));

        if (relevant.length === 0) {
            panel.innerHTML = `<div style="text-align:center;padding:30px;color:var(--txt-muted)">
                <i class="bi bi-chat-left-text" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px"></i>
                <div style="font-size:.85rem">Aún no hay observaciones de otros usuarios en tus diagramas de este proyecto.</div>
            </div>`;
            return;
        }

        const grouped = {};
        relevant.forEach(o => {
            const key = o.diagrama_id;
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(o);
        });

        panel.innerHTML = Object.entries(grouped).map(([did, comentarios]) => {
            const diag = diags.find(d => String(d.id) === String(did));
            const titulo = diag ? diag.titulo || `Diagrama #${did}` : `Diagrama #${did}`;
            return `<div style="border:1.5px solid var(--bd-color);border-radius:14px;margin-bottom:18px;overflow:hidden;background:var(--bg-card)">
                <div style="padding:14px 16px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <div style="display:flex;align-items:center;gap:10px">
                        <i class="bi bi-diagram-3" style="color:var(--primary);font-size:1rem"></i>
                        <div>
                            <div style="font-size:.92rem;font-weight:700;color:var(--txt-main)">${esc(titulo)}</div>
                            <div style="font-size:.72rem;color:var(--txt-muted)">Observaciones sobre tu diagrama</div>
                        </div>
                    </div>
                    <button onclick="window.location.href='<?= BASE_URL ?>/editor?id=${did}'"
                        style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:7px 14px;font-size:.78rem;font-weight:600;cursor:pointer">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Abrir editor
                    </button>
                </div>
                <div style="padding:14px 16px;display:flex;flex-direction:column;gap:10px">
                    ${comentarios.map(o => {
                        const fecha = new Date(o.fecha_creacion).toLocaleString('es-MX');
                        const esMaestro = o.autor_rol === 'maestro' || o.autor_rol === 'admin';
                        const esReporte = o.tipo_obs === 'reporte_error';
                        const borderClr = esReporte ? 'rgba(239,68,68,.45)' : 'var(--bd-color)';
                        const bgClr     = esReporte ? 'rgba(239,68,68,.07)' : 'var(--bg-deep)';
                        const avatarBg  = esReporte ? 'linear-gradient(135deg,#ef4444,#dc2626)'
                                        : esMaestro ? 'linear-gradient(135deg,#f59e0b,#d97706)'
                                        : 'linear-gradient(135deg,var(--primary),var(--primary2))';
                        const roleLabel = esReporte ? '⚑ Reporte de alumno' : esMaestro ? 'Maestro' : 'Alumno';
                        return `<div style="padding:14px;border:1px solid ${borderClr};border-radius:12px;background:${bgClr}">
                            ${esReporte ? `<div style="font-size:.72rem;font-weight:700;color:#ef4444;margin-bottom:8px;letter-spacing:.03em"><i class="bi bi-flag-fill me-1"></i>REPORTE DE PROBLEMA — revisa y responde al alumno</div>` : ''}
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <div style="width:34px;height:34px;background:${avatarBg};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:700;color:#fff;flex-shrink:0">
                                    ${esc((o.autor_nombre||o.autor_username||'?')[0].toUpperCase())}
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:.82rem;font-weight:700;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(o.autor_nombre||o.autor_username)}</div>
                                    <div style="font-size:.70rem;color:var(--txt-muted)">${roleLabel} · ${fecha}</div>
                                </div>
                            </div>
                            <div style="font-size:.84rem;color:var(--txt-main);line-height:1.6;white-space:pre-wrap;margin-bottom:10px">${esc(o.texto)}</div>
                            <!-- V46: Hilo de respuestas y botón reply -->
                            <div id="hiloM_${o.id}" style="margin-bottom:6px"></div>
                            <div style="display:flex;gap:8px;align-items:flex-start;margin-top:8px;border-top:1px solid var(--bd-color);padding-top:8px">
                                <button onclick="toggleHiloMaestro(${o.id})"
                                    style="background:rgba(102,126,234,.1);border:1px solid rgba(102,126,234,.2);color:var(--primary);border-radius:7px;padding:4px 12px;font-size:.72rem;cursor:pointer;white-space:nowrap;flex-shrink:0">
                                    <i class="bi bi-chat-dots me-1"></i>${o.num_respuestas > 0 ? o.num_respuestas + ' respuesta' + (o.num_respuestas!=1?'s':'') : 'Ver hilo'}
                                </button>
                                <textarea id="replyM_${o.id}" rows="2" placeholder="Responder al alumno... (Ctrl+Enter para enviar)"
                                    onkeydown="if(event.ctrlKey&&event.key==='Enter')enviarReplyMaestro(${o.id})"
                                    style="flex:1;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:6px 10px;font-size:.78rem;resize:none;outline:none;font-family:inherit"></textarea>
                                <button onclick="enviarReplyMaestro(${o.id})"
                                    style="align-self:flex-end;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:7px 14px;font-size:.75rem;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0">
                                    <i class="bi bi-send me-1"></i>Responder
                                </button>
                            </div>
                        </div>`;
                    }).join('')}
                </div>
            </div>`;
        }).join('');
    } catch(e) {
        panel.innerHTML = `<div style="text-align:center;padding:30px;color:#ef4444">Error al cargar observaciones: ${esc(e.message)}</div>`;
    }
}

async function renderTareasProyectoM(pid) {
    const panel = document.getElementById('proyTareasPanelM');
    if (!panel) return;
    panel.innerHTML = `<div style="text-align:center;padding:40px;color:var(--txt-muted)"><div class="spinner-border text-primary" style="width:1.4rem;height:1.4rem"></div></div>`;
    try {
        const data = await api(`<?= BASE_URL ?>/api/tareas-proyecto?proyecto_id=${pid}`);
        if (data?.error) throw new Error(data.error);
        const tareas = data.tareas || [];
        window._tareasProyectoM = tareas;
        if (tareas.length === 0) {
            panel.innerHTML = `<div style="text-align:center;padding:40px;color:var(--txt-muted)">
                <i class="bi bi-clipboard-check" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px"></i>
                <div style="font-size:.88rem;font-weight:600;color:var(--txt-main)">Aún no hay tareas asignadas</div>
                <div style="font-size:.78rem;color:var(--txt-muted);margin-top:6px">Crea una tarea para el grupo o asigna a un miembro específico.</div>
            </div>`;
            return;
        }
        panel.innerHTML = `<div style="display:flex;flex-direction:column;gap:10px">
            ${tareas.map(t => {
                const asignado = t.asignado_nombre ? esc(t.asignado_nombre) : 'Todo el equipo';
                const estado = t.estado === 'calificada' ? 'Calificada' : t.estado === 'entregada' ? 'Entregada' : t.estado === 'en_progreso' ? 'En progreso' : 'Pendiente';
                const color = t.estado === 'calificada' ? '#10b981' : t.estado === 'entregada' ? '#f59e0b' : '#667eea';
                const fecha = t.fecha_limite ? new Date(t.fecha_limite).toLocaleDateString('es-MX') : 'Sin fecha';
                const entregas = parseInt(t.num_entregas, 10) || 0;
                const entrego = entregas > 0;
                return `<div style="background:var(--bg-hover);border:1px solid var(--bd-color);border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:10px">
                    <div style="display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.95rem;font-weight:700;color:var(--txt-main);margin-bottom:4px">${esc(t.titulo)}</div>
                            <div style="font-size:.78rem;color:var(--txt-muted);line-height:1.5">${esc(t.descripcion||'Sin descripción')}</div>
                        </div>
                        <div style="display:flex;gap:6px;align-items:center;white-space:nowrap;flex-direction:column;align-items:flex-end">
                            <span style="font-size:.72rem;color:${color};font-weight:700">${estado}</span>
                            <span style="font-size:.72rem;color:var(--txt-muted)">${fecha}</span>
                        </div>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;font-size:.78rem;color:var(--txt-muted)">
                        <span><i class="bi bi-person-fill me-1"></i>Asignado a: ${asignado}</span>
                        <span style="color:${entrego?'#10b981':'#ef4444'}"><i class="bi bi-${entrego?'check-circle':'clock'} me-1"></i>${entrego ? entregas+' entrega(s)' : 'Sin entregar aún'}</span>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button onclick="verEntregasTareaProyecto(${t.id})"
                            style="flex:1;min-width:120px;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 12px;font-size:.78rem;font-weight:600;cursor:pointer">
                            <i class="bi bi-eye me-1"></i>Ver entregas
                        </button>
                        <button onclick="editarTareaProyectoMById(${t.id})"
                            style="background:var(--bg-card);border:1.5px solid var(--primary);color:var(--primary);border-radius:8px;padding:8px 12px;font-size:.78rem;cursor:pointer">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button onclick="eliminarTareaProyectoM(${t.id})"
                            style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;border-radius:8px;padding:8px 12px;font-size:.78rem;cursor:pointer">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>`;
            }).join('')}
        </div>`;
    } catch (e) {
        panel.innerHTML = `<div style="color:#ef4444;padding:20px">Error al cargar tareas: ${esc(e.message)}</div>`;
    }
}

function editarTareaProyectoMById(id) {
    const t = (window._tareasProyectoM || []).find(x => parseInt(x.id, 10) === parseInt(id, 10));
    if (!t) { toast('Tarea no encontrada','err'); return; }
    const miembros = window._proyectoDetalleMiembrosM || [];
    const select = document.getElementById('tAsignadoM');
    if (select) {
        select.innerHTML = `<option value="">Todo el equipo</option>` + miembros.map(m => `
            <option value="${m.id}">${esc(m.nombre_completo||m.username)} @${esc(m.username)}</option>`
        ).join('');
    }
    editarTareaProyectoM(t);
}

function abrirModalTareaProyectoM() {
    const miembros = window._proyectoDetalleMiembrosM || [];
    const select = document.getElementById('tAsignadoM');
    if (select) {
        select.innerHTML = `<option value="">Todo el equipo</option>` + miembros.filter(m => (m.rol_sistema || m.rol) === 'alumno').map(m => `
            <option value="${m.id}">${esc(m.nombre_completo||m.username)} @${esc(m.username)}</option>`
        ).join('');
    }
    document.getElementById('tTareaIdM').value = '';
    document.getElementById('tTituloM').value = '';
    document.getElementById('tDescripcionM').value = '';
    document.getElementById('tFechaEntregaM').value = '';
    new bootstrap.Modal(document.getElementById('modalTareaProyectoM')).show();
}

async function verEntregasTareaProyecto(tareaId, titulo) {
    if (!titulo) {
        const t = (window._tareasProyectoM || []).find(x => parseInt(x.id, 10) === parseInt(tareaId, 10));
        titulo = t?.titulo || 'Tarea';
    }
    document.getElementById('panelEntregas')?.remove();
    const panel = document.createElement('div');
    panel.id = 'panelEntregas';
    panel.innerHTML = `
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:8000" onclick="document.getElementById('panelEntregas').remove()"></div>
    <div style="position:fixed;top:0;right:0;width:min(600px,100vw);height:100vh;background:var(--bg-card);border-left:1px solid var(--bd-color);z-index:8001;overflow-y:auto;display:flex;flex-direction:column">
        <div style="position:sticky;top:0;background:var(--bg-card);border-bottom:1px solid var(--bd-color);padding:16px 20px;display:flex;align-items:center;gap:12px;z-index:1">
            <div style="flex:1;min-width:0">
                <div style="color:var(--txt-main);font-weight:700;font-size:.95rem">${esc(titulo)}</div>
                <div id="panelEntregasSubtitulo" style="color:var(--txt-muted);font-size:.75rem">Cargando…</div>
            </div>
            <button onclick="document.getElementById('panelEntregas').remove()" style="background:none;border:none;color:var(--txt-muted);font-size:1.2rem;cursor:pointer"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="entregasContent" style="padding:16px;flex:1">
            <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>`;
    document.body.appendChild(panel);

    try {
        const r = await api(`<?= BASE_URL ?>/api/tareas-proyecto/entregas?tarea_id=${tareaId}`);
        if (!r.success && r.error) throw new Error(r.error);
        const entregas = r.entregas || [];
        const tarea = r.tarea || {};
        const sub = document.getElementById('panelEntregasSubtitulo');
        if (sub) {
            sub.innerHTML = `<i class="bi bi-folder2-open me-1"></i>Proyecto: <strong>${esc(tarea.proyecto_nombre||'—')}</strong>`;
        }
        const entregadas = entregas.filter(e => e.entrega_id);
        const sin = entregas.filter(e => !e.entrega_id);
        const calificadas = entregadas.filter(e => e.calificacion != null && e.calificacion !== '');

        const renderContent = (tab) => {
            const list = { todas: entregas, entregadas, calificadas, pendientes: sin }[tab] || entregas;
            return `
            <div style="display:flex;gap:4px;margin-bottom:12px;border-bottom:1px solid var(--bd-color);padding-bottom:8px;flex-wrap:wrap">
                <button onclick="updateEntregasTabProyecto('todas')" style="padding:6px 10px;border:none;background:none;border-bottom:3px solid ${tab==='todas'?'var(--primary)':'transparent'};color:${tab==='todas'?'var(--primary)':'var(--txt-muted)'};font-size:.78rem;font-weight:600;cursor:pointer">Todas (${entregas.length})</button>
                <button onclick="updateEntregasTabProyecto('entregadas')" style="padding:6px 10px;border:none;background:none;border-bottom:3px solid ${tab==='entregadas'?'var(--primary)':'transparent'};color:${tab==='entregadas'?'var(--primary)':'var(--txt-muted)'};font-size:.78rem;font-weight:600;cursor:pointer">Entregadas (${entregadas.length})</button>
                <button onclick="updateEntregasTabProyecto('pendientes')" style="padding:6px 10px;border:none;background:none;border-bottom:3px solid ${tab==='pendientes'?'var(--primary)':'transparent'};color:${tab==='pendientes'?'var(--primary)':'var(--txt-muted)'};font-size:.78rem;font-weight:600;cursor:pointer">Sin entregar (${sin.length})</button>
            </div>
            <div style="display:flex;gap:8px;margin-bottom:16px">
                <div style="flex:1;background:var(--bg-hover);border-radius:8px;padding:10px;text-align:center;border:1px solid var(--bd-color)">
                    <div style="font-size:1.2rem;font-weight:700;color:#10b981">${entregadas.length}</div>
                    <div style="font-size:.7rem;color:var(--txt-muted)">Entregadas</div>
                </div>
                <div style="flex:1;background:var(--bg-hover);border-radius:8px;padding:10px;text-align:center;border:1px solid var(--bd-color)">
                    <div style="font-size:1.2rem;font-weight:700;color:#ef4444">${sin.length}</div>
                    <div style="font-size:.7rem;color:var(--txt-muted)">Pendientes</div>
                </div>
            </div>
            ${list.length === 0 ? `<div style="text-align:center;padding:30px;color:var(--txt-muted)"><i class="bi bi-inbox" style="font-size:2rem;opacity:.4"></i><p style="margin-top:8px">No hay registros en esta categoría</p></div>` :
            list.map(e => {
                const entregada = !!e.entrega_id;
                const color = !entregada ? '#ef4444' : (e.calificacion != null ? '#10b981' : '#f59e0b');
                return `<div style="background:var(--bg-hover);border:1px solid ${color}33;border-radius:10px;padding:14px;margin-bottom:10px">
                    <div style="display:flex;align-items:flex-start;gap:12px">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary2));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0">${esc((e.nombre_completo||e.username||'?')[0].toUpperCase())}</div>
                        <div style="flex:1;min-width:0">
                            <div style="font-weight:600;color:var(--txt-main)">${esc(e.nombre_completo||e.username)}</div>
                            <div style="font-size:.72rem;color:var(--txt-muted)">@${esc(e.username)}</div>
                            ${entregada ? `
                                ${e.diagrama_titulo ? `<div style="font-size:.75rem;margin-top:6px"><i class="bi bi-diagram-3 me-1"></i>${esc(e.diagrama_titulo)}</div>` : ''}
                                ${e.comentario_alumno ? `<div style="font-size:.73rem;margin-top:4px;color:var(--txt-main)">${esc(e.comentario_alumno)}</div>` : ''}
                                ${e.diagrama_id ? `<a href="<?= BASE_URL ?>/editor?id=${e.diagrama_id}" target="_blank" style="font-size:.73rem;color:var(--primary)"><i class="bi bi-box-arrow-up-right me-1"></i>Ver diagrama</a>` : ''}
                                <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end">
                                    <div><label style="font-size:.68rem;color:var(--txt-muted)">Calificación</label>
                                    <input type="number" id="calp_${e.alumno_id}" min="0" max="100" step="0.5" value="${e.calificacion??''}" style="width:80px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:6px;color:var(--txt-main);padding:5px 8px;font-size:.82rem"></div>
                                    <div style="flex:1;min-width:120px"><label style="font-size:.68rem;color:var(--txt-muted)">Comentario</label>
                                    <input type="text" id="comp_${e.alumno_id}" value="${esc(e.comentario_cal||'')}" style="width:100%;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:6px;color:var(--txt-main);padding:5px 8px;font-size:.82rem"></div>
                                    <button onclick="calificarEntregaProyecto(${tareaId},${e.alumno_id})" style="background:var(--primary);border:none;color:#fff;border-radius:6px;padding:7px 12px;font-size:.75rem;cursor:pointer"><i class="bi bi-check"></i> Guardar</button>
                                </div>
                            ` : `<div style="font-size:.75rem;color:#ef4444;margin-top:6px"><i class="bi bi-x-circle me-1"></i>No ha entregado esta tarea</div>`}
                        </div>
                        <i class="bi bi-${entregada?'check-circle-fill':'clock'}" style="color:${color}"></i>
                    </div>
                </div>`;
            }).join('')}`;
        };

        window._renderEntregasProyectoContent = renderContent;
        window._tabEntregasProyectoActual = 'todas';
        document.getElementById('entregasContent').innerHTML = renderContent('todas');
    } catch (e) {
        document.getElementById('entregasContent').innerHTML = `<p style="color:#ef4444">${esc(e.message)}</p>`;
    }
}

function updateEntregasTabProyecto(tab) {
    window._tabEntregasProyectoActual = tab;
    if (window._renderEntregasProyectoContent) {
        document.getElementById('entregasContent').innerHTML = window._renderEntregasProyectoContent(tab);
    }
}

async function calificarEntregaProyecto(tareaId, alumnoId) {
    const cal = document.getElementById('calp_'+alumnoId)?.value;
    const com = document.getElementById('comp_'+alumnoId)?.value || '';
    if (cal === '' || cal === null) { toast('Ingresa una calificación','info'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/tareas-proyecto/calificar', {
            tarea_id: tareaId, alumno_id: alumnoId,
            calificacion: parseFloat(cal), comentario: com.trim()
        });
        if (r.success) {
            toast('Calificación guardada','ok');
            const titulo = document.querySelector('#panelEntregas [style*="font-weight:700"]')?.textContent || 'Tarea';
            verEntregasTareaProyecto(tareaId);
            renderTareasProyectoM(window._proyectoDetalleIdM);
        } else throw new Error(r.error||'Error al calificar');
    } catch (e) { toast(e.message,'err'); }
}

async function guardarTareaProyectoM() {
    const titulo = document.getElementById('tTituloM').value.trim();
    if (!titulo) { toast('El título es obligatorio','info'); return; }
    const pid = window._proyectoDetalleIdM;
    const tareaId = document.getElementById('tTareaIdM').value || 0;
    if (!pid) { toast('No se encontró el proyecto','err'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/tareas-proyecto', {
            proyecto_id: pid,
            tarea_id: tareaId,
            titulo,
            descripcion: document.getElementById('tDescripcionM').value.trim(),
            fecha_limite: document.getElementById('tFechaEntregaM').value || null,
            asignado_a: (() => { const v = document.getElementById('tAsignadoM').value; return v ? parseInt(v, 10) : null; })()
        });
        if (!r) throw new Error('Sin respuesta del servidor');
        if (r.success) {
            toast(tareaId ? 'Tarea actualizada' : 'Tarea asignada correctamente','ok');
            bootstrap.Modal.getInstance(document.getElementById('modalTareaProyectoM')).hide();
            renderTareasProyectoM(pid);
        } else throw new Error(r.error||'Error al guardar tarea');
    } catch (e) {
        toast(e.message,'err');
    }
}

function editarTareaProyectoM(tarea) {
    document.getElementById('tTituloM').value = tarea.titulo || '';
    document.getElementById('tDescripcionM').value = tarea.descripcion || '';
    document.getElementById('tFechaEntregaM').value = tarea.fecha_limite || '';
    document.getElementById('tAsignadoM').value = tarea.asignado_a || '';
    document.getElementById('tTareaIdM').value = tarea.id || '';
    new bootstrap.Modal(document.getElementById('modalTareaProyectoM')).show();
}

async function eliminarTareaProyectoM(id) {
    if (!confirm('¿Eliminar esta tarea?')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/tareas-proyecto/del', { tarea_id: id });
        if (!r) throw new Error('Sin respuesta del servidor');
        if (r.success) {
            toast('Tarea eliminada','ok');
            renderTareasProyectoM(window._proyectoDetalleIdM);
        } else throw new Error(r.error||'Error al eliminar tarea');
    } catch (e) {
        toast(e.message,'err');
    }
}

function renderArchivosListaM(archivos, pid) {
    const EXT_ICON = {pdf:'bi-file-pdf',doc:'bi-file-word',docx:'bi-file-word',xls:'bi-file-excel',xlsx:'bi-file-excel',ppt:'bi-file-ppt',pptx:'bi-file-ppt',png:'bi-file-image',jpg:'bi-file-image',jpeg:'bi-file-image',gif:'bi-file-image',svg:'bi-file-image',sql:'bi-file-code',json:'bi-filetype-json',txt:'bi-file-text',md:'bi-file-text',csv:'bi-file-spreadsheet'};
    return `<div style="display:flex;flex-direction:column;gap:6px">${archivos.map(f=>{
        const ext = (f.extension||'').toLowerCase();
        const icon = EXT_ICON[ext]||'bi-file-earmark';
        const sz = parseInt(f.tamano||0);
        const szStr = sz < 1024 ? sz+'B' : sz < 1024*1024 ? (sz/1024).toFixed(1)+'KB' : (sz/1024/1024).toFixed(2)+'MB';
        const viewUrl = '<?= BASE_URL ?>/api/proyectos/view?file_id='+f.id;
        const downUrl = '<?= BASE_URL ?>/api/proyectos/download?file_id='+f.id;
        const puedeVer = ['pdf','png','jpg','jpeg','gif','webp','svg','txt','md','csv','json','html','xml','js','php','css'].includes(ext);
        return `<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;background:var(--bg-hover);border:1px solid var(--bd-color);border-radius:8px;cursor:${puedeVer?'pointer':'default'}"
                     ${puedeVer?`onclick="verArchivoM('${esc(f.nombre_original||f.nombre_disco)}','${viewUrl}','${ext}')"`:''}
                     onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--bd-color)'">
            <i class="bi ${icon}" style="color:var(--primary);font-size:1.1rem;flex-shrink:0"></i>
            <div style="flex:1;min-width:0">
                <div style="font-size:.8rem;font-weight:600;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(f.nombre_original||f.nombre_disco)}</div>
                <div style="font-size:.68rem;color:var(--txt-muted)">${szStr} · ${esc(f.subido_por_nombre||'?')}${puedeVer?' · <span style="color:var(--primary)"><i class="bi bi-eye"></i> Clic para ver</span>':''}</div>
            </div>
            <a href="${downUrl}" download="${esc(f.nombre_original||'archivo')}" onclick="event.stopPropagation()"
               style="background:rgba(102,126,234,.15);color:var(--primary);border-radius:6px;padding:4px 8px;font-size:.72rem;text-decoration:none;flex-shrink:0" title="Descargar"><i class="bi bi-download"></i></a>
            ${f.puede_eliminar ? `<button onclick="event.stopPropagation();eliminarArchivoProyectoM(${pid},${f.id})"
               style="background:rgba(239,68,68,.1);border:none;color:#ef4444;border-radius:6px;padding:4px 8px;font-size:.72rem;cursor:pointer;flex-shrink:0"><i class="bi bi-trash3"></i></button>` : ''}
        </div>`;
    }).join('')}</div>`;
}

// ── Visor de archivos inline (maestro) ─────────────────────────
function verArchivoM(nombre, url, ext) {
    document.getElementById('_modalVisorM')?.remove();
    const m = document.createElement('div');
    m.id = '_modalVisorM'; m.className = 'modal fade'; m.tabIndex = -1;
    const esImagen = ['png','jpg','jpeg','gif','webp','svg'].includes(ext);
    const esPDF    = ext === 'pdf';
    const esTexto  = ['txt','md','csv','json','html','xml','js','php','css'].includes(ext);
    let body = '';
    if (esImagen) {
        body = `<div style="text-align:center;padding:10px;background:#000"><img src="${url}" alt="${esc(nombre)}" style="max-width:100%;max-height:72vh;object-fit:contain;border-radius:6px"></div>`;
    } else if (esPDF) {
        body = `<iframe src="${url}" style="width:100%;height:76vh;border:none" title="${esc(nombre)}"></iframe>`;
    } else if (esTexto) {
        body = `<div style="background:#0a0a14;padding:16px;max-height:72vh;overflow:auto"><pre id="_vtxt" style="color:#c0ccff;font-family:monospace;font-size:.82rem;margin:0;white-space:pre-wrap">Cargando…</pre></div>`;
    } else {
        body = `<div style="padding:40px;text-align:center;color:var(--txt-muted)"><i class="bi bi-file-earmark" style="font-size:3rem;display:block;margin-bottom:12px"></i>Vista previa no disponible para este tipo de archivo</div>`;
    }
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:min(900px,96vw)">
        <div class="modal-content" style="background:#111128;border:1px solid #2a2a4a;border-radius:14px;overflow:hidden">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:12px 18px;display:flex;align-items:center;gap:10px">
                <span style="color:#fff;font-weight:700;font-size:.9rem;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(nombre)}</span>
                <a href="${url}" download="${esc(nombre)}" style="background:rgba(255,255,255,.2);color:#fff;border:none;border-radius:8px;padding:4px 12px;font-size:.75rem;text-decoration:none;flex-shrink:0">
                    <i class="bi bi-download me-1"></i>Descargar</a>
                <button data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:26px;height:26px;border-radius:50%;cursor:pointer;flex-shrink:0"><i class="bi bi-x-lg"></i></button>
            </div>
            ${body}
        </div>
    </div>`;
    document.body.appendChild(m);
    const bsM = new bootstrap.Modal(m);
    m.addEventListener('hidden.bs.modal', () => m.remove());
    bsM.show();
    if (esTexto) fetch(url, { credentials: 'same-origin' }).then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.text(); }).then(t=>{ const el=m.querySelector('#_vtxt'); if(el) el.textContent=t; }).catch(e=>{ const el=m.querySelector('#_vtxt'); if(el) el.textContent='Error al cargar: '+e.message; });
}

function recargarVistaObservaciones() {
    if (window._obsEnProyectoM && window._proyectoDetalleIdM) {
        cargarObservacionesEnProyectoM(window._proyectoDetalleIdM, window._proyectoDetalleDiagsM || []);
    } else {
        const sel = document.getElementById('selectProyObs');
        if (sel?.value) cargarDiagramasParaObs(sel.value);
    }
}

async function cargarObservacionesEnProyectoM(pid, diags) {
    const panel = document.getElementById('proyObsPanelM');
    if (!panel || !pid) return;
    window._obsEnProyectoM = true;
    window._obsContainerId = 'obsDiagramaContainerM';

    if (!diags?.length) {
        panel.innerHTML = `<div style="text-align:center;padding:30px;color:var(--txt-muted)"><i class="bi bi-diagram-3" style="font-size:2rem;opacity:.3"></i><p style="margin-top:10px">No hay diagramas en este proyecto</p></div>`;
        return;
    }

    panel.innerHTML = `<div style="text-align:center;padding:30px"><div class="spinner-border text-primary"></div></div>`;
    try {
        let todasObs = {};
        const ro = await api(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}`);
        (ro.observaciones || []).forEach(o => {
            const did = o.diagrama_id;
            if (!todasObs[did]) todasObs[did] = o.texto;
            if (o.autor_id == MAESTRO_ID) todasObs[did] = o.texto;
        });
        window._maestroObsData = { pid, diags, p: { nombre: '' }, todasObs };
        panel.innerHTML = `
            <div style="margin-bottom:14px">
                <label class="form-label" style="font-size:.78rem;color:var(--txt-muted)">Diagrama a comentar</label>
                <select id="selectDiagObsM" class="form-control" style="font-size:.85rem" onchange="renderObsDiagrama(${pid}, this.value)">
                    <option value="all">Todos los diagramas</option>
                    ${diags.map(d => `<option value="${d.id}">${esc(d.titulo||'Sin título')} · ${TIPOS_P_M[d.tipo_diagrama]||d.tipo_diagrama}</option>`).join('')}
                </select>
            </div>
            <div id="obsDiagramaContainerM"></div>`;
        renderObsDiagrama(pid, diags[0]?.id || 'all');
    } catch (e) {
        panel.innerHTML = `<p style="color:#ef4444;padding:16px">${esc(e.message)}</p>`;
    }
}

// ── Persistencia de navegación (maestro) ───────────────────────
const M_NAV_KEY = 'maestro_nav_state';
function saveMNavState(pid=null) {
    // Also persist current theme so it survives the editor redirect
    if (window._themeConfig) sessionStorage.setItem('_uth_session', JSON.stringify(window._themeConfig));
    sessionStorage.setItem(M_NAV_KEY, JSON.stringify({
        fromEditor: true,
        section: document.querySelector('.nav-btn.active')?.id?.replace('nav-','') || 'diagramas',
        proyectoId: pid || window._proyectoDetalleIdM || null
    }));
}

function crearDiagramaParaProyectoM(pid) {
    saveMNavState(pid);
    document.getElementById('_modalNuevoDiagProyectoM')?.remove();
    const m = document.createElement('div');
    m.id = '_modalNuevoDiagProyectoM';
    m.className = 'modal fade';
    m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo diagrama en este proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="background:rgba(102,126,234,.08);border:1.5px solid rgba(102,126,234,.3);border-radius:10px;padding:10px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
                    <i class="bi bi-lock-fill" style="color:var(--primary);flex-shrink:0"></i>
                    <div>
                        <div style="font-size:.78rem;font-weight:700;color:var(--primary)">Proyecto seleccionado</div>
                        <div style="font-size:.73rem;color:var(--txt-muted)">El diagrama se asociará automáticamente a este proyecto al guardar</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-type me-1"></i>Título</label>
                    <input type="text" class="form-control" id="_ndpTituloM" placeholder="Ej: Diagrama del sistema">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-diagram-3 me-1"></i>Tipo de diagrama</label>
                    <input type="hidden" id="_ndpTipoM" value="usecase">
                    <div id="_ndpPickerM" style="max-height:260px;overflow-y:auto;padding-right:4px">
                        <!-- Se puebla con JS al abrir el modal -->
                        <div style="text-align:center;padding:12px;color:var(--txt-muted);font-size:.82rem">Cargando...</div>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label"><i class="bi bi-text-paragraph me-1"></i>Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="_ndpDescM" rows="2" placeholder="Breve descripción..."></textarea>
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-confirm" onclick="confirmarCrearDiagramaProyectoM(${pid})">
                    <i class="bi bi-pencil-square me-1"></i>Ir al editor
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    const bsM = new bootstrap.Modal(m);
    m.addEventListener('hidden.bs.modal', () => m.remove());
    bsM.show();
    setTimeout(() => {
        document.getElementById('_ndpTituloM')?.focus();
        // Poblar picker SVG
        _ndpMPoblarPicker('usecase');
    }, 100);
}

function _ndpMPoblarPicker(tipoActual) {
    const picker = document.getElementById('_ndpPickerM');
    if (!picker) return;
    const grupos = [
        { cat: 'Estructurales',  tipos: ['class','object','package','composite','component','deployment','profile'] },
        { cat: 'Comportamiento', tipos: ['usecase','activity','state'] },
        { cat: 'Interacción',    tipos: ['sequence','communication','timing','overview'] },
    ];
    const TLBL = {class:'Clases',object:'Objetos',package:'Paquetes',composite:'Estructura Compuesta',component:'Componentes',deployment:'Despliegue',profile:'Perfiles',usecase:'Casos de Uso',activity:'Actividades',state:'Máquina de Estado',sequence:'Secuencia',communication:'Comunicación',timing:'Tiempos',overview:'Descripción General'};
    let html = '';
    grupos.forEach(function(g) {
        html += '<div style="font-size:.67rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.07em;padding:7px 0 3px">' + g.cat + '</div>';
        g.tipos.forEach(function(tipo) {
            const icon = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(tipo, 32) : '';
            html += '<div class="_ndpMOpt" data-tipo="' + tipo + '"'
                + ' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;'
                + 'border:1.5px solid var(--bd-color);background:var(--bg-card);'
                + 'margin-bottom:5px;transition:all .15s">'
                + '<div style="width:32px;height:32px;flex-shrink:0">' + icon + '</div>'
                + '<span style="font-size:.81rem;font-weight:600;color:var(--txt-main)">' + (TLBL[tipo]||tipo) + '</span>'
                + '</div>';
        });
    });
    picker.innerHTML = html;
    picker.querySelectorAll('._ndpMOpt').forEach(function(el) {
        el.addEventListener('click', function() { _ndpMSelect(this.dataset.tipo); });
    });
    _ndpMSelect(tipoActual || 'usecase');
}

function _ndpMSelect(val) {
    const el = document.getElementById('_ndpTipoM');
    if (el) el.value = val;
    document.querySelectorAll('._ndpMOpt').forEach(function(e) {
        e.classList.toggle('activo', e.dataset.tipo === val);
    });
}

function confirmarCrearDiagramaProyectoM(pid) {
    const titulo = document.getElementById('_ndpTituloM')?.value.trim() || 'Nuevo Diagrama';
    const tipo   = document.getElementById('_ndpTipoM')?.value || 'usecase';
    const desc   = document.getElementById('_ndpDescM')?.value.trim() || '';

    sessionStorage.setItem('nuevoDiagrama', JSON.stringify({
        titulo, tipo, descripcion: desc, etiquetas: '', _projectId: pid
    }));
    bootstrap.Modal.getInstance(document.getElementById('_modalNuevoDiagProyectoM'))?.hide();
    window.location.href = (window.BASE_URL || '') + '/editor?tipo=' + tipo + '&proyecto=' + pid;
}

async function agregarDiagramaProyectoM(pid) {
    const sel = document.getElementById('selectAgregarDiagM');
    const did = parseInt(sel?.value);
    if (!did) { toast('Selecciona un diagrama','info'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=agregar_diagrama', {proyecto_id: pid, diagrama_id: did});
        if (r.success) { toast('Diagrama agregado','ok'); abrirProyectoM(pid); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function quitarDiagramaProyectoM(pid, did) {
    if (!confirm('¿Quitar este diagrama del proyecto? El diagrama no se elimina.')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=quitar_diagrama', {proyecto_id: pid, diagrama_id: did});
        if (r.success) { toast('Diagrama quitado','ok'); abrirProyectoM(pid); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function subirArchivosProyectoM(pid, files) {
    if (!files?.length) return;
    const input = document.getElementById('proyFileInputM');
    for (const file of files) {
        const fd = new FormData();
        fd.append('archivo', file);
        fd.append('proyecto_id', pid);
        try {
            const res = await fetch('<?= BASE_URL ?>/api/proyectos/upload', { method: 'POST', body: fd, credentials: 'same-origin' });
            const r = await res.json();
            if (r.success) toast(`"${r.nombre || file.name}" subido`, 'ok');
            else toast(r.error || ('Error: ' + file.name), 'err');
        } catch(e) { toast(e.message,'err'); }
    }
    if (input) input.value = '';
    abrirProyectoM(pid);
}

async function eliminarArchivoProyectoM(pid, fid) {
    if (!confirm('¿Eliminar este archivo?')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=eliminar_archivo', {archivo_id: fid, proyecto_id: pid});
        if (r.success) { toast('Archivo eliminado','ok'); abrirProyectoM(pid); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function confirmarEliminarProyectoM(pid) {
    if (!confirm('¿Eliminar este proyecto? Los diagramas NO se eliminan.')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=eliminar', {proyecto_id: pid});
        if (r.success) { toast('Proyecto eliminado','ok'); renderProyectosMaestro(); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function confirmarSalirProyectoM(pid) {
    if (!confirm('¿Salir de este proyecto?')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=salir', {proyecto_id: pid});
        if (r.success) { toast('Saliste del proyecto','ok'); renderProyectosMaestro(); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

function modalNuevoProyectoM() {
    document.getElementById('_modalProyM')?.remove();
    const m = document.createElement('div'); m.id='_modalProyM'; m.className='modal fade'; m.tabIndex=-1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 20px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:10px">
                <div><label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Nombre *</label>
                <input id="_proyMNom" type="text" placeholder="Nombre del proyecto" maxlength="100"
                    style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem;box-sizing:border-box"></div>
                <div><label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Descripción</label>
                <textarea id="_proyMDesc" rows="2" placeholder="Descripción breve"
                    style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem;resize:vertical;box-sizing:border-box"></textarea></div>
            </div>
            <div style="padding:0 20px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.83rem;cursor:pointer">Cancelar</button>
                <button onclick="crearProyMConfirm()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer">Crear</button>
            </div>
        </div></div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
    m.addEventListener('shown.bs.modal',()=>document.getElementById('_proyMNom')?.focus());
}

async function crearProyMConfirm() {
    const nombre = document.getElementById('_proyMNom')?.value?.trim();
    const descripcion = document.getElementById('_proyMDesc')?.value?.trim();
    if (!nombre) { toast('El nombre es obligatorio','err'); return; }
    try {
        const d = await api('<?= BASE_URL ?>/api/proyectos?action=crear', {nombre, descripcion});
        if (d.success) { bootstrap.Modal.getInstance(document.getElementById('_modalProyM'))?.hide(); toast('Proyecto creado','ok'); renderProyectosMaestro(); }
        else throw new Error(d.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

function modalUnirseProyectoM() {
    document.getElementById('_modalProyMU')?.remove();
    const m = document.createElement('div'); m.id='_modalProyMU'; m.className='modal fade'; m.tabIndex=-1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:360px">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 20px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-key me-2"></i>Unirse con Código</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:20px">
                <label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Código de invitación</label>
                <input id="_proyMCod" type="text" placeholder="ABC123" maxlength="20"
                    style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:1rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;box-sizing:border-box">
            </div>
            <div style="padding:0 20px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.83rem;cursor:pointer">Cancelar</button>
                <button onclick="unirseProyMConfirm()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer">Unirse</button>
            </div>
        </div></div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
    m.addEventListener('shown.bs.modal',()=>document.getElementById('_proyMCod')?.focus());
}

async function unirseProyMConfirm() {
    const codigo = document.getElementById('_proyMCod')?.value?.trim().toUpperCase();
    if (!codigo) { toast('Ingresa el código','err'); return; }
    try {
        const d = await api('<?= BASE_URL ?>/api/proyectos?action=unirse', {codigo});
        if (d.success) { bootstrap.Modal.getInstance(document.getElementById('_modalProyMU'))?.hide(); toast('Te uniste al proyecto','ok'); renderProyectosMaestro(); }
        else throw new Error(d.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}


// ── Plantillas del sistema para maestro ─────────────────────────
const TIPO_LBL_M = {usecase:'Casos de Uso',class:'Clases',sequence:'Secuencia',activity:'Actividades',state:'Estados',component:'Componentes',deployment:'Despliegue',object:'Objetos',communication:'Comunicación'};
const TIPO_ICO_M = {usecase:'bi-person-circle',class:'bi-diagram-3',sequence:'bi-arrow-left-right',activity:'bi-activity',state:'bi-toggles',component:'bi-puzzle',deployment:'bi-cloud',object:'bi-box',communication:'bi-chat-dots'};

async function renderPlantillasMaestro() {
    const main = document.getElementById('contentArea');
    const escH = s => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    main.innerHTML = `<div style="text-align:center;padding:50px"><div class="spinner-border" style="color:var(--primary)"></div></div>`;
    try {
        const r = await api('<?= BASE_URL ?>/api/plantillas-sistema');
        const plantas = r.plantillas || [];
        if (plantas.length === 0) {
            main.innerHTML = `<div style="text-align:center;padding:60px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:16px">
                <i class="bi bi-layout-text-sidebar-reverse" style="font-size:3rem;color:var(--txt-muted);opacity:.3"></i>
                <p style="color:var(--txt-muted);margin-top:14px">El administrador no ha publicado plantillas aún.</p>
            </div>`;
            return;
        }
        const grouped = {};
        plantas.forEach(p => { (grouped[p.tipo_diagrama]||=[]).push(p); });
        let html = '<div style="margin-bottom:16px"><p style="color:var(--txt-muted);font-size:.82rem">Selecciona una plantilla para crear un diagrama con elementos predefinidos.</p></div>';
        Object.entries(grouped).forEach(([tipo, lista]) => {
            html += `<div style="margin-bottom:22px"><h5 style="font-size:.88rem;font-weight:700;color:var(--primary);margin-bottom:10px"><i class="bi ${TIPO_ICO_M[tipo]||'bi-diagram-3'} me-2"></i>${TIPO_LBL_M[tipo]||tipo} <span style="background:var(--bg-hover);color:var(--txt-muted);border-radius:8px;padding:1px 8px;font-size:.7rem">${lista.length}</span></h5><div class="row g-3">`;
            lista.forEach(p => {
                const cont = JSON.parse(p.contenido_json||'{"nodes":[],"connections":[]}');
                html += `<div class="col-md-6 col-lg-4"><div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;overflow:hidden;cursor:pointer;transition:all .18s"
                    onclick="usarPlantillaSistemaM(${p.id},'${escH(p.tipo_diagrama)}')"
                    onmouseover="this.style.borderColor=getComputedStyle(document.documentElement).getPropertyValue('--primary');this.style.transform='translateY(-3px)'"
                    onmouseout="this.style.borderColor='';this.style.transform=''">
                    <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:12px 16px">
                        <div style="font-weight:700;color:#fff;font-size:.88rem">${escH(p.titulo)}</div>
                        <div style="font-size:.68rem;color:rgba(255,255,255,.75)">${escH(p.nombre_completo||p.username||'Sistema')}</div>
                    </div>
                    <div style="padding:12px 16px">
                        <p style="font-size:.77rem;color:var(--txt-muted);margin:0 0 10px">${escH(p.descripcion||'Sin descripción')}</p>
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:.68rem;color:var(--txt-muted)">${(cont.nodes||[]).length} elementos</span>
                            <button onclick="event.stopPropagation();usarPlantillaSistemaM(${p.id},'${escH(p.tipo_diagrama)}')"
                                style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:5px 14px;font-size:.75rem;font-weight:600;cursor:pointer">
                                <i class="bi bi-pencil-square me-1"></i>Usar
                            </button>
                        </div>
                    </div>
                </div></div>`;
            });
            html += '</div></div>';
        });
        main.innerHTML = html;
    } catch(e) { main.innerHTML = `<p style="color:var(--txt-muted);text-align:center">${escH(e.message)}</p>`; }
}

async function usarPlantillaSistemaM(plantillaId, tipo) {
    try {
        const r = await api('<?= BASE_URL ?>/api/plantillas-sistema');
        const p = (r.plantillas||[]).find(x => x.id == plantillaId);
        if (!p) throw new Error('Plantilla no encontrada');
        const cont = JSON.parse(p.contenido_json||'{"nodes":[],"connections":[]}');
        sessionStorage.setItem('plantillaData', JSON.stringify({ nodes:cont.nodes||[], connections:cont.connections||[], diagramType:p.tipo_diagrama||tipo }));
        window.location.href = '<?= BASE_URL ?>/editor?tipo=' + (p.tipo_diagrama||tipo) + '&fromPlantilla=1';
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// OBSERVACIONES — hub de conversaciones maestro/alumno
// ════════════════════════════════════════════════════════════
async function renderObservaciones() {
    loading();
    const main = document.getElementById('contentArea');
    try {
        const [dataP, dataResp] = await Promise.all([
            api('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos'),
            api('<?= BASE_URL ?>/api/maestro?action=respuestas_recientes').catch(()=>({respuestas:[]}))
        ]);
        const proyectos = dataP.proyectos || [];
        const respRecientes = dataResp.respuestas || [];

        if (proyectos.length === 0) {
            main.innerHTML = `<div class="sec-card"><div class="empty-state">
                <i class="bi bi-chat-left-text"></i>
                <p>No estás en ningún proyecto aún.</p>
                <button class="btn-primary-m" onclick="showSection('proyectos')">Ir a Proyectos</button>
            </div></div>`;
            return;
        }

        // Panel de respuestas recientes de alumnos
        let respRecientesHtml = '';
        if (respRecientes.length > 0) {
            respRecientesHtml = `
            <div style="background:var(--bg-card);border:1.5px solid rgba(16,185,129,.35);border-radius:14px;margin-bottom:20px;overflow:hidden">
                <div style="padding:12px 16px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px">
                    <i class="bi bi-chat-dots" style="color:#10b981;font-size:1.1rem"></i>
                    <span style="font-weight:700;color:var(--txt-main);font-size:.92rem">Respuestas recientes de alumnos</span>
                    <span style="background:rgba(16,185,129,.15);color:#10b981;border-radius:10px;padding:1px 9px;font-size:.72rem;font-weight:700">${respRecientes.length}</span>
                </div>
                <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px">
                ${respRecientes.map(r => {
                    const fecha = new Date(r.fecha_creacion).toLocaleString('es-MX');
                    return `<div style="padding:10px 12px;border:1px solid var(--bd-color);border-left:3px solid #10b981;border-radius:10px;background:var(--bg-deep);cursor:pointer"
                                 onclick="navegarAObservacionM(${r.proyecto_id},${r.diagrama_id||0},${r.padre_id||0})"
                                 onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='var(--bg-deep)'">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                            <div style="width:26px;height:26px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.68rem;color:#fff;flex-shrink:0">
                                ${esc((r.alumno_nombre||r.alumno_username||'?')[0].toUpperCase())}
                            </div>
                            <span style="font-weight:600;font-size:.8rem;color:var(--txt-main)">${esc(r.alumno_nombre||r.alumno_username)}</span>
                            <span style="font-size:.68rem;color:var(--txt-muted)">respondió en <strong>${esc(r.proyecto_nombre||'—')}</strong> · ${esc(r.diagrama_titulo||'Diagrama')}</span>
                            <span style="margin-left:auto;font-size:.65rem;color:var(--txt-muted)">${fecha}</span>
                        </div>
                        <div style="font-size:.82rem;color:var(--txt-main);white-space:pre-wrap;line-height:1.5;margin-bottom:6px">${esc(r.texto)}</div>
                        <div style="font-size:.7rem;color:var(--txt-muted)"><i class="bi bi-arrow-return-left me-1"></i>En respuesta a: "${esc((r.obs_original||'').substring(0,80))}${(r.obs_original||'').length>80?'...':''}"</div>
                        <div style="margin-top:6px">
                            <button onclick="event.stopPropagation();navegarAObservacionM(${r.proyecto_id},${r.diagrama_id||0},${r.padre_id||0})"
                                style="background:rgba(102,126,234,.1);border:1px solid rgba(102,126,234,.2);color:var(--primary);border-radius:7px;padding:3px 12px;font-size:.72rem;cursor:pointer">
                                <i class="bi bi-reply me-1"></i>Ver hilo y responder
                            </button>
                        </div>
                    </div>`;
                }).join('')}
                </div>
            </div>`;
        }

        main.innerHTML = `
        ${respRecientesHtml}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;flex-wrap:wrap">
            <label style="font-size:.85rem;font-weight:600;color:var(--txt-main);white-space:nowrap">
                <i class="bi bi-folder2-open me-1" style="color:var(--primary)"></i>Agregar observación a proyecto:
            </label>
            <select id="selectProyObs" class="form-control" style="max-width:340px;font-size:.85rem"
                onchange="cargarDiagramasParaObs(this.value)">
                <option value="">— Selecciona un proyecto —</option>
                ${proyectos.map(p=>`<option value="${p.id}">${esc(p.nombre)}</option>`).join('')}
            </select>
        </div>
        <div id="obsContenido">
            <div class="sec-card"><div class="empty-state" style="padding:40px">
                <i class="bi bi-chat-left-text" style="font-size:2rem;opacity:.3"></i>
                <p style="margin-top:10px;font-size:.85rem;color:var(--txt-muted)">Selecciona un proyecto para ver y comentar sus diagramas</p>
            </div></div>
        </div>`;
    } catch(e) {
        main.innerHTML = `<div class="sec-card"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>${esc(e.message)}</p></div></div>`;
    }
}

async function cargarDiagramasParaObs(pid) {
    window._obsEnProyectoM = false;
    window._obsContainerId = 'obsDiagramaContainer';
    const cont = document.getElementById('obsContenido');
    if (!pid) {
        cont.innerHTML = `<div class="sec-card"><div class="empty-state" style="padding:40px">
            <i class="bi bi-arrow-up" style="font-size:2rem;opacity:.3"></i>
            <p style="margin-top:10px;font-size:.85rem;color:var(--txt-muted)">Selecciona un proyecto</p>
        </div></div>`;
        return;
    }
    cont.innerHTML = `<div style="text-align:center;padding:40px"><div class="spinner-border" style="color:var(--primary)"></div></div>`;
    try {
        const data = await api(`<?= BASE_URL ?>/api/proyectos?action=detalle&id=${pid}`);
        if (!data || !data.proyecto) throw new Error(data?.error || 'No se pudo cargar el proyecto');
        const diags = data.diagramas || [];
        const p = data.proyecto;

        if (diags.length === 0) {
            cont.innerHTML = `<div class="sec-card"><div class="empty-state">
                <i class="bi bi-diagram-3"></i>
                <p>No hay diagramas en <strong>${esc(p.nombre)}</strong></p>
            </div></div>`;
            return;
        }

        // Cargar observaciones desde servidor
        let todasObs = {};
        try {
            const ro = await api(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}`);
            const obsArr = ro.observaciones || [];
            // mapear diagrama_id => texto del autor actual si existe, sino la primera observación
            obsArr.forEach(o => {
                const did = o.diagrama_id;
                if (!todasObs[did]) todasObs[did] = o.texto;
                if (o.autor_id == MAESTRO_ID) todasObs[did] = o.texto;
            });
        } catch(e) {
            // Si falla el servidor, mostrar vacío
            todasObs = {};
        }

        window._maestroObsData = { pid, diags, p, todasObs };
        const selectedId = diags[0]?.id || 'all';
        cont.innerHTML = `
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px;margin-bottom:18px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="bi bi-folder2-open" style="color:var(--primary);font-size:1.2rem"></i>
                <div>
                    <div style="font-weight:700;color:var(--txt-main);font-size:.95rem">${esc(p.nombre)}</div>
                    <div style="font-size:.78rem;color:var(--txt-muted)">${diags.length} diagrama${diags.length!=1?'s':''} en el proyecto</div>
                </div>
            </div>
            <div style="margin-left:auto;min-width:240px;width:100%;max-width:360px">
                <label class="form-label" style="font-size:.78rem;color:var(--txt-muted);margin-bottom:6px;display:block">Diagrama a comentar</label>
                <select id="selectDiagObs" class="form-control" style="font-size:.85rem" onchange="renderObsDiagrama(${pid}, this.value)">
                    <option value="all">Todos los diagramas</option>
                    ${diags.map(d => `<option value="${d.id}">${esc(d.titulo||'Sin título')} · ${TIPOS_P_M[d.tipo_diagrama]||d.tipo_diagrama}</option>`).join('')}
                </select>
            </div>
        </div>
        <div id="obsDiagramaContainer"></div>`;

        document.getElementById('selectDiagObs').value = selectedId;
        renderObsDiagrama(pid, selectedId);

    } catch(e) {
        cont.innerHTML = `<div class="sec-card"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>${esc(e.message)}</p></div></div>`;
    }
}

function renderObsDiagrama(pid, selectedId) {
    const data = window._maestroObsData;
    if (!data) return;
    const diags = data.diags || [];
    const todasObs = data.todasObs || {};
    const cont = document.getElementById(window._obsContainerId || 'obsDiagramaContainer');
    if (!cont) return;

    if (selectedId === 'all' || selectedId === '' || selectedId === null) {
        cont.innerHTML = diags.map(d => {
            const obs = todasObs[d.id] || '';
            const savedBadge = obs
                ? `<div style="font-size:.68rem;color:#10b981;text-align:center;margin-top:4px"><i class="bi bi-check-circle me-1"></i>Observación guardada</div>`
                : '';
            return `
            <div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;margin-bottom:20px;overflow:hidden">
                <div style="padding:12px 16px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:12px">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="bi bi-diagram-3" style="color:#fff;font-size:.9rem"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:700;color:var(--txt-main);font-size:.9rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(d.titulo||'Sin título')}</div>
                        <div style="font-size:.72rem;color:var(--txt-muted)">por <strong>${esc(d.autor||'—')}</strong> · v${d.version||1} · ${TIPOS_P_M[d.tipo_diagrama]||d.tipo_diagrama}</div>
                    </div>
                    <a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:6px 14px;font-size:.75rem;font-weight:600;text-decoration:none;white-space:nowrap;flex-shrink:0">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Abrir editor
                    </a>
                </div>
                <div style="display:grid;grid-template-columns:1fr 360px;min-height:360px">
                    <div style="position:relative;overflow:hidden;background:var(--bg-card);border-right:1px solid var(--bd-color);display:flex;align-items:center;justify-content:center">
                        <div style="position:absolute;top:8px;left:10px;z-index:2;background:rgba(0,0,0,.6);color:#fff;font-size:.65rem;padding:2px 8px;border-radius:10px">
                            <i class="bi bi-eye me-1"></i>Vista previa
                        </div>
                        <div id="canvas_preview_${d.id}" style="width:100%;height:360px;overflow:auto;display:flex;align-items:center;justify-content:center">
                            <div style="color:#555;font-size:.8rem;text-align:center">
                                <div class="spinner-border spinner-border-sm" style="color:#667eea;margin-bottom:8px"></div>
                                <div>Cargando diagrama…</div>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;padding:16px;gap:10px;background:var(--bg-card)">
                        <div style="font-size:.78rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.05em">
                            <i class="bi bi-chat-left-quote me-1"></i>Observaciones del maestro
                        </div>
                        <textarea id="obs_${d.id}" rows="10" style="flex:1;width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:10px 12px;font-size:.82rem;resize:none;outline:none;transition:border-color .2s;font-family:inherit;line-height:1.5" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bd-color)'" placeholder="Escribe correcciones, sugerencias o comentarios sobre este diagrama…">${esc(obs)}</textarea>
                        <div style="display:flex;gap:8px">
                            <button onclick="guardarObsLocal(${pid},${d.id},'${esc(d.titulo||'Sin título')}')" style="flex:1;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:9px;font-size:.82rem;font-weight:600;cursor:pointer">
                                <i class="bi bi-floppy me-1"></i>Guardar
                            </button>
                            ${obs ? `<button onclick="limpiarObs(${pid},${d.id})" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:8px;padding:9px 13px;font-size:.82rem;cursor:pointer" title="Borrar observación"><i class="bi bi-trash3"></i></button>` : ''}
                        </div>
                        ${savedBadge}
                    </div>
                </div>
            </div>`;
        }).join('');
        diags.forEach(d => renderDiagramaPreview(d.id));
        return;
    }

    const selected = diags.find(d => String(d.id) === String(selectedId));
    if (!selected) {
        cont.innerHTML = `<div class="sec-card"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Diagrama no encontrado</p></div></div>`;
        return;
    }

    const obs = todasObs[selected.id] || '';
    const savedBadge = obs
        ? `<div style="font-size:.68rem;color:#10b981;text-align:center;margin-top:4px"><i class="bi bi-check-circle me-1"></i>Observación guardada</div>`
        : '';

    cont.innerHTML = `
        <div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;overflow:hidden">
            <div style="padding:12px 16px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:12px">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-diagram-3" style="color:#fff;font-size:.9rem"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;color:var(--txt-main);font-size:.9rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(selected.titulo||'Sin título')}</div>
                    <div style="font-size:.72rem;color:var(--txt-muted)">por <strong>${esc(selected.autor||'—')}</strong> · v${selected.version||1} · ${TIPOS_P_M[selected.tipo_diagrama]||selected.tipo_diagrama}</div>
                </div>
                <a href="<?= BASE_URL ?>/editor?id=${selected.id}" target="_blank" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:6px 14px;font-size:.75rem;font-weight:600;text-decoration:none;white-space:nowrap;flex-shrink:0">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Abrir editor
                </a>
            </div>
            <div style="display:grid;grid-template-columns:1fr 360px;min-height:360px">
                <div style="position:relative;overflow:hidden;background:var(--bg-card);border-right:1px solid var(--bd-color);display:flex;align-items:center;justify-content:center">
                    <div style="position:absolute;top:8px;left:10px;z-index:2;background:rgba(0,0,0,.6);color:#fff;font-size:.65rem;padding:2px 8px;border-radius:10px">
                        <i class="bi bi-eye me-1"></i>Vista previa
                    </div>
                    <div id="canvas_preview_${selected.id}" style="width:100%;height:360px;overflow:auto;display:flex;align-items:center;justify-content:center">
                        <div style="color:#555;font-size:.8rem;text-align:center">
                            <div class="spinner-border spinner-border-sm" style="color:#667eea;margin-bottom:8px"></div>
                            <div>Cargando diagrama…</div>
                        </div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;padding:16px;gap:10px;background:var(--bg-card)">
                    <div style="font-size:.78rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-chat-left-quote me-1"></i>Observaciones del maestro
                    </div>
                    <textarea id="obs_${selected.id}" rows="10" style="flex:1;width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:10px 12px;font-size:.82rem;resize:none;outline:none;transition:border-color .2s;font-family:inherit;line-height:1.5" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bd-color)'" placeholder="Escribe correcciones, sugerencias o comentarios sobre este diagrama…">${esc(obs)}</textarea>
                    <div style="display:flex;gap:8px">
                        <button onclick="guardarObsLocal(${pid},${selected.id},'${esc(selected.titulo||'Sin título')}')" style="flex:1;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:9px;font-size:.82rem;font-weight:600;cursor:pointer">
                            <i class="bi bi-floppy me-1"></i>Guardar
                        </button>
                        ${obs ? `<button onclick="limpiarObs(${pid},${selected.id})" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:8px;padding:9px 13px;font-size:.82rem;cursor:pointer" title="Borrar observación"><i class="bi bi-trash3"></i></button>` : ''}
                    </div>
                    ${savedBadge}
                </div>
            </div>
        </div>`;
    renderDiagramaPreview(selected.id);
}

// ── Renderiza el diagrama como SVG en el panel de preview de observaciones ──
async function renderDiagramaPreview(diagramaId) {
    const container = document.getElementById('canvas_preview_' + diagramaId);
    if (!container) return;
    try {
        const data = await api(`<?= BASE_URL ?>/api/diagramas/load?id=${diagramaId}`);
        const contenido = data.diagrama?.contenido || data.contenido || {};
        const nodes = contenido.nodes || [];
        const connections = contenido.connections || [];

        if (nodes.length === 0) {
            container.innerHTML = `<div style="color:#555;text-align:center;font-size:.82rem">
                <i class="bi bi-diagram-3" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3"></i>
                Diagrama vacío
            </div>`;
            return;
        }

        // Calcular bounding box
        let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
        nodes.forEach(n => {
            const w = n.width || 120, h = n.height || 60;
            minX = Math.min(minX, n.x);
            minY = Math.min(minY, n.y);
            maxX = Math.max(maxX, n.x + w);
            maxY = Math.max(maxY, n.y + h);
        });

        const pad = 40;
        const contentW = maxX - minX + pad * 2;
        const contentH = maxY - minY + pad * 2;
        const cW = container.offsetWidth  || 600;
        const cH = container.offsetHeight || 360;
        const scale = Math.min(cW / contentW, cH / contentH, 1.4);
        const offsetX = pad - minX;
        const offsetY = pad - minY;

        const nodeColors = {
            usecase: { fill: '#ffffff', stroke: '#667eea', text: '#111827' },
            actor:   { fill: 'none',    stroke: '#111827', text: '#111827' },
            class:   { fill: '#ffffff', stroke: '#4a5568', text: '#111827' },
            system:  { fill: '#ffffff', stroke: '#6b7280', text: '#111827' },
            default: { fill: '#ffffff', stroke: '#667eea', text: '#111827' }
        };

        let svgNodes = '', svgConns = '';

        nodes.forEach(n => {
            const nx = (n.x + offsetX) * scale;
            const ny = (n.y + offsetY) * scale;
            const nw = (n.width  || 120) * scale;
            const nh = (n.height ||  60) * scale;
            const c  = nodeColors[n.type] || nodeColors.default;
            const label = (n.text || n.label || '').slice(0, 28);
            const fs = Math.max(9, Math.min(13, 11 * scale));

            if (n.type === 'actor') {
                const cx = nx + nw / 2, r = Math.max(8, 12 * scale);
                const legY = ny + r * 2 + 18 * scale;
                svgNodes += `
                    <circle cx="${cx}" cy="${ny + r}" r="${r}" fill="none" stroke="${c.stroke}" stroke-width="1.5"/>
                    <line x1="${cx}" y1="${ny+r*2}" x2="${cx}" y2="${legY}" stroke="${c.stroke}" stroke-width="1.5"/>
                    <line x1="${cx-13*scale}" y1="${ny+r*2+7*scale}" x2="${cx+13*scale}" y2="${ny+r*2+7*scale}" stroke="${c.stroke}" stroke-width="1.5"/>
                    <line x1="${cx}" y1="${legY}" x2="${cx-10*scale}" y2="${legY+14*scale}" stroke="${c.stroke}" stroke-width="1.5"/>
                    <line x1="${cx}" y1="${legY}" x2="${cx+10*scale}" y2="${legY+14*scale}" stroke="${c.stroke}" stroke-width="1.5"/>
                    <text x="${cx}" y="${legY+24*scale}" text-anchor="middle" fill="${c.text}" font-size="${fs}px" font-family="sans-serif">${label}</text>`;
            } else if (n.type === 'usecase') {
                svgNodes += `
                    <ellipse cx="${nx+nw/2}" cy="${ny+nh/2}" rx="${nw/2}" ry="${nh/2}" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5"/>
                    <text x="${nx+nw/2}" y="${ny+nh/2+fs*.35}" text-anchor="middle" fill="${c.text}" font-size="${fs}px" font-family="sans-serif">${label}</text>`;
            } else if (n.type === 'system') {
                svgNodes += `
                    <rect x="${nx}" y="${ny}" width="${nw}" height="${nh}" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5" rx="4"/>
                    <text x="${nx+8}" y="${ny-6}" fill="${c.text}" font-size="${Math.max(8,fs-2)}px" font-family="sans-serif" font-weight="600">${label}</text>`;
            } else {
                svgNodes += `
                    <rect x="${nx}" y="${ny}" width="${nw}" height="${nh}" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5" rx="6"/>
                    <text x="${nx+nw/2}" y="${ny+nh/2+fs*.35}" text-anchor="middle" fill="${c.text}" font-size="${fs}px" font-family="sans-serif">${label}</text>`;
            }
        });

        connections.forEach(conn => {
            // soportar varias estructuras: from/to, fromNode/toNode, source/target, sourceId/targetId
            const sId = conn.from || conn.fromNode || conn.source || conn.sourceId || conn.fromId || conn.source_id || conn.sourceId;
            const tId = conn.to   || conn.toNode   || conn.target || conn.targetId || conn.toId   || conn.target_id || conn.targetId;
            const from = nodes.find(n => String(n.id) === String(sId));
            const to   = nodes.find(n => String(n.id) === String(tId));
            if (!from || !to) return;
            const fx = ((from.x + (from.width ||120)/2) + offsetX) * scale;
            const fy = ((from.y + (from.height|| 60)/2) + offsetY) * scale;
            const tx = ((to.x   + (to.width   ||120)/2) + offsetX) * scale;
            const ty = ((to.y   + (to.height  || 60)/2) + offsetY) * scale;
            svgConns += `<line x1="${fx}" y1="${fy}" x2="${tx}" y2="${ty}" stroke="#374151" stroke-width="1.6" opacity=".95" marker-end="url(#arw${diagramaId})"/>`;
        });

        const svgW = contentW * scale;
        const svgH = contentH * scale;

        container.innerHTML = `
        <svg width="${svgW}" height="${svgH}" viewBox="0 0 ${svgW} ${svgH}"
             xmlns="http://www.w3.org/2000/svg" style="display:block;margin:auto;max-width:100%;max-height:100%">
            <defs>
                <marker id="arw${diagramaId}" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto">
                    <path d="M0,0 L0,6 L8,3 z" fill="#374151" opacity=".95"/>
                </marker>
            </defs>
            ${svgConns}${svgNodes}
        </svg>`;

    } catch(e) {
        if (container) container.innerHTML = `<div style="color:#666;text-align:center;font-size:.78rem;padding:20px">
            <i class="bi bi-exclamation-circle" style="display:block;margin-bottom:6px;font-size:1.4rem"></i>No se pudo cargar
        </div>`;
    }
}

async function guardarObsLocal(pid, did, titulo) {
    const texto = document.getElementById('obs_'+did)?.value || '';
    if (!texto) { toast('El texto de la observación está vacío','err'); return; }

    let data;
    try {
        data = await api('<?= BASE_URL ?>/api/observaciones', { proyecto_id: pid, diagrama_id: did, texto });
    } catch (e) {
        toast('Error de conexión al guardar observación','err');
        return;
    }

    if (!data || !data.success) {
        toast('Error al guardar la observación: ' + (data?.error || 'Respuesta inválida'),'err');
        return;
    }

    toast('Observación guardada','ok');
    // V46: Cargar hilo de respuestas después de guardar
    if (data.obs_id) {
        await cargarHiloRespuestas(data.obs_id, `hilo_${did}`);
    }
    recargarVistaObservaciones();
}

async function limpiarObs(pid, did) {
    if (!confirm('¿Borrar esta observación?')) return;
    try {
        const r = await api(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}&diagrama_id=${did}`);
        const arr = r.observaciones || [];
        const miObs = arr.find(x => x.autor_id == MAESTRO_ID) || arr[0];
        if (miObs && miObs.id) {
            const data = await api('<?= BASE_URL ?>/api/observaciones/del', { obs_id: miObs.id });
            if (data && data.success) { toast('Observación eliminada','ok'); recargarVistaObservaciones(); return; }
        }
    } catch(e) {}
    toast('No se pudo eliminar la observación','err');
}

// ── V46: Sistema de replies / historial de conversación ──────────────────

/**
 * Renderiza el hilo de respuestas de una observación en el contenedor dado.
 * containerId: ID del <div> donde inyectar el hilo
 */
async function cargarHiloRespuestas(obsId, containerId) {
    const cont = document.getElementById(containerId);
    if (!cont) return;
    cont.innerHTML = '<div style="text-align:center;padding:10px"><div class="spinner-border spinner-border-sm" style="color:var(--primary)"></div></div>';
    try {
        const data = await api(`<?= BASE_URL ?>/api/observaciones/thread?obs_id=${obsId}`);
        const replies = data.replies || [];
        if (!replies.length) {
            cont.innerHTML = '<p style="font-size:.7rem;color:var(--txt-muted);text-align:center;padding:8px 0;margin:0"><i class="bi bi-chat-dots me-1"></i>Sin respuestas aún</p>';
            return;
        }
        const rolColors = { maestro:'linear-gradient(135deg,#667eea,#764ba2)', alumno:'linear-gradient(135deg,#10b981,#059669)', admin:'linear-gradient(135deg,#f59e0b,#d97706)' };
        cont.innerHTML = replies.map(r => {
            const isMine = r.autor_id == MAESTRO_ID;
            const bg = rolColors[r.rol_autor] || 'linear-gradient(135deg,#6b7280,#4b5563)';
            return `<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:8px;${isMine?'flex-direction:row-reverse':''}">
                <div style="width:26px;height:26px;border-radius:50%;background:${bg};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.68rem;color:#fff;flex-shrink:0">
                    ${esc((r.autor_nombre||'?')[0].toUpperCase())}
                </div>
                <div style="background:${isMine?'rgba(102,126,234,.12)':'var(--bg-deep)'};border:1px solid var(--bd-color);border-radius:${isMine?'10px 0 10px 10px':'0 10px 10px 10px'};padding:7px 10px;max-width:80%">
                    <div style="font-size:.65rem;color:var(--txt-muted);margin-bottom:3px">${esc(r.autor_nombre||r.autor_username)}${isMine?' (tú)':''} · ${new Date(r.fecha_creacion).toLocaleString('es-MX')}</div>
                    <div style="font-size:.78rem;color:var(--txt-main);white-space:pre-wrap;line-height:1.4">${esc(r.texto)}</div>
                </div>
            </div>`;
        }).join('');
    } catch(e) {
        cont.innerHTML = `<p style="font-size:.7rem;color:#ef4444;padding:4px 0;margin:0">${esc(e.message)}</p>`;
    }
}

async function enviarReplyObs(obsId, containerId, inputId) {
    const inp = document.getElementById(inputId);
    const txt = inp ? inp.value.trim() : '';
    if (!txt) { toast('Escribe una respuesta','info'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/observaciones/reply', { obs_id: obsId, texto: txt });
        if (!r.success) throw new Error(r.error || 'Error al enviar respuesta');
        if (inp) inp.value = '';
        toast('Respuesta enviada','ok');
        await cargarHiloRespuestas(obsId, containerId);
    } catch(e) { toast(e.message,'err'); }
}

/**
 * Agrega el panel de historial de respuestas a un bloque de observación ya renderizado.
 * Se llama después de que renderObsDiagrama inyecta el HTML.
 */
async function adjuntarHilosObs(pid, diags) {
    const ro = await api(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}`);
    const obsArr = ro.observaciones || [];
    for (const obs of obsArr) {
        const did = obs.diagrama_id;
        const hiloId = `hilo_${did}`;
        // Buscar si ya existe el contenedor de hilo
        let hiloEl = document.getElementById(hiloId);
        if (!hiloEl) {
            // Crear panel de hilo debajo del textarea del diagrama
            const parent = document.getElementById('obs_' + did)?.closest?.('div[style*="flex-direction:column"]');
            if (parent) {
                const panel = document.createElement('div');
                panel.id = hiloId;
                panel.style.cssText = 'margin-top:6px;max-height:180px;overflow-y:auto;border-top:1px solid var(--bd-color);padding-top:8px';
                // Agregar campo de reply del alumno visible aquí como historial
                const replyBar = document.createElement('div');
                replyBar.id = `replyBar_${did}`;
                replyBar.innerHTML = `
                    <div style="font-size:.68rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
                        <i class="bi bi-chat-dots me-1"></i>Historial de respuestas
                    </div>`;
                parent.appendChild(replyBar);
                parent.appendChild(panel);
            }
            hiloEl = document.getElementById(hiloId);
        }
        if (hiloEl && obs.id) {
            await cargarHiloRespuestas(obs.id, hiloId);
            // Guardar obs.id en dataset para poder hacer reply
            hiloEl.dataset.obsId = obs.id;
        }
    }
}

// ── V46: Reply del maestro a observaciones del alumno ──────────────────────

window._hiloVisibleM = {};

async function toggleHiloMaestro(obsId) {
    const cont = document.getElementById('hiloM_' + obsId);
    if (!cont) return;
    if (window._hiloVisibleM[obsId]) {
        cont.innerHTML = '';
        window._hiloVisibleM[obsId] = false;
        return;
    }
    window._hiloVisibleM[obsId] = true;
    await cargarHiloMaestro(obsId);
}

async function cargarHiloMaestro(obsId) {
    const cont = document.getElementById('hiloM_' + obsId);
    if (!cont) return;
    cont.innerHTML = '<div style="text-align:center;padding:8px"><div class="spinner-border spinner-border-sm" style="color:var(--primary)"></div></div>';
    try {
        const data = await api(`<?= BASE_URL ?>/api/observaciones/thread?obs_id=${obsId}`);
        const replies = data.replies || [];
        if (!replies.length) {
            cont.innerHTML = '<p style="font-size:.7rem;color:var(--txt-muted);text-align:center;padding:8px 0;margin:0"><i class="bi bi-chat-dots me-1"></i>Sin respuestas aún</p>';
            return;
        }
        const rolColors = { maestro:'linear-gradient(135deg,#f59e0b,#d97706)', alumno:'linear-gradient(135deg,#10b981,#059669)', admin:'linear-gradient(135deg,#667eea,#764ba2)' };
        cont.innerHTML = '<div style="border-top:1px solid var(--bd-color);padding-top:8px;margin-top:4px">'
            + replies.map(r => {
                const isMine = String(r.autor_id) === String(MAESTRO_ID);
                const bg = rolColors[r.rol_autor] || 'linear-gradient(135deg,#6b7280,#4b5563)';
                return `<div style="display:flex;align-items:flex-start;gap:7px;margin-bottom:7px;${isMine?'flex-direction:row-reverse':''}">
                    <div style="width:24px;height:24px;border-radius:50%;background:${bg};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.65rem;color:#fff;flex-shrink:0">
                        ${esc((r.autor_nombre||'?')[0].toUpperCase())}
                    </div>
                    <div style="background:${isMine?'rgba(102,126,234,.12)':'rgba(16,185,129,.08)'};border:1px solid var(--bd-color);border-radius:${isMine?'10px 0 10px 10px':'0 10px 10px 10px'};padding:6px 9px;max-width:85%">
                        <div style="font-size:.62rem;color:var(--txt-muted);margin-bottom:2px">${esc(r.autor_nombre||r.autor_username)}${isMine?' (tú)':''} · ${new Date(r.fecha_creacion).toLocaleString('es-MX')}</div>
                        <div style="font-size:.78rem;color:var(--txt-main);white-space:pre-wrap;line-height:1.4">${esc(r.texto)}</div>
                    </div>
                </div>`;
            }).join('')
            + '</div>';
    } catch(e) {
        cont.innerHTML = `<p style="font-size:.7rem;color:#ef4444;margin:0">${esc(e.message)}</p>`;
    }
}

async function enviarReplyMaestro(obsId) {
    const inp = document.getElementById('replyM_' + obsId);
    const txt = inp ? inp.value.trim() : '';
    if (!txt) { toast('Escribe tu respuesta primero','info'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/observaciones/reply', { obs_id: obsId, texto: txt });
        if (!r?.success) throw new Error(r?.error || 'Error al enviar');
        if (inp) inp.value = '';
        toast('Respuesta enviada al alumno','ok');
        window._hiloVisibleM[obsId] = true;
        await cargarHiloMaestro(obsId);
    } catch(e) { toast(e.message,'err'); }
}

// ── Fin sistema replies V46 ──────────────────────────────────────────────────

// ── V46: Sistema de invitación interna por búsqueda de usuario ───────────────

window._inviteSelected = {}; // pid → {id, nombre}

async function buscarParaInvitar(q, pid, resultsId, rolId) {
    const cont = document.getElementById(resultsId);
    if (!cont) return;
    if (!q || q.length < 2) { cont.innerHTML = ''; cont.style.display = 'none'; return; }
    cont.innerHTML = '<div style="padding:10px;font-size:.78rem;color:var(--txt-muted);text-align:center"><div class="spinner-border spinner-border-sm"></div></div>';
    cont.style.display = 'block';
    try {
        const data = await api(`<?= BASE_URL ?>/api/proyectos/buscar-usuarios?q=${encodeURIComponent(q)}&proyecto_id=${pid}`);
        const users = data.usuarios || [];
        if (!users.length) {
            cont.innerHTML = '<div style="padding:10px 12px;font-size:.78rem;color:var(--txt-muted)">No se encontraron usuarios</div>';
            return;
        }
        cont.innerHTML = users.map(u => `
            <div class="invite-result-item" onclick="seleccionarInvitado(${pid},'${resultsId}',${u.id},'${esc(u.nombre_completo||u.username)}','${esc(u.username)}')">
                <div class="invite-result-avatar">${(u.nombre_completo||u.username||'?')[0].toUpperCase()}</div>
                <div>
                    <div class="invite-result-name">${esc(u.nombre_completo||u.username)}</div>
                    <div class="invite-result-sub">@${esc(u.username)} · ${esc(u.email||u.rol)}</div>
                </div>
            </div>`).join('');
    } catch(e) {
        cont.innerHTML = `<div style="padding:10px;font-size:.75rem;color:#ef4444">${esc(e.message)}</div>`;
    }
}

function seleccionarInvitado(pid, resultsId, uid, nombre, username) {
    window._inviteSelected[pid] = { id: uid, nombre, username };
    const cont = document.getElementById(resultsId);
    if (cont) cont.style.display = 'none';
    // Actualizar el input con el nombre seleccionado
    const wrap = cont?.closest('.invite-search-wrap');
    if (wrap) {
        const inp = wrap.querySelector('input');
        if (inp) inp.value = `${nombre} (@${username})`;
    }
    toast(`Seleccionado: ${nombre}`, 'info');
}

async function invitarSeleccionadoM(pid) {
    const sel = window._inviteSelected[pid];
    if (!sel) { toast('Primero busca y selecciona un usuario', 'info'); return; }
    const rolEl = document.getElementById(`mRolInv_${pid}`);
    const rol = rolEl ? rolEl.value : 'editor';
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos/invitar', {
            proyecto_id: pid,
            usuario_id: sel.id,
            rol
        });
        if (!r.success) throw new Error(r.error || 'Error al invitar');
        toast(`${sel.nombre} agregado como ${rol}`, 'ok');
        window._inviteSelected[pid] = null;
        // Recargar detalle del proyecto
        abrirDetalleProyectoM(pid);
    } catch(e) { toast(e.message, 'err'); }
}

function copiarCodigoProyecto(codigo) {
    navigator.clipboard?.writeText(codigo).then(() => toast('Código copiado: ' + codigo, 'ok'));
}

function copiarInvitacionM(nombre, codigo) {
    const texto = `¡Te invito al proyecto "${nombre}" en DiagramasMVC!\n\nCódigo de acceso: ${codigo}\n\nPasos:\n1. Entra al sistema\n2. Ve a Proyectos → Unirme con código\n3. Ingresa: ${codigo}`;
    navigator.clipboard?.writeText(texto).then(() => toast('Invitación copiada al portapapeles', 'ok'));
}

// ── Navegación directa a una observación específica (desde respuestas recientes) ─
async function navegarAObservacionM(proyectoId, diagramaId, obsId) {
    if (!proyectoId) return;
    showSection('observaciones');
    setTimeout(async () => {
        const sel = document.getElementById('selectProyObs');
        if (sel) {
            sel.value = proyectoId;
            await cargarDiagramasParaObs(proyectoId);
            // Seleccionar el diagrama específico si existe
            if (diagramaId) {
                setTimeout(() => {
                    const selDiag = document.getElementById('selectDiagObs');
                    if (selDiag) {
                        selDiag.value = diagramaId;
                        renderObsDiagrama(proyectoId, diagramaId);
                    }
                    // Resaltar y expandir el hilo de la observación
                    if (obsId) {
                        setTimeout(async () => {
                            const hiloEl = document.getElementById('hiloM_' + obsId);
                            if (hiloEl) {
                                hiloEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                // Cargar el hilo automáticamente
                                window._hiloVisibleM = window._hiloVisibleM || {};
                                window._hiloVisibleM[obsId] = true;
                                await cargarHiloMaestro(obsId);
                                // Resaltar visualmente
                                const card = hiloEl.closest('[style*="border-radius:12px"]');
                                if (card) {
                                    card.style.outline = '2px solid var(--primary)';
                                    card.style.outlineOffset = '2px';
                                    setTimeout(() => { card.style.outline = ''; card.style.outlineOffset = ''; }, 3000);
                                }
                            }
                        }, 600);
                    }
                }, 500);
            }
        }
    }, 350);
}

// ── Fin sistema invitación V46 ────────────────────────────────────────────────

// ── Sistema de Notificaciones (Maestro) ─────────────────────────────────────

const _notifIconosM  = { observacion:'bi-chat-left-text', reporte_error:'bi-flag-fill', proyecto:'bi-folder2-open', tarea:'bi-clipboard-check', info:'bi-info-circle' };
const _notifColoresM = { observacion:'#667eea', reporte_error:'#ef4444', proyecto:'#10b981', tarea:'#f59e0b', info:'#6b7280' };

async function cargarNotificacionesM() {
    try {
        const data = await api('<?= BASE_URL ?>/api/notificaciones');
        const notifs = data.notificaciones || [];
        const noLeidas = parseInt(data.no_leidas || 0);
        const badge = document.getElementById('notifBadgeM');
        if (badge) {
            if (noLeidas > 0) {
                badge.textContent = noLeidas > 9 ? '9+' : noLeidas;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
        if (document.getElementById('notifPanelM')?.style.display !== 'none') {
            renderNotifListaM(notifs);
        }
    } catch(e) {}
}

function renderNotifListaM(notifs) {
    const cont = document.getElementById('notifListaM');
    if (!cont) return;
    if (!notifs.length) {
        cont.innerHTML = `<div style="text-align:center;padding:28px;color:var(--txt-muted);font-size:.82rem">
            <i class="bi bi-bell-slash" style="font-size:1.8rem;display:block;margin-bottom:8px;opacity:.3"></i>Sin notificaciones</div>`;
        return;
    }
    cont.innerHTML = notifs.map(n => {
        const icon  = _notifIconosM[n.tipo]  || _notifIconosM.info;
        const color = _notifColoresM[n.tipo] || _notifColoresM.info;
        const fecha = new Date(n.fecha).toLocaleString('es-MX',{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
        return `<div style="display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--bd-color);${n.leida=='1'?'opacity:.6':''}cursor:pointer;transition:background .15s"
                     onmouseover="this.style.background='rgba(102,126,234,.12)'" onmouseout="this.style.background=''"
                     onclick="leerNotifM(${n.id},'${esc(n.url||'')}','${n.tipo}')">
            <div style="width:32px;height:32px;background:${color}20;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi ${icon}" style="color:${color};font-size:.85rem"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:.8rem;color:var(--txt-main)">${esc(n.titulo)}</div>
                ${n.mensaje?`<div style="font-size:.72rem;color:var(--txt-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(n.mensaje)}</div>`:''}
                <div style="font-size:.65rem;color:var(--txt-muted);margin-top:2px">${fecha}</div>
            </div>
            ${n.leida=='0'?`<div style="width:8px;height:8px;background:var(--primary);border-radius:50%;flex-shrink:0;margin-top:4px"></div>`:''}
        </div>`;
    }).join('');
}

async function toggleNotifPanelM() {
    const panel = document.getElementById('notifPanelM');
    if (!panel) return;
    const visible = panel.style.display !== 'none';
    panel.style.display = visible ? 'none' : 'block';
    if (!visible) {
        const data = await api('<?= BASE_URL ?>/api/notificaciones').catch(()=>({notificaciones:[]}));
        renderNotifListaM(data.notificaciones || []);
    }
}

async function leerNotifM(id, url, tipo) {
    await api('<?= BASE_URL ?>/api/notificaciones/leer', { id: id }).catch(()=>{});
    await cargarNotificacionesM();
    const np = document.getElementById('notifPanelM');
    if (np) np.style.display = 'none';

    // Si la URL tiene parámetros directos de observación, navegar directamente
    if (url && url !== 'null' && url !== '' && url.includes('open_proyecto=')) {
        try {
            const urlObj = new URL(url, window.location.origin);
            const openProyecto = urlObj.searchParams.get('open_proyecto');
            const openObs      = urlObj.searchParams.get('open_obs');
            if (openProyecto) {
                // Navegar a la sección de observaciones del proyecto correcto
                showSection('observaciones');
                setTimeout(async () => {
                    const sel = document.getElementById('selectProyObs');
                    if (sel) {
                        sel.value = openProyecto;
                        await cargarDiagramasParaObs(openProyecto);
                        // Resaltar la observación específica si existe
                        if (openObs) {
                            setTimeout(() => {
                                const hiloEl = document.getElementById('hiloM_' + openObs);
                                if (hiloEl) {
                                    hiloEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    hiloEl.closest('div[style*="border"]')?.style && (hiloEl.closest('div[style*="border-radius:12px"]').style.outline = '2px solid var(--primary)');
                                    setTimeout(() => {
                                        if (hiloEl.closest('div[style*="border-radius:12px"]')) {
                                            hiloEl.closest('div[style*="border-radius:12px"]').style.outline = '';
                                        }
                                    }, 2500);
                                }
                            }, 800);
                        }
                    }
                }, 400);
                return;
            }
        } catch(e) {}
    }

    if (tipo === 'observacion' || tipo === 'reporte_error') {
        showSection('observaciones');
        return;
    }
    if (tipo === 'proyecto') {
        showSection('proyectos');
        return;
    }
    if (url && url !== 'null' && url !== '') window.location.href = url;
}

async function marcarTodasLeidasM() {
    await api('<?= BASE_URL ?>/api/notificaciones/leer-todas', {}).catch(()=>{});
    await cargarNotificacionesM();
    const panel = document.getElementById('notifPanelM');
    if (panel && panel.style.display !== 'none') {
        const data = await api('<?= BASE_URL ?>/api/notificaciones').catch(()=>({notificaciones:[]}));
        renderNotifListaM(data.notificaciones || []);
    }
}

// Cerrar panel al hacer clic fuera
document.addEventListener('click', e => {
    if (!e.target.closest('#btnNotifM') && !e.target.closest('#notifPanelM')) {
        const np = document.getElementById('notifPanelM');
        if (np) np.style.display = 'none';
    }
});

// ── Fin Sistema de Notificaciones (Maestro) ──────────────────────────────────

document.addEventListener('DOMContentLoaded', async () => {
    // Iniciar polling de notificaciones
    cargarNotificacionesM();
    setInterval(cargarNotificacionesM, 60000);

    // Manejar parámetros de URL para navegación directa desde notificaciones
    const urlParams = new URLSearchParams(window.location.search);
    const openProyecto = urlParams.get('open_proyecto');
    const openDiagrama = urlParams.get('open_diagrama');
    const openObs      = urlParams.get('open_obs');
    const hashSection  = window.location.hash.replace('#','');

    if (openProyecto && (hashSection === 'observaciones' || !hashSection)) {
        // Limpiar la URL sin recargar
        history.replaceState({}, '', window.location.pathname);
        await showSection('observaciones');
        setTimeout(async () => {
            const sel = document.getElementById('selectProyObs');
            if (sel) {
                sel.value = openProyecto;
                await cargarDiagramasParaObs(openProyecto);
                if (openObs) {
                    setTimeout(async () => {
                        const hiloEl = document.getElementById('hiloM_' + openObs);
                        if (hiloEl) {
                            hiloEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            window._hiloVisibleM = window._hiloVisibleM || {};
                            window._hiloVisibleM[openObs] = true;
                            await cargarHiloMaestro(openObs);
                        }
                    }, 700);
                }
            }
        }, 450);
        return;
    }

    const raw = sessionStorage.getItem(M_NAV_KEY);
    if (raw) {
        try {
            const state = JSON.parse(raw);
            sessionStorage.removeItem(M_NAV_KEY);
            if (state.fromEditor && state.section === 'proyectos' && state.proyectoId) {
                document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
                document.getElementById('nav-proyectos')?.classList.add('active');
                document.getElementById('pageTitle').textContent = 'Proyectos';
                await abrirProyectoM(state.proyectoId);
                return;
            }
            if (state.fromEditor && state.section === 'observaciones') {
                showSection('observaciones');
                return;
            }
            if (state.fromEditor && state.section === 'diagramas') {
                showSection('diagramas');
                return;
            }
        } catch(_) {}
    }
    showSection('inicio');
});


function toggleThemeDrawer() {
    const drawer  = document.getElementById('themeDrawer');
    const overlay = document.getElementById('themeOverlay');
    const isOpen  = drawer.style.right === '0px';
    drawer.style.right   = isOpen ? '-340px' : '0px';
    overlay.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) renderThemePanel('maestroThemeContainer', 'dark');
}
</script>
<script>window._DIAG_BASE_URL = '<?= BASE_URL ?>';</script>
<!-- V45: componentes reutilizables compartidos -->
<script src="<?= BASE_URL ?>/public/js/diagram-components.js"></script>
<script src="<?= BASE_URL ?>/public/js/diagram-mini-preview.js"></script>
</body>
</html>