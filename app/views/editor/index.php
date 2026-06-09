<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $diagrama_data ? htmlspecialchars($diagrama_data['titulo']) : 'Nuevo Diagrama'; ?> - Editor</title>
    <link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #000;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        /* Tema claro - corregido para todos los textos */
        body.light-theme {
            background-color: #f8f9fa;
            color: #000;
        }

        body.light-theme .bg-dark-card,
        body.light-theme .toolbar,
        body.light-theme .sidebar,
        body.light-theme .properties-panel,
        body.light-theme .bg-dark,
        body.light-theme .bg-black,
        body.light-theme.bg-black {
            background-color: #ffffff !important;
            border-color: #dee2e6 !important;
        }

        body.light-theme .text-white,
        body.light-theme .text-light,
        body.light-theme .text-gray,
        body.light-theme label,
        body.light-theme .small,
        body.light-theme .form-text,
        body.light-theme .text-muted {
            color: #6c757d !important;
        }

        body.light-theme .text-white,
        body.light-theme .text-light {
            color: #212529 !important;
        }

        body.light-theme .form-control::placeholder,
        body.light-theme .form-select::placeholder {
            color: #6c757d !important;
            opacity: 1;
        }

        body.light-theme .border-dark,
        body.light-theme .border-dark-gray {
            border-color: #dee2e6 !important;
        }

        body.light-theme .badge.bg-secondary {
            background-color: #e9ecef !important;
            color: #212529 !important;
        }

        body.light-theme .diagram-preview-panel {
            background: #ffffff !important;
            border-color: #dee2e6 !important;
            color: #212529 !important;
        }

        body.light-theme .diagram-preview-box {
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
            color: #495057 !important;
        }

        body.light-theme .preview-placeholder {
            opacity: 0.85;
            color: #495057 !important;
        }

        body.light-theme .diagram-preview-panel .badge {
            background: #e9ecef !important;
            color: #212529 !important;
        }

        body.light-theme .border-dark-gray {
            border-color: #dee2e6 !important;
        }

        body.light-theme .toolbar button {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #000;
        }

        body.light-theme .toolbar button:hover:not(:disabled) {
            background: #0d6efd;
            color: #fff;
        }

        body.light-theme .sidebar-tabs {
            background: #ffffff !important;
            border-bottom-color: #dee2e6 !important;
        }

        body.light-theme .sidebar-tab-btn {
            color: #495057;
        }

        body.light-theme .sidebar-tab-btn:hover {
            color: #000;
            background: rgba(13, 110, 253, 0.08);
        }

        body.light-theme .sidebar-tab-btn.active {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.08);
            border-bottom-color: #0d6efd;
        }

        body.light-theme .sidebar-collapse-btn-outer,
        body.light-theme .properties-collapse-btn-outer {
            background: #ffffff;
            border-color: #dee2e6;
            color: #495057;
        }

        body.light-theme .sidebar-collapse-btn-outer:hover,
        body.light-theme .properties-collapse-btn-outer:hover {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.12);
            border-color: #0d6efd;
        }

        body.light-theme .sidebar-tabs,
        body.light-theme .properties-tabs {
            background: #ffffff !important;
            border-bottom-color: #dee2e6 !important;
        }

        body.light-theme .properties-tab-btn,
        body.light-theme .sidebar-tab-btn {
            color: #495057;
        }

        body.light-theme .properties-tab-btn:hover,
        body.light-theme .sidebar-tab-btn:hover {
            color: #000;
            background: rgba(13, 110, 253, 0.08);
        }

        body.light-theme .properties-tab-btn.active,
        body.light-theme .sidebar-tab-btn.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background: rgba(13, 110, 253, 0.08);
        }

        body.light-theme .shape-item {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #000;
        }

        body.light-theme .shape-item .small {
            color: #000 !important;
        }

        body.light-theme .form-control,
        body.light-theme .form-select,
        body.light-theme .arrow-selector,
        body.light-theme .arrow-selector-container {
            background: #ffffff;
            border-color: #dee2e6;
            color: #000;
        }

        body.light-theme .arrow-selector option {
            background: #ffffff;
            color: #000;
        }

        body.light-theme .arrow-preview {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #000;
        }

        body.light-theme .diagram-node {
            color: #000;
        }

        /* Textos de nodo: solo color, SIN background para no tapar flechas */
        body.light-theme .diagram-node .actor-name,
        body.light-theme .diagram-node .usecase-name,
        body.light-theme .diagram-node .node-text,
        body.light-theme .diagram-node .relation-stereotype,
        body.light-theme .diagram-node .lifeline-header,
        body.light-theme .diagram-node .decision-text,
        body.light-theme .diagram-node div,
        body.light-theme .diagram-node span {
            color: #000;
            background: transparent;
        }
        /* Internos de clase/objeto: transparente también */
        body.light-theme .diagram-node .class-name,
        body.light-theme .diagram-node .class-attribute,
        body.light-theme .diagram-node .class-method {
            color: #000;
            background: transparent;
        }
        /* Actor y UseCase: fondo completamente transparente */
        body.light-theme .diagram-node[data-type="actor"],
        body.light-theme .diagram-node[data-type="usecase"] {
            background: transparent !important;
        }

        body.light-theme .uml-system .system-name {
            background: #ffffff;
            color: #000;
        }

        body.light-theme .uml-actor .actor-head,
        body.light-theme .uml-actor .actor-body,
        body.light-theme .uml-actor .actor-arms,
        body.light-theme .uml-actor .actor-legs,
        body.light-theme .uml-actor .actor-arms::before,
        body.light-theme .uml-actor .actor-arms::after,
        body.light-theme .uml-actor .actor-legs::before,
        body.light-theme .uml-actor .actor-legs::after {
            background: #1a1a2e;
            border-color: #1a1a2e;
        }
        /* Actor SVG en secuencia: usa stroke/currentColor — NO background */
        body.light-theme .uml-actor-lifeline svg {
            color: #1a1a2e;
        }
        body.light-theme .actor-lifeline-head .actor-name {
            color: #1a1a2e;
            background: transparent;
        }

        body.light-theme .uml-usecase,
        body.light-theme .uml-class,
        body.light-theme .uml-state,
        body.light-theme .uml-activity,
        body.light-theme .uml-component,
        body.light-theme .uml-node,
        body.light-theme .node-device,
        body.light-theme .uml-artifact {
            border-color: #000;
            color: #000;
            background: rgba(0, 0, 0, 0.05);
        }

        body.light-theme .uml-lifeline .lifeline-header {
            border-color: #000;
            color: #000;
        }

        body.light-theme .uml-lifeline .lifeline-line {
            background: #000;
        }

        body.light-theme .uml-initial {
            background: #000;
        }

        body.light-theme .uml-final {
            border-color: #000;
        }

        body.light-theme .uml-final::after {
            background: #000;
        }

        body.light-theme .uml-decision {
            border-top-color: #000;
        }

        body.light-theme #usecaseLayoutToggle .btn-outline-secondary {
            color: #000;
            border-color: #dee2e6;
        }

        body.light-theme .uml-fork {
            background: #000;
        }

        body.light-theme .uml-history {
            border-color: #000;
            color: #000;
        }

        body.light-theme .connection-point {
            border-color: #000;
        }

        body.light-theme .resize-handle::after {
            border-color: #000;
        }

        body.light-theme .version-badge {
            background: #f8f9fa;
            color: #000;
            border: 1px solid #dee2e6;
        }

        body.light-theme .auto-save-indicator {
            background: #f8f9fa;
            color: #000;
            border: 1px solid #dee2e6;
        }

        .bg-black {
            background-color: #000 !important;
        }

        .bg-dark-card {
            background-color: #1a1a1a !important;
        }

        .border-dark-gray {
            border-color: #333 !important;
        }

        .text-gray {
            color: #999 !important;
        }

        .toolbar {
            background-color: #1a1a1a;
            border-bottom: 1px solid #333;
            padding: 8px 16px;
            height: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar button {
            background: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
        }

        .toolbar button:hover:not(:disabled) {
            background: #0d6efd;
            border-color: #0d6efd;
        }

        .toolbar button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* Selector de flechas mejorado con soporte para tema */
        .arrow-selector-container {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 4px;
            padding: 0 8px;
            height: 32px;
            transition: all 0.2s;
        }

        .arrow-selector {
            background: #2a2a2a;
            border: none;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            min-width: 180px;
            height: 28px;
            transition: all 0.2s;
        }

        .arrow-selector:focus {
            outline: none;
            border-color: #0d6efd;
        }

        .arrow-selector option {
            background: #2a2a2a;
            color: #fff;
            padding: 8px;
        }

        .arrow-preview {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 4px;
            padding: 4px 12px;
            height: 32px;
            font-size: 13px;
            transition: all 0.2s;
        }

        .arrow-preview svg {
            width: 60px;
            height: 20px;
            stroke: currentColor;
            fill: none;
        }

        .canvas-container {
            background-color: #0a0a0a;
            height: calc(100vh - 50px);
            overflow: hidden;
            position: relative;
            cursor: default;
        }

        body.light-theme .canvas-container {
            background-color: #f0f2f5;
        }

        /* Capa de zoom/pan — se transforma con scale+translate */
        #canvasViewport {
            position: absolute;
            top: 0; left: 0;
            width: 4000px;
            height: 3000px;
            transform-origin: 0 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        body.light-theme #canvasViewport {
            background-image:
                linear-gradient(rgba(0,0,0,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.06) 1px, transparent 1px);
        }

        #diagramCanvas {
            position: absolute;
            top: 0; left: 0;
            width: 4000px;
            height: 3000px;
        }

        /* Controles de zoom flotantes */
        .zoom-controls {
            position: absolute;
            bottom: 18px;
            right: 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            z-index: 200;
        }
        .zoom-controls button {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: 1px solid #333;
            background: #1a1a1a;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s;
        }
        .zoom-controls button:hover { background: #0d6efd; border-color: #0d6efd; }
        body.light-theme .zoom-controls button {
            background: #fff; color: #222; border-color: #ccc;
        }
        .zoom-level-label {
            text-align: center;
            font-size: 11px;
            color: #888;
            padding: 2px 0;
        }

        /* Estilos para nodos de diagramas UML */
        .diagram-node {
            position: absolute;
            background: transparent;
            border: none;
            padding: 0;
            cursor: move;
            color: #fff;
            font-weight: 500;
            
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            
            z-index: 10;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .diagram-node.selected {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 4px;
        }

        /* Sistema como contenedor */
        .uml-system {
            border: 2px solid currentColor;
            border-radius: 8px;
            padding: 30px 20px 20px 20px;
            min-width: 300px;
            min-height: 200px;
            position: relative;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            background: rgba(13, 110, 253, 0.05);
        }

        .system-name {
            position: absolute;
            top: -10px;
            left: 20px;
            background: inherit;
            padding: 0 10px;
            font-size: 14px;
            font-weight: bold;
            background: #1a1a1a;
            z-index: 5;
        }

        body.light-theme .system-name {
            background: #ffffff;
        }

        .system-content {
            width: 100%;
            height: 100%;
            min-height: 150px;
            position: relative;
        }

        /* Punto de redimensionamiento */
        .resize-handle {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 20px;
            height: 20px;
            cursor: nw-resize;
            z-index: 15;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .resize-handle::after {
            content: '';
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 12px;
            height: 12px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
        }

        .resize-handle:hover {
            opacity: 1;
        }

        .diagram-node.selected .resize-handle {
            opacity: 1;
        }

        /* Actor corregido */
        .uml-actor {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
        }

        .actor-head {
            width: 30px;
            height: 30px;
            border: 2px solid currentColor;
            border-radius: 50%;
            margin-bottom: 5px;
        }

        .actor-body {
            width: 2px;
            height: 30px;
            background: currentColor;
            margin-bottom: 5px;
        }

        .actor-arms {
            width: 40px;
            height: 2px;
            background: currentColor;
            position: relative;
            margin-bottom: 10px;
        }

        .actor-arms::before,
        .actor-arms::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 2px;
            background: currentColor;
            top: 0;
        }

        .actor-arms::before {
            left: -20px;
            transform: rotate(-30deg);
            transform-origin: right;
        }

        .actor-arms::after {
            right: -20px;
            transform: rotate(30deg);
            transform-origin: left;
        }

        .actor-legs {
            width: 40px;
            height: 2px;
            background: currentColor;
            position: relative;
        }

        .actor-legs::before,
        .actor-legs::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 2px;
            background: currentColor;
            top: 0;
        }

        .actor-legs::before {
            left: -20px;
            transform: rotate(15deg);
            transform-origin: right;
        }

        .actor-legs::after {
            right: -20px;
            transform: rotate(-15deg);
            transform-origin: left;
        }

        .actor-name {
            margin-top: 5px;
            font-size: 12px;
            text-align: center;
            background: rgba(0,0,0,0.5);
            padding: 2px 8px;
            border-radius: 4px;
            color: #fff;
        }

        /* Caso de Uso */
        .uml-usecase {
            border: 2px solid currentColor;
            border-radius: 50px;
            padding: 8px 20px;
            min-width: 120px;
            text-align: center;
            background: rgba(13, 110, 253, 0.1);
            cursor: move;
        }

        /* Activación de secuencia: anular min-width del diagrama-node base */
        .diagram-node[data-type="activation"] {
            min-width: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            overflow: visible;
        }

        .usecase-name {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #fff;
        }

        /* Include/Extend */
        .uml-relation {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 100px;
        }

        .relation-line {
            width: 60px;
            height: 2px;
            background: currentColor;
            position: relative;
            margin: 10px 0;
        }

        .relation-line::after {
            content: '';
            position: absolute;
            right: -5px;
            top: -4px;
            width: 0;
            height: 0;
            border-left: 8px solid currentColor;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
        }

        .relation-stereotype {
            font-size: 10px;
            font-style: italic;
            background: rgba(0,0,0,0.5);
            padding: 2px 5px;
            border-radius: 4px;
            color: #fff;
        }

        /* Clase */
        .uml-class {
            border: 2px solid currentColor;
            border-radius: 4px;
            min-width: 150px;
            background: rgba(13, 110, 253, 0.1);
        }

        .class-name {
            border-bottom: 2px solid currentColor;
            padding: 8px;
            font-weight: bold;
            text-align: center;
            color: #fff;
        }

        .class-attributes {
            border-bottom: 2px solid currentColor;
            padding: 8px;
            min-height: 50px;
        }

        .class-methods {
            padding: 8px;
            min-height: 50px;
        }

        .class-attribute,
        .class-method {
            font-size: 11px;
            padding: 2px 0;
            color: #fff;
        }

        /* Puntos de conexión de secuencia: visibles a lo largo del eje */
        .seq-point {
            background: #0d6efd !important;
            border-color: #fff !important;
            opacity: 0 !important;
        }

        .diagram-node:hover .seq-point {
            opacity: 0.7 !important;
        }

        .seq-point:hover {
            opacity: 1 !important;
            transform: translate(-50%, -50%) scale(1.4) !important;
        }

        /* Zona continua de conexión para secuencia */
        .seq-zone {
            position: absolute;
            cursor: crosshair;
            z-index: 20;
            opacity: 0;
            transition: opacity 0.15s;
            border-radius: 4px;
        }

        .diagram-node:hover .seq-zone {
            opacity: 1;
            background: rgba(13,110,253,0.18);
            border: 1px dashed rgba(13,110,253,0.6);
        }

        .seq-zone:hover {
            background: rgba(13,110,253,0.35) !important;
        }

        /* Zonas de borde libre — permiten conectar desde cualquier punto del borde */
        .free-zone {
            position: absolute;
            cursor: crosshair;
            z-index: 20;
            opacity: 0;
            transition: opacity 0.15s;
        }

        .diagram-node:hover .free-zone {
            opacity: 1;
            background: rgba(13,110,253,0.15);
            border: 1px dashed rgba(13,110,253,0.5);
        }

        .free-zone:hover {
            background: rgba(13,110,253,0.35) !important;
            border-color: rgba(13,110,253,0.9) !important;
        }

        /* Resize handle vertical (solo alto) para activaciones */
        .resize-handle-vertical {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8px;
            cursor: s-resize;
            z-index: 15;
            background: rgba(13,110,253,0.4);
            border-radius: 0 0 3px 3px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .diagram-node:hover .resize-handle-vertical,
        .diagram-node.selected .resize-handle-vertical {
            opacity: 1;
        }

        /* Lifeline: ocupa todo el alto del nodo */
        /* Actor en diagrama de secuencia: muñeco + línea de vida */
        .uml-actor-lifeline {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .actor-lifeline-head {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
        }

        .uml-lifeline {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .lifeline-header {
            border: 2px solid currentColor;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
            background: rgba(13, 110, 253, 0.1);
            margin-bottom: 10px;
            color: #fff;
        }

        .lifeline-line {
            flex: 1;
            width: 2px;
            min-height: 400px;
            /* Línea vertical punteada UML */
            background: repeating-linear-gradient(
                to bottom,
                currentColor 0,
                currentColor 8px,
                transparent 8px,
                transparent 16px
            );
            position: relative;
            align-self: center;
        }

        .uml-activation {
            width: 100%;
            height: 100%;
            background: currentColor;
            opacity: 0.85;
            border: 1.5px solid currentColor;
            border-radius: 2px;
            box-sizing: border-box;
        }

        /* Diagrama de Actividades */
        .uml-activity {
            border: 2px solid currentColor;
            border-radius: 4px;
            padding: 10px 20px;
            min-width: 120px;
            text-align: center;
            background: rgba(13, 110, 253, 0.1);
            color: #fff;
        }

        /* Diamante de decisión: usa SVG inline para renderizado correcto */
        .uml-decision {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .decision-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 11px;
            text-align: center;
            color: #fff;
            z-index: 1;
            pointer-events: none;
            max-width: 80px;
            word-break: break-word;
            line-height: 1.2;
        }

        /* Fork/Join UML: barra horizontal ancha */
        .uml-fork {
            width: 100%;
            height: 8px;
            background: currentColor;
            border-radius: 3px;
            display: block;
        }

        /* Diagrama de Estados */
        .uml-state {
            border: 2px solid currentColor;
            border-radius: 8px;
            padding: 10px 20px;
            min-width: 120px;
            text-align: center;
            background: rgba(13, 110, 253, 0.1);
            color: #fff;
        }

        .uml-initial {
            width: 20px;
            height: 20px;
            background: currentColor;
            border-radius: 50%;
        }

        .uml-final {
            width: 30px;
            height: 30px;
            border: 2px solid currentColor;
            border-radius: 50%;
            position: relative;
        }

        .uml-final::after {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            width: 16px;
            height: 16px;
            background: currentColor;
            border-radius: 50%;
        }

        .uml-history {
            width: 30px;
            height: 30px;
            border: 2px solid currentColor;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
        }

        /* Diagrama de Componentes */
        .uml-component {
            border: 2px solid currentColor;
            border-radius: 4px;
            padding: 15px;
            min-width: 120px;
            text-align: center;
            background: rgba(13, 110, 253, 0.1);
            position: relative;
            color: #fff;
        }

        .component-icon {
            position: absolute;
            top: -10px;
            right: 10px;
            width: 20px;
            height: 20px;
            border: 2px solid currentColor;
            border-radius: 50%;
            background: inherit;
        }

        .uml-port {
            width: 10px;
            height: 10px;
            background: currentColor;
            border-radius: 50%;
        }

        /* Diagrama de Despliegue */
        .uml-node {
            border: 2px solid currentColor;
            border-radius: 8px;
            padding: 20px;
            min-width: 150px;
            min-height: 100px;
            background: rgba(13, 110, 253, 0.1);
            position: relative;
            color: #fff;
        }

        .node-header {
            position: absolute;
            top: -10px;
            left: 20px;
            background: inherit;
            padding: 0 10px;
            font-size: 12px;
            font-weight: bold;
            color: #fff;
        }

        .node-device {
            border: 2px solid currentColor;
            border-radius: 8px;
            padding: 20px;
            min-width: 150px;
            min-height: 120px;
            background: rgba(13, 110, 253, 0.1);
            position: relative;
            color: #fff;
        }

        .device-screen {
            width: 100%;
            height: 40px;
            border: 1px solid currentColor;
            margin-top: 10px;
        }

        .uml-artifact {
            border: 2px solid currentColor;
            border-radius: 4px;
            padding: 10px;
            min-width: 100px;
            text-align: center;
            background: rgba(13, 110, 253, 0.1);
            transform: rotate(5deg);
            color: #fff;
        }

        /* Nodo de Paquete UML */
        .diagram-node[data-type="package"] {
            overflow: visible !important;
            padding-top: 18px !important;
        }
        body.light-theme .diagram-node[data-type="package"] > div > div:first-child {
            color: var(--bg-deep, #0d0d1a) !important;
            background: #1a1a2e !important;
        }

        /* Línea de preview para conexiones */
        .connection-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }

        .connection-preview svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .connection-preview path {
            stroke: #0d6efd;
            stroke-width: 2;
            fill: none;
            opacity: 0.8;
        }

        /* Estilos para flechas en el canvas */
        .connection-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .connection-line path {
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }

        /* Flechas decorativas para el preview */
        .preview-arrow {
            fill: none;
            stroke: #0d6efd;
            stroke-width: 2;
        }

        /* Nodos con SVG */
        .node-svg-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 5px;
            min-height: 50px;
        }

        .node-svg-placeholder {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
        }

        .node-svg-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .node-svg-wrapper svg {
            width: 100%;
            height: 100%;
            stroke: currentColor;
            fill: none;
        }

        .node-text {
            text-align: center;
            font-size: 12px;
            word-wrap: break-word;
            max-width: 100%;
            padding: 0 5px;
            color: #fff;
        }

        .connection-point {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #0d6efd;
            border: 2px solid #fff;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: crosshair;
            z-index: 20;
            opacity: 0;
            transition: opacity 0.2s;
            
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .diagram-node:hover .connection-point {
            opacity: 0.8;
        }

        .connection-point:hover {
            opacity: 1 !important;
            transform: translate(-50%, -50%) scale(1.3);
            background: #0b5ed7;
        }

        .sidebar {
            background-color: #1a1a1a;
            border-right: 1px solid #333;
            height: calc(100vh - 50px);
            overflow: visible;
            width: 280px;
            transition: width 0.3s ease;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Botón de colapso sobresaliente del panel izquierdo */
        .sidebar-collapse-btn-outer {
            position: absolute;
            right: -36px;
            top: 0;
            background: #1a1a1a;
            border: 1px solid #444;
            border-left: none;
            color: #666;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 0 4px 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
            z-index: 45;
        }

        .sidebar-collapse-btn-outer:hover {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }

        .sidebar.collapsed {
            width: 40px !important;
        }

        .sidebar.collapsed .sidebar-tab-content {
            display: none !important;
        }

        .sidebar.collapsed .sidebar-tab-btn {
            display: none !important;
        }

        /* Tabs en el sidebar */
        /* Barra flotante de botones de colapso */
        .sidebar-tabs {
            display: flex;
            border-bottom: 1px solid #333;
            background: #1a1a1a;
            padding: 0;
            margin: 0;
            flex-shrink: 0;
            gap: 0;
            position: relative;
        }

        .sidebar-tab-btn {
            flex: 1;
            background: none;
            border: none;
            color: #888;
            padding: 12px 8px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            white-space: nowrap;
        }

        .sidebar-tab-btn:hover {
            color: #fff;
            background: rgba(13, 110, 253, 0.1);
        }

        .sidebar-tab-btn.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }

        .sidebar-tab-content {
            flex: 1;
            overflow-y: auto;
            display: none;
            padding: 12px;
        }

        .sidebar-tab-content.active {
            display: block;
        }

        .properties-panel {
            background-color: #1a1a1a;
            border-left: 1px solid #333;
            height: calc(100vh - 50px);
            overflow: visible;
            width: 300px;
            transition: width 0.3s ease;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Botón de colapso sobresaliente del panel derecho */
        .properties-collapse-btn-outer {
            position: absolute;
            left: -36px;
            top: 0;
            background: #1a1a1a;
            border: 1px solid #444;
            border-right: none;
            color: #666;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 4px 0 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
            z-index: 45;
        }

        .properties-collapse-btn-outer:hover {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }

        .properties-panel.collapsed {
            width: 40px !important;
        }

        .properties-panel.collapsed .properties-tab-content {
            display: none !important;
        }

        .properties-panel.collapsed .properties-tab-btn {
            display: none !important;
        }

        /* Tabs en el panel derecho */
        .properties-tabs {
            display: flex;
            border-bottom: 1px solid #333;
            background: #1a1a1a;
            padding: 0;
            margin: 0;
            flex-shrink: 0;
            gap: 0;
            position: relative;
        }

        .properties-tab-btn {
            flex: 1;
            background: none;
            border: none;
            color: #888;
            padding: 12px 8px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            white-space: nowrap;
        }

        .properties-tab-btn:hover {
            color: #fff;
            background: rgba(13, 110, 253, 0.1);
        }

        .properties-tab-btn.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }

        .properties-tab-content {
            flex: 1;
            overflow-y: auto;
            display: none;
            padding: 12px;
        }

        .properties-tab-content.active {
            display: block;
        }

        .shape-svg-container {
            width: 32px;
            height: 32px;
            margin: 0 auto 8px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .shape-svg-container svg {
            width: 100%;
            height: 100%;
            stroke: currentColor;
            fill: none;
            transition: all 0.2s;
        }

        .shape-svg-container svg *[stroke] {
            stroke: currentColor;
        }

        .shape-svg-container svg *[fill] {
            fill: currentColor;
        }

        .shape-item:hover .shape-svg-container svg {
            filter: drop-shadow(0 0 2px rgba(13, 110, 253, 0.5));
            transform: scale(1.1);
        }

        .shape-svg-container.fillable svg {
            fill: currentColor;
            stroke: none;
        }

        .shape-item {
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            cursor: grab;
            color: #fff;
            transition: all 0.2s;
        }

        .shape-item:hover {
            border-color: #0d6efd;
            background: #333;
            transform: translateY(-2px);
        }

        .shape-icon {
            font-size: 22px;
            margin-bottom: 6px;
            color: #0d6efd;
        }

        .form-control,
        .form-select {
            background: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            border-radius: 4px;
            padding: 8px 12px;
            width: 100%;
            margin-bottom: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            outline: none;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        }

        .form-control-color {
            padding: 4px;
            height: 40px;
        }

        label {
            color: #999;
            font-size: 12px;
            margin-bottom: 4px;
            display: block;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .minimap {
            display: none;
        }

        .diagram-preview-panel {
            background: #121212;
            border: 1px solid #2e2e2e;
            border-radius: 10px;
            padding: 12px;
        }

        .diagram-preview-box {
            min-height: 180px;
            background: #0f0f0f;
            border: 1px solid #333;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
            text-align: center;
            padding: 10px;
        }

        .preview-placeholder {
            opacity: 0.75;
            line-height: 1.5;
        }

        .diagram-preview-panel .badge {
            background: #343a40;
            color: #ced4da;
            font-size: 11px;
        }

        .form-check.form-switch .form-check-input {
            margin-top: 0.2rem;
        }

        .sidebar-tab-content .btn {
            min-height: 38px;
        }

        .sidebar-tab-content .form-check-label {
            color: #adb5bd;
        }

        .history-indicator {
            color: #0d6efd;
            font-size: 13px;
            margin-left: 16px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .auto-save-indicator {
            position: fixed;
            bottom: 20px;
            left: 300px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 20px;
            padding: 8px 16px;
            color: #4cc9f0;
            font-size: 13px;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .version-badge {
            background: #2a2a2a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: #999;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        #layersList {
            max-height: 200px;
            overflow-y: auto;
        }

        #layersList .p-2 {
            cursor: pointer;
            transition: background 0.2s;
        }

        #layersList .p-2:hover {
            background: #2a2a2a;
        }

        #noSelectionMessage {
            text-align: center;
            padding: 40px 20px;
        }

        #noSelectionMessage i {
            font-size: 48px;
            color: #444;
            margin-bottom: 15px;
        }

        #noSelectionMessage p {
            color: #666;
            font-size: 14px;
        }

        #themeToggle {
            background: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            margin-left: auto;
        }

        body.light-theme #themeToggle {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #000;
        }

        @media (max-width: 1200px) {
            .sidebar {
                width: 240px;
            }
            
            .properties-panel {
                width: 260px;
            }
            
            .minimap {
                right: 280px;
                width: 150px;
                height: 112px;
            }
        }

        @media (max-width: 768px) {
            .sidebar,
            .properties-panel {
                display: none;
            }
            
            .minimap {
                display: none;
            }
            
            .auto-save-indicator {
                left: 20px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .diagram-node {
            animation: fadeIn 0.2s ease-out;
        }

        /* ── Toast de validación ── */
        .validation-toast {
            position: fixed;
            top: 62px;
            left: 50%;
            transform: translateX(-50%);
            background: #dc3545;
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeIn 0.2s ease-out;
            pointer-events: none;
        }

        /* ── Shape items deshabilitados ── */
        .shape-item.disabled-shape {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Aviso de orden en panel de figuras ── */
        .usecase-order-hint {
            display: none;
            font-size: 11px;
            color: #fd7e14;
            margin-top: 6px;
            padding: 5px 8px;
            background: rgba(253,126,20,0.1);
            border-radius: 4px;
            border-left: 3px solid #fd7e14;
        }
        /* ── Diagrama de Objetos ── */
        .diagram-node[data-type="object"] .uml-class .class-name {
            text-decoration: underline;
        }
        .diagram-node[data-type="valor"] .uml-activity {
            font-size: 11px;
            padding: 4px 10px;
        }

        /* ── Diagrama de Comunicación ── */
        .diagram-node[data-type="message"],
        .diagram-node[data-type="link"] {
            background: rgba(13,110,253,0.08);
            border: 1.5px solid rgba(13,110,253,0.4);
            border-radius: 6px;
        }
        .diagram-node[data-type="message"] {
            font-size: 12px;
        }
        .diagram-node[data-type="link"] {
            font-size: 11px;
            font-style: italic;
        }

        /* ── Diagrama de Tiempo ── */
        .diagram-node[data-type="event"] {
            background: rgba(255,193,7,0.1);
            border: 1.5px dashed rgba(255,193,7,0.6);
            border-radius: 4px;
        }
        .diagram-node[data-type="constraint"] {
            background: rgba(108,117,125,0.1);
            border: 1px solid rgba(108,117,125,0.4);
            border-radius: 4px;
            font-size: 11px;
        }

        /* ── Interfaz requerida (Componentes) ── */
        .diagram-node[data-type="required"] {
            background: rgba(111,66,193,0.1);
            border: 1.5px dashed rgba(111,66,193,0.5);
            border-radius: 6px;
        }

    </style>
</head>
<body class="bg-black">
    <div class="toolbar d-flex align-items-center">
        <button class="btn-toolbar" onclick="volverAlDashboard()">
            <i class="bi bi-arrow-left"></i> Volver
        </button>
        
        <div class="vr mx-2 bg-secondary"></div>
        
        <button class="btn-toolbar" id="saveBtn" onclick="guardarDiagrama()">
            <i class="bi bi-save"></i> Guardar
        </button>
        <button class="btn-toolbar" id="saveVersionBtn" onclick="mostrarModalVersion()">
            <i class="bi bi-tag"></i> Versión
        </button>
        
        <div class="vr mx-2 bg-secondary"></div>
        
        <button class="btn-toolbar" id="undoBtn" disabled onclick="editor.undo()">
            <i class="bi bi-arrow-counterclockwise"></i>
        </button>
        <button class="btn-toolbar" id="redoBtn" disabled onclick="editor.redo()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        
        <div class="vr mx-2 bg-secondary"></div>
        
        <!-- Selector de flechas con preview -->
        <div class="arrow-selector-container" style="display:flex;align-items:center;gap:6px">
            <span class="text-gray small">Relación:</span>
            <select id="arrowSelector" class="arrow-selector" onchange="editor.setCurrentArrowType(this.value)">
                <!-- Se llenará dinámicamente según el tipo de diagrama -->
            </select>
            <!-- Preview inline de la flecha seleccionada -->
            <div id="arrowPreview"
                 title="Vista previa del tipo de relación seleccionado"
                 style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:6px;padding:2px 6px;display:flex;align-items:center;min-width:70px;height:28px;flex-shrink:0;cursor:help">
                <svg viewBox="0 0 60 20" width="60" height="20" style="overflow:visible;color:rgba(255,255,255,.7)">
                    <defs>
                        <marker id="previewArrowhead" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto">
                            <polygon points="0 0, 10 5, 0 10" fill="currentColor"/>
                        </marker>
                    </defs>
                    <line x1="5" y1="10" x2="45" y2="10" stroke="currentColor" stroke-width="2" marker-end="url(#previewArrowhead)"/>
                </svg>
            </div>
        </div>
        
        <div class="vr mx-2 bg-secondary"></div>

        <!-- ── Estilo de conector (línea) ─────────────────────── -->
        <div class="arrow-selector-container" title="Estilo de línea del conector">
            <span class="text-gray small">Línea:</span>
            <select id="connStyleSelect" class="arrow-selector" style="min-width:110px"
                    onchange="setConnLineStyle(this.value)">
                <option value="bezier">〜 Curva</option>
                <option value="straight">— Recta</option>
                <option value="orthogonal">⌐ Doblada</option>
                <option value="arc">⌢ Arco</option>
            </select>
        </div>

        <div class="vr mx-2 bg-secondary"></div>
        
        <button class="btn-toolbar" id="exportBtn" onclick="exportarDiagrama()">
            <i class="bi bi-download"></i> Exportar
        </button>
        <button class="btn-toolbar" id="importBtn" onclick="abrirImportar()">
            <i class="bi bi-upload"></i> Importar
        </button>

<!-- AI button moved to sidebar -->

<button id="deleteConnBtn" title="Eliminar conexión seleccionada (Del)"
            onclick="editor.deleteSelected&&editor.deleteSelected()"
            style="display:none;background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.4);color:#fca5a5;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:.8rem;align-items:center;gap:5px">
            <i class="bi bi-scissors me-1"></i>Quitar relación
        </button>
        <button class="btn-toolbar" id="themeToggle" title="Cambiar tema">
            <i class="bi bi-sun-fill"></i>
        </button>

        <!-- Zoom removido de toolbar: usa los botones del canvas (esquina inferior derecha) -->
    </div>

    <div class="auto-save-indicator" id="autoSaveIndicator">
        <i class="bi bi-check-circle-fill text-success"></i> Guardado
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-auto sidebar p-0" style="width: 280px;" id="leftSidebar">
                <!-- Botón de colapso sobresaliente -->
                <button class="sidebar-collapse-btn-outer" onclick="toggleSidebarCollapse()" title="Colapsar panel">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <!-- Tabs de navegación -->
                <div class="sidebar-tabs">
                    <button class="sidebar-tab-btn active" onclick="switchSidebarTab('diagrama')">
                        <i class="bi bi-diagram-3"></i> <span class="d-none d-lg-inline">Tipo</span>
                    </button>
                    <button class="sidebar-tab-btn" onclick="switchSidebarTab('figuras')">
                        <i class="bi bi-shapes"></i> <span class="d-none d-lg-inline">Figuras</span>
                    </button>
                    <button class="sidebar-tab-btn" onclick="switchSidebarTab('capas')">
                        <i class="bi bi-layers"></i> <span class="d-none d-lg-inline">Capas</span>
                    </button>
                </div>

                <!-- Contenido de pestañas -->
                <div class="sidebar-tab-content active" id="tab-diagrama">
                    <div class="mb-4">
                        <label class="text-gray text-uppercase small fw-bold mb-2">
                            <i class="bi bi-card-text"></i> Nombre del Diagrama
                        </label>
                        <div class="bg-dark border border-dark-gray rounded p-3 mb-3">
                            <strong class="text-white d-block" id="diagramaTitulo">
                                <?php echo $diagrama_data ? htmlspecialchars($diagrama_data['titulo']) : 'Sin título'; ?>
                            </strong>
                        </div>

                        <label class="text-gray text-uppercase small fw-bold mb-2">
                            <i class="bi bi-diagram-3"></i> Tipo de Diagrama
                        </label>
                        <input type="text" 
                               class="tipo-diagrama-info form-control bg-dark text-light border-dark" 
                               id="diagramTypeDisplay" 
                               value="<?php 
                                    $tipos = [
                                        'usecase'   => 'Casos de Uso',
                                        'class'     => 'Clases',
                                        'sequence'  => 'Secuencia',
                                        'activity'  => 'Actividades',
                                        'state'     => 'Máquina de Estado',
                                        'component' => 'Componentes',
                                        'deployment'=> 'Despliegue',
                                        'object'    => 'Objetos',
                                        'communication' => 'Comunicación',
                                        'timing'    => 'Tiempos',
                                        'package'   => 'Paquetes',
                                        'composite' => 'Estructura Compuesta',
                                        'profile'   => 'Perfiles',
                                        'overview'  => 'Descripción General',
                                    ];
                                    echo $tipos[$tipo_diagrama] ?? 'Casos de Uso';
                               ?>" 
                               readonly>
                        <small class="text-gray d-block mt-1">
                            <i class="bi bi-info-circle"></i> Tipo seleccionado en el dashboard
                        </small>

                        <div class="mt-3">
                            <span class="version-badge" id="versionInfo">
                                <i class="bi bi-tag"></i> Versión v<span id="versionNum"><?php echo $diagrama_data ? $diagrama_data['version'] : 1; ?></span>
                            </span>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="previewToggle" checked onchange="togglePreviewSection(this.checked)">
                            <label class="form-check-label text-gray" for="previewToggle">Mostrar vista general</label>
                        </div>

                        <!-- ── Side panel tabs: Vista General / IA ── -->
                        <div style="display:flex;gap:4px;margin-top:10px;margin-bottom:8px">
                            <button id="sideTabPreview" onclick="setSideTab('preview')"
                                style="flex:1;background:rgba(255,255,255,.18);border:none;color:#fff;border-radius:7px;padding:5px 0;font-size:.7rem;cursor:pointer;font-weight:600;transition:all .2s">
                                <i class="bi bi-map me-1"></i>Vista
                            </button>
                            <button id="sideTabChat" onclick="setSideTab('chat')"
                                style="flex:1;background:rgba(255,255,255,.07);border:none;color:rgba(255,255,255,.55);border-radius:7px;padding:5px 0;font-size:.7rem;cursor:pointer;transition:all .2s">
                                <i class="bi bi-robot me-1"></i>IA Chat
                            </button>
                        </div>

                        <!-- Vista general panel -->
                        <div id="sidePreviewPanel">
                        <div class="diagram-preview-panel" id="diagramPreviewSection">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-gray">Vista general</small>
                                <span class="badge bg-secondary" id="previewStatusText">Activo</span>
                            </div>
                            <div id="minimapCanvas" class="diagram-preview-box">
                                <div class="preview-placeholder">Vista previa del diagrama activada</div>
                            </div>
                        </div>
                        </div><!-- /sidePreviewPanel -->

                        <!-- IA Chat panel (sidebar) -->
                        <div id="sideAIPanel" style="display:none;flex-direction:column;overflow:hidden;flex:1">
                            <div id="aiMessages" style="flex:1;overflow-y:auto;max-height:280px;padding:4px 0;display:flex;flex-direction:column;gap:6px">
                                <div style="background:rgba(255,255,255,.08);border-radius:8px;padding:8px 10px;color:rgba(255,255,255,.75);font-size:.75rem;line-height:1.5">
                                    <i class="bi bi-robot me-1" style="color:var(--primary)"></i><strong>Asistente IA</strong><br>
                                    Haz preguntas sobre tu diagrama.<br>
                                    <span style="color:rgba(255,255,255,.45);font-size:.68rem">Doble clic en una flecha → añade punto de control.</span>
                                </div>
                            </div>
                            <div style="padding:6px 0;border-top:1px solid rgba(255,255,255,.1);margin-top:6px;flex-shrink:0">
                                <div style="display:flex;flex-wrap:wrap;gap:3px;margin-bottom:5px">
                                    <button onclick="sendAIMessage('Analiza mi diagrama completo','Analiza mi diagrama completo',true)" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:10px;padding:2px 7px;font-size:.65rem;cursor:pointer">Analizar</button>
                                    <button onclick="sendAIMessage('¿Qué elementos faltan?','¿Qué elementos faltan?',true)" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:10px;padding:2px 7px;font-size:.65rem;cursor:pointer">¿Falta algo?</button>
                                    <button onclick="sendAIMessage('¿Hay errores de notación UML?','¿Errores UML?',true)" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:10px;padding:2px 7px;font-size:.65rem;cursor:pointer">Errores</button>
                                </div>
                                <div style="display:flex;gap:4px">
                                    <textarea id="aiInput" rows="2"
                                        style="flex:1;background:rgba(0,0,0,.35);border:1px solid rgba(255,255,255,.15);border-radius:7px;color:#fff;padding:5px 7px;font-size:.73rem;resize:none;outline:none;transition:border-color .2s"
                                        placeholder="Escribe tu pregunta…"
                                        onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,.15)'"
                                        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendAIMessage()}"></textarea>
                                    <button onclick="sendAIMessage()" id="aiSendBtn"
                                        style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:7px;padding:6px 9px;cursor:pointer;align-self:flex-end;transition:opacity .2s"
                                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                </div>
                                <div style="text-align:center;margin-top:4px;font-size:.6rem;color:rgba(255,255,255,.25)">Powered by IA · /api/chat</div>
                            </div>
                        </div><!-- /sideAIPanel -->

                        <select class="d-none" id="diagramTypeSelect">
                            <optgroup label="── Estructurales ──────────────────">
                            <option value="class" <?php echo $tipo_diagrama == 'class' ? 'selected' : ''; ?>>Clases</option>
                            <option value="object" <?php echo $tipo_diagrama == 'object' ? 'selected' : ''; ?>>Objetos</option>
                            <option value="package" <?php echo $tipo_diagrama == 'package' ? 'selected' : ''; ?>>Paquetes</option>
                            <option value="composite" <?php echo $tipo_diagrama == 'composite' ? 'selected' : ''; ?>>Estructura Compuesta</option>
                            <option value="component" <?php echo $tipo_diagrama == 'component' ? 'selected' : ''; ?>>Componentes</option>
                            <option value="deployment" <?php echo $tipo_diagrama == 'deployment' ? 'selected' : ''; ?>>Despliegue</option>
                            <option value="profile" <?php echo $tipo_diagrama == 'profile' ? 'selected' : ''; ?>>Perfiles</option>
                            </optgroup>
                            <optgroup label="── Comportamiento ─────────────────">
                            <option value="usecase" <?php echo $tipo_diagrama == 'usecase' ? 'selected' : ''; ?>>Casos de Uso</option>
                            <option value="activity" <?php echo $tipo_diagrama == 'activity' ? 'selected' : ''; ?>>Actividades</option>
                            <option value="state" <?php echo $tipo_diagrama == 'state' ? 'selected' : ''; ?>>Máquina de Estado</option>
                            </optgroup>
                            <optgroup label="── Interacción ────────────────────">
                            <option value="sequence" <?php echo $tipo_diagrama == 'sequence' ? 'selected' : ''; ?>>Secuencia</option>
                            <option value="communication" <?php echo $tipo_diagrama == 'communication' ? 'selected' : ''; ?>>Comunicación</option>
                            <option value="timing" <?php echo $tipo_diagrama == 'timing' ? 'selected' : ''; ?>>Tiempos</option>
                            <option value="overview" <?php echo $tipo_diagrama == 'overview' ? 'selected' : ''; ?>>Descripción General</option>
                            </optgroup>
                            </select>
                    </div>
                </div>

                <div class="sidebar-tab-content" id="tab-figuras">
                    <div class="mb-3">
                        <label class="text-gray text-uppercase small fw-bold mb-2">
                            <i class="bi bi-shapes"></i> Figuras Disponibles
                        </label>
                        <div id="shapesContainer" class="d-grid gap-2" style="grid-template-columns: repeat(2, 1fr);">
                            <!-- Solo figuras, sin flechas -->
                        </div>
                        <div class="mt-2 small text-gray">
                            <i class="bi bi-info-circle"></i> Arrastra las figuras al lienzo
                        </div>
                        <div class="usecase-order-hint" id="usecaseOrderHint">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Primero agrega el <strong>Sistema</strong>.
                        </div>

                        <!-- Toggle de layout solo visible en Casos de Uso -->
                        <div id="usecaseLayoutToggle" style="display:none;" class="mt-3">
                            <label class="text-gray text-uppercase small fw-bold mb-2">
                                <i class="bi bi-grid-3x3-gap"></i> Layout de Casos de Uso
                            </label>
                            <div class="d-flex gap-2">
                                <button id="layoutVertBtn"
                                        class="btn btn-sm btn-primary flex-fill"
                                        onclick="editor.setUsecaseLayout('vertical')"
                                        title="Apilar casos de uso verticalmente">
                                    <i class="bi bi-layout-split"></i> Vertical
                                </button>
                                <button id="layoutHorizBtn"
                                        class="btn btn-sm btn-outline-secondary flex-fill"
                                        onclick="editor.setUsecaseLayout('horizontal')"
                                        title="Disponer casos de uso en fila horizontal">
                                    <i class="bi bi-layout-three-columns"></i> Horizontal
                                </button>
                            </div>
                            <small class="text-gray d-block mt-1">
                                <i class="bi bi-info-circle"></i> El Sistema se expande automáticamente
                            </small>
                        </div>
                    </div>
                </div>

                <div class="sidebar-tab-content" id="tab-capas">
                    <div class="mt-2">
                        <label class="text-gray text-uppercase small fw-bold mb-2">
                            <i class="bi bi-layers"></i> Capas y Elementos
                        </label>
                        <div class="bg-dark border border-dark-gray rounded p-2" id="layersList" style="max-height: 300px; overflow-y: auto;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col p-0">
                <div class="canvas-container" id="canvasContainer">
                    <div id="canvasViewport">
                        <div id="diagramCanvas">
                        </div>
                        <!-- Capa para preview de conexión -->
                        <div id="connectionPreview" class="connection-preview" style="display: none; position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;">
                            <svg style="position:absolute;top:0;left:0;width:100%;height:100%;overflow:visible;">
                                <path id="previewPath" d="" />
                                <path id="previewArrow" class="preview-arrow" d="" />
                            </svg>
                        </div>
                    </div>

                    <!-- Controles de zoom flotantes -->
                    <div class="zoom-controls">
                        <button onclick="editorZoom(0.15)" title="Acercar"><i class="bi bi-plus"></i></button>
                        <div class="zoom-level-label" id="zoomLevelFloat">100%</div>
                        <button onclick="editorZoom(-0.15)" title="Alejar"><i class="bi bi-dash"></i></button>
                        <button onclick="editorFitContent()" title="Ajustar contenido" style="font-size:12px;"><i class="bi bi-fullscreen"></i></button>
                    </div>

                    <!-- Mapa de vista previa movido al panel izquierdo -->
                </div>
            </div>

            <div class="col-auto properties-panel p-0" style="width: 300px;" id="rightPanel">
                <!-- Botón de colapso sobresaliente -->
                <button class="properties-collapse-btn-outer" onclick="togglePropertiesCollapse()" title="Colapsar panel">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <!-- Tabs de navegación -->
                <div class="properties-tabs">
                    <button class="properties-tab-btn active" onclick="switchPropertiesTab('element')">
                        <i class="bi bi-sliders2"></i> <span class="d-none d-lg-inline">Elemento</span>
                    </button>
                    <button class="properties-tab-btn" onclick="switchPropertiesTab('connection')">
                        <i class="bi bi-arrow-left-right"></i> <span class="d-none d-lg-inline">Relación</span>
                    </button>
                    <button class="properties-tab-btn" onclick="switchPropertiesTab('info')">
                        <i class="bi bi-info-circle"></i> <span class="d-none d-lg-inline">Info</span>
                    </button>
                </div>

                <!-- Contenido de pestañas -->
                <div class="properties-tab-content active" id="tab-element" style="padding: 12px;">
                    <div id="propertiesContent">
                        <div class="mb-3">
                            <label class="text-gray">Texto / Nombre</label>
                            <input type="text" class="form-control bg-dark text-light border-dark" id="nodeText" placeholder="Escribe el texto...">
                        </div>

                        <div class="mb-3">
                            <label class="text-gray">Tipo de elemento</label>
                            <div class="form-control bg-dark text-light border-dark d-flex align-items-center" style="cursor:default;opacity:.75;">
                                <i class="bi bi-tag me-2 text-primary"></i>
                                <span id="nodeTypeDisplay">—</span>
                            </div>
                            <small class="text-gray">El tipo no se puede cambiar una vez creado</small>
                        </div>

                        <div class="mb-3">
                            <label class="text-gray">Color de fondo</label>
                            <input type="color" class="form-control form-control-color bg-dark border-dark" id="nodeColor" value="#0d6efd">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="text-gray">Ancho (px)</label>
                                <input type="number" class="form-control bg-dark text-light border-dark" id="nodeWidth" placeholder="Ancho">
                            </div>
                            <div class="col">
                                <label class="text-gray">Alto (px)</label>
                                <input type="number" class="form-control bg-dark text-light border-dark" id="nodeHeight" placeholder="Alto">
                            </div>
                        </div>

                        <div class="mb-3" id="attributesSection" style="display: none;">
                            <label class="text-gray">
                                <i class="bi bi-list-ul me-1"></i>Atributos
                                <small class="d-block text-gray mt-1" style="font-style:italic;">Una línea por atributo, ej: - edad : int</small>
                            </label>
                            <textarea class="form-control bg-dark text-light border-dark" id="nodeAttributes" rows="3" placeholder="- nombre : String&#10;- edad : int"></textarea>
                        </div>

                        <div class="mb-3" id="methodsSection" style="display: none;">
                            <label class="text-gray">
                                <i class="bi bi-braces me-1"></i>Métodos
                                <small class="d-block text-gray mt-1" style="font-style:italic;">Una línea por método, ej: + getNombre()</small>
                            </label>
                            <textarea class="form-control bg-dark text-light border-dark" id="nodeMethods" rows="3" placeholder="+ getNombre() : String&#10;+ setEdad(e:int)"></textarea>
                        </div>

                        <div class="mb-3" id="seqLabelSection" style="display: none;">
                            <label class="text-gray"><i class="bi bi-tag me-1"></i>Etiqueta del mensaje</label>
                            <input type="text" class="form-control bg-dark text-light border-dark" id="nodeSeqLabel" placeholder="ej: login()">
                        </div>
                        
                        <button class="btn btn-primary w-100 mb-2" id="applyPropertiesBtn" onclick="aplicarPropiedades()">
                            <i class="bi bi-check-lg"></i> Aplicar cambios
                        </button>
                        
                        <hr class="border-dark-gray">
                        
                        <button class="btn btn-outline-danger w-100" id="deleteElementBtn" onclick="eliminarSeleccionado()">
                            <i class="bi bi-trash"></i> Eliminar elemento
                        </button>

                        <div id="noSelectionMessage" class="text-center text-gray mt-4">
                            <i class="bi bi-arrow-up-left-circle" style="font-size: 48px;"></i>
                            <p class="mt-2">Selecciona un elemento para editar</p>
                        </div>
                    </div>
                </div>

                <div class="properties-tab-content" id="tab-connection" style="padding: 12px;">
                    <div id="connectionPropertiesContent">
                        <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded" style="background:#1e3a1e;border:1px solid #2d5a2d;">
                            <i class="bi bi-arrow-right-circle text-success"></i>
                            <span class="small text-light fw-semibold">Flecha seleccionada</span>
                        </div>

                        <div class="mb-2">
                            <label class="text-gray" style="font-size:11px;">Origen → Destino</label>
                            <div class="form-control bg-dark text-light border-dark small" id="connInfo" style="cursor:default;opacity:.75;font-size:12px;">—</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-gray">Etiqueta de la flecha</label>
                            <input type="text" class="form-control bg-dark text-light border-dark" id="connLabel"
                                   placeholder="ej: 1..*, usa, crea, login()…"
                                   onkeydown="if(event.key==='Enter'){editor.aplicarPropiedades();}">
                            <small class="text-gray">Escribe y presiona Enter o «Aplicar»</small>
                        </div>

                        <div class="mb-3">
                            <label class="text-gray small">Posición del texto (X, Y)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control bg-dark text-light border-dark form-control-sm" id="connLabelOffsetX"
                                           placeholder="X" onkeydown="if(event.key==='Enter'){editor.aplicarPropiedades();}">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control bg-dark text-light border-dark form-control-sm" id="connLabelOffsetY"
                                           placeholder="Y" onkeydown="if(event.key==='Enter'){editor.aplicarPropiedades();}">
                                </div>
                            </div>
                            <small class="text-gray d-block mt-1"><i class="bi bi-cursor-move"></i> O arrastra el texto sobre la flecha</small>
                        </div>

                        <div class="mb-3">
                            <label class="text-gray">Tipo de relación</label>
                            <select class="form-select bg-dark text-light border-dark" id="connType">
                                <optgroup label="── General ──">
                                    <option value="asociacion">Asociación</option>
                                    <option value="dependencia">Dependencia</option>
                                    <option value="enlace">Enlace (sin flecha)</option>
                                </optgroup>
                                <optgroup label="── Clases ──">
                                    <option value="herencia">Herencia / Generalización</option>
                                    <option value="generalizacion">Generalización</option>
                                    <option value="realizacion">Realización</option>
                                    <option value="agregacion">Agregación</option>
                                    <option value="composicion">Composición</option>
                                </optgroup>
                                <optgroup label="── Casos de Uso ──">
                                    <option value="include">«include»</option>
                                    <option value="extend">«extend»</option>
                                </optgroup>
                                <optgroup label="── Secuencia ──">
                                    <option value="mensaje-sincrono">Mensaje síncrono</option>
                                    <option value="mensaje-asincrono">Mensaje asíncrono</option>
                                    <option value="mensaje-retorno">Mensaje de retorno</option>
                                </optgroup>
                                <optgroup label="── Actividad / Estado ──">
                                    <option value="flujo">Flujo</option>
                                    <option value="transicion">Transición</option>
                                </optgroup>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100 mb-2" onclick="editor.aplicarPropiedades()">
                            <i class="bi bi-check-lg"></i> Aplicar cambios
                        </button>

                        <hr class="border-dark-gray">

                        <div class="row gap-2 mb-3">
                            <div class="col-sm-6">
                                <button class="btn btn-primary w-100 btn-sm" onclick="editor.aplicarPropiedades()">
                                    <i class="bi bi-check-lg"></i> Aplicar
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-danger w-100 btn-sm" onclick="editor.deleteSelected()">
                                    <i class="bi bi-trash"></i> Borrar (Del)
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info small py-2 mb-0">
                            <i class="bi bi-cursor-fill"></i> <strong>Click en flecha</strong> para seleccionar<br>
                            <i class="bi bi-keyboard"></i> <strong>Delete</strong> para borrar<br>
                            <i class="bi bi-pencil"></i> <strong>Enter</strong> en etiqueta para guardar
                        </div>

                        <div id="noConnectionMessage" class="text-center text-gray mt-4">
                            <i class="bi bi-arrow-left-right"></i>
                            <p class="mt-2">Selecciona una relación para editar</p>
                        </div>
                    </div>
                </div>

                <div class="properties-tab-content" id="tab-info" style="padding: 12px;">
                    <h6 class="text-white mb-3"><i class="bi bi-info-circle"></i> Información</h6>
                    <div class="small text-gray">
                        <p><strong>Versión:</strong> <span id="versionNumInfo"><?php echo $diagrama_data ? $diagrama_data['version'] : 1; ?></span></p>
                        <p><strong>Tipo:</strong> <span id="typeInfo"><?php echo htmlspecialchars($tipo_diagrama); ?></span></p>
                        <p><strong>Total de elementos:</strong> <span id="totalElementsInfo">0</span></p>
                        <p><strong>Total de relaciones:</strong> <span id="totalConnectionsInfo">0</span></p>
                        <hr class="border-dark-gray">
                        <p class="mb-0"><i class="bi bi-keyboard me-1"></i> <strong>Atajos:</strong></p>
                        <ul class="small mt-2 mb-0">
                            <li><kbd style="background:#2a2a2a;padding:2px 4px;border-radius:3px;font-size:10px;">Ctrl+Z</kbd> Deshacer</li>
                            <li><kbd style="background:#2a2a2a;padding:2px 4px;border-radius:3px;font-size:10px;">Ctrl+Y</kbd> Rehacer</li>
                            <li><kbd style="background:#2a2a2a;padding:2px 4px;border-radius:3px;font-size:10px;">Supr</kbd> Eliminar</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="helpModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-light border-dark-gray">
                <div class="modal-header border-dark-gray">
                    <h5 class="modal-title"><i class="bi bi-question-circle"></i> Ayuda del Editor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Atajos de teclado:</h6>
                    <ul class="list-unstyled">
                        <li><kbd class="bg-dark text-light border border-secondary">Ctrl + Z</kbd> Deshacer</li>
                        <li><kbd class="bg-dark text-light border border-secondary">Ctrl + Y</kbd> Rehacer</li>
                        <li><kbd class="bg-dark text-light border border-secondary">Supr</kbd> Eliminar seleccionado</li>
                        <li><kbd class="bg-dark text-light border border-secondary">Ctrl + A</kbd> Seleccionar todo</li>
                        <li><kbd class="bg-dark text-light border border-secondary">Ctrl + S</kbd> Guardar</li>
                    </ul>
                    
                    <h6 class="mt-3">Cómo usar las flechas:</h6>
                    <p>1. Selecciona el tipo de flecha en el ComboBox superior</p>
                    <p>2. Arrastra desde un punto verde de conexión hacia otro elemento</p>
                    <p>3. Verás una previsualización en tiempo real de la flecha</p>
                    <p>4. Suelta sobre el punto verde del elemento destino para crear la conexión</p>
                    
                    <h6 class="mt-3">Tipos de flechas UML:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Asociación:</strong> Línea sólida con punta abierta →</li>
                        <li><strong>Herencia/Generalización:</strong> Línea sólida con punta triangular vacía ─▷</li>
                        <li><strong>Dependencia:</strong> Línea punteada con punta abierta - - →</li>
                        <li><strong>Agregación:</strong> Línea sólida con diamante vacío ◇──</li>
                        <li><strong>Composición:</strong> Línea sólida con diamante relleno ◆──</li>
                        <li><strong>Realización:</strong> Línea punteada con punta triangular vacía - - ▷</li>
                        <li><strong>Include/Extend:</strong> Línea punteada con estereotipo «include/extend»</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="versionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light border-dark-gray">
                <div class="modal-header border-dark-gray">
                    <h5 class="modal-title"><i class="bi bi-tag"></i> Guardar Versión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-gray">Comentario de la versión</label>
                        <textarea class="form-control bg-dark text-light border-dark" id="versionComentario" rows="3" 
                                  placeholder="Describe los cambios realizados..."></textarea>
                    </div>
                    <div class="small text-gray">
                        <i class="bi bi-info-circle"></i> Las versiones te permiten volver a estados anteriores del diagrama.
                    </div>
                </div>
                <div class="modal-footer border-dark-gray">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarVersion()">Guardar Versión</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Importar -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light border-dark-gray">
                <div class="modal-header border-dark-gray" style="background:linear-gradient(135deg,#198754,#0d6efd)">
                    <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Importar Diagrama</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Sube un archivo para importarlo como diagrama.</p>

                    <!-- Zona de drop -->
                    <div id="dropZone" class="border border-secondary border-dashed rounded p-4 text-center mb-3"
                         style="cursor:pointer;transition:all .2s;border-style:dashed!important"
                         onclick="document.getElementById('importFileInput').click()"
                         ondragover="event.preventDefault();this.style.borderColor='#667eea'"
                         ondragleave="this.style.borderColor=''"
                         ondrop="handleImportDrop(event)">
                        <i class="bi bi-cloud-upload" style="font-size:2.5rem;color:#667eea"></i>
                        <p class="mt-2 mb-1 fw-500">Arrastra un archivo aquí o haz clic</p>
                        <small class="text-muted">JSON, SVG · Máx 5 MB</small>
                        <input type="file" id="importFileInput" accept=".json,.svg" class="d-none" onchange="procesarArchivoImportado(this.files[0])">
                    </div>

                    <!-- Info del archivo seleccionado -->
                    <div id="importFileInfo" class="d-none alert alert-secondary small py-2 mb-3">
                        <i class="bi bi-file-earmark me-1"></i>
                        <span id="importFileName"></span>
                        <span class="ms-2 text-muted" id="importFileSize"></span>
                    </div>

                    <!-- Preview / estado -->
                    <div id="importPreview" class="d-none">
                        <div class="border border-secondary rounded p-3 mb-3" style="background:#111">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong id="importPreviewTitulo" class="text-light">—</strong>
                                <span class="badge" id="importPreviewTipo" style="background:linear-gradient(135deg,#667eea,#764ba2)">—</span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-diagram-3 me-1"></i><span id="importPreviewNodes">0</span> elementos ·
                                <i class="bi bi-arrow-left-right me-1"></i><span id="importPreviewConns">0</span> conexiones
                            </small>
                        </div>
                        <div class="alert alert-warning small py-2 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Esto <strong>reemplazará</strong> el diagrama actual en el canvas. Guarda antes si necesitas conservarlo.
                        </div>
                    </div>

                    <div id="importError" class="d-none alert alert-danger small py-2"></div>
                </div>
                <div class="modal-footer border-dark-gray justify-content-end gap-2">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" id="btnConfirmarImport" disabled onclick="confirmarImportacion()">
                        <i class="bi bi-check-lg me-1"></i>Importar al Editor
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Exportar -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light border-dark-gray">
                <div class="modal-header border-dark-gray" style="background:linear-gradient(135deg,#667eea,#764ba2)">
                    <h5 class="modal-title"><i class="bi bi-download me-2"></i>Exportar Diagrama</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Elige el formato en que deseas exportar tu diagrama.</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100 py-3 d-flex flex-column align-items-center gap-2"
                                    onclick="exportarJSON(); bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide()">
                                <i class="bi bi-filetype-json" style="font-size:2rem;color:#f7c948"></i>
                                <span class="fw-600">JSON</span>
                                <small class="text-muted">Datos completos, re-importable</small>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100 py-3 d-flex flex-column align-items-center gap-2"
                                    onclick="exportarSVG(); bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide()">
                                <i class="bi bi-filetype-svg" style="font-size:2rem;color:#4fc3f7"></i>
                                <span class="fw-600">SVG</span>
                                <small class="text-muted">Vector escalable</small>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100 py-3 d-flex flex-column align-items-center gap-2"
                                    onclick="exportarPNG(); bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide()">
                                <i class="bi bi-filetype-png" style="font-size:2rem;color:#a5d6a7"></i>
                                <span class="fw-600">PNG</span>
                                <small class="text-muted">Imagen de alta resolución</small>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100 py-3 d-flex flex-column align-items-center gap-2"
                                    onclick="exportarPDF(); bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide()">
                                <i class="bi bi-filetype-pdf" style="font-size:2rem;color:#ef9a9a"></i>
                                <span class="fw-600">PDF</span>
                                <small class="text-muted">Para imprimir o compartir</small>
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-secondary mt-4 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>JSON</strong> es el único formato que preserva la estructura para re-editar.
                        SVG, PNG y PDF son para visualización/impresión.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= Assets::bootstrapJs() ?>"></script>
    <script>window.BASE_URL = "<?= BASE_URL ?>";</script>
    <script src="<?= Assets::url('js/user-theme.js') ?>"></script>
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script>
        let diagramaId = <?php echo !empty($diagrama_id) ? (int)$diagrama_id : (!empty($diagrama_data['id']) ? (int)$diagrama_data['id'] : 'null'); ?>;
        const tipoDiagrama = '<?php echo htmlspecialchars($tipo_diagrama); ?>';
        // Proyecto de contexto — si se abre desde un proyecto, el diagrama se liga automáticamente
        const PROYECTO_CONTEXTO = <?php echo !empty($proyecto_id) ? (int)$proyecto_id : 'null'; ?>;

        // Datos del diagrama pre-cargados por PHP — evita un fetch extra al abrir
        const _datosPrecargados = <?php
            if ($diagrama_data && !empty($diagrama_data['contenido'])) {
                $cont = $diagrama_data['contenido'];
                // Normalizar: si es string, decodificar
                if (is_string($cont)) $cont = json_decode($cont, true) ?: [];
                echo json_encode([
                    'titulo'      => $diagrama_data['titulo'],
                    'tipo'        => $diagrama_data['tipo_diagrama'],
                    'version'     => $diagrama_data['version'],
                    'nodes'       => $cont['nodes']       ?? [],
                    'connections' => $cont['connections']  ?? [],
                    'diagramType' => $cont['diagramType']  ?? $diagrama_data['tipo_diagrama'],
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo 'null';
            }
        ?>;
        
        class HistoryManager {
            constructor() {
                this.undoStack = [];
                this.redoStack = [];
                this.maxStackSize = 50;
                this.updateUI();
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
                
                return this.undoStack.length > 0 ? 
                    JSON.parse(JSON.stringify(this.undoStack[this.undoStack.length - 1])) : null;
            }

            redo() {
                if (this.redoStack.length === 0) return null;
                
                const state = this.redoStack.pop();
                this.undoStack.push(state);
                this.updateUI();
                
                return JSON.parse(JSON.stringify(state));
            }

            canUndo() {
                return this.undoStack.length > 0;
            }

            canRedo() {
                return this.redoStack.length > 0;
            }

            updateUI() {
                const undoBtn = document.getElementById('undoBtn');
                const redoBtn = document.getElementById('redoBtn');
                if (undoBtn) undoBtn.disabled = !this.canUndo();
                if (redoBtn) redoBtn.disabled = !this.canRedo();
            }
        }

        class DiagramEditor {
            constructor() {
                this.nodes = [];
                this.connections = [];
                this.selectedNode = null;
                this.selectedConnection = null;
                this.diagramType = tipoDiagrama;
                this.history = new HistoryManager();
                this.nodeIdCounter = 1;
                this.dragging = false;
                this.dragStartX = 0;
                this.dragStartY = 0;
                this.resizing = false;
                this.resizeStartX = 0;
                this.resizeStartY = 0;
                this.resizeStartWidth = 0;
                this.resizeStartHeight = 0;
                this.connecting = false;
                this.connectionStart = null;
                this.currentArrowType  = 'asociacion';
                this.currentLineStyle  = 'bezier'; // bezier | straight | orthogonal | arc
                this.usecaseLayout    = 'vertical';   // 'vertical' | 'horizontal'
                this.activationParents = {};           // activationId -> lifelineId
                this.unsavedChanges = false;
                this.previewElement = document.getElementById('connectionPreview');
                this.previewPath = document.getElementById('previewPath');
                this.minDistance = 20;

                // Definir figuras por tipo de diagrama (solo elementos, sin relaciones)
                this.shapesByType = {
                    usecase: [
                        { type: 'actor', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeCasosdeUso/actor.svg', label: 'Actor', class: '' },
                        { type: 'usecase', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeCasosdeUso/caso-uso.svg', label: 'Caso de Uso', class: '' },
                        { type: 'system', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeCasosdeUso/sistema.svg', label: 'Sistema', class: '' }
                    ],
                    class: [
                        { type: 'class', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/clase.svg', label: 'Clase', class: '' },
                        { type: 'abstract', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/clase-abstracta.svg', label: 'Clase Abstracta', class: '' },
                        { type: 'interface', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/interfaz.svg', label: 'Interfaz', class: '' },
                        { type: 'enum', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/enumeracion.svg', label: 'Enumeración', class: '' }
                    ],
                    sequence: [
                        { type: 'actor', icon: '<?= BASE_URL ?>/public/assets/img/DiagramasdeInteraccion/actor.svg', label: 'Actor', class: '' },
                        { type: 'lifeline', icon: '<?= BASE_URL ?>/public/assets/img/DiagramasdeInteraccion/objeto.svg', label: 'Línea de Vida', class: '' },
                        { type: 'activation', icon: '<?= BASE_URL ?>/public/assets/img/DiagramasdeInteraccion/activacion.svg', label: 'Activación', class: '' }
                    ],
                    activity: [
                        { type: 'start', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/inicio.svg', label: 'Inicio', class: '' },
                        { type: 'activity', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/actividad.svg', label: 'Actividad', class: '' },
                        { type: 'decision', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/decision.svg', label: 'Decisión', class: '' },
                        { type: 'fork', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/bifurcacion.svg', label: 'Bifurcación', class: '' },
                        { type: 'union', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/union.svg', label: 'Unión', class: '' },
                        { type: 'end', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/fin.svg', label: 'Fin', class: '' }
                    ],
                    state: [
                        { type: 'initial', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeEstados/estado-inicial.svg', label: 'Estado Inicial', class: '' },
                        { type: 'state', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeEstados/estado.svg', label: 'Estado', class: '' },
                        { type: 'final', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeEstados/estado-final.svg', label: 'Estado Final', class: '' },
                        { type: 'decision', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeEstados/decision.svg', label: 'Decisión', class: '' },
                        { type: 'history', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeEstados/historia.svg', label: 'Historia', class: '' }
                    ],
                    component: [
                        { type: 'component', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComponentes/componente.svg', label: 'Componente', class: '' },
                        { type: 'interface', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComponentes/interfaz.svg', label: 'Interfaz', class: '' },
                        { type: 'required', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComponentes/interfaz-requerida.svg', label: 'Interfaz Requerida', class: '' },
                        { type: 'port', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComponentes/puerto.svg', label: 'Puerto', class: '' }
                    ],
                    deployment: [
                        { type: 'node', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeDespliegue/nodo.svg', label: 'Nodo', class: '' },
                        { type: 'device', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeDespliegue/dispositivo.svg', label: 'Dispositivo', class: '' },
                        { type: 'artifact', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeDespliegue/artefacto.svg', label: 'Artefacto', class: '' },
                        { type: 'interface', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeDespliegue/interfaz.svg', label: 'Interfaz', class: '' }
                    ],
                    object: [
                        { type: 'object', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeObjetos/objeto.svg', label: 'Objeto', class: '' },
                        { type: 'valor', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeObjetos/valor.svg', label: 'Valor', class: '' }
                    ],
                    communication: [
                        { type: 'object', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComunicacion/objeto.svg', label: 'Objeto', class: '' },
                        { type: 'message', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComunicacion/mensaje.svg', label: 'Mensaje', class: '' },
                        { type: 'link', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComunicacion/enlace.svg', label: 'Enlace', class: '' }
                    ],
                    timing: [
                        { type: 'lifeline-timing', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeTiempo/linea-vida.svg', label: 'Línea de Vida', class: '' },
                        { type: 'state-timing', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeTiempo/estado.svg', label: 'Estado', class: '' },
                        { type: 'event-timing', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeTiempo/evento.svg', label: 'Evento', class: '' },
                        { type: 'timeline', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeTiempo/linea-tiempo.svg', label: 'Línea de tiempo', class: '' },
                        { type: 'constraint-timing', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeTiempo/restriccion.svg', label: 'Restricción', class: '' }
                    ],
                    package: [
                        { type: 'package', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadePaquetes/paquete.svg', label: 'Paquete', class: '' },
                        { type: 'class', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/clase.svg', label: 'Clase / Elemento', class: '' },
                        { type: 'interface', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/interfaz.svg', label: 'Interfaz', class: '' }
                    ],
                    composite: [
                        { type: 'class', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/clase.svg', label: 'Clase/Componente', class: '' },
                        { type: 'port', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComponentes/puerto.svg', label: 'Puerto', class: '' },
                        { type: 'part', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeComponentes/componente.svg', label: 'Parte', class: '' }
                    ],
                    profile: [
                        { type: 'stereotype', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/clase.svg', label: 'Estereotipo', class: '' },
                        { type: 'metaclass', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/clase-abstracta.svg', label: 'Metaclase', class: '' },
                        { type: 'enum', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeClases/enumeracion.svg', label: 'Enumeración', class: '' }
                    ],
                    overview: [
                        { type: 'start', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/inicio.svg', label: 'Inicio', class: '' },
                        { type: 'interaction', icon: '<?= BASE_URL ?>/public/assets/img/DiagramasdeInteraccion/objeto.svg', label: 'Interacción', class: '' },
                        { type: 'decision', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/decision.svg', label: 'Decisión', class: '' },
                        { type: 'fork', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/bifurcacion.svg', label: 'Bifurcación', class: '' },
                        { type: 'end', icon: '<?= BASE_URL ?>/public/assets/img/DiagramadeActividades/fin.svg', label: 'Fin', class: '' }
                    ]
                };

                // Definir las flechas permitidas por tipo de diagrama
                this.allowedArrowsByType = {
                    usecase: [
                        { value: 'asociacion', label: 'Asociación →', preview: 'arrowhead' },
                        { value: 'include', label: '«include» - - →', preview: 'arrowhead' },
                        { value: 'extend', label: '«extend» - - →', preview: 'arrowhead' },
                        { value: 'generalizacion', label: 'Generalización ─▷', preview: 'emptyArrowhead' }
                    ],
                    class: [
                        { value: 'asociacion', label: 'Asociación →', preview: 'arrowhead' },
                        { value: 'dependencia', label: 'Dependencia - - →', preview: 'arrowhead' },
                        { value: 'herencia', label: 'Herencia ─▷', preview: 'emptyArrowhead' },
                        { value: 'agregacion', label: 'Agregación ◇──', preview: 'emptyDiamond' },
                        { value: 'composicion', label: 'Composición ◆──', preview: 'diamond' },
                        { value: 'realizacion', label: 'Realización - - ▷', preview: 'emptyArrowhead' }
                    ],
                    sequence: [
                        { value: 'mensaje-sincrono', label: 'Mensaje Síncrono →', preview: 'arrowhead' },
                        { value: 'mensaje-asincrono', label: 'Mensaje Asíncrono - - →', preview: 'arrowhead' },
                        { value: 'mensaje-retorno', label: 'Retorno ←', preview: 'arrowhead', reverse: true }
                    ],
                    activity: [
                        { value: 'flujo', label: 'Flujo →', preview: 'arrowhead' }
                    ],
                    state: [
                        { value: 'transicion', label: 'Transición →', preview: 'arrowhead' }
                    ],
                    component: [
                        { value: 'dependencia', label: 'Dependencia - - →', preview: 'arrowhead' },
                        { value: 'asociacion', label: 'Asociación →', preview: 'arrowhead' }
                    ],
                    deployment: [
                        { value: 'asociacion', label: 'Asociación →', preview: 'arrowhead' }
                    ],
                    object: [
                        { value: 'enlace', label: 'Enlace —', preview: 'arrowhead', noArrow: true }
                    ],
                    communication: [
                        { value: 'mensaje', label: 'Mensaje →', preview: 'arrowhead' }
                    ],
                    timing: [
                        { value: 'transicion', label: 'Transición →', preview: 'arrowhead' }
                    ],
                    package: [
                        { value: 'dependencia', label: 'Dependencia - - →', preview: 'arrowhead' },
                        { value: 'importacion', label: '«import» - - →', preview: 'arrowhead' },
                        { value: 'acceso', label: '«access» - - →', preview: 'arrowhead' },
                        { value: 'merge', label: '«merge» - - →', preview: 'arrowhead' }
                    ],
                    composite: [
                        { value: 'asociacion', label: 'Asociación →', preview: 'arrowhead' },
                        { value: 'delegacion', label: 'Delegación →', preview: 'arrowhead' }
                    ],
                    profile: [
                        { value: 'extension', label: 'Extensión →', preview: 'arrowhead' },
                        { value: 'dependencia', label: 'Dependencia - - →', preview: 'arrowhead' }
                    ],
                    overview: [
                        { value: 'flujo', label: 'Flujo →', preview: 'arrowhead' },
                        { value: 'referencia', label: 'Referencia - - →', preview: 'arrowhead' }
                    ]
                };

                // Definir los estilos de las flechas con marcadores mejorados
                this.arrowStyles = {
                    asociacion: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#arrowhead)'
                    },
                    herencia: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#emptyArrowhead)'
                    },
                    generalizacion: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#emptyArrowhead)'
                    },
                    dependencia: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '5,5',
                        'marker-end': 'url(#arrowhead)'
                    },
                    agregacion: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#emptyDiamond)'
                    },
                    composicion: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#diamond)'
                    },
                    realizacion: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '5,5',
                        'marker-end': 'url(#emptyArrowhead)'
                    },
                    include: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '5,5',
                        'marker-end': 'url(#arrowhead)'
                    },
                    extend: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '5,5',
                        'marker-end': 'url(#arrowhead)'
                    },
                    flujo: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#arrowhead)'
                    },
                    transicion: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#arrowhead)'
                    },
                    'mensaje-sincrono': {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '',
                        'marker-end': 'url(#arrowhead)'
                    },
                    'mensaje-asincrono': {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '5,5',
                        'marker-end': 'url(#arrowhead)'
                    },
                    'mensaje-retorno': {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': '5,5',
                        'marker-start': 'url(#arrowhead)'
                    },
                    enlace: {
                        stroke: 'currentColor',
                        'stroke-width': 2,
                        'stroke-dasharray': ''
                    }
                };

                this.init();
                
                // No crear figuras automáticas - solo grid vacío
            }

            init() {
                this.loadEventListeners();
                this.loadShapesForType(this.diagramType);
                this.loadArrowsForType(this.diagramType);
                this.updateLayersList();
                this.pushToHistory();

                setInterval(() => {
                    if (this.unsavedChanges) {
                        this.guardar();
                    }
                }, 30000);
            }

            loadEventListeners() {
                const canvas = document.getElementById('diagramCanvas');
                if (!canvas) return;
                
                canvas.addEventListener('dragover', (e) => e.preventDefault());
                canvas.addEventListener('drop', (e) => this.handleDrop(e));
                canvas.addEventListener('mousedown', (e) => this.handleCanvasMouseDown(e));
                canvas.addEventListener('mousemove', (e) => this.handleMouseMove(e));
                canvas.addEventListener('mouseup', (e) => this.handleMouseUp(e));
                canvas.addEventListener('click', (e) => this.handleCanvasClick(e));

                // Touch events para dispositivos móviles
                canvas.addEventListener('touchstart', (e) => this.handleTouchStart(e));
                canvas.addEventListener('touchmove', (e) => this.handleTouchMove(e));
                canvas.addEventListener('touchend', (e) => this.handleTouchEnd(e));

                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 'z') {
                        e.preventDefault();
                        this.undo();
                    } else if (e.ctrlKey && e.key === 'y') {
                        e.preventDefault();
                        this.redo();
                    } else if (e.key === 'Delete') {
                        this.deleteSelected();
                    } else if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        this.guardar();
                    }
                });

                window.addEventListener('beforeunload', (e) => {
                    if (this.unsavedChanges) {
                        e.preventDefault();
                        e.returnValue = 'Hay cambios sin guardar. ¿Estás seguro de salir?';
                    }
                });
            }

            loadArrowsForType(type) {
                const selector = document.getElementById('arrowSelector');
                if (!selector) return;
                
                selector.innerHTML = '';
                
                const arrows = this.allowedArrowsByType[type] || [];
                arrows.forEach(arrow => {
                    const option = document.createElement('option');
                    option.value = arrow.value;
                    option.textContent = arrow.label;
                    selector.appendChild(option);
                });
                
                if (arrows.length > 0) {
                    this.currentArrowType = arrows[0].value;
                    this.updateArrowPreview();
                }
            }

            setCurrentArrowType(type) {
                this.currentArrowType = type;
                this.updateArrowPreview();
            }

            updateArrowPreview() {
                const preview = document.getElementById('arrowPreview');
                if (!preview) return;

                const arrow = this.allowedArrowsByType[this.diagramType]?.find(a => a.value === this.currentArrowType);
                
                if (!arrow) return;

                let svgContent = '<svg viewBox="0 0 60 20">';
                svgContent += '<defs>';
                svgContent += '<marker id="previewArrowhead" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto"><polygon points="0 0, 10 5, 0 10" fill="currentColor" /></marker>';
                svgContent += '<marker id="previewEmptyArrowhead" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto"><polygon points="0 0, 10 5, 0 10" fill="none" stroke="currentColor" /></marker>';
                svgContent += '<marker id="previewDiamond" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto"><polygon points="0 5, 5 0, 10 5, 5 10" fill="currentColor" /></marker>';
                svgContent += '<marker id="previewEmptyDiamond" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto"><polygon points="0 5, 5 0, 10 5, 5 10" fill="none" stroke="currentColor" /></marker>';
                svgContent += '</defs>';
                
                let line = '<line x1="5" y1="10" x2="45" y2="10" stroke="currentColor"';
                
                const style = this.arrowStyles[this.currentArrowType] || this.arrowStyles.asociacion;
                
                if (style['stroke-dasharray']) {
                    line += ` stroke-dasharray="${style['stroke-dasharray']}"`;
                }
                
                if (arrow.reverse) {
                    if (style['marker-start']) {
                        line += ` marker-start="url(#preview${style['marker-start'].charAt(6).toUpperCase() + style['marker-start'].slice(7)})"`;
                    }
                } else {
                    if (style['marker-end']) {
                        line += ` marker-end="url(#preview${style['marker-end'].charAt(6).toUpperCase() + style['marker-end'].slice(7)})"`;
                    }
                }
                
                line += ' />';
                svgContent += line;
                svgContent += '</svg>';
                
                preview.innerHTML = svgContent;
            }

            loadShapesForType(type) {
                const container = document.getElementById('shapesContainer');
                if (!container) return;
                
                container.innerHTML = '';
                
                const shapes = this.shapesByType[type] || [];
                
                shapes.forEach(shape => {
                    const shapeEl = document.createElement('div');
                    shapeEl.className = 'shape-item';
                    shapeEl.draggable = true;
                    shapeEl.dataset.type = shape.type;
                    
                    shapeEl.innerHTML = `
                        <div class="shape-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="small">${shape.label}</div>
                    `;
                    
                    // Cargar SVG — shape.icon ya contiene URL absoluta desde PHP (BASE_URL)
                    const svgUrl = shape.icon;
                    fetch(svgUrl, { cache: 'force-cache' })
                        .then(response => {
                            if (!response.ok) throw new Error('SVG no encontrado: ' + svgUrl);
                            return response.text();
                        })
                        .then(svgText => {
                            const svgContainer = document.createElement('div');
                            svgContainer.className = 'shape-svg-container';
                            svgContainer.innerHTML = svgText;
                            
                            const iconDiv = shapeEl.querySelector('.shape-icon');
                            if (iconDiv) {
                                iconDiv.parentNode.replaceChild(svgContainer, iconDiv);
                            }
                            
                            const svg = svgContainer.querySelector('svg');
                            if (svg) {
                                svg.setAttribute('width', '32');
                                svg.setAttribute('height', '32');
                                // Mantener viewBox original si existe, o usar 0 0 24 24
                                if (!svg.getAttribute('viewBox')) {
                                    svg.setAttribute('viewBox', '0 0 24 24');
                                }
                                // Forzar herencia de color para que respete tema claro/oscuro
                                svg.style.color = 'inherit';
                                svg.querySelectorAll('[fill]').forEach(el => {
                                    const f = el.getAttribute('fill');
                                    if (f && f !== 'none' && f !== 'currentColor' && !f.startsWith('#198754') && !f.startsWith('#dc3545')) {
                                        el.setAttribute('fill', 'currentColor');
                                    }
                                });
                                svg.querySelectorAll('[stroke]').forEach(el => {
                                    const s = el.getAttribute('stroke');
                                    if (s && s !== 'none' && s !== 'currentColor') {
                                        el.setAttribute('stroke', 'currentColor');
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.warn('Error cargando SVG:', error);
                            const iconDiv = shapeEl.querySelector('.shape-icon');
                            if (iconDiv) {
                                iconDiv.innerHTML = '<i class="bi bi-diagram-3" style="font-size: 28px;"></i>';
                            }
                        });
                    
                    shapeEl.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', JSON.stringify({
                            type: shape.type
                        }));
                    });
                    
                    container.appendChild(shapeEl);
                });

                // Para casos de uso: deshabilitar usecase/actor si no hay sistema
                if (type === 'usecase') {
                    this._updateUsecaseShapeStates();
                    const tog = document.getElementById('usecaseLayoutToggle');
                    if (tog) tog.style.display = 'block';
                } else {
                    const tog = document.getElementById('usecaseLayoutToggle');
                    if (tog) tog.style.display = 'none';
                }
            }

            // ── Actualiza el estado visual de la paleta ──
            _updateUsecaseShapeStates() {
                if (this.diagramType !== 'usecase') return; // solo para casos de uso
                const hasSystem = this.nodes.some(n => n.type === 'system');
                const hint = document.getElementById('usecaseOrderHint');
                document.querySelectorAll('#shapesContainer .shape-item').forEach(el => {
                    const t = el.dataset.type;
                    if (t === 'usecase' || t === 'actor') {
                        if (!hasSystem) {
                            el.classList.add('disabled-shape');
                            el.title = 'Primero agrega el Sistema';
                        } else {
                            el.classList.remove('disabled-shape');
                            el.title = '';
                        }
                    }
                });
                if (hint) hint.style.display = hasSystem ? 'none' : 'block';
            }

            // ── Empuja otros casos de uso verticalmente para evitar solapamiento ──
            _pushUsecasesApart(movedNode) {
                const GAP = 14;
                const system = this.nodes.find(n => n.type === 'system');
                if (!system) return;
                const others = this.nodes
                    .filter(n => n.type === 'usecase' && n.id !== movedNode.id)
                    .sort((a, b) => a.y - b.y);
                others.forEach(uc => {
                    if (uc.y < movedNode.y) {
                        if (uc.y + uc.height + GAP > movedNode.y) {
                            uc.y = movedNode.y - uc.height - GAP;
                        }
                    } else {
                        if (uc.y < movedNode.y + movedNode.height + GAP) {
                            uc.y = movedNode.y + movedNode.height + GAP;
                        }
                    }
                    const minY = system.y + 40;
                    const maxY = system.y + system.height - uc.height - 10;
                    uc.y = Math.max(minY, Math.min(uc.y, maxY));
                });
            }

            // ── Cambia el layout de casos de uso y reordena ──
            setUsecaseLayout(layout) {
                this.usecaseLayout = layout;
                // Actualizar botones
                const vBtn = document.getElementById('layoutVertBtn');
                const hBtn = document.getElementById('layoutHorizBtn');
                if (vBtn && hBtn) {
                    if (layout === 'vertical') {
                        vBtn.className = 'btn btn-sm btn-primary flex-fill';
                        hBtn.className = 'btn btn-sm btn-outline-secondary flex-fill';
                    } else {
                        hBtn.className = 'btn btn-sm btn-primary flex-fill';
                        vBtn.className = 'btn btn-sm btn-outline-secondary flex-fill';
                    }
                }
                // Reposicionar todos los usecases según el nuevo layout
                this._reorganizarUsecases();
                this._normalizeUsecaseConnectionsAfterLayout();
                this.render();
                this.pushToHistory();
                this.unsavedChanges = true;
            }

            // ── Reposiciona todos los casos de uso en el layout actual ──
            // ════════════════════════════════════════════════════════════════
            // ALGORITMO ROBUSTO DE LAYOUT PARA CASOS DE USO
            // ════════════════════════════════════════════════════════════════
            // 
            // Este sistema de layout resuelve:
            // ✓ Evita traslapes completamente
            // ✓ Contenedor dinámico que se adapta al contenido
            // ✓ Distribucion eficiente con grid
            // ✓ Funciona con cualquier cantidad de elementos
            // ════════════════════════════════════════════════════════════════

            _reorganizarUsecases() {
                const system   = this.nodes.find(n => n.type === 'system');
                if (!system) return;
                const ucs      = this.nodes.filter(n => n.type === 'usecase');
                if (ucs.length === 0) return;

                // Parámetros de espaciado
                const PADDING_TOP = 50;
                const PADDING_SIDE = 30;
                const PADDING_BOTTOM = 20;
                const GAP_HORIZONTAL = 20;
                const GAP_VERTICAL = 20;
                
                // Tamaño estándar de los casos de uso
                const UC_WIDTH = 130;
                const UC_HEIGHT = 50;

                if (this.usecaseLayout === 'vertical') {
                    // ──────────────────────────────────────────────────────────
                    // LAYOUT VERTICAL: Elements stackados verticalmente
                    // ──────────────────────────────────────────────────────────
                    // Calcular dimensiones necesarias del contenedor
                    const contentWidth = Math.max(UC_WIDTH, 200);
                    const contentHeight = ucs.length * UC_HEIGHT + (ucs.length - 1) * GAP_VERTICAL;
                    
                    // Actualizar tamaño del sistema
                    system.width = contentWidth + PADDING_SIDE * 2;
                    system.height = contentHeight + PADDING_TOP + PADDING_BOTTOM;
                    
                    // Calcular ancho disponible y posicionar elementos
                    let posY = system.y + PADDING_TOP;
                    
                    ucs.forEach(uc => {
                        uc.width = UC_WIDTH;
                        uc.height = UC_HEIGHT;
                        // Centrar horizontalmente dentro del sistema
                        uc.x = system.x + (system.width - uc.width) / 2;
                        uc.y = posY;
                        posY += UC_HEIGHT + GAP_VERTICAL;
                    });
                    
                } else {
                    // ──────────────────────────────────────────────────────────
                    // LAYOUT HORIZONTAL: Una fila horizontal, sistema crece hacia los lados
                    // ──────────────────────────────────────────────────────────
                    const contentWidth = ucs.length * UC_WIDTH + (ucs.length - 1) * GAP_HORIZONTAL;
                    const targetWidth = Math.max(UC_WIDTH + PADDING_SIDE * 2, contentWidth + PADDING_SIDE * 2);
                    const targetHeight = UC_HEIGHT + PADDING_TOP + PADDING_BOTTOM;

                    system.width = targetWidth;
                    system.height = targetHeight;

                    const offsetX = system.x + PADDING_SIDE;
                    const offsetY = system.y + PADDING_TOP;

                    ucs.forEach((uc, index) => {
                        uc.width = UC_WIDTH;
                        uc.height = UC_HEIGHT;
                        uc.x = offsetX + index * (UC_WIDTH + GAP_HORIZONTAL);
                        uc.y = offsetY;
                    });
                }
            }

            // ──────────────────────────────────────────────────────────────
            // Centrar el diagrama al cargar basado en bounding box
            // ──────────────────────────────────────────────────────────────
            centerDiagramInViewport() {
                if (this.nodes.length === 0) return;
                
                // Calcular bounding box de todos los elementos
                let minX = Infinity, maxX = -Infinity;
                let minY = Infinity, maxY = -Infinity;
                
                this.nodes.forEach(node => {
                    minX = Math.min(minX, node.x);
                    maxX = Math.max(maxX, node.x + node.width);
                    minY = Math.min(minY, node.y);
                    maxY = Math.max(maxY, node.y + node.height);
                });
                
                // Obtener dimensiones del viewport
                const container = document.getElementById('canvasContainer');
                const rect = container.getBoundingClientRect();
                const viewportWidth = rect.width;
                const viewportHeight = rect.height;
                
                // Calcular las dimensiones del diagrama
                const diagramWidth = maxX - minX;
                const diagramHeight = maxY - minY;
                
                // Calcular zoom para que quepa en la pantalla
                const padding = 80;
                const zoomX = (viewportWidth - padding) / diagramWidth;
                const zoomY = (viewportHeight - padding) / diagramHeight;
                let zoom = Math.min(zoomX, zoomY, 1.2);
                zoom = Math.max(zoom, 0.3);
                
                // Calcular pan para centrar
                const centerX = minX + diagramWidth / 2;
                const centerY = minY + diagramHeight / 2;
                
                const panX = (viewportWidth / 2) / zoom - centerX;
                const panY = (viewportHeight / 2) / zoom - centerY;
                
                // Aplicar transformación usando variables globales
                window._zoom = zoom;
                window._panX = panX;
                window._panY = panY;
                
                // Actualizar UI
                const zoomLabel = document.getElementById('zoomLabel');
                if (zoomLabel) zoomLabel.textContent = Math.round(zoom * 100) + '%';
                const zoomLevelFloat = document.getElementById('zoomLevelFloat');
                if (zoomLevelFloat) zoomLevelFloat.textContent = Math.round(zoom * 100) + '%';
                
                // Aplicar transformación (función global)
                if (typeof applyTransform === 'function') {
                    applyTransform();
                }
                
                this.render();
            }

            _reorganizarUsecases_OLD() {
                // FUNCIÓN ANTERIOR - DEJADO PARA REFERENCIA
                // Ahora se usa el sistema robusto arriba
                const system   = this.nodes.find(n => n.type === 'system');
                if (!system) return;
                const ucs      = this.nodes.filter(n => n.type === 'usecase');
                if (ucs.length === 0) return;
                const GAP      = 14;
                const PADTOP   = 45;
                const PADSIDE  = 20;

                if (this.usecaseLayout === 'vertical') {
                    const totalH = ucs.length * 44 + (ucs.length - 1) * GAP + PADTOP + 20;
                    if (totalH > system.height) system.height = totalH;
                    const maxUcW = Math.max(100, system.width - PADSIDE * 2);
                    let curY = system.y + PADTOP;
                    ucs.forEach(uc => {
                        uc.width = Math.min(150, maxUcW);
                        uc.x = system.x + (system.width - uc.width) / 2;
                        uc.x = Math.max(system.x + PADSIDE, Math.min(uc.x, system.x + system.width - uc.width - PADSIDE));
                        uc.y = curY;
                        curY += uc.height + GAP;
                    });
                    const needed = curY + 20;
                    if (needed > system.y + system.height) system.height = needed - system.y;
                } else {
                    const ucW    = Math.max(80, Math.min(140, Math.floor((system.width - PADSIDE * 2 - GAP * (ucs.length - 1)) / ucs.length)));
                    const totalW = ucs.length * ucW + (ucs.length - 1) * GAP;
                    if (totalW + PADSIDE * 2 > system.width) system.width = totalW + PADSIDE * 2;
                    let curX = system.x + (system.width - totalW) / 2;
                    const ucY = system.y + PADTOP;
                    ucs.forEach(uc => {
                        uc.width = ucW;
                        uc.x     = curX;
                        uc.y     = ucY;
                        uc.x = Math.max(system.x + PADSIDE, Math.min(uc.x, system.x + system.width - uc.width - PADSIDE));
                        curX += ucW + GAP;
                    });
                    const maxUcH = ucs.reduce((m, u) => Math.max(m, u.height), 44);
                    const neededH = PADTOP + maxUcH + 20;
                    if (neededH > system.height) system.height = neededH;
                }
            }

            // ── Muestra un toast rojo de error de validación ──
            _showValidationToast(message) {
                const prev = document.getElementById('validationToast');
                if (prev) prev.remove();
                const toast = document.createElement('div');
                toast.id = 'validationToast';
                toast.className = 'validation-toast';
                toast.innerHTML = `<i class="bi bi-exclamation-circle-fill"></i> ${message}`;
                document.body.appendChild(toast);
                setTimeout(() => { if (toast.parentNode) toast.remove(); }, 3200);
            }

            // Método para verificar colisiones (solo usado en inserción inicial)
            hasCollision(node, x, y, excludeNode = null) {
                for (const otherNode of this.nodes) {
                    if (excludeNode && otherNode.id === excludeNode.id) continue;
                    
                    // Verificar superposición con margen de 20px
                    if (!(x > otherNode.x + otherNode.width + this.minDistance ||
                          x + node.width < otherNode.x - this.minDistance ||
                          y > otherNode.y + otherNode.height + this.minDistance ||
                          y + node.height < otherNode.y - this.minDistance)) {
                        return true; // Hay colisión
                    }
                }
                return false;
            }

            // Buscar posición libre dentro del sistema (solo para inserción inicial)
            findFreePositionInSystem(node, system) {
                const maxAttempts = 100;
                const startX = system.x + 30;
                const startY = system.y + 50;
                const maxX = system.x + system.width - node.width - 30;
                const maxY = system.y + system.height - node.height - 30;
                
                // Intentar en grid de 3 columnas
                const cols = 3;
                const rowHeight = node.height + 30;
                const colWidth = node.width + 30;
                
                for (let attempt = 0; attempt < maxAttempts; attempt++) {
                    const row = Math.floor(attempt / cols);
                    const col = attempt % cols;
                    
                    const testX = Math.min(startX + col * colWidth, maxX);
                    const testY = Math.min(startY + row * rowHeight, maxY);
                    
                    if (testX <= maxX && testY <= maxY && !this.hasCollision(node, testX, testY)) {
                        return { x: testX, y: testY };
                    }
                }
                
                // Si no hay espacio, intentar en posiciones aleatorias
                for (let attempt = 0; attempt < 50; attempt++) {
                    const testX = startX + Math.random() * (maxX - startX);
                    const testY = startY + Math.random() * (maxY - startY);
                    
                    if (!this.hasCollision(node, testX, testY)) {
                        return { x: testX, y: testY };
                    }
                }
                
                // Último recurso: devolver la posición original (puede colisionar)
                return { x: startX, y: startY };
            }

            handleDrop(e) {
                e.preventDefault();
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                const rect = e.currentTarget.getBoundingClientRect();
                // Ajustar coordenadas de drop por zoom y pan
                const z    = (typeof _zoom !== 'undefined') ? _zoom : 1;
                const pX   = (typeof _panX !== 'undefined') ? _panX : 0;
                const pY   = (typeof _panY !== 'undefined') ? _panY : 0;
                const dropX = (e.clientX - rect.left - pX) / z;
                const dropY = (e.clientY - rect.top  - pY) / z;

                // ── Validaciones por tipo de diagrama ──────────────────
                if (!this._validarInsercion(data.type)) return;

                const nodeId = this.generateNodeId(data.type);
                const node = {
                    id: nodeId,
                    x: dropX,
                    y: dropY,
                    text: this.getDefaultName(data.type),
                    type: data.type,
                    width: this.getDefaultWidth(data.type),
                    height: this.getDefaultHeight(data.type),
                    color: this.getColorForType(data.type),
                    attributes: '',
                    methods: ''
                };

                // ── Posicionamiento automático por tipo ────────────────
                this._posicionarNodo(node, dropX, dropY);

                this.nodes.push(node);
                this.render();
                this._updateUsecaseShapeStates();
                this.pushToHistory();
                this.unsavedChanges = true;
            }

            /**
             * Valida si se puede insertar un nodo del tipo dado en el diagrama actual.
             * Devuelve true si es válido, false si muestra un toast y bloquea.
             */
            _validarInsercion(type) {
                const tipo = this.diagramType;
                const nodes = this.nodes;

                // ── Casos de Uso ───────────────────────────────────────
                if (tipo === 'usecase') {
                    if (type === 'system' && nodes.some(n => n.type === 'system')) {
                        this._showValidationToast('Solo puede haber un Sistema por diagrama.');
                        return false;
                    }
                    if ((type === 'usecase' || type === 'actor') && !nodes.some(n => n.type === 'system')) {
                        this._showValidationToast('Agrega primero el bloque "Sistema".');
                        return false;
                    }
                }

                // ── Actividades ────────────────────────────────────────
                if (tipo === 'activity') {
                    if (type === 'start' && nodes.filter(n => n.type === 'start').length >= 1) {
                        this._showValidationToast('Solo puede haber un nodo Inicio por diagrama.');
                        return false;
                    }
                    if (type === 'end' && nodes.filter(n => n.type === 'end').length >= 1) {
                        this._showValidationToast('Solo puede haber un nodo Fin por diagrama.');
                        return false;
                    }
                }

                // ── Estados ────────────────────────────────────────────
                if (tipo === 'state') {
                    if (type === 'initial' && nodes.filter(n => n.type === 'initial').length >= 1) {
                        this._showValidationToast('Solo puede haber un Estado Inicial.');
                        return false;
                    }
                    if (type === 'final' && nodes.filter(n => n.type === 'final').length >= 1) {
                        this._showValidationToast('Solo puede haber un Estado Final.');
                        return false;
                    }
                }

                // ── Secuencia / Interacción ────────────────────────────
                if (tipo === 'sequence') {
                    if (type === 'activation') {
                        const hasLifeline = nodes.some(n => n.type === 'lifeline' || n.type === 'actor');
                        if (!hasLifeline) {
                            this._showValidationToast('Agrega al menos una Línea de Vida antes de añadir Activaciones.');
                            return false;
                        }
                    }
                }

                // ── Despliegue ─────────────────────────────────────────
                if (tipo === 'deployment') {
                    if (type === 'artifact') {
                        const hasNode = nodes.some(n => n.type === 'node' || n.type === 'device');
                        if (!hasNode) {
                            this._showValidationToast('Agrega primero un Nodo o Dispositivo antes de añadir Artefactos.');
                            return false;
                        }
                    }
                }

                // ── Objetos ────────────────────────────────────────────
                if (tipo === 'object') {
                    if (type === 'valor') {
                        if (!nodes.some(n => n.type === 'object')) {
                            this._showValidationToast('Agrega primero un Objeto antes de añadir Valores.');
                            return false;
                        }
                    }
                }

                return true;
            }

            /**
             * Posiciona el nodo de forma inteligente según el tipo de diagrama.
             */
            _posicionarNodo(node, dropX, dropY) {
                const tipo = this.diagramType;

                // ── Casos de Uso: posición automática ──────────────────
                if (tipo === 'usecase') {
                    if (node.type === 'usecase') {
                        const system = this.nodes.find(n => n.type === 'system');
                        if (system) {
                            const GAP = 14, PADDING_TOP = 45, PADDING_SIDE = 20, PADDING_BOTTOM = 20;
                            node.width = Math.min(this.getDefaultWidth('usecase'), system.width - PADDING_SIDE * 2);
                            // Centrar X dentro del sistema
                            node.x = system.x + (system.width - node.width) / 2;
                            // Clampear X para que no se salga
                            node.x = Math.max(system.x + PADDING_SIDE, Math.min(node.x, system.x + system.width - node.width - PADDING_SIDE));
                            const existingUC = this.nodes.filter(n => n.type === 'usecase');
                            if (this.usecaseLayout === 'horizontal') {
                                const currentCount = existingUC.length;
                                node.x = system.x + PADDING_SIDE + currentCount * (node.width + GAP);
                                node.y = system.y + PADDING_TOP;

                                const neededWidth = node.x + node.width + PADDING_SIDE - system.x;
                                if (neededWidth > system.width) {
                                    system.width = neededWidth;
                                }
                                const neededHeight = node.y + node.height + PADDING_BOTTOM - system.y;
                                if (neededHeight > system.height) {
                                    system.height = neededHeight;
                                }
                            } else {
                                if (existingUC.length === 0) {
                                    node.y = system.y + PADDING_TOP;
                                } else {
                                    const lowest = existingUC.reduce((p, c) => (c.y + c.height > p.y + p.height ? c : p));
                                    node.y = lowest.y + lowest.height + GAP;
                                }
                                // Centrar X dentro del sistema
                                node.x = system.x + (system.width - node.width) / 2;
                                node.x = Math.max(system.x + PADDING_SIDE, Math.min(node.x, system.x + system.width - node.width - PADDING_SIDE));

                                const needed = node.y + node.height + 20;
                                if (needed > system.y + system.height) {
                                    system.height = needed - system.y + 20;
                                }
                            }
                            return;
                        }
                    }
                    if (node.type === 'actor') {
                        const system = this.nodes.find(n => n.type === 'system');
                        if (system) {
                            const existing = this.nodes.filter(n => n.type === 'actor');
                            const col = existing.length % 2;
                            const row = Math.floor(existing.length / 2);
                            node.x = col === 0 ? system.x - node.width - 50 : system.x + system.width + 50;
                            node.y = system.y + 40 + row * (node.height + 20);
                            return;
                        }
                    }
                }

                // ── Actividades: apilar verticalmente ──────────────────
                if (tipo === 'activity') {
                    if (node.type === 'start') { node.x = 300; node.y = 40; return; }
                    if (node.type === 'end')   { node.x = 300; node.y = Math.max(...this.nodes.map(n => n.y + n.height), 100) + 40; return; }
                    if (['activity','decision','fork','union'].includes(node.type)) {
                        const below = this.nodes.filter(n => n.type !== 'end');
                        if (below.length > 0) {
                            const lowest = below.reduce((p, c) => (c.y + c.height > p.y + p.height ? c : p));
                            node.x = lowest.x;
                            node.y = lowest.y + lowest.height + 30;
                            return;
                        }
                    }
                }

                // ── Estados: en cascada ────────────────────────────────
                if (tipo === 'state') {
                    if (node.type === 'initial') { node.x = 300; node.y = 40; return; }
                    if (node.type === 'final')   {
                        node.x = 300;
                        node.y = Math.max(...this.nodes.map(n => n.y + n.height), 100) + 40;
                        return;
                    }
                }

                // ── Secuencia: lifelines en fila horizontal ────────────
                if (tipo === 'sequence' && (node.type === 'lifeline' || node.type === 'actor')) {
                    const existing = this.nodes.filter(n => n.type === 'lifeline' || n.type === 'actor');
                    node.x = 40 + existing.length * (node.width + 100);
                    node.y = 20;
                    return;
                }

                // ── Secuencia: activación se adhiere a lifeline más cercana ──
                if (tipo === 'sequence' && node.type === 'activation') {
                    const lifelines = this.nodes.filter(n => n.type === 'lifeline' || n.type === 'actor');
                    if (lifelines.length > 0) {
                        // Encontrar la lifeline más cercana al punto de drop
                        const nearest = lifelines.reduce((best, ll) => {
                            const llCX = ll.x + ll.width / 2;
                            const dist = Math.abs(dropX - llCX);
                            return dist < best.dist ? { ll, dist } : best;
                        }, { ll: lifelines[0], dist: Infinity }).ll;

                        // Centrar la activación sobre el eje de la lifeline
                        node.x     = nearest.x + nearest.width / 2 - node.width / 2;
                        node.y     = Math.max(dropY, nearest.y + 36);
                        this.activationParents[node.id] = nearest.id;
                    }
                    return;
                }

                // Por defecto: usar la posición del drop
                node.x = dropX;
                node.y = dropY;
            }

            generateNodeId(type) {
                return `${type}_${this.nodeIdCounter++}`;
            }

            getDefaultName(type) {
                const names = {
                    actor: 'Actor',
                    usecase: 'Caso de Uso',
                    system: 'Sistema',
                    class: 'Clase',
                    abstract: 'Clase Abstracta',
                    interface: 'Interfaz',
                    enum: 'Enumeración',
                    lifeline: 'Línea de Vida',
                    activation: 'Activación',
                    start: 'Inicio',
                    activity: 'Actividad',
                    decision: 'Decisión',
                    fork: 'Bifurcación',
                    union: 'Unión',
                    end: 'Fin',
                    initial: 'Inicial',
                    state: 'Estado',
                    final: 'Final',
                    history: 'Historia',
                    component: 'Componente',
                    required: 'Interfaz Requerida',
                    port: 'Puerto',
                    node: 'Nodo',
                    device: 'Dispositivo',
                    artifact: 'Artefacto',
                    object: 'Objeto',
                    valor: 'Valor',
                    message: 'Mensaje',
                    link: 'Enlace',
                    event: 'Evento',
                    constraint: 'Restricción',
                    'lifeline-t': 'Línea de Vida'
                };
                return names[type] || 'Elemento';
            }

            getDefaultWidth(type) {
                const widths = {
                    actor: 80,
                    system: 400,
                    usecase: 150,
                    class: 180,
                    abstract: 180,
                    interface: 180,
                    enum: 160,
                    lifeline: 100,
                    activation: 20,
                    component: 120,
                    required: 80,
                    port: 30,
                    node: 150,
                    device: 150,
                    artifact: 100,
                    object: 150,
                    valor: 120,
                    message: 120,
                    link: 80,
                    event: 80,
                    constraint: 100,
                    decision: 100,
                    fork: 160,
                    union: 160,
                    start: 40,
                    end: 40,
                    initial: 30,
                    final: 40,
                    history: 40,
                    state: 140
                };
                return widths[type] || 140;
            }

            getDefaultHeight(type) {
                const heights = {
                    actor: 120,
                    system: 300,
                    usecase: 60,
                    class: 150,
                    abstract: 150,
                    interface: 120,
                    enum: 120,
                    lifeline: 500,
                    activation: 80,
                    component: 80,
                    required: 40,
                    port: 30,
                    node: 100,
                    device: 120,
                    artifact: 80,
                    object: 80,
                    valor: 40,
                    message: 40,
                    link: 40,
                    event: 40,
                    constraint: 40,
                    decision: 70,
                    fork: 12,
                    union: 12,
                    start: 40,
                    end: 40,
                    initial: 30,
                    final: 40,
                    history: 40,
                    state: 60
                };
                return heights[type] || 70;
            }

            getColorForType(type) {
                const colors = {
                    start: '#198754',
                    end: '#dc3545',
                    decision: '#fd7e14',
                    process: '#0d6efd',
                    io: '#6f42c1',
                    default: '#0d6efd'
                };
                return colors[type] || colors.default;
            }

            _expandirViewportSiNecesario() {
                if (!this.nodes.length) return;
                if (this.dragging) return; // No expandir durante el arrastre para evitar parpadeo
                const viewport = document.getElementById('canvasViewport');
                if (!viewport) return;

                const MARGEN = 400; // px extra más allá del nodo más lejano
                let maxX = 0, maxY = 0, minX = Infinity, minY = Infinity;

                this.nodes.forEach(n => {
                    maxX = Math.max(maxX, n.x + (n.width  || 140) + MARGEN);
                    maxY = Math.max(maxY, n.y + (n.height || 70)  + MARGEN);
                    minX = Math.min(minX, n.x - MARGEN);
                    minY = Math.min(minY, n.y - MARGEN);
                });

                // El viewport crece pero nunca encoge (mínimo 4000x3000)
                const wActual = parseInt(viewport.style.width)  || 4000;
                const hActual = parseInt(viewport.style.height) || 3000;
                const wNuevo  = Math.max(wActual, maxX);
                const hNuevo  = Math.max(hActual, maxY);

                if (wNuevo !== wActual || hNuevo !== hActual) {
                    viewport.style.width  = wNuevo + 'px';
                    viewport.style.height = hNuevo + 'px';
                    const canvas = document.getElementById('diagramCanvas');
                    if (canvas) {
                        canvas.style.width  = wNuevo + 'px';
                        canvas.style.height = hNuevo + 'px';
                    }
                }

                // Si hay nodos con X/Y negativas, hacer pan para verlos
                // Deshabilitado para mantener la vista estable durante edición
                /*
                if (minX < 0 && typeof _panX !== 'undefined') {
                    const z = _zoom || 1;
                    if (_panX > minX * z) {
                        _panX = Math.max(0, -minX * z + 20);
                        applyTransform();
                    }
                }
                if (minY < 0 && typeof _panY !== 'undefined') {
                    const z = _zoom || 1;
                    if (_panY > minY * z) {
                        _panY = Math.max(0, -minY * z + 20);
                        applyTransform();
                    }
                }
                */
            }

            render() {
                const canvas = document.getElementById('diagramCanvas');
                if (!canvas) return;

                // ── Expandir viewport si algún nodo se sale del área ──
                this._expandirViewportSiNecesario();

                canvas.innerHTML = '';

                // ── Inyectar marcadores SVG globales (necesarios para las puntas de flecha) ──
                const svgNS = 'http://www.w3.org/2000/svg';
                const defsHolder = document.createElementNS(svgNS, 'svg');
                defsHolder.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;';
                defsHolder.setAttribute('aria-hidden', 'true');
                defsHolder.innerHTML = `
                    <defs>
                        <!-- markerUnits=userSpaceOnUse con tamaño fijo en px.
                             refX = largo del marcador → la punta queda exactamente en el endpoint del path. -->
                        <marker id="arrowhead" markerWidth="12" markerHeight="10"
                                refX="12" refY="5" orient="auto" markerUnits="userSpaceOnUse">
                            <polygon points="0 0, 12 5, 0 10" fill="#0d6efd"/>
                        </marker>
                        <marker id="emptyArrowhead" markerWidth="12" markerHeight="10"
                                refX="12" refY="5" orient="auto" markerUnits="userSpaceOnUse">
                            <polyline points="0 0, 12 5, 0 10" fill="none"
                                      stroke="#0d6efd" stroke-width="1.8"/>
                        </marker>
                        <marker id="diamond" markerWidth="16" markerHeight="10"
                                refX="16" refY="5" orient="auto" markerUnits="userSpaceOnUse">
                            <polygon points="0 5, 8 0, 16 5, 8 10" fill="#0d6efd"/>
                        </marker>
                        <marker id="emptyDiamond" markerWidth="16" markerHeight="10"
                                refX="16" refY="5" orient="auto" markerUnits="userSpaceOnUse">
                            <polygon points="0 5, 8 0, 16 5, 8 10"
                                     fill="none" stroke="#0d6efd" stroke-width="1.5"/>
                        </marker>
                    </defs>
                `;
                canvas.appendChild(defsHolder);
                
                // Primero renderizar sistemas (para que estén detrás)
                const systems = this.nodes.filter(n => n.type === 'system');
                const otherNodes = this.nodes.filter(n => n.type !== 'system');
                
                systems.forEach(node => {
                    this.renderNode(canvas, node);
                });
                
                otherNodes.forEach(node => {
                    this.renderNode(canvas, node);
                });
                
                // Render connections (visual paths first)
                this.connections.forEach(conn => this.renderConnection(conn));
                
                // ── Overlay SVG for connection hitboxes on top of nodes ──
                // This ensures connections are clickable even when nodes overlap them
                this._renderConnectionHitboxOverlay(canvas);
                
                this.updateLayersList();
                this.updatePropertiesPanel();
                // Actualizar estado de la paleta tras cada render
                this._updateUsecaseShapeStates();
            }

            _showToast(msg, type = 'info') {
                const colors = { info:'#3b82f6', ok:'#10b981', warn:'#f59e0b', err:'#ef4444' };
                const t = document.createElement('div');
                t.style.cssText = `position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:${colors[type]||colors.info};color:#fff;padding:8px 20px;border-radius:20px;font-size:.82rem;font-weight:600;z-index:99999;pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,.3)`;
                t.textContent = msg;
                document.body.appendChild(t);
                setTimeout(() => t.remove(), 3000);
            }

            _renderConnectionHitboxOverlay(canvas) {
                // Create a transparent SVG overlay that sits on top of all nodes
                // with wide invisible strokes for easy connection clicking
                const svgNS = 'http://www.w3.org/2000/svg';
                const overlay = document.createElementNS(svgNS, 'svg');
                overlay.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:50;overflow:visible;';
                overlay.setAttribute('aria-hidden', 'true');

                this.connections.forEach((conn, idx) => {
                    const fromNode = this.nodes.find(n => n.id === conn.fromNode);
                    const toNode   = this.nodes.find(n => n.id === conn.toNode);
                    if (!fromNode || !toNode) return;

                    const fp = this.getConnectionPoint(fromNode, conn.fromSide || 'right');
                    const tp = this.getConnectionPoint(toNode,   conn.toSide   || 'left');
                    if (!fp || !tp) return;

                    // Compute same path as renderConnection
                    const dx = tp.x - fp.x, dy = tp.y - fp.y;
                    const ls = conn.lineStyle || 'bezier';
                    let d;
                    if (ls === 'straight') {
                        d = `M ${fp.x} ${fp.y} L ${tp.x} ${tp.y}`;
                    } else if (ls === 'orthogonal') {
                        const mx = fp.x + dx*0.5, my = fp.y + dy*0.5;
                        const fd = conn.fromSide || 'right';
                        d = (fd==='right'||fd==='left')
                            ? `M ${fp.x} ${fp.y} L ${mx} ${fp.y} L ${mx} ${tp.y} L ${tp.x} ${tp.y}`
                            : `M ${fp.x} ${fp.y} L ${fp.x} ${my} L ${tp.x} ${my} L ${tp.x} ${tp.y}`;
                    } else if (ls === 'arc') {
                        const r = Math.sqrt(dx*dx+dy*dy)*0.65;
                        d = `M ${fp.x} ${fp.y} A ${r} ${r} 0 0 ${dx>=0?1:0} ${tp.x} ${tp.y}`;
                    } else {
                        let cx1, cy1, cx2, cy2;
                        const fd = conn.fromSide || 'right';
                        if (fd==='right'||fd==='left') { cx1=fp.x+dx*.55; cy1=fp.y; cx2=tp.x-dx*.55; cy2=tp.y; }
                        else { cx1=fp.x; cy1=fp.y+dy*.55; cx2=tp.x; cy2=tp.y-dy*.55; }
                        d = `M ${fp.x} ${fp.y} C ${cx1} ${cy1}, ${cx2} ${cy2}, ${tp.x} ${tp.y}`;
                    }

                    const hit = document.createElementNS(svgNS, 'path');
                    hit.setAttribute('d', d);
                    hit.setAttribute('stroke', 'transparent');
                    hit.setAttribute('stroke-width', '32');
                    hit.setAttribute('fill', 'none');
                    hit.style.pointerEvents = 'stroke';
                    hit.style.cursor = 'pointer';
                    hit.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.selectConnection(conn);
                    });
                    hit.addEventListener('dblclick', (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        // Add waypoint at click position
                        const p = this.getCanvasPos(e);
                        if (!conn.waypoints) conn.waypoints = [];
                        // Insert in best position along the path
                        conn.waypoints.push({ x: p.x, y: p.y });
                        this.selectConnection(conn);
                        this.render();
                        this.pushToHistory();
                        this.unsavedChanges = true;
                        // Show hint
                        const h = document.createElement('div');
                        h.style.cssText='position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:#1a1a2e;border:1px solid var(--primary);color:#aab8ff;border-radius:20px;padding:5px 16px;font-size:.75rem;z-index:9001;pointer-events:none';
                        h.textContent='Punto añadido — arrástralo para reposicionar · Doble clic sobre el punto para eliminarlo';
                        document.body.appendChild(h);
                        setTimeout(()=>h.remove(), 2500);
                    });
                    hit.addEventListener('contextmenu', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.selectConnection(conn);
                        this._showConnContextMenu(e.clientX, e.clientY, conn);
                    });
                    overlay.appendChild(hit);
                });

                canvas.appendChild(overlay);
            }

            _showConnContextMenu(x, y, conn) {
                // Remove existing context menu
                document.getElementById('_connCtxMenu')?.remove();
                const menu = document.createElement('div');
                menu.id = '_connCtxMenu';
                menu.style.cssText = `position:fixed;left:${x}px;top:${y}px;background:#1a1a2e;border:1px solid #2a2a4a;border-radius:10px;padding:6px;z-index:99999;min-width:170px;box-shadow:0 8px 24px rgba(0,0,0,.5)`;
                menu.innerHTML = `
                    <div style="padding:4px 10px;font-size:.7rem;color:#667eea;font-weight:600;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #2a2a4a;margin-bottom:4px">Conexión</div>
                    <button onclick="editor.deleteSelected();document.getElementById('_connCtxMenu')?.remove()"
                        style="width:100%;background:none;border:none;color:#fca5a5;text-align:left;padding:7px 10px;border-radius:6px;cursor:pointer;font-size:.82rem;display:flex;align-items:center;gap:8px"
                        onmouseover="this.style.background='rgba(239,68,68,.12)'" onmouseout="this.style.background='none'">
                        <i class="bi bi-trash3"></i> Eliminar conexión (Del)
                    </button>
                    <button onclick="(()=>{if(editor.selectedConnection){editor.selectedConnection.waypoints=[];editor.render();editor.pushToHistory();}document.getElementById('_connCtxMenu')?.remove()})()"
                        style="width:100%;background:none;border:none;color:#aab8ff;text-align:left;padding:7px 10px;border-radius:6px;cursor:pointer;font-size:.82rem;display:flex;align-items:center;gap:8px"
                        onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='none'">
                        <i class="bi bi-arrow-counterclockwise"></i> Limpiar puntos de control
                    </button>
                    <button onclick="editor.selectedConnection&&(editor.selectedConnection.label=prompt('Etiqueta:',editor.selectedConnection.label||'')||editor.selectedConnection.label||'',editor.render(),editor.pushToHistory());document.getElementById('_connCtxMenu')?.remove()"
                        style="width:100%;background:none;border:none;color:#ccc;text-align:left;padding:7px 10px;border-radius:6px;cursor:pointer;font-size:.82rem;display:flex;align-items:center;gap:8px"
                        onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='none'">
                        <i class="bi bi-tag"></i> Editar etiqueta
                    </button>
                    <div style="padding:6px 10px;border-top:1px solid #2a2a4a;margin-top:4px">
                        <div style="font-size:.68rem;color:#888;margin-bottom:4px">Estilo de línea</div>
                        <div style="display:flex;gap:4px">
                            ${['bezier','straight','orthogonal','arc'].map((ls,i)=>{
                                const icons=['〜','—','⌐','⌢'];
                                const labels=['Curva','Recta','Doblada','Arco'];
                                return `<button onclick="setConnLineStyle('${ls}');document.getElementById('_connCtxMenu')?.remove()"
                                    style="flex:1;background:${conn.lineStyle===ls?'rgba(102,126,234,.3)':'rgba(255,255,255,.06)'};border:1px solid ${conn.lineStyle===ls?'#667eea':'#2a2a4a'};color:#ccc;border-radius:5px;padding:4px 2px;cursor:pointer;font-size:.75rem" title="${labels[i]}">${icons[i]}</button>`;
                            }).join('')}
                        </div>
                    </div>`;
                document.body.appendChild(menu);
                // Auto-close on outside click
                setTimeout(() => document.addEventListener('click', () => menu.remove(), { once: true }), 10);
            }

            showNotification(message, type = 'info') {
                // Crear elemento de notificación temporal
                const notification = document.createElement('div');
                notification.className = `alert alert-${type === 'warning' ? 'warning' : 'info'} alert-dismissible fade show position-fixed`;
                notification.style.cssText = `
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 300px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                `;
                notification.innerHTML = `
                    <i class="bi bi-${type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                // Auto-remover después de 4 segundos
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 4000);
            }

            renderNode(canvas, node) {
                const nodeEl = document.createElement('div');
                nodeEl.className = 'diagram-node';
                nodeEl.dataset.id = node.id;
                nodeEl.dataset.type = node.type;
                
                if (this.selectedNode && this.selectedNode.id === node.id) {
                    nodeEl.classList.add('selected');
                }
                
                // Sistema va detrás (z-index bajo)
                const zIndex = node.type === 'system' ? 2 : 10;
                const isActivation = node.type === 'activation';
                const isSystem = node.type === 'system';
                nodeEl.style.cssText = `
                    left: ${node.x}px;
                    top: ${node.y}px;
                    width: ${node.width}px;
                    ${isActivation || isSystem ? `height: ${node.height}px;` : `min-height: ${node.height}px;`}
                    z-index: ${zIndex};
                `;
                
                // ── Puntos de conexión según tipo ──────────────────────
                let connectionPointsHTML = '';

                if ((node.type === 'lifeline' || node.type === 'actor') && this.diagramType === 'sequence') {
                    // Zona continua de conexión sobre la línea de vida vertical.
                    // La lifeline normal tiene header de ~36px.
                    // El actor en secuencia tiene muñeco (~80px) + nombre (~20px) = ~100px antes de la línea.
                    const seqZoneTop = node.type === 'actor' ? '104px' : '36px';
                    connectionPointsHTML = `
                        <div class="connection-zone seq-zone" style="top:${seqZoneTop};left:calc(50% - 18px);width:36px;bottom:0;"
                             data-node="${node.id}" data-side="seq-zone"></div>
                    `;
                } else if (node.type === 'activation' && this.diagramType === 'sequence') {
                    // Activación: zonas en lado izquierdo y derecho para recibir/enviar flechas en cualquier Y
                    connectionPointsHTML = `
                        <div class="connection-zone seq-zone" style="top:0;left:-10px;width:10px;bottom:0;"
                             data-node="${node.id}" data-side="seq-zone-left"></div>
                        <div class="connection-zone seq-zone" style="top:0;right:-10px;width:10px;bottom:0;"
                             data-node="${node.id}" data-side="seq-zone-right"></div>
                    `;
                } else {
                    // Zonas de borde libre: el usuario puede conectar desde cualquier punto del borde
                    connectionPointsHTML = `
                        <div class="connection-zone free-zone" style="top:0;left:0;right:0;height:10px;"
                             data-node="${node.id}" data-side="top-edge"></div>
                        <div class="connection-zone free-zone" style="bottom:0;left:0;right:0;height:10px;"
                             data-node="${node.id}" data-side="bottom-edge"></div>
                        <div class="connection-zone free-zone" style="top:0;left:0;width:10px;bottom:0;"
                             data-node="${node.id}" data-side="left-edge"></div>
                        <div class="connection-zone free-zone" style="top:0;right:0;width:10px;bottom:0;"
                             data-node="${node.id}" data-side="right-edge"></div>
                    `;
                }

                nodeEl.innerHTML = this.renderNodeContent(node) + connectionPointsHTML;
                
                if (node.type === 'system' || node.type === 'activation') {
                    const resizeHandle = document.createElement('div');
                    resizeHandle.className = node.type === 'activation'
                        ? 'resize-handle-vertical'
                        : 'resize-handle';
                    resizeHandle.addEventListener('mousedown', (e) => this.startResizing(e, node));
                    nodeEl.appendChild(resizeHandle);
                }
                
                nodeEl.addEventListener('mousedown', (e) => {
                    if (!e.target.classList.contains('resize-handle') && 
                        !e.target.classList.contains('resize-handle-vertical') &&
                        !e.target.classList.contains('connection-point') &&
                        !e.target.classList.contains('seq-zone')) {
                        this.startDraggingNode(e, node);
                    }
                });

                // Evitar que el drag nativo del navegador interfiera con el drag del canvas
                nodeEl.addEventListener('dragstart', (e) => { e.preventDefault(); e.stopPropagation(); });
                nodeEl.style.userSelect = 'none';
                nodeEl.style.webkitUserSelect = 'none';
                
                nodeEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectNode(node);
                });
                
                nodeEl.querySelectorAll('.connection-point').forEach(point => {
                    point.addEventListener('mousedown', (e) => this.startConnection(e, node));
                });

                // Zonas continuas de conexión para secuencia (capturan Y exacta)
                nodeEl.querySelectorAll('.seq-zone').forEach(zone => {
                    zone.addEventListener('mousedown', (e) => {
                        e.stopPropagation();
                        const rect    = nodeEl.getBoundingClientRect();
                        const canvasY = node.y + (e.clientY - rect.top);
                        const side    = zone.dataset.side === 'seq-zone-right' ? 'right' : 'left';
                        const syntheticSide = `abs-${Math.round(canvasY)}-${side}`;
                        this._startConnectionFromZone(e, node, syntheticSide);
                    });
                });

                // Zonas de borde libre — capturan X e Y exactos del clic
                nodeEl.querySelectorAll('.free-zone').forEach(zone => {
                    zone.addEventListener('mousedown', (e) => {
                        e.stopPropagation();
                        const rect   = nodeEl.getBoundingClientRect();
                        const edgeSide = zone.dataset.side; // top-edge, bottom-edge, left-edge, right-edge
                        let percent;
                        if (edgeSide === 'top-edge' || edgeSide === 'bottom-edge') {
                            percent = (e.clientX - rect.left) / rect.width;
                        } else { // left-edge or right-edge
                            percent = (e.clientY - rect.top) / rect.height;
                        }
                        // Codificar como edge-side-percent para coordenadas relativas al borde
                        const syntheticSide = `edge-${edgeSide}-${percent.toFixed(3)}`;
                        this._startConnectionFromZone(e, node, syntheticSide);
                    });
                });
                
                canvas.appendChild(nodeEl);
            }

            renderNodeContent(node) {
                switch(node.type) {
                    case 'actor': {
                        // SVG del muñeco — idéntico en todos los diagramas
                        const actorSVG = `
                            <svg width="60" height="80" viewBox="0 0 60 80"
                                 xmlns="http://www.w3.org/2000/svg"
                                 style="display:block;margin:0 auto;overflow:visible;">
                                <circle cx="30" cy="12" r="10"
                                        stroke="currentColor" stroke-width="2.2" fill="none"/>
                                <line x1="30" y1="22" x2="30" y2="50"
                                      stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                <line x1="10" y1="35" x2="30" y2="37"
                                      stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                <line x1="30" y1="37" x2="50" y2="35"
                                      stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                <line x1="30" y1="50" x2="14" y2="74"
                                      stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                <line x1="30" y1="50" x2="46" y2="74"
                                      stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                            </svg>`;

                        if (this.diagramType === 'sequence') {
                            // En secuencia: muñeco + nombre + línea de vida punteada (igual que lifeline)
                            return `
                                <div class="uml-actor-lifeline" style="height:${node.height}px;">
                                    <div class="actor-lifeline-head">
                                        ${actorSVG}
                                        <div class="actor-name" style="text-align:center;margin-top:2px;">${node.text}</div>
                                    </div>
                                    <div class="lifeline-line"></div>
                                </div>
                            `;
                        }
                        // Fuera de secuencia: solo muñeco + nombre
                        return `
                            <div class="uml-actor">
                                ${actorSVG}
                                <div class="actor-name">${node.text}</div>
                            </div>
                        `;
                    }
                    
                    case 'system':
                        return `
                            <div class="uml-system">
                                <div class="system-name">${node.text}</div>
                                <div class="system-content"></div>
                            </div>
                        `;
                    
                    case 'usecase':
                        return `
                            <div class="uml-usecase">
                                <div class="usecase-name">${node.text}</div>
                            </div>
                        `;
                    
                    case 'class':
                    case 'abstract':
                    case 'interface': {
                        const isAbstract  = node.type === 'abstract';
                        const isInterface = node.type === 'interface';
                        const stereotype  = isInterface ? '&laquo;interface&raquo;<br>' : (isAbstract ? '&laquo;abstract&raquo;<br>' : '');
                        const nameStyle   = isAbstract ? 'font-style:italic;' : '';
                        const attrLines   = (node.attributes || '').split('\n').filter(l => l.trim());
                        const methLines   = (node.methods    || '').split('\n').filter(l => l.trim());
                        return `
                            <div class="uml-class" style="width:${node.width}px;min-height:${node.height}px;">
                                <div class="class-name" style="${nameStyle}">${stereotype}${node.text}</div>
                                <div class="class-attributes">
                                    ${attrLines.length
                                        ? attrLines.map(a => `<div class="class-attribute">${a}</div>`).join('')
                                        : '<div class="class-attribute" style="opacity:0.45;font-style:italic;">— sin atributos —</div>'}
                                </div>
                                <div class="class-methods">
                                    ${methLines.length
                                        ? methLines.map(m => `<div class="class-method">${m}</div>`).join('')
                                        : '<div class="class-method" style="opacity:0.45;font-style:italic;">— sin métodos —</div>'}
                                </div>
                            </div>
                        `;
                    }
                    
                    case 'lifeline':
                        return `
                            <div class="uml-lifeline" style="height:${node.height}px;">
                                <div class="lifeline-header">${node.text}</div>
                                <div class="lifeline-line"></div>
                            </div>
                        `;
                    
                    case 'activation':
                        return `
                            <div class="uml-activation" style="height:${node.height}px;"></div>
                        `;
                    
                    case 'start':
                        return `
                            <div class="uml-initial"></div>
                            <div class="node-text">${node.text}</div>
                        `;
                    
                    case 'activity':
                        return `
                            <div class="uml-activity">${node.text}</div>
                        `;
                    
                    case 'decision':
                        return `
                            <div class="uml-decision" style="width:${node.width}px;height:${node.height}px;">
                                <svg width="${node.width}" height="${node.height}" viewBox="0 0 ${node.width} ${node.height}" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
                                    <polygon points="${node.width/2},2 ${node.width-2},${node.height/2} ${node.width/2},${node.height-2} 2,${node.height/2}"
                                             stroke="currentColor" stroke-width="2" fill="rgba(253,126,20,0.15)"/>
                                </svg>
                                <div class="decision-text">${node.text}</div>
                            </div>
                        `;
                    
                    case 'fork':
                        return `
                            <div title="Bifurcación (Fork): divide el flujo en ramas paralelas. Conecta una flecha entrante desde arriba y múltiples flechas salientes hacia abajo."
                                 style="width:${node.width}px;display:flex;flex-direction:column;align-items:center;">
                                <div class="uml-fork" style="width:${node.width}px;"></div>
                                <div class="node-text" style="margin-top:6px;font-size:10px;color:#aaa;">Fork</div>
                            </div>
                        `;
                    case 'union':
                        return `
                            <div title="Unión (Join): une múltiples ramas paralelas en un único flujo. Conecta flechas entrantes desde arriba y una saliente hacia abajo."
                                 style="width:${node.width}px;display:flex;flex-direction:column;align-items:center;">
                                <div class="uml-fork" style="width:${node.width}px;"></div>
                                <div class="node-text" style="margin-top:6px;font-size:10px;color:#aaa;">Join</div>
                            </div>
                        `;
                    
                    case 'end':
                        return `
                            <div class="uml-final"></div>
                            <div class="node-text">${node.text}</div>
                        `;
                    
                    case 'initial':
                        return `
                            <div class="uml-initial"></div>
                            <div class="node-text">${node.text}</div>
                        `;
                    
                    case 'state':
                        return `
                            <div class="uml-state">${node.text}</div>
                        `;
                    
                    case 'final':
                        return `
                            <div class="uml-final"></div>
                            <div class="node-text">${node.text}</div>
                        `;
                    
                    case 'history':
                        return `
                            <div class="uml-history">H</div>
                            <div class="node-text">${node.text}</div>
                        `;
                    
                    case 'component':
                        return `
                            <div class="uml-component">
                                <svg style="position:absolute;top:6px;right:6px;" width="24" height="20"
                                     viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="7" y="0" width="17" height="20" stroke="currentColor" stroke-width="1.6" fill="none" rx="1"/>
                                    <rect x="0" y="3" width="11" height="5" stroke="currentColor" stroke-width="1.4" fill="rgba(0,0,0,0.35)" rx="1"/>
                                    <rect x="0" y="12" width="11" height="5" stroke="currentColor" stroke-width="1.4" fill="rgba(0,0,0,0.35)" rx="1"/>
                                </svg>
                                <div style="padding-right:30px;word-break:break-word;">${node.text}</div>
                            </div>
                        `;
                    
                    case 'port':
                        return `
                            <div class="uml-port"></div>
                            <div class="node-text">${node.text}</div>
                        `;
                    
                    case 'node':
                        return `
                            <div class="uml-node">
                                <svg style="position:absolute;top:0;right:0;" width="${Math.min(node.width*0.35,55)}" height="${Math.min(node.height*0.35,40)}"
                                     viewBox="0 0 55 40" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Cara superior del cubo 3D -->
                                    <polygon points="10,0 55,0 55,25 10,25" fill="rgba(13,110,253,0.15)" stroke="currentColor" stroke-width="1.5"/>
                                    <!-- Cara lateral derecha del cubo -->
                                    <polygon points="55,0 55,25 45,40 45,15" fill="rgba(13,110,253,0.08)" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <div class="node-header">${node.text}</div>
                                <div class="node-content"></div>
                            </div>
                        `;
                    
                    case 'device':
                        return `
                            <div class="node-device">
                                <div class="node-header">${node.text}</div>
                                <div class="device-screen"></div>
                            </div>
                        `;
                    
                    case 'artifact':
                        return `
                            <div class="uml-artifact">${node.text}</div>
                        `;
                    
                    case 'enum':
                        return `
                            <div class="uml-class">
                                <div class="class-name" style="font-style:italic">&laquo;enumeration&raquo;<br>${node.text}</div>
                                <div class="class-attributes">
                                    ${node.attributes ? node.attributes.split('\n').map(a =>
                                        `<div class="class-attribute">${a}</div>`).join('') : ''}
                                </div>
                            </div>
                        `;

                    case 'object':
                        return `
                            <div class="uml-class">
                                <div class="class-name" style="text-decoration:underline">${node.text}</div>
                                <div class="class-attributes">
                                    ${node.attributes ? node.attributes.split('\n').map(a =>
                                        `<div class="class-attribute">${a}</div>`).join('') : ''}
                                </div>
                            </div>
                        `;

                    case 'valor':
                        return `
                            <div class="uml-activity" style="font-size:11px">${node.text}</div>
                        `;

                    case 'message':
                        return `
                            <div style="padding:6px 10px;font-size:12px;color:inherit;">${node.text}</div>
                        `;

                    case 'link':
                        return `
                            <div style="padding:4px 8px;font-size:11px;color:inherit;font-style:italic">${node.text}</div>
                        `;

                    case 'event':
                        return `
                            <div style="padding:4px 8px;font-size:11px;color:inherit;border:1px dashed currentColor;border-radius:4px;">${node.text}</div>
                        `;

                    case 'constraint':
                        return `
                            <div style="padding:4px 8px;font-size:11px;color:inherit;">{${node.text}}</div>
                        `;

                    case 'required':
                        return `
                            <div style="padding:4px 8px;font-size:11px;color:inherit;font-style:italic">&laquo;required&raquo;<br>${node.text}</div>
                        `;

                    // ── Diagrama de Paquetes ──────────────────────────
                    case 'package':
                        return `
                            <div style="width:100%;height:100%;box-sizing:border-box;border:2px solid currentColor;border-radius:2px;position:relative;display:flex;flex-direction:column;">
                                <div style="position:absolute;top:-18px;left:0;background:currentColor;color:var(--bg-deep,#0d0d1a);font-size:10px;font-weight:700;padding:2px 10px;border-radius:2px 2px 0 0;white-space:nowrap;max-width:60%;">${node.text}</div>
                                <div style="flex:1"></div>
                            </div>
                        `;

                    // ── Diagrama de Tiempos ──────────────────────────
                    case 'lifeline-timing':
                        return `
                            <div style="display:flex;align-items:center;gap:6px;height:100%;padding:2px 6px;">
                                <div style="font-size:11px;font-weight:700;color:inherit;white-space:nowrap">${node.text}</div>
                                <div style="flex:1;height:2px;background:repeating-linear-gradient(to right,currentColor 0,currentColor 6px,transparent 6px,transparent 12px)"></div>
                            </div>
                        `;

                    case 'state-timing':
                        return `
                            <div style="padding:4px 10px;font-size:11px;font-weight:600;border:1.5px solid currentColor;border-radius:3px;background:rgba(255,255,255,.06)">${node.text}</div>
                        `;

                    case 'event-timing':
                        return `
                            <div style="display:flex;flex-direction:column;align-items:center;font-size:10px;color:inherit">
                                <div style="width:1px;height:12px;background:currentColor"></div>
                                <div style="font-weight:600">${node.text}</div>
                            </div>
                        `;

                    case 'timeline':
                        return `
                            <div style="display:flex;align-items:center;width:100%;height:100%;padding:0 4px">
                                <div style="flex:1;height:2px;background:currentColor"></div>
                                <div style="width:0;height:0;border-top:5px solid transparent;border-bottom:5px solid transparent;border-left:8px solid currentColor"></div>
                            </div>
                        `;

                    case 'constraint-timing':
                        return `
                            <div style="padding:3px 8px;font-size:10px;border:1px dashed currentColor;border-radius:4px;color:inherit">{${node.text}}</div>
                        `;

                    default:
                        return `<div class="node-text">${node.text}</div>`;
                }
            }

            // ── Helper: detecta si el canvas está en tema claro ──────
            isLightTheme() {
                return document.body.classList.contains('light-theme');
            }

            // ── Helper: color de texto adaptativo ─────────────────────
            textColor() {
                return this.isLightTheme() ? '#222' : '#ddd';
            }

            // ── Helper: fondo de etiqueta adaptativo ──────────────────
            labelBg() {
                return this.isLightTheme() ? 'rgba(255,255,255,0.88)' : 'rgba(10,10,10,0.75)';
            }

            renderConnection(conn) {
                const fromNode = this.nodes.find(n => n.id === conn.fromNode);
                const toNode   = this.nodes.find(n => n.id === conn.toNode);
                
                if (!fromNode || !toNode) return;
                
                const fromPoint = this.getConnectionPoint(fromNode, conn.fromSide);
                const toPoint   = this.getConnectionPoint(toNode,   conn.toSide);
                
                const svgNS = 'http://www.w3.org/2000/svg';
                const svg   = document.createElementNS(svgNS, 'svg');
                svg.setAttribute('class', 'connection-line');
                svg.setAttribute('width',    '100%');
                svg.setAttribute('height',   '100%');
                svg.setAttribute('overflow', 'visible');
                svg.style.pointerEvents = 'none';  // none en el SVG container; los <path> tienen pointer-events:stroke
                svg.style.position = 'absolute';
                svg.style.top      = '0';
                svg.style.left     = '0';
                svg.style.zIndex   = '12'; // sobre nodos (z:10), bajo hitbox (z:50)

                const isSelected = this.selectedConnection &&
                    this.selectedConnection.fromNode === conn.fromNode &&
                    this.selectedConnection.toNode   === conn.toNode &&
                    this.selectedConnection.fromSide === conn.fromSide &&
                    this.selectedConnection.toSide   === conn.toSide;

                const arrowStyle = this.arrowStyles[conn.type] || this.arrowStyles.asociacion;
                const strokeColor = isSelected ? '#facc15' : arrowStyle.stroke;

                // ── Diagrama de Secuencia: flechas HORIZONTALES ───────
                // Horizontal si: tipo mensaje explícito, o viene de zona continua (abs-/seq-)
                const seqTypes  = ['mensaje-sincrono','mensaje-asincrono','mensaje-retorno'];
                const usesAbsY  = (conn.fromSide && conn.fromSide.startsWith('abs-')) ||
                                  (conn.toSide   && conn.toSide.startsWith('abs-'))   ||
                                  (conn.fromSide && conn.fromSide.startsWith('seq-')) ||
                                  (conn.toSide   && conn.toSide.startsWith('seq-'));
                const isSeqMsg  = seqTypes.includes(conn.type) ||
                    (this.diagramType === 'sequence' && usesAbsY);

                let d;
                if (isSeqMsg) {
                    // Y viene de fromPoint (coordenada abs exacta o midpoint)
                    const msgY = fromPoint.y;
                    const x1   = fromPoint.x;
                    const x2   = toPoint.x;
                    d = `M ${x1} ${msgY} L ${x2} ${msgY}`;
                } else {
                    // Curvas Bezier suaves para todos los demás diagramas
                    const dx  = toPoint.x - fromPoint.x;
                    const dy  = toPoint.y - fromPoint.y;
                    let cx1, cy1, cx2, cy2;

                    // Determinar dirección de salida del borde para calcular los puntos de control
                    // Soporta: 'left','right','top','bottom', abs- (secuencia), abs2- (libre)
                    const getEdgeDir = (sideStr) => {
                        if (!sideStr) return 'right';
                        if (sideStr.startsWith('edge-')) {
                            const parts = sideStr.split('-');
                            if (parts.length >= 3) {
                                const edge = parts[1] + '-' + parts[2]; // "top-edge", "bottom-edge", etc.
                                if (edge === 'left-edge')   return 'left';
                                if (edge === 'right-edge')  return 'right';
                                if (edge === 'top-edge')    return 'top';
                                if (edge === 'bottom-edge') return 'bottom';
                            }
                            return 'right';
                        }
                        if (sideStr.startsWith('abs2-')) {
                            const m = sideStr.match(/^abs2-[^-]+-[^-]+-(.+)$/);
                            const edge = m ? m[1] : '';
                            if (edge === 'left-edge')   return 'left';
                            if (edge === 'right-edge')  return 'right';
                            if (edge === 'top-edge')    return 'top';
                            if (edge === 'bottom-edge') return 'bottom';
                            return 'right';
                        }
                        if (sideStr.startsWith('abs-')) {
                            return sideStr.endsWith('-right') ? 'right' : 'left';
                        }
                        return sideStr;
                    };
                    const fromDir = getEdgeDir(conn.fromSide);
                    const toDir   = getEdgeDir(conn.toSide);

                    if (conn.fromNode === conn.toNode) {
                        if (fromPoint.x !== toPoint.x || fromPoint.y !== toPoint.y) {
                            const dist   = Math.sqrt(dx*dx + dy*dy);
                            const tension = Math.min(Math.max(dist * 0.4, 60), 220);

                            if (fromDir === 'right')       { cx1 = fromPoint.x + tension; cy1 = fromPoint.y; }
                            else if (fromDir === 'left')   { cx1 = fromPoint.x - tension; cy1 = fromPoint.y; }
                            else if (fromDir === 'bottom') { cx1 = fromPoint.x; cy1 = fromPoint.y + tension; }
                            else                           { cx1 = fromPoint.x; cy1 = fromPoint.y - tension; }

                            if (toDir === 'left')          { cx2 = toPoint.x - tension; cy2 = toPoint.y; }
                            else if (toDir === 'right')    { cx2 = toPoint.x + tension; cy2 = toPoint.y; }
                            else if (toDir === 'top')      { cx2 = toPoint.x; cy2 = toPoint.y - tension; }
                            else                           { cx2 = toPoint.x; cy2 = toPoint.y + tension; }

                            d = `M ${fromPoint.x} ${fromPoint.y} C ${cx1} ${cy1}, ${cx2} ${cy2}, ${toPoint.x} ${toPoint.y}`;
                        } else {
                            const lx = fromPoint.x;
                            const ly = fromPoint.y;
                            const loopSize = 80;

                            if (fromDir === 'right') {
                                d = `M ${lx} ${ly} C ${lx + loopSize} ${ly - loopSize / 2}, ${lx + loopSize} ${ly + loopSize / 2}, ${lx + 40} ${ly}`;
                            } else if (fromDir === 'left') {
                                d = `M ${lx} ${ly} C ${lx - loopSize} ${ly - loopSize / 2}, ${lx - loopSize} ${ly + loopSize / 2}, ${lx - 40} ${ly}`;
                            } else if (fromDir === 'top') {
                                d = `M ${lx} ${ly} C ${lx - loopSize / 2} ${ly - loopSize}, ${lx + loopSize / 2} ${ly - loopSize}, ${lx} ${ly - 40}`;
                            } else if (fromDir === 'bottom') {
                                d = `M ${lx} ${ly} C ${lx - loopSize / 2} ${ly + loopSize}, ${lx + loopSize / 2} ${ly + loopSize}, ${lx} ${ly + 40}`;
                            }
                        }
                    } else {
                        const dist   = Math.sqrt(dx*dx + dy*dy);
                        const tension = Math.min(Math.max(dist * 0.4, 60), 220);

                        if (fromDir === 'right')       { cx1 = fromPoint.x + tension; cy1 = fromPoint.y; }
                        else if (fromDir === 'left')   { cx1 = fromPoint.x - tension; cy1 = fromPoint.y; }
                        else if (fromDir === 'bottom') { cx1 = fromPoint.x; cy1 = fromPoint.y + tension; }
                        else                           { cx1 = fromPoint.x; cy1 = fromPoint.y - tension; }

                        if (toDir === 'left')          { cx2 = toPoint.x - tension; cy2 = toPoint.y; }
                        else if (toDir === 'right')    { cx2 = toPoint.x + tension; cy2 = toPoint.y; }
                        else if (toDir === 'top')      { cx2 = toPoint.x; cy2 = toPoint.y - tension; }
                        else                           { cx2 = toPoint.x; cy2 = toPoint.y + tension; }

                        d = `M ${fromPoint.x} ${fromPoint.y} C ${cx1} ${cy1}, ${cx2} ${cy2}, ${toPoint.x} ${toPoint.y}`;
                    }

                    // ── Override with selected line style ─────────────────
                    const lineStyle = conn.lineStyle || 'bezier';
                    if (lineStyle === 'straight') {
                        d = `M ${fromPoint.x} ${fromPoint.y} L ${toPoint.x} ${toPoint.y}`;
                    } else if (lineStyle === 'orthogonal') {
                        const midX = fromPoint.x + (toPoint.x - fromPoint.x) * 0.5;
                        const midY = fromPoint.y + (toPoint.y - fromPoint.y) * 0.5;
                        const fd = fromDir || 'right';
                        if (fd === 'right' || fd === 'left') {
                            d = `M ${fromPoint.x} ${fromPoint.y} L ${midX} ${fromPoint.y} L ${midX} ${toPoint.y} L ${toPoint.x} ${toPoint.y}`;
                        } else {
                            d = `M ${fromPoint.x} ${fromPoint.y} L ${fromPoint.x} ${midY} L ${toPoint.x} ${midY} L ${toPoint.x} ${toPoint.y}`;
                        }
                    } else if (lineStyle === 'arc') {
                        const ddx = toPoint.x - fromPoint.x;
                        const ddy = toPoint.y - fromPoint.y;
                        const r   = Math.sqrt(ddx*ddx + ddy*ddy) * 0.65;
                        const sw  = ddx >= 0 ? 1 : 0;
                        d = `M ${fromPoint.x} ${fromPoint.y} A ${r} ${r} 0 0 ${sw} ${toPoint.x} ${toPoint.y}`;
                    }
                    // 'bezier' uses the d already computed above

                    // ── Apply waypoints if any ────────────────────────────
                    if (conn.waypoints && conn.waypoints.length > 0) {
                        const pts = [fromPoint, ...conn.waypoints, toPoint];
                        if (ls === 'straight' || ls === 'orthogonal') {
                            d = 'M ' + pts.map(p=>`${p.x} ${p.y}`).join(' L ');
                        } else {
                            // Smooth curve through waypoints
                            let dp = `M ${pts[0].x} ${pts[0].y}`;
                            for (let k=1; k<pts.length; k++) {
                                const prev = pts[k-1];
                                const curr = pts[k];
                                const nx   = k < pts.length-1 ? pts[k+1] : curr;
                                const cpx1 = prev.x + (curr.x - prev.x) * 0.4;
                                const cpy1 = prev.y;
                                const cpx2 = curr.x - (curr.x - prev.x) * 0.1;
                                const cpy2 = curr.y;
                                dp += ` C ${cpx1} ${cpy1}, ${cpx2} ${cpy2}, ${curr.x} ${curr.y}`;
                            }
                            d = dp;
                        }
                    }
                }

                // Hitbox handled by overlay layer (_renderConnectionHitboxOverlay)

                // ── Waypoint handles ──────────────────────────────────────
                if (this.selectedConnection === conn && conn.waypoints) {
                    conn.waypoints.forEach((wp, wi) => {
                        const handle = document.createElementNS(svgNS, 'circle');
                        handle.setAttribute('cx', wp.x);
                        handle.setAttribute('cy', wp.y);
                        handle.setAttribute('r', '6');
                        handle.setAttribute('fill', 'var(--primary)');
                        handle.setAttribute('stroke', '#fff');
                        handle.setAttribute('stroke-width', '2');
                        handle.style.cursor = 'move';
                        handle.style.pointerEvents = 'all';
                        handle.addEventListener('mousedown', (ev) => {
                            ev.stopPropagation();
                            const move = (me) => {
                                const p = this.getCanvasPos(me);
                                conn.waypoints[wi] = { x: p.x, y: p.y };
                                this.render();
                            };
                            const up = () => {
                                document.removeEventListener('mousemove', move);
                                document.removeEventListener('mouseup', up);
                                this.pushToHistory();
                                this.unsavedChanges = true;
                            };
                            document.addEventListener('mousemove', move);
                            document.addEventListener('mouseup', up);
                        });
                        // Double-click on waypoint to remove it
                        handle.addEventListener('dblclick', (ev) => {
                            ev.stopPropagation();
                            conn.waypoints.splice(wi, 1);
                            this.render();
                            this.pushToHistory();
                        });
                        svg.appendChild(handle);
                    });
                }

                // ── Línea visual ──────────────────────────────────────
                const path = document.createElementNS(svgNS, 'path');
                path.setAttribute('d',            d);
                path.setAttribute('stroke',       strokeColor);
                path.setAttribute('stroke-width', isSelected ? '3' : arrowStyle['stroke-width']);
                path.setAttribute('fill',         'none');
                path.style.pointerEvents = 'none';

                if (arrowStyle['stroke-dasharray']) {
                    path.setAttribute('stroke-dasharray', arrowStyle['stroke-dasharray']);
                }
                if (arrowStyle['marker-start']) {
                    path.setAttribute('marker-start', arrowStyle['marker-start']);
                }
                if (arrowStyle['marker-end']) {
                    path.setAttribute('marker-end', arrowStyle['marker-end']);
                }
                // Resaltar con color y grosor si está seleccionada
                if (isSelected) {
                    path.setAttribute('stroke-width', '4');
                    path.setAttribute('stroke', '#facc15');
                }
                svg.appendChild(path);

                // ── Calcular midpoint para etiquetas ──────────────────
                let mx, my;
                if (isSeqMsg) {
                    const x1 = fromNode.x + fromNode.width / 2;
                    const x2 = toNode.x   + toNode.width   / 2;
                    mx = (x1 + x2) / 2;
                    my = fromPoint.y;
                } else {
                    mx = (fromPoint.x + toPoint.x) / 2;
                    my = (fromPoint.y + toPoint.y) / 2;
                    
                    // Ajustar offset si el label está dentro de un nodo
                    // Calcular vector perpendicular a la línea para desplazar el label
                    const dx = toPoint.x - fromPoint.x;
                    const dy = toPoint.y - fromPoint.y;
                    const len = Math.sqrt(dx * dx + dy * dy);
                    
                    if (len > 0) {
                        // Vector perpendicular normalizado
                        const perpX = -dy / len;
                        const perpY = dx / len;
                        
                        // Distancia de offset (30px)
                        const offset = 30;
                        const offsetX = perpX * offset;
                        const offsetY = perpY * offset;
                        
                        // Verificar si el midpoint está dentro de algún nodo
                        let isInsideNode = false;
                        for (const n of this.nodes) {
                            if (n.id === fromNode.id || n.id === toNode.id) continue;
                            if (mx >= n.x && mx <= n.x + n.width &&
                                my >= n.y && my <= n.y + n.height) {
                                isInsideNode = true;
                                break;
                            }
                        }
                        
                        // Si está dentro, desplazar perpendicular a la línea
                        if (isInsideNode) {
                            mx += offsetX;
                            my += offsetY;
                        }
                    }
                }

                // ── Etiqueta «include» / «extend» ─────────────────────
                if (conn.type === 'include' || conn.type === 'extend') {
                    const lbl = document.createElementNS(svgNS, 'text');
                    lbl.setAttribute('x',           mx);
                    lbl.setAttribute('y',           my - 8);
                    lbl.setAttribute('text-anchor', 'middle');
                    lbl.setAttribute('fill',        isSelected ? '#facc15' : this.textColor());
                    lbl.setAttribute('font-size',   '11');
                    lbl.setAttribute('font-style',  'italic');
                    lbl.setAttribute('font-family', "'Segoe UI', sans-serif");
                    lbl.style.pointerEvents = 'none';
                    lbl.textContent = `«${conn.type}»`;
                    svg.appendChild(lbl);
                }

                // ── Label personalizada (todas las flechas, incluyendo secuencia) ──
                if (conn.label) {
                    // Fondo semitransparente para legibilidad
                    const labelOffsetY = (conn.type === 'include' || conn.type === 'extend') ? -26 : -8;
                    
                    // Aplicar offsets personalizados si existen
                    const userOffsetX = conn.labelOffsetX || 0;
                    const userOffsetY = conn.labelOffsetY || 0;
                    const finalMx = mx + userOffsetX;
                    const finalMy = my + labelOffsetY + userOffsetY;
                    
                    const bg = document.createElementNS(svgNS, 'rect');
                    const textLen = conn.label.length * 6.5 + 10;
                    bg.setAttribute('x',      finalMx - textLen / 2);
                    bg.setAttribute('y',      finalMy - 13);
                    bg.setAttribute('width',  textLen);
                    bg.setAttribute('height', 16);
                    bg.setAttribute('rx',     3);
                    bg.setAttribute('fill',   isSelected ? 'rgba(250,204,21,0.25)' : this.labelBg());
                    if (isSelected) {
                        bg.setAttribute('stroke', '#facc15');
                        bg.setAttribute('stroke-width', '1.5');
                        bg.style.cursor = 'move';
                        bg.style.pointerEvents = 'auto';
                        bg.addEventListener('mousedown', (e) => this.startDraggingLabel(e, conn));
                    } else {
                        bg.style.pointerEvents = 'none';
                    }
                    svg.appendChild(bg);

                    const lbl = document.createElementNS(svgNS, 'text');
                    lbl.setAttribute('x',           finalMx);
                    lbl.setAttribute('y',           finalMy);
                    lbl.setAttribute('text-anchor', 'middle');
                    lbl.setAttribute('fill',        isSelected ? '#facc15' : this.textColor());
                    lbl.setAttribute('font-size',   '11');
                    lbl.setAttribute('font-weight', isSelected ? 'bold' : 'normal');
                    lbl.setAttribute('font-family', "'Segoe UI', sans-serif");
                    if (isSelected) {
                        lbl.style.pointerEvents = 'auto';
                        lbl.style.cursor = 'move';
                        lbl.addEventListener('mousedown', (e) => this.startDraggingLabel(e, conn));
                    } else {
                        lbl.style.pointerEvents = 'none';
                    }
                    lbl.textContent = conn.label;
                    svg.appendChild(lbl);
                }

                // ── Indicador visual de selección (pequeño círculo en midpoint) ──
                if (isSelected) {
                    const dot = document.createElementNS(svgNS, 'circle');
                    dot.setAttribute('cx',   mx);
                    dot.setAttribute('cy',   my);
                    dot.setAttribute('r',    5);
                    dot.setAttribute('fill', '#facc15');
                    dot.style.pointerEvents = 'none';
                    svg.appendChild(dot);
                }
                
                document.getElementById('diagramCanvas').appendChild(svg);
            }

            _normalizeUsecaseConnectionsAfterLayout() {
                if (this.diagramType !== 'usecase') return;

                this.connections.forEach(conn => {
                    const fromNode = this.nodes.find(n => n.id === conn.fromNode);
                    const toNode   = this.nodes.find(n => n.id === conn.toNode);

                    if (fromNode) conn.fromSide = this._normalizeUsecaseConnectionSide(fromNode, conn.fromSide);
                    if (toNode)   conn.toSide   = this._normalizeUsecaseConnectionSide(toNode,   conn.toSide);
                });
            }

            _normalizeUsecaseConnectionSide(node, side) {
                if (!side) return side;
                if (side.startsWith('abs2-')) {
                    const m = side.match(/^abs2-(-?\d+(?:\.\d+)?)-(-?\d+(?:\.\d+)?)-(.+)$/);
                    if (m) {
                        const absX = parseFloat(m[1]);
                        const absY = parseFloat(m[2]);
                        return this._closestNodeEdge(node, absX, absY);
                    }
                }
                if (side.startsWith('abs-')) {
                    const m = side.match(/^abs-(-?\d+(?:\.\d+)?)-(.+)$/);
                    if (m) {
                        const absY = parseFloat(m[1]);
                        const x    = node.x + node.width / 2;
                        const y    = isNaN(absY) ? node.y + node.height / 2 : absY;
                        return this._closestNodeEdge(node, x, y);
                    }
                }
                return side;
            }

            _closestNodeEdge(node, x, y) {
                const leftDist   = Math.abs(x - node.x);
                const rightDist  = Math.abs(x - (node.x + node.width));
                const topDist    = Math.abs(y - node.y);
                const bottomDist = Math.abs(y - (node.y + node.height));
                const minDist    = Math.min(leftDist, rightDist, topDist, bottomDist);
                if (minDist === leftDist) return 'left';
                if (minDist === rightDist) return 'right';
                if (minDist === topDist) return 'top';
                return 'bottom';
            }

            getConnectionPoint(node, side) {
                // ── Formato edge-side-percent: coordenada relativa a lo largo del borde ──
                // Formato exacto: "edge-" + side + "-" + percent
                // donde side es "top-edge", "bottom-edge", "left-edge", "right-edge"
                // y percent es un número entre 0 y 1
                if (side && side.startsWith('edge-')) {
                    const parts = side.split('-');
                    if (parts.length >= 3) {
                        const edgeSide = parts[1] + '-' + parts[2]; // "top-edge", "bottom-edge", etc.
                        const percent = parseFloat(parts[3]);
                        if (!isNaN(percent)) {
                            if (edgeSide === 'top-edge') {
                                return {
                                    x: node.x + percent * node.width,
                                    y: node.y
                                };
                            } else if (edgeSide === 'bottom-edge') {
                                return {
                                    x: node.x + percent * node.width,
                                    y: node.y + node.height
                                };
                            } else if (edgeSide === 'left-edge') {
                                return {
                                    x: node.x,
                                    y: node.y + percent * node.height
                                };
                            } else if (edgeSide === 'right-edge') {
                                return {
                                    x: node.x + node.width,
                                    y: node.y + percent * node.height
                                };
                            }
                        }
                    }
                }
                
                // ── Formato abs2-X-Y-edgename: coordenada libre en cualquier borde ──
                // Formato exacto: "abs2-" + X + "-" + Y + "-" + edgeName
                // donde edgeName puede ser "top-edge", "bottom-edge", "left-edge", "right-edge"
                if (side && side.startsWith('abs2-')) {
                    // Extraer usando regex para evitar problemas con guiones en edgeName
                    const m = side.match(/^abs2-(-?\d+(?:\.\d+)?)-(-?\d+(?:\.\d+)?)-(.+)$/);
                    if (m) {
                        const absX    = parseFloat(m[1]);
                        const absY    = parseFloat(m[2]);
                        const edge    = m[3]; // "top-edge", "bottom-edge", etc.
                        // Usar coordenadas absolutas guardadas — son relativas al canvas,
                        // no al nodo, así que se devuelven directamente.
                        return {
                            x: isNaN(absX) ? node.x + node.width  / 2 : absX,
                            y: isNaN(absY) ? node.y + node.height / 2 : absY
                        };
                    }
                    // Fallback si el regex falla
                    return { x: node.x + node.width / 2, y: node.y + node.height / 2 };
                }
                // ── Formato abs-{Y}-{left|right}: coordenada Y absoluta (secuencia) ──
                if (side && side.startsWith('abs-')) {
                    const lastDash = side.lastIndexOf('-');
                    const absY     = parseFloat(side.slice(4, lastDash));
                    const isRight  = side.slice(lastDash + 1) === 'right';
                    // Para lifeline y actor en secuencia: el eje X es siempre el centro del nodo
                    return {
                        x: node.x + node.width / 2,
                        y: isNaN(absY) ? node.y + node.height / 2 : absY
                    };
                }
                // ── Formato legacy seq-N (compatibilidad con diagramas guardados) ──
                if (side && side.startsWith('seq-')) {
                    const idx    = parseInt(side.split('-')[1], 10) || 0;
                    const HEADER = 36;
                    const step   = Math.floor((node.height - HEADER) / 9);
                    return {
                        x: node.x + node.width / 2,
                        y: node.y + HEADER + idx * step
                    };
                }
                switch(side) {
                    case 'left':   return { x: node.x,                  y: node.y + node.height / 2 };
                    case 'right':  return { x: node.x + node.width,     y: node.y + node.height / 2 };
                    case 'top':    return { x: node.x + node.width / 2, y: node.y };
                    case 'bottom': return { x: node.x + node.width / 2, y: node.y + node.height };
                    default:       return { x: node.x + node.width / 2, y: node.y + node.height / 2 };
                }
            }

            selectNode(node) {
                this.selectedNode = node;
                this.selectedConnection = null;
                this.render();
                this.updatePropertiesPanel();
            }

            selectConnection(conn) {
                this.selectedConnection = conn;
                this.selectedNode = null;
                // Sync line style picker to selected connection's style
                if (typeof setConnLineStyle === 'function' && conn.lineStyle) {
                    setConnLineStyle(conn.lineStyle, false);
                }
                // Show delete button
                const delBtn = document.getElementById('deleteConnBtn');
                if (delBtn) { delBtn.style.display = 'inline-flex'; }
                this.render();
                this.updatePropertiesPanel();
            }

            startDraggingNode(e, node) {
                e.preventDefault();
                e.stopPropagation();
                this.dragging = true;
                this.selectedNode = node;
                this.selectedConnection = null;
                // Ajustar offset por zoom
                const z = (typeof _zoom !== 'undefined') ? _zoom : 1;
                this.dragStartX = e.clientX / z - node.x;
                this.dragStartY = e.clientY / z - node.y;
                this._dragZoom  = z;
                this._lastRenderTime = 0;
                this._dragRafId = null;  // RAF id para throttle
                
                const onMouseMove = (e) => {
                    if (!this.dragging) return;
                    
                    const z    = this._dragZoom || 1;
                    const newX = e.clientX / z - this.dragStartX;
                    const newY = e.clientY / z - this.dragStartY;
                    
                    // ── Caso de uso: confinado dentro del sistema ──────────────
                    if (node.type === 'usecase') {
                        const system = this.nodes.find(n => n.type === 'system');
                        if (system) {
                            const PAD     = 12;
                            const HEADER  = 40; // altura del título del sistema
                            // Clampear posición: el usecase no puede salir del sistema
                            const minX = system.x + PAD;
                            const minY = system.y + HEADER;
                            const maxX = system.x + system.width  - node.width  - PAD;
                            const maxY = system.y + system.height - node.height - PAD;
                            node.x = Math.min(Math.max(newX, minX), Math.max(minX, maxX));
                            node.y = Math.min(Math.max(newY, minY), Math.max(minY, maxY));
                        } else {
                            node.x = newX;
                            node.y = newY;
                        }
                    }
                    // ── Actor: libre, pero NO puede entrar al sistema ──
                    else if (node.type === 'actor') {
                        const system = this.nodes.find(n => n.type === 'system');
                        if (system) {
                            const wouldOverlapX = newX + node.width  > system.x + 10 &&
                                                  newX               < system.x + system.width  - 10;
                            const wouldOverlapY = newY + node.height > system.y + 10 &&
                                                  newY               < system.y + system.height - 10;

                            if (wouldOverlapX && wouldOverlapY) {
                                // Empujar al borde horizontal más cercano
                                const distLeft  = Math.abs(newX + node.width  - system.x);
                                const distRight = Math.abs(newX               - (system.x + system.width));
                                if (distLeft <= distRight) {
                                    node.x = system.x - node.width - 10;
                                } else {
                                    node.x = system.x + system.width + 10;
                                }
                                node.y = newY;
                            } else {
                                node.x = newX;
                                node.y = newY;
                            }
                        } else {
                            node.x = newX;
                            node.y = newY;
                        }
                    }
                    else if (this.diagramType === 'sequence' && node.type === 'activation') {
                        // Activación: solo vertical, se puede cambiar de lifeline arrastrando horizontalmente
                        const lifelines = this.nodes.filter(n => n.type === 'lifeline' || n.type === 'actor');
                        if (lifelines.length > 0) {
                            const nearest = lifelines.reduce((best, ll) => {
                                const dist = Math.abs(newX + node.width / 2 - (ll.x + ll.width / 2));
                                return dist < best.dist ? { ll, dist } : best;
                            }, { ll: lifelines[0], dist: Infinity }).ll;

                            node.x = nearest.x + nearest.width / 2 - node.width / 2;
                            node.y = Math.max(nearest.y + 36, newY);
                            this.activationParents[node.id] = nearest.id;
                        } else {
                            node.x = newX;
                            node.y = newY;
                        }
                    }
                    else {
                        node.x = newX;
                        node.y = newY;
                    }
                    
                    // Render con throttling para evitar parpadeo durante arrastre
                    const now = Date.now();
                    if (now - this._lastRenderTime > 16) { // ~60fps
                        this.render();
                        this._lastRenderTime = now;
                    }
                };
                
                const onMouseUp = () => {
                    this.dragging = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    this.render(); // Render final después del arrastre
                    this.pushToHistory();
                    this.unsavedChanges = true;
                };
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            }

            startDraggingLabel(e, conn) {
                e.preventDefault();
                e.stopPropagation();
                
                const rect = document.getElementById('canvasContainer').getBoundingClientRect();
                const z = (typeof _zoom !== 'undefined') ? _zoom : 1;
                const pan = (typeof _panX !== 'undefined') ? { x: _panX, y: _panY } : { x: 0, y: 0 };
                
                const startX = (e.clientX - rect.left - pan.x) / z;
                const startY = (e.clientY - rect.top - pan.y) / z;
                
                const initialOffsetX = conn.labelOffsetX || 0;
                const initialOffsetY = conn.labelOffsetY || 0;
                
                const onMouseMove = (moveEvent) => {
                    const currentX = (moveEvent.clientX - rect.left - pan.x) / z;
                    const currentY = (moveEvent.clientY - rect.top - pan.y) / z;
                    
                    const deltaX = currentX - startX;
                    const deltaY = currentY - startY;
                    
                    conn.labelOffsetX = Math.round(initialOffsetX + deltaX);
                    conn.labelOffsetY = Math.round(initialOffsetY + deltaY);
                    
                    this.render();
                };
                
                const onMouseUp = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    this.pushToHistory();
                    this.unsavedChanges = true;
                    this.updatePropertiesPanel();
                };
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            }

            startResizing(e, node) {
                e.preventDefault();
                e.stopPropagation();
                this.resizing = true;
                this.selectedNode = node;
                const z = (typeof _zoom !== 'undefined') ? _zoom : 1;
                this._resizeZoom   = z;
                this.resizeStartX  = e.clientX / z;
                this.resizeStartY  = e.clientY / z;
                this.resizeStartWidth  = node.width;
                this.resizeStartHeight = node.height;

                const verticalOnly = node.type === 'activation';
                
                const onMouseMove = (e) => {
                    if (!this.resizing) return;
                    const rz = this._resizeZoom || 1;
                    const dx = e.clientX / rz - this.resizeStartX;
                    const dy = e.clientY / rz - this.resizeStartY;

                    if (verticalOnly) {
                        node.height = Math.max(20, this.resizeStartHeight + dy);
                    } else {
                        node.width  = Math.max(200, this.resizeStartWidth  + dx);
                        node.height = Math.max(150, this.resizeStartHeight + dy);
                    }
                    
                    this.render();
                };
                
                const onMouseUp = () => {
                    this.resizing = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    this.pushToHistory();
                    this.unsavedChanges = true;
                };
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            }

            /** Inicia una conexión desde una zona continua (lifeline / activation) */
            _startConnectionFromZone(e, fromNode, syntheticSide) {
                e.preventDefault();
                this.connecting = true;
                const pt = this.getConnectionPoint(fromNode, syntheticSide);
                this.connectionStart = {
                    node: fromNode.id,
                    side: syntheticSide,
                    x: pt.x,
                    y: pt.y
                };
                this.previewElement.style.display = 'block';
            }

            startConnection(e, fromNode) {
                e.stopPropagation();
                
                this.connecting = true;
                this.connectionStart = {
                    node: fromNode.id,
                    side: e.target.dataset.side,
                    x: this.getConnectionPoint(fromNode, e.target.dataset.side).x,
                    y: this.getConnectionPoint(fromNode, e.target.dataset.side).y
                };
                
                // Mostrar preview
                this.previewElement.style.display = 'block';
            }

            handleMouseMove(e) {
                if (!this.connecting || !this.connectionStart) return;
                
                const rect  = document.getElementById('canvasContainer').getBoundingClientRect();
                const z     = (typeof _zoom !== 'undefined') ? _zoom : 1;
                const pan   = (typeof _panX !== 'undefined') ? { x: _panX, y: _panY } : { x: 0, y: 0 };
                const mouseX = (e.clientX - rect.left - pan.x) / z;
                const mouseY = (e.clientY - rect.top  - pan.y) / z;
                
                const fromPoint = this.connectionStart;
                
                // Actualizar preview
                const midX = (fromPoint.x + mouseX) / 2;
                const midY = (fromPoint.y + mouseY) / 2;
                const d = `M ${fromPoint.x} ${fromPoint.y} Q ${midX} ${fromPoint.y}, ${midX} ${midY} T ${mouseX} ${mouseY}`;
                
                this.previewPath.setAttribute('d', d);
                
                // Aplicar estilo según el tipo de flecha seleccionado
                const arrowStyle = this.arrowStyles[this.currentArrowType] || this.arrowStyles.asociacion;
                this.previewPath.setAttribute('stroke', arrowStyle.stroke);
                this.previewPath.setAttribute('stroke-width', arrowStyle['stroke-width']);
                if (arrowStyle['stroke-dasharray']) {
                    this.previewPath.setAttribute('stroke-dasharray', arrowStyle['stroke-dasharray']);
                } else {
                    this.previewPath.removeAttribute('stroke-dasharray');
                }
                
                // Añadir flecha al preview
                if (arrowStyle['marker-end']) {
                    this.previewPath.setAttribute('marker-end', arrowStyle['marker-end']);
                }
            }

            handleMouseUp(e) {
                if (!this.connecting || !this.connectionStart) {
                    this.previewElement.style.display = 'none';
                    return;
                }
                
                const target = document.elementFromPoint(e.clientX, e.clientY);
                const isPoint    = target && target.classList.contains('connection-point');
                const isSeqZone  = target && target.classList.contains('seq-zone') && !target.classList.contains('free-zone');
                const isFreeZone = target && target.classList.contains('free-zone');
                const isZone     = isSeqZone || isFreeZone;

                if (isPoint || isZone) {
                    const toNodeId = target.dataset.node;
                    let toSide     = target.dataset.side;

                    let overrideToNodeId = null;

                    // Para zonas de secuencia: calcular Y absoluta en el canvas
                    // y redirigir a una activación si existe en esa Y
                    if (isSeqZone) {
                        const toLifeline = this.nodes.find(n => n.id === toNodeId);
                        if (toLifeline) {
                            const fromNode  = this.nodes.find(n => n.id === this.connectionStart.node);
                            const fromPoint = fromNode ? this.getConnectionPoint(fromNode, this.connectionStart.side) : null;
                            const nodeEl    = document.querySelector(`.diagram-node[data-id="${toNodeId}"]`);
                            const dropY     = nodeEl
                                ? toLifeline.y + (e.clientY - nodeEl.getBoundingClientRect().top)
                                : toLifeline.y + toLifeline.height / 2;
                            const messageY  = fromPoint ? fromPoint.y : dropY;

                            const llCenterX = toLifeline.x + toLifeline.width / 2;
                            const SNAP_MARGIN = 50; // Aumentado para detectar activaciones más lejanas
                            let targetActivation = null;
                            let bestDistance = Infinity;

                            for (const n of this.nodes) {
                                if (n.type !== 'activation') continue;
                                const actCenterX = n.x + n.width / 2;
                                if (Math.abs(actCenterX - llCenterX) > 30) continue; // Margen más amplio

                                const activationTop = n.y - SNAP_MARGIN;
                                const activationBottom = n.y + n.height + SNAP_MARGIN;
                                if (messageY < activationTop || messageY > activationBottom) continue;

                                const distance = Math.abs((n.y + n.height / 2) - messageY);
                                if (distance < bestDistance) {
                                    bestDistance = distance;
                                    targetActivation = n;
                                }
                            }

                            if (targetActivation) {
                                // Solo conectar si la activación está lo suficientemente cerca
                                const distance = Math.abs((targetActivation.y + targetActivation.height / 2) - messageY);
                                if (distance <= SNAP_MARGIN) {
                                    const clampedY = Math.min(
                                        Math.max(messageY, targetActivation.y),
                                        targetActivation.y + targetActivation.height
                                    );
                                    const side = targetActivation.x + targetActivation.width / 2 >= llCenterX
                                        ? 'seq-zone-left' : 'seq-zone-right';
                                    toSide = `abs-${Math.round(clampedY)}-${side === 'seq-zone-left' ? 'left' : 'right'}`;
                                    overrideToNodeId = targetActivation.id;
                                } else {
                                    // Activación demasiado lejana - no permitir conexión
                                    this.connecting = false;
                                    this.connectionStart = null;
                                    this.previewElement.style.display = 'none';
                                    // Mostrar mensaje de error o feedback visual
                                    this.showNotification('La activación está demasiado lejos. Acerque el mensaje a la activación para conectar.', 'warning');
                                    return;
                                }
                            } else {
                                // No hay activación cercana - no permitir conexión
                                this.connecting = false;
                                this.connectionStart = null;
                                this.previewElement.style.display = 'none';
                                this.showNotification('No hay activación cercana. Cree o acerque una activación para conectar el mensaje.', 'warning');
                                return;
                            }
                        }
                    }

                    // Para zonas de borde libre: calcular posición relativa en el borde
                    if (isFreeZone) {
                        const toNode = this.nodes.find(n => n.id === toNodeId);
                        if (toNode) {
                            const nodeEl = document.querySelector(`.diagram-node[data-id="${toNodeId}"]`);
                            const rect   = nodeEl ? nodeEl.getBoundingClientRect() : null;
                            const edgeSide = target.dataset.side;
                            let percent;
                            if (edgeSide === 'top-edge' || edgeSide === 'bottom-edge') {
                                percent = rect ? (e.clientX - rect.left) / rect.width : 0.5;
                            } else { // left-edge or right-edge
                                percent = rect ? (e.clientY - rect.top) / rect.height : 0.5;
                            }
                            toSide = `edge-${edgeSide}-${percent.toFixed(3)}`;
                        }
                    }
                    
                    // Usar activación si el snap-to-activation la eligió
                    const finalToNodeId = overrideToNodeId || toNodeId;

                    // Permitir conexiones hacia sí mismo en actividades y en estados para elementos de decisión
                    const fromNode = this.nodes.find(n => n.id === this.connectionStart.node);
                    const allowSelfConnection = this.diagramType === 'activity' || 
                                               (this.diagramType === 'state' && fromNode && (fromNode.type === 'decision' || fromNode.type === 'choice'));
                    
                    if (allowSelfConnection || this.connectionStart.node !== finalToNodeId) {
                        const fromNode = this.nodes.find(n => n.id === this.connectionStart.node);
                        const toNode   = this.nodes.find(n => n.id === finalToNodeId);
                        
                        if (fromNode && toNode) {
                            // Validar: include/extend NO se permiten con actores
                            const isIncExt = this.currentArrowType === 'include' || this.currentArrowType === 'extend';
                            if (isIncExt && (fromNode.type === 'actor' || toNode.type === 'actor')) {
                                this._showToast('«' + this.currentArrowType + '» solo se permite entre casos de uso, no con actores.', 'warn');
                            } else {
                                this.connections.push({
                                    fromNode:  this.connectionStart.node,
                                    toNode:    finalToNodeId,
                                    fromSide:  this.connectionStart.side,
                                    toSide:    toSide,
                                    type:      this.currentArrowType,
                                    label:     '',
                                    lineStyle: this.currentLineStyle || 'bezier'
                                });
                                this.render();
                                this.pushToHistory();
                                this.unsavedChanges = true;
                            }
                        }
                    }
                }
                
                this.connecting = false;
                this.connectionStart = null;
                this.previewElement.style.display = 'none';
            }

            handleCanvasMouseDown(e) {
                if (e.target === e.currentTarget) {
                    if (e.button === 0) {
                        e.preventDefault();
                        this._clearMultiSelect();

                        // Si Shift está presionado → solo pan, no selección
                        if (e.shiftKey) {
                            this.startCanvasPan(e);
                            return;
                        }

                        // Iniciar rectángulo de selección múltiple
                        const pos = this.getCanvasPos(e);
                        this._selRect = { startX: pos.x, startY: pos.y, active: true };
                        this._selEl = document.createElement('div');
                        this._selEl.id = '_multiSelRect';
                        this._selEl.style.cssText = [
                            'position:absolute',
                            `left:${pos.x}px`, `top:${pos.y}px`,
                            'width:0', 'height:0',
                            'border:2px dashed rgba(var(--primary-rgb),.8)',
                            'background:rgba(var(--primary-rgb),.07)',
                            'border-radius:3px',
                            'pointer-events:none',
                            'z-index:60',
                        ].join(';');
                        this.canvas.appendChild(this._selEl);

                        const onMove = (me) => {
                            const p = this.getCanvasPos(me);
                            const x = Math.min(p.x, pos.x), y = Math.min(p.y, pos.y);
                            const w = Math.abs(p.x - pos.x), h = Math.abs(p.y - pos.y);
                            if (this._selEl) {
                                this._selEl.style.left   = x + 'px';
                                this._selEl.style.top    = y + 'px';
                                this._selEl.style.width  = w + 'px';
                                this._selEl.style.height = h + 'px';
                            }
                        };
                        const onUp = (ue) => {
                            document.removeEventListener('mousemove', onMove);
                            document.removeEventListener('mouseup', onUp);

                            const p = this.getCanvasPos(ue);
                            const x1 = Math.min(p.x, pos.x), y1 = Math.min(p.y, pos.y);
                            const x2 = Math.max(p.x, pos.x), y2 = Math.max(p.y, pos.y);

                            if (this._selEl) { this._selEl.remove(); this._selEl = null; }

                            if (x2 - x1 > 8 || y2 - y1 > 8) {
                                // Select nodes inside rect
                                this._selectedNodes = this.nodes.filter(n => {
                                    const cx = n.x + (n.width||120)/2;
                                    const cy = n.y + (n.height||60)/2;
                                    return cx >= x1 && cx <= x2 && cy >= y1 && cy <= y2;
                                });
                                if (this._selectedNodes.length > 0) {
                                    this._highlightMultiSelect();
                                    this._showMultiSelectInfo();
                                } else {
                                    // Empty click: deselect
                                    this.selectedNode = null;
                                    this.selectedConnection = null;
                                    this.render();
                                    this.updatePropertiesPanel();
                                }
                            } else {
                                // Tiny drag = deselect
                                this.selectedNode = null;
                                this.selectedConnection = null;
                                this.render();
                                this.updatePropertiesPanel();
                            }
                            this._selRect = null;
                        };
                        document.addEventListener('mousemove', onMove);
                        document.addEventListener('mouseup', onUp);

                    } else {
                        this._clearMultiSelect();
                        this.selectedNode = null;
                        this.selectedConnection = null;
                        this.render();
                        this.updatePropertiesPanel();
                    }
                }
            }

            /** Multi-select helpers */
            _highlightMultiSelect() {
                const IDS = new Set((this._selectedNodes||[]).map(n=>n.id));
                document.querySelectorAll('.diagram-node').forEach(el => {
                    if (IDS.has(el.dataset.nodeId)) {
                        el.style.outline = '2.5px solid var(--primary)';
                        el.style.outlineOffset = '3px';
                        el.style.boxShadow = '0 0 12px rgba(var(--primary-rgb),.4)';
                    }
                });
            }

            _clearMultiSelect() {
                this._selectedNodes = [];
                document.getElementById('_multiMoveHint')?.remove();
                document.querySelectorAll('.diagram-node').forEach(el => {
                    el.style.outline = '';
                    el.style.outlineOffset = '';
                    el.style.boxShadow = '';
                });
            }

            _showMultiSelectInfo() {
                document.getElementById('_multiMoveHint')?.remove();
                const n = (this._selectedNodes||[]).length;
                if (n === 0) return;
                const hint = document.createElement('div');
                hint.id = '_multiMoveHint';
                hint.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#1a1a2e;border:1px solid var(--primary);color:#aab8ff;border-radius:20px;padding:6px 18px;font-size:.78rem;z-index:9000;pointer-events:none;box-shadow:0 4px 16px rgba(0,0,0,.4)';
                hint.innerHTML = `<i class="bi bi-check2-square me-2"></i>${n} elemento${n>1?'s':''} seleccionado${n>1?'s':''} — Arrastra cualquiera para mover todos`;
                document.body.appendChild(hint);
                setTimeout(() => hint?.remove(), 3500);
            }

            /** Called from node drag: if node is in multi-select, move all selected */
            _moveMultiSelect(deltaX, deltaY, anchorNodeId) {
                if (!this._selectedNodes?.length) return false;
                const inSel = this._selectedNodes.find(n=>n.id===anchorNodeId);
                if (!inSel) return false;
                this._selectedNodes.forEach(n => { n.x += deltaX; n.y += deltaY; });
                this.render();
                return true;
            }

            handleCanvasClick(e) {}

            handleTouchStart(e) {
                if (e.touches.length === 1 && e.target === e.currentTarget) {
                    // Un solo dedo tocando en área vacía, iniciar pan
                    e.preventDefault();
                    this.startCanvasPan(e.touches[0]);
                }
            }

            handleTouchMove(e) {
                if (this.panningCanvas && e.touches.length === 1) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    _panX = touch.clientX - this.panStartX;
                    _panY = touch.clientY - this.panStartY;
                    window.applyTransform();
                }
            }

            handleTouchEnd(e) {
                if (this.panningCanvas) {
                    this.panningCanvas = false;
                    const container = document.getElementById('canvasContainer');
                    if (container) container.style.cursor = '';
                }
            }

            startCanvasPan(event) {
                if (this.draggingNode || this.connecting) return;

                const container = document.getElementById('canvasContainer');
                if (!container) return;

                // Extraer coordenadas del evento (mouse o touch)
                const clientX = event.clientX;
                const clientY = event.clientY;

                this.panningCanvas = true;
                this.panStartX = clientX - _panX;
                this.panStartY = clientY - _panY;
                container.style.cursor = 'grabbing';

                // Para mouse events, configurar listeners adicionales
                if (event.type === 'mousedown') {
                    const onMouseMove = (e) => {
                        if (!this.panningCanvas) return;
                        _panX = e.clientX - this.panStartX;
                        _panY = e.clientY - this.panStartY;
                        window.applyTransform();
                    };

                    const onMouseUp = () => {
                        this.panningCanvas = false;
                        container.style.cursor = '';
                        document.removeEventListener('mousemove', onMouseMove);
                        document.removeEventListener('mouseup', onMouseUp);
                    };

                    document.addEventListener('mousemove', onMouseMove);
                    document.addEventListener('mouseup', onMouseUp);
                }
            }

            updatePropertiesPanel() {
                const attributesSection = document.getElementById('attributesSection');
                const methodsSection = document.getElementById('methodsSection');
                const noSelectionMessage = document.getElementById('noSelectionMessage');
                const noConnectionMessage = document.getElementById('noConnectionMessage');
                
                // ── Panel de conexión seleccionada ────────────────────
                if (this.selectedConnection) {
                    switchPropertiesTab('connection');
                    
                    const connLabel = document.getElementById('connLabel');
                    const connType  = document.getElementById('connType');
                    const connLabelOffsetX = document.getElementById('connLabelOffsetX');
                    const connLabelOffsetY = document.getElementById('connLabelOffsetY');
                    const fromNode  = this.nodes.find(n => n.id === this.selectedConnection.fromNode);
                    const toNode    = this.nodes.find(n => n.id === this.selectedConnection.toNode);
                    const connInfo  = document.getElementById('connInfo');
                    if (connLabel) {
                        connLabel.value = this.selectedConnection.label || '';
                        connLabel.focus();
                    }
                    if (connType)  connType.value  = this.selectedConnection.type  || 'asociacion';
                    if (connLabelOffsetX) connLabelOffsetX.value = this.selectedConnection.labelOffsetX || 0;
                    if (connLabelOffsetY) connLabelOffsetY.value = this.selectedConnection.labelOffsetY || 0;
                    if (connInfo && fromNode && toNode) {
                        connInfo.textContent = `${fromNode.text} → ${toNode.text}`;
                    }
                    if (noConnectionMessage) noConnectionMessage.style.display = 'none';
                    return;
                }
                if (noConnectionMessage) noConnectionMessage.style.display = 'block';

                // ── Panel de nodo seleccionado (comportamiento original) ──
                if (this.selectedNode) {
                    switchPropertiesTab('element');
                    
                    const nodeText = document.getElementById('nodeText');
                    const nodeId = document.getElementById('nodeId');
                    const nodeType = document.getElementById('nodeType');
                    const nodeColor = document.getElementById('nodeColor');
                    const nodeWidth = document.getElementById('nodeWidth');
                    const nodeHeight = document.getElementById('nodeHeight');
                    const nodeAttributes = document.getElementById('nodeAttributes');
                    const nodeMethods = document.getElementById('nodeMethods');
                    
                    if (nodeText)   nodeText.value   = this.selectedNode.text  || '';
                    if (nodeColor)  nodeColor.value  = this.selectedNode.color || '#0d6efd';
                    if (nodeWidth)  nodeWidth.value  = this.selectedNode.width  || 140;
                    if (nodeHeight) nodeHeight.value = this.selectedNode.height || 70;

                    const typeLabels = {
                        actor:'Actor', usecase:'Caso de Uso', system:'Sistema',
                        class:'Clase', abstract:'Clase Abstracta', interface:'Interfaz', enum:'Enumeración',
                        lifeline:'Línea de Vida', activation:'Activación',
                        start:'Inicio', end:'Fin', activity:'Actividad', decision:'Decisión',
                        fork:'Bifurcación', union:'Unión',
                        initial:'Inicial', state:'Estado', final:'Final', history:'Historia',
                        component:'Componente', port:'Puerto', required:'Interfaz Requerida',
                        node:'Nodo', device:'Dispositivo', artifact:'Artefacto',
                        object:'Objeto', valor:'Valor', message:'Mensaje', link:'Enlace',
                        event:'Evento', constraint:'Restricción'
                    };
                    const nodeTypeDisplay = document.getElementById('nodeTypeDisplay');
                    if (nodeTypeDisplay) nodeTypeDisplay.textContent = typeLabels[this.selectedNode.type] || this.selectedNode.type;

                    const classTypes = ['class','abstract','interface','enum','object'];
                    if (classTypes.includes(this.selectedNode.type)) {
                        if (attributesSection) attributesSection.style.display = 'block';
                        // Diagrama de objetos: solo atributos (param = valor), sin métodos
                        if (methodsSection) methodsSection.style.display = (this.selectedNode.type === 'object') ? 'none' : 'block';
                        const nodeAttributes = document.getElementById('nodeAttributes');
                        const nodeMethods    = document.getElementById('nodeMethods');
                        // Update placeholder for object type
                        if (nodeAttributes && this.selectedNode.type === 'object') {
                            nodeAttributes.placeholder = 'nombre = "Juan"\nedad = 25\nactivo = true';
                            const attrLabel = nodeAttributes.closest('.mb-3')?.querySelector('small');
                            if (attrLabel) attrLabel.textContent = 'Una línea por parámetro, ej: nombre = "Juan"';
                        }
                        if (nodeAttributes) nodeAttributes.value = this.selectedNode.attributes || '';
                        if (nodeMethods)    nodeMethods.value    = this.selectedNode.methods    || '';
                    } else {
                        if (attributesSection) attributesSection.style.display = 'none';
                        if (methodsSection)    methodsSection.style.display    = 'none';
                    }

                    const seqLabelSection = document.getElementById('seqLabelSection');
                    const nodeSeqLabel    = document.getElementById('nodeSeqLabel');
                    if (seqLabelSection && nodeSeqLabel) {
                        if (this.diagramType === 'sequence' && this.selectedNode.type === 'activation') {
                            seqLabelSection.style.display = 'block';
                            nodeSeqLabel.value = this.selectedNode.label || '';
                        } else {
                            seqLabelSection.style.display = 'none';
                        }
                    }
                    
                    if (noSelectionMessage) noSelectionMessage.style.display = 'none';
                } else {
                    // Sin selección: mostrar pestaña de info
                    if (noSelectionMessage) noSelectionMessage.style.display = 'block';
                    
                    // Actualizar estadísticas en la pestaña de info
                    const totalElementsInfo = document.getElementById('totalElementsInfo');
                    const totalConnectionsInfo = document.getElementById('totalConnectionsInfo');
                    if (totalElementsInfo) totalElementsInfo.textContent = this.nodes.length;
                    if (totalConnectionsInfo) totalConnectionsInfo.textContent = this.connections.length;
                }
            }

            aplicarPropiedades() {
                // ── Aplicar a conexión seleccionada ───────────────────
                if (this.selectedConnection) {
                    const connLabel = document.getElementById('connLabel');
                    const connType  = document.getElementById('connType');
                    const connLabelOffsetX = document.getElementById('connLabelOffsetX');
                    const connLabelOffsetY = document.getElementById('connLabelOffsetY');
                    if (connLabel) this.selectedConnection.label = connLabel.value;
                    if (connType)  this.selectedConnection.type  = connType.value;
                    if (connLabelOffsetX) this.selectedConnection.labelOffsetX = parseFloat(connLabelOffsetX.value) || 0;
                    if (connLabelOffsetY) this.selectedConnection.labelOffsetY = parseFloat(connLabelOffsetY.value) || 0;
                    this.render();
                    this.pushToHistory();
                    this.unsavedChanges = true;
                    return;
                }

                // ── Aplicar a nodo seleccionado (comportamiento original) ──
                if (!this.selectedNode) return;

                const nodeText       = document.getElementById('nodeText');
                const nodeColor      = document.getElementById('nodeColor');
                const nodeWidth      = document.getElementById('nodeWidth');
                const nodeHeight     = document.getElementById('nodeHeight');
                const nodeAttributes = document.getElementById('nodeAttributes');
                const nodeMethods    = document.getElementById('nodeMethods');
                const nodeSeqLabel   = document.getElementById('nodeSeqLabel');

                if (nodeText)     this.selectedNode.text  = nodeText.value;
                if (nodeColor)    this.selectedNode.color = nodeColor.value;
                if (nodeAttributes) this.selectedNode.attributes = nodeAttributes.value;
                if (nodeMethods)    this.selectedNode.methods    = nodeMethods.value;
                if (nodeSeqLabel)   this.selectedNode.label      = nodeSeqLabel.value;

                const newW = parseInt(nodeWidth  ? nodeWidth.value  : 0);
                const newH = parseInt(nodeHeight ? nodeHeight.value : 0);
                if (newW > 20) this.selectedNode.width  = newW;
                if (newH > 10) this.selectedNode.height = newH;

                const classTypes = ['class','abstract','interface','enum','object'];
                if (classTypes.includes(this.selectedNode.type)) {
                    const attrLines = (this.selectedNode.attributes || '').split('\n').filter(l => l.trim()).length;
                    const methLines = (this.selectedNode.methods    || '').split('\n').filter(l => l.trim()).length;
                    this.selectedNode.height = Math.max(
                        120,
                        36 + 12 + (attrLines * 20) + 12 + 12 + (methLines * 20) + 12
                    );
                }

                this.render();
                this.pushToHistory();
                this.unsavedChanges = true;
            }

            deleteSelected() {
                // ── Eliminar conexión seleccionada ────────────────────
                if (this.selectedConnection) {
                    const sc = this.selectedConnection;
                    this.connections = this.connections.filter(c =>
                        !(c.fromNode === sc.fromNode && c.toNode === sc.toNode &&
                          c.fromSide === sc.fromSide && c.toSide === sc.toSide)
                    );
                    this.selectedConnection = null;
                    const _db2 = document.getElementById('deleteConnBtn');
                    if (_db2) _db2.style.display = 'none';
                    this.render();
                    this.pushToHistory();
                    this.unsavedChanges = true;
                    return;
                }

                // ── Eliminar nodo seleccionado ────────────────────────
                if (!this.selectedNode) return;
                
                const deletedNode = this.selectedNode;
                this.connections = this.connections.filter(conn => 
                    conn.fromNode !== deletedNode.id && conn.toNode !== deletedNode.id
                );
                
                this.nodes = this.nodes.filter(node => node.id !== deletedNode.id);
                this.selectedNode = null;

                if (this.diagramType === 'usecase' && deletedNode.type === 'usecase') {
                    this._reorganizarUsecases();
                }
                
                this.render();
                this.pushToHistory();
                this.unsavedChanges = true;
            }

            updateLayersList() {
                const layersList = document.getElementById('layersList');
                if (!layersList) return;
                
                layersList.innerHTML = '';
                
                this.nodes.forEach((node) => {
                    const layerEl = document.createElement('div');
                    layerEl.className = 'p-2 border-bottom border-dark-gray d-flex align-items-center';
                    layerEl.innerHTML = `
                        <i class="bi bi-circle-fill me-2" style="color: ${node.color}; font-size: 10px;"></i>
                        <span class="small text-light flex-grow-1">${node.text} (${node.type})</span>
                        <button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="editor.selectNodeById('${node.id}')">
                            <i class="bi bi-eye"></i>
                        </button>
                    `;
                    layersList.appendChild(layerEl);
                });
            }

            selectNodeById(id) {
                const node = this.nodes.find(n => n.id === id);
                if (node) {
                    this.selectNode(node);
                }
            }

            pushToHistory() {
                const state = {
                    nodes: this.nodes,
                    connections: this.connections
                };
                this.history.push(state);
            }

            undo() {
                const state = this.history.undo();
                if (state) {
                    this.nodes = state.nodes;
                    this.connections = state.connections;
                    this.selectedNode = null;
                    this.render();
                    this.unsavedChanges = true;
                }
            }

            redo() {
                const state = this.history.redo();
                if (state) {
                    this.nodes = state.nodes;
                    this.connections = state.connections;
                    this.selectedNode = null;
                    this.render();
                    this.unsavedChanges = true;
                }
            }

            async cargarDiagrama() {
                try {
                    const response = await fetch('<?= BASE_URL ?>/api/diagramas/load?id=' + diagramaId);
                    const data = await response.json();
                    
                    if (data.success) {
                        const diagrama = data.diagrama;

                        // Normalizar contenido — puede venir como objeto o como string JSON
                        let cont = diagrama.contenido;
                        if (typeof cont === 'string') {
                            try { cont = JSON.parse(cont); } catch(e) { cont = {}; }
                        }
                        if (!cont) cont = {};

                        // Soportar formato directo { nodes, connections } Y
                        // formato exportación { nodes, connections, diagramType }
                        this.nodes       = cont.nodes       || [];
                        this.connections = cont.connections || [];
                        this.diagramType = cont.diagramType || diagrama.tipo_diagrama || 'usecase';
                        
                        const tipoDisplay = document.getElementById('diagramTypeDisplay');
                        const tipoMap = {
                            'usecase': 'Diagrama de Casos de Uso',
                            'class': 'Diagrama de Clases',
                            'sequence': 'Diagrama de Secuencia',
                            'activity': 'Diagrama de Actividades',
                            'state': 'Diagrama de Estados',
                            'component': 'Diagrama de Componentes',
                            'deployment': 'Diagrama de Despliegue',
                            'object': 'Diagrama de Objetos',
                            'communication': 'Diagrama de Comunicación',
                            'timing': 'Diagrama de Tiempo'
                        };
                        if (tipoDisplay) {
                            tipoDisplay.value = tipoMap[diagrama.tipo_diagrama] || 'Diagrama de Casos de Uso';
                        }
                        
                        document.getElementById('versionNum').textContent = diagrama.version;
                        
                        this.loadShapesForType(this.diagramType);
                        this.loadArrowsForType(this.diagramType);
                        this.render();
                        this.pushToHistory();
                        this.unsavedChanges = false;
                        // Auto-centrar el diagrama al abrirlo — esperar que el DOM termine de pintar
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                if (typeof editorFitContent === 'function') editorFitContent();
                                else if (this.nodes.length) this.centerDiagramInViewport();
                            });
                        });
                    } else {
                        mostrarAutoSave('Error al cargar diagrama', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAutoSave('Error de conexión', 'error');
                }
            }

            // Sanitiza un título: solo letras, números, espacios → guiones bajos
            sanitizarTitulo(titulo) {
                return titulo
                    .trim()
                    .replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9 _\-]/g, '')  // solo letras, números, espacios, _ y -
                    .replace(/\s+/g, '_')                                // espacios → guion bajo
                    .replace(/_+/g, '_')                                  // múltiples _ → uno solo
                    .replace(/^_|_$/g, '')                                // quitar _ al inicio/fin
                    || 'diagrama';
            }

            async guardar() {
                // ── Obtener título limpio ──────────────────────────────
                const tituloEl = document.getElementById('diagramaTitulo');
                let titulo = (tituloEl ? tituloEl.textContent : '').trim();

                if (!titulo || titulo === 'Sin título' || titulo === 'Nuevo Diagrama') {
                    const nuevoDiagrama = sessionStorage.getItem('nuevoDiagrama');
                    if (nuevoDiagrama) {
                        try {
                            const nd = JSON.parse(nuevoDiagrama);
                            titulo = (nd.titulo || '').trim() || 'Mi_diagrama';
                            sessionStorage.removeItem('nuevoDiagrama');
                            if (tituloEl) tituloEl.textContent = titulo;
                        } catch(e) { titulo = 'Mi_diagrama'; }
                    } else {
                        titulo = 'Mi_diagrama';
                    }
                }

                // ── Mismo formato que exportarJSON (para compatibilidad total) ──
                const contenido = {
                    nodes:       this.nodes,
                    connections: this.connections,
                    diagramType: this.diagramType
                };
                
                const tituloSanitizado = this.sanitizarTitulo(titulo);
                const data = {
                    id:              diagramaId,
                    titulo:          titulo,              // título legible para mostrar
                    nombre_archivo:  tituloSanitizado,   // nombre del archivo JSON
                    tipo:            this.diagramType,
                    contenido:       contenido,
                    descripcion:     '',
                    etiquetas:       '',
                    proyecto_id:     PROYECTO_CONTEXTO || 0   // ← ligar al proyecto si aplica
                };
                
                try {
                    const response = await fetch('<?= BASE_URL ?>/api/diagramas/save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.unsavedChanges = false;
                        mostrarAutoSave('Guardado correctamente');
                        
                        if (!diagramaId && result.id) {
                            // Actualizar URL e ID sin recargar la página (evita borrar el canvas)
                            diagramaId = result.id;
                            diagramaId = result.id; // actualizar en memoria ANTES de replaceState
                            history.replaceState({}, '', '<?= BASE_URL ?>/editor?id=' + result.id);
                        }
                        if (result.nueva_version) {
                            document.getElementById('versionNum').textContent = result.nueva_version;
                        }
                        
                        return true;
                    } else {
                        mostrarAutoSave('Error: ' + (result.error || 'desconocido'), 'error');
                        return false;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAutoSave('Error de conexión', 'error');
                    return false;
                }
            }
        }

        let editor;
        
        // ══════════════════════════════════════════════════════
        // ESTILOS DE CONECTOR (línea)
        // ══════════════════════════════════════════════════════
        const CONN_STYLES = [
            {
                id: 'bezier', label: 'Curva',
                title: 'Curva Bézier — flujo natural entre nodos',
                svg: `<svg viewBox="0 0 52 24" width="52" height="24">
                    <path d="M 4 12 C 18 4, 34 20, 48 12" stroke="currentColor" stroke-width="2" fill="none"
                          marker-end="url(#cs_arr)"/>
                    <defs><marker id="cs_arr" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto">
                        <polygon points="0 0,8 4,0 8" fill="currentColor"/></marker></defs>
                </svg>`
            },
            {
                id: 'straight', label: 'Recta',
                title: 'Línea recta — conexión directa punto a punto',
                svg: `<svg viewBox="0 0 52 24" width="52" height="24">
                    <line x1="4" y1="12" x2="44" y2="12" stroke="currentColor" stroke-width="2"
                          marker-end="url(#cs_arr2)"/>
                    <defs><marker id="cs_arr2" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto">
                        <polygon points="0 0,8 4,0 8" fill="currentColor"/></marker></defs>
                </svg>`
            },
            {
                id: 'orthogonal', label: 'Doblada',
                title: 'Ortogonal / Doblada — línea en ángulo recto (90°)',
                svg: `<svg viewBox="0 0 52 24" width="52" height="24">
                    <polyline points="4,18 26,18 26,6 48,6" stroke="currentColor" stroke-width="2" fill="none"
                          marker-end="url(#cs_arr3)"/>
                    <defs><marker id="cs_arr3" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto">
                        <polygon points="0 0,8 4,0 8" fill="currentColor"/></marker></defs>
                </svg>`
            },
            {
                id: 'arc', label: 'Arco',
                title: 'Arco — línea curva pronunciada en forma de arco',
                svg: `<svg viewBox="0 0 52 24" width="52" height="24">
                    <path d="M 4 18 A 30 30 0 0 1 48 18" stroke="currentColor" stroke-width="2" fill="none"
                          marker-end="url(#cs_arr4)"/>
                    <defs><marker id="cs_arr4" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto">
                        <polygon points="0 0,8 4,0 8" fill="currentColor"/></marker></defs>
                </svg>`
            }
        ];

        function initConnStylePicker() {
            // With <select>, nothing to initialize dynamically
            setConnLineStyle('bezier', false);
        }

        function setConnLineStyle(styleId, saveToConn = true) {
            if (typeof editor !== 'undefined') {
                editor.currentLineStyle = styleId;
            }
            // Sync <select> value
            const sel = document.getElementById('connStyleSelect');
            if (sel && sel.value !== styleId) sel.value = styleId;

            // If a connection is selected, update it immediately
            if (saveToConn && typeof editor !== 'undefined' && editor.selectedConnection) {
                editor.selectedConnection.lineStyle = styleId;
                editor.render();
                editor.pushToHistory();
                editor.unsavedChanges = true;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ── Cargar tema guardado al arrancar ─────────────────────
            (async () => {
                try {
                    if (typeof loadUserTheme === 'function') {
                        const cfg = await loadUserTheme();
                        // Sincronizar icono del botón
                        const icon = document.getElementById('themeToggle')?.querySelector('i');
                        if (icon && cfg?.theme === 'light') icon.className = 'bi bi-moon-fill';
                    }
                } catch(_) {
                    // fallback localStorage
                    try {
                        const local = JSON.parse(localStorage.getItem('userTheme') || 'null');
                        if (local?.theme === 'light') {
                            document.body.classList.add('light-theme');
                            const icon = document.getElementById('themeToggle')?.querySelector('i');
                            if (icon) icon.className = 'bi bi-moon-fill';
                        }
                        if (local?.primary_color) {
                            document.documentElement.style.setProperty('--primary', local.primary_color);
                            document.documentElement.style.setProperty('--primary2', local.primary2_color || '#764ba2');
                        }
                    } catch(_) {}
                }
            })();
            editor = new DiagramEditor();
            window.editor = editor;

            // ── Event listeners para inputs de propiedades ──────────────
            const connLabelOffsetX = document.getElementById('connLabelOffsetX');
            const connLabelOffsetY = document.getElementById('connLabelOffsetY');
            
            if (connLabelOffsetX) {
                connLabelOffsetX.addEventListener('input', () => {
                    if (editor.selectedConnection) {
                        editor.selectedConnection.labelOffsetX = parseFloat(connLabelOffsetX.value) || 0;
                        editor.render();
                    }
                });
                connLabelOffsetX.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        editor.aplicarPropiedades();
                    }
                });
            }
            
            if (connLabelOffsetY) {
                connLabelOffsetY.addEventListener('input', () => {
                    if (editor.selectedConnection) {
                        editor.selectedConnection.labelOffsetY = parseFloat(connLabelOffsetY.value) || 0;
                        editor.render();
                    }
                });
                connLabelOffsetY.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        editor.aplicarPropiedades();
                    }
                });
            }
            
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const isLight = document.body.classList.toggle('light-theme');
                    const icon = themeToggle.querySelector('i');
                    icon.className = isLight ? 'bi bi-moon-fill' : 'bi bi-sun-fill';

                    // Guardar tema en servidor/localStorage
                    if (typeof saveUserTheme === 'function') {
                        const cfg = typeof _currentConfig !== 'undefined' ? _currentConfig : {};
                        saveUserTheme({ ...cfg, theme: isLight ? 'light' : 'dark' });
                    }

                    if (editor) editor.updateArrowPreview();
                });


            }
            
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            // ── Zoom & Pan del canvas ──────────────────────────────────
            initZoomPan();
            renderPreviewSection();

            // ── Atajos de teclado para zoom ────────────────────────────
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && (e.key === '=' || e.key === '+')) { e.preventDefault(); editorZoom(0.1); }
                if (e.ctrlKey && e.key === '-')                    { e.preventDefault(); editorZoom(-0.1); }
                if (e.ctrlKey && e.key === '0')                    { e.preventDefault(); editorZoomReset(); }
            });
            
            // ── Cargar diagrama existente (datos ya traídos por PHP) ──
            if (diagramaId && _datosPrecargados) {
                // Usar datos precargados directamente sin fetch
                editor.nodes       = _datosPrecargados.nodes       || [];
                editor.connections = _datosPrecargados.connections  || [];
                editor.diagramType = _datosPrecargados.diagramType  || tipoDiagrama;

                const tituloEl = document.getElementById('diagramaTitulo');
                if (tituloEl) tituloEl.textContent = (_datosPrecargados.titulo || '').trim();

                const versionEl = document.getElementById('versionNum');
                if (versionEl) versionEl.textContent = _datosPrecargados.version || 1;

                const tipoDisplay = document.getElementById('diagramTypeDisplay');
                if (tipoDisplay) {
                    const tipoMap = {
                        'usecase':'Diagrama de Casos de Uso','class':'Diagrama de Clases',
                        'sequence':'Diagrama de Secuencia','activity':'Diagrama de Actividades',
                        'state':'Diagrama de Estados','component':'Diagrama de Componentes',
                        'deployment':'Diagrama de Despliegue','object':'Diagrama de Objetos',
                        'communication':'Diagrama de Comunicación','timing':'Diagrama de Tiempo'
                    };
                    tipoDisplay.value = tipoMap[editor.diagramType] || editor.diagramType;
                }

                editor.loadShapesForType(editor.diagramType);
                editor.loadArrowsForType(editor.diagramType);
                editor.render();
                
                // Centrar el diagrama en el viewport automáticamente
                setTimeout(() => {
                    editor.centerDiagramInViewport();
                }, 100);
                
                editor.pushToHistory();
                editor.unsavedChanges = false;

            } else if (diagramaId && !_datosPrecargados) {
                // Fallback: hacer fetch si PHP no pudo cargar (ej: archivo huérfano)
                editor.cargarDiagrama();

            } else {
                // Diagrama nuevo desde sessionStorage
                const nuevoDiagrama = sessionStorage.getItem('nuevoDiagrama');
                if (nuevoDiagrama) {
                    try {
                        const nd = JSON.parse(nuevoDiagrama);
                        const tituloEl = document.getElementById('diagramaTitulo');
                        if (tituloEl && nd.titulo) tituloEl.textContent = nd.titulo.trim();
                        editor.diagramType = nd.tipo || nd.diagramType || tipoDiagrama;
                        editor.loadShapesForType(editor.diagramType);
                        editor.loadArrowsForType(editor.diagramType);

                        // ── CORRECCIÓN: cargar nodos y conexiones de plantilla ──
                        if (Array.isArray(nd.nodes) && nd.nodes.length > 0) {
                            // Reasignar IDs únicos para evitar colisiones con futuros nodos
                            const idMap = {};
                            editor.nodes = nd.nodes.map(n => {
                                const newId = editor.generateId ? editor.generateId() : (editor.diagramType + '_' + (editor.nodeIdCounter = (editor.nodeIdCounter||0)+1));
                                idMap[n.id] = newId;
                                return { ...n, id: newId };
                            });
                            // Reasignar IDs en conexiones usando el mapa anterior
                            editor.connections = (nd.connections || [])
                                .map(c => ({
                                    ...c,
                                    fromNode: idMap[c.fromNode] || c.fromNode,
                                    toNode:   idMap[c.toNode]   || c.toNode
                                }))
                                .filter(c => {
                                    const ok = editor.nodes.find(n => n.id === c.fromNode)
                                            && editor.nodes.find(n => n.id === c.toNode);
                                    if (!ok) console.warn('Conexión de plantilla huérfana descartada:', c);
                                    return ok;
                                });
                            editor.unsavedChanges = true;
                        }
                        // ────────────────────────────────────────────────────────

                        editor.render();
                        editor.pushToHistory && editor.pushToHistory();
                        sessionStorage.removeItem('nuevoDiagrama');
                        // Auto-centrar al cargar
                        setTimeout(() => { if (typeof editorFitContent === 'function') editorFitContent(); }, 120);
                    } catch(e) { console.warn('sessionStorage parse error', e); }
                } else {
                    editor.loadShapesForType(editor.diagramType);
                    editor.loadArrowsForType(editor.diagramType);
                    editor.render();
                }
            }
        });

        function volverAlDashboard() {
            // Si venimos de dashboard o maestro con estado guardado, history.back() restaura la posición exacta
            const hasNavState = sessionStorage.getItem('dash_nav_state') || sessionStorage.getItem('maestro_nav_state');
            if (hasNavState && document.referrer && (document.referrer.includes('/dashboard') || document.referrer.includes('/maestro'))) {
                history.back();
            } else {
                // Fallback: ir al dashboard según el rol
                const rol = '<?= SessionManager::usuarioRol() ?>';
                const destino = rol === 'maestro' ? '<?= BASE_URL ?>/maestro' : '<?= BASE_URL ?>/dashboard';
                window.location.href = destino;
            }
        }

        function guardarDiagrama() {
            if (editor) editor.guardar();
        }

        function aplicarPropiedades() {
            if (editor) editor.aplicarPropiedades();
        }

        function eliminarSeleccionado() {
            if (editor) editor.deleteSelected();
        }

        function mostrarModalVersion() {
            const modal = new bootstrap.Modal(document.getElementById('versionModal'));
            modal.show();
        }

        function guardarVersion() {
            const comentario = document.getElementById('versionComentario').value;
            bootstrap.Modal.getInstance(document.getElementById('versionModal')).hide();
            if (editor) editor.guardar();
        }

        function exportarDiagrama() {
            const modal = new bootstrap.Modal(document.getElementById('exportModal'));
            modal.show();
        }

        // ── Panel de pre-exportación (PNG / PDF) ───────────────────────
        function abrirPanelExportacion(format) {
            // Remove any existing panel
            document.getElementById('_exportPanel')?.remove();

            const titulo = document.getElementById('diagramaTitulo')?.textContent?.trim() || 'diagrama';
            const panel  = document.createElement('div');
            panel.id = '_exportPanel';
            panel.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:99000;display:flex;align-items:center;justify-content:center';

            panel.innerHTML = `
            <div style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:16px;width:min(800px,95vw);max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.8)">

                <!-- Header -->
                <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 22px;display:flex;align-items:center;gap:12px">
                    <i class="bi bi-${format==='pdf'?'file-earmark-pdf':'file-earmark-image'}" style="font-size:1.4rem;color:#fff"></i>
                    <div style="flex:1">
                        <div style="color:#fff;font-weight:700;font-size:.95rem">Exportar como ${format.toUpperCase()}</div>
                        <div style="color:rgba(255,255,255,.7);font-size:.75rem">${titulo}</div>
                    </div>
                    <button onclick="document.getElementById('_exportPanel').remove()"
                        style="background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:.9rem">✕</button>
                </div>

                <div style="display:flex;flex:1;overflow:hidden;min-height:0">

                    <!-- Preview canvas -->
                    <div style="flex:1;background:#0a0a12;display:flex;align-items:center;justify-content:center;padding:20px;overflow:auto">
                        <div id="_expPreviewWrap" style="position:relative;display:inline-block">
                            <canvas id="_expCanvas" style="border:1px solid #2a2a4a;border-radius:4px;max-width:100%;max-height:100%"></canvas>
                            <div id="_expCropHandles" style="position:absolute;inset:0;pointer-events:none">
                                <div style="position:absolute;inset:0;border:2px dashed rgba(102,126,234,.6);border-radius:4px"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div style="width:220px;flex-shrink:0;background:#13132a;border-left:1px solid #2a2a4a;padding:16px;overflow-y:auto;display:flex;flex-direction:column;gap:14px">

                        <div>
                            <div style="font-size:.7rem;color:#667eea;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Calidad</div>
                            <div style="display:flex;flex-direction:column;gap:5px">
                                ${['1x — Rápido','2x — Estándar','3x — Alta resolución','4x — Máxima calidad'].map((l,i)=>`
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.78rem;color:#ccc">
                                    <input type="radio" name="expScale" value="${i+1}" ${i===1?'checked':''} style="accent-color:var(--primary)"> ${l}
                                </label>`).join('')}
                            </div>
                        </div>

                        <div>
                            <div style="font-size:.7rem;color:#667eea;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Color de fondo</div>
                            <div style="display:flex;flex-direction:column;gap:5px">
                                ${[
                                    ['transparent','Transparente','rgba(0,0,0,0)'],
                                    ['dark','Oscuro (tema actual)','#0d0d1a'],
                                    ['white','Blanco','#ffffff'],
                                    ['custom','Personalizado',''],
                                ].map(([v,l,col])=>`
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.78rem;color:#ccc">
                                    <input type="radio" name="expBg" value="${v}" ${v==='dark'?'checked':''} style="accent-color:var(--primary)" onchange="previewExport('${format}')">
                                    <span style="width:16px;height:16px;border-radius:3px;background:${col||'transparent'};border:1px solid #3a3a5a;display:inline-block;flex-shrink:0"></span>
                                    ${l}
                                </label>`).join('')}
                                <input type="color" id="_expCustomBg" value="#1a1a2e" style="width:100%;height:28px;border:none;border-radius:6px;cursor:pointer;background:none;padding:0"
                                    oninput="document.querySelector('[name=expBg][value=custom]').checked=true;previewExport('${format}')">
                            </div>
                        </div>

                        <div>
                            <div style="font-size:.7rem;color:#667eea;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Color de líneas</div>
                            <div style="display:flex;flex-direction:column;gap:5px">
                                ${[['original','Color original'],['black','Negro'],['white','Blanco'],['primary','Color primario']].map(([v,l])=>`
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.78rem;color:#ccc">
                                    <input type="radio" name="expStroke" value="${v}" ${v==='original'?'checked':''} style="accent-color:var(--primary)" onchange="previewExport('${format}')"> ${l}
                                </label>`).join('')}
                            </div>
                        </div>

                        <div>
                            <div style="font-size:.7rem;color:#667eea;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Márgenes (px)</div>
                            <input type="range" id="_expMargin" min="0" max="80" value="20" style="width:100%;accent-color:var(--primary)" oninput="document.getElementById('_expMarginVal').textContent=this.value;previewExport('${format}')">
                            <div style="font-size:.72rem;color:#888;text-align:center"><span id="_expMarginVal">20</span> px</div>
                        </div>

                        ${format === 'pdf' ? `
                        <div>
                            <div style="font-size:.7rem;color:#667eea;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Tamaño de hoja PDF</div>
                            <div style="display:flex;flex-direction:column;gap:5px">
                                ${[
                                    ['libre',   'Libre (dimensiones del diagrama)'],
                                    ['a4',      'A4 (210 × 297 mm)'],
                                    ['a3',      'A3 (297 × 420 mm)'],
                                    ['carta',   'Carta (216 × 279 mm)'],
                                    ['legal',   'Legal (216 × 356 mm)'],
                                ].map(([v,l]) => `
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.78rem;color:#ccc">
                                    <input type="radio" name="expPageSize" value="${v}" ${v==='libre'?'checked':''} style="accent-color:var(--primary)"> ${l}
                                </label>`).join('')}
                            </div>
                            <div style="margin-top:6px">
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.78rem;color:#ccc">
                                    <input type="checkbox" id="_expLandscape" style="accent-color:var(--primary)"> Horizontal (landscape)
                                </label>
                            </div>
                        </div>` : ''}

                        <div style="background:rgba(0,0,0,.2);border-radius:8px;padding:10px;font-size:.72rem;color:#666">
                            <div id="_expDimInfo">Calculando…</div>
                        </div>

                        <button onclick="confirmarExport('${format}')"
                            style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:10px;padding:11px;font-size:.85rem;font-weight:600;cursor:pointer;margin-top:auto">
                            <i class="bi bi-download me-2"></i>Descargar ${format.toUpperCase()}
                        </button>
                    </div>
                </div>
            </div>`;

            document.body.appendChild(panel);
            setTimeout(() => previewExport(format), 100);

            // Close on overlay click
            panel.addEventListener('click', e => { if(e.target===panel) panel.remove(); });
        }

        function _buildExportCanvas(format) {
            const svg = generarSVG();
            if (!svg) return null;
            const scale  = parseInt(document.querySelector('[name=expScale]:checked')?.value || '2');
            const margin = parseInt(document.getElementById('_expMargin')?.value || '20');
            const bgMode = document.querySelector('[name=expBg]:checked')?.value || 'dark';
            const strokeMode = document.querySelector('[name=expStroke]:checked')?.value || 'original';

            const BG_MAP = {
                transparent: 'transparent',
                dark:  '#0d0d1a',
                white: '#ffffff',
                custom: document.getElementById('_expCustomBg')?.value || '#1a1a2e',
            };
            const bg = BG_MAP[bgMode] || '#0d0d1a';

            let W = parseInt(svg.getAttribute('width'))  || 800;
            let H = parseInt(svg.getAttribute('height')) || 600;

            // Apply stroke override
            if (strokeMode !== 'original') {
                const strokeColor = {black:'#000000', white:'#ffffff', primary: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim()||'#667eea'}[strokeMode];
                svg.querySelectorAll('[stroke]:not([stroke="none"]),[stroke="currentColor"]').forEach(el => {
                    if (el.getAttribute('stroke') && el.getAttribute('stroke') !== 'none') el.setAttribute('stroke', strokeColor);
                });
                svg.querySelectorAll('[fill]:not([fill="none"]):not([fill="transparent"])').forEach(el => {
                    const f = el.getAttribute('fill');
                    if (f && f !== 'none' && f !== 'transparent' && !f.startsWith('#0') && !f.startsWith('rgba')) {
                        // Only change text-like elements
                    }
                });
            }

            return { svg, scale, margin, bg, W, H };
        }

        function previewExport(format) {
            const res = _buildExportCanvas(format);
            if (!res) return;
            const { svg, scale, margin, bg, W, H } = res;
            const serializer = new XMLSerializer();
            const svgStr = serializer.serializeToString(svg);
            const blob   = new Blob([svgStr], {type:'image/svg+xml'});
            const url    = URL.createObjectURL(blob);
            const img    = new Image();
            img.onload = () => {
                const canvas = document.getElementById('_expCanvas');
                if (!canvas) return;
                const totalW = (W + margin*2) * scale;
                const totalH = (H + margin*2) * scale;
                canvas.width  = totalW;
                canvas.height = totalH;
                canvas.style.maxWidth  = '100%';
                canvas.style.maxHeight = '400px';
                const ctx = canvas.getContext('2d');
                if (bg === 'transparent') {
                    // Checkerboard
                    const pat = document.createElement('canvas'); pat.width=16;pat.height=16;
                    const pc  = pat.getContext('2d');
                    pc.fillStyle='#aaa';pc.fillRect(0,0,8,8);pc.fillRect(8,8,8,8);
                    pc.fillStyle='#ddd';pc.fillRect(8,0,8,8);pc.fillRect(0,8,8,8);
                    ctx.fillStyle = ctx.createPattern(pat,'repeat');
                } else {
                    ctx.fillStyle = bg;
                }
                ctx.fillRect(0, 0, totalW, totalH);
                ctx.drawImage(img, margin*scale, margin*scale, W*scale, H*scale);
                URL.revokeObjectURL(url);
                const info = document.getElementById('_expDimInfo');
                if (info) info.innerHTML = `${totalW} × ${totalH} px<br>Escala: ${scale}x · Margen: ${margin}px`;
            };
            img.src = url;
        }

        function confirmarExport(format) {
            const res = _buildExportCanvas(format);
            if (!res) return;
            const { svg, scale, margin, bg, W, H } = res;
            const titulo = document.getElementById('diagramaTitulo')?.textContent?.trim() || 'diagrama';
            const serializer = new XMLSerializer();
            const svgStr = serializer.serializeToString(svg);
            const blob   = new Blob([svgStr], {type:'image/svg+xml'});
            const url    = URL.createObjectURL(blob);
            const img    = new Image();
            img.onload = () => {
                const totalW = (W + margin*2) * scale;
                const totalH = (H + margin*2) * scale;
                const canvas = document.createElement('canvas');
                canvas.width = totalW; canvas.height = totalH;
                const ctx = canvas.getContext('2d');
                if (bg !== 'transparent') { ctx.fillStyle = bg; ctx.fillRect(0,0,totalW,totalH); }
                ctx.drawImage(img, margin*scale, margin*scale, W*scale, H*scale);
                URL.revokeObjectURL(url);

                if (format === 'png') {
                    canvas.toBlob(b => {
                        descargarBlob(b, sanitizarNombre(titulo)+'.png','image/png',true);
                        document.getElementById('_exportPanel')?.remove();
                    }, 'image/png');
                } else {
                    // PDF con tamaño de hoja opcional
                    const imgData  = canvas.toDataURL('image/png');
                    const pageSize = document.querySelector('[name=expPageSize]:checked')?.value || 'libre';
                    const landscape = document.getElementById('_expLandscape')?.checked || false;
                    const jsPDF    = window.jsPDF || window.jspdf?.jsPDF;

                    // Dimensiones en mm de los tamaños estándar
                    const pageSizes = {
                        a4:    [210, 297], a3: [297, 420],
                        carta: [216, 279], legal: [216, 356],
                    };

                    if (jsPDF) {
                        let pdf;
                        if (pageSize === 'libre') {
                            // Dimensiones libres basadas en el diagrama
                            const orient = totalW > totalH ? 'landscape' : 'portrait';
                            const pwMm = totalW / scale / 3.7795;
                            const phMm = totalH / scale / 3.7795;
                            pdf = new jsPDF({ orientation: orient, unit: 'mm', format: [pwMm, phMm] });
                            pdf.addImage(imgData, 'PNG', 0, 0, pwMm, phMm);
                        } else {
                            // Tamaño estándar — ajustar el diagrama dentro de la hoja
                            let [pw, ph] = pageSizes[pageSize] || [210, 297];
                            const orient = landscape ? 'landscape' : (totalW > totalH ? 'landscape' : 'portrait');
                            if (landscape) [pw, ph] = [Math.max(pw,ph), Math.min(pw,ph)];
                            pdf = new jsPDF({ orientation: orient, unit: 'mm', format: pageSize === 'a4' || pageSize === 'a3' ? pageSize : [pw, ph] });

                            // Calcular escala para que quepa en la hoja con margen de 10mm
                            const marginMm = 10;
                            const availW = pw - marginMm * 2;
                            const availH = ph - marginMm * 2;
                            const diagWmm = totalW / scale / 3.7795;
                            const diagHmm = totalH / scale / 3.7795;
                            const scaleF = Math.min(availW / diagWmm, availH / diagHmm, 1);
                            const finalW = diagWmm * scaleF;
                            const finalH = diagHmm * scaleF;
                            const offX = (availW - finalW) / 2 + marginMm;
                            const offY = (availH - finalH) / 2 + marginMm;
                            pdf.addImage(imgData, 'PNG', offX, offY, finalW, finalH);
                        }
                        pdf.save(sanitizarNombre(titulo) + '.pdf');
                    } else {
                        // Fallback: ventana de impresión con estilos de página
                        const pageCSS = pageSize !== 'libre' ? `@page{size:${pageSize}${landscape?' landscape':''};margin:10mm}` : '@page{margin:10mm}';
                        const win = window.open('','_blank');
                        win.document.write(`<!DOCTYPE html><html><head><title>${titulo}</title>
                        <style>body{margin:0;background:${bg==='transparent'?'#fff':bg}}
                        img{max-width:100%;max-height:100vh;display:block;margin:auto}
                        ${pageCSS}@media print{body{margin:0}}</style></head>
                        <body><img src="${imgData}">
                        <script>window.onload=()=>setTimeout(()=>{window.print()},400)<\/script></body></html>`);
                        win.document.close();
                    }
                    document.getElementById('_exportPanel')?.remove();
                }
            };
            img.src = url;
        }

        // ── Descargar en JSON ──────────────────────────────────────────
        function exportarJSON() {
            if (!editor) return;
            const titulo = document.getElementById('diagramaTitulo')?.textContent || 'diagrama';
            const data = {
                titulo:      titulo,
                tipo:        editor.diagramType,
                fecha:       new Date().toISOString(),
                nodes:       editor.nodes,
                connections: editor.connections,
                diagramType: editor.diagramType
            };
            descargarBlob(
                JSON.stringify(data, null, 2),
                sanitizarNombre(titulo) + '.json',
                'application/json'
            );
        }

        // ── Generar SVG del canvas ─────────────────────────────────────
        function generarSVG() {
            if (!editor || !editor.nodes.length) {
                alert('El diagrama está vacío. Agrega elementos antes de exportar.');
                return null;
            }

            // Calcular bounding box de todos los nodos
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            editor.nodes.forEach(n => {
                minX = Math.min(minX, n.x);
                minY = Math.min(minY, n.y);
                maxX = Math.max(maxX, n.x + (n.width  || 140));
                maxY = Math.max(maxY, n.y + (n.height || 70));
            });
            editor.connections.forEach(conn => {
                (conn.waypoints||[]).forEach(wp => {
                    minX=Math.min(minX,wp.x); minY=Math.min(minY,wp.y);
                    maxX=Math.max(maxX,wp.x); maxY=Math.max(maxY,wp.y);
                });
            });
            const PAD = 50;
            minX -= PAD; minY -= PAD;
            const W = (maxX - minX) + PAD * 2;
            const H = (maxY - minY) + PAD * 2;

            const svgNS = 'http://www.w3.org/2000/svg';
            const svg   = document.createElementNS(svgNS, 'svg');
            svg.setAttribute('xmlns',   'http://www.w3.org/2000/svg');
            svg.setAttribute('width',   W);
            svg.setAttribute('height',  H);
            svg.setAttribute('viewBox', `${minX} ${minY} ${W} ${H}`);

            // Fondo
            const bg = document.createElementNS(svgNS, 'rect');
            bg.setAttribute('x', minX); bg.setAttribute('y', minY);
            bg.setAttribute('width', W); bg.setAttribute('height', H);
            bg.setAttribute('fill', '#0d0d0d');
            svg.appendChild(bg);

            // Marcadores de flecha
            const defs = document.createElementNS(svgNS, 'defs');
            defs.innerHTML = `
                <marker id="ah"        markerWidth="10" markerHeight="8"  refX="9"  refY="4"   orient="auto"><polygon points="0 0,10 4,0 8" fill="#0d6efd"/></marker>
                <marker id="ah-empty"  markerWidth="12" markerHeight="9"  refX="10" refY="4.5" orient="auto"><polyline points="0 0,11 4.5,0 9" fill="none" stroke="#0d6efd" stroke-width="1.8"/></marker>
                <marker id="ah-dash"   markerWidth="10" markerHeight="8"  refX="9"  refY="4"   orient="auto"><polygon points="0 0,10 4,0 8" fill="#999"/></marker>
                <marker id="diamond-f" markerWidth="14" markerHeight="9"  refX="13" refY="4.5" orient="auto"><polygon points="0 4.5,6.5 0,13 4.5,6.5 9" fill="#0d6efd"/></marker>
                <marker id="diamond-e" markerWidth="14" markerHeight="9"  refX="13" refY="4.5" orient="auto"><polygon points="0 4.5,6.5 0,13 4.5,6.5 9" fill="none" stroke="#0d6efd" stroke-width="1.5"/></marker>`;
            svg.appendChild(defs);

            // Helper: punto en el BORDE del nodo en dirección a (tx,ty) desde el centro
            function borderPoint(node, tx, ty) {
                const cx = node.x + node.width  / 2;
                const cy = node.y + node.height / 2;
                const dx = tx - cx, dy = ty - cy;
                if (Math.abs(dx) < 0.01 && Math.abs(dy) < 0.01) return [cx, cy];

                if (node.type === 'usecase') {
                    // Elipse: rx, ry
                    const rx = node.width / 2 - 3, ry = node.height / 2 - 3;
                    const len = Math.sqrt(dx*dx/(rx*rx) + dy*dy/(ry*ry));
                    return [cx + dx / len / (rx / Math.abs(dx || 1)) * rx,
                            cy + dy / len / (ry / Math.abs(dy || 1)) * ry];
                } else if (node.type === 'actor') {
                    // Actor: punto de salida siempre en el centro-top
                    return [cx, node.y];
                }
                // Rectángulo genérico: intersección con borde
                const hw = node.width / 2, hh = node.height / 2;
                const scaleX = Math.abs(dx) > 0.01 ? hw / Math.abs(dx) : Infinity;
                const scaleY = Math.abs(dy) > 0.01 ? hh / Math.abs(dy) : Infinity;
                const scale  = Math.min(scaleX, scaleY);
                return [cx + dx * scale, cy + dy * scale];
            }

            // Helper: punto de conexión según side declarado
            function getConnectionPoint(node, side) {
                if (side && side.startsWith('seq-')) {
                    const idx  = parseInt(side.split('-')[1], 10) || 0;
                    const HEADER = 36;
                    const step   = Math.floor((node.height - HEADER) / 9);
                    return [node.x + node.width/2, node.y + HEADER + idx * step];
                }
                const cx = node.x + node.width/2, cy = node.y + node.height/2;
                switch(side) {
                    case 'left':   return [node.x,                cy];
                    case 'right':  return [node.x + node.width,   cy];
                    case 'top':    return [cx,                     node.y];
                    case 'bottom': return [cx,                     node.y + node.height];
                    default:       return [cx,                     cy]; // centro — se ajustará luego
                }
            }

            // ── 1. DIBUJAR NODOS PRIMERO (z-order correcto) ───────────────
            editor.nodes.forEach(node => {
                const g = document.createElementNS(svgNS, 'g');
                g.setAttribute('class', 'node-' + node.type);

                if (node.type === 'actor') {
                    const cx = node.x + node.width/2;
                    const isSeq = (editor.diagramType||'').includes('sequence') || (editor.diagramType||'').includes('secuencia');
                    let actorInner = `
                        <circle cx="${cx}" cy="${node.y+14}" r="14" stroke="white" stroke-width="2" fill="none"/>
                        <line x1="${cx}" y1="${node.y+28}" x2="${cx}" y2="${node.y+65}" stroke="white" stroke-width="2"/>
                        <line x1="${cx-20}" y1="${node.y+42}" x2="${cx+20}" y2="${node.y+42}" stroke="white" stroke-width="2"/>
                        <line x1="${cx}" y1="${node.y+65}" x2="${cx-18}" y2="${node.y+90}" stroke="white" stroke-width="2"/>
                        <line x1="${cx}" y1="${node.y+65}" x2="${cx+18}" y2="${node.y+90}" stroke="white" stroke-width="2"/>
                        <text x="${cx}" y="${node.y+105}" text-anchor="middle" fill="white" font-size="12" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                    if (isSeq) {
                        const lifeY1 = node.y + 100, lifeY2 = node.y + node.height;
                        for (let y = lifeY1; y < lifeY2; y += 16)
                            actorInner += `<line x1="${cx}" y1="${y}" x2="${cx}" y2="${Math.min(y+8,lifeY2)}" stroke="white" stroke-width="2"/>`;
                    }
                    g.innerHTML = actorInner;
                } else if (node.type === 'usecase') {
                    const cx = node.x + node.width/2, cy = node.y + node.height/2;
                    const bg2 = node.backgroundColor || node.color || 'rgba(13,110,253,0.1)';
                    g.innerHTML = `
                        <ellipse cx="${cx}" cy="${cy}" rx="${node.width/2-2}" ry="${node.height/2-2}" stroke="#aaa" stroke-width="2" fill="${bg2}"/>
                        <text x="${cx}" y="${cy}" text-anchor="middle" dominant-baseline="central" fill="white" font-size="12" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                } else if (node.type === 'system') {
                    g.innerHTML = `
                        <rect x="${node.x}" y="${node.y}" width="${node.width}" height="${node.height}" fill="rgba(255,255,255,0.02)" stroke="#666" stroke-width="2" rx="4"/>
                        <text x="${node.x+16}" y="${node.y-5}" fill="#ccc" font-size="13" font-weight="600" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                } else if (['class','abstract','interface','object'].includes(node.type)) {
                    const underline = node.type === 'object' ? 'text-decoration="underline"' : '';
                    const italic    = node.type === 'abstract' ? 'font-style="italic"' : '';
                    const cx = node.x + node.width/2;
                    let y = node.y + 22;
                    let inner = `<rect x="${node.x}" y="${node.y}" width="${node.width}" height="${node.height}" fill="rgba(13,110,253,0.1)" stroke="#0d6efd" stroke-width="1.5" rx="3"/>`;
                    inner += `<text x="${cx}" y="${y}" text-anchor="middle" fill="white" font-size="13" font-weight="600" ${italic} ${underline} font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                    y += 6;
                    inner += `<line x1="${node.x}" y1="${y}" x2="${node.x+node.width}" y2="${y}" stroke="#0d6efd" stroke-width="1"/>`;
                    if (node.attributes) { node.attributes.split('\n').forEach(a => { y+=18; inner+=`<text x="${node.x+8}" y="${y}" fill="#ccc" font-size="11" font-family="'Segoe UI',sans-serif">${esc(a)}</text>`; }); }
                    y+=6; inner+=`<line x1="${node.x}" y1="${y}" x2="${node.x+node.width}" y2="${y}" stroke="#0d6efd" stroke-width="1"/>`;
                    if (node.methods)   { node.methods.split('\n').forEach(m => { y+=18; inner+=`<text x="${node.x+8}" y="${y}" fill="#ccc" font-size="11" font-family="'Segoe UI',sans-serif">${esc(m)}</text>`; }); }
                    g.innerHTML = inner;
                } else if (node.type === 'start' || node.type === 'initial') {
                    const cx=node.x+node.width/2, cy=node.y+node.height/2;
                    g.innerHTML = `<circle cx="${cx}" cy="${cy}" r="15" fill="#198754"/>
                        <text x="${cx}" y="${node.y+node.height+16}" text-anchor="middle" fill="white" font-size="11" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                } else if (node.type === 'end' || node.type === 'final') {
                    const cx=node.x+node.width/2, cy=node.y+node.height/2;
                    g.innerHTML = `<circle cx="${cx}" cy="${cy}" r="18" fill="none" stroke="#dc3545" stroke-width="2.5"/>
                        <circle cx="${cx}" cy="${cy}" r="11" fill="#dc3545"/>
                        <text x="${cx}" y="${node.y+node.height+16}" text-anchor="middle" fill="white" font-size="11" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                } else if (node.type === 'decision' || node.type === 'choice') {
                    const cx=node.x+node.width/2, cy=node.y+node.height/2;
                    g.innerHTML = `<polygon points="${cx},${node.y} ${node.x+node.width},${cy} ${cx},${node.y+node.height} ${node.x},${cy}"
                                   fill="rgba(253,126,20,0.15)" stroke="#fd7e14" stroke-width="2"/>
                        <text x="${cx}" y="${cy}" text-anchor="middle" dominant-baseline="central" fill="white" font-size="11" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                } else if (node.type === 'lifeline') {
                    const cx=node.x+node.width/2, headerH=36;
                    g.innerHTML = `<rect x="${node.x}" y="${node.y}" width="${node.width}" height="${headerH}" fill="rgba(13,110,253,0.15)" stroke="#0d6efd" stroke-width="1.5" rx="3"/>
                        <text x="${cx}" y="${node.y+headerH/2+4}" text-anchor="middle" fill="white" font-size="12" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>
                        <line x1="${cx}" y1="${node.y+headerH}" x2="${cx}" y2="${node.y+node.height}" stroke="#0d6efd" stroke-width="1.5" stroke-dasharray="6,4"/>`;
                } else if (node.type === 'fork' || node.type === 'union') {
                    const cx=node.x+node.width/2, cy=node.y+node.height/2;
                    const isH = node.width > node.height;
                    g.innerHTML = isH
                        ? `<rect x="${node.x}" y="${cy-3}" width="${node.width}" height="6" fill="#000" rx="1"/>`
                        : `<rect x="${cx-3}" y="${node.y}" width="6" height="${node.height}" fill="#000" rx="1"/>`;
                } else {
                    const fillMap={activity:'rgba(13,110,253,0.1)',state:'rgba(13,110,253,0.1)',component:'rgba(13,110,253,0.1)',node:'rgba(13,110,253,0.08)',device:'rgba(13,110,253,0.08)',artifact:'rgba(13,110,253,0.08)'};
                    g.innerHTML = `<rect x="${node.x}" y="${node.y}" width="${node.width}" height="${node.height}" fill="${fillMap[node.type]||'rgba(13,110,253,0.1)'}" stroke="#0d6efd" stroke-width="1.5" rx="5"/>
                        <text x="${node.x+node.width/2}" y="${node.y+node.height/2}" text-anchor="middle" dominant-baseline="central" fill="white" font-size="12" font-family="'Segoe UI',sans-serif">${esc(node.text)}</text>`;
                }
                svg.appendChild(g);
            });

            // ── 2. DIBUJAR CONEXIONES ENCIMA DE LOS NODOS ─────────────────
            // Grupo de conexiones — siempre encima de nodos
            const connGroup = document.createElementNS(svgNS, 'g');
            connGroup.setAttribute('class','connections');
            editor.connections.forEach(conn => {
                const fn = editor.nodes.find(n => n.id === conn.fromNode);
                const tn = editor.nodes.find(n => n.id === conn.toNode);
                if (!fn || !tn) return;

                // Centro de cada nodo
                const fcx = fn.x + fn.width/2,  fcy = fn.y + fn.height/2;
                const tcx = tn.x + tn.width/2,  tcy = tn.y + tn.height/2;

                // Punto de inicio y fin en el BORDE del nodo (no en el centro)
                let [x1,y1] = conn.fromSide ? getConnectionPoint(fn, conn.fromSide) : [fcx, fcy];
                let [x2,y2] = conn.toSide   ? getConnectionPoint(tn, conn.toSide)   : [tcx, tcy];

                // Si el punto es el centro (side='center' o no declarado), calcular borde real
                if (!conn.fromSide || conn.fromSide === 'center') [x1,y1] = borderPoint(fn, x2, y2);
                if (!conn.toSide   || conn.toSide   === 'center') [x2,y2] = borderPoint(tn, x1, y1);

                const markerMap = { herencia:'url(#ah-empty)', generalizacion:'url(#ah-empty)', composicion:'url(#diamond-f)', agregacion:'url(#diamond-e)' };
                const dashMap   = { dependencia:'5,5', include:'5,5', extend:'5,5', realizacion:'5,5', 'mensaje-asincrono':'5,5', 'mensaje-retorno':'5,5' };
                const marker    = markerMap[conn.type] || 'url(#ah)';
                const dash      = dashMap[conn.type]  || '';
                const color     = ['include','extend','dependencia'].includes(conn.type) ? '#999' : '#0d6efd';

                const path = document.createElementNS(svgNS, 'path');

                // Calcular curva que NO atraviesa los nodos
                const seqTypes = ['mensaje-sincrono','mensaje-asincrono','mensaje-retorno'];
                const bothLL = (fn.type==='lifeline'||fn.type==='actor') && (tn.type==='lifeline'||tn.type==='actor');
                if (seqTypes.includes(conn.type) || bothLL) {
                    path.setAttribute('d', `M ${x1} ${y1} L ${x2} ${y1}`);
                } else if (conn.waypoints && conn.waypoints.length > 0) {
                    let d = `M ${x1} ${y1}`;
                    conn.waypoints.forEach(wp => { d += ` L ${wp.x} ${wp.y}`; });
                    d += ` L ${x2} ${y2}`;
                    path.setAttribute('d', d);
                } else {
                    // Curva cuadrática: punto de control fuera del segmento de línea
                    const midX = (x1 + x2) / 2;
                    const midY = (y1 + y2) / 2;
                    // Desplazar el punto de control perpendicularmente para evitar pasar por nodos
                    const perpX = -(y2 - y1) * 0.15;
                    const perpY =  (x2 - x1) * 0.15;
                    path.setAttribute('d', `M ${x1} ${y1} Q ${midX + perpX} ${midY + perpY} ${x2} ${y2}`);
                }

                path.setAttribute('stroke', color);
                path.setAttribute('stroke-width', '2');
                path.setAttribute('fill', 'none');
                if (marker) path.setAttribute('marker-end', marker);
                if (dash)   path.setAttribute('stroke-dasharray', dash);
                connGroup.appendChild(path);

                // Etiqueta de conexión (si tiene label)
                if (conn.label) {
                    const midX = (x1 + x2) / 2, midY = (y1 + y2) / 2;
                    const lbg = document.createElementNS(svgNS, 'rect');
                    lbg.setAttribute('x', midX - 25); lbg.setAttribute('y', midY - 10);
                    lbg.setAttribute('width', '50'); lbg.setAttribute('height', '16');
                    lbg.setAttribute('fill', '#0d0d1a'); lbg.setAttribute('rx', '3');
                    connGroup.appendChild(lbg);
                    const lt = document.createElementNS(svgNS, 'text');
                    lt.setAttribute('x', midX); lt.setAttribute('y', midY);
                    lt.setAttribute('text-anchor', 'middle'); lt.setAttribute('dominant-baseline','central');
                    lt.setAttribute('fill', '#aaa'); lt.setAttribute('font-size', '10');
                    lt.setAttribute('font-family', "'Segoe UI',sans-serif");
                    lt.textContent = conn.label;
                    connGroup.appendChild(lt);
                }
            });
            svg.appendChild(connGroup);

            return svg;
        }

        function esc(s) {
            return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // ── Exportar SVG ───────────────────────────────────────────────
        function exportarSVG() {
            const svg = generarSVG();
            if (!svg) return;
            const titulo = document.getElementById('diagramaTitulo')?.textContent || 'diagrama';
            const serializer = new XMLSerializer();
            const svgStr = '<' + '?xml version="1.0" encoding="UTF-8"?>\n' + serializer.serializeToString(svg);
            descargarBlob(svgStr, sanitizarNombre(titulo) + '.svg', 'image/svg+xml');
        }

        // ── Exportar PNG ───────────────────────────────────────────────
        function exportarPNG() {
            // Close export modal and open the pre-export panel
            bootstrap.Modal.getInstance(document.getElementById('exportModal'))?.hide();
            setTimeout(() => abrirPanelExportacion('png'), 150);
            return;
            // (legacy below — kept for reference)
            const svg = generarSVG();
            if (!svg) return;
            const titulo = document.getElementById('diagramaTitulo')?.textContent || 'diagrama';
            const serializer = new XMLSerializer();
            const svgStr  = serializer.serializeToString(svg);
            const blob    = new Blob([svgStr], { type: 'image/svg+xml' });
            const url     = URL.createObjectURL(blob);
            const img     = new Image();
            const W       = parseInt(svg.getAttribute('width'))  || 800;
            const H       = parseInt(svg.getAttribute('height')) || 600;
            const scale   = 2; // alta resolución

            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width  = W * scale;
                canvas.height = H * scale;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#0d0d0d';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.scale(scale, scale);
                ctx.drawImage(img, 0, 0);
                URL.revokeObjectURL(url);
                canvas.toBlob(b => {
                    descargarBlob(b, sanitizarNombre(titulo) + '.png', 'image/png', true);
                }, 'image/png');
            };
            img.onerror = () => {
                URL.revokeObjectURL(url);
                alert('Error al generar PNG. Prueba con SVG.');
            };
            img.src = url;
        }

        // ── Exportar PDF ───────────────────────────────────────────────
        function exportarPDF() {
            bootstrap.Modal.getInstance(document.getElementById('exportModal'))?.hide();
            setTimeout(() => abrirPanelExportacion('pdf'), 150);
            return;
            const svg = generarSVG();
            if (!svg) return;
            const titulo = document.getElementById('diagramaTitulo')?.textContent || 'diagrama';
            const serializer = new XMLSerializer();
            const svgStr  = serializer.serializeToString(svg);
            const blob    = new Blob([svgStr], { type: 'image/svg+xml' });
            const url     = URL.createObjectURL(blob);
            const img     = new Image();
            const W       = parseInt(svg.getAttribute('width'))  || 800;
            const H       = parseInt(svg.getAttribute('height')) || 600;

            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width  = W * 2; canvas.height = H * 2;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.scale(2, 2);
                ctx.drawImage(img, 0, 0);
                URL.revokeObjectURL(url);

                const imgData = canvas.toDataURL('image/jpeg', 0.95);

                // Usar jsPDF si está disponible, sino fallback a ventana de impresión
                if (typeof window.jspdf !== 'undefined' || typeof window.jsPDF !== 'undefined') {
                    const jsPDF = window.jsPDF || window.jspdf?.jsPDF;
                    const orient = W > H ? 'landscape' : 'portrait';
                    const pdf = new jsPDF({ orientation: orient, unit: 'px', format: [W, H] });
                    pdf.addImage(imgData, 'JPEG', 0, 0, W, H);
                    pdf.save(sanitizarNombre(titulo) + '.pdf');
                } else {
                    // Fallback: abrir en ventana de impresión
                    const win = window.open('', '_blank');
                    win.document.write(`<!DOCTYPE html><html><head>
                        <title>${titulo}</title>
                        <style>body{margin:0;background:#fff;}img{max-width:100%;height:auto;display:block;}
                        @media print{@page{margin:10mm}body{margin:0}}</style>
                        </head><body>
                        <img src="${imgData}" alt="${titulo}"/>
                        <script>window.onload=()=>{window.print();}<\/script>
                        </body></html>`);
                    win.document.close();
                }
            };
            img.src = url;
        }

        // ── Helpers ────────────────────────────────────────────────────
        function descargarBlob(contenido, nombre, tipo, esBlob = false) {
            const blob = esBlob ? contenido : new Blob([contenido], { type: tipo });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = nombre;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        }

        function sanitizarNombre(nombre) {
            return nombre.replace(/[^a-zA-Z0-9_\-áéíóúüñÁÉÍÓÚÜÑ ]/g, '').trim().replace(/\s+/g, '_') || 'diagrama';
        }

        function mostrarAutoSave(mensaje, tipo = 'success') {
            const indicator = document.getElementById('autoSaveIndicator');
            if (!indicator) return;
            indicator.innerHTML = `<i class="bi bi-${tipo === 'success' ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-danger'}"></i> ${mensaje}`;
            indicator.style.display = 'block';
            setTimeout(() => { indicator.style.display = 'none'; }, 3000);
        }
        // ══════════════════════════════════════════════════════════════
        // IMPORTACIÓN
        // ══════════════════════════════════════════════════════════════
        let _importData = null; // datos parseados listos para cargar

        function abrirImportar() {
            _importData = null;
            document.getElementById('importFileInput').value = '';
            document.getElementById('importFileInfo').classList.add('d-none');
            document.getElementById('importPreview').classList.add('d-none');
            document.getElementById('importError').classList.add('d-none');
            document.getElementById('btnConfirmarImport').disabled = true;
            new bootstrap.Modal(document.getElementById('importModal')).show();
        }

        function handleImportDrop(e) {
            e.preventDefault();
            document.getElementById('dropZone').style.borderColor = '';
            const file = e.dataTransfer.files[0];
            if (file) procesarArchivoImportado(file);
        }

        function procesarArchivoImportado(file) {
            if (!file) return;
            const maxSize = 5 * 1024 * 1024; // 5 MB
            const errorEl = document.getElementById('importError');
            errorEl.classList.add('d-none');
            document.getElementById('importPreview').classList.add('d-none');
            document.getElementById('btnConfirmarImport').disabled = true;
            _importData = null;

            // Info del archivo
            document.getElementById('importFileName').textContent = file.name;
            document.getElementById('importFileSize').textContent = formatBytes(file.size);
            document.getElementById('importFileInfo').classList.remove('d-none');

            if (file.size > maxSize) {
                mostrarImportError('El archivo supera el límite de 5 MB.');
                return;
            }

            const ext = file.name.split('.').pop().toLowerCase();
            const reader = new FileReader();

            reader.onload = (e) => {
                try {
                    let datos = null;

                    if (ext === 'json') {
                        datos = importarDesdeJSON(e.target.result, file.name);
                    } else if (ext === 'svg') {
                        datos = importarDesdeSVG(e.target.result, file.name);
                    } else {
                        throw new Error('Formato no soportado. Usa JSON o SVG.');
                    }

                    if (!datos) throw new Error('No se pudo interpretar el archivo.');

                    _importData = datos;
                    mostrarImportPreview(datos);
                    document.getElementById('btnConfirmarImport').disabled = false;

                } catch (err) {
                    mostrarImportError(err.message);
                }
            };
            reader.readAsText(file);
        }

        // ── Parser JSON ────────────────────────────────────────────────
        function importarDesdeJSON(text, filename) {
            const raw = JSON.parse(text);

            // Formato nativo del editor (v3/v4): { contenido: { nodes, connections, diagramType } }
            if (raw.contenido && Array.isArray(raw.contenido.nodes)) {
                return {
                    titulo:      raw.titulo || raw.contenido.titulo || sinExt(filename),
                    tipo:        raw.contenido.diagramType || raw.tipo || 'usecase',
                    nodes:       raw.contenido.nodes,
                    connections: raw.contenido.connections || []
                };
            }

            // Formato exportación directa: { nodes, connections, diagramType }
            if (Array.isArray(raw.nodes)) {
                return {
                    titulo:      raw.titulo || sinExt(filename),
                    tipo:        raw.diagramType || raw.tipo || 'usecase',
                    nodes:       raw.nodes,
                    connections: raw.connections || []
                };
            }

            // Formato Diagrams.net / draw.io XML en JSON (conversión básica)
            if (raw.diagram || raw.mxGraphModel) {
                return convertirDrawIO(raw, filename);
            }

            throw new Error('El JSON no tiene el formato esperado (nodes/connections).');
        }

        // ── Parser SVG ─────────────────────────────────────────────────
        // Extrae texto de cada elemento SVG y crea nodos de texto genéricos
        function importarDesdeSVG(text, filename) {
            const parser = new DOMParser();
            const doc    = parser.parseFromString(text, 'image/svg+xml');
            const svgEl  = doc.querySelector('svg');
            if (!svgEl) throw new Error('SVG inválido o vacío.');

            const nodes = [];
            let   idCounter = 1;
            const tipo = detectarTipoSVG(doc) || 'usecase';

            // Rectángulos → nodos genéricos
            doc.querySelectorAll('rect[x][y][width][height]').forEach(r => {
                const x = parseFloat(r.getAttribute('x')) || 10;
                const y = parseFloat(r.getAttribute('y')) || 10;
                const w = parseFloat(r.getAttribute('width'))  || 140;
                const h = parseFloat(r.getAttribute('height')) || 60;
                if (w < 5 || h < 5) return; // ignorar fondo/decoración
                const label = buscarTextoSVGCercano(doc, x + w/2, y + h/2) || 'Elemento';
                nodes.push({ id: `imp_${idCounter++}`, x, y, width: w, height: h, text: label, type: 'activity', color: '#0d6efd', attributes: '', methods: '' });
            });

            // Elipses → usecase
            doc.querySelectorAll('ellipse[cx][cy][rx][ry]').forEach(el => {
                const cx = parseFloat(el.getAttribute('cx'));
                const cy = parseFloat(el.getAttribute('cy'));
                const rx = parseFloat(el.getAttribute('rx')) || 75;
                const ry = parseFloat(el.getAttribute('ry')) || 30;
                const label = buscarTextoSVGCercano(doc, cx, cy) || 'Caso de Uso';
                nodes.push({ id: `imp_${idCounter++}`, x: cx - rx, y: cy - ry, width: rx*2, height: ry*2, text: label, type: 'usecase', color: '#0d6efd', attributes: '', methods: '' });
            });

            // Círculos pequeños → inicial/final
            doc.querySelectorAll('circle[cx][cy][r]').forEach(c => {
                const r  = parseFloat(c.getAttribute('r'));
                if (r > 30) return;
                const cx = parseFloat(c.getAttribute('cx'));
                const cy = parseFloat(c.getAttribute('cy'));
                const t  = r < 15 ? 'initial' : 'final';
                nodes.push({ id: `imp_${idCounter++}`, x: cx - r, y: cy - r, width: r*2, height: r*2, text: t === 'initial' ? 'Inicio' : 'Fin', type: t, color: t === 'initial' ? '#198754' : '#dc3545', attributes: '', methods: '' });
            });

            if (nodes.length === 0) throw new Error('No se encontraron formas reconocibles en el SVG.');

            return {
                titulo:      sinExt(filename),
                tipo,
                nodes,
                connections: []
            };
        }

        function detectarTipoSVG(doc) {
            const text = doc.body?.textContent || doc.documentElement?.textContent || '';
            if (/actor|usecase|caso.de.uso/i.test(text)) return 'usecase';
            if (/class|clase|interface/i.test(text)) return 'class';
            if (/state|estado|transition/i.test(text)) return 'state';
            if (/activity|actividad|decision/i.test(text)) return 'activity';
            if (/sequence|lifeline|mensaje/i.test(text)) return 'sequence';
            if (/component|componente/i.test(text)) return 'component';
            if (/deploy|nodo|node/i.test(text)) return 'deployment';
            return 'activity';
        }

        function buscarTextoSVGCercano(doc, cx, cy) {
            let best = null, dist = Infinity;
            doc.querySelectorAll('text').forEach(t => {
                const tx = parseFloat(t.getAttribute('x') || t.getAttribute('cx') || 0);
                const ty = parseFloat(t.getAttribute('y') || t.getAttribute('cy') || 0);
                const d  = Math.hypot(tx - cx, ty - cy);
                if (d < dist) { dist = d; best = t.textContent.trim(); }
            });
            return dist < 200 ? best : null;
        }

        // ── Conversión draw.io básica ──────────────────────────────────
        function convertirDrawIO(raw, filename) {
            // draw.io exporta a XML, no JSON puro; esta función es un placeholder
            // para el caso en que el usuario exporte desde draw.io como JSON
            throw new Error('Formato draw.io no soportado directamente. Exporta desde draw.io como XML y luego importa como SVG, o usa el JSON nativo del editor.');
        }

        // ── Preview en modal ───────────────────────────────────────────
        function mostrarImportPreview(datos) {
            const TIPOS = {
                usecase:'Casos de Uso', class:'Clases', sequence:'Secuencia',
                activity:'Actividades', state:'Estados', component:'Componentes',
                deployment:'Despliegue', object:'Objetos', communication:'Comunicación', timing:'Tiempo'
            };
            document.getElementById('importPreviewTitulo').textContent = datos.titulo || 'Sin título';
            document.getElementById('importPreviewTipo').textContent   = TIPOS[datos.tipo] || datos.tipo;
            document.getElementById('importPreviewNodes').textContent  = datos.nodes.length;
            document.getElementById('importPreviewConns').textContent  = datos.connections.length;
            document.getElementById('importPreview').classList.remove('d-none');
        }

        function mostrarImportError(msg) {
            const el = document.getElementById('importError');
            el.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + escHtml(msg);
            el.classList.remove('d-none');
        }

        // ── Confirmar: cargar en el editor ─────────────────────────────
        function confirmarImportacion() {
            if (!_importData || !editor) return;

            editor.nodes       = _importData.nodes;
            editor.connections = _importData.connections;
            editor.diagramType = _importData.tipo;

            // Actualizar el título si es diagrama nuevo
            const tituloEl = document.getElementById('diagramaTitulo');
            if (tituloEl && (!diagramaId)) {
                tituloEl.textContent = _importData.titulo;
            }

            // Actualizar paleta de figuras y flechas según el tipo
            editor.loadShapesForType(editor.diagramType);
            if (typeof editor.loadArrowsForType === 'function') {
                editor.loadArrowsForType(editor.diagramType);
            }
            // Init connector style picker
            if (typeof initConnStylePicker === 'function') initConnStylePicker();

            // Actualizar selector de tipo
            const tipoDisplay = document.getElementById('diagramTypeDisplay');
            const tipoMap = {
                'usecase':'Diagrama de Casos de Uso','class':'Diagrama de Clases',
                'sequence':'Diagrama de Secuencia','activity':'Diagrama de Actividades',
                'state':'Diagrama de Estados','component':'Diagrama de Componentes',
                'deployment':'Diagrama de Despliegue','object':'Diagrama de Objetos',
                'communication':'Diagrama de Comunicación','timing':'Diagrama de Tiempo'
            };
            if (tipoDisplay) tipoDisplay.value = tipoMap[editor.diagramType] || editor.diagramType;

            editor.render();
            editor.pushToHistory();
            editor.unsavedChanges = true;

            bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
            mostrarAutoSave('Diagrama importado. Guarda para persistir los cambios.', 'success');

            _importData = null;
        }

        // ── Helpers ────────────────────────────────────────────────────
        function sinExt(filename) {
            return filename.replace(/\.[^/.]+$/, '');
        }
        function escHtml(s) {
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        }
        function formatBytes(b) {
            if (b < 1024) return b + ' B';
            if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
            return (b/1048576).toFixed(1) + ' MB';
        }

        // ════════════════════════════════════════════════════════════════
        // ZOOM & PAN — Canvas infinito
        // ════════════════════════════════════════════════════════════════
        let _zoom   = 1.0;
        window._panX   = 0;
        window._panY   = 0;
        let _panning = false;
        let _panStart = { x: 0, y: 0 };

        // Para compatibilidad, crear referencias locales
        let _panX = window._panX;
        let _panY = window._panY;

        function initZoomPan() {
            const container = document.getElementById('canvasContainer');
            const viewport  = document.getElementById('canvasViewport');
            if (!container || !viewport) return;

            // Wheel → zoom centrado en el cursor
            container.addEventListener('wheel', (e) => {
                e.preventDefault();
                const delta = e.deltaY > 0 ? -0.08 : 0.08;
                const rect  = container.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const mouseY = e.clientY - rect.top;
                const newZoom = Math.max(0.2, Math.min(3, _zoom + delta));
                _panX = mouseX - (mouseX - _panX) * (newZoom / _zoom);
                _panY = mouseY - (mouseY - _panY) * (newZoom / _zoom);
                _zoom = newZoom;
                applyTransform();
            }, { passive: false });

            // Click medio + arrastrar → pan (mover el lienzo)
            container.addEventListener('mousedown', (e) => {
                if (e.button === 1 || (e.button === 0 && e.altKey)) {
                    e.preventDefault();
                    _panning   = true;
                    _panStart  = { x: e.clientX - _panX, y: e.clientY - _panY };
                    container.style.cursor = 'grabbing';
                }
            });
            window.addEventListener('mousemove', (e) => {
                if (!_panning) return;
                _panX = e.clientX - _panStart.x;
                _panY = e.clientY - _panStart.y;
                applyTransform();
            });
            window.addEventListener('mouseup', (e) => {
                if (e.button === 1 || _panning) {
                    _panning = false;
                    container.style.cursor = '';
                }
            });
        }

        function applyTransform() {
            const viewport = document.getElementById('canvasViewport');
            if (!viewport) return;
            viewport.style.transform = `translate(${_panX}px, ${_panY}px) scale(${_zoom})`;
            const pct = Math.round(_zoom * 100) + '%';
            const zl  = document.getElementById('zoomLabel');
            const zlf = document.getElementById('zoomLevelFloat');
            if (zl)  zl.textContent  = pct;
            if (zlf) zlf.textContent = pct;
            renderPreviewSection();
        }

        // Hacer applyTransform global
        window.applyTransform = applyTransform;

        function editorZoom(delta) {
            const container = document.getElementById('canvasContainer');
            const rect      = container.getBoundingClientRect();
            const cx = rect.width / 2, cy = rect.height / 2;
            const newZoom = Math.max(0.2, Math.min(3, _zoom + delta));
            _panX = cx - (cx - _panX) * (newZoom / _zoom);
            _panY = cy - (cy - _panY) * (newZoom / _zoom);
            _zoom = newZoom;
            applyTransform();
        }

        function editorZoomReset() {
            _zoom = 1; _panX = 0; _panY = 0;
            applyTransform();
        }

        function editorFitContent() {
            if (!editor || !editor.nodes.length) { editorZoomReset(); return; }
            const container = document.getElementById('canvasContainer');
            const rect      = container.getBoundingClientRect();
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            editor.nodes.forEach(n => {
                minX = Math.min(minX, n.x);
                minY = Math.min(minY, n.y);
                maxX = Math.max(maxX, n.x + (n.width  || 100));
                maxY = Math.max(maxY, n.y + (n.height || 60));
            });
            const PAD   = 60;
            const cW    = maxX - minX + PAD * 2;
            const cH    = maxY - minY + PAD * 2;
            const scaleX = rect.width  / cW;
            const scaleY = rect.height / cH;
            _zoom = Math.max(0.2, Math.min(1.5, Math.min(scaleX, scaleY)));
            _panX = (rect.width  - (maxX - minX) * _zoom) / 2 - minX * _zoom;
            _panY = (rect.height - (maxY - minY) * _zoom) / 2 - minY * _zoom;
            applyTransform();
        }

        function togglePreviewSection(enabled) {
            const previewSection = document.getElementById('diagramPreviewSection');
            const previewStatus  = document.getElementById('previewStatusText');
            if (previewSection) {
                previewSection.style.display = enabled ? 'block' : 'none';
            }
            if (previewStatus) {
                previewStatus.textContent = enabled ? 'Activo' : 'Oculto';
            }
            if (enabled) {
                renderPreviewSection();
            }
        }

        function renderPreviewSection() {
            const previewCanvas = document.getElementById('minimapCanvas');
            if (!previewCanvas) return;
            const pct = Math.round(_zoom * 100) + '%';
            const xPos = Math.round(_panX);
            const yPos = Math.round(_panY);
            previewCanvas.innerHTML = `
                <div class="preview-placeholder">
                    <strong>Zoom:</strong> ${pct}<br>
                    <strong>Posición:</strong> ${xPos}px, ${yPos}px<br>
                    <small>La vista general está activada.</small>
                </div>
            `;
        }

        // ════════════════════════════════════════════════════════════════
        // SISTEMA DE PESTAÑAS Y COLLAPSE
        // ════════════════════════════════════════════════════════════════
        
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('leftSidebar');
            const btn = sidebar.querySelector('.sidebar-collapse-btn-outer i');
            const isCollapsed = sidebar.classList.toggle('collapsed');
            sidebar.style.width = isCollapsed ? '40px' : '280px';
            btn.className = isCollapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
        }

        function togglePropertiesCollapse() {
            const panel = document.getElementById('rightPanel');
            const btn = panel.querySelector('.properties-collapse-btn-outer i');
            const isCollapsed = panel.classList.toggle('collapsed');
            panel.style.width = isCollapsed ? '40px' : '300px';
            btn.className = isCollapsed ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
        }
        
        function switchSidebarTab(tabName) {
            // Ocultar todas las pestañas de contenido
            const tabContents = document.querySelectorAll('.sidebar-tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            // Mostrar solo la pestaña activa
            const activeTab = document.getElementById('tab-' + tabName);
            if (activeTab) activeTab.classList.add('active');
            
            // Actualizar botones activos
            const tabBtns = document.querySelectorAll('.sidebar-tab-btn');
            tabBtns.forEach(btn => btn.classList.remove('active'));
            
            // Encontrar y activar el botón correspondiente
            const btnMap = {
                'diagrama': 0,
                'figuras': 1,
                'capas': 2
            };
            if (btnMap[tabName] !== undefined) {
                tabBtns[btnMap[tabName]].classList.add('active');
            }
        }

        function switchPropertiesTab(tabName) {
            // Ocultar todas las pestañas de contenido
            const tabContents = document.querySelectorAll('.properties-tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            // Mostrar solo la pestaña activa
            const activeTab = document.getElementById('tab-' + tabName);
            if (activeTab) activeTab.classList.add('active');
            
            // Actualizar botones activos
            const tabBtns = document.querySelectorAll('.properties-tab-btn');
            tabBtns.forEach(btn => btn.classList.remove('active'));
            
            // Encontrar y activar el botón correspondiente
            const btnMap = {
                'element': 0,
                'connection': 1,
                'info': 2
            };
            if (btnMap[tabName] !== undefined) {
                tabBtns[btnMap[tabName]].classList.add('active');
            }
        }

    </script>
<!-- AI Chat Panel moved to sidebar (sideAIPanel) -->
<div id="aiChatPanel" style="display:none">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,var(--primary),var(--primary2));padding:16px 18px;display:flex;align-items:center;gap:12px;flex-shrink:0">
        <i class="bi bi-robot" style="font-size:1.3rem;color:#fff"></i>
        <div style="flex:1">
            <div style="color:#fff;font-weight:700;font-size:.9rem">Asistente IA</div>
            <div style="color:rgba(255,255,255,.7);font-size:.7rem">Analiza tu diagrama y da sugerencias</div>
        </div>
        <button onclick="toggleAIChat()" title="Minimizar"
            style="background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0"
            onmouseover="this.style.background='rgba(255,255,255,.35)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
            <i class="bi bi-chevron-down" style="font-size:.85rem"></i>
        </button>
    </div>

    <!-- Context upload area -->
    <div id="aiContextArea" style="padding:10px 14px;border-bottom:1px solid #2a2a4a;flex-shrink:0">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
            <span style="font-size:.7rem;color:#888;text-transform:uppercase;letter-spacing:.06em">Contexto adicional (opcional)</span>
            <button onclick="toggleContextPanel()" style="background:none;border:none;color:#667eea;font-size:.7rem;cursor:pointer">
                <i class="bi bi-chevron-down" id="ctxChevron"></i>
            </button>
        </div>
        <div id="ctxPanel" style="display:none">
            <!-- Tabs: texto o archivo -->
            <div style="display:flex;gap:6px;margin-bottom:8px">
                <button id="ctxTabText" onclick="setCtxTab('text')"
                    style="flex:1;background:rgba(102,126,234,.2);border:1px solid rgba(102,126,234,.4);color:#aab8ff;border-radius:6px;padding:5px;font-size:.72rem;cursor:pointer">
                    <i class="bi bi-card-text me-1"></i>Texto
                </button>
                <button id="ctxTabFile" onclick="setCtxTab('file')"
                    style="flex:1;background:transparent;border:1px solid #2a2a4a;color:#666;border-radius:6px;padding:5px;font-size:.72rem;cursor:pointer">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i>Archivo
                </button>
            </div>
            <!-- Texto libre -->
            <div id="ctxTextArea">
                <textarea id="aiContextText" rows="3" placeholder="Pega aquí el enunciado, requerimientos, descripción del sistema o cualquier contexto relevante…"
                    style="width:100%;background:#0d0d1a;border:1px solid #2a2a4a;border-radius:8px;color:#ccc;padding:8px 10px;font-size:.76rem;resize:vertical;outline:none"></textarea>
            </div>
            <!-- Archivo -->
            <div id="ctxFileArea" style="display:none">
                <div id="ctxDropZone"
                    style="border:2px dashed #2a2a4a;border-radius:8px;padding:16px;text-align:center;cursor:pointer;transition:all .2s"
                    onmouseover="this.style.borderColor='#667eea'" onmouseout="this.style.borderColor='#2a2a4a'"
                    onclick="document.getElementById('ctxFileInput').click()"
                    ondragover="event.preventDefault();this.style.borderColor='#667eea';this.style.background='rgba(102,126,234,.08)'"
                    ondragleave="this.style.borderColor='#2a2a4a';this.style.background='none'"
                    ondrop="handleCtxFileDrop(event)">
                    <i class="bi bi-file-earmark-text" style="font-size:1.5rem;color:#667eea;display:block;margin-bottom:6px"></i>
                    <div style="color:#888;font-size:.75rem">Arrastra o haz clic para cargar</div>
                    <div style="color:#555;font-size:.68rem;margin-top:3px">.txt · .pdf · .docx · .md</div>
                </div>
                <input type="file" id="ctxFileInput" accept=".txt,.pdf,.docx,.md" style="display:none" onchange="handleCtxFile(this.files[0])">
                <div id="ctxFileStatus" style="margin-top:6px;font-size:.72rem;color:#10b981;display:none"></div>
            </div>
        </div>
    </div>

    <!-- Chat messages -->
    <div id="aiMessages" style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px">
        <div style="background:rgba(102,126,234,.08);border:1px solid rgba(102,126,234,.2);border-radius:10px;padding:12px 14px">
            <div style="color:#aab8ff;font-size:.78rem;font-weight:600;margin-bottom:5px"><i class="bi bi-robot me-1"></i>Asistente IA</div>
            <div style="color:#ccc;font-size:.8rem;line-height:1.6">
                ¡Hola! Puedo analizar tu diagrama y darte sugerencias de mejora.<br><br>
                Puedes preguntarme sobre:<br>
                • ¿Está completo mi diagrama?<br>
                • ¿Qué elementos me faltan?<br>
                • ¿Hay errores de notación UML?<br>
                • ¿Cómo puedo mejorar la claridad?<br><br>
                También puedes agregar <strong style="color:#aab8ff">contexto adicional</strong> arriba (texto o archivo) para que mis sugerencias sean más precisas.
            </div>
        </div>
    </div>

    <!-- Input area -->
    <div style="padding:12px 14px;border-top:1px solid #2a2a4a;flex-shrink:0">
        <!-- Quick buttons -->
        <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px">
            ${['Analiza mi diagrama completo','¿Qué elementos faltan?','¿Hay errores de notación?','Dame sugerencias de mejora','¿Es correcto para el tipo ${tipoDiagrama}?'].map(q =>
                `<button onclick="sendAIMessage('${q.replace(/'/g,"\'")}','${q.replace(/'/g,"\'")}', true)"
                    style="background:rgba(102,126,234,.1);border:1px solid rgba(102,126,234,.25);color:#aab8ff;border-radius:16px;padding:3px 10px;font-size:.68rem;cursor:pointer;white-space:nowrap;transition:all .15s"
                    onmouseover="this.style.background='rgba(102,126,234,.2)'" onmouseout="this.style.background='rgba(102,126,234,.1)'">
                    ${q}
                </button>`).join('')}
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end">
            <textarea id="aiInput" rows="2" placeholder="Escribe tu pregunta o pide sugerencias…"
                style="flex:1;background:#0d0d1a;border:1px solid #2a2a4a;border-radius:10px;color:#ccc;padding:9px 12px;font-size:.8rem;resize:none;outline:none;transition:border-color .2s"
                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#2a2a4a'"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendAIMessage()}"></textarea>
            <button onclick="sendAIMessage()" id="aiSendBtn"
                style="background:linear-gradient(135deg,var(--primary),var(--primary2));border:none;color:#fff;border-radius:10px;padding:10px 14px;cursor:pointer;flex-shrink:0;transition:opacity .2s">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
        <div style="text-align:center;margin-top:6px">
            <span style="font-size:.63rem;color:#444">Powered by Claude AI · El diagrama se analiza en tiempo real</span>
        </div>
    </div>
</div>
<!-- AI Chat overlay removed (floating window doesn't need it) -->

<script>
// ══ AI CHAT LOGIC ══════════════════════════════════════════════════

let _aiOpen = false;
let _aiHistory = [];
let _ctxText = '';
let _ctxTab = 'text';

function toggleAIChat() {
    setSideTab('chat');
}
function _toggleAIChat_legacy() {
    _aiOpen = !_aiOpen;
    const panel = document.getElementById('aiChatPanel');
    if (panel) panel.style.display = _aiOpen ? 'flex' : 'none';
    const btn = document.getElementById('aiChatBtn');
    if (btn) btn.style.background = _aiOpen ? 'rgba(var(--primary-rgb),.3)' : '';
    if (_aiOpen) {
        document.getElementById('aiInput')?.focus();
        // Check provider status once
        if (!_aiStatusChecked) {
            _aiStatusChecked = true;
            fetch((window.BASE_URL||'') + '/api/chat-status')
                .then(r=>r.json())
                .then(d => {
                    const hdr = document.querySelector('#aiChatPanel .ai-provider-badge');
                    if (hdr) return;
                    const badge = document.createElement('div');
                    badge.className = 'ai-provider-badge';
                    badge.style.cssText = 'padding:4px 14px;font-size:.68rem;text-align:center;border-bottom:1px solid #2a2a4a;' +
                        (d.configured ? 'color:#10b981;background:rgba(16,185,129,.08)' : 'color:#f59e0b;background:rgba(245,158,11,.08)');
                    badge.innerHTML = d.configured
                        ? `<i class="bi bi-check-circle me-1"></i>Proveedor: <strong>${d.provider}</strong> · ${d.model}`
                        : `<i class="bi bi-exclamation-triangle me-1"></i>Sin configurar — escribe para ver instrucciones`;
                    const msgs = document.getElementById('aiMessages');
                    msgs?.parentNode?.insertBefore(badge, msgs);
                }).catch(()=>{});
        }
    }
}
let _aiStatusChecked = false;

function toggleContextPanel() {
    const p = document.getElementById('ctxPanel');
    const ch = document.getElementById('ctxChevron');
    const open = p.style.display !== 'none';
    p.style.display = open ? 'none' : 'block';
    ch.className = open ? 'bi bi-chevron-down' : 'bi bi-chevron-up';
}

function setCtxTab(tab) {
    _ctxTab = tab;
    const isText = tab === 'text';
    document.getElementById('ctxTextArea').style.display  = isText ? 'block' : 'none';
    document.getElementById('ctxFileArea').style.display  = isText ? 'none'  : 'block';
    document.getElementById('ctxTabText').style.background = isText ? 'rgba(102,126,234,.2)' : 'transparent';
    document.getElementById('ctxTabText').style.color      = isText ? '#aab8ff' : '#666';
    document.getElementById('ctxTabFile').style.background = !isText ? 'rgba(102,126,234,.2)' : 'transparent';
    document.getElementById('ctxTabFile').style.color      = !isText ? '#aab8ff' : '#666';
}

function handleCtxFileDrop(e) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#2a2a4a';
    e.currentTarget.style.background  = 'none';
    const file = e.dataTransfer.files[0];
    if (file) handleCtxFile(file);
}

async function handleCtxFile(file) {
    if (!file) return;
    const allowed = ['text/plain','text/markdown','application/pdf',
                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    const statusEl = document.getElementById('ctxFileStatus');
    statusEl.style.display = 'block';
    statusEl.style.color   = '#888';
    statusEl.textContent   = `Cargando ${file.name}…`;

    try {
        if (file.type === 'text/plain' || file.name.endsWith('.md') || file.name.endsWith('.txt')) {
            _ctxText = await file.text();
            statusEl.style.color   = '#10b981';
            statusEl.textContent   = `✓ ${file.name} (${(_ctxText.length/1000).toFixed(1)}k caracteres)`;
        } else if (file.type === 'application/pdf') {
            // Read first 6000 chars of PDF text via FileReader
            const ab = await file.arrayBuffer();
            const u8  = new Uint8Array(ab);
            // Simple PDF text extraction: grab strings between BT and ET markers
            const str = new TextDecoder('latin1').decode(u8);
            const matches = [...str.matchAll(/BT\s*(.*?)\s*ET/gs)].map(m=>m[1]);
            _ctxText = matches.join('
').replace(/\(([^)]+)\)/g,'$1').slice(0, 8000);
            statusEl.style.color   = _ctxText.length > 100 ? '#10b981' : '#f59e0b';
            statusEl.textContent   = _ctxText.length > 100
                ? `✓ ${file.name} extraído`
                : '⚠ PDF con texto no extraíble. Prueba copiando el texto.';
        } else if (file.name.endsWith('.docx')) {
            statusEl.style.color   = '#f59e0b';
            statusEl.textContent   = 'Para .docx copia el texto y usa la pestaña Texto';
            _ctxText = '';
        } else {
            _ctxText = await file.text();
            statusEl.style.color   = '#10b981';
            statusEl.textContent   = `✓ ${file.name} cargado`;
        }
    } catch(err) {
        statusEl.style.color   = '#ef4444';
        statusEl.textContent   = 'Error al leer el archivo: ' + err.message;
    }
}

function _getDiagramSnapshot() {
    // Get current diagram data from the editor
    if (typeof editor === 'undefined') return null;
    try {
        return {
            type: editor.diagramType || tipoDiagrama,
            nodes: (editor.nodes || []).map(n => ({
                id: n.id, type: n.type, text: n.text||'',
                x: Math.round(n.x), y: Math.round(n.y),
                attributes: n.attributes||'', methods: n.methods||''
            })),
            connections: (editor.connections || []).map(c => ({
                from: c.fromNode, to: c.toNode, type: c.type||'', label: c.label||''
            }))
        };
    } catch { return null; }
}

function _buildSystemPrompt(diagramData) {
    const TIPOS = {usecase:'Casos de Uso (Use Case)',class:'Clases (Class Diagram)',sequence:'Secuencia (Sequence)',activity:'Actividades (Activity)',state:'Estados (State Machine)',component:'Componentes (Component)',deployment:'Despliegue (Deployment)',object:'Objetos (Object)',communication:'Comunicación (Communication)'};
    const tipo = TIPOS[diagramData?.type] || diagramData?.type || 'UML';
    const nodos = diagramData?.nodes || [];
    const conex = diagramData?.connections || [];

    let sys = `Eres un experto en diagramas UML y diseño de software integrado en un editor de diagramas.
Tu rol es analizar el diagrama actual y dar sugerencias claras, concretas y accionables para mejorarlo.

DIAGRAMA ACTUAL:
Tipo: ${tipo}
Nodos (${nodos.length}): ${nodos.map(n=>`${n.type}:"${n.text}"${n.attributes?'['+n.attributes+']':''}`).join(' | ')||'(ninguno)'}
Conexiones (${conex.length}): ${conex.map(c=>`${c.from}→${c.to}(${c.type||'→'}${c.label?' "'+c.label+'"':''})`).join(' | ')||'(ninguna)'}

REGLAS DE RESPUESTA:
- Responde en español
- Sé conciso pero completo (máx 5 sugerencias por respuesta)
- Formatea con bullet points cuando des listas
- Si el diagrama está vacío, pide que agreguen elementos antes de analizar
- Referencia elementos específicos del diagrama por su nombre cuando sea posible
- Si hay un error de notación UML, explica la norma correcta brevemente`;

    const ctx = _ctxTab === 'text'
        ? (document.getElementById('aiContextText')?.value || '')
        : _ctxText;

    if (ctx && ctx.trim()) {
        sys += `

CONTEXTO ADICIONAL DEL USUARIO:
${ctx.slice(0, 6000)}`;
    }
    return sys;
}

function _appendMessage(role, text, streaming = false) {
    const msgs = document.getElementById('aiMessages');
    const isAI = role === 'assistant';
    const div = document.createElement('div');
    div.style.cssText = `background:${isAI ? 'rgba(102,126,234,.08)' : 'rgba(255,255,255,.04)'};border:1px solid ${isAI ? 'rgba(102,126,234,.2)' : '#2a2a4a'};border-radius:10px;padding:10px 13px`;
    div.innerHTML = `<div style="color:${isAI ? '#aab8ff' : '#888'};font-size:.72rem;font-weight:600;margin-bottom:5px">
        <i class="bi bi-${isAI ? 'robot' : 'person-circle'} me-1"></i>${isAI ? 'Asistente IA' : 'Tú'}
    </div>
    <div class="ai-msg-body" style="color:#ccc;font-size:.8rem;line-height:1.65;white-space:pre-wrap">${text}</div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div.querySelector('.ai-msg-body');
}

function setSideTab(tab) {
    const isPreview = tab === 'preview';
    document.getElementById('sidePreviewPanel').style.display = isPreview ? 'block' : 'none';
    const chatPanel = document.getElementById('sideAIPanel');
    if (chatPanel) chatPanel.style.display = isPreview ? 'none' : 'flex';
    const btnP = document.getElementById('sideTabPreview');
    const btnC = document.getElementById('sideTabChat');
    if (btnP) { btnP.style.background = isPreview ? 'rgba(255,255,255,.18)' : 'rgba(255,255,255,.07)'; btnP.style.color = isPreview ? '#fff' : 'rgba(255,255,255,.55)'; btnP.style.fontWeight = isPreview ? '600' : '400'; }
    if (btnC) { btnC.style.background = !isPreview ? 'rgba(255,255,255,.18)' : 'rgba(255,255,255,.07)'; btnC.style.color = !isPreview ? '#fff' : 'rgba(255,255,255,.55)'; btnC.style.fontWeight = !isPreview ? '600' : '400'; }
    if (!isPreview) document.getElementById('aiInput')?.focus();
}

async function sendAIMessage(overrideText = null, displayText = null, isQuick = false) {
    const input = document.getElementById('aiInput');
    const userMsg = overrideText || input?.value.trim();
    if (!userMsg) return;
    if (!isQuick && input) input.value = '';

    const diag = _getDiagramSnapshot();
    _appendMessage('user', displayText || userMsg);
    _aiHistory.push({ role: 'user', content: userMsg });

    // Disable send button while generating
    const btn = document.getElementById('aiSendBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<div class="spinner-border spinner-border-sm" style="width:.9rem;height:.9rem"></div>'; }

    // Add streaming response placeholder
    const msgBody = _appendMessage('assistant', '');
    let full = '';

    try {
        const sysPrompt = _buildSystemPrompt(diag);
        // Keep history to last 8 messages for context window
        const historySlice = _aiHistory.slice(-8);

        const res = await fetch((window.BASE_URL||'') + '/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                system:   sysPrompt,
                messages: historySlice,
                context:  _ctxTab === 'text'
                            ? (document.getElementById('aiContextText')?.value || '')
                            : _ctxText
            })
        });

        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Error del servidor');
        if (data.demo) {
            msgBody.style.color = '#f59e0b';
        }

        const text = data.text || '(sin respuesta)';
        full = text;
        // Format markdown-like: bold, bullets
        msgBody.innerHTML = full
            .replace(/\*\*(.*?)\*\*/g,'<strong style="color:#fff">$1</strong>')
            .replace(/^• /gm,'<span style="color:var(--primary)">▸</span> ')
            .replace(/^- /gm,'<span style="color:var(--primary)">▸</span> ')
            .replace(/^(\d+\.) /gm,'<span style="color:var(--primary)">$1</span> ');

        _aiHistory.push({ role: 'assistant', content: full });
    } catch(err) {
        msgBody.style.color = '#fca5a5';
        msgBody.textContent = 'Error: ' + err.message;
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i>'; }
        document.getElementById('aiMessages').scrollTop = 99999;
    }
}
</script>
</body>
</html>