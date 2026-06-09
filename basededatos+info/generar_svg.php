<?php
// generar_svg.php - Ejecuta este archivo UNA SOLA VEZ para crear todos los SVG

// Crear todas las carpetas necesarias
$carpetas = [
    'img/DiagramadeClases',
    'img/DiagramadeCasosdeUso',
    'img/DiagramasdeInteracción',
    'img/DiagramadeActividades',
    'img/DiagramadeEstados',
    'img/DiagramadeComponentes',
    'img/DiagramadeDespliegue',
    'img/DiagramadeObjetos',
    'img/DiagramadeComunicación',
    'img/DiagramadeTiempo'
];

foreach ($carpetas as $carpeta) {
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
        echo "Creada carpeta: $carpeta<br>";
    }
}

// ===== DIAGRAMA DE CLASES =====
$clases = [
    'clase.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><line x1="4" y1="10" x2="20" y2="10"/><line x1="4" y1="14" x2="20" y2="14"/></svg>',
    
    'clase-abstracta.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1" stroke-dasharray="2 2"/><line x1="4" y1="10" x2="20" y2="10"/><line x1="4" y1="14" x2="20" y2="14"/></svg>',
    
    'interfaz.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="6"/><text x="12" y="16" text-anchor="middle" font-size="8" fill="currentColor">«I»</text></svg>',
    
    'enumeracion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><line x1="4" y1="10" x2="20" y2="10"/><text x="8" y="15" font-size="5" fill="currentColor">«enum»</text></svg>',
    
    'asociacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/></svg>',
    
    'asociacion-unidireccional.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12"/><polygon points="18,12 14,9 14,15" fill="currentColor"/></svg>',
    
    'asociacion-bidireccional.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="12" x2="18" y2="12"/><polygon points="4,12 8,9 8,15" fill="currentColor"/><polygon points="20,12 16,9 16,15" fill="currentColor"/></svg>',
    
    'autoasociacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 8 L18 8 L18 14 L12 14" fill="none"/><polygon points="18,14 15,12 15,16" fill="currentColor"/></svg>',
    
    'herencia.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12"/><polygon points="18,12 14,8 14,16" fill="none" stroke="currentColor"/></svg>',
    
    'realizacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12" stroke-dasharray="3 2"/><polygon points="18,12 14,8 14,16" fill="none" stroke="currentColor"/></svg>',
    
    'dependencia.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12" stroke-dasharray="3 2"/><polygon points="18,12 14,9 14,15" fill="currentColor"/></svg>',
    
    'agregacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="12" x2="20" y2="12"/><polygon points="4,12 8,8 8,16" fill="none" stroke="currentColor"/></svg>',
    
    'composicion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="12" x2="20" y2="12"/><polygon points="4,12 8,8 8,16" fill="currentColor"/></svg>'
];

foreach ($clases as $archivo => $contenido) {
    file_put_contents("img/DiagramadeClases/$archivo", $contenido);
    echo "Creado: img/DiagramadeClases/$archivo<br>";
}

// ===== DIAGRAMA DE CASOS DE USO =====
$usecase = [
    'actor.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M4 21v-4c0-3 4-5 8-5s8 2 8 5v4"/></svg>',
    
    'caso-uso.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="8" ry="5"/></svg>',
    
    'sistema.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><text x="12" y="16" text-anchor="middle" font-size="8" fill="currentColor">Sistema</text></svg>',
    
    'asociacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/></svg>',
    
    'include.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12" stroke-dasharray="3 2"/><polygon points="18,12 14,9 14,15" fill="currentColor"/><text x="10" y="6" font-size="5" fill="currentColor">«include»</text></svg>',
    
    'extend.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12" stroke-dasharray="3 2"/><polygon points="18,12 14,9 14,15" fill="currentColor"/><text x="10" y="6" font-size="5" fill="currentColor">«extend»</text></svg>',
    
    'generalizacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12"/><polygon points="18,12 14,8 14,16" fill="none" stroke="currentColor"/></svg>'
];

