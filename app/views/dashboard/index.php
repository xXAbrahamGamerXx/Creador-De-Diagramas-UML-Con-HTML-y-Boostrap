<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Diagramas UML</title>
    <link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
    <style>
        :root {
            --primary:     #667eea;
            --primary2:    #764ba2;
            --primary-rgb: 102,126,234;
            --sidebar-w:   260px;
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

        * { box-sizing: border-box; }

        body {
            background: var(--bg-deep);
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        /* Light theme overrides */
        body.light-theme { --bg-deep:#f0f2f8; --bg-card:#fff; --bg-hover:#f8f9ff; --bd-color:#e8eaf0; --txt-main:#1a1a2e; --txt-muted:#666; color:#1a1a2e; }
        body.light-theme .diagram-card { background: #fff; border-color: #dde0f0; }
        body.light-theme .stat-card { background: #fff; }
        body.light-theme .page-header h2 { color: #1a1a2e; }
        body.light-theme .search-input { background: #fff; color: #1a1a2e; border-color: #dde0f0; }
        body.light-theme .form-control, body.light-theme .form-select { background: #fff; color: #1a1a2e; border-color: #dde0f0; }
        body.light-theme .form-control:focus, body.light-theme .form-select:focus { border-color: var(--primary); background: #fff; }
        body.light-theme .empty-state { background: #fff; }
        body.light-theme .nav-item-btn { color: rgba(30,30,46,.75); }
        body.light-theme .nav-item-btn:hover { background: rgba(var(--primary-rgb),.1); color: var(--primary); }
        body.light-theme .sidebar { background: linear-gradient(160deg,#f8f9ff,#eef0ff); color:#1e1e2e; }
        body:not(.light-theme) [style*="background:#fff;"] { background: var(--bg-card) !important; }
        body:not(.light-theme) [style*="color:#1a1a2e"] { color: var(--txt-main) !important; }
        body:not(.light-theme) [style*="color:#888"] { color: var(--txt-muted) !important; }
        body:not(.light-theme) [style*="color:#666"] { color: var(--txt-muted) !important; }
        body:not(.light-theme) [style*="color:#bbb"] { color: var(--txt-muted) !important; }
        body:not(.light-theme) [style*="border:1.5px solid #e8eaf0"] { border-color: var(--bd-color) !important; }
        body:not(.light-theme) [style*="border-bottom:1.5px solid #f0f2f8"] { border-color: var(--bd-color) !important; }
        body:not(.light-theme) [style*="border-bottom:2px solid #e8eaf0"] { border-color: var(--bd-color) !important; }
        body:not(.light-theme) [style*="background:#f8f9ff"] { background: var(--bg-hover) !important; }
        body:not(.light-theme) [style*="background:#f0f2f8"] { background: var(--bg-hover) !important; }
        body.light-theme .sidebar-brand h4 { color:#1e1e2e; }
        body.light-theme .sidebar-user h6 { color:#1e1e2e; }
        body.light-theme .sidebar-user small { color:#666; }
        body.light-theme .sidebar-user .avatar { background:rgba(0,0,0,.2); }
        body.light-theme .nav-item-btn.active { color:#1e1e2e; }
        body.light-theme .sidebar-footer .nav-item-btn { color: rgba(30,30,46,.75) !important; }
        body.light-theme #themeDrawer { background:#f8f9ff !important; border-color:#dde0f0 !important; }
        body.light-theme [style*="color:rgba(255,255,255"] { color:#1e1e2e !important; }
        body.light-theme #themeDrawer [style*="color:#fff"] { color:#1e1e2e !important; }
        body.light-theme .text-muted,
        body.light-theme .form-text,
        body.light-theme small,
        body.light-theme .sidebar-user small,
        body.light-theme .empty-state p { color:#5d5d5d !important; }
        body.light-theme .form-label,
        body.light-theme .page-header h2,
        body.light-theme .t th,
        body.light-theme .t td,
        body.light-theme .nav-item-btn,
        body.light-theme .modal-content,
        body.light-theme .form-control,
        body.light-theme .form-select { color:#1d1d28 !important; }
        body.light-theme #themeDrawer [style*="background:linear-gradient"][style*="color:#fff"] { color:#fff !important; }
        
        /* Force theme variables for dynamic inline styles in dark mode */
        body:not(.light-theme) [style*="color:#64748b"],
        body:not(.light-theme) [style*="color:#999"],
        body:not(.light-theme) [style*="color:#9ca3af"],
        body:not(.light-theme) [style*="color:#a0aec0"] { color: var(--txt-muted) !important; }
        
        body:not(.light-theme) [style*="background:#e8eaf0"],
        body:not(.light-theme) [style*="background:#f0f0f5"],
        body:not(.light-theme) [style*="background:#fafbfc"] { background: var(--bg-hover) !important; }
        
        body:not(.light-theme) [style*="border:1px solid #e8eaf0"],
        body:not(.light-theme) [style*="border:1px solid #f0f2f8"],
        body:not(.light-theme) [style*="border:1px solid #dde0f0"] { border-color: var(--bd-color) !important; }


        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(160deg, var(--bg-card), var(--bg-hover));
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 0;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,.15);
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar-brand h4 { margin: 0; font-weight: 700; font-size: 1.1rem; }
        .sidebar-brand small { opacity: .7; font-size: .75rem; }
        .sidebar-user {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar-user .avatar {
            width: 48px; height: 48px;
            background: rgba(255,255,255,.25);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 10px;
        }
        .sidebar-user h6 { margin: 0; font-size: .9rem; font-weight: 600; }
        .sidebar-user small { opacity: .75; font-size: .75rem; }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-item-btn {
            display: flex; align-items: center; gap: 12px;
            width: 100%; padding: 11px 14px;
            background: none; border: none; border-radius: 10px;
            color: rgba(255,255,255,.8); cursor: pointer;
            font-size: .88rem; font-weight: 500;
            transition: all .2s; text-align: left;
        }
        .nav-item-btn:hover { background: rgba(255,255,255,.12); color: #fff; transform: translateX(3px); }
        .nav-item-btn.active { background: rgba(255,255,255,.22); color: #fff; }
        .nav-item-btn i { font-size: 1.1rem; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.15);
        }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            padding: 32px;
        }
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px;
        }
        .page-header h2 { margin: 0; font-weight: 700; color: var(--txt-main); font-size: 1.5rem; }

        .btn-new {
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            color: #fff; border: none;
            padding: 10px 22px; border-radius: 10px;
            font-weight: 600; font-size: .9rem;
            display: flex; align-items: center; gap: 7px;
            cursor: pointer; transition: all .2s;
        }
        .btn-new:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(102,126,234,.4); }

        /* ── STAT CARDS ── */
        .stat-card {
            background: var(--bg-card); border-radius: 14px;
            padding: 22px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            border: 1px solid transparent;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(102,126,234,.22); border-color: rgba(102,126,234,.45); }
        .proyecto-card { background: var(--bg-card); transition: transform .2s, box-shadow .2s, border-color .2s; }
        .proyecto-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(102,126,234,.2); border-color: rgba(102,126,234,.4); background: var(--bg-card) !important; }
        /* Filas de listas (tareas, grupos, etc.) */
        .list-row { transition: background .18s, border-color .2s; cursor: pointer; }
        .list-row:hover { background: rgba(102,126,234,.12) !important; border-color: rgba(102,126,234,.5) !important; }
        .stat-icon { font-size: 2rem; margin-bottom: 10px; }
        .stat-num { font-size: 1.9rem; font-weight: 700; color: var(--txt-main); line-height: 1; }
        .stat-label { color: var(--txt-muted); font-size: .82rem; margin-top: 4px; }

        /* ── DIAGRAM CARDS ── */
        .diagram-card {
            background: var(--bg-card); border-radius: 14px;
            border: 1.5px solid var(--bd-color);
            overflow: hidden;
            transition: all .25s;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }
        .diagram-card:hover {
            border-color: var(--primary);
            box-shadow: 0 6px 22px rgba(102,126,234,.18);
            transform: translateY(-3px);
        }
        .card-preview {
            height: 130px;
            background: linear-gradient(135deg, var(--bg-hover) 0%, var(--bg-card) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 3.2rem;
            position: relative;
            overflow: hidden;
        }
        .card-preview::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(102,126,234,.08), rgba(118,75,162,.08));
        }
        .card-preview .preview-icon { position: relative; z-index: 1; opacity: .6; }
        .card-body-inner { padding: 14px 16px; }
        .card-title {
            font-weight: 600; color: var(--txt-main); font-size: .9rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 4px;
        }
        .card-meta { font-size: .75rem; color: var(--txt-muted); display: flex; gap: 10px; flex-wrap: wrap; }
        .card-footer-inner {
            padding: 10px 16px;
            border-top: 1px solid var(--bd-color);
            display: flex; justify-content: space-between; align-items: center;
        }
        .badge-tipo {
            font-size: .7rem; padding: 3px 9px; border-radius: 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            color: #fff; font-weight: 500;
        }
        .card-actions { display: flex; gap: 4px; }
        .card-actions .btn-icon {
            background: none; border: none;
            color: var(--txt-muted); font-size: .95rem;
            padding: 4px 7px; border-radius: 7px;
            cursor: pointer; transition: all .15s;
        }
        .card-actions .btn-icon:hover { background: var(--bg-hover); color: var(--primary); }
        .card-actions .btn-icon.danger:hover { background: #ffe5e5; color: #dc3545; }

        /* ── LUCIDCHART-STYLE CARD REDESIGN ── */
        .diagram-card {
            background: var(--bg-card);
            border-radius: 12px;
            border: 1.5px solid var(--bd-color);
            overflow: visible;
            transition: all .22s;
            cursor: default;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            position: relative;
        }
        .diagram-card:hover {
            border-color: var(--primary);
            box-shadow: 0 6px 24px rgba(102,126,234,.18);
            transform: translateY(-2px);
        }
        .lc-preview {
            height: 140px;
            background: var(--bg-deep);
            border-radius: 10px 10px 0 0;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            position: relative;
            border-bottom: 1px solid var(--bd-color);
            cursor: pointer;
        }
        .lc-preview:hover::after {
            content: 'Abrir';
            position: absolute; inset: 0;
            background: rgba(102,126,234,.13);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; color: var(--primary);
            border-radius: 10px 10px 0 0;
            pointer-events: none;
        }
        .lc-body {
            padding: 10px 12px 8px;
        }
        .lc-title {
            font-weight: 600; font-size: .88rem; color: var(--txt-main);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 2px;
        }
        .lc-meta {
            font-size: .7rem; color: var(--txt-muted);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .lc-footer {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 12px;
            border-top: 1px solid var(--bd-color);
        }
        .lc-btn-open {
            background: var(--primary); color: #fff;
            border: none; border-radius: 7px; padding: 5px 14px;
            font-size: .76rem; font-weight: 600; cursor: pointer;
            transition: opacity .15s;
            text-decoration: none; display: inline-block;
        }
        .lc-btn-open:hover { opacity: .88; color: #fff; }
        .lc-icon-btn {
            background: none; border: 1px solid var(--bd-color);
            color: var(--txt-muted); border-radius: 7px;
            width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: .85rem; transition: all .15s; flex-shrink: 0;
        }
        .lc-icon-btn:hover { border-color: var(--primary); color: var(--primary); background: rgba(102,126,234,.08); }
        .lc-dots-wrap { position: relative; margin-left: auto; }
        .lc-dropdown {
            position: absolute; bottom: calc(100% + 6px); right: 0;
            background: var(--bg-card); border: 1.5px solid var(--bd-color);
            border-radius: 10px; box-shadow: 0 8px 28px rgba(0,0,0,.18);
            min-width: 190px; z-index: 9999; overflow: hidden;
            animation: fadeInUp .15s ease;
        }
        body.light-theme .lc-dropdown { box-shadow: 0 8px 28px rgba(0,0,0,.12); }
        @keyframes fadeInUp {
            from { opacity:0; transform: translateY(6px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .lc-dd-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px; font-size: .82rem; color: var(--txt-main);
            cursor: pointer; transition: background .1s;
            white-space: nowrap;
        }
        .lc-dd-item:hover { background: var(--bg-hover); }
        .lc-dd-item i { font-size: .95rem; width: 16px; text-align:center; color: var(--txt-muted); }
        .lc-dd-item.danger { color: #ef4444; }
        .lc-dd-item.danger i { color: #ef4444; }
        .lc-dd-sep { height: 1px; background: var(--bd-color); margin: 4px 0; }

        /* ── SEARCH ── */
        .search-wrap { position: relative; }
        .search-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: var(--txt-muted); font-size: 1rem;
        }
        .search-input {
            padding: 10px 16px 10px 40px;
            border-radius: 10px; border: 1.5px solid var(--bd-color);
            font-size: .88rem; width: 100%; transition: border-color .2s;
            background: var(--bg-card); color: var(--txt-main);
        }
        .search-input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(102,126,234,.12); background: var(--bg-card); color: var(--txt-main); }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center; padding: 60px 20px;
            background: var(--bg-card); border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .empty-state i { font-size: 4rem; opacity: .25; color: var(--primary); margin-bottom: 16px; }
        .empty-state h5 { color: var(--txt-main); }
        .empty-state p  { color: var(--txt-muted); font-size: .88rem; }

        /* ── MODAL ── */
        .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            color: #fff; border-radius: 16px 16px 0 0; padding: 20px 24px;
        }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--bd-color); }
        .form-control, .form-select {
            border-radius: 10px; padding: 10px 14px;
            border: 1.5px solid var(--bd-color); font-size: .88rem;
            background: var(--bg-card); color: var(--txt-main);
            transition: border-color .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary); outline: none;
            box-shadow: 0 0 0 3px rgba(102,126,234,.12);
            background: var(--bg-card); color: var(--txt-main);
        }
        .form-label { font-weight: 600; font-size: .85rem; color: var(--txt-muted); margin-bottom: 6px; }
        .btn-confirm {
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            border: none; color: #fff; padding: 10px 24px;
            border-radius: 10px; font-weight: 600; cursor: pointer; transition: all .2s;
        }
        .btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(102,126,234,.35); }
        .btn-cancel {
            background: var(--bg-hover); border: none; color: var(--txt-main);
            padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer;
        }

        /* ── PAGINATION ── */
        .pagination .page-link {
            color: var(--primary); border-radius: 8px; margin: 0 2px;
            border: 1.5px solid #e0e2ea;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            border-color: var(--primary); color: #fff;
        }

        /* ── TOAST ── */
        #toast-container {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
        }
        .toast-msg {
            padding: 12px 20px; border-radius: 10px; font-size: .88rem; font-weight: 500;
            box-shadow: 0 4px 16px rgba(0,0,0,.15); animation: slideIn .3s ease;
            max-width: 320px;
        }
        .toast-msg.success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .toast-msg.error   { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .toast-msg.info    { background: #dbeafe; color: #1e40af; border-left: 4px solid #3b82f6; }
        @keyframes slideIn { from { opacity:0; transform: translateX(30px); } to { opacity:1; transform: translateX(0); } }

        /* ── DISTRIBUTION BAR ── */
        .dist-bar { height: 8px; border-radius: 4px; background: #e0e2ea; overflow: hidden; margin-top: 4px; }
        .dist-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--primary2)); border-radius: 4px; transition: width .6s ease; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; padding: 16px; }
        }

        /* ── PLANTILLAS ── */
        .plantilla-card {
            cursor: pointer;
            transition: all .2s;
            border: 1px solid #e0e2ea;
        }
        .plantilla-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(102,126,234,.15);
            border-color: var(--primary);
        }
        .plantilla-card .card-title {
            font-size: .9rem;
            font-weight: 600;
        }
        .plantilla-card .card-text {
            font-size: .8rem;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-person-fill me-2"></i>Panel Alumno</h4>
        <small>DiagramasUML</small>
    </div>
    <div class="sidebar-user">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div>
            <h6><?= htmlspecialchars(SessionManager::usuarioNombre()) ?></h6>
            <small><?= htmlspecialchars($_SESSION['email'] ?? '') ?></small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div style="font-size:.65rem;font-weight:700;letter-spacing:.08em;opacity:.5;padding:10px 12px 4px;text-transform:uppercase">Principal</div>
        <button class="nav-item-btn active" id="nav-dashboard" onclick="switchView('dashboard')">
            <i class="bi bi-speedometer2"></i> Inicio
        </button>
        <button class="nav-item-btn" id="nav-diagramas" onclick="switchView('diagramas')">
            <i class="bi bi-diagram-3"></i> Mis Diagramas
        </button>
        <button class="nav-item-btn" id="nav-proyectos" onclick="switchView('proyectos')">
            <i class="bi bi-folder2-open"></i> Proyectos
        </button>
        <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
        <hr style="border-color:rgba(255,255,255,.15);margin:10px 0">
        <a href="<?= BASE_URL ?>/admin" class="nav-item-btn text-decoration-none" style="color:rgba(255,255,255,.8)">
            <i class="bi bi-shield-fill-check" style="color:#667eea"></i> Panel Admin
        </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <button class="nav-item-btn" onclick="abrirModalNuevoDiagramaAlumno()" style="color:rgba(255,255,255,.85)"><i class="bi bi-plus-square"></i> Nuevo Diagrama</button>
        <button class="nav-item-btn" onclick="toggleThemeDrawer()" style="color:rgba(255,255,255,.7)"><i class="bi bi-palette"></i> Colores &amp; Tema</button>
        <a href="<?= BASE_URL ?>/logout" class="nav-item-btn text-decoration-none" style="color:rgba(255,255,255,.7)">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>
</aside>
<!-- ══ THEME DRAWER ══ -->
<div id="themeDrawer" style="position:fixed;top:0;right:-340px;width:320px;height:100vh;background:var(--bg-card);border-left:1px solid var(--bd-color);z-index:9000;overflow-y:auto;transition:right .3s ease;padding:20px 16px;box-shadow:-6px 0 24px rgba(0,0,0,.5)">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <span style="color:#fff;font-weight:700;font-size:.95rem"><i class="bi bi-palette me-2" style="color:var(--primary)"></i>Apariencia</span>
        <button onclick="toggleThemeDrawer()" style="background:none;border:none;color:#888;font-size:1.2rem;cursor:pointer;padding:4px"><i class="bi bi-x-lg"></i></button>
    </div>
    <div id="dashThemeContainer"></div>
</div>
<div id="themeOverlay" onclick="toggleThemeDrawer()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:8999"></div>

<!-- ══ MAIN ══ -->
<main class="main">
    <div class="page-header">
        <h2 id="pageTitle">Dashboard</h2>
        <div style="display:flex;align-items:center;gap:10px;margin-left:auto">
            <!-- Búsqueda global -->
            <div style="position:relative">
                <input type="text" id="globalSearch" placeholder="Buscar..." autocomplete="off"
                    style="background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.15);border-radius:20px;color:var(--txt-main);padding:6px 14px 6px 34px;font-size:.82rem;width:200px;outline:none;transition:all .2s"
                    onfocus="this.style.width='280px';this.style.borderColor='rgba(102,126,234,.6)'"
                    onblur="this.style.width='200px';this.style.borderColor='rgba(255,255,255,.15)'"
                    oninput="busquedaGlobalHandler(this.value)">
                <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#888;font-size:.85rem;pointer-events:none"></i>
                <div id="searchDropdown" style="display:none;position:absolute;top:calc(100% + 6px);left:0;width:340px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.4);z-index:1000;max-height:360px;overflow-y:auto"></div>
            </div>
            <!-- Campana de notificaciones -->
            <div style="position:relative">
                <button id="btnNotif" onclick="toggleNotifPanel()"
                    style="background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.15);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;transition:all .2s"
                    onmouseover="this.style.background='rgba(102,126,234,.2)'" onmouseout="this.style.background='rgba(255,255,255,.08)'">
                    <i class="bi bi-bell" style="color:var(--txt-main);font-size:.95rem"></i>
                    <span id="notifBadge" style="display:none;position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;border-radius:50%;width:16px;height:16px;font-size:.6rem;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1"></span>
                </button>
                <div id="notifPanel" style="display:none;position:absolute;top:calc(100% + 8px);right:0;width:320px;background:var(--bg-card);border:1px solid var(--bd-color);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.4);z-index:1001;max-height:420px;overflow-y:auto">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;justify-content:space-between">
                        <span style="font-weight:700;font-size:.88rem;color:var(--txt-main)"><i class="bi bi-bell me-2" style="color:var(--primary)"></i>Notificaciones</span>
                        <button onclick="marcarTodasLeidas()" style="background:none;border:none;color:var(--primary);font-size:.72rem;cursor:pointer">Marcar todas leídas</button>
                    </div>
                    <div id="notifLista"><div style="text-align:center;padding:20px;color:var(--txt-muted);font-size:.82rem">Cargando...</div></div>
                </div>
            </div>
            <button class="btn-new" onclick="abrirModalNuevo()">
                <i class="bi bi-plus-lg"></i> Nuevo Diagrama
            </button>
        </div>
    </div>
    <div id="contentArea"></div>
</main>

<!-- ══ TOAST CONTAINER ══ -->
<div id="toast-container"></div>

<!-- ══ MODAL NUEVO / EDITAR DIAGRAMA ══ -->
<div class="modal fade" id="modalDiagrama" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitulo">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId" value="">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-type me-1"></i>Título</label>
                    <input type="text" class="form-control" id="fTitulo" placeholder="Ej: Diagrama de login">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-diagram-3 me-1"></i>Tipo de Diagrama</label>
                    <input type="hidden" id="fTipo" value="usecase">
                    <div id="tipoPickerGrid" style="max-height:280px;overflow-y:auto;padding-right:4px"><div style="text-align:center;padding:20px;color:var(--txt-muted);font-size:.82rem"><div class="spinner-border spinner-border-sm me-2"></div>Cargando tipos...</div></div>
                </div>
                <!-- Proyecto requerido — se carga dinámicamente al abrir el modal -->
                <div class="mb-3" id="fProyectoWrap">
                    <label class="form-label"><i class="bi bi-diagram-3 me-1" style="color:var(--primary)"></i>Ligar a un proyecto <span class="text-danger fw-semibold">(requerido)</span></label>
                    <select class="form-select" id="fProyecto">
                        <option value="">— Selecciona un proyecto —</option>
                    </select>
                    <div class="form-text text-muted mt-1"><i class="bi bi-info-circle"></i>Selecciona un proyecto existente para crear el diagrama. No se permiten diagramas libres.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-text-paragraph me-1"></i>Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="fDescripcion" rows="2" placeholder="Breve descripción..."></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label"><i class="bi bi-tags me-1"></i>Etiquetas <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="text" class="form-control" id="fEtiquetas" placeholder="proyecto, trabajo, personal">
                    <div class="form-text text-muted mt-1"><i class="bi bi-info-circle"></i> Separa con comas</div>
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-confirm" id="btnModalAction" onclick="accionModal()">
                    <i class="bi bi-pencil-square me-1"></i>Ir al Editor
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL CONFIRMAR ELIMINAR ══ -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#dc3545;">
                <h5 class="modal-title text-white"><i class="bi bi-trash3 me-2"></i>Eliminar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                <p class="mt-3 mb-1 fw-600">¿Eliminar este diagrama?</p>
                <small class="text-muted" id="eliminarNombre"></small>
                <p class="text-danger mt-2 mb-0" style="font-size:.8rem;">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger px-4" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= Assets::bootstrapJs() ?>"></script>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
<script src="<?= Assets::url('js/user-theme.js') ?>"></script>
<script>
// ── ESTADO GLOBAL ──────────────────────────────────────────────
const MI_USER_ID = <?= (int)SessionManager::usuarioId() ?>;

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

const TIPOS = {
    // ── Estructurales ──────────────────────────────────
    class:         { label: 'Clases',                icon: () => getTipoIconoSVG('class', 32) },
    object:        { label: 'Objetos',               icon: () => getTipoIconoSVG('object', 32) },
    package:       { label: 'Paquetes',              icon: () => getTipoIconoSVG('package', 32) },
    composite:     { label: 'Estructura Compuesta',  icon: () => getTipoIconoSVG('composite', 32) },
    component:     { label: 'Componentes',           icon: () => getTipoIconoSVG('component', 32) },
    deployment:    { label: 'Despliegue',            icon: () => getTipoIconoSVG('deployment', 32) },
    profile:       { label: 'Perfiles',              icon: () => getTipoIconoSVG('profile', 32) },
    // ── Comportamiento ──────────────────────────────────
    usecase:       { label: 'Casos de Uso',          icon: () => getTipoIconoSVG('usecase', 32) },
    activity:      { label: 'Actividades',           icon: () => getTipoIconoSVG('activity', 32) },
    state:         { label: 'Máquina de Estado',     icon: () => getTipoIconoSVG('state', 32) },
    // ── Interacción ──────────────────────────────────────
    sequence:      { label: 'Secuencia',             icon: () => getTipoIconoSVG('sequence', 32) },
    communication: { label: 'Comunicación',          icon: () => getTipoIconoSVG('communication', 32) },
    timing:        { label: 'Tiempos',               icon: () => getTipoIconoSVG('timing', 32) },
    overview:      { label: 'Descripción General',   icon: () => getTipoIconoSVG('overview', 32) },
};
const TIPO_ICONS_BI = {
    usecase: 'bi-person-circle', class: 'bi-box', sequence: 'bi-arrow-left-right',
    activity: 'bi-activity', state: 'bi-diagram-3', component: 'bi-cpu',
    deployment: 'bi-hdd-network', object: 'bi-table', communication: 'bi-chat-dots', timing: 'bi-clock'
};

let modalDiagrama, modalEliminar;
let pendingDeleteId  = null;
let currentPage      = 1;
let currentSearch    = '';
let searchTimer      = null;

// ── Estado de navegación persistente ──────────────────────────
// Guarda en sessionStorage qué vista y qué proyecto estaba abierto
// para restaurar la posición exacta al volver del editor.
const NAV_KEY = 'dash_nav_state';

function saveNavState(extra = {}) {
    if (!extra.fromEditor) return;
    // Persist theme so it survives the editor redirect
    if (window._themeConfig) sessionStorage.setItem('_uth_session', JSON.stringify(window._themeConfig));
    const state = {
        view: document.querySelector('.nav-item-btn.active')?.id?.replace('nav-','') || 'dashboard',
        proyectoId: _proyectoActual?.proyecto?.id || null,
        proyectoTab: document.getElementById('tabPD')?.style.borderBottomColor?.includes('var') ? 'diagramas' : 'archivos',
        ...extra
    };
    sessionStorage.setItem(NAV_KEY, JSON.stringify(state));
}

async function restoreNavState() {
    try {
        const raw = sessionStorage.getItem(NAV_KEY);
        if (!raw) { switchView('dashboard'); return; }
        const state = JSON.parse(raw);
        if (!state.fromEditor) { switchView(state.view || 'dashboard'); return; }
        sessionStorage.removeItem(NAV_KEY);
        if (state.view === 'proyectos' && state.proyectoId) {
            // Activar pestaña proyectos sin llamar a renderProyectos completo primero
            document.querySelectorAll('.nav-item-btn').forEach(b => b.classList.remove('active'));
            const btn = document.getElementById('nav-proyectos');
            if (btn) btn.classList.add('active');
            document.getElementById('pageTitle').textContent = 'Proyectos';
            // Abrir directamente el proyecto
            await abrirProyecto(state.proyectoId);
            // Restaurar tab si aplica
            if (state.proyectoTab === 'archivos') setTimeout(() => setProyTab('archivos'), 100);
        } else {
            switchView(state.view || 'dashboard');
        }
    } catch(_) {
        switchView('dashboard');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    modalDiagrama = new bootstrap.Modal(document.getElementById('modalDiagrama'));
    modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));

    document.getElementById('btnConfirmarEliminar').addEventListener('click', confirmarEliminar);

    // Cerrar notifPanel y searchDropdown al clic fuera
    document.addEventListener('click', e => {
        if (!e.target.closest('#btnNotif') && !e.target.closest('#notifPanel')) {
            document.getElementById('notifPanel')?.style && (document.getElementById('notifPanel').style.display = 'none');
        }
        if (!e.target.closest('#globalSearch') && !e.target.closest('#searchDropdown')) {
            const d = document.getElementById('searchDropdown');
            if (d) d.style.display = 'none';
        }
    });

    // Cargar notificaciones al inicio y cada 60s
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 60000);

    // Restaurar posición si se viene del editor
    restoreNavState();
    // También restaurar al volver via back/forward (bfcache)
    window.addEventListener('pageshow', (e) => { try { restoreNavState(); } catch(_) {} });
});

// ── NOTIFICACIONES ──────────────────────────────────────────────────
async function cargarNotificaciones() {
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/notificaciones');
        const notifs = data.notificaciones || [];
        const noLeidas = data.no_leidas || 0;
        const badge = document.getElementById('notifBadge');
        if (badge) {
            badge.textContent = noLeidas > 9 ? '9+' : noLeidas;
            badge.style.display = noLeidas > 0 ? 'flex' : 'none';
        }
        // Actualizar panel si está abierto
        if (document.getElementById('notifPanel')?.style.display !== 'none') {
            renderNotifLista(notifs);
        }
    } catch(_) {}
}

function renderNotifLista(notifs) {
    const cont = document.getElementById('notifLista');
    if (!cont) return;
    const iconos = { tarea:'bi-clipboard-check', calificacion:'bi-star-fill', observacion:'bi-chat-left-text', info:'bi-info-circle' };
    const colores = { tarea:'#667eea', calificacion:'#f59e0b', observacion:'#10b981', info:'#888' };
    if (!notifs.length) {
        cont.innerHTML = `<div style="text-align:center;padding:24px;color:var(--txt-muted);font-size:.82rem">
            <i class="bi bi-bell-slash" style="font-size:1.8rem;display:block;margin-bottom:8px;opacity:.3"></i>Sin notificaciones
        </div>`;
        return;
    }
    cont.innerHTML = notifs.map(n => {
        const icon  = iconos[n.tipo]  || iconos.info;
        const color = colores[n.tipo] || colores.info;
        const fecha = new Date(n.fecha).toLocaleString('es-MX',{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
        return `<div style="display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--bd-color);${n.leida=='1'?'opacity:.6':''}cursor:pointer;transition:background .15s"
                     onmouseover="this.style.background='rgba(102,126,234,.12)'" onmouseout="this.style.background=''"
                     onclick="leerNotif(${n.id},'${escHtml(n.url||'')}','${n.tipo}')">
            <div style="width:32px;height:32px;background:${color}20;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi ${icon}" style="color:${color};font-size:.85rem"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:.8rem;color:var(--txt-main)">${escHtml(n.titulo)}</div>
                ${n.mensaje?`<div style="font-size:.72rem;color:var(--txt-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escHtml(n.mensaje)}</div>`:''}
                <div style="font-size:.65rem;color:var(--txt-muted);margin-top:2px">${fecha}</div>
            </div>
            ${n.leida=='0'?`<div style="width:8px;height:8px;background:#667eea;border-radius:50%;flex-shrink:0;margin-top:4px"></div>`:''}
        </div>`;
    }).join('');
}

async function toggleNotifPanel() {
    const panel = document.getElementById('notifPanel');
    if (!panel) return;
    const visible = panel.style.display !== 'none';
    panel.style.display = visible ? 'none' : 'block';
    if (!visible) {
        const data = await apiFetch('<?= BASE_URL ?>/api/notificaciones').catch(()=>({notificaciones:[]}));
        renderNotifLista(data.notificaciones || []);
    }
}

async function leerNotif(id, url, tipo) {
    await apiFetch('<?= BASE_URL ?>/api/notificaciones/leer', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) }).catch(()=>{});
    await cargarNotificaciones();
    // Close notif panel
    const np = document.getElementById('notifPanel');
    if (np) np.style.display = 'none';

    if (tipo === 'observacion' || tipo === 'reporte_error') {
        if (_proyectoActual?.proyecto?.id) {
            // Ya hay proyecto abierto → ir a pestaña observaciones
            switchView('proyectos');
            setTimeout(() => setProyTab('observaciones'), 300);
        } else {
            // No hay proyecto → ir a la lista de proyectos
            switchView('proyectos');
            toast('Abre el proyecto para ver las observaciones', 'info');
        }
        return;
    }
    if (tipo === 'tarea' || tipo === 'calificacion') {
        switchView('proyectos');
        toast('Abre el proyecto y ve a la pestaña Tareas', 'info');
        return;
    }
    if (url && url !== 'null' && url !== '') window.location.href = url;
}

async function marcarTodasLeidas() {
    await apiFetch('<?= BASE_URL ?>/api/notificaciones/leer-todas', { method:'POST', headers:{'Content-Type':'application/json'}, body:'{}' }).catch(()=>{});
    await cargarNotificaciones();
    const panel = document.getElementById('notifPanel');
    if (panel && panel.style.display !== 'none') {
        const data = await apiFetch('<?= BASE_URL ?>/api/notificaciones').catch(()=>({notificaciones:[]}));
        renderNotifLista(data.notificaciones || []);
    }
}

// ── BÚSQUEDA GLOBAL ─────────────────────────────────────────────────
let _searchTimer = null;
async function busquedaGlobalHandler(q) {
    clearTimeout(_searchTimer);
    const drop = document.getElementById('searchDropdown');
    if (!drop) return;
    if (!q || q.length < 2) { drop.style.display = 'none'; return; }
    drop.style.display = 'block';
    drop.innerHTML = '<div style="padding:12px;text-align:center;color:#888;font-size:.8rem">Buscando...</div>';
    _searchTimer = setTimeout(async () => {
        try {
            const data = await apiFetch(`<?= BASE_URL ?>/api/busqueda?q=${encodeURIComponent(q)}`);
            const res = data.resultados || [];
            if (!res.length) {
                drop.innerHTML = '<div style="padding:16px;text-align:center;color:#888;font-size:.8rem">Sin resultados para "' + escHtml(q) + '"</div>';
                return;
            }
            const iconos = { diagrama:'bi-diagram-3', proyecto:'bi-folder2-open', archivo:'bi-file-earmark', observacion:'bi-chat-left-text' };
            const colores = { diagrama:'#667eea', proyecto:'#10b981', archivo:'#f59e0b', observacion:'#888' };
            drop.innerHTML = res.map(r => {
                const icon  = iconos[r.tipo]  || 'bi-search';
                const color = colores[r.tipo] || '#888';
                const accion = r.tipo === 'diagrama' ? `saveNavState({fromEditor:true});window.location.href='<?= BASE_URL ?>/editor?id=${r.id}'`
                             : r.tipo === 'proyecto' ? `switchView('proyectos');setTimeout(()=>abrirProyecto(${r.id}),200);document.getElementById('searchDropdown').style.display='none'`
                             : `document.getElementById('searchDropdown').style.display='none'`;
                return `<div style="display:flex;align-items:center;gap:10px;padding:9px 14px;cursor:pointer;border-bottom:1px solid var(--bd-color);transition:background .15s"
                             onmouseover="this.style.background='rgba(102,126,234,.12)'" onmouseout="this.style.background=''"
                             onclick="${accion}">
                    <i class="bi ${icon}" style="color:${color};font-size:1rem;flex-shrink:0"></i>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.82rem;font-weight:600;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escHtml((r.nombre||'').slice(0,60))}</div>
                        <div style="font-size:.68rem;color:#888">${r.tipo} ${r.subtipo?'· '+r.subtipo:''}</div>
                    </div>
                </div>`;
            }).join('');
        } catch(e) {
            drop.innerHTML = '<div style="padding:12px;color:#ef4444;font-size:.8rem">Error: ' + escHtml(e.message) + '</div>';
        }
    }, 350);
}

// ── NAVEGACIÓN ─────────────────────────────────────────────────
function switchView(view) {
    document.querySelectorAll('.nav-item-btn').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById('nav-' + view);
    if (btn) btn.classList.add('active');

    const titles = {
        dashboard: 'Inicio', diagramas: 'Mis Diagramas', proyectos: 'Proyectos', tareas: 'Mis Tareas', observaciones: 'Observaciones'
    };
    document.getElementById('pageTitle').textContent = titles[view] || view;

    const btnNew = document.querySelector('.btn-new');
    if (btnNew) btnNew.style.display = '';

    // Limpiar proyecto actual al salir de proyectos o al ver el listado general
    if (view !== 'proyectos' && view !== 'observaciones' && view !== 'tareas') _proyectoActual = null;

    sessionStorage.setItem(NAV_KEY, JSON.stringify({ view, proyectoId: _proyectoActual?.proyecto?.id || null }));

    const views = { dashboard: renderDashboard, diagramas: renderDiagramas, proyectos: renderProyectos, tareas: renderTareasAlumno, observaciones: renderObservaciones };
    if (views[view]) views[view]();
}

async function renderObservaciones() {
    const cont = document.getElementById('mainContent');
    if (!cont) return;
    // If no active project, try to auto-load the most recent one
    if (!_proyectoActual?.proyecto?.id) {
        cont.innerHTML = '<div style="text-align:center;padding:40px"><div class="spinner-border text-primary"></div><p class="mt-3" style="color:var(--txt-muted)">Cargando proyecto activo...</p></div>';
        try {
            const pd = await apiFetch('<?= BASE_URL ?>/api/proyectos');
            const proyectos = pd.proyectos || [];
            if (proyectos.length === 0) {
                cont.innerHTML = '<div style="text-align:center;padding:60px;color:var(--txt-muted)"><i class="bi bi-folder-x" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px"></i><h5>Sin proyectos</h5><p style="font-size:.88rem">Únete a un proyecto para ver observaciones de tus diagramas.</p></div>';
                return;
            }
            // Auto-load first project
            const p = proyectos[0];
            const det = await apiFetch(`<?= BASE_URL ?>/api/proyectos/${p.id}`);
            _proyectoActual = det;
            window._proyectoDiagsAlumno = det.diagramas || [];
        } catch(e) {
            cont.innerHTML = '<div style="color:#ef4444;padding:20px">Error al cargar proyecto: ' + escHtml(e.message) + '</div>';
            return;
        }
    }
    const pid = _proyectoActual?.proyecto?.id;
    const panel = document.getElementById('obsAlumnoPanel') || cont;
    // Render header
    cont.innerHTML = `
        <div style="max-width:860px;margin:0 auto">
            <div style="margin-bottom:20px">
                <h4 style="margin:0;color:var(--txt-main);font-size:1.05rem;font-weight:700"><i class="bi bi-eye me-2" style="color:var(--primary)"></i>Observaciones del maestro</h4>
                <p style="color:var(--txt-muted);font-size:.82rem;margin:6px 0 0">Proyecto: <strong>${escHtml(_proyectoActual?.proyecto?.nombre||'—')}</strong> — revisa los comentarios y corrígelos desde el editor.</p>
            </div>
            <div id="obsAlumnoPanel"></div>
        </div>`;
    await cargarObservacionesAlumno();
}

async function apiFetch(url, opts = {}) {
    const res  = await fetch(url, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', ...(opts.headers || {}) },
        ...opts
    });
    const text = await res.text();
    if (!text.trim()) {
        if (res.redirected || res.url.includes('/login')) throw new Error('Sesión expirada. Vuelve a iniciar sesión.');
        throw new Error('Sin respuesta del servidor');
    }
    try { return JSON.parse(text); }
    catch { throw new Error('Respuesta inválida del servidor: ' + text.substring(0, 120)); }
}

// ══════════════════════════════════════════════════════════════
// DASHBOARD
// ══════════════════════════════════════════════════════════════
async function renderDashboard() {
    document.getElementById('contentArea').innerHTML = `
        <div class="row g-3 mb-4" id="statsRow">
            <div class="col-6 col-md-4"><div class="stat-card">
                <div class="stat-icon text-primary"><i class="bi bi-folder2-open"></i></div>
                <div class="stat-num" id="s-proyectos">-</div>
                <div class="stat-label">Proyectos activos</div>
            </div></div>
            <div class="col-6 col-md-4"><div class="stat-card">
                <div class="stat-icon text-success"><i class="bi bi-diagram-3"></i></div>
                <div class="stat-num" id="s-diagramas">-</div>
                <div class="stat-label">Mis Diagramas</div>
            </div></div>
            <div class="col-6 col-md-4"><div class="stat-card">
                <div class="stat-icon text-warning"><i class="bi bi-hdd-stack"></i></div>
                <div class="stat-num" id="s-espacio">-</div>
                <div class="stat-label">Espacio usado</div>
                <div id="s-espacio-bar" style="margin-top:8px;display:none">
                    <div style="background:var(--bd-color);border-radius:4px;height:5px;overflow:hidden;margin-bottom:3px">
                        <div id="s-espacio-fill" style="height:100%;width:0%;border-radius:4px;transition:width .5s;background:#10b981"></div>
                    </div>
                    <div id="s-espacio-label" style="font-size:.65rem;color:var(--txt-muted);text-align:center"></div>
                </div>
            </div></div>
        </div>

        <!-- Proyectos recientes -->
        <div class="stat-card mb-3" id="proyRecCard">
            <h6 style="font-weight:700;margin-bottom:14px;font-size:.9rem;color:var(--txt-main)">
                <i class="bi bi-folder2-open me-2" style="color:var(--primary)"></i>Proyectos recientes
                <button onclick="switchView('proyectos')" style="float:right;background:none;border:none;color:var(--primary);font-size:.78rem;cursor:pointer;font-weight:600">Ver todos →</button>
            </h6>
            <div id="proyRecList"><div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div></div>
        </div>

        <!-- Diagramas recientes -->
        <div class="stat-card" id="diagRecCard">
            <h6 style="font-weight:700;margin-bottom:14px;font-size:.9rem;color:var(--txt-main)">
                <i class="bi bi-clock me-2" style="color:var(--primary)"></i>Diagramas recientes
                <button onclick="switchView('diagramas')" style="float:right;background:none;border:none;color:var(--primary);font-size:.78rem;cursor:pointer;font-weight:600">Ver todos →</button>
            </h6>
            <div id="diagRecList"><div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div></div>
        </div>`;

    try {
        const [dataD, dataP] = await Promise.all([
            apiFetch('<?= BASE_URL ?>/api/diagramas'),
            apiFetch('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos')
        ]);

        // Stats
        const s = dataD.estadisticas || {};
        const proyectos = dataP.proyectos || [];
        document.getElementById('s-proyectos').textContent = proyectos.length;
        document.getElementById('s-diagramas').textContent = s.total_diagramas || 0;

        const espacioUsado = s.espacio_usado || 0;
        const limiteMb = s.espacio_limite_mb ?? 100;
        document.getElementById('s-espacio').textContent = formatBytes(espacioUsado);
        if (limiteMb > 0) {
            const pct = Math.min(100, (espacioUsado / (limiteMb * 1024 * 1024)) * 100);
            const fill = document.getElementById('s-espacio-fill');
            fill.style.width = pct.toFixed(1) + '%';
            fill.style.background = pct > 90 ? '#ef4444' : pct > 70 ? '#f59e0b' : '#10b981';
            document.getElementById('s-espacio-label').textContent = `${pct.toFixed(1)}% de ${limiteMb} MB`;
            document.getElementById('s-espacio-bar').style.display = 'block';
        }

        // Proyectos recientes
        const proyEl = document.getElementById('proyRecList');
        if (proyectos.length === 0) {
            proyEl.innerHTML = `<div style="text-align:center;padding:20px;color:var(--txt-muted)">
                <i class="bi bi-folder2-open" style="font-size:1.8rem;opacity:.3;display:block;margin-bottom:8px"></i>
                <div style="font-size:.85rem">No tienes proyectos aún</div>
                <button onclick="switchView('proyectos')" class="btn-new" style="margin-top:10px;font-size:.8rem;padding:7px 16px">
                    <i class="bi bi-plus-lg me-1"></i>Crear o unirse a un proyecto
                </button>
            </div>`;
        } else {
            proyEl.innerHTML = proyectos.slice(0, 5).map(p => `
                <div onclick="switchView('proyectos');setTimeout(()=>abrirProyecto(${p.id}),250)"
                     style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--bd-color);cursor:pointer;transition:opacity .15s"
                     onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                    <div style="width:38px;height:38px;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="bi bi-folder2-open" style="color:#fff"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:700;font-size:.87rem;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escHtml(p.nombre)}</div>
                        <div style="font-size:.72rem;color:var(--txt-muted)">
                            <i class="bi bi-people me-1"></i>${p.num_miembros} miembro${p.num_miembros!=1?'s':''}
                            &nbsp;·&nbsp;<i class="bi bi-diagram-3 me-1"></i>${p.num_diagramas} diagrama${p.num_diagramas!=1?'s':''}
                        </div>
                    </div>
                    <span style="background:${p.rol==='owner'?'rgba(102,126,234,.15)':'rgba(16,185,129,.1)'};color:${p.rol==='owner'?'var(--primary)':'#10b981'};border-radius:8px;padding:2px 8px;font-size:.62rem;font-weight:700;flex-shrink:0">${p.rol==='owner'?'OWNER':'MIEMBRO'}</span>
                    <i class="bi bi-chevron-right" style="color:var(--txt-muted);font-size:.8rem;flex-shrink:0"></i>
                </div>`).join('');
        }

        // Diagramas recientes
        const diagEl = document.getElementById('diagRecList');
        const diags = (dataD.diagramas || []).slice(0, 6);
        if (diags.length === 0) {
            diagEl.innerHTML = `<div style="text-align:center;padding:20px;color:var(--txt-muted)">
                <i class="bi bi-diagram-3" style="font-size:1.8rem;opacity:.3;display:block;margin-bottom:8px"></i>
                <div style="font-size:.85rem">No tienes diagramas aún</div>
                <button onclick="abrirModalNuevo()" class="btn-new" style="margin-top:10px;font-size:.8rem;padding:7px 16px">
                    <i class="bi bi-plus-lg me-1"></i>Crear diagrama
                </button>
            </div>`;
        } else {
            diagEl.innerHTML = diags.map(d => {
                const tipoInfo = TIPOS[d.tipo_diagrama] || { label: d.tipo_diagrama, icon: '📄' };
                const fecha = new Date(d.fecha_modificacion || d.fecha_creacion);
                return `<div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--bd-color);cursor:pointer;transition:opacity .15s"
                         onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'"
                         onclick="abrirDiagrama(${d.id})">
                    <div style="width:36px;height:36px;flex-shrink:0">${getTipoIconoSVG(d.tipo_diagrama, 36)}</div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:.87rem;color:var(--txt-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escHtml(d.titulo)}</div>
                        <div style="font-size:.72rem;color:var(--txt-muted)">${tipoInfo.label} · v${d.version||1}</div>
                    </div>
                    <div style="font-size:.72rem;color:var(--txt-muted);flex-shrink:0">${fecha.toLocaleDateString('es-MX')}</div>
                    <i class="bi bi-pencil-square" style="color:var(--txt-muted);font-size:.85rem;flex-shrink:0"></i>
                </div>`;
            }).join('');
        }
    } catch(e) {
        toast('Error: ' + e.message, 'error');
    }
}

// ══════════════════════════════════════════════════════════════
// MIS DIAGRAMAS
// ══════════════════════════════════════════════════════════════
async function renderDiagramas() {
    currentPage   = 1;
    currentSearch = '';
    document.getElementById('contentArea').innerHTML = `
        <div class="d-flex gap-3 mb-4 flex-wrap">
            <div class="search-wrap flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="Buscar por título, descripción o etiquetas..."
                       oninput="onSearch(this.value)">
            </div>
            <select class="form-select" style="width:auto;border-radius:10px;border:1.5px solid var(--bd-color);font-size:.88rem;background:var(--bg-card);color:var(--txt-main)" id="filterTipo" onchange="currentPage=1;cargarDiagramas()">
                <option value="">Todos los tipos</option>
                ${Object.entries(TIPOS).map(([v,t]) => `<option value="${v}">${t.label}</option>`).join('')}
            </select>
        </div>
        <div id="diagramasGrid"></div>
        <div id="paginationWrap" class="d-flex justify-content-center mt-4"></div>`;

    await cargarDiagramas();
}

function onSearch(val) {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { currentSearch = val; currentPage = 1; cargarDiagramas(); }, 400);
}

async function cargarDiagramas() {
    const grid = document.getElementById('diagramasGrid');
    if (!grid) return; // no estamos en la vista Mis Diagramas
    grid.innerHTML = '<div style="text-align:center;padding:40px"><div class="spinner-border text-primary"></div></div>';

    try {
        const tipo   = document.getElementById('filterTipo')?.value || '';
        const params = new URLSearchParams({ filtro: currentSearch, pagina: currentPage });
        if (tipo) params.set('tipo', tipo);

        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas?' + params);
        if (!data.success) throw new Error(data.error || 'Error');

        const diagramas = data.diagramas || [];

        if (diagramas.length === 0) {
            grid.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-diagram-3"></i>
                    <h5>No hay diagramas${currentSearch ? ' que coincidan' : ''}</h5>
                    <p>${currentSearch ? 'Intenta con otra búsqueda' : 'Crea tu primer diagrama para comenzar'}</p>
                    ${!currentSearch ? `<button class="btn-new" onclick="abrirModalNuevo()"><i class="bi bi-plus-lg"></i> Nuevo Diagrama</button>` : ''}
                </div>`;
        } else {
            grid.innerHTML = `<div class="row g-3">${diagramas.map(renderCardDiagrama).join('')}</div>`;
            if (window.DiagramMiniRenderer) DiagramMiniRenderer.observeAll(grid);
        }

        // Paginación (si hay estadísticas con total)
        renderPaginacion(data.total || diagramas.length, currentPage);

    } catch (e) {
        grid.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>${e.message}</div>`;
        toast(e.message, 'error');
    }
}

// ══════════════════════════════════════════════════════════
// CARD ESTILO LUCIDCHART — usa DiagramMiniRenderer para preview
// ══════════════════════════════════════════════════════════

let _activeDropdown = null;

function cerrarDropdowns(e) {
    if (_activeDropdown && !_activeDropdown.contains(e.target)) {
        _activeDropdown.querySelector('.lc-dropdown')?.remove();
        _activeDropdown = null;
    }
}
document.addEventListener('click', cerrarDropdowns);

function toggleDDiagrama(e, id, titulo) {
    e.stopPropagation();
    const wrap = e.currentTarget.closest('.lc-dots-wrap');
    if (_activeDropdown === wrap) {
        wrap.querySelector('.lc-dropdown')?.remove();
        _activeDropdown = null;
        return;
    }
    if (_activeDropdown) {
        _activeDropdown.querySelector('.lc-dropdown')?.remove();
    }
    _activeDropdown = wrap;
    const tEsc = titulo.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
    const dd = document.createElement('div');
    dd.className = 'lc-dropdown';
    dd.innerHTML =
        '<div class="lc-dd-item" onclick="abrirDiagrama('+id+')">' +
            '<i class="bi bi-pencil-square"></i> Abrir en editor' +
        '</div>' +
        '<div class="lc-dd-item" onclick="duplicarDiagrama('+id+',\''+tEsc+'\')">' +
            '<i class="bi bi-files"></i> Hacer una copia' +
        '</div>' +
        '<div class="lc-dd-item" onclick="iniciarRenombrar('+id+',\''+tEsc+'\')">' +
            '<i class="bi bi-cursor-text"></i> Renombrar' +
        '</div>' +
        '<div class="lc-dd-sep"></div>' +
        '<div class="lc-dd-item danger" onclick="pedirEliminar('+id+',\''+tEsc+'\')">' +
            '<i class="bi bi-trash3"></i> Mover a la papelera' +
        '</div>';
    wrap.appendChild(dd);
}

async function iniciarRenombrar(id, tituloActual) {
    if (_activeDropdown) {
        _activeDropdown.querySelector('.lc-dropdown')?.remove();
        _activeDropdown = null;
    }
    const nuevoTitulo = prompt('Nuevo nombre del diagrama:', tituloActual);
    if (!nuevoTitulo || nuevoTitulo.trim() === tituloActual) return;
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas/rename', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, titulo: nuevoTitulo.trim() })
        });
        if (data.success) { toast('Renombrado correctamente', 'ok'); await cargarDiagramas(); }
        else throw new Error(data.error || 'Error al renombrar');
    } catch(e) { toast(e.message, 'error'); }
}

function renderCardDiagrama(d) {
    const fecha    = new Date(d.fecha_modificacion || d.fecha_creacion).toLocaleDateString('es-MX');
    const tipoInfo = TIPOS[d.tipo_diagrama] || { label: d.tipo_diagrama };
    const id       = d.id;
    const titulo   = escHtml(d.titulo || 'Sin título');

    return '<div class="col-sm-6 col-lg-4 col-xl-3">' +
        '<div class="diagram-card">' +
            '<div class="lc-preview" data-preview-id="' + id + '" onclick="abrirDiagrama(' + id + ')" title="Abrir en editor">' +
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;opacity:0.3">' +
                    getTipoIconoSVG(d.tipo_diagrama, 44) +
                '</div>' +
            '</div>' +
            '<div class="lc-body">' +
                '<div class="lc-title" title="' + titulo + '">' + titulo + '</div>' +
                '<div class="lc-meta">' +
                    '<span style="display:inline-flex;align-items:center;gap:3px">' +
                        getTipoIconoSVG(d.tipo_diagrama, 11) + '&nbsp;' + tipoInfo.label +
                    '</span>' +
                    '&nbsp;&middot;&nbsp;' + fecha +
                '</div>' +
            '</div>' +
            '<div class="lc-footer">' +
                '<button class="lc-btn-open" onclick="abrirDiagrama(' + id + ')">Abrir</button>' +
                '<div class="lc-dots-wrap" onclick="event.stopPropagation()">' +
                    '<button class="lc-icon-btn" title="M\u00e1s opciones" onclick="toggleDDiagrama(event,' + id + ',\'' + titulo.replace(/'/g,"\\'") + '\')">' +
                        '<i class="bi bi-three-dots"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function renderPaginacion(total, pagActual) {
    const wrap = document.getElementById('paginationWrap');
    if (!wrap) return;
    const porPagina = 12;
    const totalPags = Math.ceil(total / porPagina);
    if (totalPags <= 1) { wrap.innerHTML = ''; return; }

    let html = '<ul class="pagination">';
    if (pagActual > 1) html += `<li class="page-item"><a class="page-link" onclick="irPagina(${pagActual-1})" href="#">&laquo;</a></li>`;
    for (let i = 1; i <= totalPags; i++) {
        html += `<li class="page-item${i===pagActual?' active':''}"><a class="page-link" onclick="irPagina(${i})" href="#">${i}</a></li>`;
    }
    if (pagActual < totalPags) html += `<li class="page-item"><a class="page-link" onclick="irPagina(${pagActual+1})" href="#">&raquo;</a></li>`;
    html += '</ul>';
    wrap.innerHTML = html;
}

function irPagina(p) {
    event.preventDefault();
    currentPage = p;
    cargarDiagramas();
}

// ══════════════════════════════════════════════════════════════
// PLANTILLAS / ESTADÍSTICAS
// ══════════════════════════════════════════════════════════════
// ══════════════════════════════════════════════════════════════
// GRUPOS (alumno)
// ══════════════════════════════════════════════════════════════
async function renderGruposAlumno() {
    document.getElementById('contentArea').innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">Grupos a los que perteneces</span>
            <button class="btn-new" onclick="abrirModalUnirseGrupo()"><i class="bi bi-plus-lg"></i> Unirse a Grupo</button>
        </div>
        <div id="gruposGrid"></div>`;
    await cargarGruposAlumno();
}

async function cargarGruposAlumno() {
    const grid = document.getElementById('gruposGrid');
    if (!grid) return;
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/alumno?action=mis_grupos');
        if (!data.grupos || data.grupos.length === 0) {
            grid.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-collection"></i>
                    <h5>No estás en ningún grupo</h5>
                    <p>Pídele el código a tu maestro para unirte</p>
                    <button class="btn-new" onclick="abrirModalUnirseGrupo()"><i class="bi bi-plus-lg"></i> Unirse a Grupo</button>
                </div>`;
        } else {
            grid.innerHTML = `<div class="row g-3">${data.grupos.map(g => `
                <div class="col-md-6 col-lg-4">
                    <div class="diagram-card" style="cursor:default">
                        <div class="card-preview" style="height:80px;font-size:2rem">🏫</div>
                        <div class="card-body-inner">
                            <div class="card-title">${escHtml(g.nombre)}</div>
                            <div class="card-meta">
                                <span><i class="bi bi-person-badge me-1"></i>${escHtml(g.maestro_nombre||'Maestro')}</span>
                            </div>
                            ${g.descripcion ? `<div class="card-meta mt-1"><i class="bi bi-text-paragraph me-1"></i>${escHtml(g.descripcion)}</div>` : ''}
                        </div>
                        <div class="card-footer-inner">
                            <span class="badge-tipo">${g.num_alumnos} alumnos</span>
                            <button class="btn-icon danger" title="Salir del grupo" onclick="salirGrupo(${g.id},'${escHtml(g.nombre)}')">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>`).join('')}</div>`;
        }
    } catch(e) { toast(e.message, 'error'); }
}

// Modal unirse a grupo
function abrirModalUnirseGrupo() {
    const html = `
        <div class="modal fade" id="modalUnirseGrupo" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border-radius:16px 16px 0 0">
                        <h5 class="modal-title"><i class="bi bi-collection me-2"></i>Unirse a Grupo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label" style="font-weight:600;font-size:.85rem">Código del grupo</label>
                        <input type="text" class="form-control" id="codigoGrupoInput" placeholder="Ej: AB12CD" maxlength="6" style="text-transform:uppercase;letter-spacing:.1em;font-size:1.1rem;text-align:center">
                        <div id="unirseError" class="text-danger small mt-2 d-none"></div>
                    </div>
                    <div class="modal-footer justify-content-end gap-2">
                        <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn-confirm" onclick="confirmarUnirseGrupo()"><i class="bi bi-check-lg me-1"></i>Unirse</button>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    const m = new bootstrap.Modal(document.getElementById('modalUnirseGrupo'));
    document.getElementById('modalUnirseGrupo').addEventListener('hidden.bs.modal', () => {
        document.getElementById('modalUnirseGrupo').remove();
    });
    m.show();
}

async function confirmarUnirseGrupo() {
    const codigo = document.getElementById('codigoGrupoInput').value.trim().toUpperCase();
    const errEl  = document.getElementById('unirseError');
    if (!codigo) { errEl.textContent='Ingresa el código'; errEl.classList.remove('d-none'); return; }
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/alumno?action=unirse_grupo', {
            method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({codigo})
        });
        if (data.success) {
            toast('¡Te uniste al grupo!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalUnirseGrupo')).hide();
            cargarGruposAlumno();
        } else {
            errEl.textContent = data.error||'Código incorrecto'; errEl.classList.remove('d-none');
        }
    } catch(e) { errEl.textContent = e.message; errEl.classList.remove('d-none'); }
}

async function salirGrupo(id, nombre) {
    if (!confirm(`¿Salir del grupo "${nombre}"?`)) return;
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/alumno?action=salir_grupo', {
            method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({grupo_id:id})
        });
        if (data.success) { toast('Saliste del grupo', 'success'); cargarGruposAlumno(); }
        else throw new Error(data.error||'Error');
    } catch(e) { toast(e.message, 'error'); }
}

// ══════════════════════════════════════════════════════════════
// TAREAS (alumno)
// ══════════════════════════════════════════════════════════════
// ════════════════════════════════════════════════════════════
// TAREAS — estilo Teams
// ════════════════════════════════════════════════════════════

// Helper: render tipo option con icono SVG
function renderTipoOption(val, label) {
    return `<div class="tipo-option${val===document.getElementById('fTipo')?.value?' selected':''}"
        onclick="seleccionarTipo('${val}')" data-tipo="${val}"
        style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
               cursor:pointer;border:1.5px solid var(--bd-color);background:var(--bg-card);
               transition:all .15s;margin-bottom:6px">
        <div style="width:36px;height:36px;flex-shrink:0">${getTipoIconoSVG(val, 36)}</div>
        <div>
            <div style="font-weight:600;font-size:.82rem;color:var(--txt-main)">${label}</div>
        </div>
    </div>`;
}
function seleccionarTipo(val) {
    const hidden = document.getElementById('fTipo');
    if (hidden) hidden.value = val;
    document.querySelectorAll('.tipo-option').forEach(el => {
        const sel = el.dataset.tipo === val;
        el.style.borderColor = sel ? 'var(--primary)' : 'var(--bd-color)';
        el.style.background = sel ? 'rgba(var(--primary-rgb),.1)' : 'var(--bg-card)';
    });
}

const TIPOS_T = {usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',activity:'Actividades', state:'Máquina de Estado', component:'Componentes',deployment:'Despliegue', object:'Objetos', communication:'Comunicación',timing:'Tiempos', package:'Paquetes', composite:'Estructura Compuesta',profile:'Perfiles', overview:'Descripción General'};

async function renderTareasAlumno() {
    const ca = document.getElementById('contentArea');
    ca.innerHTML = `<div id="tareasContainer" style="min-height:200px">
        <div style="display:flex;align-items:center;justify-content:center;padding:60px 20px;gap:12px">
            <div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div>
            <span style="color:#888;font-size:.9rem">Cargando tareas…</span>
        </div>
    </div>`;

    let data, tareas = [];
    try {
        data  = await apiFetch('<?= BASE_URL ?>/api/alumno?action=mis_tareas');
        tareas = data.tareas || [];
    } catch(e) {
        document.getElementById('tareasContainer').innerHTML = `
        <div class="empty-state">
            <i class="bi bi-wifi-off" style="color:#f59e0b"></i>
            <h5 style="color:#555;margin:8px 0 4px">Error al cargar tareas</h5>
            <p style="color:#888;font-size:.82rem">${escHtml(e.message)}</p>
            <button onclick="renderTareasAlumno()"
                style="margin-top:10px;background:var(--primary);border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">
                <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
            </button>
        </div>`;
        return;
    }

    if (tareas.length === 0) {
        document.getElementById('tareasContainer').innerHTML = `
        <div class="empty-state">
            <i class="bi bi-clipboard-check" style="color:#cbd5e1"></i>
            <h5 style="color:#64748b;margin:8px 0 4px">Sin tareas asignadas</h5>
            <p style="color:#94a3b8;font-size:.82rem">
                Cuando un maestro asigne tareas a tus grupos aparecerán aquí.<br>
                Si acabas de unirte a un grupo, intenta recargar.
            </p>
            <button onclick="renderTareasAlumno()"
                style="margin-top:10px;background:var(--primary);border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">
                <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
            </button>
        </div>`;
        return;
    }

    // Separar por estado
    const entregadas = tareas.filter(t => t.diagrama_id != null || t.calificacion != null);
    const pendientes = tareas.filter(t => !t.diagrama_id && !t.calificacion && !(t.fecha_entrega && new Date(t.fecha_entrega) < new Date()));
    const vencidas   = tareas.filter(t => !t.diagrama_id && !t.calificacion && t.fecha_entrega && new Date(t.fecha_entrega) < new Date());

    document.getElementById('tareasContainer').innerHTML = `
    <!-- Tabs de estado -->
    <div style="display:flex;gap:8px;margin-bottom:18px">
        ${['todas','pendientes','vencidas','entregadas'].map((id,i) => {
            const labels  = {todas:'Todas',pendientes:'Pendientes',vencidas:'Vencidas',entregadas:'Entregadas'};
            const colors  = {todas:'#667eea',pendientes:'#f59e0b',vencidas:'#ef4444',entregadas:'#10b981'};
            const counts  = {todas:tareas.length,pendientes:pendientes.length,vencidas:vencidas.length,entregadas:entregadas.length};
            const col = colors[id];
            return `<button id="tabBtn_${id}" onclick="setTabTarea('${id}')"
                style="flex:1;padding:10px 6px;border-radius:12px;border:2px solid ${col}33;
                       background:var(--bg-card);cursor:pointer;transition:all .2s;
                       box-shadow:0 2px 8px rgba(0,0,0,.06)">
                <div style="font-size:1.3rem;font-weight:800;color:${col}">${counts[id]}</div>
                <div style="font-size:.68rem;color:${col};font-weight:600;margin-top:1px">${labels[id]}</div>
            </button>`;
        }).join('')}
    </div>
    <div id="tareasLista"></div>`;

    window._todasTareas = tareas;
    setTabTarea('todas');
}

function setTabTarea(tab) {
    window._tabTareaActual = tab;
    const all = window._todasTareas || [];
    let lista;
    if (tab === 'pendientes') lista = all.filter(t => t.diagrama_id==null && t.calificacion==null && !(t.fecha_entrega && new Date(t.fecha_entrega)<new Date()));
    else if (tab === 'vencidas') lista = all.filter(t => t.diagrama_id==null && t.calificacion==null && t.fecha_entrega && new Date(t.fecha_entrega)<new Date());
    else if (tab === 'entregadas') lista = all.filter(t => t.diagrama_id!=null || t.calificacion!=null);
    else lista = all;

    // Update active tab style
    ['todas','pendientes','vencidas','entregadas'].forEach(id => {
        const el = document.getElementById('tabBtn_'+id);
        if (!el) return;
        const colMap = {todas:'#667eea',pendientes:'#f59e0b',vencidas:'#ef4444',entregadas:'#10b981'};
        const active = id === tab;
        const col = colMap[id];
        el.style.borderColor  = active ? col : col + '33';
        el.style.background   = active ? col : 'var(--bg-card)';
        el.style.color        = active ? '#fff' : col;
        el.style.boxShadow    = active ? `0 4px 14px rgba(${hexToRgb2(col)},.35)` : '0 2px 8px rgba(0,0,0,.06)';
        // Update inner divs color
        const divs = el.querySelectorAll('div');
        divs.forEach(d => d.style.color = active ? '#fff' : col);
    });

    const cont = document.getElementById('tareasLista');
    if (!cont) return;
    if (lista.length === 0) {
        cont.innerHTML = `<div class="empty-state"><i class="bi bi-clipboard-check"></i><p>Sin tareas en esta categoría</p></div>`;
        return;
    }

    cont.innerHTML = lista.map(t => {
        const vencida   = t.fecha_entrega && new Date(t.fecha_entrega) < new Date();
        const entregada = t.diagrama_id != null;
        const calif     = t.calificacion != null;
        const color     = entregada ? '#10b981' : vencida ? '#ef4444' : '#f59e0b';
        const icono     = entregada ? 'check-circle-fill' : vencida ? 'clock-history' : 'clipboard';
        const estado    = entregada ? (calif ? `Calificación: <strong style="color:#10b981">${parseFloat(t.calificacion).toFixed(1)}</strong>` : 'Entregada') : vencida ? 'Vencida' : 'Pendiente';
        const destino   = t.proyecto_nombre
            ? `<span style="font-size:.75rem;color:#60a5fa"><i class="bi bi-diagram-3 me-1"></i>${escHtml(t.proyecto_nombre)}</span>`
            : `<span style="font-size:.75rem;color:#666"><i class="bi bi-collection me-1"></i>${escHtml(t.grupo_nombre||'—')}</span>`;

        return `<div class="list-row" style="background:#fff;border:1.5px solid ${color}33;border-left:4px solid ${color};border-radius:12px;padding:16px 18px;margin-bottom:10px;box-shadow:0 2px 10px rgba(0,0,0,.05)"
                     onclick="abrirTarea(${t.id})">
            <div style="display:flex;align-items:flex-start;gap:14px">
                <div style="width:42px;height:42px;border-radius:10px;background:${color}22;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
                    <i class="bi bi-${icono}" style="font-size:1.3rem;color:${color}"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;color:#1a1a2e;font-size:.95rem;margin-bottom:4px">${escHtml(t.titulo)}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:6px">
                        <span style="font-size:.75rem;color:#666"><i class="bi bi-person-badge me-1"></i>${escHtml(t.maestro_nombre||'Maestro')}</span>
                        ${destino}
                        <span style="background:rgba(102,126,234,.12);color:var(--primary);border-radius:10px;padding:1px 8px;font-size:.7rem;font-weight:600">${TIPOS_T[t.tipo_diagrama]||t.tipo_diagrama}</span>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
                        <span style="font-size:.75rem;color:${color}">${estado}</span>
                        ${t.fecha_entrega ? `<span style="font-size:.72rem;color:${vencida&&!entregada?'#ef4444':'#666'}"><i class="bi bi-calendar-event me-1"></i>${new Date(t.fecha_entrega).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'})}</span>` : ''}
                    </div>
                    ${t.descripcion ? `<div style="margin-top:6px;font-size:.73rem;color:#64748b;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">${escHtml(t.descripcion)}</div>` : ''}
                    ${t.comentario_maestro ? `<div style="margin-top:6px;font-size:.73rem;color:#d97706;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:6px;padding:4px 8px"><i class="bi bi-chat-left-text me-1"></i>${escHtml(t.comentario_maestro)}</div>` : ''}
                </div>
                <i class="bi bi-chevron-right" style="color:#bbb;flex-shrink:0;margin-top:10px"></i>
            </div>
        </div>`;
    }).join('');
}

// ════════════════════════════════════════════════════════════
// PROYECTOS COLABORATIVOS
// ════════════════════════════════════════════════════════════
const TIPOS_P    = {usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',activity:'Actividades', state:'Máquina de Estado', component:'Componentes',deployment:'Despliegue', object:'Objetos', communication:'Comunicación',timing:'Tiempos', package:'Paquetes', composite:'Estructura Compuesta',profile:'Perfiles', overview:'Descripción General'};
const TIPOS_ICON = {usecase:'bi-person-circle', class:'bi-box', sequence:'bi-arrow-left-right',activity:'bi-activity', state:'bi-diagram-3', component:'bi-cpu',deployment:'bi-hdd-network', object:'bi-table', communication:'bi-chat-dots',timing:'bi-clock', package:'bi-folder2', composite:'bi-layout-three-columns',profile:'bi-tag', overview:'bi-map'};
// Use getTipoIconoSVG() for card previews and larger icons

let _proyectoActual = null;

async function renderProyectos() {
    const ca = document.getElementById('contentArea');
    ca.innerHTML = `<div id="proyWrapper"><div style="display:flex;align-items:center;justify-content:center;padding:60px 20px;gap:12px"><div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div><span style="color:#888">Cargando proyectos…</span></div></div>`;

    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos');
        const proyectos = data.proyectos || [];

        document.getElementById('proyWrapper').innerHTML = `
        <!-- Header de proyectos -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
            <div>
                <h4 style="margin:0;color:#1a1a2e;font-size:1.05rem;font-weight:700"><i class="bi bi-diagram-3 me-2" style="color:var(--primary)"></i>Proyectos Colaborativos</h4>
                <p style="color:#888;font-size:.78rem;margin:3px 0 0">Espacios compartidos donde todos los miembros pueden ver y editar diagramas</p>
            </div>
            <div style="display:flex;gap:8px">
                <button onclick="modalNuevoProyecto()"
                    style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:10px;padding:9px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-plus-circle me-1"></i>Nuevo Proyecto
                </button>
                <button onclick="modalUnirseProyecto()"
                    style="background:var(--bg-card);border:2px solid var(--primary);color:var(--primary);border-radius:10px;padding:9px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-key me-1"></i>Unirse con código
                </button>
            </div>
        </div>

        <!-- Lista de proyectos -->
        ${proyectos.length === 0
            ? `<div class="empty-state" style="background:var(--bg-card);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07)">
                <i class="bi bi-diagram-3" style="color:var(--txt-muted)"></i>
                <h5 style="color:var(--txt-muted)">Sin proyectos aún</h5>
                <p style="color:var(--txt-muted);font-size:.82rem">Crea un proyecto nuevo o únete con un código de invitación</p>
               </div>`
            : `<div class="row g-3">
                ${proyectos.map(p => `
                <div class="col-md-6 col-lg-4">
                    <div class="proyecto-card" style="padding:0;overflow:hidden;cursor:pointer;border:1.5px solid var(--bd-color);border-radius:14px"
                         onclick="abrirProyecto(${p.id})">
                        <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 18px">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                <span style="background:rgba(255,255,255,.2);color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px">${p.rol==='owner'?'OWNER':'EDITOR'}</span>
                                <code style="background:rgba(255,255,255,.15);color:#fff;font-size:.7rem;padding:2px 8px;border-radius:6px;letter-spacing:.05em">${escHtml(p.codigo)}</code>
                            </div>
                            <div style="font-weight:700;color:#fff;font-size:.92rem">${escHtml(p.nombre)}</div>
                            ${p.descripcion ? `<div style="color:rgba(255,255,255,.7);font-size:.73rem;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(p.descripcion)}</div>` : ''}
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
        document.getElementById('proyWrapper').innerHTML = `<div class="empty-state"><i class="bi bi-exclamation-triangle" style="color:#f59e0b"></i><p style="color:var(--txt-main)">${escHtml(e.message)}</p></div>`;
    }
}

async function abrirProyecto(pid) {
    const ca = document.getElementById('contentArea');
    ca.innerHTML = `<div id="proyDetalle"><div style="display:flex;align-items:center;justify-content:center;padding:60px 20px;gap:12px"><div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div></div></div>`;

    try {
        const data = await apiFetch(`<?= BASE_URL ?>/api/proyectos?action=detalle&id=${pid}`);
        _proyectoActual = data;
        const p  = data.proyecto;
        const yo = data.rol;
        const diags  = data.diagramas  || [];
        const miembros = data.miembros || [];
        window._proyectoDiagsAlumno = diags;

        document.getElementById('proyDetalle').innerHTML = `
        <!-- Back + Header -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
            <button onclick="renderProyectos()" style="background:none;border:1.5px solid #e8eaf0;color:#667eea;border-radius:8px;padding:6px 12px;cursor:pointer;font-size:.8rem">
                <i class="bi bi-arrow-left me-1"></i>Proyectos
            </button>
            <div style="flex:1">
                <h4 style="margin:0;color:#1a1a2e;font-size:1rem;font-weight:700">${escHtml(p.nombre)}</h4>
                <span style="font-size:.73rem;color:#888">${escHtml(p.descripcion||'Sin descripción')}</span>
            </div>
            <code style="background:rgba(102,126,234,.1);color:var(--primary);font-size:.8rem;font-weight:700;padding:4px 12px;border-radius:8px;letter-spacing:.06em;cursor:pointer"
                  onclick="navigator.clipboard?.writeText('${p.codigo}');toast('Código copiado','success')"
                  title="Clic para copiar">
                ${escHtml(p.codigo)} <i class="bi bi-copy" style="font-size:.7rem"></i>
            </code>
        </div>

        <div class="row g-3">
            <!-- ── TABS: Diagramas / Archivos / Observaciones ── -->
        <div class="col-12 mb-2">
            <div style="display:flex;gap:6px;border-bottom:2px solid var(--bd-color);padding-bottom:0">
                <button id="tabPD" onclick="setProyTab('diagramas')"
                    style="background:none;border:none;border-bottom:3px solid var(--primary);color:var(--primary);padding:8px 16px;font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:-2px">
                    <i class="bi bi-diagram-3 me-1"></i>Diagramas (${diags.length})
                </button>
                <button id="tabPA" onclick="setProyTab('archivos')"
                    style="background:none;border:none;border-bottom:3px solid transparent;color:var(--txt-muted);padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                    <i class="bi bi-folder2-open me-1"></i>Archivos (${(data.archivos||[]).length})
                </button>
                <button id="tabPT" onclick="setProyTab('tareas')"
                    style="background:none;border:none;border-bottom:3px solid transparent;color:var(--txt-muted);padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                    <i class="bi bi-clipboard-check me-1"></i>Tareas
                </button>
                <button id="tabPO" onclick="setProyTab('observaciones')"
                    style="background:none;border:none;border-bottom:3px solid transparent;color:var(--txt-muted);padding:8px 16px;font-size:.85rem;cursor:pointer;margin-bottom:-2px">
                    <i class="bi bi-chat-left-text me-1"></i>Observaciones
                </button>
            </div>
        </div>

        <!-- ── Panel izquierdo: diagramas del proyecto ── -->
            <div class="col-md-8" id="panelDiagramas">
                <div class="stat-card" style="padding:0;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1.5px solid #f0f2f8;display:flex;align-items:center;gap:10px">
                        <i class="bi bi-diagram-3" style="color:var(--primary)"></i>
                        <h5 style="margin:0;font-size:.88rem;font-weight:600;color:var(--txt-main)">Diagramas del Proyecto (${diags.length})</h5>
                        <div style="margin-left:auto">
                                <button onclick="crearDiagramaParaProyecto(${pid})"
                                    style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 14px;font-size:.8rem;cursor:pointer;font-weight:600"
                                    title="Crear un diagrama nuevo para este proyecto">
                                    <i class="bi bi-plus-circle me-1"></i>Nuevo diagrama
                                </button>
                        </div>
                    </div>
                    <div style="padding:14px 16px">
                    ${diags.length === 0
                        ? `<div class="empty-state" style="padding:30px;box-shadow:none">
                               <i class="bi bi-file-earmark-plus" style="color:#cbd5e1;font-size:2.5rem"></i>
                               <p style="color:#94a3b8;font-size:.82rem;margin-top:8px">No hay diagramas en este proyecto aún.<br>Agrega uno desde tu dashboard.</p>
                           </div>`
                        : `<div class="row g-3">
                            ${diags.map(d => {
                                const esMio = d.usuario_id == <?= SessionManager::usuarioId() ?>;
                                const tipoLabel = TIPOS_P[d.tipo_diagrama] || d.tipo_diagrama;
                                const icono = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(d.tipo_diagrama, 44) : '';
                                const iconoSm = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(d.tipo_diagrama, 11) : '';
                                return `
                                <div class="col-sm-6 col-lg-4">
                                  <div class="diagram-card">
                                    <div class="lc-preview" data-preview-id="${d.id}"
                                         onclick="saveNavState({fromEditor:true});window.location.href='<?= BASE_URL ?>/editor?id=${d.id}&proyecto=${pid}'"
                                         title="Abrir en editor">
                                      <div style="display:flex;align-items:center;justify-content:center;height:100%;opacity:0.3">${icono}</div>
                                    </div>
                                    <div class="lc-body">
                                      <div class="lc-title" title="${escHtml(d.titulo||'Sin título')}">${escHtml(d.titulo||'Sin título')}</div>
                                      <div class="lc-meta">
                                        <span style="display:inline-flex;align-items:center;gap:3px">${iconoSm}&nbsp;${tipoLabel}</span>
                                        &nbsp;·&nbsp;${escHtml(d.autor||'—')}${esMio?' (tú)':''}
                                      </div>
                                    </div>
                                    <div class="lc-footer">
                                      <button class="lc-btn-open" onclick="saveNavState({fromEditor:true});window.location.href='<?= BASE_URL ?>/editor?id=${d.id}&proyecto=${pid}'">Abrir</button>
                                      ${esMio||yo==='owner' ? `<button onclick="event.stopPropagation();quitarDiagramaProyecto(${pid},${d.id})"
                                        style="margin-left:auto;background:none;border:1px solid var(--bd-color);color:#fca5a5;border-radius:7px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.85rem"
                                        title="Quitar del proyecto"><i class="bi bi-x-lg"></i></button>` : ''}
                                    </div>
                                  </div>
                                </div>`;
                            }).join('')}
                           </div>`}
                    </div>
                </div>
            </div>

            <!-- ── Panel de archivos (hidden by default) ── -->
            <div class="col-md-8" id="panelArchivos" style="display:none">
                <div class="stat-card" style="padding:0;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1.5px solid #f0f2f8;display:flex;align-items:center;gap:10px">
                        <i class="bi bi-folder2-open" style="color:var(--primary)"></i>
                        <h5 style="margin:0;font-size:.88rem;font-weight:600;color:#1a1a2e">Archivos del Proyecto</h5>
                        <div style="margin-left:auto">
                            <label style="background:linear-gradient(135deg,var(--primary),var(--primary2));color:#fff;border-radius:8px;padding:6px 14px;font-size:.75rem;font-weight:600;cursor:pointer">
                                <i class="bi bi-upload me-1"></i>Subir archivo
                                <input type="file" id="proyFileInput" style="display:none" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.md,.sql,.csv,.json,.xml,.png,.jpg,.jpeg,.gif,.svg"
                                    onchange="subirArchivosProyecto(${pid}, this.files)">
                            </label>
                        </div>
                    </div>
                    <div id="archivosLista" style="padding:14px 16px">
                    ${(data.archivos||[]).length === 0
                        ? `<div class="empty-state" style="padding:30px;box-shadow:none">
                               <i class="bi bi-folder-plus" style="color:#cbd5e1;font-size:2rem"></i>
                               <p style="color:#94a3b8;font-size:.82rem;margin-top:8px">Sin archivos. Sube documentos, presentaciones, hojas de cálculo, SQL y más.</p>
                           </div>`
                        : renderArchivosList(data.archivos||[], pid)}
                    </div>
                </div>
            </div>

            <!-- ── Panel de Tareas del proyecto (alumno) ── -->
            <div class="col-md-8" id="panelTareas" style="display:none">
                <div class="stat-card" style="padding:0;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1.5px solid var(--bd-color);display:flex;align-items:center;gap:10px">
                        <i class="bi bi-clipboard-check" style="color:var(--primary)"></i>
                        <h5 style="margin:0;font-size:.88rem;font-weight:600;color:var(--txt-main)">Tareas del Proyecto</h5>
                        <span style="margin-left:auto;font-size:.72rem;color:var(--txt-muted)">Completa y entrega las tareas asignadas</span>
                    </div>
                    <div id="tareasProyContenido" style="padding:14px 16px">
                        <div style="text-align:center;padding:20px"><div class="spinner-border spinner-border-sm text-primary"></div></div>
                    </div>
                </div>
            </div>

            <!-- ── Panel de observaciones por diagrama (como maestro) ── -->
            <div class="col-md-8" id="panelObservaciones" style="display:none">
                <div class="stat-card" style="padding:0;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1.5px solid var(--bd-color);display:flex;align-items:center;gap:10px">
                        <i class="bi bi-chat-left-text" style="color:var(--primary)"></i>
                        <h5 style="margin:0;font-size:.88rem;font-weight:600;color:var(--txt-main)">Observaciones por diagrama</h5>
                        <span style="margin-left:auto;font-size:.72rem;color:var(--txt-muted)">Comenta cada diagrama y revisa las del maestro</span>
                    </div>
                    <div id="obsAlumnoProyPanel" style="padding:14px 16px">
                        <div style="text-align:center;padding:20px"><div class="spinner-border spinner-border-sm text-primary"></div></div>
                    </div>
                </div>
            </div>

            <!-- ── Panel derecho: miembros ── -->
            <div class="col-md-4">
                <div class="stat-card" style="padding:0;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1.5px solid #f0f2f8">
                        <h5 style="margin:0;font-size:.88rem;font-weight:600;color:#1a1a2e">
                            <i class="bi bi-people me-2" style="color:var(--primary)"></i>Miembros (${miembros.length})
                        </h5>
                    </div>
                    <div style="padding:10px 14px">
                    ${miembros.map(m => {
                        const esYo = m.id == <?= SessionManager::usuarioId() ?>;
                        const esOwnerItem = m.rol_proyecto === 'owner';
                        return `<div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f8f9ff">
                            <div style="width:34px;height:34px;background:${esOwnerItem?'linear-gradient(135deg,var(--primary),var(--primary2))':'#e8eaf0'};border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:${esOwnerItem?'#fff':'#667eea'};flex-shrink:0">
                                ${escHtml((m.nombre_completo||m.username||'?')[0].toUpperCase())}
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:.82rem;font-weight:600;color:var(--txt-main)">
                                    ${escHtml(m.nombre_completo||m.username)}
                                    ${esYo?'<span style="font-size:.62rem;color:var(--primary);margin-left:4px">(tú)</span>':''}
                                </div>
                                <div style="font-size:.68rem;color:var(--txt-muted)">
                                    @${escHtml(m.username)}
                                    <span style="margin-left:4px;background:${esOwnerItem?'rgba(102,126,234,.12)':'rgba(0,0,0,.06)'};color:${esOwnerItem?'var(--primary)':'#888'};border-radius:6px;padding:0 5px;font-size:.6rem">${m.rol_proyecto}</span>
                                </div>
                            </div>
                            ${yo==='owner' && !esOwnerItem ? `
                            <div style="display:flex;flex-direction:column;gap:4px;flex-shrink:0">
                                <button onclick="abrirPermisosM(${pid},${m.id},'${escHtml(m.nombre_completo||m.username)}')"
                                    style="background:rgba(102,126,234,.1);border:1px solid rgba(102,126,234,.25);color:var(--primary);border-radius:6px;padding:2px 7px;font-size:.65rem;cursor:pointer" title="Configurar permisos">
                                    <i class="bi bi-shield-lock"></i>
                                </button>
                                <button onclick="expulsarMiembro(${pid},${m.id},'${escHtml(m.nombre_completo||m.username)}')"
                                    style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#ef4444;border-radius:6px;padding:2px 7px;font-size:.65rem;cursor:pointer" title="Expulsar">
                                    <i class="bi bi-person-x"></i>
                                </button>
                            </div>` : ''}
                        </div>`;
                    }).join('')}
                    ${yo==='owner' ? `<div style="margin-top:10px">
                        <button onclick="confirmarEliminarProyecto(${pid})"
                            style="width:100%;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:8px;padding:7px;font-size:.75rem;cursor:pointer">
                            <i class="bi bi-trash3 me-1"></i>Eliminar proyecto
                        </button>
                    </div>` : `<div style="margin-top:10px">
                        <button onclick="confirmarSalirProyecto(${pid})"
                            style="width:100%;background:#f8f9ff;border:1px solid #e8eaf0;color:#888;border-radius:8px;padding:7px;font-size:.75rem;cursor:pointer">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir del proyecto
                        </button>
                    </div>`}
                    </div>
                </div>
            </div>
        </div>`;
        // Activar previews en tarjetas de proyecto
        if (window.DiagramMiniRenderer) {
            requestAnimationFrame(() => DiagramMiniRenderer.observeAll(document.getElementById('panelDiagramas')));
        }
    } catch(e) {
        document.getElementById('proyDetalle').innerHTML = `<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>${escHtml(e.message)}</p></div>`;
    }
}

function crearDiagramaParaProyecto(pid) {
    // Abrir modal de nuevo diagrama con el proyecto preseleccionado y bloqueado
    document.getElementById('_modalNuevoDiagProyecto')?.remove();
    const m = document.createElement('div');
    m.id = '_modalNuevoDiagProyecto';
    m.className = 'modal fade';
    m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama en este Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Proyecto bloqueado — no se puede cambiar -->
                <div style="background:rgba(102,126,234,.08);border:1.5px solid rgba(102,126,234,.3);border-radius:10px;padding:10px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
                    <i class="bi bi-lock-fill" style="color:var(--primary);flex-shrink:0"></i>
                    <div>
                        <div style="font-size:.78rem;font-weight:700;color:var(--primary)">Proyecto actual (bloqueado)</div>
                        <div style="font-size:.73rem;color:var(--txt-muted)">El diagrama se ligará automáticamente a este proyecto al guardar</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-type me-1"></i>Título</label>
                    <input type="text" class="form-control" id="_ndpTitulo" placeholder="Ej: Diagrama del sistema">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-diagram-3 me-1"></i>Tipo de Diagrama</label>
                    <input type="hidden" id="_ndpTipo" value="usecase">
                    <div id="_ndpPicker" style="max-height:260px;overflow-y:auto;padding-right:4px">
                        <div style="text-align:center;padding:12px;color:var(--txt-muted);font-size:.82rem">Cargando...</div>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label"><i class="bi bi-text-paragraph me-1"></i>Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="_ndpDesc" rows="2" placeholder="Breve descripción..."></textarea>
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-confirm" onclick="confirmarCrearDiagramaProyecto(${pid})">
                    <i class="bi bi-pencil-square me-1"></i>Ir al Editor
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    const bsM = new bootstrap.Modal(m);
    m.addEventListener('hidden.bs.modal', () => m.remove());
    bsM.show();
    setTimeout(() => document.getElementById('_ndpTitulo')?.focus(), 300);
    setTimeout(() => _ndpPoblarPicker('usecase'), 150);
}

function confirmarCrearDiagramaProyecto(pid) {
    const titulo = document.getElementById('_ndpTitulo')?.value.trim() || 'Nuevo Diagrama';
    const tipo   = document.getElementById('_ndpTipo')?.value || 'usecase';
    const desc   = document.getElementById('_ndpDesc')?.value.trim() || '';

    sessionStorage.setItem('nuevoDiagrama', JSON.stringify({
        titulo, tipo, descripcion: desc, etiquetas: '', _projectId: pid
    }));
    bootstrap.Modal.getInstance(document.getElementById('_modalNuevoDiagProyecto'))?.hide();
    window.location.href = (window.BASE_URL || '') + '/editor?tipo=' + tipo + '&proyecto=' + pid;
}

// Poblar picker de tipos en modal proyecto alumno
function _ndpPoblarPicker(tipoActual) {
    const picker = document.getElementById('_ndpPicker');
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
            const active = tipo === (tipoActual || 'usecase');
            const icon = typeof getTipoIconoSVG === 'function' ? getTipoIconoSVG(tipo, 32) : '';
            html += '<div class="_ndpOpt" data-tipo="' + tipo + '" onclick="_ndpSelect(\'' + tipo + '\')"'
                + ' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;'
                + 'border:1.5px solid ' + (active ? 'var(--primary)' : 'var(--bd-color)') + ';'
                + 'background:' + (active ? 'rgba(var(--primary-rgb),.1)' : 'var(--bg-card)') + ';'
                + 'margin-bottom:5px;transition:all .15s">'
                + '<div style="width:32px;height:32px;flex-shrink:0">' + icon + '</div>'
                + '<span style="font-size:.81rem;font-weight:600;color:var(--txt-main)">' + (TLBL[tipo]||tipo) + '</span>'
                + '</div>';
        });
    });
    picker.innerHTML = html;
}

// Selecciona el tipo de diagrama en el picker del modal de proyecto
function _ndpSelect(val) {
    const el = document.getElementById('_ndpTipo');
    if (el) el.value = val;
    document.querySelectorAll('._ndpOpt').forEach(function(e) {
        const active = e.dataset.tipo === val;
        e.style.borderColor = active ? 'var(--primary)' : 'var(--bd-color)';
        e.style.background  = active ? 'rgba(102,126,234,.12)' : 'var(--bg-card)';
    });
}

async function agregarDiagramaProyecto(pid) {
    const sel = document.getElementById('selectAgregarDiag');
    const did = parseInt(sel?.value);
    if (!did) { toast('Selecciona un diagrama','info'); return; }
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=agregar_diagrama', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ proyecto_id: pid, diagrama_id: did })
        });
        if (r.success) { toast('Diagrama agregado al proyecto','success'); abrirProyecto(pid); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

async function quitarDiagramaProyecto(pid, did) {
    if (!confirm('¿Quitar este diagrama del proyecto? (El diagrama no se elimina, solo sale del proyecto)')) return;
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=quitar_diagrama', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ proyecto_id: pid, diagrama_id: did })
        });
        if (r.success) { toast('Diagrama quitado','success'); abrirProyecto(pid); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

async function confirmarEliminarProyecto(pid) {
    if (!confirm('¿Eliminar este proyecto? Esta acción no se puede deshacer. Los diagramas NO se eliminan.')) return;
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=eliminar', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ proyecto_id: pid })
        });
        if (r.success) { toast('Proyecto eliminado','success'); renderProyectos(); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

async function confirmarSalirProyecto(pid) {
    if (!confirm('¿Salir de este proyecto?')) return;
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=salir', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ proyecto_id: pid })
        });
        if (r.success) { toast('Saliste del proyecto','success'); renderProyectos(); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

// ── Gestión de permisos (solo owner) ──────────────────────────
function abrirPermisosM(pid, mid, nombre) {
    document.getElementById('_modalPermisos')?.remove();
    const m = document.createElement('div');
    m.id = '_modalPermisos'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Permisos: ${escHtml(nombre)}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:.8rem;color:var(--txt-muted);margin-bottom:14px">Solo el creador del proyecto (owner) puede modificar permisos de sus miembros.</div>
                <div class="mb-3 d-flex align-items-center justify-content-between" style="padding:10px;background:var(--bg-hover);border-radius:8px">
                    <div>
                        <div style="font-size:.85rem;font-weight:600">Solo lectura</div>
                        <div style="font-size:.73rem;color:var(--txt-muted)">No puede modificar ni agregar diagramas</div>
                    </div>
                    <input type="checkbox" id="_p_lectura" style="width:18px;height:18px;cursor:pointer">
                </div>
                <div class="mb-3 d-flex align-items-center justify-content-between" style="padding:10px;background:var(--bg-hover);border-radius:8px">
                    <div>
                        <div style="font-size:.85rem;font-weight:600">Puede subir archivos</div>
                        <div style="font-size:.73rem;color:var(--txt-muted)">Permite subir documentos al proyecto</div>
                    </div>
                    <input type="checkbox" id="_p_subir" checked style="width:18px;height:18px;cursor:pointer">
                </div>
                <div class="mb-1 d-flex align-items-center justify-content-between" style="padding:10px;background:var(--bg-hover);border-radius:8px">
                    <div>
                        <div style="font-size:.85rem;font-weight:600">Puede eliminar archivos</div>
                        <div style="font-size:.73rem;color:var(--txt-muted)">Permite eliminar archivos del proyecto</div>
                    </div>
                    <input type="checkbox" id="_p_eliminar" style="width:18px;height:18px;cursor:pointer">
                </div>
            </div>
            <div class="modal-footer justify-content-end gap-2">
                <button class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-confirm" onclick="guardarPermisosM(${pid},${mid})"><i class="bi bi-check me-1"></i>Guardar Permisos</button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    const bsM = new bootstrap.Modal(m);
    m.addEventListener('hidden.bs.modal', () => m.remove());
    bsM.show();
}

async function guardarPermisosM(pid, mid) {
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=actualizar_permisos', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({
                proyecto_id:    pid,
                miembro_id:     mid,
                solo_lectura:   document.getElementById('_p_lectura')?.checked  ? 1 : 0,
                puede_subir:    document.getElementById('_p_subir')?.checked    ? 1 : 0,
                puede_eliminar: document.getElementById('_p_eliminar')?.checked ? 1 : 0,
            })
        });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalPermisos'))?.hide();
            toast('Permisos actualizados','success');
        } else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

