/**
 * user-theme.js — Tema y colores por usuario (v2)
 * Cargado en admin, maestro, dashboard y editor.
 *
 * Funciona con CSS custom properties:
 *   --primary:     color primario
 *   --primary2:    color secundario (degradado)
 *   --primary-rgb: componentes R,G,B del primario (para rgba())
 *
 * Guardado: /api/user-config (BD) + localStorage (fallback offline)
 */

const PALETTES = [
    { label:'Violeta',    p1:'#667eea', p2:'#764ba2' },
    { label:'Océano',     p1:'#0ea5e9', p2:'#2563eb' },
    { label:'Esmeralda',  p1:'#10b981', p2:'#059669' },
    { label:'Naranja',    p1:'#f59e0b', p2:'#d97706' },
    { label:'Rosa',       p1:'#ec4899', p2:'#db2777' },
    { label:'Rojo',       p1:'#ef4444', p2:'#b91c1c' },
    { label:'Cian',       p1:'#06b6d4', p2:'#0284c7' },
    { label:'Lima',       p1:'#84cc16', p2:'#4d7c0f' },
];

const SIDEBAR_PALETTES = [
    { label:'Oscuro Pro',      color:'#1a1a2e' },
    { label:'Azul Profundo',   color:'#0f3a7d' },
    { label:'Púrpura',         color:'#2d1b4e' },
    { label:'Verde Oscuro',    color:'#1a3a2a' },
    { label:'Gris Elegante',   color:'#2a2a3e' },
    { label:'Carbón',          color:'#1e1e2e' },
    { label:'Tinta Azul',      color:'#1a2d4d' },
    { label:'Marrón Oscuro',   color:'#2d1f1a' },
    { label:'Negro Puro',      color:'#0a0a0f' },
    { label:'Azul Corporativo',color:'#1b3a5c' },
];

// Config global (accesible desde cualquier panel)
window._themeConfig = { 
    theme:'light', 
    primary_color:'#667eea', 
    primary2_color:'#764ba2',
    sidebar_color: null  // null = usar color predeterminado, o hexadecimal para color personalizado
};

// ── Aplicar al documento ───────────────────────────────────────────
function _hexToRgb(hex) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return `${r},${g},${b}`;
}

function hexToLighterShade(hex, factor = 0.9) {
    const [r, g, b] = _hexToRgb(hex).split(',').map(Number);
    const lighter = [
        Math.min(255, Math.round(r + (255 - r) * (1 - factor))),
        Math.min(255, Math.round(g + (255 - g) * (1 - factor))),
        Math.min(255, Math.round(b + (255 - b) * (1 - factor)))
    ];
    return `rgb(${lighter.join(',')})`;
}

function hexToDarkerShade(hex, factor = 1.2) {
    const [r, g, b] = _hexToRgb(hex).split(',').map(Number);
    const darker = [
        Math.max(0, Math.round(r / factor)),
        Math.max(0, Math.round(g / factor)),
        Math.max(0, Math.round(b / factor))
    ];
    return `rgb(${darker.join(',')})`;
}

