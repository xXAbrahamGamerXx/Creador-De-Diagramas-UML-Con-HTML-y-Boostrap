<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de Administración — DiagramasUML</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/diagram-ui.css">
<link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
<style>
:root {
    --primary:     #667eea;
    --primary2:    #764ba2;
    --primary-rgb: 102,126,234;
    --sidebar:     240px;
    /* Panel tints — derived from primary. Updated by user-theme.js */
    --bg-deep:   #0a0a14;
    --bg-panel:  #13132a;
    --bg-card:   #1a1a2e;
    --bg-hover:  #16213e;
    --bd-color:  #2a2a4a;
    /* Colores semánticos de acción (UX) */
    --c-success: #10b981;  /* Verde  — guardar, confirmar, activar */
    --c-danger:  #ef4444;  /* Rojo   — eliminar, desactivar, error */
    --c-warning: #f59e0b;  /* Amarillo — editar, advertencia, pendiente */
    --c-info:    #60a5fa;  /* Azul   — ver, info, detalle */
    --c-neutral: #6b7280;  /* Gris   — cancelar, neutro */
}
body { background:var(--bg-deep); color:#e0e0e0; font-family:'Segoe UI',sans-serif; margin:0; }
body.light-theme { --bg-deep:#f4f6fb; --bg-panel:#fff; --bg-card:#f8f9ff; --bg-hover:#eef0ff; --bd-color:#dde0f0; color:#1e1e2e; }
body.light-theme .section-card { background:#fff; border-color:#dde0f0; }
body.light-theme .section-card .card-header { background:#f0f2ff; border-color:#dde0f0; }
body.light-theme .admin-table { background:#fff; }
body.light-theme .admin-table thead tr { background:#eef0ff !important; }
body.light-theme .admin-table thead th { color:var(--primary); border-color:#dde0f0 !important; background:#eef0ff !important; }
body.light-theme .admin-table tbody td { color:#1e1e2e; border-color:#eef0ff; }
body.light-theme .nav-btn { color:rgba(30,30,46,.75); }
body.light-theme .nav-btn:hover { background:rgba(var(--primary-rgb),.1); color:var(--primary); }
body.light-theme .nav-btn.active { color:var(--primary); }
body.light-theme .page-header h2 { color:#1e1e2e; }
body.light-theme .stat-num { color:#1e1e2e; }
body.light-theme .stat-card { background:#fff !important; }
body.light-theme .form-control-dark { background:#fff !important; border-color:#dde0f0 !important; color:#1e1e2e !important; }
body.light-theme .form-control-dark:focus { border-color:var(--primary) !important; background:#fff !important; color:#1e1e2e !important; box-shadow:0 0 0 3px rgba(var(--primary-rgb),.12) !important; }
body.light-theme .log-output { background:#f8f9ff !important; border-color:#dde0f0 !important; color:#333; }
body.light-theme .doc-section code { background:#f0f2ff !important; color:var(--primary) !important; }
body.light-theme .doc-section .file-path { background:#f8f9ff !important; border-color:#dde0f0 !important; color:var(--primary) !important; }
/* Sidebar brand & user en light */
body.light-theme .sidebar { background:linear-gradient(160deg,#f8f9ff,#eef0ff) !important; border-color:#dde0f0 !important; }
body.light-theme .sidebar-brand h4 { color:var(--primary) !important; }
body.light-theme .sidebar-brand small { color:#666 !important; }
body.light-theme .sidebar-brand { border-color:#dde0f0 !important; }
/* Elementos inline hardcodeados */
body.light-theme div[style*="background:#1a1a2e"] { background:#fff !important; border-color:#dde0f0 !important; }
body.light-theme div[style*="background:#0d0d1a"] { background:#f8f9ff !important; border-color:#dde0f0 !important; }
body.light-theme div[style*="border:1px solid #2a2a4a"] { border-color:#dde0f0 !important; }
body.light-theme code[style*="background:#0d0d1a"] { background:#f0f2ff !important; color:var(--primary) !important; }
body.light-theme div[style*="color:#fff;font-weight"] { color:#1e1e2e !important; }
body.light-theme div[style*="font-weight:600;color:#fff"] { color:#1e1e2e !important; }
body.light-theme div[style*="font-weight:700;color:#fff"] { color:#1e1e2e !important; }
body.light-theme div[style*="background:#13132a"] { background:#f0f2ff !important; }
body.light-theme div[style*="background:#080812"] { background:#f8f9ff !important; border-color:#dde0f0 !important; color:#1e1e2e !important; }
body.light-theme div[style*="background:#0a0a12"] { background:#f8f9ff !important; }
body.light-theme div[style*="background:#080810"] { background:#f8f9ff !important; }
body.light-theme div[style*="background:#0a0a10"] { background:#f8f9ff !important; }
body.light-theme span[style*="color:#888"] { color:#666 !important; }
body.light-theme div[style*="color:#888"] { color:#666 !important; }
body.light-theme pre[style*="background:#080812"] { background:#f8f9ff !important; color:#333 !important; border-color:#dde0f0 !important; }
/* Botones y headers con gradiente siempre mantienen texto blanco */
body.light-theme [style*="background:linear-gradient"] { color:#fff !important; }
body.light-theme [style*="background:linear-gradient"] * { color:#fff !important; }
/* excepciones dentro de gradientes que usan otros colores */
body.light-theme [style*="background:linear-gradient"] .bi { color:#fff !important; }
/* ── Light-theme: admin user cards text colors ──────────────── */
body.light-theme div[style*="background:#0d0d1a"] { background:var(--bg-card,#f8f9ff) !important; border-color:var(--bd-color,#dde0f0) !important; }
body.light-theme div[style*="background:#0d0d1a"] * { color:var(--txt-main,#1e1e2e) !important; }
body.light-theme div[style*="background:#0d0d1a"] button { color:inherit !important; }
/* Specific white text inside cards */
body.light-theme div[style*="color:#fff;font-size:.85rem"] { color:#1e1e2e !important; }
body.light-theme div[style*="color:#888;white-space"] { color:#666 !important; }
body.light-theme div[style*="color:var(--primary,#aab8ff)"] { color:var(--primary,#4c5ddb) !important; }
body.light-theme td[style*="color:var(--primary,#aab8ff)"] { color:var(--primary,#4c5ddb) !important; }
/* Table row white texts */
body.light-theme .admin-table td { color:#1e1e2e !important; }
body.light-theme .admin-table td span[style*="color:#fff"] { color:#1e1e2e !important; }
body.light-theme .admin-table td div[style*="color:#fff"] { color:#1e1e2e !important; }
/* Notice/banner in light mode */
body.light-theme #_juniorNoticeBanner * { color:var(--txt-main,#1e1e2e) !important; }
body.light-theme #_juniorNoticeBanner strong[style*='color:#f59e0b'] { color:#b45309 !important; }
body.light-theme #adminPermissionNotice div[style*='color:var(--txt-main,#d1d5db)'] { color:#374151 !important; }
body.light-theme #adminPermissionNotice div[style*='color:var(--txt-main,#e2e8f0)'] { color:#374151 !important; }
body.light-theme .doc-section h4 { border-color:#dde0f0 !important; }
body.light-theme .doc-section p,
body.light-theme .doc-section li { color:#333 !important; }
body.light-theme #themeDrawer { background:#f8f9ff !important; border-color:#dde0f0 !important; }

/* Sidebar */
.sidebar { position:fixed; top:0; left:0; width:var(--sidebar); height:100vh; background:linear-gradient(160deg,var(--bg-card),var(--bg-hover)); border-right:1px solid #2a2a4a; display:flex; flex-direction:column; z-index:100; overflow-y:auto; }
.sidebar-brand { padding:20px 18px 16px; border-bottom:1px solid var(--bd-color); }
.sidebar-brand h4 { margin:0; font-size:.95rem; font-weight:700; color:#fff; }
.sidebar-brand small { color:#667eea; font-size:.75rem; }
.nav-btn { display:flex; align-items:center; gap:10px; width:100%; padding:10px 18px; background:none; border:none; color:rgba(255,255,255,.65); font-size:.85rem; cursor:pointer; transition:all .2s; text-align:left; border-left:3px solid transparent; }
.nav-btn:hover { background:rgba(var(--primary-rgb),.12); color:#fff; }
.nav-btn.active { background:rgba(var(--primary-rgb),.2); color:#fff; border-left-color:var(--primary); }
.nav-btn i { width:18px; text-align:center; font-size:1rem; }
.nav-btn.disabled { opacity:.45; cursor:not-allowed; color:rgba(255,255,255,.45); }
.nav-btn.disabled:hover { background:none; }
.nav-btn.disabled i { color:rgba(255,255,255,.45); }
.nav-section { padding:14px 18px 6px; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(var(--primary-rgb),.4); }
.sidebar-footer { margin-top:auto; padding:14px 18px; border-top:1px solid var(--bd-color); }

/* Main */
.main { margin-left:var(--sidebar); min-height:100vh; padding:28px 32px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.page-header h2 { margin:0; font-size:1.3rem; font-weight:700; color:#fff; }
.badge-admin { background:linear-gradient(135deg,var(--primary),var(--primary2)); color:#fff; font-size:.7rem; padding:3px 10px; border-radius:20px; font-weight:500; }

/* Cards */
.stat-card { background:var(--bg-card); border:1px solid var(--bd-color); border-radius:12px; padding:20px; transition:transform .2s,box-shadow .2s,border-color .2s; }
.stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(102,126,234,.25); border-color:rgba(102,126,234,.5); }
/* Filas de carpetas / listas con hover visible */
.folder-row { transition:background .18s,border-color .2s !important; cursor:pointer; }
.folder-row:hover { background:rgba(102,126,234,.14) !important; border-color:rgba(102,126,234,.5) !important; }
.folder-row:hover .fr-title { color:#c7d2fe !important; }
.stat-icon { font-size:1.8rem; margin-bottom:8px; }
.stat-num { font-size:1.8rem; font-weight:700; color:#fff; line-height:1; }
.stat-label { color:#888; font-size:.78rem; margin-top:3px; }

/* Section content */
.section-card { background:var(--bg-card); border:1px solid var(--bd-color); border-radius:12px; overflow:hidden; margin-bottom:20px; }
.section-card .card-header { background:var(--bg-hover); border-bottom:1px solid var(--bd-color); padding:14px 20px; display:flex; align-items:center; gap:10px; }
.section-card .card-header h5 { margin:0; font-size:.9rem; font-weight:600; }
.section-card .card-body { padding:20px; }

/* Table */
.admin-table { width:100%; border-collapse:collapse; font-size:.83rem; }
.admin-table th { background:#0d0d1a; color:#888; font-weight:500; padding:10px 14px; text-align:left; border-bottom:1px solid #2a2a4a; }
.admin-table td { padding:10px 14px; border-bottom:1px solid #1e1e3a; color:#ccc; vertical-align:middle; transition:background .15s,color .15s; }
.admin-table tr:hover td { background:rgba(102,126,234,.1); color:#fff; }
.admin-table tr:last-child td { border-bottom:none; }

/* Badges */
.rol-admin { background:rgba(220,53,69,.2); color:#ff6b6b; border:1px solid rgba(220,53,69,.3); }
.rol-user  { background:rgba(13,110,253,.2); color:#7bb3ff; border:1px solid rgba(13,110,253,.3); }
.status-ok  { color:var(--c-success); }
.status-err { color:var(--c-danger); }
.status-warn{ color:var(--c-warning); }
.badge-tipo { background:rgba(102,126,234,.2); color:var(--primary,#aab8ff); border:1px solid rgba(102,126,234,.3); font-size:.7rem; padding:2px 8px; border-radius:10px; }

/* DB status */
.db-status { padding:16px 20px; border-radius:10px; display:flex; align-items:center; gap:14px; }
.db-status.ok  { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); }
.db-status.err { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.3);  }
.db-status i { font-size:1.8rem; }

/* Log output */
.log-output { background:#0a0a12; border:1px solid #2a2a4a; border-radius:8px; padding:14px 16px; font-family:'Courier New',monospace; font-size:.8rem; max-height:320px; overflow-y:auto; }
.log-ok   { color:#10b981; }
.log-warn { color:#f59e0b; }
.log-err  { color:#ef4444; }
.log-info { color:#60a5fa; }

/* Form */
.form-control-dark { background:#0d0d1a; border:1px solid #2a2a4a; color:#e0e0e0; border-radius:8px; padding:8px 12px; font-size:.85rem; }
.form-control-dark:focus { border-color:#667eea; outline:none; box-shadow:0 0 0 3px rgba(102,126,234,.15); background:#0d0d1a; color:#e0e0e0; }
.btn-admin { background:linear-gradient(135deg,var(--primary),var(--primary2)); border:none; color:#fff; padding:9px 20px; border-radius:8px; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .2s; }
.btn-admin:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(102,126,234,.4); }
.btn-admin-outline { background:none; border:1px solid var(--primary); color:var(--primary); padding:7px 16px; border-radius:8px; font-size:.82rem; cursor:pointer; transition:all .2s; }
.btn-admin-outline:hover { background:rgba(102,126,234,.1); }
.btn-danger-sm { background:none; border:1px solid #dc3545; color:#dc3545; padding:4px 10px; border-radius:6px; font-size:.75rem; cursor:pointer; }
.btn-danger-sm:hover { background:rgba(220,53,69,.15); }

/* Toast */
.lock-icon { float:right;margin-left:auto;flex-shrink:0; }
#adminToast { position:fixed; bottom:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
.t-msg { padding:10px 18px; border-radius:8px; font-size:.83rem; font-weight:500; box-shadow:0 4px 16px rgba(0,0,0,.4); animation:tIn .3s ease; }
.t-ok   { background:var(--bg-panel,#fff); border-left:4px solid #10b981; color:#10b981; box-shadow:0 2px 12px rgba(0,0,0,.15); }
.t-err  { background:var(--bg-panel,#fff); border-left:4px solid #ef4444; color:#ef4444; box-shadow:0 2px 12px rgba(0,0,0,.15); }
.t-warn { background:var(--bg-panel,#fff); border-left:4px solid #f59e0b; color:#b45309; box-shadow:0 2px 12px rgba(0,0,0,.15); }
.t-info { background:#0d1b2b; border-left:4px solid #60a5fa; color:#60a5fa; }
@keyframes tIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }

/* Doc */
.doc-section { margin-bottom:28px; }
.doc-section h4 { color:#667eea; font-size:1rem; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #2a2a4a; }
.doc-section h5 { color:var(--primary,#aab8ff); font-size:.88rem; margin:14px 0 6px; }
.doc-section p, .doc-section li { color:#aaa; font-size:.85rem; line-height:1.7; }
.doc-section code { background:#0d0d1a; color:#f59e0b; padding:2px 6px; border-radius:4px; font-size:.8rem; }
.doc-section .file-path { background:#0d0d1a; border:1px solid #2a2a4a; border-radius:6px; padding:10px 14px; font-family:'Courier New',monospace; font-size:.8rem; color:#60a5fa; margin-bottom:8px; }

/* ── Filtros del panel admin ────────────────────────────── */
.btn-cancel { background:none; border:1px solid #3a3a5a; color:#888; padding:7px 16px; border-radius:8px; font-size:.82rem; cursor:pointer; }
.btn-cancel:hover { background:rgba(255,255,255,.06); color:#ccc; }

/* ── Toggle deslizante para ocultar/mostrar secciones ──────────── */
.toggle-switch {
    position: relative;
    width: 42px;
    height: 22px;
    background: #2a2a4a;
    border-radius: 11px;
    cursor: pointer;
    transition: background .25s;
    border: 1px solid #3a3a6a;
    flex-shrink: 0;
}
.toggle-switch.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
}
.toggle-knob {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    background: #888;
    border-radius: 50%;
    transition: transform .25s, background .25s;
}
.toggle-switch.active .toggle-knob {
    transform: translateX(20px);
    background: #fff;
}
        /* ── V46: Cards Lucidchart compartidas con maestro/alumno ─── */
        .lc-card-admin { background:var(--bg-card); border-radius:12px; border:1.5px solid var(--bd-color);
            overflow:hidden; transition:all .22s; box-shadow:0 2px 8px rgba(0,0,0,.06); }
        .lc-card-admin:hover { border-color:var(--primary); box-shadow:0 6px 24px rgba(102,126,234,.18); transform:translateY(-2px); }
        .lc-preview-admin { height:130px; border-radius:10px 10px 0 0; background:var(--bg-deep);
            border-bottom:1px solid var(--bd-color); display:flex; align-items:center;
            justify-content:center; overflow:hidden; cursor:pointer; position:relative; }
        .lc-preview-admin:hover::after { content:'Abrir'; position:absolute; inset:0;
            background:rgba(102,126,234,.13); display:flex; align-items:center;
            justify-content:center; font-size:.8rem; font-weight:700; color:var(--primary);
            border-radius:10px 10px 0 0; pointer-events:none; }
        .lc-body-admin { padding:9px 12px 7px; }
        .lc-title-admin { font-weight:700; font-size:.86rem; color:var(--txt-main);
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:2px; }
        .lc-meta-admin { font-size:.68rem; color:var(--txt-muted); white-space:nowrap;
            overflow:hidden; text-overflow:ellipsis; }
        .lc-footer-admin { display:flex; align-items:center; gap:6px; padding:7px 12px;
            border-top:1px solid var(--bd-color); }
        .lc-btn-open-admin { background:var(--primary); color:#fff; border:none;
            border-radius:7px; padding:4px 13px; font-size:.74rem; font-weight:600;
            cursor:pointer; transition:opacity .15s; }
        .lc-btn-open-admin:hover { opacity:.88; }
        .lc-btn-danger-admin { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3);
            color:#ef4444; border-radius:7px; width:28px; height:28px; display:flex;
            align-items:center; justify-content:center; cursor:pointer; font-size:.8rem;
            margin-left:auto; transition:all .15s; }
        /* Explorador de carpetas en proyectos */
        .folder-section { margin-bottom:18px; }
        .folder-header { display:flex; align-items:center; gap:10px; padding:10px 14px;
            background:rgba(102,126,234,.08); border-radius:10px 10px 0 0;
            border:1.5px solid var(--bd-color); border-bottom:none;
            font-weight:700; font-size:.84rem; color:var(--txt-main); }
        .folder-body { border:1.5px solid var(--bd-color); border-radius:0 0 10px 10px;
            padding:14px; background:var(--bg-card); }
        /* Chat de observaciones */
        .obs-bubble { display:flex; gap:10px; margin-bottom:12px; }
        .obs-avatar { width:32px; height:32px; border-radius:50%; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.78rem; color:#fff; }
        .obs-body { flex:1; background:var(--bg-deep); border-radius:0 10px 10px 10px;
            padding:10px 12px; border:1px solid var(--bd-color); }
        .obs-bubble.reply .obs-body { background:rgba(102,126,234,.08);
            border-radius:10px 0 10px 10px; }
        .obs-bubble.reply { flex-direction:row-reverse; }
        .obs-bubble.reply .obs-body { text-align:right; }
        .obs-meta { font-size:.68rem; color:var(--txt-muted); margin-bottom:4px; }
        .obs-text { font-size:.8rem; color:var(--txt-main); white-space:pre-wrap; line-height:1.45; }
        .obs-reply-tag { font-size:.65rem; font-style:italic; color:var(--primary); margin-bottom:3px; }
</style>
</head>
<body>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-shield-fill-check me-2" style="color:#667eea"></i>Panel Admin</h4>
        <small>DiagramasUML v4.0</small>
    </div>

    <?php $esEmerg = SessionManager::esEmergencia(); ?>
    <div class="nav-section">Proyectos & Contenido</div>
    <button class="nav-btn active" id="nav-resumen"          onclick="showSection('resumen')"><i class="bi bi-speedometer2"></i> Resumen</button>
    <button class="nav-btn"        id="nav-proyectos-admin"  onclick="showSection('proyectos-admin')"><i class="bi bi-folder2-open"></i> Proyectos</button>
    <button class="nav-btn"        id="nav-diagramas"        onclick="showSection('diagramas')"><i class="bi bi-diagram-3"></i> Diagramas</button>
    <button class="nav-btn"        id="nav-usuarios"         onclick="showSection('usuarios')"><i class="bi bi-people"></i> Usuarios</button>

    <div class="nav-section">Sistema</div>
    <button class="nav-btn" id="nav-config"        onclick="showSection('config')"><i class="bi bi-sliders"></i> Configuración Global</button>
    <button class="nav-btn" id="nav-mantenimiento" onclick="showSection('mantenimiento')"><i class="bi bi-folder-symlink"></i> Archivos de Usuarios</button>
    <button class="nav-btn" id="nav-svg"           onclick="showSection('svg')"><i class="bi bi-folder-check"></i> Archivos del Sistema</button>
    <button class="nav-btn" id="nav-docs"          onclick="showSection('docs')"><i class="bi bi-book"></i> Documentación</button>

    <div class="sidebar-footer">
        <button class="nav-btn" onclick="toggleThemeDrawer()"><i class="bi bi-palette"></i> Colores &amp; Tema</button>
        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>/logout'"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</button>
    </div>
</aside>

<!-- ══ THEME DRAWER ═══════════════════════════════════════════ -->
<div id="themeDrawer" style="position:fixed;top:0;right:-340px;width:320px;height:100vh;background:var(--bg-card);border-left:1px solid var(--bd-color);z-index:9000;overflow-y:auto;transition:right .3s ease;padding:20px 16px;box-shadow:-6px 0 24px rgba(0,0,0,.5)">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <span style="color:#fff;font-weight:700;font-size:.95rem"><i class="bi bi-palette me-2" style="color:var(--primary)"></i>Apariencia</span>
        <button onclick="toggleThemeDrawer()" style="background:none;border:none;color:#888;font-size:1.2rem;cursor:pointer;padding:4px"><i class="bi bi-x-lg"></i></button>
    </div>
    <div id="adminThemeContainer"></div>
</div>
<div id="themeOverlay" onclick="toggleThemeDrawer()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:8999"></div>

<!-- ══ MAIN ═════════════════════════════════════════════════ -->
<main class="main">

<?php if (SessionManager::esEmergencia()): ?>
<div style="background:linear-gradient(135deg,rgba(220,53,69,.18),rgba(180,30,40,.12));border:1px solid rgba(220,53,69,.4);border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:14px;margin-bottom:20px">
    <i class="bi bi-shield-exclamation" style="font-size:1.6rem;color:#ff6b6b;flex-shrink:0"></i>
    <div style="flex:1">
        <div style="color:#ff8a8a;font-weight:700;font-size:.9rem"><i class="bi bi-exclamation-triangle-fill me-1"></i>MODO DE EMERGENCIA ACTIVO</div>
        <div style="color:rgba(255,180,180,.7);font-size:.78rem;margin-top:2px">
            Sesión sin base de datos &mdash; Solo puedes reparar la configuración de BD.
            Expira en <strong style="color:#fcd34d"><?= SessionManager::minutosEmergenciaRestantes() ?> min</strong>.
        </div>
    </div>
    <a href="<?= BASE_URL ?>/logout" style="background:rgba(220,53,69,.3);border:1px solid rgba(220,53,69,.5);color:#ff8a8a;border-radius:8px;padding:6px 14px;font-size:.8rem;text-decoration:none;white-space:nowrap">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</div>
<?php endif; ?>

    <div class="page-header">
        <h2 id="pageTitle">Resumen del Sistema</h2>
        <div id="adminPermissionNotice" style="margin-top:10px"></div>
        <div class="d-flex align-items-center gap-3">
<?php if (SessionManager::esEmergencia()): ?>
            <span style="background:rgba(220,53,69,.2);color:#ff8a8a;border:1px solid rgba(220,53,69,.4);font-size:.7rem;padding:3px 10px;border-radius:20px;font-weight:600">
                <i class="bi bi-shield-exclamation me-1"></i>EMERGENCIA
            </span>
<?php else: ?>
            <span class="badge-admin"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
<?php endif; ?>
            <small class="text-muted"><?= htmlspecialchars(SessionManager::usuarioNombre()) ?></small>
        </div>
    </div>
    <div id="contentArea"></div>
</main>

<div id="adminToast"></div>

<script src="<?= Assets::bootstrapJs() ?>"></script>
<script>window.BASE_URL = "<?= BASE_URL ?>";</script>
<script src="<?= Assets::url('js/user-theme.js') ?>"></script>
<!-- V46: componentes y renderer compartidos con maestro/alumno -->
<script>window._DIAG_BASE_URL = "<?= BASE_URL ?>";</script>
<script src="<?= BASE_URL ?>/public/js/diagram-components.js"></script>
<script src="<?= BASE_URL ?>/public/js/diagram-mini-preview.js"></script>
<script>
// ── Estado PHP → JS ─────────────────────────────────────────
const DB_OK           = <?= $dbOK ? 'true' : 'false' ?>;
const DB_ERROR        = <?= json_encode($dbError) ?>;
const IS_EMERGENCY    = <?= SessionManager::esEmergencia() ? 'true' : 'false' ?>;
const CURRENT_USER_ID = <?= (int)(SessionManager::usuarioId() ?? 0) ?>;
<?php
// Detectar si el usuario actual es admin junior y quién es el superadmin
$_isJunior = false; $_superAdminId = 0;
if ($dbOK) {
    try {
        $db_tmp = new Database(); $conn_tmp = $db_tmp->getConnection();
        $uid_tmp = (int)SessionManager::usuarioId();
        $row_tmp = $conn_tmp->prepare("SELECT es_admin_junior FROM usuarios WHERE id=:id");
        $row_tmp->execute([':id'=>$uid_tmp]);
        $res_tmp = $row_tmp->fetch(PDO::FETCH_ASSOC);
        $_isJunior = $res_tmp && $res_tmp['es_admin_junior'];
        $super_tmp = $conn_tmp->query("SELECT id FROM usuarios WHERE rol='admin' AND (es_admin_junior=0 OR es_admin_junior IS NULL) ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $_superAdminId = $super_tmp ? (int)$super_tmp['id'] : 0;
        if ($_isJunior) {
            try {
                $permStmt = $conn_tmp->prepare("SELECT permiso FROM admin_permisos WHERE admin_id=:id");
                $permStmt->execute([':id' => $uid_tmp]);
                $_currentAdminPermisos = $permStmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (Exception $e) {
                $_currentAdminPermisos = [];
            }
        }
    } catch(Exception $e) {}
}
?>
const IS_ADMIN_JUNIOR = <?= $_isJunior ? 'true' : 'false' ?>;
const SUPER_ADMIN_ID  = <?= $_superAdminId ?>;
const CURRENT_ADMIN_PERMISOS = <?= json_encode($_currentAdminPermisos ?? []) ?>;

// ── Toast ────────────────────────────────────────────────────
// Muestra notificación flotante en esquina inferior derecha. type: 'ok'|'err'|'info'. Auto-elimina tras 4s.
function toast(msg, type='ok') {
    const el = document.createElement('div');
    el.className = `t-msg t-${type}`;
    const icons = {ok:'bi-check-circle-fill', err:'bi-x-circle-fill', info:'bi-info-circle-fill', warn:'bi-shield-exclamation'};
    el.innerHTML = `<i class="bi ${icons[type]||'bi-info-circle-fill'} me-2"></i>${msg}`;
    document.getElementById('adminToast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// ── Fetch helper ─────────────────────────────────────────────
// Helper fetch: GET si body=null, POST con JSON si hay body. Parsea la respuesta como JSON.
async function api(url, body=null) {
    const opts = body
        ? { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body) }
        : { method:'GET', credentials:'same-origin' };
    const res  = await fetch(url, opts);
    const text = await res.text();
    try { return JSON.parse(text); }
    catch { throw new Error('Respuesta inválida: ' + text.substring(0,100)); }
}

// ── Navegación ───────────────────────────────────────────────
const sections = {
    resumen:          renderResumen,
    'proyectos-admin':renderProyectosAdmin,
    usuarios:         renderUsuarios,
    diagramas:        renderDiagramas,
    config:           renderConfig,
    mantenimiento:    renderMantenimiento,
    svg:              renderSVGs,
    docs:             renderDocs,
    db:               renderDB,
    setup:            renderSetupEmergencia,
};
const titles = {
    resumen:'Resumen del Sistema',
    'proyectos-admin':'Proyectos',
    usuarios:'Gestión de Usuarios',
    diagramas:'Gestión de Diagramas',
    config:'Configuración Global',
    mantenimiento: 'Archivos de Usuarios',
    svg:'Archivos del Sistema',
    docs:'Documentación',
    db:'Base de Datos',
    setup:'Credenciales de Emergencia',
};

const JUNIOR_SECTION_PERMS = {
    usuarios: 'ver_usuarios',
    diagramas: 'ver_diagramas',
    svg: 'ver_svgs',
    db: 'setup_db',
    // docs is not controlled directly by current junior permission set
};
const PERM_TITLES = {
    ver_usuarios: 'Ver lista de usuarios',
    crear_alumnos: 'Crear alumnos',
    crear_maestros: 'Crear maestros',
    editar_usuarios: 'Editar usuarios',
    desactivar_usuarios: 'Activar/desactivar usuarios',
    ver_diagramas: 'Ver diagramas',
    eliminar_diagramas: 'Eliminar diagramas',
    ver_grupos: 'Ver grupos y tareas',
    setup_db: 'Mantenimiento de BD',
    ver_svgs: 'Ver archivos SVG'
};

function hasPermission(key) {
    return !IS_ADMIN_JUNIOR || CURRENT_ADMIN_PERMISOS.includes(key);
}

function sectionPermission(section) {
    return JUNIOR_SECTION_PERMS[section] || null;
}

function hasSectionPermission(section) {
    const permiso = sectionPermission(section);
    return !permiso || hasPermission(permiso);
}

function renderAdminNotice() {
    const notice = document.getElementById('adminPermissionNotice');
    if (!notice) return;
    if (!IS_ADMIN_JUNIOR) { notice.innerHTML = ''; return; }

    // Only show once — track per user in localStorage
    const seenKey = 'junior_notice_seen_' + (CURRENT_USER_ID || 'u');
    if (localStorage.getItem(seenKey) === '1') { notice.innerHTML = ''; return; }

    const activos = CURRENT_ADMIN_PERMISOS.length > 0
        ? CURRENT_ADMIN_PERMISOS.map(k => PERM_TITLES[k] || k).join(', ')
        : 'ninguno';
    const bloqueados = Object.keys(PERM_TITLES)
        .filter(k => !CURRENT_ADMIN_PERMISOS.includes(k))
        .map(k => PERM_TITLES[k]).join(', ') || 'ninguno';

    notice.innerHTML = `
        <div id="_juniorNoticeBanner" style="
            background:linear-gradient(135deg,rgba(245,158,11,.12),rgba(245,158,11,.06));
            border:1.5px solid rgba(245,158,11,.4);border-radius:12px;padding:16px 18px;
            margin-bottom:12px;position:relative">
            <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap">
                <div style="flex:1;min-width:240px">
                    <div style="font-size:.88rem;font-weight:700;color:#f59e0b;margin-bottom:6px">
                        <i class="bi bi-shield-exclamation me-1"></i>Cuenta de administrador con acceso restringido
                    </div>
                    <div style="font-size:.8rem;color:var(--txt-main);line-height:1.6;opacity:.9">
                        Tienes permisos limitados. Las secciones y acciones no disponibles aparecen con el ícono
                        <img src="/public/assets/img/iconos-uml/lock.svg" width="13" height="13" style="vertical-align:middle;margin:0 2px"> bloqueado.
                        Contacta al administrador principal si necesitas más acceso.
                    </div>
                </div>
                <div style="font-size:.78rem;line-height:1.7;min-width:220px;color:var(--txt-main)">
                    <div><strong style="color:#f59e0b">✓ Activos:</strong> ${activos}</div>
                    <div><strong style="color:#888">✗ Bloqueados:</strong> ${bloqueados}</div>
                </div>
            </div>
            <div style="margin-top:14px;text-align:right">
                <button onclick="cerrarAvisoJunior()" style="
                    background:linear-gradient(135deg,#f59e0b,#d97706);border:none;color:#fff;
                    border-radius:8px;padding:7px 20px;font-size:.8rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check-lg me-1"></i>Entendido
                </button>
            </div>
        </div>`;
}

function cerrarAvisoJunior() {
    const seenKey = 'junior_notice_seen_' + (CURRENT_USER_ID || 'u');
    localStorage.setItem(seenKey, '1');
    const banner = document.getElementById('_juniorNoticeBanner');
    if (banner) {
        banner.style.transition = 'opacity .3s, max-height .4s';
        banner.style.opacity = '0';
        banner.style.maxHeight = '0';
        banner.style.overflow = 'hidden';
        setTimeout(() => { banner.parentElement && (banner.parentElement.innerHTML = ''); }, 400);
    }
}

function adjustJuniorNavButtons() {
    if (!IS_ADMIN_JUNIOR) return;
    document.querySelectorAll('.nav-btn').forEach(btn => {
        const id = btn.id?.replace('nav-', '');
        const permiso = sectionPermission(id);
        if (permiso && !hasPermission(permiso)) {
            btn.classList.add('disabled');
            btn.title = 'No tienes permiso para esta sección';
            if (!btn.querySelector('.lock-icon')) {
                btn.innerHTML += '<img src="<?= BASE_URL ?>/public/assets/img/iconos-uml/lock.svg" width="16" height="16" class="lock-icon" style="margin-left:auto;opacity:.8" alt="Bloqueado">';
            }
        }
    });
}

// Navega a una sección del panel (SPA). Actualiza botón activo en sidebar y llama al renderizador.
function showSection(id) {
    // Secciones bloqueadas en modo emergencia
    const bloqueadasEmerg = ['resumen','usuarios','diagramas','grupos','espacio','plantillas','svg','docs'];
    if (IS_EMERGENCY && bloqueadasEmerg.includes(id)) {
        toast('No disponible en modo emergencia. Repara la BD primero.', 'err');
        return;
    }
    if (IS_ADMIN_JUNIOR && !hasSectionPermission(id)) {
        const permiso = sectionPermission(id);
        const label = permiso ? PERM_TITLES[permiso] || 'esta sección' : 'esta sección';
        toast(`No tienes permiso para ver ${label}.`, 'warn');
        return;
    }
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('nav-'+id)?.classList.add('active');
    document.getElementById('pageTitle').textContent = titles[id] || id;
    renderAdminNotice();
    if (sections[id]) sections[id]();
}

// Muestra spinner de carga en #contentArea mientras se espera la respuesta de la API.
function loading() {
    document.getElementById('contentArea').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="text-muted mt-3 small">Cargando...</p>
        </div>`;
}

// ════════════════════════════════════════════════════════════
// RESUMEN
// ════════════════════════════════════════════════════════════
// PROYECTOS ADMIN — ver y gestionar todos los proyectos
// ════════════════════════════════════════════════════════════
async function renderProyectosAdmin() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/admin?action=proyectos');
        const proyectos = data.proyectos || [];
        window._adminProyectos = proyectos;

        document.getElementById('contentArea').innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px">
            <div>
                <h4 style="margin:0;font-size:1rem;font-weight:700;color:var(--txt-main)">
                    <i class="bi bi-folder2-open me-2" style="color:var(--primary)"></i>Proyectos del Sistema
                </h4>
                <p style="margin:3px 0 0;font-size:.74rem;color:var(--txt-muted)">${proyectos.length} proyecto${proyectos.length!=1?'s':''} en total</p>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <div style="position:relative">
                    <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--txt-muted);font-size:.8rem"></i>
                    <input type="text" placeholder="Buscar proyecto..." id="buscarProyAdmin"
                        style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:10px;color:var(--txt-main);padding:7px 14px 7px 30px;font-size:.82rem;width:220px;outline:none"
                        oninput="filtrarProysAdmin(this.value)">
                </div>
            </div>
        </div>
        <div id="proyAdminGrid" class="row g-3">
        ${proyectos.length === 0
            ? '<div class="col-12"><div style="text-align:center;padding:60px;color:var(--txt-muted)"><i class="bi bi-folder2-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px"></i><p>No hay proyectos en el sistema</p></div></div>'
            : proyectos.map(p => `
            <div class="col-sm-6 col-lg-4 col-xl-3 _proyAdminCard" data-nombre="${esc(p.nombre).toLowerCase()}">
                <div class="lc-card-admin" style="cursor:default">
                    <!-- Cabecera del proyecto -->
                    <div style="padding:16px 14px 12px;border-bottom:1px solid var(--bd-color)">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                            <div style="width:38px;height:38px;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="bi bi-folder2-open" style="color:#fff;font-size:1rem"></i>
                            </div>
                            <code style="background:rgba(102,126,234,.08);color:var(--primary);padding:2px 8px;border-radius:6px;font-size:.72rem;font-weight:700;letter-spacing:.04em">${esc(p.codigo)}</code>
                        </div>
                        <div style="font-weight:700;font-size:.9rem;color:var(--txt-main);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${esc(p.nombre)}">${esc(p.nombre)}</div>
                        ${p.descripcion ? `<div style="font-size:.7rem;color:var(--txt-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(p.descripcion)}</div>` : ''}
                        <div style="font-size:.7rem;color:var(--txt-muted);margin-top:4px"><i class="bi bi-person me-1"></i>${esc(p.owner_nombre||p.owner_username||'—')}</div>
                    </div>
                    <!-- Stats rápidos -->
                    <div style="display:flex;border-bottom:1px solid var(--bd-color)">
                        <div style="flex:1;text-align:center;padding:8px 4px;border-right:1px solid var(--bd-color)">
                            <div style="font-size:.95rem;font-weight:700;color:var(--primary)">${p.num_diagramas||0}</div>
                            <div style="font-size:.6rem;color:var(--txt-muted)">Diagramas</div>
                        </div>
                        <div style="flex:1;text-align:center;padding:8px 4px;border-right:1px solid var(--bd-color)">
                            <div style="font-size:.95rem;font-weight:700;color:#10b981">${p.num_miembros||0}</div>
                            <div style="font-size:.6rem;color:var(--txt-muted)">Miembros</div>
                        </div>
                        <div style="flex:1;text-align:center;padding:8px 4px">
                            <div style="font-size:.95rem;font-weight:700;color:#f59e0b">${p.num_archivos||0}</div>
                            <div style="font-size:.6rem;color:var(--txt-muted)">Archivos</div>
                        </div>
                    </div>
                    <!-- Acciones -->
                    <div class="lc-footer-admin">
                        <button class="lc-btn-open-admin" onclick="verDetalleProyAdmin(${p.id},'${esc(p.nombre)}')">
                            <i class="bi bi-eye me-1"></i>Ver detalle
                        </button>
                        <button class="lc-btn-danger-admin" title="Eliminar proyecto"
                            onclick="eliminarProyAdmin(${p.id},'${esc(p.nombre)}')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>`).join('')}
        </div>`;
    } catch(e) { loadErr(e.message); }
}

function filtrarProysAdmin(q) {
    q = q.toLowerCase();
    document.querySelectorAll('._proyAdminCard').forEach(card => {
        card.style.display = !q || (card.dataset.nombre||'').includes(q) ? '' : 'none';
    });
}

async function verDetalleProyAdmin(pid, nombre) {
    // Mostrar panel completo de proyecto dentro del contentArea (no modal)
    loading();
    try {
        const data = await api(`<?= BASE_URL ?>/api/proyectos?action=detalle&id=${pid}`);
        const p = data.proyecto || {};
        const diags = data.diagramas || [];
        const miembros = data.miembros || [];
        const archivos = data.archivos || [];

        document.getElementById('contentArea').innerHTML = `
        <!-- Breadcrumb -->
        <div data-proy-admin-pid="${pid}" style="display:none"></div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:18px;font-size:.82rem;color:var(--txt-muted)">
            <button onclick="showSection('proyectos-admin')"
                style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:.82rem;padding:0;font-weight:600">
                <i class="bi bi-arrow-left me-1"></i>Todos los proyectos
            </button>
            <span>/</span>
            <span style="color:var(--txt-main);font-weight:700">${esc(p.nombre||nombre)}</span>
            <span style="background:rgba(239,68,68,.12);color:#ef4444;border-radius:6px;padding:1px 8px;font-size:.65rem;font-weight:700;margin-left:auto">ADMIN — Acceso total</span>
        </div>

        <!-- Stats rápidos -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:18px">
            <div class="stat-card" style="padding:14px;text-align:center">
                <div style="font-size:1.4rem;font-weight:700;color:var(--primary)">${diags.length}</div>
                <div style="font-size:.72rem;color:var(--txt-muted)">Diagramas</div>
            </div>
            <div class="stat-card" style="padding:14px;text-align:center">
                <div style="font-size:1.4rem;font-weight:700;color:#10b981">${miembros.length}</div>
                <div style="font-size:.72rem;color:var(--txt-muted)">Miembros</div>
            </div>
            <div class="stat-card" style="padding:14px;text-align:center">
                <div style="font-size:1.4rem;font-weight:700;color:#f59e0b">${archivos.length}</div>
                <div style="font-size:.72rem;color:var(--txt-muted)">Archivos</div>
            </div>
        </div>

        <!-- Tabs -->
        <div style="display:flex;gap:6px;border-bottom:2px solid #2a2a4a;margin-bottom:16px">
            <button id="aTabD" onclick="adminProyTab('diags')"
                style="background:none;border:none;border-bottom:3px solid var(--primary);color:var(--primary);padding:8px 16px;font-size:.85rem;font-weight:700;cursor:pointer;margin-bottom:-2px">
                <i class="bi bi-diagram-3 me-1"></i>Diagramas
            </button>
            <button id="aTabM" onclick="adminProyTab('miem')"
                style="background:none;border:none;border-bottom:3px solid transparent;color:#888;padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                <i class="bi bi-people me-1"></i>Miembros
            </button>
            <button id="aTabA" onclick="adminProyTab('arch')"
                style="background:none;border:none;border-bottom:3px solid transparent;color:#888;padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                <i class="bi bi-folder2-open me-1"></i>Archivos
            </button>
            <button id="aTabO" onclick="adminProyTab('obs')"
                style="background:none;border:none;border-bottom:3px solid transparent;color:#888;padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                <i class="bi bi-chat-left-text me-1"></i>Observaciones
            </button>
        </div>

        <!-- Panel Diagramas — V46 cards Lucidchart -->
        <div id="aPanelDiags">
        ${diags.length === 0
            ? '<div style="text-align:center;padding:40px;color:var(--txt-muted)"><i class="bi bi-diagram-3" style="font-size:2.5rem;display:block;margin-bottom:10px;opacity:.3"></i><p>Sin diagramas en este proyecto</p></div>'
            : `<div class="row g-3">
                ${diags.map(d => {
                    const tipoLabel = TIPOS[d.tipo_diagrama] || d.tipo_diagrama;
                    const fecha = new Date(d.fecha_modificacion||d.fecha_creacion).toLocaleDateString('es-MX');
                    const icon44 = typeof getTipoIconoSVG==='function' ? getTipoIconoSVG(d.tipo_diagrama,44) : '';
                    const icon11 = typeof getTipoIconoSVG==='function' ? getTipoIconoSVG(d.tipo_diagrama,11) : '';
                    return `<div class="col-sm-6 col-lg-4">
                        <div class="lc-card-admin">
                            <div class="lc-preview-admin" data-preview-id="${d.id}"
                                 onclick="window.open('<?= BASE_URL ?>/editor?id=${d.id}','_blank')" title="Abrir en editor">
                                <div style="display:flex;align-items:center;justify-content:center;height:100%;opacity:0.3">${icon44}</div>
                            </div>
                            <div class="lc-body-admin">
                                <div class="lc-title-admin" title="${esc(d.titulo||'Sin título')}">${esc(d.titulo||'Sin título')}</div>
                                <div class="lc-meta-admin">
                                    <span style="display:inline-flex;align-items:center;gap:3px">${icon11}&nbsp;${tipoLabel}</span>
                                    &nbsp;·&nbsp;${esc(d.autor||'—')} · v${d.version||1} · ${fecha}
                                </div>
                            </div>
                            <div class="lc-footer-admin">
                                <a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank" class="lc-btn-open-admin">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <button class="lc-btn-danger-admin" title="Quitar del proyecto"
                                    onclick="adminEliminarDiagProyecto(${pid},${d.id},'${esc(d.titulo||'')}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
                }).join('')}
               </div>`}
        </div>

        <!-- Panel Miembros -->
        <div id="aPanelMiem" style="display:none">
        ${miembros.map(mb => `
            <div style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:10px;padding:12px 16px;margin-bottom:8px;display:flex;align-items:center;gap:12px">
                <div style="width:36px;height:36px;background:${mb.rol_proyecto==='owner'?'linear-gradient(135deg,var(--primary),var(--primary2))':'#2a2a4a'};border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0">
                    ${esc((mb.nombre_completo||mb.username||'?')[0].toUpperCase())}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;color:var(--txt-main);font-size:.88rem">${esc(mb.nombre_completo||mb.username)}</div>
                    <div style="font-size:.7rem;color:#888">@${esc(mb.username)} · ${mb.rol_proyecto}</div>
                </div>
                ${mb.rol_proyecto !== 'owner' ? `
                <button onclick="adminExpulsarMiembro(${pid},${mb.id},'${esc(mb.username)}')"
                    style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#ef4444;border-radius:7px;padding:5px 10px;font-size:.72rem;cursor:pointer">
                    <i class="bi bi-person-x me-1"></i>Expulsar
                </button>` : '<span style="background:rgba(102,126,234,.2);color:var(--primary);border-radius:6px;padding:3px 10px;font-size:.7rem;font-weight:700">OWNER</span>'}
            </div>`).join('')}
        </div>

        <!-- Panel Archivos — V46 explorador tipo carpeta -->
        <div id="aPanelArch" style="display:none">
        ${archivos.length === 0
            ? '<div style="text-align:center;padding:40px;color:var(--txt-muted)"><i class="bi bi-folder" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:10px"></i><p>Sin archivos en este proyecto</p></div>'
            : `<div class="folder-section">
                <div class="folder-header">
                    <i class="bi bi-folder2-open" style="color:var(--primary)"></i>
                    <span>Archivos del Proyecto</span>
                    <span style="margin-left:auto;font-size:.72rem;font-weight:400;color:var(--txt-muted)">${archivos.length} archivo${archivos.length!=1?'s':''}</span>
                </div>
                <div class="folder-body">
                    <div class="row g-2">
                    ${archivos.map(f => {
                        const vUrl = '<?= BASE_URL ?>/api/proyectos/view?file_id='+f.id;
                        const dUrl = '<?= BASE_URL ?>/api/proyectos/download?file_id='+f.id;
                        const ext = (f.extension||'').toLowerCase();
                        const puedeVer = ['pdf','png','jpg','jpeg','gif','webp','svg','txt','md','csv','json'].includes(ext);
                        const extIcons = {pdf:'bi-file-earmark-pdf',png:'bi-file-earmark-image',jpg:'bi-file-earmark-image',jpeg:'bi-file-earmark-image',gif:'bi-file-earmark-image',webp:'bi-file-earmark-image',svg:'bi-file-earmark-image',doc:'bi-file-earmark-word',docx:'bi-file-earmark-word',xls:'bi-file-earmark-excel',xlsx:'bi-file-earmark-excel',ppt:'bi-file-earmark-slides',pptx:'bi-file-earmark-slides',txt:'bi-file-earmark-text',md:'bi-file-earmark-text',csv:'bi-file-earmark-spreadsheet',json:'bi-file-earmark-code',sql:'bi-file-earmark-code',zip:'bi-file-earmark-zip'};
                        const extColors = {pdf:'#ef4444',png:'#8b5cf6',jpg:'#8b5cf6',jpeg:'#8b5cf6',gif:'#8b5cf6',webp:'#8b5cf6',svg:'#10b981',doc:'#2563eb',docx:'#2563eb',xls:'#059669',xlsx:'#059669',ppt:'#f97316',pptx:'#f97316',txt:'#6b7280',md:'#6b7280',csv:'#0891b2',json:'#ca8a04',sql:'#7c3aed',zip:'#9333ea'};
                        const icon = extIcons[ext] || 'bi-file-earmark';
                        const color = extColors[ext] || 'var(--primary)';
                        return `<div class="col-sm-6 col-md-4 col-lg-3">
                            <div style="background:var(--bg-deep);border:1.5px solid var(--bd-color);border-radius:10px;padding:10px;transition:all .18s;${puedeVer?'cursor:pointer':''}"
                                 onmouseover="this.style.borderColor='var(--primary)'"
                                 onmouseout="this.style.borderColor='var(--bd-color)'"
                                 ${puedeVer?`onclick="verArchivoAdmin('${esc(f.nombre_original)}','${vUrl}','${dUrl}','${ext}')"`:''}>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                                    <i class="bi ${icon}" style="color:${color};font-size:1.5rem;flex-shrink:0"></i>
                                    <div style="flex:1;min-width:0">
                                        <div style="font-weight:600;font-size:.78rem;color:var(--txt-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${esc(f.nombre_original)}">${esc(f.nombre_original)}</div>
                                        <div style="font-size:.64rem;color:var(--txt-muted)">.${ext.toUpperCase()}${puedeVer?' · clic para ver':''}</div>
                                    </div>
                                </div>
                                <div style="font-size:.63rem;color:var(--txt-muted);margin-bottom:6px"><i class="bi bi-person me-1"></i>${esc(f.subido_por_nombre||'—')}</div>
                                <div style="display:flex;gap:4px" onclick="event.stopPropagation()">
                                    <a href="${dUrl}" download style="flex:1;text-align:center;background:rgba(16,185,129,.12);color:#10b981;border:none;border-radius:6px;padding:4px 0;font-size:.7rem;text-decoration:none">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button onclick="adminEliminarArchivo(${f.id},${pid})"
                                        style="flex:1;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:6px;padding:4px 0;font-size:.7rem;cursor:pointer">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    }).join('')}
                    </div>
                </div>
            </div>`}
        </div>

        <!-- Panel Observaciones -->
        <div id="aPanelObs" style="display:none">
            <div id="aObsCont" style="text-align:center;padding:20px"><div class="spinner-border spinner-border-sm" style="color:var(--primary)"></div></div>
        </div>`;

    } catch(e) { toast(e.message,'err'); showSection('proyectos-admin'); }
    // V46: activar previews en cards de diagramas
    if (window.DiagramMiniRenderer) {
        requestAnimationFrame(() => DiagramMiniRenderer.observeAll(document.getElementById('aPanelDiags')));
    }
}

function adminProyTab(tab) {
    ['aPanelDiags','aPanelMiem','aPanelArch','aPanelObs'].forEach(id=>{
        const el=document.getElementById(id); if(el) el.style.display='none';
    });
    ['aTabD','aTabM','aTabA','aTabO'].forEach(id=>{
        const b=document.getElementById(id);
        if(b){b.style.borderBottomColor='transparent';b.style.color='#888';b.style.fontWeight='';}
    });
    const panels={diags:'aPanelDiags',miem:'aPanelMiem',arch:'aPanelArch',obs:'aPanelObs'};
    const tabs={diags:'aTabD',miem:'aTabM',arch:'aTabA',obs:'aTabO'};
    const el=document.getElementById(panels[tab]); if(el) el.style.display='';
    const b=document.getElementById(tabs[tab]); if(b){b.style.borderBottomColor='var(--primary)';b.style.color='var(--primary)';b.style.fontWeight='700';}
    if(tab==='obs') cargarObsAdmin();
}

async function cargarObsAdmin() {
    const cont = document.getElementById('aObsCont');
    const pid = document.querySelector('[data-proy-admin-pid]')?.dataset.proyAdminPid;
    if (!pid) { if(cont) cont.innerHTML='<p style="color:#888">No se pudo determinar el proyecto</p>'; return; }
    try {
        const data = await api(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}`);
        const obs = data.observaciones||[];
        if(!obs.length){
            cont.innerHTML='<div style="text-align:center;padding:40px;color:var(--txt-muted)"><i class="bi bi-chat-left-text" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px"></i><p>Sin observaciones en este proyecto</p></div>';
            return;
        }
        // Renderizar como hilo de conversación (maestro observa → alumno responde)
        const rolColors = { maestro:'linear-gradient(135deg,#667eea,#764ba2)', alumno:'linear-gradient(135deg,#10b981,#059669)', admin:'linear-gradient(135deg,#f59e0b,#d97706)' };
        cont.innerHTML = obs.map(o => {
            const esMaestro = o.rol_autor === 'maestro' || o.rol_autor === 'admin';
            const colorAvatar = rolColors[o.rol_autor] || 'linear-gradient(135deg,#6b7280,#4b5563)';
            const rolTag = o.rol_autor ? `<span style="font-size:.6rem;background:rgba(102,126,234,.15);color:var(--primary);border-radius:4px;padding:1px 5px;margin-left:4px;font-weight:700;text-transform:uppercase">${esc(o.rol_autor)}</span>` : '';
            const diagTag = o.diagrama_titulo ? `<div style="font-size:.63rem;color:var(--primary);margin-top:2px;opacity:.7"><i class="bi bi-diagram-3 me-1"></i>${esc(o.diagrama_titulo)}</div>` : '';
            const replyCount = o.num_respuestas ? `<div style="font-size:.62rem;color:var(--txt-muted);margin-top:5px"><i class="bi bi-reply me-1"></i>${o.num_respuestas} respuesta${o.num_respuestas!=1?'s':''}</div>` : '';
            return `<div class="obs-bubble ${esMaestro?'':'reply'}">
                <div class="obs-avatar" style="background:${colorAvatar}">
                    ${esc((o.autor_nombre||'?')[0].toUpperCase())}
                </div>
                <div class="obs-body">
                    <div class="obs-meta">${esc(o.autor_nombre||o.autor_username)}${rolTag}&nbsp;·&nbsp;${new Date(o.fecha_creacion).toLocaleString('es-MX')}</div>
                    ${diagTag}
                    <div class="obs-text">${esc(o.texto)}</div>
                    ${replyCount}
                    <div style="display:flex;justify-content:flex-end;margin-top:6px">
                        <button onclick="adminEliminarObs(${o.id},${pid})"
                            style="background:rgba(239,68,68,.1);border:none;color:#ef4444;border-radius:6px;padding:2px 8px;font-size:.68rem;cursor:pointer">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    } catch(e){ if(cont) cont.innerHTML=`<p style="color:#ef4444">${esc(e.message)}</p>`; }
}

async function adminEliminarDiagProyecto(pid, did, titulo) {
    if (!confirm(`¿Quitar el diagrama "${titulo}" del proyecto?`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=quitar_diagrama', { proyecto_id: pid, diagrama_id: did });
        if (r.success) { toast('Diagrama quitado','ok'); verDetalleProyAdmin(pid, ''); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function adminExpulsarMiembro(pid, mid, username) {
    if (!confirm(`¿Expulsar a @${username} del proyecto?`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/proyectos?action=expulsar_miembro', { proyecto_id: pid, miembro_id: mid });
        if (r.success) { toast('Miembro expulsado','ok'); verDetalleProyAdmin(pid, ''); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

function verArchivoAdmin(nombre, vUrl, dUrl, ext) {
    // Reutilizar el mismo modal del visor (admin usa dashboard.php separado pero misma lógica)
    document.getElementById('_aVisor')?.remove();
    const m = document.createElement('div');
    m.id='_aVisor'; m.className='modal fade'; m.tabIndex=-1;
    const esImg=['png','jpg','jpeg','gif','webp','svg'].includes(ext);
    const esPDF=ext==='pdf';
    const esTxt=['txt','md','csv','json','html','xml'].includes(ext);
    let body = esImg ? `<div style="text-align:center;background:#000;padding:16px"><img src="${vUrl}" style="max-width:100%;max-height:72vh;object-fit:contain"></div>`
             : esPDF ? `<iframe src="${vUrl}" style="width:100%;height:76vh;border:none;display:block"></iframe>`
             : esTxt ? `<div style="background:#0a0a14;padding:16px;max-height:72vh;overflow:auto"><pre id="_avtxt" style="color:#c0ccff;font-family:monospace;font-size:.82rem;margin:0;white-space:pre-wrap">Cargando…</pre></div>`
             : `<div style="padding:40px;text-align:center;color:#888">Vista previa no disponible</div>`;
    m.innerHTML=`<div class="modal-dialog modal-dialog-centered" style="max-width:min(900px,96vw)"><div class="modal-content" style="background:#111128;border:1px solid #2a2a4a;border-radius:14px;overflow:hidden">
        <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:12px 18px;display:flex;align-items:center;gap:10px">
            <span style="color:#fff;font-weight:700;font-size:.88rem;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(nombre)}</span>
            <a href="${dUrl}" download="${esc(nombre)}" style="background:rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:5px 13px;font-size:.75rem;text-decoration:none;flex-shrink:0"><i class="bi bi-download me-1"></i>Descargar</a>
            <button data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;flex-shrink:0"><i class="bi bi-x-lg"></i></button>
        </div>${body}</div></div>`;
    document.body.appendChild(m);
    const bsM=new bootstrap.Modal(m);
    m.addEventListener('hidden.bs.modal',()=>m.remove());
    bsM.show();
    if(esTxt) fetch(vUrl).then(r=>r.text()).then(t=>{const el=m.querySelector('#_avtxt');if(el)el.textContent=t;}).catch(()=>{});
}

async function adminEliminarArchivo(fid, pid) {
    if (!confirm('¿Eliminar este archivo?')) return;
    try {
        const r = await fetch('<?= BASE_URL ?>/api/proyectos/del-file', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ file_id: fid })
        }).then(r=>r.json());
        if (r.success) { toast('Archivo eliminado','ok'); verDetalleProyAdmin(pid,''); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function adminEliminarObs(oid, pid) {
    if (!confirm('¿Eliminar esta observación?')) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/observaciones/del', { obs_id: oid });
        if (r.success) { toast('Observación eliminada','ok'); cargarObsAdmin(); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

async function eliminarProyAdmin(pid, nombre) {
    if (!confirm(`¿Eliminar el proyecto "${nombre}" y todos sus datos? Esta acción no se puede deshacer.`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=eliminar_proyecto', { proyecto_id: pid });
        if (r.success) { toast('Proyecto eliminado','ok'); renderProyectosAdmin(); }
        else throw new Error(r.error||'Error al eliminar');
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// CONFIGURACIÓN GLOBAL
// ════════════════════════════════════════════════════════════
async function renderConfig() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/admin/config');
        const c = data.config || {};
        document.getElementById('contentArea').innerHTML = `
        <div class="section-card">
            <div class="card-header"><i class="bi bi-sliders text-primary"></i><h5>Configuración Global del Sistema</h5>
                <button class="ms-auto btn-admin" onclick="guardarConfig()"><i class="bi bi-floppy me-1"></i>Guardar cambios</button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted d-block mb-1">Espacio máx. alumno (MB)</label>
                        <input id="cfg_espacio_alumno" type="number" class="form-control-dark w-100" value="${esc(c.espacio_limite_alumno_mb||'100')}">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block mb-1">Espacio máx. maestro (MB)</label>
                        <input id="cfg_espacio_maestro" type="number" class="form-control-dark w-100" value="${esc(c.espacio_limite_maestro_mb||'500')}">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block mb-1">Máx. proyectos por alumno</label>
                        <input id="cfg_max_proyectos" type="number" class="form-control-dark w-100" value="${esc(c.max_proyectos_alumno||'10')}">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block mb-1">Máx. miembros por proyecto</label>
                        <input id="cfg_max_miembros" type="number" class="form-control-dark w-100" value="${esc(c.max_miembros_proyecto||'20')}">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block mb-1">Tamaño máx. archivo (MB)</label>
                        <input id="cfg_tamano_archivo" type="number" class="form-control-dark w-100" value="${esc(c.tamano_max_archivo_mb||'25')}">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block mb-1">Tipos de archivo permitidos</label>
                        <input id="cfg_tipos_archivo" type="text" class="form-control-dark w-100" value="${esc(c.tipos_archivo_permitidos||'pdf,doc,docx,xls,xlsx,png,jpg')}">
                        <small class="text-muted">Separados por comas, sin punto</small>
                    </div>
                </div>
                <div id="cfgMsg" class="mt-3"></div>
            </div>
        </div>`;
    } catch(e) { loadErr(e.message); }
}

async function guardarConfig() {
    const cfg = {
        espacio_limite_alumno_mb:  document.getElementById('cfg_espacio_alumno')?.value,
        espacio_limite_maestro_mb: document.getElementById('cfg_espacio_maestro')?.value,
        max_proyectos_alumno:      document.getElementById('cfg_max_proyectos')?.value,
        max_miembros_proyecto:     document.getElementById('cfg_max_miembros')?.value,
        tamano_max_archivo_mb:     document.getElementById('cfg_tamano_archivo')?.value,
        tipos_archivo_permitidos:  document.getElementById('cfg_tipos_archivo')?.value,
    };
    try {
        const r = await api('<?= BASE_URL ?>/api/admin/config', cfg);
        if (r.success) toast('Configuración guardada','ok');
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// CORREO / SMTP
// ════════════════════════════════════════════════════════════

// ── V46: SMTP y modo mantenimiento eliminados ─────────────────────────
function renderSmtp() { document.getElementById('contentArea').innerHTML = '<div class="section-card"><div class="card-body"><p style="color:var(--txt-muted);padding:30px;text-align:center"><i class="bi bi-trash3 me-2"></i>Función eliminada en V46</p></div></div>'; }
function renderModeMante() { renderSmtp(); }
function guardarSmtp() {}
function probarSmtp() {}
function setMante() {}
// ──────────────────────────────────────────────────────────────────────


// ════════════════════════════════════════════════════════════
// AUDITORÍA DE ACCESOS
// ════════════════════════════════════════════════════════════
async function renderAuditoria(accion = '') {
    loading();
    try {
        const url = '<?= BASE_URL ?>/api/admin/auditoria' + (accion ? '?accion='+encodeURIComponent(accion) : '?limite=150');
        const data = await api(url);
        const eventos = data.eventos || [];
        const tipos   = data.tipos   || [];

        document.getElementById('contentArea').innerHTML = `
        <div class="section-card">
            <div class="card-header"><i class="bi bi-shield-check text-success"></i><h5>Auditoría de Accesos</h5>
                <div class="ms-auto d-flex gap-2">
                    <select onchange="renderAuditoria(this.value)" class="form-control-dark" style="font-size:.78rem">
                        <option value="">Todas las acciones</option>
                        ${tipos.map(t=>`<option value="${esc(t)}" ${t===accion?'selected':''}>${esc(t)}</option>`).join('')}
                    </select>
                    <button class="btn-admin-outline" onclick="renderAuditoria()"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="admin-table">
                    <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>IP</th><th>Detalle</th></tr></thead>
                    <tbody>
                    ${eventos.length === 0
                        ? '<tr><td colspan="5" style="text-align:center;color:#888;padding:30px">Sin eventos registrados aún</td></tr>'
                        : eventos.map(e => `<tr>
                            <td style="font-size:.75rem;white-space:nowrap;color:#888">${new Date(e.fecha).toLocaleString('es-MX')}</td>
                            <td style="color:var(--primary,#aab8ff)">${esc(e.username||'—')}</td>
                            <td><span style="background:rgba(102,126,234,.12);color:var(--primary);border-radius:4px;padding:1px 7px;font-size:.72rem">${esc(e.accion)}</span></td>
                            <td style="font-size:.75rem;color:#888">${esc(e.ip||'—')}</td>
                            <td style="font-size:.75rem;color:var(--txt-muted);max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(e.detalle||'')}</td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            </div>
        </div>`;
    } catch(e) { loadErr(e.message); }
}

// ════════════════════════════════════════════════════════════
// BACKUP DE BASE DE DATOS
// ════════════════════════════════════════════════════════════
async function renderBackup() {
    loading();
    document.getElementById('contentArea').innerHTML = `
    <div class="section-card">
        <div class="card-header"><i class="bi bi-database-down text-warning"></i><h5>Backup de Base de Datos</h5></div>
        <div class="card-body">
            <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:.83rem;color:#fcd34d">
                <i class="bi bi-exclamation-triangle me-2"></i>
                El backup incluye <strong>todas las tablas y datos</strong> de la base de datos en un archivo SQL.
                Se guarda en <code>data/</code> dentro del servidor. Descárgalo inmediatamente después de crearlo.
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
                <button class="btn-admin" style="font-size:.9rem;padding:12px 24px" onclick="hacerBackup()">
                    <i class="bi bi-database-down me-2"></i>Generar Backup Ahora
                </button>
            </div>
            <div id="backupLog" class="log-output mt-4 d-none" style="min-height:60px"></div>
        </div>
    </div>`;
}

async function hacerBackup() {
    const log = document.getElementById('backupLog');
    if (log) { log.classList.remove('d-none'); log.textContent = 'Generando backup... esto puede tardar unos segundos.'; }
    try {
        const r = await api('<?= BASE_URL ?>/api/admin/backup', {});
        if (r.success) {
            if (log) log.innerHTML = `✅ Backup generado: <strong>${esc(r.filename)}</strong><br>Tablas: ${r.tablas} · Tamaño: ${formatBytes(r.size)}<br><small style="color:#888">Guardado en data/ del servidor</small>`;
            toast('Backup completado: ' + r.filename,'ok');
        } else throw new Error(r.error);
    } catch(e) {
        if (log) log.textContent = '❌ Error: ' + e.message;
        toast(e.message,'err');
    }
}

// ════════════════════════════════════════════════════════════
// REPORTES Y ESTADÍSTICAS
// ════════════════════════════════════════════════════════════
async function renderReportes() {
    loading();
    try {
        const [usuarios, diagramas, proyData] = await Promise.all([
            api('<?= BASE_URL ?>/api/admin?action=stats_usuarios'),
            api('<?= BASE_URL ?>/api/admin?action=stats_diagramas'),
            api('<?= BASE_URL ?>/api/admin?action=proyectos'),
        ]);
        const proyectos = proyData.proyectos || [];
        const porTipo   = diagramas.por_tipo || [];
        const totalD    = diagramas.total || 0;

        document.getElementById('contentArea').innerHTML = `
        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="stat-card" style="text-align:center">
                <div style="font-size:2rem;font-weight:700;color:var(--primary)">${proyectos.length}</div>
                <div style="font-size:.8rem;color:var(--txt-muted)">Proyectos totales</div>
            </div></div>
            <div class="col-md-3"><div class="stat-card" style="text-align:center">
                <div style="font-size:2rem;font-weight:700;color:#10b981">${usuarios.total||0}</div>
                <div style="font-size:.8rem;color:var(--txt-muted)">Usuarios registrados</div>
            </div></div>
            <div class="col-md-3"><div class="stat-card" style="text-align:center">
                <div style="font-size:2rem;font-weight:700;color:#f59e0b">${totalD}</div>
                <div style="font-size:.8rem;color:var(--txt-muted)">Diagramas creados</div>
            </div></div>
            <div class="col-md-3"><div class="stat-card" style="text-align:center">
                <div style="font-size:2rem;font-weight:700;color:#ef4444">${formatBytes(diagramas.espacio||0)}</div>
                <div style="font-size:.8rem;color:var(--txt-muted)">Espacio usado</div>
            </div></div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-pie-chart text-warning"></i><h5>Distribución por tipo de diagrama</h5></div>
                    <div class="card-body">
                    ${porTipo.length === 0 ? '<p class="text-muted small">Sin datos</p>'
                    : porTipo.map(t => {
                        const pct = totalD ? Math.round(t.count/totalD*100) : 0;
                        return `<div class="mb-2">
                            <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                                <small style="color:var(--txt-main)">${TIPOS[t.tipo_diagrama]||t.tipo_diagrama}</small>
                                <small style="color:#888">${t.count} (${pct}%)</small>
                            </div>
                            <div style="height:6px;background:#1e1e3a;border-radius:3px">
                                <div style="height:100%;width:${pct}%;background:linear-gradient(90deg,#667eea,#764ba2);border-radius:3px"></div>
                            </div>
                        </div>`;
                    }).join('')}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-people text-info"></i><h5>Usuarios por rol</h5></div>
                    <div class="card-body">
                        ${[['alumno','#667eea','Alumnos'],['maestro','#10b981','Maestros'],['admin','#ef4444','Administradores']].map(([rol,col,label])=>`
                        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid #1e1e3a">
                            <div style="width:10px;height:10px;border-radius:50%;background:${col};flex-shrink:0"></div>
                            <div style="flex:1;font-size:.85rem;color:var(--txt-main)">${label}</div>
                            <div style="font-size:1.1rem;font-weight:700;color:${col}">${(usuarios['por_rol']||{})[rol]||0}</div>
                        </div>`).join('')}
                    </div>
                </div>
                <div class="section-card mt-3">
                    <div class="card-header"><i class="bi bi-folder2-open text-primary"></i><h5>Top proyectos por diagramas</h5></div>
                    <div class="card-body p-0">
                    ${proyectos.slice(0,5).map(p=>`
                        <div style="display:flex;align-items:center;gap:10px;padding:8px 14px;border-bottom:1px solid #1e1e3a;cursor:pointer"
                             onclick="verDetalleProyAdmin(${p.id},'${esc(p.nombre)}')">
                            <div style="flex:1;font-size:.83rem;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(p.nombre)}</div>
                            <span style="background:rgba(16,185,129,.12);color:#10b981;border-radius:6px;padding:2px 8px;font-size:.72rem;flex-shrink:0">${p.num_diagramas} diags</span>
                        </div>`).join('')}
                    </div>
                </div>
            </div>
        </div>`;
    } catch(e) { loadErr(e.message); }
}

// ════════════════════════════════════════════════════════════
// MODO MANTENIMIENTO
// ════════════════════════════════════════════════════════════


// ════════════════════════════════════════════════════════════
async function renderResumen() {
    loading();
    try {
        const [usuarios, diagramas, proyectosData] = await Promise.all([
            api('<?= BASE_URL ?>/api/admin?action=stats_usuarios'),
            api('<?= BASE_URL ?>/api/admin?action=stats_diagramas'),
            api('<?= BASE_URL ?>/api/admin?action=proyectos')
        ]);

        const numProyectos = (proyectosData.proyectos||[]).length;

        const dbStatusHtml = DB_OK
            ? `<div class="db-status ok"><i class="bi bi-database-fill-check status-ok"></i>
                <div><strong class="text-success">Base de datos conectada</strong>
                <div class="small text-muted">diagramas_db · MySQL</div></div></div>`
            : `<div class="db-status err"><i class="bi bi-database-fill-x status-err"></i>
                <div><strong class="text-danger">Sin conexión a BD</strong>
                <div class="small text-danger">${DB_ERROR}</div>
                <button class="btn-admin-outline mt-2" onclick="showSection('db')">Configurar</button></div></div>`;

        document.getElementById('contentArea').innerHTML = `
            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="stat-card" style="cursor:pointer" onclick="showSection('proyectos-admin')">
                    <div class="stat-icon" style="color:var(--primary)"><i class="bi bi-folder2-open"></i></div>
                    <div class="stat-num">${numProyectos}</div>
                    <div class="stat-label">Proyectos activos</div>
                </div></div>
                <div class="col-md-3"><div class="stat-card" style="cursor:pointer" onclick="showSection('usuarios')">
                    <div class="stat-icon" style="color:#667eea"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-num">${usuarios.total||0}</div>
                    <div class="stat-label">Usuarios registrados</div>
                </div></div>
                <div class="col-md-3"><div class="stat-card" style="cursor:pointer" onclick="showSection('diagramas')">
                    <div class="stat-icon" style="color:#f59e0b"><i class="bi bi-diagram-3-fill"></i></div>
                    <div class="stat-num">${diagramas.total||0}</div>
                    <div class="stat-label">Diagramas creados</div>
                </div></div>
                <div class="col-md-3"><div class="stat-card">
                    <div class="stat-icon" style="color:#ef4444"><i class="bi bi-hdd-fill"></i></div>
                    <div class="stat-num">${formatBytes(diagramas.espacio||0)}</div>
                    <div class="stat-label">Espacio usado</div>
                </div></div>
            </div>

            <div class="row g-3">
                <div class="col-md-5">
                    <div class="section-card">
                        <div class="card-header"><i class="bi bi-database text-primary"></i><h5>Estado del Sistema</h5></div>
                        <div class="card-body">
                            ${dbStatusHtml}
                            <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                <div style="background:#1a1a2e;border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.2rem;font-weight:700;color:var(--primary)">${usuarios.activos||0}</div>
                                    <div style="font-size:.7rem;color:#888">Usuarios activos</div>
                                </div>
                                <div style="background:#1a1a2e;border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.2rem;font-weight:700;color:#10b981">${usuarios.admins||0}</div>
                                    <div style="font-size:.7rem;color:#888">Administradores</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="section-card">
                        <div class="card-header"><i class="bi bi-clock-history text-info"></i><h5>Diagramas recientes</h5>
                            <button class="ms-auto btn-admin-outline" style="font-size:.72rem" onclick="showSection('diagramas')">Ver todos</button>
                        </div>
                        <div class="card-body p-0">
                            <table class="admin-table">
                                <thead><tr><th>Título</th><th>Usuario</th><th>Tipo</th><th>Fecha</th><th></th></tr></thead>
                                <tbody>
                                ${(diagramas.recientes||[]).map(d => `
                                    <tr>
                                        <td style="font-weight:600">${esc(d.titulo)}</td>
                                        <td><span style="color:var(--primary,#aab8ff)">${esc(d.username||'?')}</span></td>
                                        <td><span class="badge-tipo">${TIPOS[d.tipo_diagrama]||d.tipo_diagrama}</span></td>
                                        <td class="text-muted" style="font-size:.75rem">${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</td>
                                        <td><a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank"
                                            style="background:var(--primary);border:none;color:#fff;border-radius:6px;padding:3px 8px;font-size:.7rem;text-decoration:none">
                                            <i class="bi bi-pencil"></i>
                                        </a></td>
                                    </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>`;
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// USUARIOS
// ════════════════════════════════════════════════════════════
// _todosUsuarios: cache de usuarios para filtrado client-side sin recargar del servidor
let _todosUsuarios = [];

async function renderUsuarios() {
    loading();
    try {
        _todosUsuarios = (await api('<?= BASE_URL ?>/api/admin?action=usuarios')).filter(u => u.id != CURRENT_USER_ID);

        // Separar por rol
        const admins   = _todosUsuarios.filter(u => u.rol === 'admin');
        const maestros = _todosUsuarios.filter(u => u.rol === 'maestro');
        const alumnos  = _todosUsuarios.filter(u => u.rol === 'alumno');

        const modales = buildModalesUsuario();

        document.getElementById('contentArea').innerHTML = `
            <!-- ─ Header ──────────────────────────────────────── -->
            <div class="section-card mb-3">
                <div class="card-header">
                    <i class="bi bi-people text-primary"></i>
                    <h5>Usuarios — <span id="uContador">${_todosUsuarios.length}</span> registrados</h5>
                    <div class="ms-auto d-flex gap-2">
                        <button class="btn-admin-outline" onclick="renderUsuarios()"><i class="bi bi-arrow-clockwise"></i></button>
                        ${(() => {
                            const puedeCrear = !IS_ADMIN_JUNIOR || hasPermission('crear_alumnos') || hasPermission('crear_maestros');
                            const titulo = !IS_ADMIN_JUNIOR ? 'Crear Usuario' : 'No tienes permiso para crear usuarios';
                            return `<button class="btn-admin" ${!puedeCrear ? 'disabled title="No tienes permiso para crear usuarios" style="opacity:.6;cursor:not-allowed"' : ''} onclick="${puedeCrear ? 'abrirModalCrearUsuario()' : 'void(0)'}"><i class="bi bi-person-plus me-1"></i>${titulo}</button>`;
                        })()}
                    </div>
                </div>

                <!-- ─ Barra de búsqueda ──────────────────────── -->
                <div class="card-body pb-2 pt-3" style="border-bottom:1px solid #2a2a4a">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="d-flex align-items-center gap-1" style="flex:1;min-width:180px">
                            <i class="bi bi-search text-muted"></i>
                            <input type="text" id="uBusqueda" class="form-control-dark w-100"
                                placeholder="Buscar por nombre o usuario…"
                                style="font-size:.82rem" autocomplete="new-password"
                                name="busqueda_usuarios_nofill"
                                oninput="filtrarUsuarios()">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-toggle-on text-muted"></i>
                            <select id="uFiltroEstado" class="form-control-dark" style="font-size:.82rem" onchange="filtrarUsuarios()">
                                <option value="">Todos los estados</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        <button class="btn-admin-outline" style="font-size:.78rem;padding:6px 12px" onclick="limpiarFiltrosUsuarios()">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </button>
                    </div>
                </div>

                <!-- ─ Tarjetas resumen por rol ───────────────── -->
                <div class="card-body pb-0">
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div id="tabTodos" onclick="setTabRol('todos')"
                                style="cursor:pointer;border:2px solid #667eea;border-radius:10px;padding:12px 14px;background:rgba(102,126,234,.15);transition:all .2s;user-select:none">
                                <div style="font-size:1.4rem;font-weight:700;color:var(--txt-main)">${_todosUsuarios.length}</div>
                                <div style="font-size:.72rem;color:var(--primary,#aab8ff);margin-top:2px"><i class="bi bi-people me-1"></i>Total usuarios</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div id="tabAdmins" onclick="setTabRol('admin')"
                                style="cursor:pointer;border:2px solid var(--bd-color);border-radius:10px;padding:12px 14px;background:var(--bg-card);transition:all .2s;user-select:none;opacity:.85">
                                <div style="font-size:1.4rem;font-weight:700;color:#f59e0b">${admins.length}</div>
                                <div style="font-size:.72rem;color:#f59e0b;margin-top:2px"><i class="bi bi-shield-fill-check me-1"></i>Administradores</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div id="tabMaestros" onclick="setTabRol('maestro')"
                                style="cursor:pointer;border:2px solid var(--bd-color);border-radius:10px;padding:12px 14px;background:var(--bg-card);transition:all .2s;user-select:none;opacity:.85">
                                <div style="font-size:1.4rem;font-weight:700;color:#60a5fa">${maestros.length}</div>
                                <div style="font-size:.72rem;color:#60a5fa;margin-top:2px"><i class="bi bi-person-badge me-1"></i>Maestros</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div id="tabAlumnos" onclick="setTabRol('alumno')"
                                style="cursor:pointer;border:2px solid var(--bd-color);border-radius:10px;padding:12px 14px;background:var(--bg-card);transition:all .2s;user-select:none;opacity:.85">
                                <div style="font-size:1.4rem;font-weight:700;color:#6ee7b7">${alumnos.length}</div>
                                <div style="font-size:.72rem;color:#6ee7b7;margin-top:2px"><i class="bi bi-mortarboard me-1"></i>Alumnos</div>
                            </div>
                        </div>
                    </div>

                    <!-- ─ Tarjetas de admins siempre visibles ── -->
                    ${admins.length > 0 ? `
                    <div style="margin-bottom:10px">
                        <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:#f59e0b;margin-bottom:8px;font-weight:600;display:flex;align-items:center;gap:8px">
                            <i class="bi bi-shield-fill-check"></i>Administradores (${admins.length})
                            <span style="font-weight:400;color:#666;text-transform:none;letter-spacing:0;font-size:.7rem">— siempre visibles • haz clic en la tarjeta de Administradores para filtrar</span>
                        </div>
                        <div class="row g-2">
                        ${admins.map(u => buildUserCard(u)).join('')}
                        </div>
                    </div>` : ''}
                </div>
            </div>

            <!-- ─ Tabla dinámica (filtrada) ───────────────────── -->
            <div class="section-card">
                <div class="card-header" id="tablaHeader">
                    <i class="bi bi-table text-muted"></i>
                    <h5 id="tablaTitle">Todos los usuarios</h5>
                    <span class="ms-auto badge-tipo" id="tablaCount">${_todosUsuarios.length}</span>
                </div>
                <div class="card-body p-0">
                    <table class="admin-table">
                        <thead><tr><th>#</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th><th>Diagramas</th><th>Creado por</th><th>Acciones</th></tr></thead>
                        <tbody id="uTbody"></tbody>
                    </table>
                </div>
            </div>

            ${modales}`;

        // Forzar campo vacío (evita que el navegador rellene con autocomplete)
        const busqEl = document.getElementById('uBusqueda');
        if (busqEl) busqEl.value = '';
        filtrarUsuarios(); // render inicial de la tabla
    } catch(e) { toast(e.message,'err'); }
}

function buildUserCard(u) {
    const colMap = {admin:'#f59e0b', maestro:'#60a5fa', alumno:'#6ee7b7'};
    const col = colMap[u.rol] || '#ccc';
    // Protecciones: junior no puede tocar al superadmin ni editarse a sí mismo permisos
    const esSuperAdmin  = u.id == SUPER_ADMIN_ID;
    const esMismo       = u.id == CURRENT_USER_ID;
    // Principal admin: NO action allowed from any junior or other admin
    const puedeEditar   = !esSuperAdmin && ((!IS_ADMIN_JUNIOR || hasPermission('editar_usuarios')) && !(IS_ADMIN_JUNIOR && esMismo));
    const puedeEliminar = !esSuperAdmin && !(IS_ADMIN_JUNIOR && esMismo);
    const puedePermisos = !esSuperAdmin && ((!IS_ADMIN_JUNIOR || hasPermission('editar_usuarios')) && !(IS_ADMIN_JUNIOR && esMismo));
    const puedeToggle   = !esSuperAdmin && (!IS_ADMIN_JUNIOR || hasPermission('desactivar_usuarios'));

    return `<div class="col-md-6 col-lg-4">
        <div style="background:var(--bg-card,#0d0d1a);border:1px solid var(--bd-color,#2a2a4a);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:12px;transition:border-color .2s"
             onmouseover="this.style.borderColor='${col}60'" onmouseout="this.style.borderColor='var(--bd-color)'">
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary2));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.9rem;flex-shrink:0;position:relative">
                ${(u.username||'?')[0].toUpperCase()}
                ${esSuperAdmin ? '<span style="position:absolute;bottom:-3px;right:-3px;background:#f59e0b;border-radius:50%;width:14px;height:14px;font-size:.5rem;display:flex;align-items:center;justify-content:center" title="Superadmin">★</span>' : ''}
            </div>
            <div style="min-width:0;flex:1">
                <div style="font-weight:600;color:var(--txt-main,#e2e8f0);font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    ${esc(u.username)}
                    ${u.es_admin_junior=='1'?'<span style="color:#f59e0b;font-size:.65rem;font-weight:400"> junior</span>':''}
                    ${esSuperAdmin?'<span style="color:#f59e0b;font-size:.62rem"> ★ principal</span>':''}
                </div>
                <div style="font-size:.72rem;color:var(--txt-muted,#888);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(u.nombre_completo||'—')}</div>
                <span style="font-size:.63rem;color:${u.activo=='1'?'#10b981':'#ef4444'}">${u.activo=='1'?'● Activo':'● Inactivo'}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-end;flex-shrink:0">
                <div class="d-flex gap-1">
                    ${puedeEditar ? `<button class="btn-admin-outline" style="font-size:.65rem;padding:2px 6px" title="Editar usuario"
                        onclick="abrirEditarUsuario(${u.id},'${esc(u.username)}','${esc(u.nombre_completo||'')}','${esc(u.email)}','${u.rol}','${u.activo}')">
                        <i class="bi bi-pencil"></i></button>` : `<button class="btn-admin-outline" disabled style="font-size:.65rem;padding:2px 6px;opacity:.5;cursor:not-allowed" title="No tienes permiso para editar usuarios"><i class="bi bi-pencil"></i></button>`}
                    ${u.rol==='admin' ? (puedePermisos ? `<button class="btn-admin-outline" title="Permisos"
                        style="font-size:.65rem;padding:2px 6px;color:#f59e0b;border-color:#f59e0b"
                        onclick="abrirPermisos(${u.id},'${esc(u.username)}')">
                        <i class="bi bi-shield-lock"></i></button>` : `<button class="btn-admin-outline" disabled style="font-size:.65rem;padding:2px 6px;opacity:.5;cursor:not-allowed" title="No tienes permiso para ver permisos"><i class="bi bi-shield-lock"></i></button>`) : ''}
                    ${esSuperAdmin ? '' : puedeToggle ? `<button class="btn-admin-outline" title="${u.activo=='1'?'Desactivar':'Activar'}"
                        style="font-size:.65rem;padding:2px 6px;color:${u.activo=='1'?'#6ee7b7':'#888'};border-color:${u.activo=='1'?'#6ee7b7':'#555'}"
                        onclick="toggleActivo(${u.id},${u.activo})">
                        <i class="bi bi-${u.activo=='1'?'toggle-on':'toggle-off'}"></i></button>` : `<button class="btn-admin-outline" disabled style="font-size:.65rem;padding:2px 6px;opacity:.5;cursor:not-allowed" title="No tienes permiso para cambiar el estado"><i class="bi bi-${u.activo=='1'?'toggle-on':'toggle-off'}"></i></button>`}
                    ${puedeEliminar && !esSuperAdmin ? `<button class="btn-danger-sm" title="Eliminar usuario"
                        style="font-size:.65rem;padding:2px 6px"
                        onclick="eliminarUsuario(${u.id},'${esc(u.username)}')">
                        <i class="bi bi-trash3"></i></button>` : `<button class="btn-danger-sm" disabled style="font-size:.65rem;padding:2px 6px;opacity:.5;cursor:not-allowed" title="No puedes eliminar este usuario"><i class="bi bi-trash3"></i></button>`}
                </div>
            </div>
        </div>
    </div>`;
}

let _tabRolActual = 'todos';

function setTabRol(rol) {
    _tabRolActual = rol;
    const tabIds = {todos:'tabTodos', admin:'tabAdmins', maestro:'tabMaestros', alumno:'tabAlumnos'};
    const colMap = {todos:'#667eea', admin:'#f59e0b', maestro:'#60a5fa', alumno:'#6ee7b7'};
    const labels = {todos:'Todos los usuarios', admin:'Administradores', maestro:'Maestros', alumno:'Alumnos'};

    Object.entries(tabIds).forEach(([r, id]) => {
        const el = document.getElementById(id);
        if (!el) return;
        const active = r === rol;
        const col = colMap[r];
        el.style.border = `2px solid ${active ? col : 'var(--bd-color)'}`;
        el.style.background = active ? `rgba(${hexToRgb(col)},.15)` : 'var(--bg-card)';
        el.querySelectorAll('div').forEach(d2 => { if (!active) d2.style.opacity = '0.7'; else d2.style.opacity = '1'; });
    });

    const title = document.getElementById('tablaTitle');
    if (title) title.textContent = labels[rol] || 'Usuarios';
    filtrarUsuarios();
}

function hexToRgb(hex) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return `${r},${g},${b}`;
}

// ── Funciones de filtrado de usuarios ────────────────────────
function filtrarUsuarios() {
    const busq   = (document.getElementById('uBusqueda')?.value    || '').toLowerCase().trim();
    const estado = (document.getElementById('uFiltroEstado')?.value|| '');
    const rol    = _tabRolActual === 'todos' ? '' : _tabRolActual;

    const filtrados = _todosUsuarios.filter(u => {
        const matchBusq   = !busq   || (u.username||'').toLowerCase().includes(busq)
                                     || (u.nombre_completo||'').toLowerCase().includes(busq);
        const matchRol    = !rol    || u.rol === rol;
        const matchEstado = !estado || String(u.activo) === estado;
        return matchBusq && matchRol && matchEstado;
    });

    const rolBadge = r => { const map={admin:'#ff6b6b',maestro:'#60a5fa',alumno:'#6ee7b7'}; return `<span style="background:rgba(255,255,255,.08);color:${map[r]||'#ccc'};border:1px solid ${map[r]||'#555'};border-radius:20px;padding:2px 10px;font-size:.72rem">${r}</span>`; };

    const tbody = document.getElementById('uTbody');
    const tablaCount = document.getElementById('tablaCount');
    if (tablaCount) tablaCount.textContent = filtrados.length;
    if (!tbody) return;

    if (filtrados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-search me-2"></i>Sin resultados para esos filtros</td></tr>`;
        return;
    }

    tbody.innerHTML = filtrados.map(u => {
        const esSuperAdmin  = u.id == SUPER_ADMIN_ID;
        const esMismo       = u.id == CURRENT_USER_ID;
        const puedeEditar   = !esSuperAdmin && ((!IS_ADMIN_JUNIOR || hasPermission('editar_usuarios')) && !(IS_ADMIN_JUNIOR && esMismo));
        const puedeEliminar = !esSuperAdmin && !(IS_ADMIN_JUNIOR && esMismo);
        const puedePermisos = !esSuperAdmin && ((!IS_ADMIN_JUNIOR || hasPermission('editar_usuarios')) && !(IS_ADMIN_JUNIOR && esMismo));
        const puedeToggle   = !IS_ADMIN_JUNIOR || hasPermission('desactivar_usuarios');
        return `<tr>
            <td class="text-muted">${u.id}</td>
            <td>
                <strong class="text-light">${esc(u.username)}</strong>
                ${u.es_admin_junior=='1'?' <span style="color:#f59e0b;font-size:.68rem">junior</span>':''}
                ${esSuperAdmin?' <span style="color:#f59e0b;font-size:.65rem">★ principal</span>':''}
            </td>
            <td>${esc(u.nombre_completo||'—')}</td>
            <td>${rolBadge(u.rol)}</td>
            <td>${u.activo=='1'?'<span class="status-ok" style="font-size:.75rem">● Activo</span>':'<span class="status-err" style="font-size:.75rem">● Inactivo</span>'}</td>
            <td><span class="badge-tipo">${u.num_diagramas||0}</span></td>
            <td class="text-muted" style="font-size:.75rem">${esc(u.creador_nombre||'—')}</td>
            <td>
                <div class="d-flex gap-1">
                    ${puedeEditar ? `<button class="btn-admin-outline" style="font-size:.7rem;padding:2px 8px" title="Editar" onclick="abrirEditarUsuario(${u.id},'${esc(u.username)}','${esc(u.nombre_completo||'')}','${esc(u.email)}','${u.rol}','${u.activo}')"><i class="bi bi-pencil"></i></button>` : `<button class="btn-admin-outline" disabled style="font-size:.7rem;padding:2px 8px;opacity:.5;cursor:not-allowed" title="No tienes permiso para editar usuarios"><i class="bi bi-pencil"></i></button>`}
                    ${u.rol==='admin' ? (puedePermisos ? `<button class="btn-admin-outline" style="font-size:.7rem;padding:2px 8px;color:#f59e0b;border-color:#f59e0b" title="Permisos" onclick="abrirPermisos(${u.id},'${esc(u.username)}')"><i class="bi bi-shield-lock"></i></button>` : `<button class="btn-admin-outline" disabled style="font-size:.7rem;padding:2px 8px;opacity:.5;cursor:not-allowed" title="No tienes permiso para ver permisos"><i class="bi bi-shield-lock"></i></button>`) : ''}
                    ${puedeToggle ? `<button class="btn-admin-outline" style="font-size:.7rem;padding:2px 8px" title="${u.activo=='1'?'Desactivar':'Activar'}" onclick="toggleActivo(${u.id},${u.activo})"><i class="bi bi-${u.activo=='1'?'toggle-on':'toggle-off'}"></i></button>` : `<button class="btn-admin-outline" disabled style="font-size:.7rem;padding:2px 8px;opacity:.5;cursor:not-allowed" title="No tienes permiso para cambiar el estado"><i class="bi bi-${u.activo=='1'?'toggle-on':'toggle-off'}"></i></button>`}
                    ${puedeEliminar ? `<button class="btn-danger-sm" style="font-size:.7rem;padding:2px 8px" title="Eliminar" onclick="eliminarUsuario(${u.id},'${esc(u.username)}')"><i class="bi bi-trash3"></i></button>` : `<button class="btn-danger-sm" disabled style="font-size:.7rem;padding:2px 8px;opacity:.5;cursor:not-allowed" title="No puedes eliminar este usuario"><i class="bi bi-trash3"></i></button>`}
                </div>
            </td>
        </tr>`;}).join('');
}

// Resetea los tres filtros de usuarios y restaura la lista completa.
function buildModalesUsuario() {
    return `
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:14px">
                <div class="modal-header" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:14px 14px 0 0">
                    <h5 class="modal-title" id="modalUsuarioTitulo">Crear Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="uEditId">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="small d-block mb-1" style="color:var(--primary,#aab8ff)">
                                <i class="bi bi-person-fill me-1"></i>Nombre completo <span style="color:#ef4444">*</span>
                            </label>
                            <input type="text" id="uNombre" class="form-control-dark w-100"
                                placeholder="Ej: Juan García López" autocomplete="off">
                            <small style="color:#555;font-size:.7rem">Nombre real de la persona</small>
                        </div>
                        <div class="col-6">
                            <label class="small d-block mb-1" style="color:var(--primary,#aab8ff)">
                                <i class="bi bi-at me-1"></i>Nombre de usuario <span style="color:#ef4444">*</span>
                            </label>
                            <input type="text" id="uUsername" class="form-control-dark w-100"
                                placeholder="Ej: juan_garcia" autocomplete="off">
                            <small style="color:#555;font-size:.7rem">Para iniciar sesión — sin espacios</small>
                        </div>
                        <div class="col-12">
                            <label class="small d-block mb-1" style="color:var(--primary,#aab8ff)">
                                <i class="bi bi-envelope-fill me-1"></i>Correo electrónico <span style="color:#ef4444">*</span>
                            </label>
                            <input type="email" id="uEmail" class="form-control-dark w-100"
                                placeholder="correo@ejemplo.com" autocomplete="off">
                            <small style="color:#555;font-size:.7rem">Se usa para notificaciones y recuperación de cuenta</small>
                        </div>
                        <div class="col-12" id="uPasswordBox" style="background:rgba(102,126,234,.12);border:1px solid rgba(102,126,234,.2);border-radius:8px;padding:12px">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                <label class="small" style="color:var(--primary,#aab8ff);font-weight:600;margin:0">
                                    <i class="bi bi-key me-1"></i><span id="uPasswordLabel">Contraseña</span>
                                    <span style="color:#ef4444" id="uPasswordRequired"> *</span>
                                </label>
                                <span id="uPasswordHint" style="color:#f59e0b;font-size:.72rem;font-weight:500"></span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="password" id="uPassword" class="form-control-dark" style="flex:1"
                                    placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                                <button type="button" onclick="togglePassVis()" class="btn-admin-outline"
                                    style="padding:6px 10px;flex-shrink:0" title="Ver/ocultar contraseña">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                                <button type="button" onclick="generarPassword()" class="btn-admin-outline"
                                    style="padding:6px 10px;flex-shrink:0;font-size:.72rem" title="Generar contraseña aleatoria segura">
                                    <i class="bi bi-shuffle me-1"></i>Generar
                                </button>
                            </div>
                            <div id="uPasswordStrength" style="height:3px;border-radius:2px;margin-top:6px;background:#2a2a4a;transition:all .3s"></div>
                        </div>
                        <div class="col-6">
                            <label class="small d-block mb-1" style="color:var(--primary,#aab8ff)">
                                <i class="bi bi-shield-fill me-1"></i>Rol del usuario <span style="color:#ef4444">*</span>
                            </label>
                            <select id="uRol" class="form-control-dark w-100" onchange="onRolChange()">
                                <option value="alumno">🎓 Alumno — usa el editor</option>
                                <option value="maestro">📋 Maestro — grupos y tareas</option>
                                <option value="admin">🛡️ Administrador — panel completo</option>
                            </select>
                            <small style="color:#555;font-size:.7rem">Define qué puede hacer el usuario</small>
                        </div>
                        <div class="col-6" style="display:flex;align-items:flex-end">
                            <label class="d-flex align-items-center gap-2 w-100" style="cursor:pointer;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:8px;padding:10px 12px">
                                <input type="checkbox" id="uActivo" checked style="width:16px;height:16px;accent-color:#10b981">
                                <div>
                                    <div class="small" style="color:#6ee7b7;font-weight:600">Cuenta activa</div>
                                    <div style="font-size:.7rem;color:#888">Si se desactiva, el usuario no puede entrar</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-12" id="juniorSection" style="display:none">
                            <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;overflow:hidden">
                                <div style="background:rgba(245,158,11,.1);border-bottom:1px solid rgba(245,158,11,.2);padding:10px 16px;display:flex;align-items:center;gap:10px">
                                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;flex:1">
                                        <input type="checkbox" id="uEsJunior" style="width:16px;height:16px" onchange="onJuniorChange()">
                                        <i class="bi bi-shield-half" style="color:#f59e0b"></i>
                                        <span class="small" style="color:#fcd34d;font-weight:600">Admin Junior — acceso restringido</span>
                                    </label>
                                    <span class="small text-muted" style="font-size:.72rem">Activa para definir permisos</span>
                                </div>
                                <div id="permisosInlineSection" style="display:none;padding:0">
                                    <div style="padding:10px 16px 6px;font-size:.75rem;color:#888">
                                        <i class="bi bi-info-circle me-1"></i>Las funciones <strong>sin permiso</strong> aparecen bloqueadas en el panel del admin junior.
                                    </div>
                                    <div style="overflow-x:auto">
                                        <table style="width:100%;border-collapse:collapse;font-size:.78rem">
                                            <thead><tr style="background:#0a0a12">
                                                <th style="padding:8px 14px;color:var(--primary);font-weight:600;border-bottom:1px solid #2a2a4a;width:32px"></th>
                                                <th style="padding:8px 14px;color:var(--primary);font-weight:600;border-bottom:1px solid #2a2a4a">Permiso</th>
                                                <th style="padding:8px 14px;color:#888;font-weight:500;border-bottom:1px solid #2a2a4a;font-size:.72rem">Si está <span style="color:#ef4444">OFF</span> → no puede…</th>
                                            </tr></thead>
                                            <tbody id="permisosInlineBody"></tbody>
                                        </table>
                                    </div>
                                    <div style="padding:8px 14px 10px;display:flex;gap:8px">
                                        <button type="button" class="btn-admin-outline" style="font-size:.75rem;padding:4px 12px" onclick="seleccionarTodosPermisos(true)"><i class="bi bi-check-all me-1"></i>Activar todos</button>
                                        <button type="button" class="btn-admin-outline" style="font-size:.75rem;padding:4px 12px;border-color:#555;color:#888" onclick="seleccionarTodosPermisos(false)"><i class="bi bi-x-lg me-1"></i>Desactivar todos</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="uError" class="mt-3 text-danger small d-none"></div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #2a2a4a;justify-content:flex-end;gap:8px">
                    <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn-admin" onclick="guardarUsuario()"><i class="bi bi-check me-1"></i>Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalPermisos" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:14px">
                <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px 14px 0 0">
                    <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Permisos de <span id="permisosUsername"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="permisosAdminId">
                    <p class="text-muted small mb-3">Selecciona qué puede hacer este admin junior:</p>
                    <div id="permisosCheckboxes" class="row g-2"></div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #2a2a4a;justify-content:flex-end;gap:8px">
                    <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn-admin" onclick="guardarPermisos()"><i class="bi bi-check me-1"></i>Guardar Permisos</button>
                </div>
            </div>
        </div>
    </div>`;
}

async function eliminarUsuario(id, username) {
    if (!confirm(`¿Eliminar permanentemente al usuario "${username}"?

Esto también eliminará todos sus diagramas del sistema.`)) return;
    try {
        const r = await api(`<?= BASE_URL ?>/api/admin?action=eliminar_usuario`, { id });
        if (r.success) {
            toast(`Usuario "${username}" eliminado`, 'ok');
            renderUsuarios();
        } else {
            toast(r.error || 'Error al eliminar', 'err');
        }
    } catch(e) { toast(e.message, 'err'); }
}

function limpiarFiltrosUsuarios() {
    ['uBusqueda','uFiltroRol','uFiltroEstado'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    filtrarUsuarios();
}


// Definición extendida con consecuencias visibles
const PERMISOS_DISPONIBLES = [
    { key:'ver_usuarios',        label:'Ver lista de usuarios',       icon:'bi-people',          consecuencia:'No puede ver la sección de Usuarios. El menú aparece bloqueado.' },
    { key:'crear_alumnos',       label:'Crear alumnos',               icon:'bi-person-plus',     consecuencia:'El botón "Crear Usuario" no permite rol Alumno.' },
    { key:'crear_maestros',      label:'Crear maestros',              icon:'bi-person-badge',    consecuencia:'El botón "Crear Usuario" no permite rol Maestro.' },
    { key:'editar_usuarios',     label:'Editar cualquier usuario',    icon:'bi-pencil-square',   consecuencia:'El ícono de edición en la tabla de usuarios queda desactivado.' },
    { key:'desactivar_usuarios', label:'Activar/desactivar usuarios', icon:'bi-toggle-on',       consecuencia:'No puede cambiar el estado activo/inactivo de un usuario.' },
    { key:'ver_diagramas',       label:'Ver todos los diagramas',     icon:'bi-diagram-3',       consecuencia:'La sección de Diagramas del sistema no es accesible.' },
    { key:'eliminar_diagramas',  label:'Eliminar diagramas',          icon:'bi-trash3',          consecuencia:'El botón de eliminar en la lista de diagramas está desactivado.' },
    { key:'ver_grupos',          label:'Ver grupos y tareas',         icon:'bi-collection',      consecuencia:'La sección Grupos & Tareas no aparece en el menú lateral.' },
    { key:'setup_db',            label:'Mantenimiento de BD',         icon:'bi-database-gear',   consecuencia:'No puede ver Instalación ni Mantenimiento de base de datos.' },
    { key:'ver_svgs',            label:'Verificar/generar SVGs',      icon:'bi-image',           consecuencia:'La sección de SVGs no es accesible.' },
];

function onRolChange() {
    const rol = document.getElementById('uRol').value;
    document.getElementById('juniorSection').style.display = rol === 'admin' ? 'block' : 'none';
    if (rol !== 'admin') {
        document.getElementById('uEsJunior').checked = false;
        document.getElementById('permisosInlineSection').style.display = 'none';
    }
}

// Mantener compatibilidad con código anterior
function toggleJuniorCheck() { onRolChange(); }

function onJuniorChange() {
    const esJunior = document.getElementById('uEsJunior').checked;
    const section  = document.getElementById('permisosInlineSection');
    section.style.display = esJunior ? 'block' : 'none';
    if (esJunior) renderPermisosInline([]);
}

function renderPermisosInline(permisosActivos) {
    const activos = new Set(permisosActivos);
    const tbody = document.getElementById('permisosInlineBody');
    if (!tbody) return;
    tbody.innerHTML = PERMISOS_DISPONIBLES.map(p => `
        <tr style="border-bottom:1px solid #1e1e3a;transition:background .15s" onmouseover="this.style.background='rgba(102,126,234,.1)'" onmouseout="this.style.background=''">
            <td style="padding:8px 14px;text-align:center">
                <label style="cursor:pointer;margin:0">
                    <input type="checkbox" class="perm-inline-check" data-key="${p.key}" ${activos.has(p.key) ? 'checked' : ''}
                        style="width:15px;height:15px;accent-color:#667eea" onchange="actualizarFilaPermiso(this)">
                </label>
            </td>
            <td style="padding:8px 14px">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi ${p.icon}" style="color:#667eea;font-size:.9rem;width:16px;text-align:center"></i>
                    <span id="perm-label-${p.key}" style="color:#e0e0e0;font-weight:500">${p.label}</span>
                </div>
            </td>
            <td style="padding:8px 14px">
                <span id="perm-consec-${p.key}" style="color:${activos.has(p.key) ? '#10b981' : '#ef4444'};font-size:.73rem">
                    ${activos.has(p.key) ? '<i class="bi bi-check-circle me-1"></i>Permitido' : '<i class="bi bi-x-circle me-1"></i>' + p.consecuencia}
                </span>
            </td>
        </tr>`).join('');
}

function actualizarFilaPermiso(checkbox) {
    const key     = checkbox.dataset.key;
    const permiso = PERMISOS_DISPONIBLES.find(p => p.key === key);
    const label   = document.getElementById('perm-label-' + key);
    const consec  = document.getElementById('perm-consec-' + key);
    if (!permiso || !label || !consec) return;
    if (checkbox.checked) {
        label.style.color  = '#e0e0e0';
        consec.innerHTML   = '<i class="bi bi-check-circle me-1"></i>Permitido';
        consec.style.color = '#10b981';
    } else {
        label.style.color  = '#888';
        consec.innerHTML   = '<i class="bi bi-x-circle me-1"></i>' + permiso.consecuencia;
        consec.style.color = '#ef4444';
    }
}

function seleccionarTodosPermisos(estado) {
    document.querySelectorAll('.perm-inline-check').forEach(cb => {
        cb.checked = estado;
        actualizarFilaPermiso(cb);
    });
}

function togglePassVis() {
    const inp  = document.getElementById('uPassword');
    const icon = document.getElementById('eyeIcon');
    if (!inp) return;
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
}

function generarPassword() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
    let pass = '';
    for (let i = 0; i < 12; i++) pass += chars[Math.floor(Math.random() * chars.length)];
    const inp  = document.getElementById('uPassword');
    if (inp) { inp.type = 'text'; inp.value = pass; }
    const icon = document.getElementById('eyeIcon');
    if (icon) icon.className = 'bi bi-eye-slash';
    // Copiar al portapapeles
    navigator.clipboard?.writeText(pass).then(() => toast('Contraseña generada y copiada al portapapeles', 'info'));
}

function abrirModalCrearUsuario() {
    ['uEditId','uNombre','uUsername','uEmail','uPassword'].forEach(id => document.getElementById(id).value='');
    document.getElementById('uRol').value = 'alumno';
    document.getElementById('uActivo').checked = true;
    document.getElementById('uEsJunior').checked = false;
    document.getElementById('uPasswordHint').textContent = '';
    document.getElementById('uPasswordLabel').textContent = 'Contraseña nueva';
    document.getElementById('uPasswordRequired').style.display = 'inline';
    document.getElementById('uPasswordBox').style.borderColor = 'rgba(102,126,234,.2)';
    document.getElementById('uPasswordStrength').style.width = '0';
    document.getElementById('uPassword').type = 'password';
    document.getElementById('eyeIcon').className = 'bi bi-eye';
    document.getElementById('uError').classList.add('d-none');
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-person-plus me-2"></i>Crear Nuevo Usuario';
    document.getElementById('juniorSection').style.display = 'none';
    document.getElementById('permisosInlineSection').style.display = 'none';
    const rolSelect = document.getElementById('uRol');
    if (rolSelect && IS_ADMIN_JUNIOR) {
        const crearAlumno  = hasPermission('crear_alumnos');
        const crearMaestro = hasPermission('crear_maestros');
        for (const opt of Array.from(rolSelect.options)) {
            opt.disabled = opt.value === 'alumno' ? !crearAlumno
                : opt.value === 'maestro' ? !crearMaestro
                : true;
        }
        if (!crearAlumno) rolSelect.value = crearMaestro ? 'maestro' : 'admin';
    } else if (rolSelect) {
        for (const opt of Array.from(rolSelect.options)) opt.disabled = false;
    }
    // Mostrar campo de contraseña obligatorio
    document.getElementById('uPassword').placeholder = 'Mínimo 6 caracteres (obligatorio)';
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}
function abrirEditarUsuario(id,username,nombre,email,rol,activo) {
    document.getElementById('uEditId').value=id; document.getElementById('uNombre').value=nombre;
    document.getElementById('uUsername').value=username; document.getElementById('uEmail').value=email;
    document.getElementById('uRol').value=rol; document.getElementById('uActivo').checked=activo=='1';
    document.getElementById('uPassword').value='';
    document.getElementById('uPasswordHint').textContent = '⚠ Dejar vacío para conservar la contraseña actual';
    document.getElementById('uPasswordLabel').textContent = 'Nueva contraseña (opcional)';
    document.getElementById('uPasswordRequired').style.display = 'none';
    document.getElementById('uPasswordBox').style.borderColor = 'rgba(245,158,11,.3)';
    document.getElementById('uPassword').placeholder = 'Escribe aquí para cambiar la contraseña';
    document.getElementById('uPassword').type = 'password';
    document.getElementById('eyeIcon').className = 'bi bi-eye';
    document.getElementById('uPasswordStrength').style.width = '0';
    document.getElementById('uError').classList.add('d-none');
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar: ' + esc(username);
    document.getElementById('juniorSection').style.display=rol==='admin'?'block':'none';
    document.getElementById('permisosInlineSection').style.display='none';
    document.getElementById('uEsJunior').checked=false;

    // Si es admin, cargar permisos actuales y mostrar la sección
    if (rol === 'admin') {
        api(`<?= BASE_URL ?>/api/admin?action=get_permisos&admin_id=${id}`)
            .then(r => {
                const permisos = r.permisos || [];
                const esJunior = permisos.length > 0;
                document.getElementById('uEsJunior').checked = esJunior;
                if (esJunior) {
                    document.getElementById('permisosInlineSection').style.display = 'block';
                    renderPermisosInline(permisos);
                }
            })
            .catch(() => {});
    }

    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}
function _pwStrength(pw) {
    if (!pw) return 0;
    let score = 0;
    if (pw.length >= 6)  score++;
    if (pw.length >= 10) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    return score; // 0-5
}
document.addEventListener('input', e => {
    if (e.target.id !== 'uPassword') return;
    const bar = document.getElementById('uPasswordStrength');
    if (!bar) return;
    const s = _pwStrength(e.target.value);
    const colors = ['#2a2a4a','#ef4444','#f59e0b','#f59e0b','#10b981','#10b981'];
    bar.style.width   = (s * 20) + '%';
    bar.style.background = colors[s];
});

async function guardarUsuario() {
    const errEl=document.getElementById('uError'); errEl.classList.add('d-none');
    const id=document.getElementById('uEditId').value;
    const esJunior=document.getElementById('uEsJunior').checked?1:0;
    const pw = document.getElementById('uPassword').value;
    const body={id:id||null,nombre:document.getElementById('uNombre').value.trim(),username:document.getElementById('uUsername').value.trim(),email:document.getElementById('uEmail').value.trim(),password:pw,rol:document.getElementById('uRol').value,activo:document.getElementById('uActivo').checked?1:0,es_admin_junior:esJunior};
    if(id && !pw) delete body.password;
    if(!body.nombre||!body.username||!body.email){errEl.textContent='Nombre, usuario y email son obligatorios';errEl.classList.remove('d-none');return;}
    if(!id&&pw.length<6){errEl.textContent='La contraseña debe tener al menos 6 caracteres';errEl.classList.remove('d-none');return;}
    if(id&&pw&&pw.length<6){errEl.textContent='Si cambias la contraseña debe tener al menos 6 caracteres';errEl.classList.remove('d-none');return;}
    try {
        const r=await api(`<?= BASE_URL ?>/api/admin?action=${id?'editar_usuario':'crear_usuario'}`,body);
        if(r.success){
            // Si es admin junior, guardar permisos inline también
            if(body.rol==='admin' && esJunior) {
                const permisos=[...document.querySelectorAll('.perm-inline-check:checked')].map(el=>el.dataset.key);
                const userId = id || r.id;
                if(userId) {
                    await api('<?= BASE_URL ?>/api/admin?action=set_permisos',{admin_id:userId,permisos}).catch(()=>{});
                }
            }
            toast(id?'Usuario actualizado':'Usuario creado','ok');
            bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
            renderUsuarios();
        } else{errEl.textContent=r.error||'Error';errEl.classList.remove('d-none');}
    } catch(e){errEl.textContent=e.message;errEl.classList.remove('d-none');}
}
async function abrirPermisos(adminId,username) {
    document.getElementById('permisosAdminId').value=adminId;
    document.getElementById('permisosUsername').textContent=username;
    try {
        const r=await api(`<?= BASE_URL ?>/api/admin?action=get_permisos&admin_id=${adminId}`);
        const activos=new Set(r.permisos||[]);
        document.getElementById('permisosCheckboxes').innerHTML=PERMISOS_DISPONIBLES.map(p=>`<div class="col-12"><label class="d-flex align-items-center gap-2 py-1" style="cursor:pointer"><input type="checkbox" class="perm-check" data-key="${p.key}" ${activos.has(p.key)?'checked':''} style="width:16px;height:16px"><i class="bi ${p.icon} text-muted" style="font-size:.9rem"></i><span class="small text-light">${p.label}</span></label></div>`).join('');
        new bootstrap.Modal(document.getElementById('modalPermisos')).show();
    } catch(e){toast(e.message,'err');}
}
async function guardarPermisos() {
    const adminId=document.getElementById('permisosAdminId').value;
    const permisos=[...document.querySelectorAll('.perm-check:checked')].map(el=>el.dataset.key);
    try {
        const r=await api('<?= BASE_URL ?>/api/admin?action=set_permisos',{admin_id:adminId,permisos});
        if(r.success){toast('Permisos actualizados','ok');bootstrap.Modal.getInstance(document.getElementById('modalPermisos')).hide();}
        else throw new Error(r.error||'Error');
    } catch(e){toast(e.message,'err');}
}
async function toggleRol(id,rolActual) {
    const roles=['alumno','maestro','admin'];
    const nuevo=roles[(roles.indexOf(rolActual)+1)%roles.length];
    if(!confirm(`¿Cambiar rol a "${nuevo}"?`))return;
    try {
        const r=await api('<?= BASE_URL ?>/api/admin?action=set_rol',{id,rol:nuevo});
        if(r.success){toast('Rol actualizado','ok');renderUsuarios();}
        else throw new Error(r.error||'Error');
    } catch(e){toast(e.message,'err');}
}
async function toggleActivo(id,actual) {
    const nuevo=actual=='1'?0:1;
    if(!confirm(`¿${nuevo?'Activar':'Desactivar'} este usuario?`))return;
    try {
        const r=await api('<?= BASE_URL ?>/api/admin?action=set_activo',{id,activo:nuevo});
        if(r.success){toast('Estado actualizado','ok');renderUsuarios();}
        else throw new Error(r.error||'Error');
    } catch(e){toast(e.message,'err');}
}


// ════════════════════════════════════════════════════════════
// DIAGRAMAS
// ════════════════════════════════════════════════════════════
// _todosDiagramas: cache de diagramas para filtrado client-side
let _todosDiagramas = [];

async function renderDiagramas() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/admin?action=diagramas');
        _todosDiagramas = data.diagramas || [];
        window._diagAdminAll = _todosDiagramas;

        document.getElementById('contentArea').innerHTML = `
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-diagram-3 text-warning"></i>
                <h5>Diagramas — <span id="dContador">${_todosDiagramas.length}</span> resultados
                    <small class="text-muted ms-1" style="font-size:.72rem">(total BD: ${data.total||0})</small>
                </h5>
                <div class="ms-auto d-flex gap-2">
                    <button class="btn-admin-outline" onclick="renderDiagramas()"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
            <!-- Filtros -->
            <div class="card-body pb-2 pt-3" style="border-bottom:1px solid var(--bd-color)">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-flex align-items-center gap-1" style="flex:1;min-width:180px">
                        <i class="bi bi-search text-muted"></i>
                        <input type="text" id="dBusqueda" class="form-control-dark w-100"
                            placeholder="Buscar por título, usuario o nombre..."
                            style="font-size:.82rem" oninput="filtrarDiagramas()">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-funnel text-muted"></i>
                        <select id="dFiltroTipo" class="form-control-dark" style="font-size:.82rem" onchange="filtrarDiagramas()">
                            <option value="">Todos los tipos</option>
                            ${Object.entries(TIPOS).map(([v,t])=>`<option value="${v}">${t.label||t}</option>`).join('')}
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-file-earmark text-muted"></i>
                        <select id="dFiltroArchivo" class="form-control-dark" style="font-size:.82rem" onchange="filtrarDiagramas()">
                            <option value="">Todos los archivos</option>
                            <option value="ok">Archivo OK</option>
                            <option value="falta">Archivo faltante</option>
                        </select>
                    </div>
                    <button class="btn-admin-outline" style="font-size:.78rem;padding:6px 12px" onclick="limpiarFiltrosDiagramas()">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </button>
                </div>
            </div>
            <!-- Grid de cards -->
            <div class="card-body">
                <div id="dCardsGrid" class="row g-3">
                    ${_renderDiagCards(_todosDiagramas)}
                </div>
            </div>
        </div>`;

        // Activar previews UML en las cards
        if (window.DiagramMiniRenderer) {
            requestAnimationFrame(() => DiagramMiniRenderer.observeAll(document.getElementById('dCardsGrid')));
        }
    } catch(e) { toast(e.message,'err'); }
}

function _renderDiagCards(arr) {
    if (!arr.length) return `
        <div class="col-12 text-center py-5">
            <i class="bi bi-diagram-3" style="font-size:3rem;opacity:.2;display:block;margin-bottom:12px;color:var(--txt-muted)"></i>
            <p style="color:var(--txt-muted)">No hay diagramas con esos filtros</p>
        </div>`;

    return arr.map(d => {
        const tipoLabel = (TIPOS[d.tipo_diagrama]?.label || TIPOS[d.tipo_diagrama] || d.tipo_diagrama);
        const fecha = new Date(d.fecha_modificacion).toLocaleDateString('es-MX');
        const icon44 = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(d.tipo_diagrama, 44) : '';
        const icon11 = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(d.tipo_diagrama, 11) : '';
        const archOk = d.archivo_existe;
        return `
        <div class="col-sm-6 col-lg-4 col-xl-3" data-diag-admin-id="${d.id}">
            <div class="lc-card-admin">
                <!-- Preview -->
                <div class="lc-preview-admin" data-preview-id="${d.id}"
                     onclick="window.open('<?= BASE_URL ?>/editor?id=${d.id}','_blank')" title="Abrir en editor">
                    <div style="display:flex;align-items:center;justify-content:center;height:100%">${icon44}</div>
                </div>
                <!-- Body -->
                <div class="lc-body-admin">
                    <div class="lc-title-admin" title="${esc(d.titulo)}">${esc(d.titulo)}</div>
                    <div class="lc-meta-admin">
                        <span style="display:inline-flex;align-items:center;gap:2px">${icon11}&nbsp;${tipoLabel}</span>
                        &nbsp;·&nbsp;<span style="color:var(--primary);font-weight:600">${esc(d.username||'?')}</span>
                    </div>
                    <div class="lc-meta-admin" style="margin-top:2px">
                        v${d.version} · ${fecha}
                        ${archOk
                            ? `<span style="color:#10b981;margin-left:6px"><i class="bi bi-file-earmark-check"></i> OK</span>`
                            : `<span style="color:#ef4444;margin-left:6px"><i class="bi bi-file-earmark-x"></i> Falta</span>`}
                    </div>
                </div>
                <!-- Footer -->
                <div class="lc-footer-admin">
                    <a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank" class="lc-btn-open-admin">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <button class="lc-btn-danger-admin" title="Eliminar diagrama"
                        onclick="eliminarDiagrama(${d.id},'${esc(d.titulo)}')">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');
}

function filtrarDiagramas() {
    const busq    = (document.getElementById('dBusqueda')?.value      || '').toLowerCase().trim();
    const tipo    = (document.getElementById('dFiltroTipo')?.value    || '');
    const archivo = (document.getElementById('dFiltroArchivo')?.value || '');

    const filtrados = _todosDiagramas.filter(d => {
        const matchBusq    = !busq    || (d.titulo||'').toLowerCase().includes(busq) || (d.username||'').toLowerCase().includes(busq) || (d.nombre_completo||'').toLowerCase().includes(busq);
        const matchTipo    = !tipo    || d.tipo_diagrama === tipo;
        const matchArchivo = !archivo || (archivo === 'ok' ? d.archivo_existe : !d.archivo_existe);
        return matchBusq && matchTipo && matchArchivo;
    });

    const contador = document.getElementById('dContador');
    if (contador) contador.textContent = filtrados.length;

    const grid = document.getElementById('dCardsGrid');
    if (!grid) return;
    grid.innerHTML = _renderDiagCards(filtrados);
    if (window.DiagramMiniRenderer) {
        requestAnimationFrame(() => DiagramMiniRenderer.observeAll(grid));
    }
}

function limpiarFiltrosDiagramas() {
    ['dBusqueda','dFiltroTipo','dFiltroArchivo'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    filtrarDiagramas();
}

async function eliminarDiagrama(id, titulo) {
    if (!confirm(`¿Eliminar diagrama "${titulo}" (#${id})? Esta acción no se puede deshacer.`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=eliminar_diagrama', { id });
        if (r.success) { toast('Diagrama eliminado','ok'); renderDiagramas(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// BASE DE DATOS
// ════════════════════════════════════════════════════════════
function renderDB() {
    document.getElementById('contentArea').innerHTML = `
        <div class="row g-3">
            <!-- Estado conexión -->
            <div class="col-12">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-database text-primary"></i><h5>Estado de la Conexión</h5></div>
                    <div class="card-body">
                        <div id="dbStatusResult">
                            ${DB_OK
                                ? `<div class="db-status ok"><i class="bi bi-database-fill-check" style="font-size:2rem;color:#10b981"></i>
                                    <div><strong class="text-success">Conectado correctamente</strong>
                                    <div class="small text-muted mt-1">MySQL · diagramas_db · localhost</div></div></div>`
                                : `<div class="db-status err"><i class="bi bi-database-fill-x" style="font-size:2rem;color:#ef4444"></i>
                                    <div><strong class="text-danger">Error de conexión</strong>
                                    <div class="small text-danger mt-1">${DB_ERROR||'No se pudo conectar'}</div></div></div>`}
                        </div>
                        <button class="btn-admin mt-3" onclick="probarConexion()">
                            <i class="bi bi-plug me-1"></i> Probar Conexión Ahora
                        </button>
                    </div>
                </div>
            </div>

            <!-- Editar credenciales -->
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-gear text-warning"></i><h5>Configurar Conexión</h5></div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Edita <code>config/database.php</code> con tus credenciales de XAMPP/MySQL.</p>
                        <div class="mb-2">
                            <label class="small text-muted mb-1 d-block">Host</label>
                            <input type="text" id="cf_host" class="form-control-dark w-100" placeholder="localhost" value="localhost">
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted mb-1 d-block">Base de Datos</label>
                            <input type="text" id="cf_db" class="form-control-dark w-100" placeholder="diagramas_db" value="diagramas_db">
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted mb-1 d-block">Usuario</label>
                            <input type="text" id="cf_user" class="form-control-dark w-100" placeholder="root" value="root">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1 d-block">Contraseña</label>
                            <input type="password" id="cf_pass" class="form-control-dark w-100" placeholder="(vacía en XAMPP por defecto)">
                        </div>
                        <button class="btn-admin w-100" onclick="guardarConfigDB()">
                            <i class="bi bi-save me-1"></i> Guardar y Probar
                        </button>
                        <div id="cfResult" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Tablas existentes -->
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-table text-info"></i><h5>Tablas en la BD</h5></div>
                    <div class="card-body">
                        <div id="tablasResult"><div class="text-center"><div class="spinner-border spinner-border-sm text-primary"></div></div></div>
                        <button class="btn-admin-outline mt-3 w-100" onclick="cargarTablas()"><i class="bi bi-arrow-clockwise me-1"></i> Actualizar</button>
                    </div>
                </div>
            </div>
        </div>`;
    cargarTablas();
}

async function probarConexion() {
    const el = document.getElementById('dbStatusResult');
    el.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=test_conexion');
        if (r.success) {
            el.innerHTML = `<div class="db-status ok"><i class="bi bi-database-fill-check" style="font-size:2rem;color:#10b981"></i>
                <div><strong class="text-success">✓ Conectado</strong>
                <div class="small text-muted">${r.info||''}</div></div></div>`;
            toast('Conexión exitosa','ok');
        } else throw new Error(r.error);
    } catch(e) {
        el.innerHTML = `<div class="db-status err"><i class="bi bi-database-fill-x" style="font-size:2rem;color:#ef4444"></i>
            <div><strong class="text-danger">Sin conexión</strong>
            <div class="small text-danger">${esc(e.message)}</div></div></div>`;
        toast('Error: '+e.message,'err');
    }
}

async function guardarConfigDB() {
    const body = { host:document.getElementById('cf_host').value, db:document.getElementById('cf_db').value, user:document.getElementById('cf_user').value, pass:document.getElementById('cf_pass').value };
    const res  = document.getElementById('cfResult');
    res.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=guardar_config_db', body);
        if (r.success) { res.innerHTML = '<span class="status-ok small">✓ Guardado y conexión verificada</span>'; toast('Configuración guardada','ok'); }
        else { res.innerHTML = `<span class="status-err small">${esc(r.error||'Error')}</span>`; toast(r.error||'Error','err'); }
    } catch(e) { res.innerHTML = `<span class="status-err small">${esc(e.message)}</span>`; }
}

async function cargarTablas() {
    const el = document.getElementById('tablasResult');
    if (!el) return;
    el.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=tablas');
        if (r.tablas && r.tablas.length) {
            el.innerHTML = r.tablas.map(t => `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:#2a2a4a!important">
                    <span class="small text-light"><i class="bi bi-table me-2 text-primary"></i>${esc(t.nombre)}</span>
                    <span class="badge-tipo">${t.filas} filas</span>
                </div>`).join('');
        } else {
            el.innerHTML = '<p class="text-muted small">No hay tablas o no hay conexión.</p>';
        }
    } catch(e) { el.innerHTML = `<span class="status-err small">${esc(e.message)}</span>`; }
}

// ════════════════════════════════════════════════════════════
// CREDENCIALES DE EMERGENCIA (antes llamada Setup)
// Solo muestra la sección de credenciales de emergencia
// ════════════════════════════════════════════════════════════
function renderSetupEmergencia() {
    document.getElementById('contentArea').innerHTML = `
        <div class="row g-3">
            <div class="col-12">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-shield-lock text-warning"></i><h5>Credenciales de Acceso de Emergencia</h5></div>
                    <div class="card-body">
                        <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.82rem;color:#fcd34d">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>¿Para qué sirve?</strong> Si la base de datos queda inaccesible, el superadmin puede entrar al panel
                            usando estas credenciales alternativas almacenadas en <code>data/emergency.dat</code>, <strong>sin necesidad de BD</strong>.
                            La sesión de emergencia dura 30 min y solo permite reparar la configuración de BD.
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                            <div>
                                <label class="small text-muted d-block mb-1"><i class="bi bi-person-fill me-1"></i>Usuario de emergencia</label>
                                <input type="text" id="emergUsername" class="form-control-dark w-100" placeholder="superadmin_emerg" autocomplete="off">
                            </div>
                            <div>
                                <label class="small text-muted d-block mb-1">
                                    <i class="bi bi-key-fill me-1"></i>Contraseña <span style="color:#ef4444;font-size:.7rem">(mín. 10 chars, mayúscula, número, símbolo)</span>
                                </label>
                                <input type="password" id="emergPassword" class="form-control-dark w-100" placeholder="MiClave@2024!" autocomplete="new-password">
                            </div>
                            <div>
                                <label class="small text-muted d-block mb-1"><i class="bi bi-key-fill me-1"></i>Confirmar contraseña</label>
                                <input type="password" id="emergPasswordConfirm" class="form-control-dark w-100" placeholder="Repetir contraseña" autocomplete="new-password">
                            </div>
                            <div style="display:flex;align-items:flex-end">
                                <button class="btn-admin w-100" onclick="guardarEmergencia()" style="background:linear-gradient(135deg,#dc3545,#9b1a26)">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Guardar Credenciales
                                </button>
                            </div>
                        </div>
                        <div id="emergLog" class="log-output mt-3 d-none"></div>
                        <div style="margin-top:14px;padding:10px 14px;background:#0a0a12;border-radius:8px;font-size:.76rem;color:#666">
                            <i class="bi bi-shield-check me-1"></i>
                            Las credenciales se almacenan como hash Argon2 + salt + pepper en <code>data/emergency.dat</code>.
                            Nunca en texto plano ni en la base de datos. Máximo 5 intentos fallidos por IP cada 10 min.
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
}

// ════════════════════════════════════════════════════════════
// SETUP / INSTALACIÓN (mantenido internamente para compatibilidad)
// ════════════════════════════════════════════════════════════
function renderSetup() {
    document.getElementById('contentArea').innerHTML = `
        <div class="row g-3">
            <div class="col-12">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-database-fill-add text-success"></i><h5>Instalar / Crear Base de Datos</h5></div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Esto crea todas las tablas necesarias si no existen. Es seguro ejecutarlo más de una vez (usa <code>CREATE TABLE IF NOT EXISTS</code>). También inserta datos de prueba si la tabla está vacía.</p>
                        <button class="btn-admin" onclick="ejecutarSetup()">
                            <i class="bi bi-play-fill me-1"></i> Ejecutar Instalación
                        </button>
                        <div id="setupLog" class="log-output mt-3 d-none"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-folder-plus text-info"></i><h5>Crear Carpetas de Usuarios</h5></div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Crea <code>uploads/usuario_N/</code> para cada usuario activo que no tenga carpeta.</p>
                        <button class="btn-admin" onclick="crearCarpetas()">
                            <i class="bi bi-folder-plus me-1"></i> Crear Carpetas
                        </button>
                        <div id="carpetasLog" class="log-output mt-3 d-none"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header"><i class="bi bi-trash text-danger"></i><h5>Zona de Peligro</h5></div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">⚠️ Estas acciones son irreversibles.</p>
                        <button class="btn-danger-sm w-100 mb-2" onclick="limpiarHuerfanos()">
                            <i class="bi bi-file-earmark-x me-1"></i> Limpiar Referencias Huérfanas
                        </button>
                        <div id="huerfanosLog" class="log-output mt-2 d-none"></div>
                    </div>
                </div>
            </div>

            <!-- ── Acceso de Emergencia ─────────────────────────── -->
            <div class="col-12">
                <div class="section-card" style="border-color:rgba(220,53,69,.35)">
                    <div class="card-header" style="background:rgba(220,53,69,.15);border-color:rgba(220,53,69,.3)">
                        <i class="bi bi-shield-exclamation" style="color:#ff6b6b"></i>
                        <h5 style="color:#ff8a8a">Acceso de Emergencia (sin BD)</h5>
                    </div>
                    <div class="card-body">
                        <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.82rem;color:#fcd34d">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>¿Para qué sirve?</strong> Si la base de datos queda inaccesible (servidor MySQL caído, credenciales cambiadas, etc.),
                            el superadmin puede entrar al panel usando estas credenciales alternativas almacenadas en un archivo local seguro (<code>data/emergency.dat</code>),
                            <strong>sin necesidad de BD</strong>. La sesión de emergencia dura 30 min y solo permite reparar la configuración de BD.
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                            <div>
                                <label class="small text-muted d-block mb-1">
                                    <i class="bi bi-person-fill me-1"></i>Usuario de emergencia
                                </label>
                                <input type="text" id="emergUsername" class="form-control-dark w-100" placeholder="superadmin_emerg" autocomplete="off">
                            </div>
                            <div>
                                <label class="small text-muted d-block mb-1">
                                    <i class="bi bi-key-fill me-1"></i>Contraseña de emergencia
                                    <span style="color:#ef4444;font-size:.7rem"> (mín. 10 chars, mayúscula, número, símbolo)</span>
                                </label>
                                <input type="password" id="emergPassword" class="form-control-dark w-100" placeholder="MiClave@2024!" autocomplete="new-password">
                            </div>
                            <div>
                                <label class="small text-muted d-block mb-1">
                                    <i class="bi bi-key-fill me-1"></i>Confirmar contraseña
                                </label>
                                <input type="password" id="emergPasswordConfirm" class="form-control-dark w-100" placeholder="Repetir contraseña" autocomplete="new-password">
                            </div>
                            <div style="display:flex;align-items:flex-end">
                                <button class="btn-admin w-100" onclick="guardarEmergencia()" style="background:linear-gradient(135deg,#dc3545,#9b1a26)">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Guardar Credenciales de Emergencia
                                </button>
                            </div>
                        </div>

                        <div id="emergLog" class="log-output mt-3 d-none"></div>

                        <div style="margin-top:14px;padding:10px 14px;background:#0a0a12;border-radius:8px;font-size:.76rem;color:#666">
                            <i class="bi bi-shield-check me-1"></i>
                            Las credenciales se almacenan como hash Argon2 + salt + pepper en <code>data/emergency.dat</code> (fuera del webroot, permisos 600).
                            Nunca se guardan en la base de datos ni en texto plano.
                            El acceso con error registra la IP en <code>data/emergency_log.txt</code>.
                            Máximo 5 intentos fallidos por IP cada 10 minutos.
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
}

async function guardarEmergencia() {
    const log  = document.getElementById('emergLog');
    const user = document.getElementById('emergUsername').value.trim();
    const pass = document.getElementById('emergPassword').value;
    const conf = document.getElementById('emergPasswordConfirm').value;

    log.classList.remove('d-none');
    log.innerHTML = '';

    if (!user || !pass) { log.innerHTML = '<span class="log-err">✗ Completa usuario y contraseña.</span>'; return; }
    if (pass !== conf)  { log.innerHTML = '<span class="log-err">✗ Las contraseñas no coinciden.</span>'; return; }
    if (pass.length < 10) { log.innerHTML = '<span class="log-err">✗ La contraseña debe tener al menos 10 caracteres.</span>'; return; }
    if (!/[A-Z]/.test(pass) || !/[0-9]/.test(pass) || !/[^A-Za-z0-9]/.test(pass)) {
        log.innerHTML = '<span class="log-err">✗ La contraseña debe incluir mayúscula, número y símbolo (ej: @#!$).</span>'; return;
    }

    log.innerHTML = '<span class="log-info">Generando hash seguro...</span>\n';
    try {
        const r = await api('<?= BASE_URL ?>/api/setup-emergency', { username: user, password: pass });
        if (r.success) {
            log.innerHTML += '<span class="log-ok">✓ ' + esc(r.message || 'Credenciales guardadas correctamente.') + '</span>\n';
            log.innerHTML += '<span class="log-info">ℹ Las credenciales anteriores (si las había) fueron reemplazadas.</span>\n';
            document.getElementById('emergUsername').value = '';
            document.getElementById('emergPassword').value = '';
            document.getElementById('emergPasswordConfirm').value = '';
            toast('Credenciales de emergencia configuradas', 'ok');
        } else {
            log.innerHTML += '<span class="log-err">✗ ' + esc(r.error || 'Error desconocido') + '</span>\n';
        }
    } catch(e) { log.innerHTML += '<span class="log-err">✗ ' + esc(e.message) + '</span>\n'; }
}

async function ejecutarSetup() {
    const log = document.getElementById('setupLog');
    log.classList.remove('d-none');
    log.innerHTML = '<span class="log-info">Ejecutando instalación...</span>\n';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=setup_db');
        (r.log||[]).forEach(l => {
            const cls = l.startsWith('✓')?'log-ok':l.startsWith('⚠')?'log-warn':l.startsWith('✗')?'log-err':'log-info';
            log.innerHTML += `<span class="${cls}">${esc(l)}</span>\n`;
        });
        if (r.success) { toast('Instalación completada','ok'); cargarTablas(); }
        else toast(r.error||'Error','err');
    } catch(e) { log.innerHTML += `<span class="log-err">${esc(e.message)}</span>\n`; toast(e.message,'err'); }
}

async function crearCarpetas() {
    const log = document.getElementById('carpetasLog');
    log.classList.remove('d-none');
    log.innerHTML = '<span class="log-info">Creando carpetas...</span>\n';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=crear_carpetas');
        log.innerHTML += `<span class="log-ok">✓ ${r.total} usuarios revisados · ${r.creadas} carpetas creadas</span>\n`;
        toast(`${r.creadas} carpetas creadas`,'ok');
    } catch(e) { log.innerHTML += `<span class="log-err">${esc(e.message)}</span>\n`; toast(e.message,'err'); }
}

async function limpiarHuerfanos() {
    if (!confirm('¿Limpiar referencias huérfanas? Los diagramas sin archivo quedarán vacíos en la BD.')) return;
    const log = document.getElementById('huerfanosLog');
    log.classList.remove('d-none');
    log.innerHTML = '<span class="log-info">Analizando...</span>\n';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=limpiar_huerfanos');
        log.innerHTML += `<span class="log-ok">✓ ${r.revisados} revisados · ${r.limpiados} limpiados</span>\n`;
        toast(`${r.limpiados} referencias limpiadas`,'ok');
    } catch(e) { log.innerHTML += `<span class="log-err">${esc(e.message)}</span>\n`; toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// MANTENIMIENTO
// ════════════════════════════════════════════════════════════
async function renderMantenimiento() {
    loading();
    try {
        const [rInfo, rDisk] = await Promise.all([
            api('<?= BASE_URL ?>/api/admin?action=mantenimiento_info'),
            api('<?= BASE_URL ?>/api/admin?action=disk_usage').catch(() => null)
        ]);
        const r = rInfo;

        // ── Formateo de bytes ──
        const fmtB = b => {
            if (!b) return '0 B';
            const u = ['B','KB','MB','GB'];
            let i = 0; while (b >= 1024 && i < 3) { b /= 1024; i++; }
            return b.toFixed(i ? 1 : 0) + ' ' + u[i];
        };
        const rolColor = {admin:'#ff6b6b', maestro:'#60a5fa', alumno:'#6ee7b7'};

        // ── Progreso de disco ──
        const diskPct = rDisk && rDisk.bytes_disco
            ? Math.min(100, Math.round((rDisk.bytes_disco - rDisk.bytes_libres) / rDisk.bytes_disco * 100))
            : null;

        document.getElementById('contentArea').innerHTML = `

        <!-- ══ Stats resumen ══════════════════════════════════════ -->
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon"><i class="bi bi-folder2-open" style="color:#f59e0b;font-size:1.5rem"></i></div>
                    <div class="stat-num">${r.carpetas_usuario||0}</div>
                    <div class="stat-label">Carpetas de usuario</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon"><i class="bi bi-file-earmark-code" style="color:#60a5fa;font-size:1.5rem"></i></div>
                    <div class="stat-num">${r.archivos_json||0}</div>
                    <div class="stat-label">Diagramas en disco</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon"><i class="bi bi-exclamation-triangle" style="color:${(r.huerfanos||0)>0?'#f59e0b':'#10b981'};font-size:1.5rem"></i></div>
                    <div class="stat-num" style="color:${(r.huerfanos||0)>0?'#f59e0b':'#10b981'}">${r.huerfanos||0}</div>
                    <div class="stat-label">Refs. huérfanas</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon"><i class="bi bi-hdd" style="color:#a78bfa;font-size:1.5rem"></i></div>
                    <div class="stat-num">${rDisk ? fmtB(rDisk.bytes_uploads) : '—'}</div>
                    <div class="stat-label">Tamaño uploads</div>
                </div>
            </div>
        </div>

        <!-- ══ Herramientas de mantenimiento ══════════════════════ -->
        <div class="section-card mb-3">
            <div class="card-header">
                <i class="bi bi-tools" style="color:var(--primary)"></i>
                <h5>Herramientas de Mantenimiento</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Limpiar huérfanos -->
                    <div class="col-md-6">
                        <div style="background:#0d0d1a;border:1px solid ${(r.huerfanos||0)>0?'rgba(245,158,11,.4)':'#2a2a4a'};border-radius:10px;padding:16px">
                            <div style="display:flex;align-items:flex-start;gap:12px">
                                <div style="background:rgba(245,158,11,.12);border-radius:10px;padding:10px;flex-shrink:0">
                                    <i class="bi bi-link-45deg" style="font-size:1.4rem;color:#f59e0b"></i>
                                </div>
                                <div style="flex:1">
                                    <div style="color:#fff;font-weight:600;font-size:.88rem;margin-bottom:4px">Limpiar referencias huérfanas</div>
                                    <div style="color:#666;font-size:.75rem;margin-bottom:12px">
                                        Elimina de la BD los registros de diagramas cuyo archivo JSON ya no existe en disco.
                                        ${(r.huerfanos||0)>0 ? `<span style="color:#f59e0b"> ${r.huerfanos} detectadas.</span>` : '<span style="color:#10b981"> Todo OK.</span>'}
                                    </div>
                                    <button class="btn-admin-outline" style="font-size:.78rem" onclick="mant_limpiarHuerfanos()">
                                        <i class="bi bi-broom me-1"></i>Ejecutar limpieza
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Optimizar BD -->
                    <div class="col-md-6">
                        <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;padding:16px">
                            <div style="display:flex;align-items:flex-start;gap:12px">
                                <div style="background:rgba(102,126,234,.12);border-radius:10px;padding:10px;flex-shrink:0">
                                    <i class="bi bi-database-gear" style="font-size:1.4rem;color:#667eea"></i>
                                </div>
                                <div style="flex:1">
                                    <div style="color:#fff;font-weight:600;font-size:.88rem;margin-bottom:4px">Optimizar base de datos</div>
                                    <div style="color:#666;font-size:.75rem;margin-bottom:12px">
                                        Ejecuta OPTIMIZE TABLE en todas las tablas. Recupera espacio y mejora el rendimiento de consultas.
                                    </div>
                                    <button class="btn-admin-outline" style="font-size:.78rem" onclick="mant_optimizarBD()">
                                        <i class="bi bi-lightning-charge me-1"></i>Optimizar ahora
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carpetas huérfanas -->
                    <div class="col-md-6">
                        <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;padding:16px">
                            <div style="display:flex;align-items:flex-start;gap:12px">
                                <div style="background:rgba(239,68,68,.1);border-radius:10px;padding:10px;flex-shrink:0">
                                    <i class="bi bi-folder-x" style="font-size:1.4rem;color:#ef4444"></i>
                                </div>
                                <div style="flex:1">
                                    <div style="color:#fff;font-weight:600;font-size:.88rem;margin-bottom:4px">Eliminar carpetas huérfanas</div>
                                    <div style="color:#666;font-size:.75rem;margin-bottom:12px">
                                        Elimina carpetas <code style="color:var(--primary,#aab8ff)">uploads/usuario_N/</code> cuyos usuarios ya no existen en la BD.
                                    </div>
                                    <button class="btn-admin-outline" style="font-size:.78rem;border-color:rgba(239,68,68,.5);color:#fca5a5"
                                        onclick="mant_vaciarCarpetasHuerfanas()">
                                        <i class="bi bi-trash3 me-1"></i>Eliminar carpetas sin usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uso de disco -->
                    <div class="col-md-6">
                        <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;padding:16px">
                            <div style="display:flex;align-items:flex-start;gap:12px">
                                <div style="background:rgba(167,139,250,.12);border-radius:10px;padding:10px;flex-shrink:0">
                                    <i class="bi bi-pie-chart" style="font-size:1.4rem;color:#a78bfa"></i>
                                </div>
                                <div style="flex:1">
                                    <div style="color:#fff;font-weight:600;font-size:.88rem;margin-bottom:4px">Espacio en disco</div>
                                    ${rDisk && rDisk.bytes_disco ? `
                                    <div style="margin-bottom:8px">
                                        <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#888;margin-bottom:4px">
                                            <span>Usado: ${fmtB(rDisk.bytes_disco - rDisk.bytes_libres)}</span>
                                            <span>Libre: ${fmtB(rDisk.bytes_libres)}</span>
                                        </div>
                                        <div style="background:#2a2a4a;border-radius:4px;height:6px;overflow:hidden">
                                            <div style="width:${diskPct}%;background:${diskPct>85?'#ef4444':diskPct>60?'#f59e0b':'#10b981'};height:100%;transition:width .5s"></div>
                                        </div>
                                        <div style="font-size:.7rem;color:#666;margin-top:4px">${diskPct}% usado de ${fmtB(rDisk.bytes_disco)}</div>
                                    </div>` : '<div style="color:#666;font-size:.75rem;margin-bottom:12px">Info no disponible</div>'}
                                    <div style="font-size:.72rem;color:#888">Uploads del sistema: <span style="color:#a78bfa">${rDisk ? fmtB(rDisk.bytes_uploads) : '—'}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ══ Log de accesos de emergencia ════════════════════════ -->
        <div class="section-card mb-3">
            <div class="card-header">
                <i class="bi bi-journal-text" style="color:#f59e0b"></i>
                <h5>Log de Accesos de Emergencia</h5>
                <button class="ms-auto btn-admin-outline" style="font-size:.75rem" onclick="mant_verLog()">
                    <i class="bi bi-eye me-1"></i>Ver log
                </button>
            </div>
            <div id="emergencyLogContainer" style="display:none" class="card-body">
                <pre id="emergencyLogContent" style="color:var(--primary,#aab8ff);font-size:.72rem;max-height:200px;overflow-y:auto;margin:0;background:#080810;padding:10px;border-radius:6px">Cargando…</pre>
            </div>
        </div>

        <!-- ══ Carpetas de usuarios ═════════════════════════════════ -->
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-folder2-open" style="color:#60a5fa"></i>
                <h5>Carpetas de Usuarios</h5>
                <span class="ms-auto d-flex align-items-center gap-2">
                    <span class="text-muted small">${r.archivos_json||0} diagramas · ${r.carpetas_usuario||0} carpetas</span>
                </span>
            </div>
            <div class="card-body py-2">
            ${(r.estructura||[]).length === 0
                ? `<p class="text-muted small py-2">No hay carpetas de usuario en disco.</p>`
                : (r.estructura||[]).map(e => `
                <div style="border:1px solid #2a2a4a;border-radius:8px;margin-bottom:8px;overflow:hidden">
                    <div class="folder-row" style="padding:10px 14px;display:flex;align-items:center;gap:12px;background:#0d0d1a;"
                         onclick="toggleCarpeta(${e.id})">
                        <i class="bi bi-chevron-right" id="chevron_${e.id}" style="color:#667eea;font-size:.8rem;transition:transform .2s;flex-shrink:0"></i>
                        <i class="bi bi-folder-fill" style="color:#f59e0b;font-size:1.1rem;flex-shrink:0"></i>
                        <div style="flex:1;min-width:0">
                            <div class="fr-title" style="color:#fff;font-size:.85rem;font-weight:600;display:flex;align-items:center;flex-wrap:wrap;gap:6px;transition:color .18s">
                                usuario_${e.id}
                                ${e.username ? `<span style="color:#888;font-weight:400;font-size:.78rem">${esc(e.username)}</span>` : ''}
                                ${e.nombre   ? `<span style="color:#666;font-size:.73rem">(${esc(e.nombre)})</span>` : ''}
                                ${e.rol      ? `<span style="color:${rolColor[e.rol]||'#ccc'};font-size:.65rem;border:1px solid currentColor;border-radius:10px;padding:1px 7px">${e.rol}</span>` : '<span style="color:#555;font-size:.65rem;border:1px solid #555;border-radius:10px;padding:1px 7px">sin usuario</span>'}
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;margin-left:8px">
                            <div style="color:var(--primary,#aab8ff);font-size:.82rem;font-weight:500">${e.archivos} archivos</div>
                            <div style="color:#555;font-size:.72rem">${e.tamano>0?fmtB(e.tamano):'vacío'}</div>
                        </div>
                    </div>
                    <div id="carpeta_${e.id}" style="display:none;background:#080810;border-top:1px solid #1e1e3a;padding:10px 14px">
                        <div id="archivos_${e.id}" style="font-size:.78rem;color:#888">
                            <span style="opacity:.6"><i class="bi bi-hourglass-split me-1"></i>Cargando archivos…</span>
                        </div>
                    </div>
                </div>`).join('')}
            </div>
        </div>`;
        filtrarDiagramas();
        // Note: filtrarDiagramas() above is intentional — filters the diagrams table
        // if it happens to be active. Safe no-op if diagrams section is not shown.

    } catch(e) { toast(e.message,'err'); }
}

// ── Acciones de mantenimiento ──────────────────────────────
async function mant_limpiarHuerfanos() {
    if (!confirm('¿Limpiar referencias huérfanas de la base de datos?\nEsto elimina registros de diagramas cuyo archivo ya no existe en disco.')) return;
    toast('Limpiando referencias huérfanas…','info');
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=limpiar_huerfanos');
        if (r.success) {
            toast(`Limpieza completa: ${r.limpiados} eliminados de ${r.revisados} revisados`, 'ok');
            renderMantenimiento();
        } else toast(r.error || 'Error','err');
    } catch(e) { toast(e.message,'err'); }
}

async function mant_optimizarBD() {
    toast('Optimizando tablas…','info');
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=optimizar_bd');
        const ok = (r.tablas||[]).filter(t=>t.ok).length;
        toast(`BD optimizada: ${ok}/${r.total} tablas OK`, 'ok');
    } catch(e) { toast(e.message,'err'); }
}

async function mant_vaciarCarpetasHuerfanas() {
    if (!confirm('¿Eliminar carpetas de usuarios que ya NO existen en la base de datos?\nEsta acción no se puede deshacer.')) return;
    toast('Buscando carpetas huérfanas…','info');
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=vaciar_carpetas_huerfanas');
        if (r.success) {
            if (r.total === 0) toast('No se encontraron carpetas huérfanas', 'ok');
            else toast(`${r.total} carpeta(s) eliminada(s): ${r.eliminadas.join(', ')}`, 'ok');
            renderMantenimiento();
        } else toast(r.error || 'Error','err');
    } catch(e) { toast(e.message,'err'); }
}

async function mant_verLog() {
    const cont = document.getElementById('emergencyLogContainer');
    const pre  = document.getElementById('emergencyLogContent');
    if (!cont || !pre) return;
    const visible = cont.style.display !== 'none';
    cont.style.display = visible ? 'none' : 'block';
    if (!visible) {
        try {
            const r = await fetch('<?= BASE_URL ?>/api/admin?action=ver_log_emergencia', { credentials:'same-origin' });
            const d = await r.json();
            pre.textContent = d.log || '(sin entradas)';
        } catch(e) { pre.textContent = 'Error al cargar log: ' + e.message; }
    }
}


// ════════════════════════════════════════════════════════════
// SVGs
// ════════════════════════════════════════════════════════════
async function renderSVGs() {
    // Verificar archivos del sistema además de SVGs
    loading();
    try {
        const rSvg = await api('<?= BASE_URL ?>/api/admin?action=check_svgs');
        const rMant = null; // carpetas ahora en sección Mantenimiento

        const svgGroups = rSvg.grupos || [];
        const totalSvg  = svgGroups.reduce((s,g)=>s+g.archivos.length,0);
        const okSvg     = svgGroups.reduce((s,g)=>s+g.archivos.filter(f=>f.existe).length,0);
        const missSvg   = totalSvg - okSvg;

        // ── Verificación de archivos clave del sistema ──────────────────
        const BASE = '<?= BASE_URL ?>';
        const archivosClaveCheck = await checkArchivosClave(BASE);

        // Checks de archivos del sistema (sin carpetas — ahora en Mantenimiento)
        const sysChecks = [];

        let html = `
        <!-- Stats resumen -->
        <div class="row g-3 mb-3">
            <div class="col-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">${missSvg===0?'✅':'⚠️'}</div>
                    <div class="stat-num" style="color:${missSvg===0?'#10b981':'#f59e0b'}">${okSvg}/${totalSvg}</div>
                    <div class="stat-label">SVGs de diagramas</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">${archivosClaveCheck.cssOk?'✅':'❌'}</div>
                    <div class="stat-num" style="color:${archivosClaveCheck.cssOk?'#10b981':'#ef4444'}">${archivosClaveCheck.cssOk?'OK':'Error'}</div>
                    <div class="stat-label">CSS del sistema</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">${archivosClaveCheck.jsOk?'✅':'❌'}</div>
                    <div class="stat-num" style="color:${archivosClaveCheck.jsOk?'#10b981':'#ef4444'}">${archivosClaveCheck.jsOk?'OK':'Error'}</div>
                    <div class="stat-label">JS del sistema</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card text-center">
                    <div class="stat-icon"><i class="bi bi-arrow-right-circle" style="color:#60a5fa;font-size:1.3rem"></i></div>
                    <div class="stat-num" style="color:#60a5fa;font-size:.85rem">Mant.</div>
                    <div class="stat-label">Ver carpetas →</div>
                </div>
            </div>
        </div>`;

        // ── Archivos clave del sistema ──────────────────────────────────
        html += `<div class="section-card mb-3">
            <div class="card-header"><i class="bi bi-hdd-rack text-primary"></i><h5>Archivos Clave del Sistema</h5></div>
            <div class="card-body py-1">
                ${archivosClaveCheck.archivos.map(a => `
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #1e1e3a">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi ${a.icon}" style="color:var(--primary);width:18px;text-align:center"></i>
                        <div>
                            <span class="small text-light">${esc(a.label)}</span>
                            <span class="text-muted" style="font-size:.7rem;margin-left:8px">${esc(a.ruta)}</span>
                        </div>
                    </div>
                    <span class="small ${a.ok?'status-ok':'status-err'}" style="white-space:nowrap">
                        ${a.ok ? `<i class="bi bi-check-circle me-1"></i>${a.size||'OK'}` : '<i class="bi bi-x-circle me-1"></i>FALTA'}
                    </span>
                </div>`).join('')}
                ${sysChecks.map(ch => `
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #1e1e3a">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi ${ch.icon}" style="color:var(--primary);width:18px;text-align:center"></i>
                        <span class="small text-light">${ch.label}</span>
                    </div>
                    <span class="small ${ch.ok?'status-ok':'status-warn'}">${ch.val}</span>
                </div>`).join('')}
`

        html += `</div></div>`;

        // ── SVGs por grupo ──────────────────────────────────────────────
        html += `<div class="section-card mb-3">
            <div class="card-header">
                <i class="bi bi-images text-warning"></i>
                <h5>SVGs de Elementos de Diagrama</h5>
                <span class="ms-auto d-flex gap-2">
                    <span class="badge-tipo">${okSvg}/${totalSvg} OK</span>
                    ${missSvg>0?`<button class="btn-admin-outline" style="font-size:.72rem;padding:3px 10px" onclick="generarTodosSVGs()"><i class="bi bi-magic me-1"></i>Reparar todos</button>`:''}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-2">`;

        svgGroups.forEach((g, gi) => {
            const allOk = g.archivos.every(f => f.existe);
            const miss  = g.archivos.filter(f=>!f.existe).length;
            const nombre = g.carpeta.replace('Diagramade','').replace('Diagramas','Diagrama').replace('Interaccion','Interacción');
            const ok  = g.archivos.filter(f=>f.existe).length;
            html += `<div class="col-12">
                <div style="background:#0d0d1a;border:1px solid ${allOk?'#2a2a4a':'rgba(245,158,11,.3)'};border-radius:8px;overflow:hidden">
                    <!-- Header row — clickable -->
                    <div style="padding:10px 14px;display:flex;align-items:center;gap:10px;cursor:pointer;transition:background .18s"
                         onclick="toggleSvgGroup(${gi})"
                         onmouseover="this.style.background='rgba(102,126,234,.12)'"
                         onmouseout="this.style.background='transparent'">
                        <i class="bi bi-chevron-right" id="svgChev_${gi}" style="color:#667eea;font-size:.78rem;transition:transform .2s"></i>
                        <i class="bi bi-${allOk?'check-circle-fill':'exclamation-triangle-fill'}" style="color:${allOk?'#10b981':'#f59e0b'};font-size:.95rem"></i>
                        <span style="font-size:.85rem;font-weight:600;color:#fff;flex:1">${esc(nombre)}</span>
                        <span style="font-size:.73rem;color:${allOk?'#10b981':'#f59e0b'}">${ok}/${g.archivos.length} SVGs</span>
                        ${!allOk?`<button class="btn-admin-outline" style="font-size:.68rem;padding:2px 8px" onclick="event.stopPropagation();generarSVGsFaltantes('${esc(g.carpeta)}')"><i class="bi bi-magic me-1"></i>Reparar</button>`:''}
                    </div>
                    <!-- Expandible: lista de archivos -->
                    <div id="svgGroup_${gi}" style="display:none;border-top:1px solid #1e1e3a;padding:10px 14px">
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                        ${g.archivos.map(a => `
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.72rem;
                                         background:${a.existe?'rgba(16,185,129,.1)':'rgba(239,68,68,.1)'};
                                         border:1px solid ${a.existe?'rgba(16,185,129,.3)':'rgba(239,68,68,.3)'};
                                         color:${a.existe?'#6ee7b7':'#fca5a5'}">
                                <i class="bi bi-${a.existe?'check-circle':'x-circle'}"></i>
                                ${esc(a.nombre.replace('.svg',''))}
                            </span>`).join('')}
                        </div>
                    </div>
                </div>
            </div>`;
        });

        html += `</div></div></div>`;

        // ── Carpetas de PROYECTOS ──────────────────────────────
        try {
            const rProy = await api('<?= BASE_URL ?>/api/admin?action=proyectos_info');
            if (rProy && rProy.proyectos) {
                html += `<div class="section-card mt-3">
                    <div class="card-header">
                        <i class="bi bi-diagram-3" style="color:var(--primary)"></i>
                        <h5>Carpetas de Proyectos (${rProy.proyectos.length})</h5>
                        <span class="ms-auto text-muted small">${rProy.total_archivos||0} archivos · ${rProy.total_diagramas||0} diagramas</span>
                    </div>
                    <div class="card-body py-2">
                    ${rProy.proyectos.length === 0
                        ? '<p class="text-muted small py-2">No hay proyectos creados.</p>'
                        : rProy.proyectos.map(p => `
                    <div style="border:1px solid #2a2a4a;border-radius:8px;margin-bottom:8px;overflow:hidden">
                        <div class="folder-row" style="padding:10px 14px;display:flex;align-items:center;gap:12px;background:#0d0d1a;"
                             onclick="toggleProyFolder('${p.id}')">
                            <i class="bi bi-chevron-right" id="pchev_${p.id}" style="color:#667eea;font-size:.8rem;transition:transform .2s;flex-shrink:0"></i>
                            <i class="bi bi-diagram-3" style="color:#667eea;font-size:1rem;flex-shrink:0"></i>
                            <div style="flex:1;min-width:0">
                                <div style="color:#fff;font-size:.85rem;font-weight:600">${esc(p.nombre)}</div>
                                <div style="color:#888;font-size:.72rem">
                                    Código: <code style="color:var(--primary,#aab8ff)">${esc(p.codigo)}</code>
                                    · ${p.num_miembros} miembros · Creado por ${esc(p.creador||'—')}
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0">
                                <div style="color:#6ee7b7;font-size:.78rem">${p.num_diagramas||0} diagramas</div>
                                <div style="color:#60a5fa;font-size:.72rem">${p.num_archivos||0} archivos</div>
                            </div>
                        </div>
                        <div id="proy_${p.id}" style="display:none;background:#080810;border-top:1px solid #1e1e3a;padding:10px 14px">
                            <div style="display:flex;gap:16px;flex-wrap:wrap">
                                <div style="flex:1;min-width:180px">
                                    <div style="font-size:.7rem;color:#6ee7b7;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px"><i class="bi bi-diagram-3 me-1"></i>Diagramas</div>
                                    ${(p.diagramas||[]).length === 0
                                        ? '<div style="color:#555;font-size:.75rem">Sin diagramas</div>'
                                        : (p.diagramas||[]).map(d=>`
                                    <div style="display:flex;align-items:center;gap:8px;padding:3px 0;border-bottom:1px solid #1a1a2e">
                                        <i class="bi bi-diagram-3" style="color:#6ee7b7;font-size:.78rem;flex-shrink:0"></i>
                                        <div style="flex:1;min-width:0"><div style="color:#ccc;font-size:.73rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(d.titulo||'Sin título')}</div><div style="color:#555;font-size:.66rem">por ${esc(d.autor||'—')}</div></div>
                                        <a href="<?= BASE_URL ?>/editor?id=${d.id}" target="_blank" style="color:#667eea;font-size:.7rem;text-decoration:none"><i class="bi bi-box-arrow-up-right"></i></a>
                                    </div>`).join('')}
                                </div>
                                <div style="flex:1;min-width:180px">
                                    <div style="font-size:.7rem;color:#60a5fa;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px"><i class="bi bi-folder2-open me-1"></i>Archivos</div>
                                    ${(p.archivos||[]).length === 0
                                        ? '<div style="color:#555;font-size:.75rem">Sin archivos</div>'
                                        : (p.archivos||[]).map(a=>`
                                    <div style="display:flex;align-items:center;gap:8px;padding:3px 0;border-bottom:1px solid #1a1a2e">
                                        <i class="bi bi-file-earmark" style="color:#60a5fa;font-size:.78rem;flex-shrink:0"></i>
                                        <div style="flex:1;min-width:0"><div style="color:#ccc;font-size:.73rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(a.nombre_original)}</div><div style="color:#555;font-size:.66rem">por ${esc(a.autor||'—')} · ${a.tamano>0?((a.tamano/1024).toFixed(1)+' KB'):'—'}</div></div>
                                        <a href="<?= BASE_URL ?>/api/proyectos/download?file_id=${a.id}" style="color:#667eea;font-size:.7rem;text-decoration:none"><i class="bi bi-download"></i></a>
                                    </div>`).join('')}
                                </div>
                            </div>
                        </div>
                    </div>`).join('')}
                    </div>
                </div>`;
                html += '';
            }
        } catch(_) {}

        // ── V46: Panel de Recuperación y Restauración ─────────────────────
        html += `
        <div class="section-card mb-3">
            <div class="card-header">
                <i class="bi bi-shield-check" style="color:#10b981"></i>
                <h5>Recuperación y Restauración</h5>
                <span class="ms-auto" style="font-size:.72rem;color:var(--txt-muted)">Herramientas de soporte para restaurar archivos críticos</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Restaurar SVGs UML -->
                    <div class="col-md-4">
                        <div style="background:var(--bg-deep);border:1.5px solid rgba(16,185,129,.25);border-radius:12px;padding:16px">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <div style="width:36px;height:36px;background:rgba(16,185,129,.15);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-images" style="color:#10b981;font-size:1rem"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:.84rem;color:var(--txt-main)">SVGs UML</div>
                                    <div style="font-size:.68rem;color:var(--txt-muted)">Iconos de tipos de diagrama</div>
                                </div>
                            </div>
                            <div style="font-size:.73rem;color:var(--txt-muted);margin-bottom:10px">
                                Regenera o restaura los SVG de todos los tipos UML si se dañan o eliminan.
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <button onclick="generarTodosSVGs()"
                                    style="flex:1;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);color:#10b981;border-radius:8px;padding:6px 10px;font-size:.73rem;cursor:pointer;font-weight:600">
                                    <i class="bi bi-magic me-1"></i>Regenerar todos
                                </button>
                                <button onclick="verificarSVGsUML()"
                                    style="background:rgba(96,165,250,.1);border:1px solid rgba(96,165,250,.25);color:#60a5fa;border-radius:8px;padding:6px 10px;font-size:.73rem;cursor:pointer">
                                    <i class="bi bi-search me-1"></i>Verificar
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Restaurar CSS/JS del sistema -->
                    <div class="col-md-4">
                        <div style="background:var(--bg-deep);border:1.5px solid rgba(102,126,234,.25);border-radius:12px;padding:16px">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <div style="width:36px;height:36px;background:rgba(102,126,234,.15);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-filetype-css" style="color:var(--primary);font-size:1rem"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:.84rem;color:var(--txt-main)">CSS / JS del Sistema</div>
                                    <div style="font-size:.68rem;color:var(--txt-muted)">Archivos de estilos y scripts</div>
                                </div>
                            </div>
                            <div style="font-size:.73rem;color:var(--txt-muted);margin-bottom:10px">
                                Verifica integridad de CSS y JS críticos. Si hay errores visuales o JS roto, usa esta opción.
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <button onclick="renderSVGs()"
                                    style="flex:1;background:rgba(102,126,234,.12);border:1px solid rgba(102,126,234,.3);color:var(--primary);border-radius:8px;padding:6px 10px;font-size:.73rem;cursor:pointer;font-weight:600">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Re-verificar
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Limpiar caché / datos de sesión -->
                    <div class="col-md-4">
                        <div style="background:var(--bg-deep);border:1.5px solid rgba(245,158,11,.25);border-radius:12px;padding:16px">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <div style="width:36px;height:36px;background:rgba(245,158,11,.15);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-database-gear" style="color:#f59e0b;font-size:1rem"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:.84rem;color:var(--txt-main)">Diagnóstico BD</div>
                                    <div style="font-size:.68rem;color:var(--txt-muted)">Estado de la base de datos</div>
                                </div>
                            </div>
                            <div style="font-size:.73rem;color:var(--txt-muted);margin-bottom:10px">
                                Revisa conexión y estado de la base de datos. Si hay errores de acceso, usa el diagnóstico.
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <button onclick="showSection('db')"
                                    style="flex:1;background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.3);color:#f59e0b;border-radius:8px;padding:6px 10px;font-size:.73rem;cursor:pointer;font-weight:600">
                                    <i class="bi bi-database me-1"></i>Ver diagnóstico
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Info sobre restauración de SQL -->
                    <div class="col-12">
                        <div style="background:rgba(239,68,68,.06);border:1.5px solid rgba(239,68,68,.2);border-radius:12px;padding:14px 16px;display:flex;align-items:flex-start;gap:12px">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b;font-size:1.1rem;flex-shrink:0;margin-top:1px"></i>
                            <div>
                                <div style="font-weight:700;font-size:.82rem;color:var(--txt-main);margin-bottom:4px">Restauración completa del sistema</div>
                                <div style="font-size:.73rem;color:var(--txt-muted);line-height:1.5">
                                    Para restaurar la base de datos completa, usa el archivo SQL en
                                    <code style="background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px">basededatos+info/Base/diagramas_MASTER_v33.sql</code>.
                                    Ve a <strong style="color:var(--primary)">Gestión BD</strong> para importarlo desde la interfaz,
                                    o importa directamente desde phpMyAdmin. Los archivos de usuario en
                                    <code style="background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px">public/proyectos/</code> se conservan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        document.getElementById('contentArea').innerHTML = html;
    } catch(e) { toast(e.message,'err'); }
}

function toggleProyFolder(pid) {
    const body = document.getElementById('proy_'+pid);
    const chev = document.getElementById('pchev_'+pid);
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : 'block';
    if (chev) chev.style.transform = open ? '' : 'rotate(90deg)';
}

/** Verifica accesibilidad HTTP de archivos clave del sistema */
async function checkArchivosClave(base) {
    const archivos = [
        { label:'Hoja de estilos principal',  ruta:'public/assets/css/style.css',     icon:'bi-filetype-css' },
        { label:'Editor JS',                  ruta:'public/assets/js/editor.js',       icon:'bi-filetype-js'  },
        { label:'Theme JS',                   ruta:'public/assets/js/user-theme.js',   icon:'bi-filetype-js'  },
        { label:'Bootstrap CSS (local)',       ruta:'public/assets/vendor/bootstrap/css/bootstrap.min.css', icon:'bi-filetype-css' },
        { label:'Bootstrap JS (local)',        ruta:'public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js', icon:'bi-filetype-js' },
        { label:'Bootstrap Icons CSS (local)', ruta:'public/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css', icon:'bi-filetype-css' },
    ];
    let cssOk = true, jsOk = true;
    const results = await Promise.all(archivos.map(async a => {
        try {
            const r = await fetch(base + '/' + a.ruta, { method:'HEAD' });
            const ok = r.ok;
            const size = r.headers.get('content-length');
            const sizeStr = size ? (parseInt(size)/1024).toFixed(1)+' KB' : 'OK';
            if (!ok && a.ruta.includes('.css')) cssOk = false;
            if (!ok && a.ruta.includes('.js'))  jsOk  = false;
            return { ...a, ok, size: ok ? sizeStr : null };
        } catch {
            if (a.ruta.includes('.css')) cssOk = false;
            if (a.ruta.includes('.js'))  jsOk  = false;
            return { ...a, ok: false, size: null };
        }
    }));
    return { archivos: results, cssOk, jsOk };
}

function verificarSVGsUML() {
    renderSVGs(); // Recarga la sección completa mostrando estado actualizado
    toast('Verificando SVGs del sistema...', 'info');
}

async function generarSVGsFaltantes(carpeta) {
    toast('Generando SVGs para '+carpeta+'...','info');
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=generar_svgs', { carpeta });
        toast(`${r.generados||0} SVGs generados`,'ok');
        renderSVGs();
    } catch(e) { toast(e.message,'err'); }
}

async function generarTodosSVGs() {
    toast('Reparando todos los SVGs faltantes...','info');
    try {
        const rCheck = await api('<?= BASE_URL ?>/api/admin?action=check_svgs');
        for (const g of (rCheck.grupos||[])) {
            if (!g.archivos.every(f=>f.existe)) {
                await api('<?= BASE_URL ?>/api/admin?action=generar_svgs', { carpeta: g.carpeta });
            }
        }
        toast('Reparación completa','ok');
        renderSVGs();
    } catch(e) { toast(e.message,'err'); }
}

/** Abre un modal para editar el código fuente de un SVG directamente */
async function editarCodigoSVG(ruta, nombre) {
    try {
        // Cargar el SVG vía HEAD+fetch
        const res = await fetch((window.BASE_URL||'') + '/' + ruta);
        const code = res.ok ? await res.text() : '';

        document.getElementById('_modalSVGEditor')?.remove();
        const m = document.createElement('div');
        m.id = '_modalSVGEditor'; m.className = 'modal fade'; m.tabIndex = -1;
        m.innerHTML = `<div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="background:#0a0a14;border:1px solid #2a2a4a;border-radius:14px">
                <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:14px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between">
                    <span style="color:#fff;font-weight:700"><i class="bi bi-filetype-svg me-2"></i>Editar SVG: ${esc(nombre)}</span>
                    <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
                </div>
                <div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <div style="font-size:.72rem;color:#667eea;font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em">Código SVG</div>
                        <textarea id="_svgCode" style="width:100%;height:380px;background:#060610;color:#a8d0ff;border:1px solid #2a2a4a;border-radius:8px;padding:10px;font-family:monospace;font-size:.78rem;resize:vertical;outline:none"
                            oninput="previewSVG()">${esc(code)}</textarea>
                    </div>
                    <div>
                        <div style="font-size:.72rem;color:#667eea;font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em">Vista previa</div>
                        <div id="_svgPreview" style="background:#fff;border-radius:8px;padding:16px;min-height:380px;display:flex;align-items:center;justify-content:center;overflow:auto">${code}</div>
                    </div>
                </div>
                <div style="padding:0 16px 16px;display:flex;justify-content:flex-end;gap:8px">
                    <button data-bs-dismiss="modal" style="background:#1a1a2e;border:1px solid #2a2a4a;color:#888;border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">Cancelar</button>
                    <button onclick="guardarCodigoSVG('${esc(ruta)}','${esc(nombre)}')"
                        style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                        <i class="bi bi-floppy me-1"></i>Guardar SVG
                    </button>
                </div>
            </div>
        </div>`;
        document.body.appendChild(m);
        const bsM = new bootstrap.Modal(m);
        m.addEventListener('hidden.bs.modal', () => m.remove());
        bsM.show();
    } catch(e) { toast(e.message,'err'); }
}

function previewSVG() {
    const code = document.getElementById('_svgCode')?.value || '';
    const prev = document.getElementById('_svgPreview');
    if (prev) prev.innerHTML = code;
}

async function guardarCodigoSVG(ruta, nombre) {
    const code = document.getElementById('_svgCode')?.value || '';
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=guardar_svg', { ruta, contenido: code });
        if (r.success) {
            toast(`SVG "${nombre}" guardado correctamente`, 'ok');
            bootstrap.Modal.getInstance(document.getElementById('_modalSVGEditor'))?.hide();
            renderSVGs();
        } else throw new Error(r.error||'Error al guardar');
    } catch(e) { toast(e.message,'err'); }
}

// ════════════════════════════════════════════════════════════
// PLANTILLAS — GESTIÓN DE PLANTILLAS DEL SISTEMA
// ════════════════════════════════════════════════════════════
const TIPO_LABELS_P = {
    usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',
    activity:'Actividades', state:'Estados', component:'Componentes',
    deployment:'Despliegue', object:'Objetos', communication:'Comunicación', timing:'Tiempo'
};
const TIPO_ICONS_P = {
    usecase:'bi-person-bounding-box', class:'bi-grid-3x3-gap', sequence:'bi-arrow-left-right',
    activity:'bi-diagram-3', state:'bi-toggles', component:'bi-boxes',
    deployment:'bi-server', object:'bi-diagram-2', communication:'bi-chat-dots', timing:'bi-clock'
};

async function renderPlantillasAdmin() {
    const main = document.getElementById('contentArea');
    main.innerHTML = `<div style="text-align:center;padding:50px"><div class="spinner-border" style="color:var(--primary)"></div><p class="mt-3" style="color:var(--txt-muted)">Cargando plantillas…</p></div>`;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=plantillas');
        const plantillas = r.plantillas || [];

        main.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:18px">
            <div>
                <p style="color:var(--txt-muted);font-size:.8rem;margin:3px 0 0">Las plantillas aparecen en el dashboard de alumnos y maestros al crear un diagrama nuevo.</p>
            </div>
            <button onclick="modalNuevaPlantillaAdmin()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:10px;padding:9px 18px;font-size:.83rem;font-weight:600;cursor:pointer">
                <i class="bi bi-plus-circle me-1"></i>Nueva Plantilla
            </button>
        </div>

        ${plantillas.length === 0
            ? `<div style="text-align:center;padding:60px 20px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:16px">
                <i class="bi bi-layout-text-sidebar-reverse" style="font-size:3rem;color:var(--txt-muted);opacity:.4"></i>
                <p style="color:var(--txt-muted);margin-top:12px">No hay plantillas aún. Crea la primera.</p>
               </div>`
            : `<div class="row g-3">
                ${plantillas.map(p => {
                    const icon = TIPO_ICONS_P[p.tipo_diagrama] || 'bi-diagram-3';
                    const lbl  = TIPO_LABELS_P[p.tipo_diagrama] || p.tipo_diagrama;
                    return `
                    <div class="col-md-6 col-lg-4">
                        <div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:14px;overflow:hidden;transition:all .18s"
                             onmouseover="this.style.borderColor=getComputedStyle(document.documentElement).getPropertyValue('--primary');this.style.transform='translateY(-3px)'"
                             onmouseout="this.style.borderColor='';this.style.transform=''">
                            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:14px 16px;display:flex;align-items:center;gap:10px">
                                <div style="width:38px;height:38px;background:rgba(255,255,255,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi ${icon}" style="color:#fff;font-size:1.1rem"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:700;color:#fff;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(p.titulo)}</div>
                                    <span style="background:rgba(255,255,255,.2);color:#fff;font-size:.65rem;font-weight:700;padding:1px 7px;border-radius:8px">${lbl}</span>
                                </div>
                            </div>
                            <div style="padding:12px 16px">
                                <p style="color:var(--txt-muted);font-size:.78rem;margin:0 0 10px;min-height:34px">${esc(p.descripcion||'Sin descripción')}</p>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                                    <div style="font-size:.7rem;color:var(--txt-muted)">
                                        <i class="bi bi-person me-1"></i>${esc(p.username||'admin')}
                                        <span style="margin:0 5px">·</span>
                                        <i class="bi bi-calendar3 me-1"></i>${new Date(p.fecha_modificacion||p.fecha_creacion).toLocaleDateString('es-MX')}
                                    </div>
                                </div>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <button onclick="window.open('<?= BASE_URL ?>/editor?id=${p.id}','_blank')"
                                        style="flex:1;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:7px 0;font-size:.76rem;font-weight:600;cursor:pointer">
                                        <i class="bi bi-pencil me-1"></i>Editar en editor
                                    </button>
                                    <button onclick="modalEditarPlantillaAdmin(${p.id},'${esc(p.titulo)}','${esc(p.descripcion||'')}')"
                                        style="background:var(--bg-hover);border:1.5px solid var(--bd-color);color:var(--txt-main);border-radius:8px;padding:7px 10px;font-size:.76rem;cursor:pointer" title="Editar metadatos">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <button onclick="eliminarPlantillaAdmin(${p.id},'${esc(p.titulo)}')"
                                        style="background:rgba(239,68,68,.08);border:1.5px solid rgba(239,68,68,.25);color:#ef4444;border-radius:8px;padding:7px 10px;font-size:.76rem;cursor:pointer" title="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }).join('')}
               </div>`}`;
    } catch(e) { toast(e.message, 'err'); }
}

function modalNuevaPlantillaAdmin() {
    document.getElementById('_modalPlant')?.remove();
    const tipos = Object.entries(TIPO_LABELS_P).map(([k,v])=>`<option value="${k}">${v}</option>`).join('');
    const m = document.createElement('div');
    m.id = '_modalPlant'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 20px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-plus-circle me-2"></i>Nueva Plantilla</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Título *</label>
                    <input id="_plantTitulo" type="text" placeholder="Nombre de la plantilla" maxlength="100"
                        style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Tipo de diagrama</label>
                    <select id="_plantTipo" style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem">${tipos}</select>
                </div>
                <div>
                    <label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Descripción</label>
                    <textarea id="_plantDesc" rows="2" placeholder="Descripción breve (opcional)" maxlength="255"
                        style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem;resize:vertical;box-sizing:border-box"></textarea>
                </div>
                <p style="font-size:.74rem;color:var(--txt-muted);margin:0">La plantilla se creará vacía. Ábrela en el editor para agregar nodos y conexiones.</p>
            </div>
            <div style="padding:0 20px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.83rem;cursor:pointer">Cancelar</button>
                <button onclick="crearPlantillaAdmin()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-plus-circle me-1"></i>Crear Plantilla
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
    m.addEventListener('shown.bs.modal', ()=> document.getElementById('_plantTitulo')?.focus());
}

async function crearPlantillaAdmin() {
    const titulo = document.getElementById('_plantTitulo')?.value?.trim();
    const tipo   = document.getElementById('_plantTipo')?.value;
    const desc   = document.getElementById('_plantDesc')?.value?.trim();
    if (!titulo) { toast('El título es obligatorio', 'err'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=crear_plantilla', { titulo, tipo, descripcion: desc });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalPlant'))?.hide();
            toast('Plantilla creada', 'ok');
            renderPlantillasAdmin();
        } else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message, 'err'); }
}

function modalEditarPlantillaAdmin(id, tituloActual, descActual) {
    document.getElementById('_modalPlantEdit')?.remove();
    const m = document.createElement('div');
    m.id = '_modalPlantEdit'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 20px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-info-circle me-2"></i>Editar Metadatos</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Título *</label>
                    <input id="_plantEditTitulo" type="text" value="${esc(tituloActual)}" maxlength="100"
                        style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:.78rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:4px">Descripción</label>
                    <textarea id="_plantEditDesc" rows="2" maxlength="255"
                        style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 12px;font-size:.87rem;resize:vertical;box-sizing:border-box">${esc(descActual)}</textarea>
                </div>
            </div>
            <div style="padding:0 20px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.83rem;cursor:pointer">Cancelar</button>
                <button onclick="guardarMetaPlantillaAdmin(${id})" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check2 me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
}

async function guardarMetaPlantillaAdmin(id) {
    const titulo = document.getElementById('_plantEditTitulo')?.value?.trim();
    const desc   = document.getElementById('_plantEditDesc')?.value?.trim();
    if (!titulo) { toast('El título es obligatorio', 'err'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=editar_plantilla', { id, titulo, descripcion: desc });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalPlantEdit'))?.hide();
            toast('Plantilla actualizada', 'ok');
            renderPlantillasAdmin();
        } else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message, 'err'); }
}

async function eliminarPlantillaAdmin(id, titulo) {
    if (!confirm(`¿Eliminar la plantilla "${titulo}"? Esta acción no se puede deshacer.`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin?action=eliminar_plantilla', { id });
        if (r.success) { toast('Plantilla eliminada', 'ok'); renderPlantillasAdmin(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message, 'err'); }
}

// ════════════════════════════════════════════════════════════
// ESPACIO EN DISCO — USUARIOS
// ════════════════════════════════════════════════════════════
async function renderEspacioAdmin() {
    const main = document.getElementById('contentArea');
    main.innerHTML = `<div style="text-align:center;padding:50px"><div class="spinner-border" style="color:var(--primary)"></div><p class="mt-3" style="color:var(--txt-muted)">Cargando datos de espacio…</p></div>`;
    try {
        const r  = await api('<?= BASE_URL ?>/api/admin-dashboard?action=espacio_usuarios');
        const us = r.usuarios || [];
        const fmtB = b => {
            b = parseInt(b||0);
            if (b < 1024) return b + ' B';
            if (b < 1024*1024) return (b/1024).toFixed(1) + ' KB';
            return (b/1024/1024).toFixed(2) + ' MB';
        };
        const totalBytes      = us.reduce((a,u) => a + parseInt(u.espacio_usado_bytes||0), 0);
        const totalLimiteBytes = us.reduce((a,u) => a + (parseInt(u.espacio_limite_mb||0) * 1024 * 1024), 0);
        const sinLimite        = us.filter(u => parseInt(u.espacio_limite_mb||0) === 0).length;

        main.innerHTML = `
        <!-- Resumen global -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px">
            ${[
                {label:'Usuarios',      val: us.length,                                      icon:'bi-people',       clr:'var(--primary)'},
                {label:'Espacio Usado', val: fmtB(totalBytes),                               icon:'bi-hdd-fill',     clr:'#10b981'},
                {label:'Límite Total',  val: totalLimiteBytes===0?'Ilimitado':fmtB(totalLimiteBytes), icon:'bi-archive', clr:'#f59e0b'},
                {label:'Sin Límite',    val: sinLimite,                                      icon:'bi-infinity',     clr:'#8b5cf6'},
            ].map(c=>`
            <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:12px;padding:16px;text-align:center">
                <i class="bi ${c.icon}" style="font-size:1.4rem;color:${c.clr}"></i>
                <div style="font-size:1.5rem;font-weight:700;color:var(--txt-main);margin:6px 0 2px">${c.val}</div>
                <div style="font-size:.72rem;color:var(--txt-muted);font-weight:600;text-transform:uppercase">${c.label}</div>
            </div>`).join('')}
        </div>

        <!-- Límite global -->
        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;padding:18px 20px;margin-bottom:16px">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                <div>
                    <div style="font-weight:700;color:var(--txt-main);margin-bottom:2px"><i class="bi bi-globe me-2" style="color:var(--primary)"></i>Límite Global</div>
                    <div style="font-size:.78rem;color:var(--txt-muted)">Aplica el mismo límite a todos los usuarios (excepto admin). Pon 0 para ilimitado.</div>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    <div style="display:flex;align-items:center;gap:0;border:1.5px solid var(--bd-color);border-radius:8px;overflow:hidden;background:var(--bg-deep)">
                        <input type="number" id="_globalLimAdmin" min="0" value="100" placeholder="0=ilimitado"
                            style="width:110px;background:transparent;color:var(--txt-main);border:none;padding:7px 10px;font-size:.85rem;outline:none">
                        <span style="background:var(--bg-hover);color:var(--txt-muted);padding:7px 10px;font-size:.8rem;font-weight:600;border-left:1px solid var(--bd-color)">MB</span>
                    </div>
                    <button onclick="cambiarLimiteGlobalAdmin()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer">
                        <i class="bi bi-check2 me-1"></i>Aplicar a Todos
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div style="background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:8px">
                <i class="bi bi-people-fill" style="color:var(--primary)"></i>
                <span style="font-weight:700;color:var(--txt-main)">Espacio por Usuario</span>
            </div>
            <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:.83rem">
                <thead><tr style="background:var(--bg-deep)">
                    <th style="padding:10px 14px;text-align:left;font-size:.72rem;color:var(--txt-muted);font-weight:600">Usuario</th>
                    <th style="padding:10px 14px;text-align:left;font-size:.72rem;color:var(--txt-muted);font-weight:600">Rol</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.72rem;color:var(--txt-muted);font-weight:600">Diagramas</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.72rem;color:var(--txt-muted);font-weight:600">Usado</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.72rem;color:var(--txt-muted);font-weight:600">Límite</th>
                    <th style="padding:10px 14px;min-width:110px;font-size:.72rem;color:var(--txt-muted);font-weight:600">Uso</th>
                    <th style="padding:10px 14px;text-align:center;font-size:.72rem;color:var(--txt-muted);font-weight:600">Acción</th>
                </tr></thead>
                <tbody>
                ${us.map(u => {
                    const usado  = parseInt(u.espacio_usado_bytes||0);
                    const lim    = parseInt(u.espacio_limite_mb||100);
                    const limB   = lim * 1024 * 1024;
                    const pct    = lim === 0 ? 0 : Math.min(100,(usado/limB*100));
                    const barClr = pct > 90 ? '#ef4444' : pct > 70 ? '#f59e0b' : '#10b981';
                    const rClr   = u.rol==='admin' ? '#ef4444' : u.rol==='maestro' ? '#8b5cf6' : '#3b82f6';
                    return `<tr style="border-bottom:1px solid var(--bd-color)">
                        <td style="padding:10px 14px">
                            <div style="font-weight:600;color:var(--txt-main)">${esc(u.username)}</div>
                            <div style="font-size:.71rem;color:var(--txt-muted)">${esc(u.nombre_completo||'')}</div>
                        </td>
                        <td style="padding:10px 14px">
                            <span style="background:${rClr}22;color:${rClr};border:1px solid ${rClr}44;border-radius:20px;padding:2px 10px;font-size:.7rem;font-weight:600">${u.rol}</span>
                        </td>
                        <td style="padding:10px 14px;text-align:right;color:var(--txt-main)">${u.num_diagramas}</td>
                        <td style="padding:10px 14px;text-align:right;font-weight:600;color:var(--txt-main)">${fmtB(usado)}</td>
                        <td style="padding:10px 14px;text-align:right;color:var(--txt-muted)">${lim===0?'∞':lim+' MB'}</td>
                        <td style="padding:10px 14px">
                            <div style="background:var(--bg-deep);border-radius:4px;height:6px;overflow:hidden">
                                <div style="height:100%;width:${pct.toFixed(1)}%;background:${barClr};border-radius:4px;transition:width .4s"></div>
                            </div>
                            <div style="font-size:.66rem;color:var(--txt-muted);margin-top:2px;text-align:center">${lim===0?'ilimitado':pct.toFixed(1)+'%'}</div>
                        </td>
                        <td style="padding:10px 14px;text-align:center">
                            ${u.rol!=='admin'
                                ? `<button onclick="editarLimiteEspacioAdmin(${u.id},'${esc(u.username)}',${lim})"
                                    style="background:none;border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:4px 12px;font-size:.75rem;cursor:pointer;transition:all .2s"
                                    onmouseover="this.style.borderColor=getComputedStyle(document.documentElement).getPropertyValue('--primary');this.style.color=getComputedStyle(document.documentElement).getPropertyValue('--primary')"
                                    onmouseout="this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                  </button>`
                                : '<span style="font-size:.72rem;color:var(--txt-muted)">—</span>'}
                        </td>
                    </tr>`;
                }).join('')}
                </tbody>
            </table>
            </div>
        </div>`;
    } catch(e) { toast(e.message, 'err'); }
}

async function cambiarLimiteGlobalAdmin() {
    const lim = parseInt(document.getElementById('_globalLimAdmin')?.value ?? '100');
    if (isNaN(lim) || lim < 0) { toast('Ingresa un número válido (0 = ilimitado)', 'err'); return; }
    if (!confirm(`¿Aplicar límite de ${lim===0?'ILIMITADO':lim+' MB'} a TODOS los usuarios (excepto admin)?`)) return;
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=cambiar_limite_global', { limite_mb: lim });
        if (r.success) { toast(`Límite ${lim===0?'ilimitado':lim+' MB'} aplicado a todos`, 'ok'); renderEspacioAdmin(); }
        else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message, 'err'); }
}

function editarLimiteEspacioAdmin(uid, username, limActual) {
    document.getElementById('_modalEspAdmin')?.remove();
    const m = document.createElement('div');
    m.id = '_modalEspAdmin'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:380px">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--bd-color);background:var(--bg-card)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 20px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-hdd me-2"></i>Límite — ${username}</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px">
                <label style="font-size:.8rem;color:var(--txt-muted);font-weight:600;display:block;margin-bottom:6px">Límite de Almacenamiento <span style="font-weight:400">(0 = sin límite)</span></label>
                <div style="display:flex;align-items:center;gap:0;border:1.5px solid var(--bd-color);border-radius:8px;overflow:hidden;background:var(--bg-deep)">
                    <input id="_espAdminVal" type="number" min="0" value="${limActual}"
                        style="flex:1;background:transparent;color:var(--txt-main);border:none;padding:9px 12px;font-size:1rem;font-weight:600;outline:none">
                    <span style="background:var(--bg-hover);color:var(--txt-muted);padding:9px 12px;font-size:.85rem;font-weight:700;border-left:1px solid var(--bd-color)">MB</span>
                </div>
                <p style="font-size:.74rem;color:var(--txt-muted);margin-top:8px;margin-bottom:0">1 GB = 1024 MB · Pon 0 para dar espacio ilimitado a este usuario.</p>
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:var(--bg-deep);border:1.5px solid var(--bd-color);color:var(--txt-muted);border-radius:8px;padding:8px 18px;font-size:.83rem;cursor:pointer">Cancelar</button>
                <button onclick="guardarLimiteEspacioAdmin(${uid})" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.83rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check2 me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
    m.addEventListener('shown.bs.modal', () => document.getElementById('_espAdminVal')?.focus());
}

async function guardarLimiteEspacioAdmin(uid) {
    const lim = parseInt(document.getElementById('_espAdminVal')?.value ?? '100');
    if (isNaN(lim) || lim < 0) { toast('Valor inválido', 'err'); return; }
    try {
        const r = await api('<?= BASE_URL ?>/api/admin-dashboard?action=cambiar_limite_espacio', { usuario_id: uid, limite_mb: lim });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalEspAdmin'))?.hide();
            toast('Límite actualizado', 'ok');
            renderEspacioAdmin();
        } else throw new Error(r.error||'Error');
    } catch(e) { toast(e.message, 'err'); }
}

// ════════════════════════════════════════════════════════════
// DOCUMENTACIÓN
// ════════════════════════════════════════════════════════════
function renderDocs() {
    document.getElementById('contentArea').innerHTML = `

    <!-- ══ Descargas ══════════════════════════════════════════════ -->
    <div class="section-card mb-3">
        <div class="card-header">
            <i class="bi bi-cloud-download text-primary"></i>
            <h5>Documentos del Proyecto</h5>
            <span class="ms-auto text-muted small">DiagramasUML MVC v5.0</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                ${[
                    { label:'Reporte Técnico MVC',      sub:'Arquitectura, diseño e implementación',     icon:'bi-file-earmark-word', color:'#667eea', bg:'rgba(102,126,234,.12)', url:'DiagramasUML_Reporte_Tecnico_MVC.docx', ext:'.docx' },
                    { label:'Reporte MVC (PDF)',         sub:'Versión lista para imprimir',               icon:'bi-file-earmark-pdf',  color:'#ef4444', bg:'rgba(239,68,68,.12)',   url:'DiagramasUML_Reporte_MVC (1).pdf',     ext:'.pdf'  },
                    { label:'Presentación del Proyecto', sub:'Diapositivas ejecutivas',                   icon:'bi-file-earmark-slides',color:'#f59e0b',bg:'rgba(245,158,11,.12)', url:'DiagramasUML_APP_MVC_Presentacion.pptx',ext:'.pptx'},
                    { label:'Casos de Uso (.docx)',      sub:'Especificación de casos de uso UML',        icon:'bi-file-earmark-word', color:'#10b981', bg:'rgba(16,185,129,.12)', url:'Diagramas de Casos de Uso de su Proyecto.docx',ext:'.docx'},
                    { label:'Casos de Uso (.pdf)',       sub:'Versión PDF lista para entregar',           icon:'bi-file-earmark-pdf',  color:'#10b981', bg:'rgba(16,185,129,.1)',  url:'Diagramas de Casos de Uso de su Proyecto.pdf', ext:'.pdf' },
                    { label:'Esquema SQL completo',      sub:'Script DDL con todas las tablas v2',        icon:'bi-file-earmark-code', color:'#60a5fa', bg:'rgba(96,165,250,.12)', url:'Base/diagramas_v2.sql',                ext:'.sql'  },
                ].map(d => `
                <div class="col-md-6 col-lg-4">
                    <div style="background:#0d0d1a;border:1px solid ${d.color}44;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;height:100%">
                        <div style="background:${d.bg};border-radius:10px;padding:10px;flex-shrink:0">
                            <i class="bi ${d.icon}" style="font-size:1.6rem;color:${d.color}"></i>
                        </div>
                        <div style="min-width:0;flex:1">
                            <div style="color:#fff;font-weight:600;font-size:.85rem;margin-bottom:2px">${d.label}</div>
                            <div style="color:#666;font-size:.72rem;margin-bottom:8px">${d.sub}</div>
                            <a href="<?= BASE_URL ?>/basededatos+info/${d.url}" download
                               style="display:inline-flex;align-items:center;gap:5px;background:linear-gradient(135deg,${d.color},${d.color}cc);border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:.72rem;text-decoration:none;font-weight:600"
                               onclick="toast('Descargando ${d.label}…','info')">
                                <i class="bi bi-download"></i> ${d.ext}
                            </a>
                        </div>
                    </div>
                </div>`).join('')}
            </div>
        </div>
    </div>

    <!-- ══ Documentación técnica inline ═══════════════════════════ -->
    <div class="section-card">
        <div class="card-header">
            <i class="bi bi-book-half text-primary"></i>
            <h5>Documentación Técnica en Línea</h5>
        </div>
        <div class="card-body" style="max-height:78vh;overflow-y:auto;padding:0">

        <!-- Nav pills de secciones -->
        <div style="display:flex;flex-wrap:wrap;gap:6px;padding:14px 18px;border-bottom:1px solid #2a2a4a;position:sticky;top:0;background:#1a1a2e;z-index:5">
            ${['overview','arquitectura','bd','rutas','api','json','instalacion','roles','bugs'].map((s,i) => {
                const labels = {overview:'Resumen',arquitectura:'Arquitectura MVC',bd:'Base de Datos',rutas:'Rutas',api:'API REST',json:'Formato JSON',instalacion:'Instalación',roles:'Roles y Permisos',bugs:'Problemas conocidos'};
                return `<button onclick="document.getElementById('doc_${s}').scrollIntoView({behavior:'smooth',block:'start'})"
                    style="background:rgba(102,126,234,.15);border:1px solid rgba(102,126,234,.3);color:var(--primary,#aab8ff);border-radius:20px;padding:4px 12px;font-size:.75rem;cursor:pointer;white-space:nowrap;transition:all .2s"
                    onmouseover="this.style.background='rgba(102,126,234,.3)'" onmouseout="this.style.background='rgba(102,126,234,.15)'">${labels[s]}</button>`;
            }).join('')}
        </div>

        <div style="padding:20px 24px">

        <!-- ── 1. Resumen ────────────────────────────────────────── -->
        <div id="doc_overview" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-info-circle me-2"></i>Resumen del Sistema
            </h4>
            <p style="color:#ccc;font-size:.88rem;line-height:1.7">
                <strong style="color:#fff">DiagramasUML MVC</strong> es una aplicación web para crear, editar, guardar y exportar diagramas UML.
                Construida en <strong style="color:var(--primary,#aab8ff)">PHP 8+ (patrón MVC)</strong> + MySQL + JavaScript puro, sin frameworks externos de JS.
                Soporta 10 tipos de diagramas UML, almacenamiento de contenido como archivos JSON por usuario,
                exportación a SVG, PNG y PDF, y un sistema completo de grupos, tareas y entregas para uso educativo.
            </p>
            <div class="row g-2 mt-2">
                ${[
                    ['bi-diagram-3','10 tipos de diagrama UML','Casos de uso, clases, secuencia, actividades, estados, componentes, despliegue, objetos, comunicación y tiempo'],
                    ['bi-people-fill','3 roles de usuario','Alumno (editor), Maestro (grupos y tareas), Administrador (panel completo)'],
                    ['bi-file-earmark-code','Almacenamiento híbrido','Metadata en BD MySQL + contenido en archivos JSON por usuario en disco'],
                    ['bi-shield-lock','Seguridad multinivel','Hashing bcrypt, sesiones PHP, validación de permisos por rol, login de emergencia offline'],
                    ['bi-palette','Temas por usuario','Color primario personalizable + modo oscuro/claro guardado en BD por usuario'],
                    ['bi-clipboard-check','Sistema educativo','Grupos, tareas con fecha de entrega, entregas con diagrama adjunto y calificación'],
                ].map(([icon,title,desc]) => `
                <div class="col-md-6">
                    <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:8px;padding:12px;display:flex;gap:10px">
                        <i class="bi ${icon}" style="color:var(--primary);font-size:1.2rem;flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <div style="color:#fff;font-size:.82rem;font-weight:600;margin-bottom:2px">${title}</div>
                            <div style="color:#888;font-size:.73rem;line-height:1.5">${desc}</div>
                        </div>
                    </div>
                </div>`).join('')}
            </div>
        </div>

        <!-- ── 2. Arquitectura MVC ───────────────────────────────── -->
        <div id="doc_arquitectura" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-diagram-2 me-2"></i>Arquitectura MVC
            </h4>
            <pre style="background:#080812;border:1px solid #2a2a4a;padding:16px;border-radius:10px;font-size:.75rem;color:#aaa;overflow-x:auto;line-height:1.6">DiagramasMVC/
├── index.php                   — Punto de entrada único (front controller)
├── .htaccess                   — Redirige todo a index.php (mod_rewrite)
│
├── app/
│   ├── bootstrap.php           — Autoload, constantes ROOT_PATH / BASE_URL / PUBLIC_PATH
│   ├── routes.php              — Mapeo URL → Controlador::método
│   │
│   ├── core/                   — Núcleo del framework
│   │   ├── Router.php          — Enrutador: parsea URL y despacha al controlador
│   │   ├── Controller.php      — Clase base: helpers json(), redirigir(), getJsonInput()
│   │   ├── Model.php           — Clase base: inyecta conexión PDO
│   │   ├── Database.php        — Singleton PDO (lee config/database.php)
│   │   ├── Session.php         — SessionManager: login, roles, emergencia, expiración
│   │   ├── Assets.php          — Helper de URLs de assets (CSS, JS, vendor)
│   │   └── FileManager.php     — CRUD de archivos JSON en uploads/usuario_N/
│   │
│   ├── controllers/
│   │   ├── AuthController.php      — Login, register, logout, login de emergencia offline
│   │   ├── DashboardController.php — Dashboard alumno + API diagramas + API user-config
│   │   ├── EditorController.php    — Vista del editor + API save/load diagrama
│   │   ├── MaestroController.php   — Panel maestro + API grupos/tareas/entregas/calificación
│   │   ├── AdminController.php     — Panel admin + API usuarios/diagramas/mantenimiento/SVGs
│   │   └── AlumnoController.php    — API alumno: grupos, tareas, entregas, mis diagramas
│   │
│   ├── models/
│   │   ├── DiagramModel.php    — CRUD de diagramas (BD + archivo JSON)
│   │   └── UserModel.php       — CRUD de usuarios y permisos
│   │
│   └── views/
│       ├── auth/login.php      — Formulario de login + panel de emergencia offline
│       ├── dashboard/index.php — SPA del alumno (diagramas, grupos, tareas, estadísticas)
│       ├── editor/index.php    — Editor de diagramas (canvas + toolbar + propiedades)
│       ├── maestro/index.php   — Panel maestro (grupos, alumnos, tareas, calificación)
│       └── admin/index.php     — Panel admin (usuarios, diagramas, BD, mantenimiento, docs)
│
├── public/
│   ├── assets/
│   │   ├── css/style.css       — Estilos globales compartidos
│   │   ├── js/editor.js        — Lógica JS del editor (~5500 líneas)
│   │   ├── js/user-theme.js    — Sistema de temas y colores por usuario
│   │   └── vendor/             — Bootstrap 5 + Bootstrap Icons (local, sin CDN)
│   └── uploads/
│       ├── usuario_1/          — Archivos JSON de diagramas del usuario 1
│       │   ├── mi_diagrama.json
│       │   └── .htaccess       — Deniega acceso web directo
│       └── usuario_N/
│
├── config/
│   ├── database.php            — Credenciales de BD (excluir de git)
│   └── .emergency.key          — Hash Argon2id de credenciales de emergencia (chmod 600)
│
├── data/
│   ├── emergency_log.txt       — Log de accesos en modo emergencia
│   └── .htaccess               — Deny from all
│
└── basededatos+info/
    ├── Base/diagramas_v2.sql   — Schema DDL completo (ejecutar en instalación)
    └── [documentos del proyecto]</pre>
        </div>

        <!-- ── 3. Base de Datos ─────────────────────────────────── -->
        <div id="doc_bd" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-database me-2"></i>Esquema de Base de Datos
            </h4>
            ${[
                { name:'usuarios', desc:'Todos los usuarios del sistema (alumnos, maestros, admins)', cols:[
                    ['id','INT PK AUTO_INCREMENT','Identificador único'],
                    ['username','VARCHAR(50) UNIQUE','Nombre para iniciar sesión'],
                    ['email','VARCHAR(100) UNIQUE','Correo electrónico'],
                    ['password','VARCHAR(255)','Hash bcrypt/Argon2id'],
                    ['nombre_completo','VARCHAR(100)','Nombre real para mostrar'],
                    ['rol','ENUM(alumno,maestro,admin)','Define acceso al sistema'],
                    ['es_admin_junior','BOOLEAN','Admin con permisos restringidos'],
                    ['creado_por','INT FK NULL','Admin que creó este usuario'],
                    ['activo','BOOLEAN','FALSE bloquea el login'],
                    ['fecha_registro','TIMESTAMP','Alta de la cuenta'],
                    ['ultimo_acceso','TIMESTAMP NULL','Actualizado en cada login'],
                ]},
                { name:'diagramas', desc:'Cada diagrama creado por un usuario', cols:[
                    ['id','INT PK AUTO_INCREMENT','Identificador único'],
                    ['usuario_id','INT FK → usuarios','Propietario (CASCADE DELETE)'],
                    ['titulo','VARCHAR(200)','Nombre visible en el dashboard'],
                    ['tipo_diagrama','VARCHAR(50)','usecase|class|sequence|activity|state|component|deployment|object|communication|timing'],
                    ['archivo_ruta','VARCHAR(500) NULL','Ruta relativa al JSON en disco'],
                    ['archivo_tamano','INT','Tamaño en bytes del JSON'],
                    ['version','INT','Contador de guardados'],
                    ['descripcion','TEXT','Descripción opcional'],
                    ['etiquetas','VARCHAR(500)','Tags separados por coma'],
                    ['fecha_creacion','TIMESTAMP','Automático al crear'],
                    ['fecha_modificacion','TIMESTAMP ON UPDATE','Actualizado al guardar'],
                ]},
                { name:'grupos', desc:'Grupos de trabajo creados por maestros', cols:[
                    ['id','INT PK AUTO_INCREMENT','Identificador único'],
                    ['nombre','VARCHAR(100)','Nombre del grupo'],
                    ['descripcion','TEXT NULL','Descripción opcional'],
                    ['codigo','VARCHAR(20) UNIQUE','Código de invitación para alumnos'],
                    ['maestro_id','INT FK → usuarios','Maestro dueño del grupo'],
                    ['activo','BOOLEAN','Grupos inactivos no aceptan inscripciones'],
                ]},
                { name:'tareas', desc:'Tareas asignadas a un grupo por un maestro', cols:[
                    ['id','INT PK AUTO_INCREMENT','Identificador único'],
                    ['grupo_id','INT FK → grupos','Grupo al que aplica (CASCADE DELETE)'],
                    ['maestro_id','INT FK → usuarios','Maestro que asignó la tarea'],
                    ['titulo','VARCHAR(200)','Nombre de la tarea'],
                    ['descripcion','TEXT NULL','Instrucciones para el alumno'],
                    ['tipo_diagrama','VARCHAR(50)','Tipo de diagrama requerido'],
                    ['fecha_entrega','DATETIME NULL','Fecha límite (NULL = sin límite)'],
                    ['activa','BOOLEAN','Tareas inactivas no aparecen a los alumnos'],
                ]},
                { name:'entregas', desc:'Entrega de un alumno a una tarea específica', cols:[
                    ['id','INT PK AUTO_INCREMENT','Identificador único'],
                    ['tarea_id','INT FK → tareas','Tarea entregada (CASCADE DELETE)'],
                    ['alumno_id','INT FK → usuarios','Alumno que entrega'],
                    ['diagrama_id','INT FK NULL → diagramas','Diagrama adjunto (SET NULL si se borra)'],
                    ['comentario_alumno','TEXT NULL','Nota del alumno al entregar'],
                    ['calificacion','DECIMAL(5,2) NULL','Calificación del maestro (0–100)'],
                    ['comentario','TEXT NULL','Retroalimentación del maestro'],
                    ['fecha_entrega','TIMESTAMP','Fecha/hora de la entrega'],
                    ['fecha_calificacion','TIMESTAMP NULL','Cuándo fue calificada'],
                    ['UNIQUE','(tarea_id, alumno_id)','Un alumno entrega una vez por tarea'],
                ]},
                { name:'user_config', desc:'Preferencias de tema y color por usuario', cols:[
                    ['user_id','INT PK FK → usuarios','Un registro por usuario (CASCADE DELETE)'],
                    ['theme','VARCHAR(10)','dark o light'],
                    ['primary_color','VARCHAR(7)','Color primario en hex (#667eea)'],
                    ['primary2_color','VARCHAR(7)','Color secundario en hex (#764ba2)'],
                    ['updated_at','TIMESTAMP ON UPDATE','Última modificación'],
                ]},
                { name:'admin_permisos', desc:'Permisos individuales para admins junior', cols:[
                    ['admin_id','INT FK → usuarios','Admin junior al que aplica'],
                    ['permiso','VARCHAR(100)','Nombre del permiso (ver_usuarios, crear_alumnos…)'],
                    ['PRIMARY KEY','(admin_id, permiso)','Un permiso por admin'],
                ]},
            ].map(t => `
            <div style="margin-bottom:20px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <i class="bi bi-table" style="color:var(--primary)"></i>
                    <code style="color:#fff;font-size:.88rem;font-weight:600">${t.name}</code>
                    <span style="color:#888;font-size:.75rem">— ${t.desc}</span>
                </div>
                <table class="admin-table" style="font-size:.75rem">
                    <thead><tr><th>Campo</th><th>Tipo</th><th>Descripción</th></tr></thead>
                    <tbody>
                    ${t.cols.map(([f,ty,d]) => `<tr><td><code>${f}</code></td><td style="color:var(--primary,#aab8ff)">${ty}</td><td style="color:#aaa">${d}</td></tr>`).join('')}
                    </tbody>
                </table>
            </div>`).join('')}
        </div>

        <!-- ── 4. Rutas ─────────────────────────────────────────── -->
        <div id="doc_rutas" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-signpost-split me-2"></i>Rutas del Sistema (routes.php)
            </h4>
            <table class="admin-table" style="font-size:.76rem">
                <thead><tr><th>Método</th><th>URL</th><th>Controlador → Método</th><th>Descripción</th></tr></thead>
                <tbody>
                ${[
                    ['GET','/','AuthController','index','Redirige a /login o /dashboard según sesión'],
                    ['GET','/login','AuthController','loginView','Vista de inicio de sesión'],
                    ['POST','/api/login','AuthController','login','Autentica usuario, inicia sesión'],
                    ['POST','/api/register','AuthController','register','Registra nuevo usuario'],
                    ['POST','/api/emergency-login','AuthController','emergencyLogin','Login offline sin BD (hash local)'],
                    ['POST','/api/emergency-unlock','AuthController','emergencyUnlock','Desbloquea panel de emergencia'],
                    ['POST','/api/setup-emergency','AuthController','setupEmergency','Configura credenciales de emergencia'],
                    ['GET','/logout','AuthController','logout','Destruye sesión, redirige a login'],
                    ['GET','/dashboard','DashboardController','index','Panel del alumno (SPA)'],
                    ['GET','/api/diagramas','DashboardController','getDiagramas','Lista diagramas del usuario'],
                    ['POST','/api/diagramas/delete','DashboardController','delete','Elimina un diagrama'],
                    ['POST','/api/diagramas/duplicate','DashboardController','duplicate','Duplica un diagrama'],
                    ['GET','/editor','EditorController','index','Vista del editor de diagramas'],
                    ['POST','/api/diagramas/save','EditorController','save','Guarda o actualiza diagrama'],
                    ['GET','/api/diagramas/load','EditorController','load','Carga diagrama por ?id=N'],
                    ['GET','/maestro','MaestroController','index','Panel del maestro (SPA)'],
                    ['GET|POST','/api/maestro','MaestroController','api','API maestro: grupos, alumnos, tareas, entregas, calificación'],
                    ['GET','/admin','AdminController','index','Panel de administración (SPA)'],
                    ['GET|POST','/api/admin','AdminController','api','API admin: usuarios, diagramas, BD, mantenimiento, SVGs, archivos'],
                    ['GET|POST','/api/alumno','AlumnoController','api','API alumno: mis grupos, mis tareas, entregar, mis diagramas'],
                    ['GET','/api/user-config','DashboardController','getUserConfig','Devuelve tema/colores del usuario'],
                    ['POST','/api/user-config','DashboardController','saveUserConfig','Guarda tema/colores del usuario'],
                ].map(([m,url,ctrl,met,desc]) => `<tr>
                    <td><span style="background:rgba(102,126,234,.15);color:var(--primary,#aab8ff);border-radius:4px;padding:1px 7px;font-size:.68rem;font-weight:600">${m}</span></td>
                    <td><code style="color:#667eea">${url}</code></td>
                    <td style="color:#ccc;font-size:.72rem"><code>${ctrl}</code> → <code>${met}</code></td>
                    <td style="color:#888;font-size:.72rem">${desc}</td>
                </tr>`).join('')}
                </tbody>
            </table>
        </div>

        <!-- ── 5. API REST (acciones ?action=) ─────────────────── -->
        <div id="doc_api" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-plug me-2"></i>Acciones API Internas (?action=)
            </h4>
            <p style="color:#888;font-size:.8rem;margin-bottom:12px">Las rutas <code>/api/admin</code>, <code>/api/maestro</code> y <code>/api/alumno</code> usan el parámetro <code>?action=</code> para seleccionar la operación. Todas responden JSON y requieren sesión activa con el rol correspondiente.</p>
            ${[
                { title:'AdminController (/api/admin?action=…)', color:'#f59e0b', actions:[
                    ['stats_usuarios','GET','—','Contadores globales del sistema'],
                    ['usuarios','GET','—','Lista todos los usuarios (sin el admin actual)'],
                    ['crear_usuario','POST','{nombre,username,email,password,rol,activo,es_admin_junior}','Crea nuevo usuario con permisos opcionales'],
                    ['editar_usuario','POST','{id,nombre,username,email,password?,rol,activo}','Edita usuario (password vacío = no cambiar)'],
                    ['toggle_activo','POST','{id,activo}','Activa/desactiva cuenta de usuario'],
                    ['actualizar_permisos','POST','{admin_id,permisos[]}','Sobreescribe permisos de admin junior'],
                    ['check_svgs','GET','—','Verifica existencia de cada SVG por tipo de diagrama'],
                    ['generar_svgs','POST','{carpeta}','Genera SVGs faltantes para un tipo'],
                    ['mantenimiento_info','GET','—','Info de carpetas, archivos y referencias huérfanas con datos de BD'],
                    ['archivos_usuario','GET','?uid=N','Lista archivos JSON de un usuario con metadata de BD'],
                    ['eliminar_archivo_usuario','POST','{uid,nombre}','Elimina archivo JSON y su registro en BD'],
                    ['renombrar_archivo_usuario','POST','{uid,nombre,nuevo,titulo}','Renombra archivo y actualiza BD'],
                    ['limpiar_huerfanos','POST','—','Elimina de BD registros sin archivo en disco'],
                    ['optimizar_bd','POST','—','OPTIMIZE TABLE en todas las tablas'],
                    ['vaciar_carpetas_huerfanas','POST','—','Elimina carpetas uploads/usuario_N sin usuario en BD'],
                    ['disk_usage','GET','—','Uso de disco: total, libre, desglose por carpeta'],
                    ['ver_log_emergencia','GET','—','Contenido del log de accesos offline'],
                ]},
                { title:'MaestroController (/api/maestro?action=…)', color:'#60a5fa', actions:[
                    ['stats','GET','—','Estadísticas del maestro (grupos, alumnos, tareas)'],
                    ['grupos','GET','—','Lista grupos del maestro'],
                    ['crear_grupo','POST','{nombre,descripcion}','Crea grupo con código aleatorio'],
                    ['eliminar_grupo','POST','{id}','Elimina grupo y desvincula alumnos'],
                    ['alumnos_grupo','GET','?grupo_id=N','Alumnos inscritos en un grupo'],
                    ['todos_alumnos','GET','—','Todos los alumnos del sistema'],
                    ['diagramas_alumno','GET','?alumno_id=N','Diagramas de un alumno'],
                    ['tareas','GET','—','Tareas asignadas por el maestro con conteo de entregas'],
                    ['crear_tarea','POST','{titulo,grupo_id,tipo_diagrama,descripcion,fecha_entrega}','Asigna tarea al grupo'],
                    ['eliminar_tarea','POST','{id}','Elimina tarea y sus entregas'],
                    ['ver_entregas','GET','?tarea_id=N','Todos los alumnos con su entrega (o null si no entregó)'],
                    ['calificar_entrega','POST','{tarea_id,alumno_id,calificacion,comentario}','Guarda calificación y retroalimentación'],
                ]},
                { title:'AlumnoController (/api/alumno?action=…)', color:'#6ee7b7', actions:[
                    ['mis_grupos','GET','—','Grupos en los que está inscrito el alumno'],
                    ['unirse_grupo','POST','{codigo}','Inscribirse a un grupo por código'],
                    ['salir_grupo','POST','{grupo_id}','Darse de baja de un grupo'],
                    ['mis_tareas','GET','—','Tareas de todos los grupos del alumno con estado de entrega'],
                    ['entregar_tarea','POST','{tarea_id,diagrama_id,comentario_alumno}','Entrega o actualiza entrega de una tarea'],
                    ['mis_diagramas_tarea','GET','?tipo=','Lista diagramas del alumno para selector de entrega'],
                ]},
            ].map(g => `
            <div style="margin-bottom:18px">
                <div style="font-size:.78rem;font-weight:600;color:${g.color};margin-bottom:6px;display:flex;align-items:center;gap:6px">
                    <i class="bi bi-code-slash"></i> ${g.title}
                </div>
                <table class="admin-table" style="font-size:.73rem">
                    <thead><tr><th>action=</th><th>Método</th><th>Parámetros</th><th>Descripción</th></tr></thead>
                    <tbody>
                    ${g.actions.map(([a,m,p,d]) => `<tr>
                        <td><code style="color:${g.color}">${a}</code></td>
                        <td><span style="background:rgba(102,126,234,.12);color:var(--primary,#aab8ff);border-radius:4px;padding:1px 6px;font-size:.65rem">${m}</span></td>
                        <td style="color:#666;font-size:.7rem">${p}</td>
                        <td style="color:#aaa">${d}</td>
                    </tr>`).join('')}
                    </tbody>
                </table>
            </div>`).join('')}
        </div>

        <!-- ── 6. Formato JSON ──────────────────────────────────── -->
        <div id="doc_json" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-filetype-json me-2"></i>Formato del Archivo JSON de Diagrama
            </h4>
            <p style="color:#888;font-size:.8rem;margin-bottom:10px">Cada diagrama se guarda en <code>public/uploads/usuario_N/&lt;titulo_sanitizado&gt;.json</code>. La ruta relativa se registra en <code>diagramas.archivo_ruta</code>.</p>
            <pre style="background:#080812;border:1px solid #2a2a4a;padding:16px;border-radius:10px;font-size:.76rem;color:#aaa;overflow-x:auto;line-height:1.65">{
  "diagrama_id": 5,
  "titulo": "Login — Casos de Uso",
  "fecha_guardado": "2026-04-01 14:30:00",
  "version_app": "5.0",
  "contenido": {
    "diagramType": "usecase",
    "nodes": [
      {
        "id": "actor_1",
        "type": "actor",
        "x": 60, "y": 140,
        "text": "Usuario",
        "width": 80, "height": 120,
        "color": "#ffffff",
        "attributes": "",
        "methods": ""
      },
      {
        "id": "usecase_1",
        "type": "usecase",
        "x": 280, "y": 160,
        "text": "Iniciar sesión",
        "width": 160, "height": 60,
        "color": "#ffffff"
      }
    ],
    "connections": [
      {
        "fromNode": "actor_1",
        "toNode": "usecase_1",
        "fromSide": "right",
        "toSide": "left",
        "type": "asociacion",
        "label": ""
      }
    ]
  }
}</pre>
        </div>

        <!-- ── 7. Instalación ───────────────────────────────────── -->
        <div id="doc_instalacion" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-gear me-2"></i>Instalación y Configuración
            </h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;padding:14px 16px;height:100%">
                        <div style="color:var(--primary,#aab8ff);font-weight:600;font-size:.82rem;margin-bottom:10px"><i class="bi bi-card-checklist me-2"></i>Requisitos</div>
                        ${[
                            ['PHP 8.0+','Extensiones PDO y PDO_MySQL habilitadas'],
                            ['MySQL 5.7+ / MariaDB 10.3+','Con usuario con permisos CREATE TABLE'],
                            ['Apache + mod_rewrite','XAMPP / Laragon / servidor Linux'],
                            ['public/uploads/ escribible','chmod 755 en Linux, permiso de escritura en Windows'],
                            ['config/ protegida','No accesible desde el navegador (ya incluye .htaccess)'],
                        ].map(([req,note]) => `<div style="display:flex;gap:8px;margin-bottom:8px;align-items:flex-start">
                            <i class="bi bi-check-circle-fill" style="color:#10b981;margin-top:2px;flex-shrink:0"></i>
                            <div><span style="color:#ccc;font-size:.8rem;font-weight:500">${req}</span><br><span style="color:#666;font-size:.72rem">${note}</span></div>
                        </div>`).join('')}
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;padding:14px 16px;height:100%">
                        <div style="color:var(--primary,#aab8ff);font-weight:600;font-size:.82rem;margin-bottom:10px"><i class="bi bi-list-ol me-2"></i>Pasos de instalación</div>
                        ${[
                            ['Copia la carpeta','Coloca <code>DiagramasMVC/</code> dentro de <code>htdocs/</code> (XAMPP) o el directorio raíz del servidor'],
                            ['Crea la base de datos','En phpMyAdmin crea una BD vacía y ejecuta <code>basededatos+info/Base/diagramas_v2.sql</code>'],
                            ['Configura la conexión','Edita <code>config/database.php</code> con host, nombre de BD, usuario y contraseña'],
                            ['Verifica permisos','Asegúrate de que <code>public/uploads/</code> y <code>data/</code> son escribibles por el servidor web'],
                            ['Accede al sistema','Abre <code>http://localhost/DiagramasMVC/</code> — redirige a login automáticamente'],
                            ['Primer login','Usuarios de prueba incluidos en el SQL: <code>admin</code> / <code>alumno_prueba</code> / <code>maestro_prueba</code> con contraseña <code>password</code>'],
                        ].map(([t,d],i) => `<div style="display:flex;gap:10px;margin-bottom:9px;align-items:flex-start">
                            <span style="width:22px;height:22px;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;color:#fff;flex-shrink:0;margin-top:1px">${i+1}</span>
                            <div><span style="color:#ccc;font-size:.8rem;font-weight:500">${t}</span><br><span style="color:#666;font-size:.72rem">${d}</span></div>
                        </div>`).join('')}
                    </div>
                </div>
                <div class="col-12">
                    <div style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.3);border-radius:10px;padding:12px 16px">
                        <div style="color:#f59e0b;font-size:.8rem;font-weight:600;margin-bottom:4px"><i class="bi bi-shield-lock me-2"></i>Configurar acceso de emergencia (recomendado)</div>
                        <div style="color:#888;font-size:.75rem;line-height:1.6">Desde el panel Admin → Instalación → sección "Credenciales de Emergencia", define usuario y contraseña para poder entrar al panel aunque la BD esté caída. Se guarda como hash Argon2id en <code>config/.emergency.key</code> (fuera del webroot público).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 8. Roles y Permisos ──────────────────────────────── -->
        <div id="doc_roles" style="margin-bottom:32px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-shield-fill me-2"></i>Roles y Permisos
            </h4>
            <div class="row g-3 mb-3">
            ${[
                { rol:'alumno', color:'#6ee7b7', icon:'bi-mortarboard-fill', label:'Alumno', accesos:[
                    'Dashboard con sus diagramas','Editor de diagramas completo','Unirse/salir de grupos con código','Ver y entregar tareas asignadas','Adjuntar diagrama existente o crear uno nuevo al entregar','Ver su calificación y retroalimentación'
                ]},
                { rol:'maestro', color:'#60a5fa', icon:'bi-person-badge-fill', label:'Maestro', accesos:[
                    'Todo lo del alumno','Panel de maestro','Crear y gestionar grupos','Ver alumnos de sus grupos','Asignar tareas con fecha y tipo de diagrama','Ver entregas por alumno con nombre completo','Calificar y comentar entregas','Ver diagramas de sus alumnos'
                ]},
                { rol:'admin', color:'#f59e0b', icon:'bi-shield-fill-check', label:'Administrador', accesos:[
                    'Todo lo del maestro','Panel de administración completo','Crear/editar/desactivar usuarios de cualquier rol','Asignar permisos a admins junior','Ver y gestionar todos los diagramas','Configuración y diagnóstico de BD','Mantenimiento de archivos y carpetas','Verificar y reparar SVGs del sistema','Acceso en modo emergencia sin BD'
                ]},
            ].map(r => `<div class="col-md-4">
                <div style="background:#0d0d1a;border:1.5px solid ${r.color}44;border-radius:12px;padding:16px;height:100%">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                        <div style="width:36px;height:36px;background:${r.color}22;border-radius:8px;display:flex;align-items:center;justify-content:center">
                            <i class="bi ${r.icon}" style="color:${r.color};font-size:1.1rem"></i>
                        </div>
                        <span style="color:${r.color};font-weight:700;font-size:.88rem">${r.label}</span>
                    </div>
                    ${r.accesos.map(a => `<div style="display:flex;gap:6px;margin-bottom:5px;font-size:.75rem">
                        <i class="bi bi-check2" style="color:${r.color};flex-shrink:0;margin-top:1px"></i>
                        <span style="color:#aaa">${a}</span>
                    </div>`).join('')}
                </div>
            </div>`).join('')}
            </div>
            <div style="background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;padding:14px 16px">
                <div style="color:#f59e0b;font-size:.8rem;font-weight:600;margin-bottom:8px"><i class="bi bi-shield-half me-2"></i>Admin Junior</div>
                <p style="color:#888;font-size:.77rem;margin-bottom:8px;line-height:1.6">Un administrador puede ser marcado como "junior", lo que restringe su acceso a solo los permisos que el admin principal le conceda explícitamente. Los permisos disponibles son:</p>
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                ${['ver_usuarios','crear_alumnos','crear_maestros','editar_usuarios','eliminar_usuarios','ver_diagramas','eliminar_diagramas','ver_reportes','ver_grupos','gestionar_grupos','ver_svgs','ver_docs'].map(p =>
                    `<code style="background:rgba(245,158,11,.1);color:#f59e0b;border:1px solid rgba(245,158,11,.3);border-radius:6px;padding:2px 8px;font-size:.7rem">${p}</code>`
                ).join('')}
                </div>
            </div>
        </div>

        <!-- ── 9. Problemas conocidos ────────────────────────────── -->
        <div id="doc_bugs" style="margin-bottom:16px">
            <h4 style="color:var(--primary);border-bottom:1px solid #2a2a4a;padding-bottom:8px;margin-bottom:14px">
                <i class="bi bi-bug me-2"></i>Problemas Resueltos y Notas Técnicas
            </h4>
            <table class="admin-table" style="font-size:.75rem">
                <thead><tr><th>Problema</th><th>Causa</th><th>Solución aplicada</th></tr></thead>
                <tbody>
                ${[
                    ['Plantillas corrompían el editor','El sessionStorage se leía pero nunca se cargaban nodes/connections','Se reasignan IDs únicos y se filtran conexiones huérfanas al cargar plantilla'],
                    ['Login de emergencia no autenticaba','Claves de sesión inconsistentes (user_rol vs rol)','Session.php normaliza las claves de sesión de emergencia automáticamente'],
                    ['Tema no se guardaba en admin/maestro','BASE_URL se definía DESPUÉS de cargar user-theme.js (race condition)','BASE_URL se mueve a un <script> inline ANTES de la etiqueta del JS'],
                    ['Duplicar creaba el mismo archivo','El anti-duplicado detectaba el nombre y redirigía a actualizar()','duplicar() genera nombre único con sufijo _copia_TIMESTAMP'],
                    ['SVGs no cargan (secuencia/comunicación)','Tildes en nombres de carpetas rompen URLs en Windows','Carpetas renombradas sin tildes (Interaccion, Comunicacion)'],
                    ['Búsqueda predeterminada con datos del admin','El navegador rellenaba el campo con autocomplete','autocomplete="new-password" + limpiar value programáticamente tras render'],
                    ['Referencias huérfanas en BD','Al reinstalar, archivos JSON se pierden pero BD mantiene rutas','Botón "Limpiar huérfanas" en sección Mantenimiento'],
                    ['Colores negros en tarjetas de tareas','Estilos copiados del panel oscuro (admin) aplicados al dashboard claro','Colores adaptados al tema claro del dashboard del alumno'],
                ].map(([p,c,s]) => `<tr><td style="color:#fca5a5">${p}</td><td style="color:#888">${c}</td><td style="color:#6ee7b7">${s}</td></tr>`).join('')}
                </tbody>
            </table>
        </div>

        </div><!-- /padding -->
        </div><!-- /card-body scrollable -->
    </div><!-- /section-card -->
    `; // end innerHTML
}

// ── Corregir badges de rol ─────────────────────────────────────
// (alumno, maestro, admin en lugar de usuario, admin)
// Ya están bien en el HTML de tabla de usuarios, solo actualizamos las clases:

// ════════════════════════════════════════════════════════════
// GRUPOS & TAREAS (admin)
// ════════════════════════════════════════════════════════════
// Cache de grupos y tareas para filtrado client-side
let _todosGrupos  = [];
let _todasTareas  = [];

async function renderGruposAdmin() {
    loading();
    try {
        const data = await api('<?= BASE_URL ?>/api/admin?action=grupos_admin');
        _todosGrupos = data.grupos  || [];
        _todasTareas = data.tareas  || [];

        document.getElementById('contentArea').innerHTML = `
            <!-- Tarjetas de estadísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="stat-card">
                    <div class="stat-icon" style="color:#667eea"><i class="bi bi-collection-fill"></i></div>
                    <div class="stat-num">${data.total_grupos||0}</div><div class="stat-label">Grupos activos</div>
                </div></div>
                <div class="col-md-4"><div class="stat-card">
                    <div class="stat-icon" style="color:#10b981"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-num">${data.total_inscripciones||0}</div><div class="stat-label">Inscripciones</div>
                </div></div>
                <div class="col-md-4"><div class="stat-card">
                    <div class="stat-icon" style="color:#f59e0b"><i class="bi bi-clipboard-check-fill"></i></div>
                    <div class="stat-num">${data.total_tareas||0}</div><div class="stat-label">Tareas asignadas</div>
                </div></div>
            </div>

            <!-- ── SECCIÓN GRUPOS ───────────────────────────────────── -->
            <div class="section-card mb-3">
                <div class="card-header">
                    <i class="bi bi-collection text-primary"></i>
                    <h5>Grupos — <span id="gContador">${_todosGrupos.length}</span></h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <!-- Toggle para ocultar/mostrar la tabla de grupos -->
                        <div class="d-flex align-items-center gap-2" style="font-size:.8rem;color:#888">
                            <span>Mostrar</span>
                            <div class="toggle-switch" id="toggleGrupos" onclick="toggleSeccion('grupos')" title="Ocultar/mostrar grupos">
                                <div class="toggle-knob"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filtro por nombre de grupo/equipo -->
                <div class="card-body pb-2 pt-3" style="border-bottom:1px solid #2a2a4a" id="filtroGruposBar">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="d-flex align-items-center gap-1" style="flex:1;min-width:160px">
                            <i class="bi bi-search text-muted"></i>
                            <input type="text" id="gBusqueda" class="form-control-dark w-100"
                                placeholder="Buscar grupo o equipo..."
                                style="font-size:.82rem" autocomplete="off"
                                oninput="filtrarGrupos()">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-toggle-on text-muted"></i>
                            <select id="gFiltroEstado" class="form-control-dark" style="font-size:.82rem" onchange="filtrarGrupos()">
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        <button class="btn-admin-outline" style="font-size:.78rem;padding:6px 12px" onclick="limpiarFiltrosGrupos()">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
                <div id="tablaGruposWrap" class="card-body p-0">
                    <table class="admin-table">
                        <thead><tr><th>#</th><th>Nombre / Equipo</th><th>Código</th><th>Maestro</th><th>Alumnos</th><th>Tareas</th><th>Estado</th></tr></thead>
                        <tbody id="gTbody">
                        ${_todosGrupos.map(g => `
                            <tr>
                                <td class="text-muted">${g.id}</td>
                                <td><strong class="text-light">${esc(g.nombre)}</strong></td>
                                <td><code style="background:#0d0d1a;color:#667eea;padding:2px 8px;border-radius:4px">${esc(g.codigo)}</code></td>
                                <td style="color:var(--primary,#aab8ff)">${esc(g.maestro_nombre||'?')}</td>
                                <td><span class="badge-tipo">${g.num_alumnos||0}</span></td>
                                <td><span class="badge-tipo">${g.num_tareas||0}</span></td>
                                <td>${g.activo=='1'?'<span class="status-ok">Activo</span>':'<span class="status-err">Inactivo</span>'}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── SECCIÓN TAREAS ───────────────────────────────────── -->
            <div class="section-card">
                <div class="card-header">
                    <i class="bi bi-clipboard-check text-warning"></i>
                    <h5>Tareas — <span id="tContador">${_todasTareas.length}</span></h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <!-- Toggle para ocultar/mostrar la tabla de tareas -->
                        <div class="d-flex align-items-center gap-2" style="font-size:.8rem;color:#888">
                            <span>Mostrar</span>
                            <div class="toggle-switch" id="toggleTareas" onclick="toggleSeccion('tareas')" title="Ocultar/mostrar tareas">
                                <div class="toggle-knob"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filtros de tareas: por nombre de tarea y por grupo -->
                <div class="card-body pb-2 pt-3" style="border-bottom:1px solid #2a2a4a" id="filtroTareasBar">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="d-flex align-items-center gap-1" style="flex:1;min-width:160px">
                            <i class="bi bi-search text-muted"></i>
                            <input type="text" id="tBusqueda" class="form-control-dark w-100"
                                placeholder="Buscar tarea..."
                                style="font-size:.82rem" autocomplete="off"
                                oninput="filtrarTareas()">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-collection text-muted"></i>
                            <select id="tFiltroGrupo" class="form-control-dark" style="font-size:.82rem" onchange="filtrarTareas()">
                                <option value="">Todos los grupos</option>
                                ${_todosGrupos.map(g => `<option value="${esc(g.nombre)}">${esc(g.nombre)}</option>`).join('')}
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-diagram-2 text-muted"></i>
                            <select id="tFiltroTipo" class="form-control-dark" style="font-size:.82rem" onchange="filtrarTareas()">
                                <option value="">Todos los tipos</option>
                        <optgroup label="── Estructurales ──────────────">
                        <option value="class">Clases</option>
                        <option value="object">Objetos</option>
                        <option value="package">Paquetes</option>
                        <option value="composite">Estructura Compuesta</option>
                        <option value="component">Componentes</option>
                        <option value="deployment">Despliegue</option>
                        <option value="profile">Perfiles</option>
                        </optgroup>
                        <optgroup label="── Comportamiento ─────────────">
                        <option value="usecase">Casos de Uso</option>
                        <option value="activity">Actividades</option>
                        <option value="state">Máquina de Estado</option>
                        </optgroup>
                        <optgroup label="── Interacción ────────────────">
                        <option value="sequence">Secuencia</option>
                        <option value="communication">Comunicación</option>
                        <option value="timing">Tiempos</option>
                        <option value="overview">Descripción General</option>
                        </optgroup>
                            </select>
                        </div>
                        <button class="btn-admin-outline" style="font-size:.78rem;padding:6px 12px" onclick="limpiarFiltrosTareas()">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
                <div id="tablaTareasWrap" class="card-body p-0">
                    <table class="admin-table">
                        <thead><tr><th>Tarea</th><th>Grupo</th><th>Maestro</th><th>Tipo</th><th>Entrega</th><th>Entregas</th></tr></thead>
                        <tbody id="tTbody">
                        ${_todasTareas.map(t => `
                            <tr>
                                <td><strong class="text-light">${esc(t.titulo)}</strong></td>
                                <td style="color:var(--primary,#aab8ff)">${esc(t.grupo_nombre||'—')}</td>
                                <td class="text-muted">${esc(t.maestro_nombre||'—')}</td>
                                <td><span class="badge-tipo">${TIPOS[t.tipo_diagrama]||t.tipo_diagrama}</span></td>
                                <td class="text-muted" style="font-size:.78rem">${t.fecha_entrega?new Date(t.fecha_entrega).toLocaleDateString('es-MX'):'Sin fecha'}</td>
                                <td><span class="badge-tipo">${t.num_entregas||0}/${t.total_alumnos||'?'}</span></td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>`;

        // Inicializar toggles en estado "activo" (visible)
        document.getElementById('toggleGrupos').classList.add('active');
        document.getElementById('toggleTareas').classList.add('active');

    } catch(e) { toast(e.message,'err'); }
}

// Filtra la tabla de grupos por texto y estado
function filtrarGrupos() {
    const busq   = (document.getElementById('gBusqueda')?.value     || '').toLowerCase().trim();
    const estado = (document.getElementById('gFiltroEstado')?.value || '');

    const filtrados = _todosGrupos.filter(g => {
        const matchBusq   = !busq   || (g.nombre||'').toLowerCase().includes(busq)
                                     || (g.maestro_nombre||'').toLowerCase().includes(busq)
                                     || (g.codigo||'').toLowerCase().includes(busq);
        const matchEstado = !estado || String(g.activo) === estado;
        return matchBusq && matchEstado;
    });

    const contador = document.getElementById('gContador');
    if (contador) contador.textContent = filtrados.length;

    const tbody = document.getElementById('gTbody');
    if (!tbody) return;

    if (filtrados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-search me-2"></i>Sin grupos con esos filtros</td></tr>`;
        return;
    }
    tbody.innerHTML = filtrados.map(g => `
        <tr>
            <td class="text-muted">${g.id}</td>
            <td><strong class="text-light">${esc(g.nombre)}</strong></td>
            <td><code style="background:#0d0d1a;color:#667eea;padding:2px 8px;border-radius:4px">${esc(g.codigo)}</code></td>
            <td style="color:var(--primary,#aab8ff)">${esc(g.maestro_nombre||'?')}</td>
            <td><span class="badge-tipo">${g.num_alumnos||0}</span></td>
            <td><span class="badge-tipo">${g.num_tareas||0}</span></td>
            <td>${g.activo=='1'?'<span class="status-ok">Activo</span>':'<span class="status-err">Inactivo</span>'}</td>
        </tr>`).join('');
}

// Filtra la tabla de tareas por nombre, grupo y tipo de diagrama
function filtrarTareas() {
    const busq  = (document.getElementById('tBusqueda')?.value    || '').toLowerCase().trim();
    const grupo = (document.getElementById('tFiltroGrupo')?.value || '');
    const tipo  = (document.getElementById('tFiltroTipo')?.value  || '');

    const filtrados = _todasTareas.filter(t => {
        const matchBusq  = !busq  || (t.titulo||'').toLowerCase().includes(busq)
                                   || (t.maestro_nombre||'').toLowerCase().includes(busq);
        const matchGrupo = !grupo || (t.grupo_nombre||'') === grupo;
        const matchTipo  = !tipo  || t.tipo_diagrama === tipo;
        return matchBusq && matchGrupo && matchTipo;
    });

    const contador = document.getElementById('tContador');
    if (contador) contador.textContent = filtrados.length;

    const tbody = document.getElementById('tTbody');
    if (!tbody) return;

    if (filtrados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-search me-2"></i>Sin tareas con esos filtros</td></tr>`;
        return;
    }
    tbody.innerHTML = filtrados.map(t => `
        <tr>
            <td><strong class="text-light">${esc(t.titulo)}</strong></td>
            <td style="color:var(--primary,#aab8ff)">${esc(t.grupo_nombre||'—')}</td>
            <td class="text-muted">${esc(t.maestro_nombre||'—')}</td>
            <td><span class="badge-tipo">${TIPOS[t.tipo_diagrama]||t.tipo_diagrama}</span></td>
            <td class="text-muted" style="font-size:.78rem">${t.fecha_entrega?new Date(t.fecha_entrega).toLocaleDateString('es-MX'):'Sin fecha'}</td>
            <td><span class="badge-tipo">${t.num_entregas||0}/${t.total_alumnos||'?'}</span></td>
        </tr>`).join('');
}

function limpiarFiltrosGrupos() {
    ['gBusqueda','gFiltroEstado'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    filtrarGrupos();
}

function limpiarFiltrosTareas() {
    ['tBusqueda','tFiltroGrupo','tFiltroTipo'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    filtrarTareas();
}

// Oculta o muestra la tabla de grupos o tareas con animación
function toggleSeccion(cual) {
    const ids = cual === 'grupos'
        ? { wrap: 'tablaGruposWrap', filtro: 'filtroGruposBar', btn: 'toggleGrupos' }
        : { wrap: 'tablaTareasWrap', filtro: 'filtroTareasBar', btn: 'toggleTareas' };

    const wrap   = document.getElementById(ids.wrap);
    const filtro = document.getElementById(ids.filtro);
    const btn    = document.getElementById(ids.btn);
    if (!wrap) return;

    const visible = btn.classList.contains('active');
    if (visible) {
        wrap.style.display   = 'none';
        filtro.style.display = 'none';
        btn.classList.remove('active');
    } else {
        wrap.style.display   = '';
        filtro.style.display = '';
        btn.classList.add('active');
    }
}

// ── Constantes ───────────────────────────────────────────────

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
// Escapa HTML especial para insertar datos de BD en el DOM de forma segura (previene XSS).
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
// Convierte bytes a unidades legibles: 0→'0 B', 1500→'1.5 KB', 2000000→'1.9 MB'
function formatBytes(b){ if(!b||b===0)return'0 B'; const k=1024,s=['B','KB','MB','GB'],i=Math.floor(Math.log(b)/Math.log(k)); return parseFloat((b/Math.pow(k,i)).toFixed(1))+' '+s[i]; }

// Cargar sección inicial
function toggleThemeDrawer() {
    const drawer  = document.getElementById('themeDrawer');
    const overlay = document.getElementById('themeOverlay');
    const isOpen  = drawer.style.right === '0px';
    drawer.style.right   = isOpen ? '-340px' : '0px';
    overlay.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) renderThemePanel('adminThemeContainer', 'dark');
}


// ════════════════════════════════════════════════════════════
// CARPETAS DE USUARIOS — expandibles con gestión de archivos
// ════════════════════════════════════════════════════════════
function toggleSvgGroup(idx) {
    const body = document.getElementById('svgGroup_'+idx);
    const chev = document.getElementById('svgChev_'+idx);
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : 'block';
    if (chev) chev.style.transform = open ? '' : 'rotate(90deg)';
}

async function toggleCarpeta(uid) {
    const body  = document.getElementById('carpeta_'+uid);
    const chev  = document.getElementById('chevron_'+uid);
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : 'block';
    if (chev) chev.style.transform = open ? '' : 'rotate(90deg)';
    if (!open) await cargarArchivosUsuario(uid);
}

async function cargarArchivosUsuario(uid) {
    const cont = document.getElementById('archivos_'+uid);
    if (!cont) return;
    try {
        const r = await api(`<?= BASE_URL ?>/api/admin?action=archivos_usuario&uid=${uid}`);
        if (!r.success) { cont.innerHTML = `<span class="status-err">${esc(r.error||'Error')}</span>`; return; }
        if (!r.archivos.length) { cont.innerHTML = '<span style="color:#555">Carpeta vacía</span>'; return; }

        cont.innerHTML = `
        <table style="width:100%;border-collapse:collapse;font-size:.78rem">
            <thead><tr style="color:#667eea;border-bottom:1px solid #2a2a4a">
                <th style="padding:5px 8px">Archivo</th>
                <th style="padding:5px 8px">Título en BD</th>
                <th style="padding:5px 8px">Tipo</th>
                <th style="padding:5px 8px">Tamaño</th>
                <th style="padding:5px 8px">Modificado</th>
                <th style="padding:5px 8px">BD</th>
                <th style="padding:5px 8px">Acciones</th>
            </tr></thead>
            <tbody>
            ${r.archivos.map(a => `
            <tr style="border-bottom:1px solid #1a1a2e" id="frow_${uid}_${a.nombre.replace('.json','')}">
                <td style="padding:5px 8px;color:#ccc;font-family:monospace">${esc(a.nombre)}</td>
                <td style="padding:5px 8px;color:#aaa">${a.titulo ? esc(a.titulo) : '<span style="color:#444">—</span>'}</td>
                <td style="padding:5px 8px">
                    ${a.tipo ? `<span class="badge-tipo" style="font-size:.65rem">${esc(a.tipo)}</span>` : '<span style="color:#444">—</span>'}
                </td>
                <td style="padding:5px 8px;color:#888">${(a.tamano/1024).toFixed(1)} KB</td>
                <td style="padding:5px 8px;color:#666">${esc(a.modificado)}</td>
                <td style="padding:5px 8px">
                    ${a.en_bd
                        ? '<i class="bi bi-database-check" style="color:#10b981" title="Registrado en BD"></i>'
                        : '<i class="bi bi-database-x" style="color:#f59e0b" title="Sin registro en BD (huérfano)"></i>'}
                </td>
                <td style="padding:5px 8px">
                    <div class="d-flex gap-1">
                        <button class="btn-admin-outline" style="font-size:.65rem;padding:2px 7px"
                            title="Editar nombre/título"
                            onclick="editarArchivo(${uid},'${esc(a.nombre)}','${esc(a.titulo||'')}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn-danger-sm" style="font-size:.65rem;padding:2px 7px"
                            title="Eliminar archivo"
                            onclick="eliminarArchivoUsuario(${uid},'${esc(a.nombre)}')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </td>
            </tr>`).join('')}
            </tbody>
        </table>`;
    } catch(e) {
        if (cont) cont.innerHTML = `<span class="status-err">Error: ${esc(e.message)}</span>`;
    }
}

async function eliminarArchivoUsuario(uid, nombre) {
    if (!confirm(`¿Eliminar "${nombre}" del usuario ${uid}?\nEsto también lo elimina de la base de datos si existe.`)) return;
    try {
        const r = await api(`<?= BASE_URL ?>/api/admin?action=eliminar_archivo_usuario`, { uid, nombre });
        if (r.success) { toast('Archivo eliminado', 'ok'); await cargarArchivosUsuario(uid); }
        else toast(r.error || 'Error al eliminar', 'err');
    } catch(e) { toast(e.message, 'err'); }
}

function editarArchivo(uid, nombre, tituloActual) {
    const nuevoNombre = prompt(`Nuevo nombre de archivo (sin .json):`, nombre.replace('.json',''));
    if (!nuevoNombre) return;
    const nuevoTitulo = prompt(`Título del diagrama:`, tituloActual || '');
    if (nuevoTitulo === null) return; // cancelado

    api(`<?= BASE_URL ?>/api/admin?action=renombrar_archivo_usuario`, {
        uid, nombre,
        nuevo: nuevoNombre.trim().replace('.json','') + '.json',
        titulo: nuevoTitulo.trim()
    }).then(r => {
        if (r.success) { toast('Archivo actualizado', 'ok'); cargarArchivosUsuario(uid); }
        else toast(r.error || 'Error', 'err');
    }).catch(e => toast(e.message, 'err'));
}

document.addEventListener('DOMContentLoaded', () => {
    adjustJuniorNavButtons();
    renderAdminNotice();
    // En modo emergencia, ir directo a la configuración de BD
    if (IS_EMERGENCY) {
        showSection('db');
        toast('Modo emergencia: solo puedes reparar la conexión a BD.', 'info');
    } else {
        showSection('resumen');
    }
});
</script>
</body>
</html>