async function expulsarMiembro(pid, mid, nombre) {
    if (!confirm(`¿Expulsar a "${nombre}" del proyecto?`)) return;
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=expulsar_miembro', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ proyecto_id: pid, miembro_id: mid })
        });
        if (r.success) { toast('Miembro expulsado','success'); abrirProyecto(pid); }
        else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

function modalNuevoProyecto() {
    document.getElementById('_modalProy')?.remove();
    const m = document.createElement('div');
    m.id = '_modalProy'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.2)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:18px 22px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto Colaborativo</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px">
                <label style="font-size:.8rem;color:#666;font-weight:600;display:block;margin-bottom:5px">Nombre del proyecto *</label>
                <input id="_pNombre" type="text" class="form-control" placeholder="Ej: Proyecto Final — Sistemas" style="margin-bottom:12px">
                <label style="font-size:.8rem;color:#666;font-weight:600;display:block;margin-bottom:5px">Descripción (opcional)</label>
                <textarea id="_pDesc" class="form-control" rows="2" placeholder="¿De qué trata este proyecto?"></textarea>
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:#f8f9ff;border:1.5px solid #e8eaf0;color:#888;border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">Cancelar</button>
                <button onclick="crearProyecto()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check2 me-1"></i>Crear Proyecto
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
}