function applyThemeConfig(cfg) {
    if (!cfg) return;
    const root = document.documentElement;
    const isLightTheme = cfg.theme === 'light';
    
    if (cfg.primary_color) {
        root.style.setProperty('--primary',     cfg.primary_color);
        root.style.setProperty('--primary-rgb', _hexToRgb(cfg.primary_color));

        const [r, g, b] = _hexToRgb(cfg.primary_color).split(',').map(Number);

        if (isLightTheme) {
            // Light theme colors
            root.style.setProperty('--bg-deep',  '#f0f2f8');
            root.style.setProperty('--bg-card',  '#fff');
            root.style.setProperty('--bg-hover', '#f8f9ff');
            root.style.setProperty('--bd-color', '#e8eaf0');
            root.style.setProperty('--txt-main', '#1a1a2e');
            root.style.setProperty('--txt-muted','#666');
        } else {
            // Dark theme colors derived from primary color
            root.style.setProperty('--bg-deep',  `rgb(${Math.round(r*.04)},${Math.round(g*.04)},${Math.round(b*.08)})`);
            root.style.setProperty('--bg-panel', `rgb(${Math.round(r*.08)},${Math.round(g*.08)},${Math.round(b*.14)})`);
            root.style.setProperty('--bg-card',  `rgb(${Math.round(r*.12)},${Math.round(g*.12)},${Math.round(b*.20)})`);
            root.style.setProperty('--bg-hover', `rgb(${Math.round(r*.10)},${Math.round(g*.10)},${Math.round(b*.17)})`);
            root.style.setProperty('--bd-color', `rgba(${r},${g},${b},.18)`);
            root.style.setProperty('--txt-main', '#e8eaff');
            root.style.setProperty('--txt-muted','#8888aa');
        }
        root.style.setProperty('--bg-hover-light', `rgba(${r},${g},${b},.04)`);
    }
    if (cfg.primary2_color) {
        root.style.setProperty('--primary2', cfg.primary2_color);
    }
    
    // Aplicar color personalizado del sidebar si existe
    if (cfg.sidebar_color) {
        const sidebarColor = cfg.sidebar_color;
        
        const style = document.getElementById('_sidebarCustomStyle');
        if (style) style.remove();
        
        // Auto-detect if the sidebar color is dark or light for text contrast
        const [_sr,_sg,_sb] = _hexToRgb(sidebarColor).split(',').map(Number);
        const _luminance = (0.299*_sr + 0.587*_sg + 0.114*_sb);
        const _textColor = _luminance < 140 ? '#ffffff' : '#1a1a2e';
        const _textMuted = _luminance < 140 ? 'rgba(255,255,255,0.65)' : 'rgba(26,26,46,0.65)';
        const newStyle = document.createElement('style');
        newStyle.id = '_sidebarCustomStyle';
        newStyle.textContent = `
            .sidebar { background: ${sidebarColor} !important; color: ${_textColor} !important; }
            .sidebar .nav-btn { color: ${_textMuted} !important; }
            .sidebar .nav-btn:hover,
            .sidebar .nav-btn.active { color: ${_textColor} !important; background: rgba(${_luminance < 140 ? '255,255,255' : '0,0,0'},.12) !important; }
            .sidebar .sidebar-brand h4,
            .sidebar .sidebar-brand p,
            .sidebar .sidebar-user h6,
            .sidebar .sidebar-user small,
            .sidebar .nav-section,
            .sidebar .sidebar-footer .nav-btn { color: ${_textColor} !important; opacity: 0.9; }
            .sidebar .sidebar-footer .nav-btn { color: ${_textMuted} !important; }
        `;
        document.head.appendChild(newStyle);
    } else {
        // Remover estilos personalizados si existen
        const style = document.getElementById('_sidebarCustomStyle');
        if (style) style.remove();
    }
    
    // Tema oscuro / claro
    document.body.classList.toggle('light-theme', isLightTheme);
    // Al volver a oscuro, eliminar las correcciones de inline styles
    if (!isLightTheme) {
        document.getElementById('_lightInlineFix')?.remove();
    }
    window._themeConfig = { ...window._themeConfig, ...cfg };
    if (isLightTheme) {
        setTimeout(fixLightThemeColors, 50);
    }
}

// ── Cargar desde servidor ──────────────────────────────────────────
async function loadUserTheme() {
    const base = window.BASE_URL || '';
    // If returning from editor, apply session theme immediately to avoid flash
    try {
        const sess = JSON.parse(sessionStorage.getItem('_uth_session') || 'null');
        if (sess?.primary_color) applyThemeConfig(sess);
    } catch(_) {}
    try {
        const r   = await fetch(base + '/api/user-config');
        const d   = await r.json();
        if (d.success && d.config) {
            applyThemeConfig(d.config);
            localStorage.setItem('_uth', JSON.stringify(d.config));
            sessionStorage.setItem('_uth_session', JSON.stringify(d.config));
            return d.config;
        }
    } catch(_) {}
    // Fallback localStorage
    try {
        const local = JSON.parse(localStorage.getItem('_uth') || 'null');
        if (local?.primary_color) { applyThemeConfig(local); return local; }
    } catch(_) {}
    return window._themeConfig;
}

