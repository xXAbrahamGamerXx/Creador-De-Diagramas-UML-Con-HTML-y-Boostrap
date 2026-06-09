/**
 * editor.js - Lógica completa del editor de diagramas
 * Versión: 2.0 - Actores UML correctos, flechas con punta, posicionamiento inteligente
 */

// ─────────────────────────────────────────────
// Clase para manejar el historial (Undo/Redo)
// ─────────────────────────────────────────────
class HistoryManager {
    constructor() {
        this.undoStack = [];
        this.redoStack = [];
        this.maxStackSize = 50;
    }

    push(state) {
        if (this.undoStack.length >= this.maxStackSize) {
            this.undoStack.shift();
        }
        const stateCopy = JSON.parse(JSON.stringify(state));
        this.undoStack.push(stateCopy);
        this.redoStack = [];
        this.updateUI();
    }

    undo() {
        if (this.undoStack.length === 0) return null;
        const currentState = this.undoStack.pop();
        this.redoStack.push(currentState);
        this.updateUI();
        return this.undoStack.length > 0
            ? JSON.parse(JSON.stringify(this.undoStack[this.undoStack.length - 1]))
            : null;
    }

    redo() {
        if (this.redoStack.length === 0) return null;
        const state = this.redoStack.pop();
        this.undoStack.push(state);
        this.updateUI();
        return JSON.parse(JSON.stringify(state));
    }

    canUndo() { return this.undoStack.length > 0; }
    canRedo() { return this.redoStack.length > 0; }

    updateUI() {
        const undoBtn      = document.getElementById('undoBtn');
        const redoBtn      = document.getElementById('redoBtn');
        const historyCount = document.getElementById('historyCount');
        if (undoBtn)      undoBtn.disabled      = !this.canUndo();
        if (redoBtn)      redoBtn.disabled      = !this.canRedo();
        if (historyCount) historyCount.textContent = this.undoStack.length;
    }

    clear() {
        this.undoStack = [];
        this.redoStack = [];
        this.updateUI();
    }
}

// ─────────────────────────────────────────────
// Clase principal del editor
// ─────────────────────────────────────────────
class DiagramEditor {
    constructor() {
        this.nodes            = [];
        this.connections      = [];
        this.selectedNode     = null;
        this.diagramType      = 'flowchart';
        this.history          = new HistoryManager();
        this.nodeIdCounter    = 1;
        this.dragging         = false;
        this.dragStartX       = 0;
        this.dragStartY       = 0;
        this.connecting       = false;
        this.connectionStart  = null;
        this.unsavedChanges   = false;
        this.autoSaveInterval = null;
        this.diagramId        = null;

        // ── Paleta de figuras por tipo de diagrama ──
        this.shapesByType = {
            flowchart: [
                { type: 'start',    icon: 'bi bi-play-circle',       label: 'Inicio'         },
                { type: 'process',  icon: 'bi bi-square',             label: 'Proceso'        },
                { type: 'decision', icon: 'bi bi-gem',                label: 'Decisión'       },
                { type: 'io',       icon: 'bi bi-box-arrow-in-right', label: 'Entrada/Salida' },
                { type: 'end',      icon: 'bi bi-stop-circle',        label: 'Fin'            }
            ],
            sequence: [
                { type: 'actor',      icon: 'bi bi-person',     label: 'Actor'          },
                { type: 'lifeline',   icon: 'bi bi-arrow-down', label: 'Línea de vida'  },
                { type: 'message',    icon: 'bi bi-arrow-right', label: 'Mensaje'        },
                { type: 'activation', icon: 'bi bi-bar-chart',  label: 'Activación'     }
            ],
            class: [
                { type: 'class',     icon: 'bi bi-box',         label: 'Clase'          },
                { type: 'interface', icon: 'bi bi-diagram-2',   label: 'Interfaz'       },
                { type: 'abstract',  icon: 'bi bi-file-code',   label: 'Abstracta'      },
                { type: 'enum',      icon: 'bi bi-list',        label: 'Enumeración'    }
            ],
            usecase: [
                { type: 'actor',   icon: 'bi bi-person-fill', label: 'Actor'         },
                { type: 'usecase', icon: 'bi bi-circle',      label: 'Caso de Uso'   },
                { type: 'system',  icon: 'bi bi-bounding-box', label: 'Sistema'      }
            ],
            activity: [
                { type: 'start',    icon: 'bi bi-play-circle', label: 'Inicio'        },
                { type: 'activity', icon: 'bi bi-square',      label: 'Actividad'     },
                { type: 'decision', icon: 'bi bi-gem',         label: 'Decisión'      },
                { type: 'fork',     icon: 'bi bi-diagram-3',   label: 'Bifurcación'   },
                { type: 'end',      icon: 'bi bi-stop-circle', label: 'Fin'           }
            ],
            state: [
                { type: 'state',   icon: 'bi bi-square',      label: 'Estado'        },
                { type: 'initial', icon: 'bi bi-circle',      label: 'Inicial'       },
                { type: 'final',   icon: 'bi bi-stop-circle', label: 'Final'         },
                { type: 'choice',  icon: 'bi bi-gem',         label: 'Elección'      }
            ],
            component: [
                { type: 'component', icon: 'bi bi-box',      label: 'Componente'    },
                { type: 'interface', icon: 'bi bi-plug',     label: 'Interfaz'      },
                { type: 'port',      icon: 'bi bi-circle',   label: 'Puerto'        }
            ],
            deployment: [
                { type: 'node',     icon: 'bi bi-cpu',         label: 'Nodo'         },
                { type: 'device',   icon: 'bi bi-hdd',         label: 'Dispositivo'  },
                { type: 'artifact', icon: 'bi bi-file-binary', label: 'Artefacto'    }
            ]
        };

        this.init();
    }

    // ══════════════════════════════════════════
    // INICIALIZACIÓN
    // ══════════════════════════════════════════
    init() {
        this.loadEventListeners();
        this.loadShapesForType(this.diagramType);
        this.updateLayersList();
        this.pushToHistory();

        // Auto-guardado cada 30 s
        this.autoSaveInterval = setInterval(() => {
            if (this.unsavedChanges) this.guardar();
        }, 30000);
    }

