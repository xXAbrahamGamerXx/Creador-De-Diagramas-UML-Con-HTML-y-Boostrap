/**
 * diagram-components.js — V45
 * Componentes reutilizables para diagramas UML.
 * Usado por: dashboard (alumno), maestro, proyectos, mis diagramas.
 *
 * Expone:
 *   DiagramComponents.TIPOS          → mapa tipo→{label, icon()}
 *   DiagramComponents.getTipoIcono() → HTML del icono SVG
 *   DiagramComponents.renderCard()   → HTML de una card Lucidchart
 *   DiagramComponents.renderTipoPicker() → HTML del selector visual
 *   DiagramComponents.escHtml()      → escape HTML
 *   DiagramComponents.formatBytes()  → formato bytes
 */
(function (global) {
    'use strict';

    const ICON_BASE = (global.BASE_URL || global._DIAG_BASE_URL || '').replace(/\/$/, '')
        + '/public/assets/img/iconos-uml/';

    const TIPOS_SVG = {
        usecase:       'usecase.svg',
        class:         'class.svg',
        sequence:      'sequence.svg',
        activity:      'activity.svg',
        state:         'state.svg',
        component:     'component.svg',
        deployment:    'deployment.svg',
        object:        'object.svg',
        package:       'package.svg',
        composite:     'composite.svg',
        profile:       'profile.svg',
        communication: 'communication.svg',
        timing:        'timing.svg',
        overview:      'overview.svg',
    };

    const TIPOS = {
        // ── Estructurales ──────────────────────────────────
        class:         { label: 'Clases',                categoria: 'Estructurales' },
        object:        { label: 'Objetos',               categoria: 'Estructurales' },
        package:       { label: 'Paquetes',              categoria: 'Estructurales' },
        composite:     { label: 'Estructura Compuesta',  categoria: 'Estructurales' },
        component:     { label: 'Componentes',           categoria: 'Estructurales' },
        deployment:    { label: 'Despliegue',            categoria: 'Estructurales' },
        profile:       { label: 'Perfiles',              categoria: 'Estructurales' },
        // ── Comportamiento ──────────────────────────────────
        usecase:       { label: 'Casos de Uso',          categoria: 'Comportamiento' },
        activity:      { label: 'Actividades',           categoria: 'Comportamiento' },
        state:         { label: 'Máquina de Estado',     categoria: 'Comportamiento' },
        // ── Interacción ──────────────────────────────────────
        sequence:      { label: 'Secuencia',             categoria: 'Interacción' },
        communication: { label: 'Comunicación',          categoria: 'Interacción' },
        timing:        { label: 'Tiempos',               categoria: 'Interacción' },
        overview:      { label: 'Descripción General',   categoria: 'Interacción' },
    };

    /**
     * Devuelve <img> del icono SVG del tipo de diagrama.
     * @param {string} tipo  - clave de TIPOS
     * @param {number} size  - tamaño px
     */
    function getTipoIcono(tipo, size) {
        size = size || 40;
        const file = TIPOS_SVG[tipo];
        if (!file) {
            return '<span style="font-size:' + (size * 0.06) + 'rem;opacity:.4">📄</span>';
        }
        return '<img src="' + ICON_BASE + file + '" width="' + size + '" height="' + size
            + '" style="object-fit:contain;display:block" alt="' + tipo + '" loading="lazy">';
    }

    /**
     * Escapa HTML para evitar XSS.
     */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /**
     * Formatea bytes a unidad legible.
     */
    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    /**
     * Renderiza una card estilo Lucidchart para un diagrama.
     * @param {Object} d        - objeto diagrama {id, titulo, tipo_diagrama, fecha_modificacion, version}
     * @param {Object} opts     - opciones {onOpen, onOptions, showOwner}
     */
    function renderCard(d, opts) {
        opts = opts || {};
        const id       = d.id;
        const titulo   = escHtml(d.titulo || 'Sin título');
        const fecha    = new Date(d.fecha_modificacion || d.fecha_creacion).toLocaleDateString('es-MX');
        const tipoInfo = TIPOS[d.tipo_diagrama] || { label: d.tipo_diagrama };
        const onOpen   = opts.onOpen   || ('abrirDiagrama(' + id + ')');
        const onOpts   = opts.onOptions || ('toggleDDiagrama(event,' + id + ',"' + titulo.replace(/"/g, '\\"') + '")');
        const ownerBadge = opts.autor
            ? '<span style="font-size:.62rem;color:var(--txt-muted);margin-left:4px">@' + escHtml(opts.autor) + '</span>'
            : '';

        return '<div class="col-sm-6 col-lg-4 col-xl-3">'
            + '<div class="diagram-card">'
            + '<div class="lc-preview" data-preview-id="' + id + '" onclick="' + onOpen + '" title="Abrir en editor">'
            + '<div style="display:flex;align-items:center;justify-content:center;height:100%;opacity:0.3">'
            + getTipoIcono(d.tipo_diagrama, 44)
            + '</div>'
            + '</div>'
            + '<div class="lc-body">'
            + '<div class="lc-title" title="' + titulo + '">' + titulo + ownerBadge + '</div>'
            + '<div class="lc-meta">'
            + '<span style="display:inline-flex;align-items:center;gap:3px">'
            + getTipoIcono(d.tipo_diagrama, 11) + '&nbsp;' + escHtml(tipoInfo.label)
            + '</span>'
            + '&nbsp;&middot;&nbsp;' + fecha
            + '</div>'
            + '</div>'
            + '<div class="lc-footer">'
            + '<button class="lc-btn-open" onclick="' + onOpen + '">Abrir</button>'
            + '<div class="lc-dots-wrap" onclick="event.stopPropagation()">'
            + '<button class="lc-icon-btn" title="Más opciones" onclick="' + onOpts + '">'
            + '<i class="bi bi-three-dots"></i>'
            + '</button>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>';
    }

    /**
     * Renderiza el picker visual de tipos de diagrama.
     * @param {string} selectedTipo   - tipo activo al inicio
     * @param {string} onSelectFn     - nombre de función JS a llamar con (tipo) al seleccionar
     * @param {string} hiddenInputId  - id del <input type="hidden"> a actualizar
     */
    function renderTipoPicker(selectedTipo, onSelectFn, hiddenInputId) {
        selectedTipo = selectedTipo || 'usecase';
        onSelectFn   = onSelectFn   || '_dcSelectTipo';
        hiddenInputId = hiddenInputId || 'fTipo';

        // Registrar función global de selección si no existe
        if (!global[onSelectFn]) {
            global[onSelectFn] = function (val) {
                const el = document.getElementById(hiddenInputId);
                if (el) el.value = val;
                document.querySelectorAll('._dcTipoOpt').forEach(function (e) {
                    const sel = e.dataset.tipo === val;
                    e.style.borderColor = sel ? 'var(--primary)' : 'var(--bd-color)';
                    e.style.background  = sel ? 'rgba(var(--primary-rgb),.1)' : 'var(--bg-card)';
                });
            };
        }

        const grupos = [
            { cat: 'Estructurales',  tipos: ['class','object','package','composite','component','deployment','profile'] },
            { cat: 'Comportamiento', tipos: ['usecase','activity','state'] },
            { cat: 'Interacción',    tipos: ['sequence','communication','timing','overview'] },
        ];

        let html = '';
        grupos.forEach(function (g) {
            html += '<div style="font-size:.67rem;font-weight:700;color:var(--txt-muted);text-transform:uppercase;'
                + 'letter-spacing:.07em;padding:7px 0 3px">' + g.cat + '</div>';
            g.tipos.forEach(function (tipo) {
                const info   = TIPOS[tipo] || { label: tipo };
                const active = tipo === selectedTipo;
                html += '<div class="_dcTipoOpt" data-tipo="' + tipo + '" onclick="' + onSelectFn + '(\'' + tipo + '\')"'
                    + ' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;'
                    + 'border:1.5px solid ' + (active ? 'var(--primary)' : 'var(--bd-color)') + ';'
                    + 'background:' + (active ? 'rgba(var(--primary-rgb),.1)' : 'var(--bg-card)') + ';'
                    + 'margin-bottom:5px;transition:all .15s">'
                    + '<div style="width:32px;height:32px;flex-shrink:0">' + getTipoIcono(tipo, 32) + '</div>'
                    + '<span style="font-size:.81rem;font-weight:600;color:var(--txt-main)">' + escHtml(info.label) + '</span>'
                    + '</div>';
            });
        });
        return html;
    }

    // ── Exponer API pública ──────────────────────────────────────────────
    global.DiagramComponents = {
        TIPOS:             TIPOS,
        TIPOS_SVG:         TIPOS_SVG,
        getTipoIcono:      getTipoIcono,
        renderCard:        renderCard,
        renderTipoPicker:  renderTipoPicker,
        escHtml:           escHtml,
        formatBytes:       formatBytes,
    };

    // Compatibilidad: alias globales que ya usa el código existente
    global.getTipoIconoSVG = getTipoIcono;
    global.TIPOS           = TIPOS;

})(window);