async function crearProyecto() {
    const nombre = document.getElementById('_pNombre')?.value.trim();
    const desc   = document.getElementById('_pDesc')?.value.trim();
    if (!nombre) { toast('Ingresa un nombre','info'); return; }
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=crear', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ nombre, descripcion: desc })
        });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalProy'))?.hide();
            toast(`Proyecto creado. Código: ${r.codigo}`,'success');
            renderProyectos();
        } else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}

function modalUnirseProyecto() {
    document.getElementById('_modalUnirse')?.remove();
    const m = document.createElement('div');
    m.id = '_modalUnirse'; m.className = 'modal fade'; m.tabIndex = -1;
    m.innerHTML = `<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.2)">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:18px 22px;border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between">
                <h5 style="color:#fff;margin:0;font-size:.95rem"><i class="bi bi-key me-2"></i>Unirse a un Proyecto</h5>
                <button type="button" data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="padding:22px">
                <label style="font-size:.8rem;color:#666;font-weight:600;display:block;margin-bottom:5px">Código de invitación</label>
                <input id="_pCodigo" type="text" class="form-control" placeholder="XXXXXXXX" maxlength="8"
                    style="font-family:monospace;font-size:1.1rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-align:center"
                    oninput="this.value=this.value.toUpperCase()">
            </div>
            <div style="padding:0 22px 18px;display:flex;justify-content:flex-end;gap:8px">
                <button data-bs-dismiss="modal" style="background:#f8f9ff;border:1.5px solid #e8eaf0;color:#888;border-radius:8px;padding:8px 18px;font-size:.82rem;cursor:pointer">Cancelar</button>
                <button onclick="unirseProyecto()" style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-check2 me-1"></i>Unirse
                </button>
            </div>
        </div>
    </div>`;
    document.body.appendChild(m);
    new bootstrap.Modal(m).show();
}