    loadEventListeners() {
        const canvas = document.getElementById('diagramCanvas');
        if (!canvas) return;

        canvas.addEventListener('dragover', (e) => e.preventDefault());
        canvas.addEventListener('drop',     (e) => this.handleDrop(e));
        canvas.addEventListener('mousedown', (e) => this.handleCanvasMouseDown(e));
        canvas.addEventListener('click',    (e) => this.handleCanvasClick(e));

        const typeSelect = document.getElementById('diagramTypeSelect');
        if (typeSelect) {
            typeSelect.addEventListener('change', (e) => {
                this.diagramType = e.target.value;
                this.loadShapesForType(this.diagramType);
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'z') { e.preventDefault(); this.undo(); }
            else if (e.ctrlKey && e.key === 'y') { e.preventDefault(); this.redo(); }
            else if (e.key === 'Delete')        { this.deleteSelected(); }
            else if (e.ctrlKey && e.key === 's') { e.preventDefault(); this.guardar(); }
            else if (e.ctrlKey && e.key === 'a') { e.preventDefault(); this.selectAll(); }
        });

        window.addEventListener('beforeunload', (e) => {
            if (this.unsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Hay cambios sin guardar. ¿Estás seguro de salir?';
            }
        });
    }

    // ══════════════════════════════════════════
    // PALETA DE FIGURAS
    // ══════════════════════════════════════════
    loadShapesForType(type) {
        const container = document.getElementById('shapesContainer');
        if (!container) return;

        container.innerHTML = '';
        const shapes = this.shapesByType[type] || this.shapesByType.flowchart;

        shapes.forEach(shape => {
            const shapeEl = document.createElement('div');
            shapeEl.className  = 'shape-item';
            shapeEl.draggable  = true;
            shapeEl.dataset.type = shape.type;
            shapeEl.innerHTML  = `
                <div class="shape-icon"><i class="${shape.icon}"></i></div>
                <div class="small">${shape.label}</div>
            `;
            shapeEl.title = `Clic para añadir · Arrastrar para posicionar`;

            // ── Drag para posicionar libremente ──
            shapeEl.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', JSON.stringify({ type: shape.type }));
            });

            // ── Clic para añadir en posición automática ──
            shapeEl.addEventListener('click', () => {
                this.addNodeAtSmartPosition(shape.type);
            });

            container.appendChild(shapeEl);
        });
    }

    // ══════════════════════════════════════════
    // POSICIONAMIENTO INTELIGENTE
    // ══════════════════════════════════════════

    /**
     * Añade un nodo en una posición calculada para no solaparse.
     */
    addNodeAtSmartPosition(type) {
        const pos  = this.getSmartPosition(type);
        const id   = this.generateNodeId();
        const text = this.getDefaultText(type);
        const node = this.createNode(pos.x, pos.y, text, type, id);

        // El system boundary tiene dimensiones especiales
        if (type === 'system') {
            node.width  = 480;
            node.height = 520;
            this.render();
        }

        this.pushToHistory();
        this.unsavedChanges = true;
    }

    /**
     * Calcula dónde colocar el siguiente nodo de cada tipo.
     *
     * Lógica para use case:
     *   - Los casos de uso se apilan VERTICALMENTE en la columna central.
     *   - Los actores van a la izquierda / derecha, alternando.
     *   - El sistema se coloca una sola vez, grande, en la zona central.
     */
    getSmartPosition(type) {
        if (type === 'actor') {
            const actors = this.nodes.filter(n => n.type === 'actor');
            const col    = actors.length % 2;          // 0 = izq, 1 = der
            const row    = Math.floor(actors.length / 2);
            return {
                x: col === 0 ? 30 : 730,
                y: 100 + row * 170
            };
        }

        if (type === 'usecase') {
            const usecases = this.nodes.filter(n => n.type === 'usecase');
            return {
                x: 260,
                y: 80 + usecases.length * 105
            };
        }

        if (type === 'system') {
            return { x: 170, y: 30 };
        }

        // Tipos genéricos: cascada con desplazamiento
        const sameType = this.nodes.filter(n => n.type === type);
        return {
            x: 130 + (this.nodes.length % 5) * 25,
            y: 130 + sameType.length * 90
        };
    }

    /**
     * Texto predeterminado según el tipo de nodo.
     */
    getDefaultText(type) {
        const map = {
            actor:      'Actor',
            usecase:    'Caso de Uso',
            system:     'Sistema',
            start:      'Inicio',
            end:        'Fin',
            process:    'Proceso',
            decision:   'Decisión',
            io:         'Entrada/Salida',
            class:      'Clase',
            interface:  'Interfaz',
            abstract:   'Abstracta',
            enum:       'Enumeración',
            state:      'Estado',
            initial:    'Inicial',
            final:      'Final',
            choice:     'Elección',
            activity:   'Actividad',
            fork:       'Bifurcación',
            lifeline:   'Línea de Vida',
            message:    'Mensaje',
            activation: 'Activación',
            component:  'Componente',
            port:       'Puerto',
            node:       'Nodo',
            device:     'Dispositivo',
            artifact:   'Artefacto'
        };
        return map[type] || 'Elemento';
    }

    /**
     * Dimensiones predeterminadas por tipo.
     */
    getDefaultDimensions(type) {
        const map = {
            actor:    { width: 80,  height: 100 },
            usecase:  { width: 160, height: 60  },
            system:   { width: 480, height: 520 },
            start:    { width: 80,  height: 60  },
            end:      { width: 80,  height: 60  },
            decision: { width: 130, height: 70  }
        };
        return map[type] || { width: 140, height: 60 };
    }

    // ══════════════════════════════════════════
    // CREACIÓN DE NODOS
    // ══════════════════════════════════════════
    handleDrop(e) {
        e.preventDefault();
        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        const rect = e.currentTarget.getBoundingClientRect();
        const x    = e.clientX - rect.left;
        const y    = e.clientY - rect.top;
        const id   = this.generateNodeId();
        const text = this.getDefaultText(data.type);
        this.createNode(x, y, text, data.type, id);
        this.pushToHistory();
        this.unsavedChanges = true;
    }

    generateNodeId() {
        return `${this.diagramType}_${this.nodeIdCounter++}`;
    }

    createNode(x, y, text, type, id) {
        const dims = this.getDefaultDimensions(type);
        const node = {
            id,
            x,
            y,
            text,
            type,
            width:       dims.width,
            height:      dims.height,
            color:       this.getColorForType(type),
            description: ''
        };
        this.nodes.push(node);
        this.render();
        return node;
    }

    getColorForType(type) {
        const map = {
            start:   '#198754',
            end:     '#dc3545',
            decision:'#fd7e14',
            process: '#0d6efd',
            io:      '#6f42c1',
            actor:   '#ffffff',
            usecase: '#0d6efd',
            system:  '#555555'
        };
        return map[type] || '#0d6efd';
    }

    // ══════════════════════════════════════════
    // RENDER PRINCIPAL
    // ══════════════════════════════════════════
    render() {
        const canvas = document.getElementById('diagramCanvas');
        if (!canvas) return;

        canvas.innerHTML = '';

        // 1. SVG global con marcadores de flecha (referenciables por todos los paths)
        this._injectGlobalDefs(canvas);

        // 2. Fronteras de sistema (fondo, z-index bajo)
        this.nodes
            .filter(n => n.type === 'system')
            .forEach(node => canvas.appendChild(this.createSystemElement(node)));

        // 3. Crear contenedor SVG ÚNICO para todas las conexiones (CORRECCIÓN)
        this._renderAllConnections(canvas);

        // 4. Resto de nodos
        this.nodes
            .filter(n => n.type !== 'system')
            .forEach(node => {
                let el;
                if (node.type === 'actor')   el = this.createActorElement(node);
                else if (node.type === 'usecase') el = this.createUsecaseElement(node);
                else                          el = this.createStandardElement(node);
                canvas.appendChild(el);
            });

        this.updateLayersList();
        this.updatePropertiesPanel();
        this.updateMinimap();
    }

    /**
     * CORRECCIÓN: Renderiza TODAS las conexiones en UN SOLO contenedor SVG
     * Esto evita el doble renderizado y mantiene orden de capas consistente
     */
    _renderAllConnections(canvas) {
        // Eliminar conexiones sin nodos válidos (conexiones huérfanas)
        this.connections = this.connections.filter(conn => {
            const fromNode = this.nodes.find(n => n.id === conn.fromNode);
            const toNode = this.nodes.find(n => n.id === conn.toNode);
            return fromNode && toNode;  // Solo mantener si ambos nodos existen
        });

        if (this.connections.length === 0) return;

        const svgNS = 'http://www.w3.org/2000/svg';
        const svgContainer = document.createElementNS(svgNS, 'svg');
        svgContainer.setAttribute('class', 'connections-layer');
        svgContainer.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
        `;
        svgContainer.setAttribute('overflow', 'visible');

        // Inyectar defs dentro de este contenedor SVG
        svgContainer.innerHTML = `
            <defs>
                <marker id="arrow-solid" markerWidth="10" markerHeight="8"
                        refX="9" refY="4" orient="auto" markerUnits="userSpaceOnUse">
                    <polygon points="0 0, 10 4, 0 8" fill="#0d6efd"/>
                </marker>
                <marker id="arrow-open" markerWidth="12" markerHeight="9"
                        refX="10" refY="4.5" orient="auto" markerUnits="userSpaceOnUse">
                    <polyline points="0 0, 11 4.5, 0 9" fill="none"
                              stroke="#0d6efd" stroke-width="1.8"/>
                </marker>
                <marker id="arrow-dashed" markerWidth="10" markerHeight="8"
                        refX="9" refY="4" orient="auto" markerUnits="userSpaceOnUse">
                    <polygon points="0 0, 10 4, 0 8" fill="#999"/>
                </marker>
                <marker id="arrow-diamond" markerWidth="12" markerHeight="8"
                        refX="12" refY="4" orient="auto" markerUnits="userSpaceOnUse">
                    <polygon points="0 4, 6 0, 12 4, 6 8" fill="#0d6efd"/>
                </marker>
            </defs>
        `;

        // Agregar cada conexión como <g> separado dentro del contenedor
        this.connections.forEach(conn => {
            const pathEl = this._createConnectionPath(conn);
            if (pathEl) svgContainer.appendChild(pathEl);
        });

        canvas.appendChild(svgContainer);
    }

    /**
     * Crea un <g> con la ruta y etiqueta de UNA conexión
     * Retorna null si los nodos no existen (validación adicional)
     */
    _createConnectionPath(conn) {
        const fromNode = this.nodes.find(n => n.id === conn.fromNode);
        const toNode   = this.nodes.find(n => n.id === conn.toNode);
        if (!fromNode || !toNode) return null;

        const svgNS = 'http://www.w3.org/2000/svg';
        const g = document.createElementNS(svgNS, 'g');

        const from = this.getConnectionPoint(fromNode, conn.fromSide);
        const to   = this.getConnectionPoint(toNode,   conn.toSide);

        const connType = conn.type || 'association';
        const isDashed = ['include', 'extend', 'dependency'].includes(connType);
        const markerId = isDashed ? 'arrow-dashed' : 'arrow-solid';
        const color    = isDashed ? '#999' : '#0d6efd';

        // Curva Bezier
        const dx  = to.x - from.x;
        const dy  = to.y - from.y;
        let cx1, cy1, cx2, cy2;

        if (conn.fromSide === 'right' || conn.fromSide === 'left') {
            cx1 = from.x + dx * 0.55; cy1 = from.y;
            cx2 = to.x   - dx * 0.55; cy2 = to.y;
        } else {
            cx1 = from.x;             cy1 = from.y + dy * 0.55;
            cx2 = to.x;               cy2 = to.y   - dy * 0.55;
        }

        const d = `M ${from.x} ${from.y} C ${cx1} ${cy1}, ${cx2} ${cy2}, ${to.x} ${to.y}`;

        const path = document.createElementNS(svgNS, 'path');
        path.setAttribute('d', d);
        path.setAttribute('stroke', color);
        path.setAttribute('stroke-width', '2');
        path.setAttribute('fill', 'none');
        path.setAttribute('marker-end', `url(#${markerId})`);
        if (isDashed) path.setAttribute('stroke-dasharray', '7 4');

        g.appendChild(path);

        // Etiqueta «include» / «extend»
        if (connType === 'include' || connType === 'extend') {
            const mx = (from.x + to.x) / 2;
            const my = (from.y + to.y) / 2;
            const lbl = document.createElementNS(svgNS, 'text');
            lbl.setAttribute('x', mx);
            lbl.setAttribute('y', my - 7);
            lbl.setAttribute('text-anchor', 'middle');
            lbl.setAttribute('fill', '#aaa');
            lbl.setAttribute('font-size', '11');
            lbl.setAttribute('font-style', 'italic');
            lbl.setAttribute('font-family', "'Segoe UI', sans-serif");
            lbl.textContent = `«${connType}»`;
            g.appendChild(lbl);
        }

        // Label personalizado
        if (conn.label && connType !== 'include' && connType !== 'extend') {
            const mx = (from.x + to.x) / 2;
            const my = (from.y + to.y) / 2;
            const lbl = document.createElementNS(svgNS, 'text');
            lbl.setAttribute('x', mx);
            lbl.setAttribute('y', my - 7);
            lbl.setAttribute('text-anchor', 'middle');
            lbl.setAttribute('fill', '#ccc');
            lbl.setAttribute('font-size', '11');
            lbl.setAttribute('font-family', "'Segoe UI', sans-serif");
            lbl.textContent = conn.label;
            g.appendChild(lbl);
        }

        return g;
    }

    /**
     * Inyecta un SVG oculto con los marcadores de flecha globales.
     * (Ahora redundante pero se conserva para compatibilidad futura)
     */
    _injectGlobalDefs(canvas) {
        // Ya no es necesario porque los defs se inyectan en _renderAllConnections
        // Función conservada por si se necesita para otros SVG en el futuro
    }

    // ══════════════════════════════════════════
    // ELEMENTOS DE NODO
    // ══════════════════════════════════════════

    /** Helper: añade eventos de drag, clic y conexión a un elemento nodo. */
    _attachNodeEvents(el, node) {
        el.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('connection-point')) return;
            this.startDraggingNode(e, node);
        });
        el.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectNode(node);
        });
        el.querySelectorAll('.connection-point').forEach(pt => {
            pt.addEventListener('mousedown', (e) => this.startConnection(e, node));
        });
    }

    /** Helper: genera los 4 puntos de conexión como HTML string. */
    _connectionPoints(nodeId) {
        return `
            <div class="connection-point" style="top:0;   left:50%;"  data-node="${nodeId}" data-side="top"></div>
            <div class="connection-point" style="top:100%;left:50%;"  data-node="${nodeId}" data-side="bottom"></div>
            <div class="connection-point" style="top:50%; left:0;"    data-node="${nodeId}" data-side="left"></div>
            <div class="connection-point" style="top:50%; left:100%;" data-node="${nodeId}" data-side="right"></div>
        `;
    }

    /**
     * Actor UML — palito correcto:
     *   círculo (cabeza) + cuerpo + brazos + piernas
     */
    createActorElement(node) {
        const el        = document.createElement('div');
        const isSelected = this.selectedNode && this.selectedNode.id === node.id;
        const stroke    = isSelected ? '#4da3ff' : 'white';

        el.className = 'diagram-node node-actor' + (isSelected ? ' selected' : '');
        el.style.cssText = `
            left: ${node.x}px; top: ${node.y}px;
            width: 80px; height: 100px;
            background: transparent;
            border: ${isSelected ? '2px dashed #0d6efd' : '2px solid transparent'};
            border-radius: 6px;
            box-shadow: none;
            padding: 0;
            text-align: center;
            cursor: move;
        `;

        el.innerHTML = `
            <svg width="80" height="78" viewBox="0 0 80 78"
                 xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
                <!-- Cabeza -->
                <circle cx="40" cy="13" r="11"
                        stroke="${stroke}" stroke-width="2.2" fill="none"/>
                <!-- Cuerpo -->
                <line x1="40" y1="24" x2="40" y2="50"
                      stroke="${stroke}" stroke-width="2.2" stroke-linecap="round"/>
                <!-- Brazo izquierdo -->
                <line x1="15" y1="36" x2="40" y2="38"
                      stroke="${stroke}" stroke-width="2.2" stroke-linecap="round"/>
                <!-- Brazo derecho -->
                <line x1="40" y1="38" x2="65" y2="36"
                      stroke="${stroke}" stroke-width="2.2" stroke-linecap="round"/>
                <!-- Pierna izquierda -->
                <line x1="40" y1="50" x2="20" y2="75"
                      stroke="${stroke}" stroke-width="2.2" stroke-linecap="round"/>
                <!-- Pierna derecha -->
                <line x1="40" y1="50" x2="60" y2="75"
                      stroke="${stroke}" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
            <div style="color:white;font-size:11.5px;font-weight:500;
                        line-height:1.25;word-break:break-word;padding:0 4px;
                        margin-top:2px;">
                ${this._escapeHtml(node.text)}
            </div>
            <!-- Puntos de conexión ajustados a la figura -->
            <div class="connection-point" style="top:2px; left:50%;"   data-node="${node.id}" data-side="top"></div>
            <div class="connection-point" style="top:100%;left:50%;"   data-node="${node.id}" data-side="bottom"></div>
            <div class="connection-point" style="top:36px;left:0;"     data-node="${node.id}" data-side="left"></div>
            <div class="connection-point" style="top:36px;left:100%;"  data-node="${node.id}" data-side="right"></div>
        `;

        // Actualizar dimensiones del nodo
        node.width  = 80;
        node.height = 100;

        this._attachNodeEvents(el, node);
        return el;
    }

    /**
     * Caso de Uso UML — óvalo con texto centrado.
     */
    createUsecaseElement(node) {
        const w         = node.width  || 160;
        const h         = node.height || 60;
        const isSelected = this.selectedNode && this.selectedNode.id === node.id;
        const stroke    = isSelected ? '#4da3ff' : '#aaaaaa';
        const sw        = isSelected ? 3 : 2;
        const fill      = isSelected ? 'rgba(13,110,253,0.22)' : 'rgba(255,255,255,0.05)';

        const el = document.createElement('div');
        el.className = 'diagram-node node-usecase' + (isSelected ? ' selected' : '');
        el.style.cssText = `
            left: ${node.x}px; top: ${node.y}px;
            width: ${w}px; height: ${h}px;
            background: transparent; border: none;
            box-shadow: none; padding: 0; cursor: move;
        `;

        const text = this._escapeHtml(node.text);

        el.innerHTML = `
            <svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}"
                 xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
                <ellipse cx="${w/2}" cy="${h/2}"
                         rx="${w/2 - 3}" ry="${h/2 - 3}"
                         stroke="${stroke}" stroke-width="${sw}" fill="${fill}"/>
                <text x="${w/2}" y="${h/2}"
                      text-anchor="middle" dominant-baseline="central"
                      fill="white" font-size="12"
                      font-family="'Segoe UI',Tahoma,Geneva,Verdana,sans-serif"
                      font-weight="500">${text}</text>
            </svg>
            ${this._connectionPoints(node.id)}
        `;

        this._attachNodeEvents(el, node);
        return el;
    }

    /**
     * Frontera de sistema — rectángulo con label en la parte superior.
     */
    createSystemElement(node) {
        const w         = node.width  || 480;
        const h         = node.height || 520;
        const isSelected = this.selectedNode && this.selectedNode.id === node.id;

        const el = document.createElement('div');
        el.className = 'diagram-node node-system' + (isSelected ? ' selected' : '');
        el.style.cssText = `
            left: ${node.x}px; top: ${node.y}px;
            width: ${w}px; height: ${h}px;
            background: rgba(255,255,255,0.02);
            border: 2px solid ${isSelected ? '#4da3ff' : '#666'};
            border-radius: 6px;
            z-index: 1; cursor: move; padding: 0; box-shadow: none;
        `;

        el.innerHTML = `
            <div style="position:absolute;top:-14px;left:14px;
                        background:#0a0a0a;padding:1px 10px;
                        color:#ccc;font-size:13px;font-weight:600;
                        border-radius:3px;white-space:nowrap;">
                ${this._escapeHtml(node.text)}
            </div>
            ${this._connectionPoints(node.id)}
        `;

        this._attachNodeEvents(el, node);
        return el;
    }

    /**
     * Nodo estándar (flowchart, clases, etc.)
     */
    createStandardElement(node) {
        // CORRECCIÓN: Manejo específico para elementos de diagrama de secuencia
        if (node.type === 'lifeline') {
            return this.createLifelineElement(node);
        } else if (node.type === 'activation') {
            return this.createActivationElement(node);
        }

        const el = document.createElement('div');
        el.className = `diagram-node ${this.getNodeClass(node.type)}`
            + (this.selectedNode && this.selectedNode.id === node.id ? ' selected' : '');

        el.style.cssText = `
            left: ${node.x}px; top: ${node.y}px;
            width: ${node.width}px; min-height: ${node.height}px;
        `;

        el.innerHTML = `
            <div>${this._escapeHtml(node.text)}</div>
            ${this._connectionPoints(node.id)}
        `;

        this._attachNodeEvents(el, node);
        return el;
    }

    /**
     * CORRECCIÓN: Elemento específico para LÍNEA DE VIDA (Sequence Diagram)
     * Dibuja una línea vertical punteada con puntos de conexión en los costados
     */
    createLifelineElement(node) {
        const el = document.createElement('div');
        const isSelected = this.selectedNode && this.selectedNode.id === node.id;

        el.className = 'diagram-node node-lifeline' + (isSelected ? ' selected' : '');
        el.style.cssText = `
            left: ${node.x}px; top: ${node.y}px;
            width: 2px; height: ${node.height || 600}px;
            background: ${isSelected ? '#4da3ff' : '#0d6efd'};
            border: none;
            border-radius: 0;
            cursor: move;
            position: absolute;
        `;

        // Contenedor para puntos de conexión (distribuidos verticalmente)
        el.innerHTML = `
            <div class="connection-point" style="top:10%;left:-8px;width:16px;height:16px;" data-node="${node.id}" data-side="left"></div>
            <div class="connection-point" style="top:30%;left:-8px;width:16px;height:16px;" data-node="${node.id}" data-side="left"></div>
            <div class="connection-point" style="top:50%;left:-8px;width:16px;height:16px;" data-node="${node.id}" data-side="left"></div>
            <div class="connection-point" style="top:70%;left:-8px;width:16px;height:16px;" data-node="${node.id}" data-side="left"></div>
            <div class="connection-point" style="top:90%;left:-8px;width:16px;height:16px;" data-node="${node.id}" data-side="left"></div>
        `;

        this._attachNodeEvents(el, node);
        return el;
    }

    /**
     * CORRECCIÓN: Elemento específico para LÍNEA DE ACTIVACIÓN (Sequence Diagram)
     * Dibuja un pequeño rectángulo que representa cuando un actor está activo
     * Este es el lugar correcto donde deben conectarse las flechas de mensaje
     */
    createActivationElement(node) {
        const el = document.createElement('div');
        const isSelected = this.selectedNode && this.selectedNode.id === node.id;

        el.className = 'diagram-node node-activation' + (isSelected ? ' selected' : '');
        el.style.cssText = `
            left: ${node.x}px; top: ${node.y}px;
            width: ${node.width || 12}px; height: ${node.height || 80}px;
            background: ${isSelected ? '#4da3ff' : '#fff'};
            border: 2px solid ${isSelected ? '#4da3ff' : '#0d6efd'};
            border-radius: 2px;
            cursor: move;
            position: absolute;
            box-shadow: ${isSelected ? '0 0 8px rgba(77,163,255,0.5)' : 'none'};
        `;

        // Puntos de conexión en todos los lados (especialmente importantes aquí)
        el.innerHTML = `
            <div class="connection-point" style="top:0;   left:50%;" data-node="${node.id}" data-side="top"></div>
            <div class="connection-point" style="top:100%;left:50%;" data-node="${node.id}" data-side="bottom"></div>
            <div class="connection-point" style="top:50%; left:0;" data-node="${node.id}" data-side="left"></div>
            <div class="connection-point" style="top:50%; left:100%;" data-node="${node.id}" data-side="right"></div>
        `;

        this._attachNodeEvents(el, node);
        return el;
    }

    // ══════════════════════════════════════════
    // CONEXIONES / FLECHAS (ACTUALIZADO)
    // ══════════════════════════════════════════

    getConnectionPoint(node, side) {
        switch (side) {
            case 'left':   return { x: node.x,                    y: node.y + node.height / 2 };
            case 'right':  return { x: node.x + node.width,       y: node.y + node.height / 2 };
            case 'top':    return { x: node.x + node.width / 2,   y: node.y };
            case 'bottom': return { x: node.x + node.width / 2,   y: node.y + node.height };
            default:       return { x: node.x + node.width / 2,   y: node.y + node.height / 2 };
        }
    }

    getNodeClass(type) {
        const map = {
            start:    'node-start',
            end:      'node-end',
            decision: 'node-decision',
            process:  'node-process',
            io:       'node-io',
            initial:  'node-start',
            final:    'node-end',
            choice:   'node-decision',
            activity: 'node-process'
        };
        return map[type] || '';
    }

    // ══════════════════════════════════════════
    // SELECCIÓN Y PROPIEDADES
    // ══════════════════════════════════════════
    selectNode(node) {
        this.selectedNode = node;
        this.render();
        this.updatePropertiesPanel();
    }

    updatePropertiesPanel() {
        const propsContent = document.getElementById('propertiesContent');
        const noSelection  = document.getElementById('noSelectionMessage');
        if (!propsContent || !noSelection) return;

        if (this.selectedNode) {
            propsContent.style.display = 'block';
            noSelection.style.display  = 'none';

            const nodeText   = document.getElementById('nodeText');
            const nodeId     = document.getElementById('nodeId');
            const nodeType   = document.getElementById('nodeType');
            const nodeColor  = document.getElementById('nodeColor');
            const nodeDesc   = document.getElementById('nodeDescription');

            if (nodeText)  nodeText.value  = this.selectedNode.text        || '';
            if (nodeId)    nodeId.value    = this.selectedNode.id          || '';
            if (nodeType)  nodeType.value  = this.selectedNode.type        || 'default';
            if (nodeColor) nodeColor.value = this.selectedNode.color       || '#0d6efd';
            if (nodeDesc)  nodeDesc.value  = this.selectedNode.description || '';
        } else {
            propsContent.style.display = 'none';
            noSelection.style.display  = 'block';
        }
    }

    aplicarPropiedades() {
        if (!this.selectedNode) return;

        const nodeText  = document.getElementById('nodeText');
        const nodeId    = document.getElementById('nodeId');
        const nodeType  = document.getElementById('nodeType');
        const nodeColor = document.getElementById('nodeColor');
        const nodeDesc  = document.getElementById('nodeDescription');

        const oldId = this.selectedNode.id;
        const newId = nodeId ? nodeId.value : oldId;

        if (nodeText)  this.selectedNode.text        = nodeText.value;
        if (nodeId && newId !== oldId) {
            // CORRECCIÓN: Actualizar todas las referencias en conexiones cuando cambia el ID
            this.connections.forEach(conn => {
                if (conn.fromNode === oldId) conn.fromNode = newId;
                if (conn.toNode === oldId)   conn.toNode = newId;
            });
            this.selectedNode.id = newId;
        }
        if (nodeType)  this.selectedNode.type        = nodeType.value;
        if (nodeColor) this.selectedNode.color       = nodeColor.value;
        if (nodeDesc)  this.selectedNode.description = nodeDesc.value;

        this.render();
        this.pushToHistory();
        this.unsavedChanges = true;
    }

    // ══════════════════════════════════════════
    // ARRASTRAR NODOS
    // ══════════════════════════════════════════
    startDraggingNode(e, node) {
        e.preventDefault();
        this.dragging   = true;
        this.selectedNode = node;
        this.dragStartX = e.clientX - node.x;
        this.dragStartY = e.clientY - node.y;

        const onMouseMove = (e) => {
            if (!this.dragging) return;
            node.x = e.clientX - this.dragStartX;
            node.y = e.clientY - this.dragStartY;
            this.render();
        };

        const onMouseUp = () => {
            this.dragging = false;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup',   onMouseUp);
            this.pushToHistory();
            this.unsavedChanges = true;
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup',   onMouseUp);
    }

    // ══════════════════════════════════════════
    // CREAR CONEXIONES
    // ══════════════════════════════════════════
    startConnection(e, fromNode) {
        e.stopPropagation();
        
        // CORRECCIÓN: Validar que el nodo origen sea válido para conexiones
        if (!this._canNodeInitiateConnection(fromNode)) {
            console.warn(`${fromNode.type} no puede iniciar conexiones en ${this.diagramType}`);
            return;
        }

        this.connecting = true;
        this.connectionStart = {
            node: fromNode.id,
            side: e.target.dataset.side,
            x: e.clientX,
            y: e.clientY
        };

        const onMouseMove = () => { /* línea temporal: mejora futura */ };

        const onMouseUp = (e) => {
            const target = document.elementFromPoint(e.clientX, e.clientY);
            if (target && target.classList.contains('connection-point')) {
                const toNodeId = target.dataset.node;
                const toSide   = target.dataset.side;
                const toNode   = this.nodes.find(n => n.id === toNodeId);

                // CORRECCIÓN: Validar que el nodo destino sea válido para recibir conexiones
                if (!toNode || !this._canNodeReceiveConnection(toNode, this.connectionStart.node === toNodeId)) {
                    console.warn(`${toNode?.type || 'unknown'} no puede recibir conexiones en ${this.diagramType}`);
                    this.connecting = false;
                    this.connectionStart = null;
                    return;
                }

                if (this.connectionStart.node !== toNodeId) {
                    // Inferir tipo de conexión según el diagrama actual
                    const connType = this._inferConnectionType(this.connectionStart.node, toNodeId, fromNode.type || 'unknown', toNode.type || 'unknown');

                    this.connections.push({
                        fromNode: this.connectionStart.node,
                        toNode:   toNodeId,
                        fromSide: this.connectionStart.side,
                        toSide,
                        type:     connType,
                        label:    ''
                    });

                    this.render();
                    this.pushToHistory();
                    this.unsavedChanges = true;
                }
            }

            this.connecting      = false;
            this.connectionStart = null;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup',   onMouseUp);
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup',   onMouseUp);
    }

    /**
     * CORRECCIÓN: Valida si un nodo puede INICIAR una conexión
     * según el tipo de diagrama actual
     */
    _canNodeInitiateConnection(node) {
        switch (this.diagramType) {
            case 'sequence':
                // En secuencia: solo mensajes, activación y líneas de vida pueden iniciar
                return ['message', 'activation', 'lifeline', 'actor'].includes(node.type);
            case 'usecase':
                // En casos de uso: actores y casos de uso pueden conectar
                return ['actor', 'usecase'].includes(node.type);
            case 'class':
                // En clases: todas pueden conectar
                return true;
            case 'flowchart':
            case 'activity':
                // En flujos y actividades: todo menos los puntos finales
                return node.type !== 'final';
            default:
                return true;
        }
    }

    /**
     * CORRECCIÓN: Valida si un nodo puede RECIBIR una conexión
     * según el tipo de diagrama actual
     */
    _canNodeReceiveConnection(node, isCircularConnection) {
        if (isCircularConnection) return false;  // No permitir auto-loops

        switch (this.diagramType) {
            case 'sequence':
                // En secuencia: solo pueden recibir en activación o líneas de vida
                // Importante: Las flechas deben conectarse en activación, no en lifeline
                return ['activation', 'lifeline', 'message', 'actor'].includes(node.type);
            case 'usecase':
                return ['actor', 'usecase'].includes(node.type);
            case 'class':
                return true;
            case 'flowchart':
            case 'activity':
                return node.type !== 'initial';
            default:
                return true;
        }
    }

    /**
     * Inferir el tipo de relación según el contexto.
     * Se extendió para pasar tipos de nodo como contexto
     */
    _inferConnectionType(fromId, toId, fromType, toType) {
        // Casos de uso: si es usecase->usecase, puede ser asociación/include/extend
        if (fromType === 'usecase' && toType === 'usecase') {
            return 'association';  // Extensible en UI futura para elegir include/extend
        }
        return 'association';
    }

    // ══════════════════════════════════════════
    // CANVAS EVENTS
    // ══════════════════════════════════════════
    handleCanvasMouseDown(e) {
        if (e.target === e.currentTarget) {
            this.selectedNode = null;
            this.render();
            this.updatePropertiesPanel();
        }
    }

    handleCanvasClick(e) { /* reservado */ }

    // ══════════════════════════════════════════
    // PANEL DE CAPAS / MINIMAPA
    // ══════════════════════════════════════════
    updateLayersList() {
        const layersList = document.getElementById('layersList');
        if (!layersList) return;

        layersList.innerHTML = '';
        this.nodes.forEach(node => {
            const el = document.createElement('div');
            el.className = 'p-2 border-bottom border-dark-gray d-flex align-items-center';
            el.innerHTML = `
                <i class="bi bi-circle-fill me-2" style="color:${node.color};font-size:10px;"></i>
                <span class="small text-light flex-grow-1">${node.text} (${node.id})</span>
                <button class="btn btn-sm btn-outline-primary py-0 px-1"
                        onclick="editor.selectNodeById('${node.id}')">
                    <i class="bi bi-eye"></i>
                </button>
            `;
            layersList.appendChild(el);
        });
    }

    selectNodeById(id) {
        const node = this.nodes.find(n => n.id === id);
        if (node) this.selectNode(node);
    }

    updateMinimap() {
        const minimap = document.getElementById('minimapCanvas');
        if (!minimap) return;
        minimap.innerHTML = `<div class="p-2 text-gray small">${this.nodes.length} elementos · ${this.connections.length} conexiones</div>`;
    }

    // ══════════════════════════════════════════
    // HISTORIAL (UNDO / REDO)
    // ══════════════════════════════════════════
    pushToHistory() {
        this.history.push({ nodes: this.nodes, connections: this.connections });
    }

    undo() {
        const state = this.history.undo();
        if (state) {
            this.nodes       = JSON.parse(JSON.stringify(state.nodes));
            this.connections = JSON.parse(JSON.stringify(state.connections));
            // CORRECCIÓN: Validar conexiones al restaurar desde historial
            this.connections = this.connections.filter(conn => {
                const fromNode = this.nodes.find(n => n.id === conn.fromNode);
                const toNode = this.nodes.find(n => n.id === conn.toNode);
                return fromNode && toNode;
            });
            this.selectedNode = null;
            this.render();
            this.unsavedChanges = true;
        }
    }

    redo() {
        const state = this.history.redo();
        if (state) {
            this.nodes       = JSON.parse(JSON.stringify(state.nodes));
            this.connections = JSON.parse(JSON.stringify(state.connections));
            // CORRECCIÓN: Validar conexiones al restaurar desde historial
            this.connections = this.connections.filter(conn => {
                const fromNode = this.nodes.find(n => n.id === conn.fromNode);
                const toNode = this.nodes.find(n => n.id === conn.toNode);
                return fromNode && toNode;
            });
            this.selectedNode = null;
            this.render();
            this.unsavedChanges = true;
        }
    }

    // ══════════════════════════════════════════
    // ELIMINAR
    // ══════════════════════════════════════════
    deleteSelected() {
        if (!this.selectedNode) return;

        // CORRECCIÓN: Contar conexiones que se van a eliminar para informar al usuario
        const orphanedConnections = this.connections.filter(c =>
            c.fromNode === this.selectedNode.id || c.toNode === this.selectedNode.id
        );

        const deletingText = orphanedConnections.length > 0 
            ? `Se eliminarán también ${orphanedConnections.length} conexión(es)`
            : 'Sin conexiones';

        if (!confirm(`Eliminar "${this.selectedNode.text}"?\n${deletingText}`)) {
            return;
        }

        this.connections = this.connections.filter(c =>
            c.fromNode !== this.selectedNode.id && c.toNode !== this.selectedNode.id
        );
        this.nodes = this.nodes.filter(n => n.id !== this.selectedNode.id);
        this.selectedNode = null;

        this.render();
        this.pushToHistory();
        this.unsavedChanges = true;
    }

    selectAll() {
        alert('Seleccionar todo — funcionalidad próximamente');
    }

    // ══════════════════════════════════════════
    // API — CARGAR / GUARDAR
    // ══════════════════════════════════════════
    setDiagramId(id) { this.diagramId = id; }

    async cargarDiagrama(id) {
        try {
            const response = await fetch('api/load_diagram.php?id=' + id);
            const data     = await response.json();

            if (data.success) {
                const d = data.diagrama;
                this.nodes       = d.contenido.nodes       || [];
                this.connections = d.contenido.connections || [];
                
                // CORRECCIÓN: Validar conexiones cargadas para asegurar integridad
                this.connections = this.connections.filter(conn => {
                    const fromNode = this.nodes.find(n => n.id === conn.fromNode);
                    const toNode = this.nodes.find(n => n.id === conn.toNode);
                    return fromNode && toNode;
                });

                this.diagramType = d.tipo_diagrama;

                const typeSelect  = document.getElementById('diagramTypeSelect');
                const versionNum  = document.getElementById('versionNum');
                if (typeSelect) typeSelect.value         = this.diagramType;
                if (versionNum) versionNum.textContent   = d.version;

                this.loadShapesForType(this.diagramType);
                this.render();
                this.pushToHistory();
                this.unsavedChanges = false;
                return true;
            }
            return false;
        } catch (err) {
            console.error('Error cargando diagrama:', err);
            return false;
        }
    }

    async guardar() {
        const tituloEl = document.getElementById('diagramaTitulo');
        if (!tituloEl) return false;

        const contenido = {
            nodes:       this.nodes,
            connections: this.connections,
            diagramType: this.diagramType
        };

        const data = {
            id:          this.diagramId,
            titulo:      tituloEl.textContent,
            tipo:        this.diagramType,
            contenido,
            descripcion: '',
            etiquetas:   ''
        };

        try {
            const response = await fetch('api/save_diagram.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(data)
            });
            const result = await response.json();

            if (result.success) {
                this.unsavedChanges = false;
                this.showAutoSave('Guardado correctamente');

                if (!this.diagramId && result.id) {
                    this.diagramId = result.id;
                    history.pushState({}, '', 'editor.php?id=' + result.id);
                }

                const versionNum = document.getElementById('versionNum');
                if (versionNum) {
                    versionNum.textContent = (parseInt(versionNum.textContent) || 0) + 1;
                }
                return true;
            } else {
                this.showAutoSave('Error: ' + (result.error || 'desconocido'), 'error');
                return false;
            }
        } catch (err) {
            console.error('Error guardando:', err);
            this.showAutoSave('Error de conexión', 'error');
            return false;
        }
    }

    showAutoSave(mensaje, tipo = 'success') {
        const indicator = document.getElementById('autoSaveIndicator');
        if (!indicator) return;
        const icon = tipo === 'success' ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-danger';
        indicator.innerHTML = `<i class="bi bi-${icon}"></i> ${mensaje}`;
        indicator.style.display = 'block';
        setTimeout(() => { indicator.style.display = 'none'; }, 3000);
    }

    // ══════════════════════════════════════════
    // EXPORTAR / NUEVO
    // ══════════════════════════════════════════
    newDiagram() {
        if (confirm('¿Crear nuevo diagrama? Se perderán los cambios no guardados.')) {
            window.location.href = 'editor.php';
        }
    }

    exportar(formato = 'json') {
        const contenido = {
            nodes:       this.nodes,
            connections: this.connections,
            diagramType: this.diagramType,
            version:     '2.0',
            fecha:       new Date().toISOString()
        };

        if (formato === 'json') {
            const blob = new Blob([JSON.stringify(contenido, null, 2)], { type: 'application/json' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = 'diagrama.json';
            a.click();
            URL.revokeObjectURL(url);
        } else if (formato === 'png') {
            alert('Exportación a PNG próximamente');
        }
    }

    destroy() {
        if (this.autoSaveInterval) clearInterval(this.autoSaveInterval);
    }

    // ══════════════════════════════════════════
    // UTILIDADES PRIVADAS
    // ══════════════════════════════════════════
    _escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
}

// ─────────────────────────────────────────────
// INICIALIZACIÓN
// ─────────────────────────────────────────────
let editor = null;

document.addEventListener('DOMContentLoaded', function () {
    editor = new DiagramEditor();
    window.editor = editor;

    const saveBtn       = document.getElementById('saveBtn');
    const saveVersionBtn= document.getElementById('saveVersionBtn');
    const exportBtn     = document.getElementById('exportBtn');
    const applyBtn      = document.getElementById('applyPropertiesBtn');
    const deleteBtn     = document.getElementById('deleteElementBtn');
    const newBtn        = document.getElementById('newBtn');

    if (saveBtn)        saveBtn.addEventListener('click',        () => editor.guardar());
    if (saveVersionBtn) saveVersionBtn.addEventListener('click', () => mostrarModalVersion());
    if (exportBtn)      exportBtn.addEventListener('click',      () => editor.exportar('json'));
    if (applyBtn)       applyBtn.addEventListener('click',       () => editor.aplicarPropiedades());
    if (deleteBtn)      deleteBtn.addEventListener('click',      () => editor.deleteSelected());
    if (newBtn)         newBtn.addEventListener('click',         () => editor.newDiagram());

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    if (window.diagramaId) {
        editor.setDiagramId(window.diagramaId);
        editor.cargarDiagrama(window.diagramaId);
    } else {
        // Nodo de bienvenida para diagrama nuevo
        setTimeout(() => {
            editor.createNode(300, 200, 'Inicio', 'start', 'start');
        }, 100);
    }
});

// ─────────────────────────────────────────────
// FUNCIONES GLOBALES (llamadas desde HTML)
// ─────────────────────────────────────────────
function mostrarModalVersion() {
    const modal = new bootstrap.Modal(document.getElementById('versionModal'));
    modal.show();
}

function guardarVersion() {
    bootstrap.Modal.getInstance(document.getElementById('versionModal')).hide();
    if (editor) editor.guardar();
}

function exportarDiagrama() {
    if (editor) editor.exportar('json');
}