foreach ($usecase as $archivo => $contenido) {
    file_put_contents("img/DiagramadeCasosdeUso/$archivo", $contenido);
    echo "Creado: img/DiagramadeCasosdeUso/$archivo<br>";
}

// ===== DIAGRAMA DE SECUENCIA =====
$secuencia = [
    'actor.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M4 21v-4c0-3 4-5 8-5s8 2 8 5v4"/></svg>',
    
    'objeto.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="8" rx="1"/><line x1="12" y1="10" x2="12" y2="22" stroke-dasharray="2 2"/></svg>',
    
    'activacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="10" y="4" width="4" height="16" fill="currentColor" fill-opacity="0.3"/></svg>',
    
    'mensaje-sincrono.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12"/><polygon points="18,12 14,9 14,15" fill="currentColor"/></svg>',
    
    'mensaje-asincrono.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12"/><polygon points="18,12 14,9 14,15" fill="none" stroke="currentColor"/></svg>',
    
    'mensaje-retorno.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="12" x2="18" y2="12" stroke-dasharray="3 2"/><polygon points="4,12 8,9 8,15" fill="currentColor"/></svg>',
    
    'destruccion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="8" x2="16" y2="16"/><line x1="16" y1="8" x2="8" y2="16"/></svg>'
];

foreach ($secuencia as $archivo => $contenido) {
    file_put_contents("img/DiagramasdeInteracción/$archivo", $contenido);
    echo "Creado: img/DiagramasdeInteracción/$archivo<br>";
}

// ===== DIAGRAMA DE ACTIVIDADES =====
$actividades = [
    'inicio.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5" fill="currentColor"/></svg>',
    
    'actividad.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="3"/></svg>',
    
    'decision.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12,2 22,12 12,22 2,12" fill="none"/></svg>',
    
    'bifurcacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="8" y="4" width="8" height="16" fill="currentColor"/></svg>',
    
    'union.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="8" y="4" width="8" height="16" fill="currentColor"/></svg>',
    
    'fin.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>',
    
    'fin-flujo.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="6"/><line x1="8" y1="8" x2="16" y2="16"/><line x1="16" y1="8" x2="8" y2="16"/></svg>',
    
    'flujo.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><polygon points="20,12 16,9 16,15" fill="currentColor"/></svg>'
];

foreach ($actividades as $archivo => $contenido) {
    file_put_contents("img/DiagramadeActividades/$archivo", $contenido);
    echo "Creado: img/DiagramadeActividades/$archivo<br>";
}

// ===== DIAGRAMA DE ESTADOS =====
$estados = [
    'estado-inicial.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5" fill="currentColor"/></svg>',
    
    'estado.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="3"/></svg>',
    
    'estado-final.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>',
    
    'decision.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12,2 22,12 12,22 2,12" fill="none"/></svg>',
    
    'historia.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/><text x="12" y="16" text-anchor="middle" font-size="10" fill="currentColor">H</text></svg>',
    
    'transicion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><polygon points="20,12 16,9 16,15" fill="currentColor"/></svg>'
];

foreach ($estados as $archivo => $contenido) {
    file_put_contents("img/DiagramadeEstados/$archivo", $contenido);
    echo "Creado: img/DiagramadeEstados/$archivo<br>";
}

// ===== DIAGRAMA DE COMPONENTES =====
$componentes = [
    'componente.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><rect x="7" y="7" width="3" height="3" fill="currentColor"/><rect x="14" y="7" width="3" height="3" fill="currentColor"/></svg>',
    
    'interfaz.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><text x="12" y="16" text-anchor="middle" font-size="8" fill="currentColor">I</text></svg>',
    
    'interfaz-requerida.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8 L20 12 L16 16" fill="none"/><circle cx="8" cy="12" r="4" fill="none"/></svg>',
    
    'puerto.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="10" y="10" width="4" height="4" fill="currentColor"/></svg>',
    
    'dependencia.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12" stroke-dasharray="3 2"/><polygon points="18,12 14,9 14,15" fill="currentColor"/></svg>'
];