async function unirseProyecto() {
    const codigo = document.getElementById('_pCodigo')?.value.trim().toUpperCase();
    if (!codigo || codigo.length < 6) { toast('Ingresa el código completo','info'); return; }
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=unirse', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ codigo })
        });
        if (r.success) {
            bootstrap.Modal.getInstance(document.getElementById('_modalUnirse'))?.hide();
            toast(`¡Te uniste a "${r.nombre}"!`,'success');
            renderProyectos();
        } else throw new Error(r.error);
    } catch(e) { toast(e.message,'error'); }
}
// ═══ ARCHIVOS DE PROYECTO ═════════════════════════════════
const FILE_ICONS = {
    pdf:'bi-file-earmark-pdf', doc:'bi-file-earmark-word', docx:'bi-file-earmark-word',
    xls:'bi-file-earmark-excel', xlsx:'bi-file-earmark-excel',
    ppt:'bi-file-earmark-slides', pptx:'bi-file-earmark-slides',
    txt:'bi-file-earmark-text', md:'bi-markdown', sql:'bi-database',
    csv:'bi-filetype-csv', json:'bi-filetype-json', xml:'bi-filetype-xml',
    png:'bi-file-earmark-image', jpg:'bi-file-earmark-image',
    jpeg:'bi-file-earmark-image', gif:'bi-file-earmark-image',
    svg:'bi-filetype-svg', html:'bi-filetype-html', zip:'bi-file-earmark-zip',
    rar:'bi-file-earmark-zip',
};
const FILE_COLORS = {
    pdf:'#ef4444', doc:'#3b82f6', docx:'#3b82f6',
    xls:'#10b981', xlsx:'#10b981',
    ppt:'#f59e0b', pptx:'#f59e0b',
    sql:'#8b5cf6', json:'#06b6d4', csv:'#10b981',
    md:'#64748b', txt:'#94a3b8', svg:'#f97316',
    png:'#ec4899', jpg:'#ec4899', jpeg:'#ec4899',
};