// ── Guardar ────────────────────────────────────────────────────────
async function saveUserTheme(cfg) {
    applyThemeConfig(cfg);
    localStorage.setItem('_uth', JSON.stringify(window._themeConfig));
    // Also keep a sessionStorage copy so returning from editor restores it
    sessionStorage.setItem('_uth_session', JSON.stringify(window._themeConfig));
    const base = window.BASE_URL || '';
    try {
        await fetch(base + '/api/user-config', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify(window._themeConfig)
        });
    } catch(_) {}
}

// ── Renderizar panel de tema ───────────────────────────────────────
// panelTheme: 'dark' (para admin/maestro) o 'light' (para dashboard)
function renderThemePanel(containerId, panelTheme) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const cfg = window._themeConfig;
    const isDarkPanel = panelTheme !== 'light';

    // Colores base según el panel donde se renderiza (no el tema del usuario)
    const bg0   = isDarkPanel ? 'rgba(255,255,255,.04)' : '#fff';
    const bg1   = isDarkPanel ? '#0d0d1a'               : '#f8f9ff';
    const bord  = isDarkPanel ? 'rgba(255,255,255,.1)'  : '#e8eaf0';
    const txt   = isDarkPanel ? '#ccc'                  : '#555';
    const txt2  = isDarkPanel ? '#888'                  : '#999';
    const inputBg = isDarkPanel ? '#1a1a2e'             : '#fff';
    const inputClr= isDarkPanel ? '#fff'                : '#333';

    el.innerHTML = `
    <div style="background:${bg0};border:1px solid ${bord};border-radius:12px;padding:16px">

        <div style="font-size:.7rem;color:${txt2};margin-bottom:8px;text-transform:uppercase;letter-spacing:.07em;font-weight:600">Paletas rápidas</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px">
            ${PALETTES.map((p,i) => `
            <button onclick="applyPalette(${i},'${containerId}','${panelTheme}')" title="${p.label}"
                style="width:32px;height:32px;border-radius:50%;cursor:pointer;flex-shrink:0;
                       background:linear-gradient(135deg,${p.p1},${p.p2});
                       border:2px solid ${cfg.primary_color===p.p1?'#fff':bord};
                       box-shadow:${cfg.primary_color===p.p1?'0 0 0 3px '+p.p1+'66':'none'};
                       transition:transform .15s,box-shadow .15s"
                onmouseover="this.style.transform='scale(1.18)'"
                onmouseout="this.style.transform='scale(1)'">
            </button>`).join('')}
        </div>

        <div style="font-size:.7rem;color:${txt2};margin-bottom:8px;text-transform:uppercase;letter-spacing:.07em;font-weight:600">Color personalizado</div>
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.78rem;color:${txt}">
                <input type="color" id="_tc1_${containerId}" value="${cfg.primary_color}"
                    style="width:32px;height:32px;border:none;border-radius:6px;cursor:pointer;padding:2px;background:${inputBg}"
                    oninput="previewColor('${containerId}')">
                Color 1
            </label>
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.78rem;color:${txt}">
                <input type="color" id="_tc2_${containerId}" value="${cfg.primary2_color}"
                    style="width:32px;height:32px;border:none;border-radius:6px;cursor:pointer;padding:2px;background:${inputBg}"
                    oninput="previewColor('${containerId}')">
                Color 2
            </label>
            <button onclick="applyCustomColor('${containerId}','${panelTheme}')"
                style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;
                       border-radius:8px;padding:7px 14px;font-size:.78rem;cursor:pointer">
                <i class="bi bi-check2 me-1"></i>Aplicar
            </button>
        </div>

        <div style="font-size:.7rem;color:${txt2};margin-bottom:8px;text-transform:uppercase;letter-spacing:.07em;font-weight:600">Modo</div>
        <div style="display:flex;gap:8px;margin-bottom:20px">
            <button id="_btnD_${containerId}" onclick="setThemeMode('dark','${containerId}','${panelTheme}')"
                style="flex:1;padding:9px;border-radius:8px;font-size:.82rem;cursor:pointer;border:none;transition:all .2s;
                       ${cfg.theme!=='light'
                            ?'background:linear-gradient(135deg,var(--primary),var(--primary2));color:#fff;'
                            :`background:${bg1};border:1px solid ${bord};color:${txt2};`}">
                <i class="bi bi-moon-fill me-1"></i>Oscuro
            </button>
            <button id="_btnL_${containerId}" onclick="setThemeMode('light','${containerId}','${panelTheme}')"
                style="flex:1;padding:9px;border-radius:8px;font-size:.82rem;cursor:pointer;border:none;transition:all .2s;
                       ${cfg.theme==='light'
                            ?'background:linear-gradient(135deg,var(--primary),var(--primary2));color:#fff;'
                            :`background:${bg1};border:1px solid ${bord};color:${txt2};`}">
                <i class="bi bi-sun-fill me-1"></i>Claro
            </button>
        </div>

        <div style="font-size:.7rem;color:${txt2};margin-bottom:8px;text-transform:uppercase;letter-spacing:.07em;font-weight:600">Vista previa</div>
        <div style="border-radius:10px;overflow:hidden;border:1px solid ${bord}">
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:10px 14px;font-size:.82rem;color:#fff;font-weight:600">
                <i class="bi bi-palette me-1"></i>Tus colores activos
            </div>
            <div style="background:${bg1};padding:12px 14px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <button style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;padding:6px 14px;border-radius:6px;font-size:.78rem;cursor:default">Botón</button>
                <span style="background:rgba(var(--primary-rgb),.15);color:var(--primary);border:1px solid var(--primary);border-radius:12px;padding:3px 10px;font-size:.73rem">Badge</span>
                <input style="background:${inputBg};border:1.5px solid var(--primary);border-radius:6px;color:${inputClr};padding:5px 10px;font-size:.78rem;width:90px;outline:none" placeholder="Input…" readonly>
            </div>
        </div>

        <div style="font-size:.7rem;color:${txt2};margin-bottom:8px;margin-top:14px;text-transform:uppercase;letter-spacing:.07em;font-weight:600">Color del panel lateral</div>
        
        <div style="font-size:.7rem;color:${txt2};margin-bottom:8px;text-transform:uppercase;letter-spacing:.07em;font-weight:600">Paletas rápidas del sidebar</div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-bottom:16px">
            ${SIDEBAR_PALETTES.map((sp,i) => `
            <button onclick="applyQuickSidebarColor('${sp.color}','${containerId}','${panelTheme}')" title="${sp.label}"
                style="width:100%;aspect-ratio:1;border-radius:8px;cursor:pointer;flex-shrink:0;
                       background:${sp.color};
                       border:2px solid ${cfg.sidebar_color===sp.color?'#fff':'rgba(255,255,255,.2)'};
                       box-shadow:${cfg.sidebar_color===sp.color?'0 0 0 2px rgba(255,255,255,.4)':'none'};
                       transition:transform .15s,box-shadow .15s;
                       position:relative"
                onmouseover="this.style.transform='scale(1.1)';this.title='${sp.label}'"
                onmouseout="this.style.transform='scale(1)'">
                ${cfg.sidebar_color===sp.color?'<i class="bi bi-check" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:.8rem;font-weight:bold"></i>':''}
            </button>`).join('')}
        </div>

        <div style="display:flex;gap:10px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.78rem;color:${txt}">
                <input type="color" id="_sidebarColor_${containerId}" value="${cfg.sidebar_color || '#1a1a2e'}"
                    style="width:32px;height:32px;border:none;border-radius:6px;cursor:pointer;padding:2px;background:${inputBg}"
                    oninput="previewSidebarColor('${containerId}')">
                Personalizado
            </label>
            <button onclick="changeSidebarColor(document.getElementById('_sidebarColor_${containerId}').value, '${containerId}','${panelTheme}')"
                style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;
                       border-radius:8px;padding:7px 14px;font-size:.78rem;cursor:pointer">
                <i class="bi bi-check2 me-1"></i>Aplicar
            </button>
            <button onclick="changeSidebarColor(null, '${containerId}','${panelTheme}')"
                style="background:none;border:1px solid ${bord};color:${txt2};
                       border-radius:8px;padding:7px 14px;font-size:.78rem;cursor:pointer">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar
            </button>
        </div>

        <div style="margin-top:14px;display:flex;gap:10px;text-align:center">
            <button onclick="resetTheme('${containerId}','${panelTheme}')"
                style="flex:1;background:none;border:1px solid ${bord};color:${txt2};border-radius:8px;
                       padding:6px 12px;font-size:.75rem;cursor:pointer;transition:all .2s"
                onmouseover="this.style.color='${txt}';this.style.borderColor='${txt}'"
                onmouseout="this.style.color='${txt2}';this.style.borderColor='${bord}'">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar colores
            </button>
            <button onclick="resetAllConfig('${containerId}','${panelTheme}')"
                style="flex:1;background:none;border:1.5px solid var(--c-warning);color:var(--c-warning);border-radius:8px;
                       padding:6px 12px;font-size:.75rem;cursor:pointer;transition:all .2s"
                onmouseover="this.style.background='rgba(245,158,11,.1)'"
                onmouseout="this.style.background='none'">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar todo
            </button>
        </div>
    </div>`;
}