foreach ($componentes as $archivo => $contenido) {
    file_put_contents("img/DiagramadeComponentes/$archivo", $contenido);
    echo "Creado: img/DiagramadeComponentes/$archivo<br>";
}

// ===== DIAGRAMA DE DESPLIEGUE =====
$despliegue = [
    'nodo.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="8" width="16" height="12" rx="1"/><line x1="4" y1="12" x2="20" y2="12"/></svg>',
    
    'dispositivo.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><line x1="4" y1="8" x2="20" y2="8"/><circle cx="12" cy="18" r="2"/></svg>',
    
    'artefacto.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><line x1="12" y1="4" x2="12" y2="20"/></svg>',
    
    'interfaz.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/></svg>',
    
    'asociacion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/></svg>'
];

foreach ($despliegue as $archivo => $contenido) {
    file_put_contents("img/DiagramadeDespliegue/$archivo", $contenido);
    echo "Creado: img/DiagramadeDespliegue/$archivo<br>";
}

// ===== DIAGRAMA DE OBJETOS =====
$objetos = [
    'objeto.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><line x1="4" y1="10" x2="20" y2="10"/><text x="8" y="15" font-size="5" fill="currentColor">:Clase</text></svg>',
    
    'enlace.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/></svg>',
    
    'valor.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><text x="12" y="16" text-anchor="middle" font-size="8" fill="currentColor">valor</text></svg>'
];

foreach ($objetos as $archivo => $contenido) {
    file_put_contents("img/DiagramadeObjetos/$archivo", $contenido);
    echo "Creado: img/DiagramadeObjetos/$archivo<br>";
}

// ===== DIAGRAMA DE COMUNICACIÓN =====
$comunicacion = [
    'objeto.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><line x1="4" y1="10" x2="20" y2="10"/><text x="8" y="15" font-size="5" fill="currentColor">:Clase</text></svg>',
    
    'enlace.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/></svg>',
    
    'mensaje.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="16" y2="12"/><polygon points="18,12 14,9 14,15" fill="currentColor"/><text x="8" y="6" font-size="5" fill="currentColor">1:</text></svg>'
];

foreach ($comunicacion as $archivo => $contenido) {
    file_put_contents("img/DiagramadeComunicación/$archivo", $contenido);
    echo "Creado: img/DiagramadeComunicación/$archivo<br>";
}

// ===== DIAGRAMA DE TIEMPO =====
$tiempo = [
    'linea-vida.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="4" x2="20" y2="4"/><line x1="4" y1="8" x2="20" y2="8"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="16" x2="20" y2="16"/><line x1="4" y1="20" x2="20" y2="20"/></svg>',
    
    'estado.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="6" width="16" height="12" rx="2"/><text x="12" y="15" text-anchor="middle" font-size="6" fill="currentColor">Estado</text></svg>',
    
    'evento.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>',
    
    'restriccion.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8 L18 8 L18 16 L6 16 Z" stroke-dasharray="2 2"/><text x="12" y="14" text-anchor="middle" font-size="5" fill="currentColor">{t}</text></svg>',
    
    'linea-tiempo.svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><circle cx="8" cy="12" r="2" fill="currentColor"/><circle cx="16" cy="12" r="2" fill="currentColor"/></svg>'
];

foreach ($tiempo as $archivo => $contenido) {
    file_put_contents("img/DiagramadeTiempo/$archivo", $contenido);
    echo "Creado: img/DiagramadeTiempo/$archivo<br>";
}

echo "<br><br>✅ <strong>Todos los SVG han sido creados exitosamente!</strong>";
echo "<br><br>Total de imágenes generadas: 63";
echo "<br><br><a href='editor.php'>Ir al Editor</a>";
?>