function fmtBytes(b) {
    if (!b) return '0 B';
    const u=['B','KB','MB','GB']; let i=0;
    while(b>=1024&&i<3){b/=1024;i++;} return b.toFixed(i?1:0)+' '+u[i];
}

function renderArchivosList(archivos, pid) {
    if (!archivos.length) return '<p style="color:#94a3b8;font-size:.82rem;text-align:center;padding:20px">Sin archivos aún</p>';
    const BASE = '<?= BASE_URL ?>';
    return archivos.map(a => {
        const icon = (window.FILE_ICONS||{})[a.extension] || 'bi-file-earmark';
        const color = (window.FILE_COLORS||{})[a.extension] || '#667eea';
        const ext = (a.extension||'').toLowerCase();
        const vUrl = `${BASE}/api/proyectos/view?file_id=${a.id}`;
        const dUrl = `${BASE}/api/proyectos/download?file_id=${a.id}`;
        const puedeVer = ['pdf','png','jpg','jpeg','gif','webp','svg','txt','md','csv','json','html','xml'].includes(ext);
        const fn = puedeVer ? `verArchivo('${escHtml(a.nombre_original)}','${vUrl}','${dUrl}','${ext}')` : `window.open('${dUrl}','_blank')`;
        return `<div style="background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:10px;padding:10px 12px;display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:6px" onclick="${fn}"><i class="bi ${icon}" style="font-size:1.4rem;color:${color};flex-shrink:0"></i><div style="flex:1;min-width:0"><div style="font-weight:600;color:var(--txt-main);font-size:.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(a.nombre_original)}</div><div style="font-size:.67rem;color:var(--txt-muted)">${fmtBytes(a.tamano||0)} · ${escHtml(a.autor||'—')}${puedeVer?' · <span style=color:var(--primary)>clic para ver</span>':''}</div></div><div style="display:flex;gap:4px;flex-shrink:0" onclick="event.stopPropagation()"><a href="${dUrl}" download style="background:rgba(16,185,129,.1);color:#10b981;border:none;border-radius:5px;padding:3px 8px;font-size:.7rem;text-decoration:none" title="Descargar"><i class="bi bi-download"></i></a><button onclick="eliminarArchivoProyecto(${a.id},${pid})" style="background:rgba(239,68,68,.08);color:#ef4444;border:none;border-radius:5px;padding:3px 8px;font-size:.7rem;cursor:pointer" title="Eliminar"><i class="bi bi-trash3"></i></button></div></div>`;
    }).join('');
}

// ── Visor de archivos inline ────────────────────────────────────
function verArchivo(nombre, viewUrl, downloadUrl, ext) {
    document.getElementById('_modalVisorArchivo')?.remove();
    const m = document.createElement('div');
    m.id = '_modalVisorArchivo';
    m.className = 'modal fade';
    m.tabIndex = -1;

    const esImagen = ['png','jpg','jpeg','gif','webp','svg'].includes(ext);
    const esPDF    = ext === 'pdf';
    const esTexto  = ['txt','md','csv','json','html','xml','js','php','css'].includes(ext);

    let visorHtml = '';
    if (esImagen) {
        visorHtml = `<div style="text-align:center;padding:16px;background:#0a0a0a;min-height:200px;display:flex;align-items:center;justify-content:center">
            <img src="${viewUrl}" alt="${escHtml(nombre)}" style="max-width:100%;max-height:72vh;object-fit:contain;border-radius:6px"
                 onerror="this.parentElement.innerHTML='<div style=color:#888;font-size:.85rem>No se pudo cargar la imagen</div>'">
        </div>`;
    } else if (esPDF) {
        visorHtml = `<iframe src="${viewUrl}" style="width:100%;height:78vh;border:none;display:block" title="${escHtml(nombre)}"
                     onerror="this.outerHTML='<div style=padding:30px;color:#888;text-align:center>No se pudo mostrar el PDF. Use el botón Descargar.</div>'">
                     </iframe>`;
    } else if (esTexto) {
        visorHtml = `<div style="background:#0a0a14;padding:16px;max-height:72vh;overflow:auto">
            <pre id="_vtxd" style="color:#c0ccff;font-family:'Courier New',monospace;font-size:.82rem;margin:0;white-space:pre-wrap;word-break:break-word"><span style="color:#555">Cargando…</span></pre>
        </div>`;
    } else {
        visorHtml = `<div style="padding:50px;text-align:center;color:#888">
            <i class="bi bi-file-earmark" style="font-size:3rem;display:block;margin-bottom:12px;opacity:.4"></i>
            <div style="margin-bottom:16px">Vista previa no disponible para este tipo de archivo</div>
            <a href="${downloadUrl}" download="${escHtml(nombre)}"
               style="background:linear-gradient(135deg,var(--primary),var(--primary2));color:#fff;border-radius:8px;padding:9px 20px;text-decoration:none;font-size:.85rem">
                <i class="bi bi-download me-1"></i>Descargar archivo
            </a>
        </div>`;
    }

    m.innerHTML = `<div class="modal-dialog modal-dialog-centered" style="max-width:min(920px,96vw)">
        <div class="modal-content" style="background:#111128;border:1px solid #2a2a4a;border-radius:14px;overflow:hidden">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:12px 18px;display:flex;align-items:center;gap:10px">
                <i class="bi bi-file-earmark-fill" style="color:rgba(255,255,255,.8);font-size:1rem;flex-shrink:0"></i>
                <span style="color:#fff;font-weight:700;font-size:.88rem;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${escHtml(nombre)}">${escHtml(nombre)}</span>
                <a href="${downloadUrl}" download="${escHtml(nombre)}"
                   style="background:rgba(255,255,255,.2);color:#fff;border:none;border-radius:8px;padding:5px 13px;font-size:.75rem;text-decoration:none;flex-shrink:0;white-space:nowrap">
                    <i class="bi bi-download me-1"></i>Descargar
                </a>
                <button data-bs-dismiss="modal" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;flex-shrink:0;font-size:.85rem;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            ${visorHtml}
        </div>
    </div>`;
    document.body.appendChild(m);
    const bsM = new bootstrap.Modal(m);
    m.addEventListener('hidden.bs.modal', () => m.remove());
    bsM.show();

    if (esTexto) {
        fetch(viewUrl, { credentials: 'same-origin' })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
            .then(txt => { const el = m.querySelector('#_vtxd'); if (el) el.textContent = txt; })
            .catch(e  => { const el = m.querySelector('#_vtxd'); if (el) el.textContent = 'Error: ' + e.message; });
    }
}

function setProyTab(tab) {
    ['panelDiagramas','panelArchivos','panelTareas','panelObservaciones'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
    const target = { diagramas:'panelDiagramas', archivos:'panelArchivos', tareas:'panelTareas', observaciones:'panelObservaciones' }[tab];
    if (target) { const el = document.getElementById(target); if (el) el.style.display = ''; }

    ['tabPD','tabPA','tabPT','tabPO'].forEach(id => {
        const b = document.getElementById(id);
        if (b) { b.style.borderBottomColor = 'transparent'; b.style.color = 'var(--txt-muted)'; b.style.fontWeight = ''; }
    });
    const activeTab = { diagramas:'tabPD', archivos:'tabPA', tareas:'tabPT', observaciones:'tabPO' }[tab];
    if (activeTab) {
        const b = document.getElementById(activeTab);
        if (b) { b.style.borderBottomColor = 'var(--primary)'; b.style.color = 'var(--primary)'; b.style.fontWeight = '600'; }
    }

    if (tab === 'tareas') cargarTareasProyectoAlumno();
    if (tab === 'observaciones') cargarObservacionesEnProyectoAlumno();
}

// ── Cargar Tareas del Proyecto (alumno) ──────────────────────────────
async function cargarTareasProyectoAlumno() {
    const cont = document.getElementById('tareasProyContenido');
    if (!cont) return;
    const pid = _proyectoActual?.proyecto?.id;
    if (!pid) { cont.innerHTML = '<p style="color:#888">No hay proyecto activo</p>'; return; }
    cont.innerHTML = '<div style="text-align:center;padding:20px"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    try {
        const data = await apiFetch(`<?= BASE_URL ?>/api/tareas-proyecto?proyecto_id=${pid}`);
        if (data.error) throw new Error(data.error);
        const tareas = data.tareas || [];
        const miUid = <?= (int)SessionManager::usuarioId() ?>;
        const visibles = tareas.filter(t => {
            const aa = t.asignado_a != null && t.asignado_a !== '' ? parseInt(t.asignado_a, 10) : null;
            return aa === null || aa === miUid;
        });
        if (visibles.length === 0) {
            cont.innerHTML = `<div style="text-align:center;padding:40px;color:var(--txt-muted)">
                <i class="bi bi-clipboard-check" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px"></i>
                <div style="font-size:.88rem;font-weight:600;color:var(--txt-main)">Sin tareas asignadas</div>
                <div style="font-size:.78rem;color:var(--txt-muted);margin-top:6px">Cuando el maestro asigne tareas a este proyecto apareceran aqui.</div>
            </div>`;
            return;
        }
        cont.innerHTML = `<div style="display:flex;flex-direction:column;gap:12px">
            ${visibles.map(t => {
                const estado = t.estado === 'calificada' ? 'Calificada' : t.estado === 'entregada' ? 'Entregada' : 'Pendiente';
                const colorE = t.estado === 'calificada' ? '#10b981' : t.estado === 'entregada' ? '#f59e0b' : '#667eea';
                const fecha = t.fecha_limite ? new Date(t.fecha_limite).toLocaleDateString('es-MX') : 'Sin fecha limite';
                const yaEntregada = t.mi_entrega || t.mi_diagrama_id;
                return `<div style="background:var(--bg-hover);border:1.5px solid var(--bd-color);border-radius:14px;padding:16px">
                    <div style="display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap;margin-bottom:10px">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.95rem;font-weight:700;color:var(--txt-main);margin-bottom:4px">${escHtml(t.titulo)}</div>
                            <div style="font-size:.78rem;color:var(--txt-muted);line-height:1.5">${escHtml(t.descripcion||'Sin descripcion')}</div>
                        </div>
                        <div style="text-align:right;white-space:nowrap">
                            <span style="font-size:.72rem;color:${colorE};font-weight:700;display:block">${estado}</span>
                            <span style="font-size:.68rem;color:var(--txt-muted)">${fecha}</span>
                        </div>
                    </div>
                    ${yaEntregada ? `<div style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);border-radius:8px;padding:8px 12px;font-size:.78rem;color:#059669;margin-bottom:8px">
                        <i class="bi bi-check-circle me-1"></i>Ya entregaste esta tarea
                        ${t.calificacion != null ? ` · Calificacion: <strong>${t.calificacion}</strong>` : ''}
                    </div>` : ''}
                    <button onclick="abrirTarea(${t.id})"
                        style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:7px 16px;font-size:.78rem;font-weight:600;cursor:pointer;width:100%">
                        <i class="bi bi-${yaEntregada ? 'eye' : 'send'} me-1"></i>${yaEntregada ? 'Ver entrega' : 'Entregar tarea'}
                    </button>
                </div>`;
            }).join('')}
        </div>`;
    } catch(e) {
        cont.innerHTML = `<div style="color:#ef4444;padding:20px">Error al cargar tareas: ${escHtml(e.message)}</div>`;
    }
}

// ── Enviar observacion del alumno ─────────────────────────────────────
async function cargarObservacionesEnProyectoAlumno() {
    const panel = document.getElementById('obsAlumnoProyPanel');
    const pid = _proyectoActual?.proyecto?.id;
    const diags = window._proyectoDiagsAlumno || [];
    if (panel && pid) await montarObservacionesDiagramasAlumno(panel, pid, diags);
}

function renderTarjetaObsAlumno(d, pid, miObs, porDiag) {
    const comentarios = porDiag[d.id] || [];
    // Alumno solo ve observaciones del maestro/admin
    const soloDeMaestro = comentarios.filter(o => o.autor_rol === 'maestro' || o.autor_rol === 'admin');

    let obsHtml;
    if (soloDeMaestro.length === 0) {
        obsHtml = '<div style="font-size:.82rem;color:var(--txt-muted);padding:14px 0;text-align:center">'
                + '<i class="bi bi-chat-left-text" style="font-size:1.4rem;opacity:.3;display:block;margin-bottom:8px"></i>'
                + 'Sin observaciones del maestro aún</div>';
    } else {
        obsHtml = soloDeMaestro.map(function(o) {
            var fecha = new Date(o.fecha_creacion).toLocaleString('es-MX');
            return '<div style="padding:10px 12px;border:1px solid rgba(245,158,11,.25);border-left:3px solid #f59e0b;border-radius:8px;background:rgba(245,158,11,.05);margin-bottom:8px">'
                 + '<div style="font-size:.73rem;font-weight:700;color:#d97706;margin-bottom:5px">'
                 + '<i class="bi bi-person-badge me-1"></i>' + escHtml(o.autor_nombre||o.autor_username||'Maestro')
                 + '<span style="font-size:.62rem;color:var(--txt-muted);margin-left:8px;font-weight:400">' + fecha + '</span>'
                 + '</div>'
                 + '<div style="font-size:.84rem;color:var(--txt-main);white-space:pre-wrap;line-height:1.5">' + escHtml(o.texto) + '</div>'
                 + '</div>';
        }).join('');
    }

    return '<div id="obs_diagrama_' + d.id + '" style="border:1.5px solid var(--bd-color);border-radius:14px;margin-bottom:18px;overflow:hidden;background:var(--bg-card)">'
         + '<div style="padding:12px 16px;border-bottom:1px solid var(--bd-color);display:flex;align-items:center;gap:10px;flex-wrap:wrap">'
         + '<div style="flex:1;min-width:0">'
         + '<div style="font-weight:700;color:var(--txt-main);font-size:.9rem">' + escHtml(d.titulo||'Sin título') + '</div>'
         + '<div style="font-size:.72rem;color:var(--txt-muted)">por ' + escHtml(d.autor||d.autor_username||'—') + ' · v' + (d.version||1) + '</div>'
         + '</div>'
         + '<a href="<?= BASE_URL ?>/editor?id=' + d.id + '&proyecto=' + pid + '" style="background:linear-gradient(135deg,var(--primary),var(--primary2));color:#fff;border-radius:8px;padding:7px 14px;font-size:.75rem;font-weight:600;text-decoration:none">'
         + '<i class="bi bi-pencil me-1"></i>Abrir y corregir</a>'
         + '</div>'
         + '<div style="padding:14px 16px">'
         + '<div style="font-size:.72rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">'
         + '<i class="bi bi-eye me-1" style="color:var(--primary)"></i>Observaciones del maestro</div>'
         + obsHtml
         + '</div>'
         + '<div style="padding:10px 16px;border-top:1px solid var(--bd-color);background:rgba(239,68,68,.03)">'
         + '<div style="font-size:.72rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px"><i class="bi bi-chat-right-text me-1" style="color:#f59e0b"></i>¿Tuviste algún inconveniente con la tarea?</div>'
         + '<textarea id="reporte_' + d.id + '" rows="2" placeholder="Describe el inconveniente: instrucciones poco claras, error en la tarea, algo que no puedes completar..." '
         + 'style="width:100%;background:var(--bg-deep);color:var(--txt-main);border:1.5px solid var(--bd-color);border-radius:8px;padding:8px 10px;font-size:.78rem;resize:none;outline:none;font-family:inherit"></textarea>'
         + '<button onclick="enviarReporteError(' + d.id + ',' + pid + ')" '
         + 'style="margin-top:6px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.4);color:#dc2626;border-radius:7px;padding:5px 14px;font-size:.75rem;font-weight:600;cursor:pointer">'
         + '<i class="bi bi-chat-right-text me-1"></i>Enviar al maestro</button>'
         + '</div>'
         + '</div>';
}