// ── Acciones del panel ─────────────────────────────────────────────
function applyPalette(idx, containerId, panelTheme) {
    const p = PALETTES[idx];
    saveUserTheme({ ...window._themeConfig, primary_color:p.p1, primary2_color:p.p2 });
    renderThemePanel(containerId, panelTheme);
}

function previewColor(containerId) {
    const p1 = document.getElementById('_tc1_'+containerId)?.value;
    const p2 = document.getElementById('_tc2_'+containerId)?.value;
    if (p1) document.documentElement.style.setProperty('--primary',  p1);
    if (p2) document.documentElement.style.setProperty('--primary2', p2);
    if (p1) document.documentElement.style.setProperty('--primary-rgb', _hexToRgb(p1));
}

function previewSidebarColor(containerId) {
    const color = document.getElementById('_sidebarColor_'+containerId)?.value;
    if (color) {
        const style = document.getElementById('_sidebarPreviewStyle');
        if (style) style.remove();
        
        const newStyle = document.createElement('style');
        newStyle.id = '_sidebarPreviewStyle';
        newStyle.textContent = `
            .sidebar { background: ${color} !important; }
        `;
        document.head.appendChild(newStyle);
    }
}

function applyCustomColor(containerId, panelTheme) {
    const p1 = document.getElementById('_tc1_'+containerId)?.value || window._themeConfig.primary_color;
    const p2 = document.getElementById('_tc2_'+containerId)?.value || window._themeConfig.primary2_color;
    saveUserTheme({ ...window._themeConfig, primary_color:p1, primary2_color:p2 });
    renderThemePanel(containerId, panelTheme);
}

