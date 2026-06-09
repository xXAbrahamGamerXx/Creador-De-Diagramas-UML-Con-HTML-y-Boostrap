/**
 * DiagramMiniRenderer — Renderizador de previsualización de diagramas.
 * Usa la misma lógica visual del editor pero solo-lectura, lazy-loaded.
 * Se configura con: window._DIAG_BASE_URL antes de cargar este script.
 */
(function() {
'use strict';

const BASE_URL = (window._DIAG_BASE_URL || '').replace(/\/$/, '');

const TYPE_COL = {
    usecase:'#6366f1', class:'#0891b2', sequence:'#8b5cf6', activity:'#059669',
    state:'#d97706',   component:'#db2777', deployment:'#7c3aed', object:'#0284c7',
    communication:'#16a34a', timing:'#ca8a04', package:'#9333ea',
    composite:'#0f766e', profile:'#b45309', overview:'#1d4ed8'
};

function col(tipo) { return TYPE_COL[tipo] || '#6366f1'; }

/* ── Node HTML (same shapes as editor, inline styles) ───────────────── */
function nodeHTML(node, diagTipo) {
    const c   = col(diagTipo);
    const w   = +(node.width)  || 120;
    const h   = +(node.height) || 60;
    const fs  = Math.max(7, Math.min(13, h * 0.28));
    const bg  = c + '18';
    const bd  = `2px solid ${c}`;
    const txt = ((node.text || node.label || '').substring(0, 35))
                    .replace(/</g,'&lt;').replace(/>/g,'&gt;');

    switch (node.type) {
        case 'actor': {
            const sz = Math.min(Math.min(w, h) * 0.55, 36);
            return `<div style="display:flex;flex-direction:column;align-items:center;gap:2px;color:${c}">
                <svg width="${sz}" height="${sz*1.4}" viewBox="0 0 60 80" style="display:block;overflow:visible">
                    <circle cx="30" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="30" y1="22" x2="30" y2="50" stroke="currentColor" stroke-width="2"/>
                    <line x1="10" y1="35" x2="50" y2="35" stroke="currentColor" stroke-width="2"/>
                    <line x1="30" y1="50" x2="14" y2="74" stroke="currentColor" stroke-width="2"/>
                    <line x1="30" y1="50" x2="46" y2="74" stroke="currentColor" stroke-width="2"/>
                </svg>
                <div style="font-size:${fs}px;color:${c};text-align:center;max-width:${w}px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${txt}</div>
            </div>`;
        }

        case 'usecase':
            return `<div style="border:${bd};border-radius:50px;padding:3px 8px;text-align:center;background:${bg};width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:${fs}px;color:#fff;box-sizing:border-box;overflow:hidden">${txt}</div>`;

        case 'system':
            return `<div style="border:${bd};border-radius:8px;width:100%;height:100%;background:rgba(13,110,253,0.05);position:relative;box-sizing:border-box">
                <div style="position:absolute;top:-8px;left:8px;padding:0 4px;font-size:${Math.max(6,fs-3)}px;font-weight:700;color:${c};background:var(--bg-card,#1e1e2e);white-space:nowrap;overflow:hidden;max-width:80%">${txt}</div>
            </div>`;

        case 'class': case 'abstract': case 'interface': case 'object': {
            const isAbs  = node.type==='abstract', isIface = node.type==='interface', isObj = node.type==='object';
            const ster   = isIface ? '«interface»' : isAbs ? '«abstract»' : '';
            const attrs  = ((node.attributes||'').split('\n').filter(Boolean)).slice(0,5);
            const meths  = ((node.methods||'').split('\n').filter(Boolean)).slice(0,5);
            const nameSt = isObj ? 'text-decoration:underline;' : isAbs ? 'font-style:italic;' : '';
            const attTxt = attrs.map(a=>`<div style="font-size:${Math.max(5,fs-2)}px;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${a.replace(/</g,'&lt;')}</div>`).join('');
            const metTxt = meths.map(m=>`<div style="font-size:${Math.max(5,fs-2)}px;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${m.replace(/</g,'&lt;')}</div>`).join('');
            return `<div style="border:${bd};border-radius:3px;background:${bg};width:100%;min-height:${h}px;box-sizing:border-box">
                <div style="border-bottom:${bd};padding:2px 4px;font-weight:700;text-align:center;font-size:${fs}px;color:#fff;${nameSt}">${ster?`<div style="font-size:${Math.max(5,fs-3)}px;font-weight:400;font-style:normal">${ster}</div>`:''}${txt}</div>
                <div style="border-bottom:${bd};padding:2px 4px;min-height:16px">${attTxt}</div>
                <div style="padding:2px 4px;min-height:16px">${metTxt}</div>
            </div>`;
        }

        case 'activity': case 'valor': case 'note':
            return `<div style="border:${bd};border-radius:20px;padding:3px 8px;text-align:center;background:${bg};width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:${fs}px;color:#fff;box-sizing:border-box;overflow:hidden">${txt}</div>`;

        case 'state':
            return `<div style="border:${bd};border-radius:8px;padding:3px 8px;text-align:center;background:${bg};width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:${fs}px;color:#fff;box-sizing:border-box;overflow:hidden">${txt}</div>`;

        case 'decision': {
            return `<div style="width:${w}px;height:${h}px;position:relative">
                <svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}" style="display:block;overflow:visible">
                    <polygon points="${w/2},2 ${w-2},${h/2} ${w/2},${h-2} 2,${h/2}" stroke="${c}" stroke-width="2" fill="${c}18"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:${Math.max(5,fs-2)}px;text-align:center;color:#fff;padding:0 ${Math.max(4,w*0.22)}px;box-sizing:border-box;overflow:hidden">${txt}</div>
            </div>`;
        }

        case 'start': case 'initial': {
            const r = Math.min(w, h) * 0.38;
            return `<div style="display:flex;flex-direction:column;align-items:center;gap:2px">
                <div style="width:${r*2}px;height:${r*2}px;border-radius:50%;background:${c}"></div>
                ${txt ? `<div style="font-size:${Math.max(5,fs-3)}px;color:rgba(255,255,255,0.65);overflow:hidden;max-width:${w}px">${txt}</div>` : ''}
            </div>`;
        }

        case 'end': case 'final': {
            const r = Math.min(w, h) * 0.38;
            return `<div style="display:flex;flex-direction:column;align-items:center;gap:2px">
                <div style="width:${r*2}px;height:${r*2}px;border-radius:50%;border:2px solid ${c};display:flex;align-items:center;justify-content:center">
                    <div style="width:${r}px;height:${r}px;border-radius:50%;background:${c}"></div>
                </div>
                ${txt ? `<div style="font-size:${Math.max(5,fs-3)}px;color:rgba(255,255,255,0.65);overflow:hidden;max-width:${w}px">${txt}</div>` : ''}
            </div>`;
        }

        case 'fork': case 'union':
            return `<div style="width:100%;height:${Math.max(h,8)}px;background:${c};border-radius:3px"></div>`;

        case 'lifeline':
            return `<div style="display:flex;flex-direction:column;align-items:center;width:100%;height:${h}px">
                <div style="border:${bd};padding:2px 6px;font-size:${fs}px;color:#fff;background:${bg};width:100%;text-align:center;box-sizing:border-box;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${txt}</div>
                <div style="border-left:2px dashed ${c};flex:1;opacity:0.35"></div>
            </div>`;

        case 'activation':
            return `<div style="width:100%;height:${h}px;background:${c}28;border:${bd};border-radius:2px;box-sizing:border-box"></div>`;

        case 'component':
            return `<div style="border:${bd};border-radius:4px;padding:4px;background:${bg};width:100%;height:${h}px;position:relative;display:flex;align-items:center;box-sizing:border-box;overflow:hidden">
                <svg style="position:absolute;top:4px;right:4px;flex-shrink:0" width="14" height="12" viewBox="0 0 24 20">
                    <rect x="7" y="0" width="17" height="20" stroke="${c}" stroke-width="1.5" fill="none" rx="1"/>
                    <rect x="0" y="3" width="11" height="5" stroke="${c}" stroke-width="1.4" fill="${c}40" rx="1"/>
                    <rect x="0" y="12" width="11" height="5" stroke="${c}" stroke-width="1.4" fill="${c}40" rx="1"/>
                </svg>
                <div style="font-size:${fs}px;color:#fff;padding-right:20px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${txt}</div>
            </div>`;

        case 'node': case 'device': case 'execution-environment': case 'artifact': case 'subsystem':
            return `<div style="border:${bd};border-radius:4px;background:${bg};width:100%;height:${h}px;position:relative;box-sizing:border-box;display:flex;align-items:center;padding:4px 8px">
                <div style="font-size:${fs}px;color:#fff;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${txt}</div>
            </div>`;

        case 'package':
            return `<div style="width:${w}px;height:${h}px;position:relative">
                <div style="position:absolute;top:0;left:0;width:${Math.min(w*0.4,55)}px;height:12px;background:${bg};border:${bd};border-radius:2px 2px 0 0;box-sizing:border-box"></div>
                <div style="position:absolute;top:10px;left:0;right:0;bottom:0;border:${bd};background:${bg}80;border-radius:0 4px 4px 4px"></div>
                <div style="position:absolute;bottom:4px;left:6px;right:6px;font-size:${Math.max(5,fs-2)}px;color:rgba(255,255,255,0.8);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${txt}</div>
            </div>`;

        case 'history':
            return `<div style="display:flex;align-items:center;justify-content:center;width:${Math.min(w,h)}px;height:${Math.min(w,h)}px;border-radius:50%;border:${bd};font-size:${fs}px;color:${c};font-weight:700">H</div>`;

        case 'port':
            return `<div style="width:${w}px;height:${h}px;border:${bd};background:${bg};box-sizing:border-box"></div>`;

        default:
            return `<div style="border:${bd};border-radius:4px;padding:3px;text-align:center;background:${bg};width:100%;height:${h}px;display:flex;align-items:center;justify-content:center;font-size:${fs}px;color:#fff;box-sizing:border-box;overflow:hidden">${txt}</div>`;
    }
}

/* ── Connection endpoint computation (mirrors editor logic) ─────────── */
function connPt(node, side) {
    const nx=+(node.x)||0, ny=+(node.y)||0;
    const nw=+(node.width)||120, nh=+(node.height)||60;
    const cx=nx+nw/2, cy=ny+nh/2;
    if (!side) return {x:cx, y:cy};

    if (side.startsWith('edge-')) {
        const parts = side.split('-');
        const edge  = parts[1]+'-'+parts[2];
        const pct   = parseFloat(parts[3]) || 0.5;
        if (edge==='top-edge')    return {x:nx+pct*nw, y:ny};
        if (edge==='bottom-edge') return {x:nx+pct*nw, y:ny+nh};
        if (edge==='left-edge')   return {x:nx,        y:ny+pct*nh};
        if (edge==='right-edge')  return {x:nx+nw,     y:ny+pct*nh};
    }
    if (side.startsWith('abs2-')) {
        const m = side.match(/^abs2-(-?\d+\.?\d*)-(-?\d+\.?\d*)-/);
        if (m) return {x:+m[1], y:+m[2]};
    }
    if (side.startsWith('abs-')) {
        const last = side.lastIndexOf('-');
        const absY = parseFloat(side.slice(4, last));
        const right = side.endsWith('-right');
        return {x: right ? nx+nw : nx, y: isNaN(absY) ? cy : absY};
    }
    if (side==='left')   return {x:nx,    y:cy};
    if (side==='right')  return {x:nx+nw, y:cy};
    if (side==='top')    return {x:cx,    y:ny};
    if (side==='bottom') return {x:cx,    y:ny+nh};
    return {x:cx, y:cy};
}

function edgeDir(side) {
    if (!side) return 'right';
    if (side.startsWith('edge-')) {
        if (side.includes('right-edge')) return 'right';
        if (side.includes('left-edge'))  return 'left';
        if (side.includes('bottom-edge'))return 'bottom';
        return 'top';
    }
    if (side.startsWith('abs-'))  return side.endsWith('-right') ? 'right' : 'left';
    if (side.startsWith('abs2-')) return 'right';
    return side; // 'left','right','top','bottom'
}

/* ── Main render function ───────────────────────────────────────────── */
function render(container, content, tipo) {
    const nodes = content.nodes || content.nodos || [];
    const conns  = content.connections || content.arrows || content.flechas || [];
    const c      = col(tipo);

    if (!nodes.length) { container.dataset.loaded = 'empty'; return; }

    // Bounding box
    let minX=1e9, minY=1e9, maxX=-1e9, maxY=-1e9;
    nodes.forEach(n => {
        const x=+(n.x)||0, y=+(n.y)||0, w=+(n.width)||120, h=+(n.height)||60;
        if(x<minX)minX=x; if(y<minY)minY=y;
        if(x+w>maxX)maxX=x+w; if(y+h>maxY)maxY=y+h;
    });

    const PAD   = 18;
    const bboxW = (maxX-minX)+PAD*2;
    const bboxH = (maxY-minY)+PAD*2;
    const cW    = container.offsetWidth  || 280;
    const cH    = container.offsetHeight || 140;
    const scale = Math.min(cW/bboxW, cH/bboxH, 1.2);
    const DX    = PAD - minX;  // shift nodes into wrapper space
    const DY    = PAD - minY;
    const sW    = bboxW*scale, sH = bboxH*scale;
    const oX    = ((cW-sW)/2).toFixed(2);
    const oY    = ((cH-sH)/2).toFixed(2);

    // SVG paths for connections
    let paths = '';
    conns.forEach(conn => {
        const fn = nodes.find(n=>n.id===conn.fromNode);
        const tn = nodes.find(n=>n.id===conn.toNode);
        if (!fn||!tn) return;
        const fp = connPt(fn, conn.fromSide);
        const tp = connPt(tn, conn.toSide);
        const x1=fp.x+DX, y1=fp.y+DY, x2=tp.x+DX, y2=tp.y+DY;
        const dx=x2-x1, dy=y2-y1;
        const dist=Math.sqrt(dx*dx+dy*dy);
        const ten=Math.min(Math.max(dist*0.4, 30), 140);
        const fd=edgeDir(conn.fromSide), td=edgeDir(conn.toSide);
        let cx1,cy1,cx2,cy2;
        if(fd==='right'){cx1=x1+ten;cy1=y1;}
        else if(fd==='left'){cx1=x1-ten;cy1=y1;}
        else if(fd==='bottom'){cx1=x1;cy1=y1+ten;}
        else{cx1=x1;cy1=y1-ten;}
        if(td==='left'){cx2=x2-ten;cy2=y2;}
        else if(td==='right'){cx2=x2+ten;cy2=y2;}
        else if(td==='top'){cx2=x2;cy2=y2-ten;}
        else{cx2=x2;cy2=y2+ten;}
        paths+=`<path d="M${x1} ${y1} C${cx1} ${cy1},${cx2} ${cy2},${x2} ${y2}" stroke="${c}" stroke-width="1.2" fill="none" opacity="0.45" stroke-linecap="round"/>`;
    });

    // Node divs
    let divs = '';
    nodes.forEach(n => {
        const x=(+(n.x)||0)+DX, y=(+(n.y)||0)+DY;
        const w=+(n.width)||120, h=+(n.height)||60;
        divs+=`<div style="position:absolute;left:${x}px;top:${y}px;width:${w}px;height:${h}px;pointer-events:none;color:${c}">${nodeHTML(n, tipo)}</div>`;
    });

    container.style.position = 'relative';
    container.style.overflow  = 'hidden';
    container.innerHTML = `<div style="position:absolute;left:${oX}px;top:${oY}px;width:${bboxW}px;height:${bboxH}px;transform:scale(${scale.toFixed(4)});transform-origin:top left;pointer-events:none">
        <svg style="position:absolute;top:0;left:0;pointer-events:none;overflow:visible" width="${bboxW}" height="${bboxH}">
            ${paths}
        </svg>
        ${divs}
    </div>`;
}

/* ── IntersectionObserver for lazy loading ──────────────────────────── */
const _cache   = {};
const _pending = new Set();

const _io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const id = el.dataset.previewId;
        if (!id || el.dataset.loaded) return;
        el.dataset.loaded = 'loading';
        _io.unobserve(el);

        if (_cache[id]) { render(el, _cache[id].content, _cache[id].tipo); return; }

        fetch(BASE_URL + '/api/diagramas/preview?id=' + id, {credentials:'same-origin'})
            .then(r => r.json())
            .then(data => {
                if (data.success && data.content) {
                    const nodes = data.content.nodes || data.content.nodos || [];
                    if (nodes.length) {
                        _cache[id] = data;
                        render(el, data.content, data.tipo);
                        el.dataset.loaded = 'done';
                    } else {
                        el.dataset.loaded = 'empty';
                    }
                } else {
                    el.dataset.loaded = 'empty';
                }
            })
            .catch(() => { el.dataset.loaded = 'empty'; });
    });
}, { rootMargin: '120px', threshold: 0.01 });

/* ── Public API ─────────────────────────────────────────────────────── */
window.DiagramMiniRenderer = {
    /** Observe a single element that has data-preview-id */
    observe: function(el) { if (el && !el.dataset.loaded) _io.observe(el); },
    /** Observe all [data-preview-id] inside root (or document) */
    observeAll: function(root) {
        (root||document).querySelectorAll('[data-preview-id]:not([data-loaded])').forEach(el => _io.observe(el));
    },
    /** Render immediately (if content already available) */
    render: render
};

})();