async function montarObservacionesDiagramasAlumno(container, pid, diags) {
    if (!container) return;
    if (!pid) { container.innerHTML = '<p style="color:#888">No hay proyecto activo</p>'; return; }
    if (!diags?.length) {
        container.innerHTML = `<div style="text-align:center;padding:30px;color:var(--txt-muted)">
            <i class="bi bi-diagram-3" style="font-size:2rem;opacity:.3"></i>
            <p style="margin-top:10px">No hay diagramas en este proyecto</p></div>`;
        return;
    }
    container.innerHTML = `<div style="text-align:center;padding:24px"><div class="spinner-border spinner-border-sm text-primary"></div></div>`;
    try {
        const data = await apiFetch(`<?= BASE_URL ?>/api/observaciones?proyecto_id=${pid}`);
        const obsList = data.observaciones || [];
        const miObs = {};
        const porDiag = {};
        obsList.forEach(o => {
            const did = o.diagrama_id;
            if (!porDiag[did]) porDiag[did] = [];
            porDiag[did].push(o);
            if (Number(o.autor_id) === MI_USER_ID) miObs[did] = o.texto;
        });
        window._alumnoObsProy = { pid, diags, miObs, porDiag };
        container.innerHTML = `
            <div style="margin-bottom:14px">
                <label style="font-size:.78rem;color:var(--txt-muted)">Diagrama</label>
                <select id="selectDiagObsAlumno" class="form-select" style="font-size:.85rem;max-width:360px" onchange="filtrarObsDiagramaAlumno(this.value)">
                    <option value="all">Todos los diagramas (${diags.length})</option>
                    ${diags.map(d => `<option value="${d.id}">${escHtml(d.titulo||'Sin título')}</option>`).join('')}
                </select>
            </div>
            <div id="obsDiagramasListaAlumno">${diags.map(d => renderTarjetaObsAlumno(d, pid, miObs, porDiag)).join('')}</div>`;
    } catch (e) {
        container.innerHTML = `<p style="color:#ef4444">${escHtml(e.message)}</p>`;
    }
}

function filtrarObsDiagramaAlumno(selectedId) {
    const st = window._alumnoObsProy;
    if (!st) return;
    const lista = document.getElementById('obsDiagramasListaAlumno');
    if (!lista) return;
    const diags = selectedId === 'all' ? st.diags : st.diags.filter(d => String(d.id) === String(selectedId));
    lista.innerHTML = diags.map(d => renderTarjetaObsAlumno(d, st.pid, st.miObs, st.porDiag)).join('');
}

async function guardarObsAlumnoDiagrama(pid, did) {
    const txt = document.getElementById('obsAlum_'+did)?.value?.trim();
    if (!txt) { toast('Escribe tu observación primero','info'); return; }
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/observaciones', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ proyecto_id: pid, diagrama_id: did, texto: txt })
        });
        if (r?.success) {
            toast('Observación guardada','success');
            const panel = document.getElementById('obsAlumnoProyPanel') || document.getElementById('obsAlumnoContenido');
            const diags = window._proyectoDiagsAlumno || [];
            if (panel) await montarObservacionesDiagramasAlumno(panel, pid, diags);
        } else throw new Error(r?.error || 'Error al guardar');
    } catch (e) { toast(e.message, 'error'); }
}

// ── Reportar error del alumno al maestro ──────────────────────────────────
async function enviarReporteError(diagramaId, proyectoId) {
    const txt = document.getElementById('reporte_' + diagramaId)?.value?.trim();
    if (!txt) { toast('Describe el problema antes de enviar', 'info'); return; }
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/observaciones', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ proyecto_id: proyectoId, diagrama_id: diagramaId, texto: txt, tipo_obs: 'reporte_error' })
        });
        if (r?.success) {
            toast('Reporte enviado al maestro', 'success');
            const el = document.getElementById('reporte_' + diagramaId);
            if (el) { el.value = ''; el.placeholder = '✓ Reporte enviado'; }
        } else throw new Error(r?.error || 'Error al enviar');
    } catch(e) { toast(e.message, 'error'); }
}

// ── Nuevo Diagrama (alumno desde sidebar) ────────────────────────────
function abrirModalNuevoDiagramaAlumno() {
    abrirModalNuevo();
}

async function cargarObservacionesAlumno() {
    const cont = document.getElementById('obsAlumnoContenido');
    if (!cont) return;
    const pid = _proyectoActual?.proyecto?.id;
    if (!pid) {
        cont.innerHTML = `<div style="text-align:center;padding:30px;color:var(--txt-muted)">
            <p>Abre un proyecto desde la pestaña <strong>Proyectos</strong> para comentar diagramas.</p>
            <button onclick="switchView('proyectos')" style="margin-top:12px;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:8px 16px;font-size:.82rem;cursor:pointer">Ir a Proyectos</button>
        </div>`;
        return;
    }
    let diags = window._proyectoDiagsAlumno || _proyectoActual?.diagramas || [];
    // If diags not cached, fetch from API
    if (!diags.length && pid) {
        try {
            cont.innerHTML = '<div style="text-align:center;padding:24px"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2" style="font-size:.82rem;color:var(--txt-muted)">Cargando diagramas...</p></div>';
            const pd = await apiFetch(`<?= BASE_URL ?>/api/proyectos/${pid}`);
            diags = pd.diagramas || pd.proyecto?.diagramas || [];
            window._proyectoDiagsAlumno = diags;
        } catch(_) {}
    }
    await montarObservacionesDiagramasAlumno(cont, pid, diags);
}

async function subirArchivosProyecto(pid, files) {
    for (const file of files) {
        const fd = new FormData();
        fd.append('proyecto_id', pid);
        fd.append('archivo', file);
        try {
            const r = await fetch((window.BASE_URL||'') + '/api/proyectos/upload', { method:'POST', body:fd, credentials:'same-origin' });
            const d = await r.json();
            if (d.success) toast(`"${d.nombre}" subido correctamente`, 'success');
            else toast(d.error || 'Error al subir', 'error');
        } catch(e) { toast(e.message, 'error'); }
    }
    // Refresh detail
    if (_proyectoActual?.proyecto?.id) abrirProyecto(_proyectoActual.proyecto.id);
}

async function eliminarArchivoProyecto(fid, pid) {
    if (!confirm('¿Eliminar este archivo del proyecto?')) return;
    try {
        const r = await fetch((window.BASE_URL||'') + '/api/proyectos/del-file', {
            method:'POST', headers:{'Content-Type':'application/json'},
            credentials:'same-origin',
            body: JSON.stringify({ file_id: fid })
        });
        const d = await r.json();
        if (d.success) { toast('Archivo eliminado', 'success'); abrirProyecto(pid); }
        else toast(d.error, 'error');
    } catch(e) { toast(e.message, 'error'); }
}

function abrirDiagramaProyecto(id, titulo) {
    saveNavState({fromEditor:true});
    window.location.href = '<?= BASE_URL ?>/editor?id=' + id;
}

function hexToRgb2(hex) {
    const r=parseInt(hex.slice(1,3),16), g=parseInt(hex.slice(3,5),16), b=parseInt(hex.slice(5,7),16);
    return `${r},${g},${b}`;
}

// ── Panel de tarea (modal estilo Teams) ───────────────────────
async function abrirTarea(tareaId) {
    // Crear modal dinámico
    document.getElementById('modalTareaDetalle')?.remove();
    const modal = document.createElement('div');
    modal.id = 'modalTareaDetalle';
    modal.className = 'modal fade';
    modal.tabIndex = -1;
    modal.innerHTML = `
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:#fff;border:none;border-radius:16px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.2)">
            <div id="modalTareaBody" style="overflow-y:auto;flex:1;padding:0">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>`;
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    try {
        const [tData, dData] = await Promise.all([
            apiFetch('<?= BASE_URL ?>/api/alumno?action=mis_tareas'),
            apiFetch('<?= BASE_URL ?>/api/alumno?action=mis_diagramas_tarea')
        ]);
        const t = (tData.tareas||[]).find(x => x.id == tareaId);
        if (!t) { document.getElementById('modalTareaBody').innerHTML = '<p class="text-danger p-4">Tarea no encontrada</p>'; return; }

        const diagramas = dData.diagramas || [];
        const vencida   = t.fecha_entrega && new Date(t.fecha_entrega) < new Date();
        const entregada = t.diagrama_id != null;
        const calif     = t.calificacion != null;
        const color     = entregada ? '#10b981' : vencida ? '#ef4444' : '#f59e0b';

        document.getElementById('modalTareaBody').innerHTML = `
        <!-- Header de la tarea -->
        <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:20px 24px;border-radius:16px 16px 0 0;position:sticky;top:0;z-index:10">
            <div style="display:flex;align-items:flex-start;gap:14px">
                <div style="width:48px;height:48px;border-radius:12px;background:${color}22;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-clipboard-check" style="font-size:1.5rem;color:${color}"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <h5 style="color:#fff;margin:0 0 4px;font-size:1.05rem;text-shadow:0 1px 3px rgba(0,0,0,.2)">${escHtml(t.titulo)}</h5>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
                        <span style="font-size:.75rem;color:rgba(255,255,255,.8)"><i class="bi bi-person-badge me-1"></i>${escHtml(t.maestro_nombre||'Maestro')}</span>
                        ${t.proyecto_nombre
                            ? `<span style="font-size:.75rem;color:rgba(255,255,255,.85)"><i class="bi bi-diagram-3 me-1"></i>${escHtml(t.proyecto_nombre)}</span>`
                            : `<span style="font-size:.75rem;color:rgba(255,255,255,.8)"><i class="bi bi-collection me-1"></i>${escHtml(t.grupo_nombre||'—')}</span>`}
                        <span style="background:rgba(255,255,255,.2);color:#fff;border-radius:20px;padding:2px 10px;font-size:.7rem;font-weight:600">${entregada?(calif?'Calificada':'Entregada'):vencida?'Vencida':'Pendiente'}</span>
                    </div>
                </div>
                <button type="button" onclick="bootstrap.Modal.getInstance(document.getElementById('modalTareaDetalle')).hide()"
                    style="background:rgba(255,255,255,.2);border:none;color:#fff;font-size:1rem;cursor:pointer;flex-shrink:0;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background .2s"
                    onmouseover="this.style.background='rgba(255,255,255,.35)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
                    <i class="bi bi-x-lg"></i></button>
            </div>
        </div>

        <div style="padding:20px 24px">

        <!-- Descripción -->
        ${t.descripcion ? `<div style="background:#f8f9ff;border:1px solid #e8eaf0;border-left:4px solid var(--primary);border-radius:10px;padding:14px 16px;margin-bottom:16px">
            <div style="font-size:.72rem;color:var(--primary);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px"><i class="bi bi-card-text me-1"></i>Instrucciones</div>
            <div style="color:#444;font-size:.87rem;line-height:1.6">${escHtml(t.descripcion)}</div>
        </div>` : ''}

        <!-- Fecha y tipo -->
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px">
            <div style="background:#f8f9ff;border:1px solid #e8eaf0;border-radius:10px;padding:10px 14px;flex:1;min-width:140px">
                <div style="font-size:.68rem;color:#888;margin-bottom:2px;text-transform:uppercase;letter-spacing:.04em">Tipo de diagrama</div>
                <div style="color:var(--primary);font-size:.85rem;font-weight:600">${TIPOS_T[t.tipo_diagrama]||t.tipo_diagrama}</div>
            </div>
            ${t.fecha_entrega ? `<div style="background:${vencida&&!entregada?'#fff5f5':'#f8f9ff'};border:1px solid ${vencida&&!entregada?'#fecaca':'#e8eaf0'};border-radius:10px;padding:10px 14px;flex:1;min-width:140px">
                <div style="font-size:.68rem;color:#888;margin-bottom:2px;text-transform:uppercase;letter-spacing:.04em">Fecha de entrega</div>
                <div style="color:${vencida&&!entregada?'#dc2626':'#555'};font-size:.85rem;font-weight:600">${new Date(t.fecha_entrega).toLocaleDateString('es-MX',{weekday:'long',day:'2-digit',month:'long',year:'numeric'})}</div>
            </div>` : ''}
        </div>

        <!-- Calificación si ya tiene -->
        ${calif ? `<div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:16px 18px;margin-bottom:16px">
            <div style="display:flex;align-items:center;gap:14px">
                <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(16,185,129,.4)">
                    <span style="font-size:1.1rem;font-weight:800;color:#fff">${parseFloat(t.calificacion).toFixed(1)}</span>
                </div>
                <div>
                    <div style="color:#059669;font-weight:700;font-size:.9rem">¡Tarea calificada!</div>
                    ${t.comentario_maestro ? `<div style="color:#166534;font-size:.8rem;margin-top:4px;line-height:1.4"><i class="bi bi-chat-left-quote me-1"></i>${escHtml(t.comentario_maestro)}</div>` : '<div style="color:#888;font-size:.75rem;margin-top:2px">Sin comentarios del maestro</div>'}
                </div>
            </div>
        </div>` : ''}

        <!-- Sección de entrega -->
        <div style="background:#f8f9ff;border:1.5px solid #e8eaf0;border-radius:12px;overflow:hidden;margin-bottom:16px">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:12px 16px">
                <div style="color:#fff;font-weight:600;font-size:.85rem"><i class="bi bi-upload me-2"></i>Tu entrega</div>
            </div>
            <div style="padding:16px">

            <!-- Seleccionar diagrama existente -->
            <div style="margin-bottom:12px">
                <label style="font-size:.75rem;color:#666;font-weight:600;display:block;margin-bottom:6px"><i class="bi bi-diagram-3 me-1" style="color:var(--primary)"></i>Seleccionar un diagrama ya creado</label>
                <select id="selectDiagrama_${t.id}" onchange="actualizarAccionesDiagrama(${t.id})" style="width:100%;background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:8px;color:var(--txt-main);padding:8px 12px;font-size:.82rem;outline:none;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bd-color)'">
                    <option value="">— Elige un diagrama de tu dashboard —</option>
                    ${diagramas.map(d=>`<option value="${d.id}" ${d.id==t.diagrama_id?'selected':''}>${escHtml(d.titulo||'Sin título')} · ${TIPOS_T[d.tipo_diagrama]||d.tipo_diagrama} · ${new Date(d.fecha_modificacion).toLocaleDateString('es-MX')}</option>`).join('')}
                </select>
                <!-- Acciones sobre el diagrama seleccionado -->
                <div id="accionesDiagrama_${t.id}" style="display:${entregada && t.diagrama_id ? 'flex' : 'none'};gap:8px;margin-top:8px;flex-wrap:wrap;align-items:center">
                    <a id="btnEditarDiag_${t.id}" href="<?= BASE_URL ?>/editor?id=${t.diagrama_id||''}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:600;color:#fff;background:linear-gradient(135deg,var(--primary),var(--primary2));border-radius:7px;padding:5px 12px;text-decoration:none">
                        <i class="bi bi-pencil-square"></i>Editar diagrama
                    </a>
                    <button id="btnEliminarDiag_${t.id}" onclick="eliminarDiagramaDeTarea(${t.id}, parseInt(document.getElementById('selectDiagrama_${t.id}').value)||0)"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:600;color:#dc2626;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:7px;padding:5px 12px;cursor:pointer">
                        <i class="bi bi-trash3"></i>Eliminar y rehacerlo
                    </button>
                    <span style="font-size:.68rem;color:var(--txt-muted);margin-left:2px"><i class="bi bi-info-circle me-1"></i>Tras editar, vuelve aquí y entrega de nuevo</span>
                </div>
                ${entregada && t.diagrama_id ? '' : ''}
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="flex:1;height:1px;background:#e8eaf0"></div>
                <span style="color:#999;font-size:.72rem">O también</span>
                <div style="flex:1;height:1px;background:#e8eaf0"></div>
            </div>

            <!-- Crear nuevo diagrama -->
            <button onclick="crearParaTarea('${t.tipo_diagrama}','${escHtml(t.titulo)}')"
                style="width:100%;background:rgba(102,126,234,.12);border:1.5px dashed rgba(102,126,234,.4);border-radius:8px;padding:10px;color:var(--primary);font-size:.82rem;font-weight:500;cursor:pointer;transition:all .2s;margin-bottom:14px"
                onmouseover="this.style.background='rgba(102,126,234,.12)';this.style.borderColor='var(--primary)'"
                onmouseout="this.style.background='rgba(102,126,234,.12)';this.style.borderColor='rgba(102,126,234,.4)'">
                <i class="bi bi-plus-circle me-2"></i>Crear nuevo diagrama para esta tarea
            </button>

            <!-- Comentario del alumno -->
            <div style="margin-bottom:14px">
                <label style="font-size:.75rem;color:#888;display:block;margin-bottom:5px">Comentario (opcional)</label>
                <textarea id="comentarioAlumno_${t.id}" rows="2"
                    style="width:100%;background:var(--bg-card);border:1.5px solid var(--bd-color);border-radius:8px;color:var(--txt-main);padding:8px 12px;font-size:.82rem;resize:vertical;outline:none;transition:border-color .2s"
                    onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e8eaf0'"
                    placeholder="Añade una nota para tu maestro...">${escHtml(t.comentario_alumno||'')}</textarea>
            </div>

            <!-- Botón entregar -->
            <button onclick="entregarTarea(${t.id})"
                style="width:100%;background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:8px;padding:11px;font-size:.88rem;font-weight:600;cursor:pointer;transition:opacity .2s"
                ${!entregada&&vencida?'':''}
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="bi bi-send-fill me-2"></i>${entregada?'Actualizar entrega':'Entregar tarea'}
            </button>

            </div>
        </div>

        </div>`;
    } catch(e) {
        document.getElementById('modalTareaBody').innerHTML = `<p class="text-danger p-4">${escHtml(e.message)}</p>`;
    }
}

async function entregarTarea(tareaId) {
    const sel = document.getElementById('selectDiagrama_'+tareaId);
    const com = document.getElementById('comentarioAlumno_'+tareaId);
    const diagramaId = sel ? parseInt(sel.value) || null : null;
    const comentario = com ? com.value.trim() : '';

    if (!diagramaId) {
        toast('Selecciona un diagrama para entregar','info');
        return;
    }

    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/tareas-proyecto/entregar', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ tarea_id: tareaId, diagrama_id: diagramaId, texto: comentario })
        });
        if (r.success) {
            toast('¡Tarea entregada correctamente!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalTareaDetalle'))?.hide();
            cargarTareasProyectoAlumno();
        } else throw new Error(r.error || 'Error al entregar');
    } catch(e) { toast(e.message,'error'); }
}

// Actualiza los botones editar/eliminar cuando el alumno cambia el diagrama seleccionado
function actualizarAccionesDiagrama(tareaId) {
    const sel   = document.getElementById('selectDiagrama_' + tareaId);
    const panel = document.getElementById('accionesDiagrama_' + tareaId);
    const btnE  = document.getElementById('btnEditarDiag_'   + tareaId);
    const btnD  = document.getElementById('btnEliminarDiag_' + tareaId);
    if (!sel || !panel) return;
    const did = parseInt(sel.value) || 0;
    if (!did) { panel.style.display = 'none'; return; }
    panel.style.display = 'flex';
    if (btnE) btnE.href = '<?= BASE_URL ?>/editor?id=' + did;
    if (btnD) btnD.setAttribute('onclick', `eliminarDiagramaDeTarea(${tareaId}, ${did})`);
}

// Elimina el diagrama seleccionado para que el alumno pueda rehacerlo desde cero
async function eliminarDiagramaDeTarea(tareaId, diagramaId) {
    if (!diagramaId) { toast('Selecciona un diagrama primero', 'info'); return; }
    const nombre = document.getElementById('selectDiagrama_'+tareaId)
        ?.querySelector(`option[value="${diagramaId}"]`)?.textContent?.split(' · ')[0] || 'este diagrama';
    if (!confirm(`¿Eliminar "${nombre}"?\n\nSe borrará permanentemente. Podrás crear uno nuevo para volver a entregar la tarea.`)) return;
    try {
        const r = await apiFetch('<?= BASE_URL ?>/api/diagramas?action=eliminar', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id: diagramaId })
        });
        if (r.success || r.deleted) {
            toast('Diagrama eliminado. Crea uno nuevo para entregar.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalTareaDetalle'))?.hide();
            // Reabre el modal de la tarea con datos frescos
            setTimeout(() => abrirTarea(tareaId), 400);
        } else throw new Error(r.error || 'No se pudo eliminar');
    } catch(e) { toast(e.message, 'error'); }
}

function crearParaTarea(tipo, titulo) {
    sessionStorage.setItem('nuevoDiagrama', JSON.stringify({ titulo: titulo + ' (tarea)', tipo, descripcion: '', etiquetas: '' }));
    window.location.href = '<?= BASE_URL ?>/editor?tipo=' + tipo;
}

async function renderEstadisticas() {
    document.getElementById('contentArea').innerHTML = `<div id="statsDetail"><div class="text-center py-5"><div class="spinner-border text-primary"></div></div></div>`;

    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas');
        if (!data.success) throw new Error(data.error);

        const s = data.estadisticas;
        const totalDiagramas = s.total_diagramas || 0;

        document.getElementById('statsDetail').innerHTML = `
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="stat-card text-center">
                    <div class="stat-icon text-primary"><i class="bi bi-diagram-3"></i></div>
                    <div class="stat-num">${totalDiagramas}</div>
                    <div class="stat-label">Diagramas totales</div>
                </div></div>
                <div class="col-md-4"><div class="stat-card text-center">
                    <div class="stat-icon text-success"><i class="bi bi-hdd-stack"></i></div>
                    <div class="stat-num">${formatBytes(s.espacio_usado || 0)}</div>
                    <div class="stat-label">Espacio en disco</div>
                </div></div>
                <div class="col-md-4"><div class="stat-card text-center">
                    <div class="stat-icon text-warning"><i class="bi bi-tags"></i></div>
                    <div class="stat-num">${s.por_tipo?.length || 0}</div>
                    <div class="stat-label">Tipos distintos</div>
                </div></div>
            </div>
            <div class="stat-card">
                <h6 class="fw-600 mb-4"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Desglose por Tipo</h6>
                ${(s.por_tipo || []).length === 0
                    ? '<p class="text-muted text-center">Sin datos</p>'
                    : (s.por_tipo.map(t => {
                        const label = TIPOS[t.tipo_diagrama]?.label || t.tipo_diagrama;
                        const icon  = TIPOS[t.tipo_diagrama]?.icon || '📄';
                        const pct   = totalDiagramas ? Math.round((t.count/totalDiagramas)*100) : 0;
                        return `
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span style="font-size:1.3rem;width:32px;text-align:center">${icon}</span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="fw-500">${label}</small>
                                        <small class="text-muted">${t.count} (${pct}%)</small>
                                    </div>
                                    <div class="dist-bar"><div class="dist-fill" style="width:${pct}%"></div></div>
                                </div>
                            </div>`;
                    }).join(''))
                }
            </div>`;
    } catch (e) {
        toast(e.message, 'error');
    }
}