function setThemeMode(mode, containerId, panelTheme) {
    saveUserTheme({ ...window._themeConfig, theme:mode });
    // Sync editor toggle if present
    const edIcon = document.getElementById('themeToggle')?.querySelector('i');
    if (edIcon) edIcon.className = mode==='light' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    renderThemePanel(containerId, mode==='light' ? 'light' : 'dark');
    if (mode === 'light') {
        setTimeout(fixLightThemeColors, 50);
    } else {
        document.getElementById('_lightInlineFix')?.remove();
    }
}

function fixLightThemeColors() {
    // Eliminar hoja de corrección previa (si existe)
    document.getElementById('_lightInlineFix')?.remove();
    if (!document.body.classList.contains('light-theme')) return;

    // Inyectamos una <style> con selectores de atributo en lugar de mutar
    // inline styles directamente — así es completamente reversible al cambiar
    // de vuelta a tema oscuro (basta con eliminar esta hoja).
    const s = document.createElement('style');
    s.id = '_lightInlineFix';
    s.textContent = `
/* ── Fondos oscuros → blancos/claros ──────────────────────── */
body.light-theme [style*="background:#0d0d1a"],
body.light-theme [style*="background:#0a0a12"],
body.light-theme [style*="background:#080812"],
body.light-theme [style*="background:#080810"],
body.light-theme [style*="background:#0a0a10"],
body.light-theme [style*="background:#0a0a14"],
body.light-theme [style*="background:#080808"]
{ background:#f8f9ff !important; }

body.light-theme [style*="background:#1a1a2e"]
{ background:#fff !important; }

body.light-theme [style*="background:#13132a"],
body.light-theme [style*="background:#1e1e3a"],
body.light-theme [style*="background:#16213e"]
{ background:#f0f2ff !important; }

body.light-theme [style*="background:#2a2a4a"]:not([style*="rgba"])
{ background:#e8eaf0 !important; }

/* ── Bordes oscuros → claros ───────────────────────────────── */
body.light-theme [style*="border:1px solid #2a2a4a"]
{ border-color:#dde0f0 !important; }
body.light-theme [style*="border-bottom:1px solid #2a2a4a"]
{ border-bottom-color:#dde0f0 !important; }
body.light-theme [style*="border-top:1px solid #2a2a4a"]
{ border-top-color:#dde0f0 !important; }
body.light-theme [style*="border:1px solid #1e1e3a"]
{ border-color:#dde0f0 !important; }
body.light-theme [style*="border-top:1px solid #1e1e3a"]
{ border-top-color:#dde0f0 !important; }

/* ── Texto blanco sobre fondo claro → oscuro ───────────────── */
/* Excluimos elementos con gradiente (botones, headers) donde #fff es correcto */
body.light-theme [style*="color:#e0e0e0"]
{ color:#1e1e2e !important; }
body.light-theme [style*="color:#aab8ff"]
{ color:var(--primary) !important; }
body.light-theme div[style*="color:#fff;font-weight"],
body.light-theme div[style*="font-weight:600;color:#fff"],
body.light-theme div[style*="font-weight:700;color:#fff"]
{ color:#1e1e2e !important; }
body.light-theme [style*="color:#fff;font-size"]
{ color:#1e1e2e !important; }
body.light-theme span[style*="color:#888"]
{ color:#666 !important; }

/* ── Gradientes y botones con color primario mantienen #fff ── */
body.light-theme [style*="background:linear-gradient"] *,
body.light-theme [style*="background:linear-gradient"]
{ color:#fff; }
`;
    document.head.appendChild(s);
}