// ══════════════════════════════════════════════════════════════
// ACCIONES CRUD
// ══════════════════════════════════════════════════════════════
async function abrirModalNuevo(proyectoPreseleccionado = null) {
    document.getElementById('editId').value      = '';
    document.getElementById('fTitulo').value     = '';
    document.getElementById('fTipo').value       = 'usecase';
    document.getElementById('fDescripcion').value= '';
    document.getElementById('fEtiquetas').value  = '';
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nuevo Diagrama';
    document.getElementById('btnModalAction').innerHTML = '<i class="bi bi-pencil-square me-1"></i>Ir al Editor';

    // Render tipo picker with SVG icons
    const grid = document.getElementById('tipoPickerGrid');
    if (grid) {
        const lbl = (txt) => `<div style="font-size:.68rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.07em;padding:8px 0 4px">${txt}</div>`;
        grid.innerHTML = [
            lbl("Estructurales"),
            ...["class","object","package","composite","component","deployment","profile"].map(t => renderTipoOption(t, TIPOS[t]?.label||t)),
            lbl("Comportamiento"),
            ...["usecase","activity","state"].map(t => renderTipoOption(t, TIPOS[t]?.label||t)),
            lbl("Interacción"),
            ...["sequence","communication","timing","overview"].map(t => renderTipoOption(t, TIPOS[t]?.label||t))
        ].join("");
        seleccionarTipo("usecase");
    }

    // Cargar proyectos del usuario para el selector opcional
    const sel = document.getElementById('fProyecto');
    const wrap = document.getElementById('fProyectoWrap');
    if (sel) {
        sel.innerHTML = '<option value="">— Selecciona un proyecto —</option>';
        try {
            const data = await apiFetch('<?= BASE_URL ?>/api/proyectos?action=mis_proyectos');
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
        // Si se llama desde un proyecto específico, preseleccionarlo
        if (proyectoPreseleccionado) sel.value = proyectoPreseleccionado;
    }
    if (document.getElementById('btnModalAction')) {
        document.getElementById('btnModalAction').disabled = sel?.disabled || false;
    }

    modalDiagrama.show();
}

function accionModal() {
    const titulo = document.getElementById('fTitulo').value.trim();
    if (!titulo) { toast('El título no puede estar vacío', 'info'); return; }

    const tipo        = document.getElementById('fTipo').value;
    const descripcion = document.getElementById('fDescripcion').value;
    const etiquetas   = document.getElementById('fEtiquetas').value;
    const editId      = document.getElementById('editId').value;
    const proyectoId  = document.getElementById('fProyecto')?.value || '';

    if (!editId && !proyectoId) {
        toast('Debes seleccionar un proyecto antes de crear el diagrama','info');
        return;
    }

    if (editId) {
        guardarMetadatos(editId, titulo, tipo, descripcion, etiquetas);
    } else {
        const diagramaData = { titulo, tipo, descripcion, etiquetas };

        // Si hay datos de plantilla, incluirlos
        const plantillaData = sessionStorage.getItem('plantillaData');
        if (plantillaData) {
            try {
                const plantilla = JSON.parse(plantillaData);
                diagramaData.nodes       = plantilla.nodes;
                diagramaData.connections = plantilla.connections;
                diagramaData.diagramType = plantilla.diagramType;
                sessionStorage.removeItem('plantillaData');
            } catch(_) {}
        }

        if (proyectoId) diagramaData._projectId = proyectoId;

        sessionStorage.setItem('nuevoDiagrama', JSON.stringify(diagramaData));
        modalDiagrama.hide();

        // Guardar posición actual antes de ir al editor
        saveNavState({fromEditor:true});

        const url = proyectoId
            ? '<?= BASE_URL ?>/editor?tipo=' + tipo + '&proyecto=' + proyectoId
            : '<?= BASE_URL ?>/editor?tipo=' + tipo;
        window.location.href = url;
    }
}

async function guardarMetadatos(id, titulo, tipo, descripcion, etiquetas) {
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, titulo, tipo, descripcion, etiquetas, contenido: [] })
        });
        if (data.success) {
            toast('Metadatos actualizados', 'success');
            modalDiagrama.hide();
            cargarDiagramas();
        } else {
            throw new Error(data.error || 'Error al guardar');
        }
    } catch (e) {
        toast(e.message, 'error');
    }
}

function abrirDiagrama(id) {
    saveNavState({fromEditor:true});
    window.location.href = '<?= BASE_URL ?>/editor?id=' + id;
}

async function duplicarDiagrama(id, titulo) {
    toast('Duplicando…', 'info');
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas/duplicate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        if (data.success) {
            toast(`Copia de "${titulo}" creada`, 'success');
            // Ir a la primera página y recargar para ver la copia
            currentPage = 1;
            currentFilter = '';
            const fi = document.getElementById('searchInput');
            if (fi) fi.value = '';
            await cargarDiagramas();
        } else {
            throw new Error(data.error || 'Error al duplicar');
        }
    } catch (e) {
        toast(e.message, 'error');
    }
}

function pedirEliminar(id, titulo) {
    pendingDeleteId = id;
    document.getElementById('eliminarNombre').textContent = titulo;
    modalEliminar.show();
}

async function confirmarEliminar() {
    if (!pendingDeleteId) return;
    const id = pendingDeleteId;
    pendingDeleteId = null;
    modalEliminar.hide();

    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        if (data.success) {
            toast('Diagrama eliminado', 'success');
            // Recargar SOLO la sección activa, sin cambiar de vista
            const vistaActiva = document.querySelector('.nav-item-btn.active')?.id?.replace('nav-', '');
            if (vistaActiva === 'diagramas') {
                cargarDiagramas();
            } else if (vistaActiva === 'estadisticas') {
                renderEstadisticas();
            }
            // Actualizar stats del sidebar sin cambiar vista
            actualizarStatsSilencioso();
        } else {
            throw new Error(data.error || 'Error al eliminar');
        }
    } catch (e) {
        toast(e.message, 'error');
    }
}

// ── UTILS ──────────────────────────────────────────────────────
// Actualiza los contadores del dashboard en memoria sin cambiar de vista
async function actualizarStatsSilencioso() {
    try {
        const data = await apiFetch('<?= BASE_URL ?>/api/diagramas');
        if (!data.success) return;
        const s = data.estadisticas;
        // Si el dashboard está visible, actualizar sus elementos
        const el = document.getElementById('s-total');
        if (el) {
            el.textContent = s.total_diagramas || 0;
            const espacioUsado2 = s.espacio_usado || 0;
            const limiteMb2     = s.espacio_limite_mb ?? 100;
            document.getElementById('s-espacio').textContent = formatBytes(espacioUsado2);
            if (document.getElementById('s-espacio-fill')) {
                const pct2 = limiteMb2 > 0 ? Math.min(100, (espacioUsado2 / (limiteMb2*1024*1024)) * 100) : 0;
                document.getElementById('s-espacio-fill').style.width = pct2.toFixed(1) + '%';
                document.getElementById('s-espacio-fill').style.background = pct2 > 90 ? '#ef4444' : pct2 > 70 ? '#f59e0b' : '#10b981';
                document.getElementById('s-espacio-label').textContent = limiteMb2 > 0 ? `${pct2.toFixed(1)}% de ${limiteMb2} MB` : 'Sin límite';
            }
            document.getElementById('s-tipos').textContent   = s.por_tipo?.length || 0;
        }
    } catch (_) { /* silencioso */ }
}
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// ══════════════════════════════════════════════════════════════
// PLANTILLAS
// ══════════════════════════════════════════════════════════════

// Definición de plantillas por tipo de diagrama
const PLANTILLAS = {
    usecase: [
        {
            id: 'usecase_basico',
            titulo: 'Sistema de Login Básico',
            descripcion: 'Diagrama de casos de uso para un sistema de autenticación simple con usuario y administrador.',
            nodes: [
                { id: 'actor_usuario', type: 'actor', text: 'Usuario', x: 100, y: 150, width: 80, height: 100 },
                { id: 'usecase_login', type: 'usecase', text: 'Iniciar Sesión', x: 250, y: 120, width: 120, height: 60 },
                { id: 'usecase_logout', type: 'usecase', text: 'Cerrar Sesión', x: 250, y: 200, width: 120, height: 60 },
                { id: 'system_auth', type: 'system', text: 'Sistema de Autenticación', x: 450, y: 100, width: 200, height: 200 }
            ],
            connections: [
                { fromNode: 'actor_usuario', toNode: 'usecase_login', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '' },
                { fromNode: 'actor_usuario', toNode: 'usecase_logout', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '' },
                { fromNode: 'usecase_login', toNode: 'system_auth', fromSide: 'right', toSide: 'left', type: 'include', label: '«include»' },
                { fromNode: 'usecase_logout', toNode: 'system_auth', fromSide: 'right', toSide: 'left', type: 'include', label: '«include»' }
            ]
        },
        {
            id: 'usecase_ecommerce',
            titulo: 'Plataforma E-commerce',
            descripcion: 'Diagrama completo para una tienda en línea con comprador, vendedor y sistema de pagos.',
            nodes: [
                { id: 'actor_comprador', type: 'actor', text: 'Comprador', x: 50, y: 100, width: 80, height: 100 },
                { id: 'actor_vendedor', type: 'actor', text: 'Vendedor', x: 50, y: 250, width: 80, height: 100 },
                { id: 'usecase_buscar', type: 'usecase', text: 'Buscar Productos', x: 200, y: 80, width: 130, height: 60 },
                { id: 'usecase_comprar', type: 'usecase', text: 'Realizar Compra', x: 200, y: 150, width: 130, height: 60 },
                { id: 'usecase_pagar', type: 'usecase', text: 'Procesar Pago', x: 200, y: 220, width: 130, height: 60 },
                { id: 'usecase_vender', type: 'usecase', text: 'Publicar Producto', x: 200, y: 290, width: 130, height: 60 },
                { id: 'system_tienda', type: 'system', text: 'Sistema de Tienda', x: 400, y: 150, width: 180, height: 200 }
            ],
            connections: [
                { fromNode: 'actor_comprador', toNode: 'usecase_buscar', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '' },
                { fromNode: 'actor_comprador', toNode: 'usecase_comprar', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '' },
                { fromNode: 'actor_vendedor', toNode: 'usecase_vender', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '' },
                { fromNode: 'usecase_buscar', toNode: 'system_tienda', fromSide: 'right', toSide: 'left', type: 'include', label: '«include»' },
                { fromNode: 'usecase_comprar', toNode: 'system_tienda', fromSide: 'right', toSide: 'left', type: 'include', label: '«include»' },
                { fromNode: 'usecase_pagar', toNode: 'system_tienda', fromSide: 'right', toSide: 'left', type: 'include', label: '«include»' },
                { fromNode: 'usecase_vender', toNode: 'system_tienda', fromSide: 'right', toSide: 'left', type: 'include', label: '«include»' },
                { fromNode: 'usecase_comprar', toNode: 'usecase_pagar', fromSide: 'bottom', toSide: 'top', type: 'include', label: '«include»' }
            ]
        }
    ],
    class: [
        {
            id: 'class_basico',
            titulo: 'Sistema de Biblioteca',
            descripcion: 'Modelo de clases básico para un sistema de gestión de biblioteca con libros, usuarios y préstamos.',
            nodes: [
                { id: 'class_libro', type: 'class', text: 'Libro', x: 100, y: 100, width: 150, height: 120, attributes: 'titulo: String\nautor: String\nisbn: String\nanio: int', methods: 'prestar()\ndevolver()\nestado(): String' },
                { id: 'class_usuario', type: 'class', text: 'Usuario', x: 300, y: 100, width: 150, height: 120, attributes: 'id: int\nnombre: String\nemail: String\ntelefono: String', methods: 'registrar()\nactualizar()' },
                { id: 'class_prestamo', type: 'class', text: 'Prestamo', x: 200, y: 250, width: 150, height: 120, attributes: 'fechaPrestamo: Date\nfechaDevolucion: Date\nestado: String', methods: 'crear()\nfinalizar()' }
            ],
            connections: [
                { fromNode: 'class_usuario', toNode: 'class_prestamo', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '1..*' },
                { fromNode: 'class_libro', toNode: 'class_prestamo', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '1..1' }
            ]
        },
        {
            id: 'class_herencia',
            titulo: 'Jerarquía de Empleados',
            descripcion: 'Ejemplo de herencia en diagramas de clases con empleados de diferentes tipos.',
            nodes: [
                { id: 'class_empleado', type: 'class', text: 'Empleado', x: 200, y: 50, width: 150, height: 120, attributes: 'id: int\nnombre: String\nsalario: double\nfechaContratacion: Date', methods: 'calcularSalario(): double\nobtenerAntiguedad(): int' },
                { id: 'class_gerente', type: 'class', text: 'Gerente', x: 100, y: 200, width: 150, height: 120, attributes: 'departamento: String\nbono: double', methods: 'gestionarEquipo()\nrevisarReportes()' },
                { id: 'class_desarrollador', type: 'class', text: 'Desarrollador', x: 300, y: 200, width: 150, height: 120, attributes: 'lenguajes: String[]\nnivel: String', methods: 'programar()\nrevisarCodigo()' },
                { id: 'class_proyecto', type: 'class', text: 'Proyecto', x: 200, y: 350, width: 150, height: 120, attributes: 'nombre: String\nfechaInicio: Date\nfechaFin: Date\npresupuesto: double', methods: 'asignarEmpleados()\ncalcularProgreso()' }
            ],
            connections: [
                { fromNode: 'class_gerente', toNode: 'class_empleado', fromSide: 'top', toSide: 'bottom', type: 'herencia', label: '' },
                { fromNode: 'class_desarrollador', toNode: 'class_empleado', fromSide: 'top', toSide: 'bottom', type: 'herencia', label: '' },
                { fromNode: 'class_gerente', toNode: 'class_proyecto', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '1..*' },
                { fromNode: 'class_desarrollador', toNode: 'class_proyecto', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '1..*' }
            ]
        }
    ],
    sequence: [
        {
            id: 'sequence_basico',
            titulo: 'Interacción de Login',
            descripcion: 'Diagrama de secuencia básico mostrando el proceso de autenticación de un usuario.',
            nodes: [
                { id: 'actor_user', type: 'actor', text: 'Usuario', x: 50, y: 50, width: 80, height: 100 },
                { id: 'lifeline_ui', type: 'lifeline', text: 'InterfazUsuario', x: 200, y: 50, width: 120, height: 400 },
                { id: 'lifeline_auth', type: 'lifeline', text: 'ServicioAuth', x: 350, y: 50, width: 120, height: 400 },
                { id: 'lifeline_db', type: 'lifeline', text: 'BaseDatos', x: 500, y: 50, width: 120, height: 400 },
                { id: 'activation_ui', type: 'activation', text: '', x: 200, y: 120, width: 120, height: 60 },
                { id: 'activation_auth', type: 'activation', text: '', x: 350, y: 180, width: 120, height: 120 },
                { id: 'activation_db', type: 'activation', text: '', x: 500, y: 240, width: 120, height: 60 }
            ],
            connections: [
                { fromNode: 'actor_user', toNode: 'activation_ui', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'ingresarCredenciales()' },
                { fromNode: 'activation_ui', toNode: 'activation_auth', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'validarUsuario(user,pass)' },
                { fromNode: 'activation_auth', toNode: 'activation_db', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'buscarUsuario(user)' },
                { fromNode: 'activation_db', toNode: 'activation_auth', fromSide: 'left', toSide: 'right', type: 'mensaje-retorno', label: 'usuarioData' },
                { fromNode: 'activation_auth', toNode: 'activation_ui', fromSide: 'left', toSide: 'right', type: 'mensaje-retorno', label: 'tokenSesion' }
            ]
        },
        {
            id: 'sequence_complejo',
            titulo: 'Procesamiento de Pedido',
            descripcion: 'Diagrama de secuencia complejo mostrando el flujo completo de procesamiento de un pedido en un e-commerce.',
            nodes: [
                { id: 'actor_customer', type: 'actor', text: 'Cliente', x: 50, y: 50, width: 80, height: 100 },
                { id: 'lifeline_web', type: 'lifeline', text: 'WebApp', x: 200, y: 50, width: 120, height: 500 },
                { id: 'lifeline_order', type: 'lifeline', text: 'ServicioPedido', x: 350, y: 50, width: 120, height: 500 },
                { id: 'lifeline_payment', type: 'lifeline', text: 'ServicioPago', x: 500, y: 50, width: 120, height: 500 },
                { id: 'lifeline_inventory', type: 'lifeline', text: 'Inventario', x: 650, y: 50, width: 120, height: 500 },
                { id: 'activation_web1', type: 'activation', text: '', x: 200, y: 120, width: 120, height: 60 },
                { id: 'activation_order1', type: 'activation', text: '', x: 350, y: 180, width: 120, height: 80 },
                { id: 'activation_payment', type: 'activation', text: '', x: 500, y: 220, width: 120, height: 60 },
                { id: 'activation_inventory', type: 'activation', text: '', x: 650, y: 240, width: 120, height: 60 },
                { id: 'activation_order2', type: 'activation', text: '', x: 350, y: 320, width: 120, height: 60 }
            ],
            connections: [
                { fromNode: 'actor_customer', toNode: 'activation_web1', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'realizarPedido(items)' },
                { fromNode: 'activation_web1', toNode: 'activation_order1', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'crearPedido(customerId,items)' },
                { fromNode: 'activation_order1', toNode: 'activation_inventory', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'verificarStock(items)' },
                { fromNode: 'activation_inventory', toNode: 'activation_order1', fromSide: 'left', toSide: 'right', type: 'mensaje-retorno', label: 'stockDisponible' },
                { fromNode: 'activation_order1', toNode: 'activation_payment', fromSide: 'right', toSide: 'left', type: 'mensaje-sincrono', label: 'procesarPago(total,paymentInfo)' },
                { fromNode: 'activation_payment', toNode: 'activation_order1', fromSide: 'left', toSide: 'right', type: 'mensaje-retorno', label: 'pagoConfirmado' },
                { fromNode: 'activation_order1', toNode: 'activation_order2', fromSide: 'bottom', toSide: 'top', type: 'mensaje-sincrono', label: 'confirmarPedido()' },
                { fromNode: 'activation_order2', toNode: 'activation_web1', fromSide: 'left', toSide: 'right', type: 'mensaje-retorno', label: 'pedidoConfirmado' }
            ]
        }
    ],
    activity: [
        {
            id: 'activity_basico',
            titulo: 'Proceso de Compra',
            descripcion: 'Diagrama de actividades básico mostrando el flujo de compra de un producto.',
            nodes: [
                { id: 'start', type: 'start', text: '', x: 200, y: 50, width: 40, height: 40 },
                { id: 'activity_buscar', type: 'activity', text: 'Buscar producto', x: 180, y: 120, width: 120, height: 60 },
                { id: 'decision_stock', type: 'decision', text: '¿Producto disponible?', x: 180, y: 200, width: 120, height: 80 },
                { id: 'activity_comprar', type: 'activity', text: 'Agregar al carrito', x: 50, y: 300, width: 120, height: 60 },
                { id: 'activity_pagar', type: 'activity', text: 'Procesar pago', x: 50, y: 380, width: 120, height: 60 },
                { id: 'activity_no_stock', type: 'activity', text: 'Mostrar mensaje\n"Producto agotado"', x: 310, y: 300, width: 120, height: 60 },
                { id: 'end_exitoso', type: 'end', text: '', x: 50, y: 460, width: 40, height: 40 },
                { id: 'end_fallido', type: 'end', text: '', x: 310, y: 380, width: 40, height: 40 }
            ],
            connections: [
                { fromNode: 'start', toNode: 'activity_buscar', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'activity_buscar', toNode: 'decision_stock', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'decision_stock', toNode: 'activity_comprar', fromSide: 'left', toSide: 'top', type: 'asociacion', label: 'Sí' },
                { fromNode: 'decision_stock', toNode: 'activity_no_stock', fromSide: 'right', toSide: 'top', type: 'asociacion', label: 'No' },
                { fromNode: 'activity_comprar', toNode: 'activity_pagar', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'activity_pagar', toNode: 'end_exitoso', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'activity_no_stock', toNode: 'end_fallido', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' }
            ]
        },
        {
            id: 'activity_bucle',
            titulo: 'Validación con Reintentos',
            descripcion: 'Diagrama de actividades mostrando un proceso con validación y reintentos limitados.',
            nodes: [
                { id: 'start', type: 'start', text: '', x: 250, y: 30, width: 40, height: 40 },
                { id: 'activity_ingresar', type: 'activity', text: 'Ingresar datos', x: 200, y: 100, width: 120, height: 60 },
                { id: 'activity_validar', type: 'activity', text: 'Validar datos', x: 200, y: 180, width: 120, height: 60 },
                { id: 'decision_valido', type: 'decision', text: '¿Datos válidos?', x: 200, y: 260, width: 120, height: 80 },
                { id: 'activity_procesar', type: 'activity', text: 'Procesar datos', x: 50, y: 360, width: 120, height: 60 },
                { id: 'activity_error', type: 'activity', text: 'Mostrar error', x: 350, y: 360, width: 120, height: 60 },
                { id: 'decision_reintentar', type: 'decision', text: '¿Reintentar?', x: 350, y: 440, width: 120, height: 80 },
                { id: 'end_exitoso', type: 'end', text: '', x: 50, y: 500, width: 40, height: 40 },
                { id: 'end_fallido', type: 'end', text: '', x: 350, y: 520, width: 40, height: 40 }
            ],
            connections: [
                { fromNode: 'start', toNode: 'activity_ingresar', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'activity_ingresar', toNode: 'activity_validar', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'activity_validar', toNode: 'decision_valido', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'decision_valido', toNode: 'activity_procesar', fromSide: 'left', toSide: 'top', type: 'asociacion', label: 'Sí' },
                { fromNode: 'decision_valido', toNode: 'activity_error', fromSide: 'right', toSide: 'top', type: 'asociacion', label: 'No' },
                { fromNode: 'activity_procesar', toNode: 'end_exitoso', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'activity_error', toNode: 'decision_reintentar', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '' },
                { fromNode: 'decision_reintentar', toNode: 'activity_ingresar', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'Sí' },
                { fromNode: 'decision_reintentar', toNode: 'end_fallido', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'No' }
            ]
        }
    ],
    state: [
        {
            id: 'state_basico',
            titulo: 'Estados de Usuario',
            descripcion: 'Diagrama de estados básico mostrando los diferentes estados por los que puede pasar un usuario en el sistema.',
            nodes: [
                { id: 'initial', type: 'initial', text: '', x: 100, y: 100, width: 30, height: 30 },
                { id: 'state_inactivo', type: 'state', text: 'Inactivo', x: 200, y: 80, width: 120, height: 60 },
                { id: 'state_activo', type: 'state', text: 'Activo', x: 200, y: 160, width: 120, height: 60 },
                { id: 'state_suspendido', type: 'state', text: 'Suspendido', x: 350, y: 160, width: 120, height: 60 },
                { id: 'final', type: 'final', text: '', x: 500, y: 120, width: 30, height: 30 }
            ],
            connections: [
                { fromNode: 'initial', toNode: 'state_inactivo', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'registro' },
                { fromNode: 'state_inactivo', toNode: 'state_activo', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'activar' },
                { fromNode: 'state_activo', toNode: 'state_suspendido', fromSide: 'bottom', toSide: 'left', type: 'asociacion', label: 'suspender' },
                { fromNode: 'state_suspendido', toNode: 'state_activo', fromSide: 'top', toSide: 'right', type: 'asociacion', label: 'reactivar' },
                { fromNode: 'state_activo', toNode: 'final', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'eliminar' }
            ]
        },
        {
            id: 'state_maquina',
            titulo: 'Máquina Expendedora',
            descripcion: 'Diagrama de estados para una máquina expendedora mostrando selección, pago y dispensación de productos.',
            nodes: [
                { id: 'initial', type: 'initial', text: '', x: 50, y: 150, width: 30, height: 30 },
                { id: 'state_esperando', type: 'state', text: 'Esperando\nSelección', x: 150, y: 120, width: 120, height: 60 },
                { id: 'state_seleccionado', type: 'state', text: 'Producto\nSeleccionado', x: 300, y: 120, width: 120, height: 60 },
                { id: 'state_pagando', type: 'state', text: 'Procesando\nPago', x: 450, y: 120, width: 120, height: 60 },
                { id: 'state_entregando', type: 'state', text: 'Entregando\nProducto', x: 300, y: 220, width: 120, height: 60 },
                { id: 'state_cancelado', type: 'state', text: 'Operación\nCancelada', x: 150, y: 220, width: 120, height: 60 },
                { id: 'final', type: 'final', text: '', x: 550, y: 150, width: 30, height: 30 }
            ],
            connections: [
                { fromNode: 'initial', toNode: 'state_esperando', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '' },
                { fromNode: 'state_esperando', toNode: 'state_seleccionado', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'seleccionarProducto' },
                { fromNode: 'state_seleccionado', toNode: 'state_pagando', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'confirmarPago' },
                { fromNode: 'state_pagando', toNode: 'state_entregando', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'pagoExitoso' },
                { fromNode: 'state_entregando', toNode: 'final', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'productoEntregado' },
                { fromNode: 'state_seleccionado', toNode: 'state_cancelado', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'cancelar' },
                { fromNode: 'state_pagando', toNode: 'state_cancelado', fromSide: 'bottom', toSide: 'right', type: 'asociacion', label: 'pagoRechazado' },
                { fromNode: 'state_cancelado', toNode: 'state_esperando', fromSide: 'left', toSide: 'bottom', type: 'asociacion', label: 'reiniciar' }
            ]
        }
    ]
};

// Agregar más plantillas para completar el mínimo solicitado
PLANTILLAS.component = PLANTILLAS.component || [
    {
        id: 'component_basico',
        titulo: 'Arquitectura Web Básica',
        descripcion: 'Componentes básicos de una aplicación web con interfaz, lógica de negocio y base de datos.',
        nodes: [
            { id: 'comp_ui', type: 'component', text: 'InterfazUsuario', x: 100, y: 100, width: 150, height: 80 },
            { id: 'comp_business', type: 'component', text: 'LogicaNegocio', x: 300, y: 100, width: 150, height: 80 },
            { id: 'comp_data', type: 'component', text: 'AccesoDatos', x: 200, y: 220, width: 150, height: 80 },
            { id: 'interface_repo', type: 'interface', text: 'IRepository', x: 450, y: 160, width: 120, height: 60 }
        ],
        connections: [
            { fromNode: 'comp_ui', toNode: 'comp_business', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'uses' },
            { fromNode: 'comp_business', toNode: 'comp_data', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'uses' },
            { fromNode: 'comp_data', toNode: 'interface_repo', fromSide: 'right', toSide: 'left', type: 'realizacion', label: '' }
        ]
    },
    {
        id: 'component_microservicios',
        titulo: 'Arquitectura de Microservicios',
        descripcion: 'Sistema de microservicios con API Gateway, servicios independientes y base de datos distribuida.',
        nodes: [
            { id: 'comp_gateway', type: 'component', text: 'ApiGateway', x: 50, y: 100, width: 120, height: 60 },
            { id: 'comp_auth', type: 'component', text: 'AuthService', x: 200, y: 50, width: 120, height: 60 },
            { id: 'comp_user', type: 'component', text: 'UserService', x: 350, y: 50, width: 120, height: 60 },
            { id: 'comp_product', type: 'component', text: 'ProductService', x: 200, y: 150, width: 120, height: 60 },
            { id: 'comp_order', type: 'component', text: 'OrderService', x: 350, y: 150, width: 120, height: 60 },
            { id: 'interface_rest', type: 'interface', text: 'REST API', x: 50, y: 200, width: 100, height: 40 }
        ],
        connections: [
            { fromNode: 'comp_gateway', toNode: 'comp_auth', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'routes' },
            { fromNode: 'comp_gateway', toNode: 'comp_user', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'routes' },
            { fromNode: 'comp_gateway', toNode: 'comp_product', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'routes' },
            { fromNode: 'comp_gateway', toNode: 'comp_order', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'routes' },
            { fromNode: 'comp_auth', toNode: 'interface_rest', fromSide: 'bottom', toSide: 'top', type: 'realizacion', label: '' },
            { fromNode: 'comp_user', toNode: 'interface_rest', fromSide: 'bottom', toSide: 'top', type: 'realizacion', label: '' },
            { fromNode: 'comp_product', toNode: 'interface_rest', fromSide: 'bottom', toSide: 'top', type: 'realizacion', label: '' },
            { fromNode: 'comp_order', toNode: 'interface_rest', fromSide: 'bottom', toSide: 'top', type: 'realizacion', label: '' }
        ]
    }
];

PLANTILLAS.deployment = PLANTILLAS.deployment || [
    {
        id: 'deployment_basico',
        titulo: 'Despliegue Web Simple',
        descripcion: 'Arquitectura de despliegue básica con servidor web, aplicación y base de datos.',
        nodes: [
            { id: 'node_client', type: 'device', text: 'ClienteWeb', x: 50, y: 100, width: 100, height: 60 },
            { id: 'node_server', type: 'node', text: 'ServidorWeb', x: 200, y: 80, width: 120, height: 100 },
            { id: 'node_db', type: 'node', text: 'ServidorBD', x: 350, y: 80, width: 120, height: 100 },
            { id: 'artifact_app', type: 'artifact', text: 'AppWeb.war', x: 210, y: 120, width: 100, height: 40 },
            { id: 'artifact_db', type: 'artifact', text: 'BaseDatos', x: 360, y: 120, width: 100, height: 40 }
        ],
        connections: [
            { fromNode: 'node_client', toNode: 'node_server', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'HTTP' },
            { fromNode: 'node_server', toNode: 'node_db', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'JDBC' },
            { fromNode: 'artifact_app', toNode: 'node_server', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'deployed on' },
            { fromNode: 'artifact_db', toNode: 'node_db', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'deployed on' }
        ]
    },
    {
        id: 'deployment_cloud',
        titulo: 'Arquitectura Cloud',
        descripcion: 'Despliegue en la nube con balanceador de carga, servidores escalables y almacenamiento distribuido.',
        nodes: [
            { id: 'device_user', type: 'device', text: 'Usuario', x: 50, y: 150, width: 80, height: 60 },
            { id: 'node_lb', type: 'node', text: 'LoadBalancer', x: 180, y: 140, width: 100, height: 80 },
            { id: 'node_web1', type: 'node', text: 'WebServer1', x: 320, y: 100, width: 100, height: 60 },
            { id: 'node_web2', type: 'node', text: 'WebServer2', x: 320, y: 180, width: 100, height: 60 },
            { id: 'node_cache', type: 'node', text: 'CacheServer', x: 450, y: 140, width: 100, height: 80 },
            { id: 'node_storage', type: 'node', text: 'Storage', x: 580, y: 140, width: 100, height: 80 }
        ],
        connections: [
            { fromNode: 'device_user', toNode: 'node_lb', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'HTTPS' },
            { fromNode: 'node_lb', toNode: 'node_web1', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'distribute' },
            { fromNode: 'node_lb', toNode: 'node_web2', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'distribute' },
            { fromNode: 'node_web1', toNode: 'node_cache', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'cache' },
            { fromNode: 'node_web2', toNode: 'node_cache', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'cache' },
            { fromNode: 'node_cache', toNode: 'node_storage', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'persist' }
        ]
    }
];

PLANTILLAS.object = PLANTILLAS.object || [
    {
        id: 'object_basico',
        titulo: 'Instancias de Objetos',
        descripcion: 'Ejemplo de diagrama de objetos mostrando instancias concretas con sus valores.',
        nodes: [
            { id: 'obj_persona', type: 'object', text: 'persona1:Persona', x: 100, y: 100, width: 150, height: 80, attributes: 'nombre="Juan"\nedad=25\nemail="juan@email.com"' },
            { id: 'obj_cuenta', type: 'object', text: 'cuenta1:CuentaBancaria', x: 300, y: 100, width: 180, height: 80, attributes: 'numero="123456"\nsaldo=1500.50\ntipo="Ahorros"' },
            { id: 'obj_transaccion', type: 'object', text: 'trans1:Transaccion', x: 200, y: 220, width: 160, height: 80, attributes: 'id=1001\nmonto=500.00\nfecha="2024-01-15"' }
        ],
        connections: [
            { fromNode: 'obj_persona', toNode: 'obj_cuenta', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'tiene' },
            { fromNode: 'obj_cuenta', toNode: 'obj_transaccion', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'realiza' }
        ]
    },
    {
        id: 'object_complejo',
        titulo: 'Sistema de Reserva',
        descripcion: 'Instancias de objetos en un sistema de reservas de hotel con relaciones complejas.',
        nodes: [
            { id: 'obj_cliente', type: 'object', text: 'cliente1:Cliente', x: 50, y: 100, width: 140, height: 80, attributes: 'id=1\nnombre="María"\ndni="12345678"' },
            { id: 'obj_hotel', type: 'object', text: 'hotel1:Hotel', x: 250, y: 50, width: 140, height: 80, attributes: 'id=1\nnombre="Hotel Plaza"\nciudad="Madrid"' },
            { id: 'obj_habitacion', type: 'object', text: 'hab101:Habitacion', x: 250, y: 160, width: 140, height: 80, attributes: 'numero=101\ntipo="Doble"\nprecio=120.00' },
            { id: 'obj_reserva', type: 'object', text: 'reserva1:Reserva', x: 450, y: 100, width: 150, height: 80, attributes: 'id=100\nfechaEntrada="2024-02-15"\nfechaSalida="2024-02-17"' }
        ],
        connections: [
            { fromNode: 'obj_cliente', toNode: 'obj_reserva', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'realiza' },
            { fromNode: 'obj_hotel', toNode: 'obj_habitacion', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: 'contiene' },
            { fromNode: 'obj_habitacion', toNode: 'obj_reserva', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 'reservada_en' }
        ]
    }
];

PLANTILLAS.communication = PLANTILLAS.communication || [
    {
        id: 'communication_basico',
        titulo: 'Interacción de Objetos',
        descripcion: 'Diagrama de comunicación básico mostrando mensajes entre objetos en un escenario simple.',
        nodes: [
            { id: 'obj_user', type: 'object', text: 'usuario:Usuario', x: 100, y: 100, width: 120, height: 60 },
            { id: 'obj_system', type: 'object', text: 'sistema:Sistema', x: 300, y: 100, width: 120, height: 60 },
            { id: 'obj_db', type: 'object', text: 'bd:BaseDatos', x: 200, y: 200, width: 120, height: 60 }
        ],
        connections: [
            { fromNode: 'obj_user', toNode: 'obj_system', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '1: login(user,pass)' },
            { fromNode: 'obj_system', toNode: 'obj_db', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '2: validarUsuario()' },
            { fromNode: 'obj_db', toNode: 'obj_system', fromSide: 'top', toSide: 'bottom', type: 'asociacion', label: '3: usuarioData' },
            { fromNode: 'obj_system', toNode: 'obj_user', fromSide: 'left', toSide: 'right', type: 'asociacion', label: '4: tokenSesion' }
        ]
    },
    {
        id: 'communication_complejo',
        titulo: 'Flujo de Compra Online',
        descripcion: 'Interacciones complejas en un proceso de compra electrónica con múltiples objetos.',
        nodes: [
            { id: 'obj_customer', type: 'object', text: 'cliente:Cliente', x: 50, y: 100, width: 120, height: 60 },
            { id: 'obj_cart', type: 'object', text: 'carrito:CarritoCompra', x: 200, y: 50, width: 140, height: 60 },
            { id: 'obj_catalog', type: 'object', text: 'catalogo:Catalogo', x: 200, y: 150, width: 140, height: 60 },
            { id: 'obj_payment', type: 'object', text: 'pago:ServicioPago', x: 400, y: 50, width: 140, height: 60 },
            { id: 'obj_inventory', type: 'object', text: 'inventario:Inventario', x: 400, y: 150, width: 140, height: 60 },
            { id: 'obj_order', type: 'object', text: 'pedido:Pedido', x: 300, y: 220, width: 120, height: 60 }
        ],
        connections: [
            { fromNode: 'obj_customer', toNode: 'obj_cart', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '1: agregarProducto()' },
            { fromNode: 'obj_cart', toNode: 'obj_catalog', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '2: verificarDisponibilidad()' },
            { fromNode: 'obj_catalog', toNode: 'obj_inventory', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '3: consultarStock()' },
            { fromNode: 'obj_inventory', toNode: 'obj_catalog', fromSide: 'left', toSide: 'right', type: 'asociacion', label: '4: stockInfo' },
            { fromNode: 'obj_cart', toNode: 'obj_payment', fromSide: 'right', toSide: 'left', type: 'asociacion', label: '5: procesarPago()' },
            { fromNode: 'obj_payment', toNode: 'obj_order', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '6: crearPedido()' },
            { fromNode: 'obj_order', toNode: 'obj_inventory', fromSide: 'bottom', toSide: 'top', type: 'asociacion', label: '7: actualizarStock()' }
        ]
    }
];

PLANTILLAS.timing = PLANTILLAS.timing || [
    {
        id: 'timing_basico',
        titulo: 'Diagrama de Tiempo Simple',
        descripcion: 'Línea de tiempo básica mostrando estados de un componente a lo largo del tiempo.',
        nodes: [
            { id: 'lifeline_comp', type: 'lifeline', text: 'ComponenteA', x: 100, y: 50, width: 120, height: 300 },
            { id: 'event_start', type: 'event', text: 'Inicio', x: 50, y: 100, width: 80, height: 40 },
            { id: 'event_active', type: 'event', text: 'Activo', x: 50, y: 180, width: 80, height: 40 },
            { id: 'event_stop', type: 'event', text: 'Detenido', x: 50, y: 260, width: 80, height: 40 }
        ],
        connections: [
            { fromNode: 'event_start', toNode: 'lifeline_comp', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=0' },
            { fromNode: 'event_active', toNode: 'lifeline_comp', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=5s' },
            { fromNode: 'event_stop', toNode: 'lifeline_comp', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=15s' }
        ]
    },
    {
        id: 'timing_complejo',
        titulo: 'Sistema con Múltiples Estados',
        descripcion: 'Diagrama de tiempo complejo mostrando múltiples componentes con estados concurrentes.',
        nodes: [
            { id: 'lifeline_server', type: 'lifeline', text: 'ServidorWeb', x: 100, y: 50, width: 120, height: 350 },
            { id: 'lifeline_db', type: 'lifeline', text: 'BaseDatos', x: 250, y: 50, width: 120, height: 350 },
            { id: 'lifeline_cache', type: 'lifeline', text: 'Cache', x: 400, y: 50, width: 120, height: 350 },
            { id: 'event_init', type: 'event', text: 'Inicialización', x: 50, y: 80, width: 100, height: 40 },
            { id: 'event_load', type: 'event', text: 'Alta Carga', x: 50, y: 150, width: 100, height: 40 },
            { id: 'event_fail', type: 'event', text: 'Falla DB', x: 50, y: 220, width: 100, height: 40 },
            { id: 'event_recover', type: 'event', text: 'Recuperación', x: 50, y: 290, width: 100, height: 40 }
        ],
        connections: [
            { fromNode: 'event_init', toNode: 'lifeline_server', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=0' },
            { fromNode: 'event_init', toNode: 'lifeline_db', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=0' },
            { fromNode: 'event_init', toNode: 'lifeline_cache', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=0' },
            { fromNode: 'event_load', toNode: 'lifeline_cache', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=10s' },
            { fromNode: 'event_fail', toNode: 'lifeline_db', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=25s' },
            { fromNode: 'event_recover', toNode: 'lifeline_db', fromSide: 'right', toSide: 'left', type: 'asociacion', label: 't=35s' }
        ]
    }
];

async function renderPlantillas() {
    const contentArea = document.getElementById('contentArea');
    
    contentArea.innerHTML = `
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Plantillas de Diagramas</strong><br>
                    Selecciona una plantilla para comenzar rápidamente con diagramas predefinidos. Cada plantilla incluye elementos y conexiones listas para usar.
                </div>
            </div>
        </div>
        
        <div class="row g-4" id="plantillasContainer">
            <!-- Las plantillas se cargarán aquí -->
        </div>
    `;
    
    // Cargar plantillas por tipo
    const container = document.getElementById('plantillasContainer');
    let html = '';
    
    Object.keys(PLANTILLAS).forEach(tipo => {
        const plantillasTipo = PLANTILLAS[tipo];
        const tipoInfo = TIPOS[tipo];
        
        html += `
            <div class="col-12">
                <h4 class="mb-3">
                    <i class="bi ${TIPO_ICONS_BI[tipo] || 'bi-diagram-3'} me-2"></i>
                    ${tipoInfo.label}
                </h4>
                <div class="row g-3">
        `;
        
        plantillasTipo.forEach(plantilla => {
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 plantilla-card" onclick="mostrarVistaPreviaPlantilla('${tipo}', '${plantilla.id}')">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi ${TIPO_ICONS_BI[tipo] || 'bi-diagram-3'} me-2 text-primary"></i>
                                <h6 class="card-title mb-0">${plantilla.titulo}</h6>
                            </div>
                            <p class="card-text text-muted small">${plantilla.descripcion}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${plantilla.nodes.length} elementos</small>
                                <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); usarPlantilla('${tipo}', '${plantilla.id}')">
                                    <i class="bi bi-pencil-square me-1"></i>Usar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Función para mostrar vista previa de plantilla
function mostrarVistaPreviaPlantilla(tipo, plantillaId) {
    const plantilla = PLANTILLAS[tipo].find(p => p.id === plantillaId);
    if (!plantilla) return;
    
    // Crear modal de vista previa
    const modalHtml = `
        <div class="modal fade" id="modalVistaPrevia" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi ${TIPO_ICONS_BI[tipo] || 'bi-diagram-3'} me-2"></i>
                            ${plantilla.titulo}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; background: #f8f9fa; min-height: 300px;">
                                    <div id="previewCanvas" style="width: 100%; height: 280px; background: white; border-radius: 4px;">
                                        <!-- Vista previa simplificada -->
                                        <div class="text-center text-muted mt-5">
                                            <i class="bi ${TIPO_ICONS_BI[tipo] || 'bi-diagram-3'} fs-1 mb-3"></i>
                                            <p>Vista previa del diagrama</p>
                                            <small>${plantilla.nodes.length} elementos, ${plantilla.connections.length} conexiones</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Descripción</h6>
                                <p class="text-muted small mb-3">${plantilla.descripcion}</p>
                                
                                <h6>Contenido</h6>
                                <ul class="list-unstyled small">
                                    <li><i class="bi bi-circle-fill text-primary me-2"></i>${plantilla.nodes.length} elementos</li>
                                    <li><i class="bi bi-arrow-right text-primary me-2"></i>${plantilla.connections.length} conexiones</li>
                                </ul>
                                
                                <div class="mt-3">
                                    <button class="btn btn-primary w-100" onclick="usarPlantilla('${tipo}', '${plantillaId}'); bootstrap.Modal.getInstance(document.getElementById('modalVistaPrevia')).hide();">
                                        <i class="bi bi-pencil-square me-2"></i>Usar esta plantilla
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const existingModal = document.getElementById('modalVistaPrevia');
    if (existingModal) existingModal.remove();
    
    // Agregar nuevo modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
    modal.show();
}

// Función para usar una plantilla
function usarPlantilla(tipo, plantillaId) {
    const plantilla = PLANTILLAS[tipo].find(p => p.id === plantillaId);
    if (!plantilla) {
        toast('Plantilla no encontrada', 'error');
        return;
    }
    
    // Crear el diagrama con la plantilla
    const diagramaData = {
        titulo: plantilla.titulo + ' (plantilla)',
        tipo: tipo,
        descripcion: plantilla.descripcion,
        etiquetas: 'plantilla'
    };
    
    // Guardar datos de la plantilla en sessionStorage
    sessionStorage.setItem('plantillaData', JSON.stringify({
        nodes: plantilla.nodes,
        connections: plantilla.connections,
        diagramType: tipo
    }));
    
    // Abrir modal de nuevo diagrama con datos prellenados
    document.getElementById('fTitulo').value = diagramaData.titulo;
    document.getElementById('fTipo').value = tipo;
    document.getElementById('fDescripcion').value = diagramaData.descripcion;
    document.getElementById('fEtiquetas').value = diagramaData.etiquetas;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalDiagrama'));
    modal.show();
    
    toast('Plantilla seleccionada. Completa los detalles y ve al editor.', 'info');
}

function toggleThemeDrawer() {
    const drawer  = document.getElementById('themeDrawer');
    const overlay = document.getElementById('themeOverlay');
    const isOpen  = drawer.style.right === '0px';
    drawer.style.right   = isOpen ? '-340px' : '0px';
    overlay.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) renderThemePanel('dashThemeContainer', 'light');
}
</script>
<script>window._DIAG_BASE_URL = '<?= BASE_URL ?>';</script>
<!-- V45: componentes reutilizables compartidos -->
<script src="<?= BASE_URL ?>/public/js/diagram-components.js"></script>
<script src="<?= BASE_URL ?>/public/js/diagram-mini-preview.js"></script>
</body>
</html>