function changeSidebarColor(color, containerId, panelTheme) {
    const cfg = { ...window._themeConfig, sidebar_color: color || null };
    saveUserTheme(cfg);
    renderThemePanel(containerId, panelTheme);
}

function applyQuickSidebarColor(color, containerId, panelTheme) {
    // Aplicar el color de la paleta rápida al sidebar
    const cfg = { ...window._themeConfig, sidebar_color: color };
    saveUserTheme(cfg);
    
    // Actualizar el input de color con el valor de la paleta
    const colorInput = document.getElementById('_sidebarColor_'+containerId);
    if (colorInput) colorInput.value = color;
    
    // Re-renderizar el panel para mostrar que la paleta está seleccionada
    renderThemePanel(containerId, panelTheme);
}

function resetTheme(containerId, panelTheme) {
    saveUserTheme({ theme:'light', primary_color:'#667eea', primary2_color:'#764ba2' });
    renderThemePanel(containerId, 'light');
}

function resetAllConfig(containerId, panelTheme) {
    saveUserTheme({ 
        theme:'light', 
        primary_color:'#667eea', 
        primary2_color:'#764ba2',
        sidebar_color: null
    });
    renderThemePanel(containerId, panelTheme);
}

// ── Auto-cargar al iniciar ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadUserTheme);
