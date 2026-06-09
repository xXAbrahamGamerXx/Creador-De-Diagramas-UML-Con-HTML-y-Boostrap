<?php
/**
 * app/routes.php — Registro de rutas
 *
 * Mapea URLs a Controlador::método.
 * Se carga desde index.php después del bootstrap.
 *
 * Convención:
 *   $router->get('/ruta',  'Controlador', 'metodo')  → solo GET
 *   $router->post('/ruta', 'Controlador', 'metodo')  → solo POST
 *
 * Las rutas de API usan prefijo /api/ y responden JSON.
 * Las rutas de vista responden HTML.
 */

$router = new Router();

// ── Raíz ──────────────────────────────────────────────────────────────
$router->get('/',          'AuthController', 'index');

// ── Autenticación ─────────────────────────────────────────────────────
$router->get('/login',     'AuthController', 'loginView');
$router->get('/register',  'AuthController', 'registerView');
$router->get('/logout',    'AuthController', 'logout');

// ── API Auth (POST) ───────────────────────────────────────────────────
$router->post('/api/login',             'AuthController', 'login');
$router->post('/api/register',          'AuthController', 'register');
$router->post('/api/emergency-login',   'AuthController', 'emergencyLogin');
$router->post('/api/emergency-unlock',  'AuthController', 'emergencyUnlock');
$router->post('/api/setup-emergency',   'AuthController', 'setupEmergency');

// ── Dashboard (Alumno) ────────────────────────────────────────────────
$router->get('/dashboard', 'DashboardController', 'index');

// ── API Diagramas ─────────────────────────────────────────────────────
$router->get('/api/diagramas',              'DashboardController', 'getDiagramas');
$router->get('/api/plantillas-sistema',  'DashboardController', 'getPlantillasSistema');
$router->post('/api/diagramas/delete',      'DashboardController', 'delete');
$router->get('/api/diagramas/preview',      'DashboardController', 'previewDiagrama');
$router->post('/api/diagramas/rename',      'DashboardController', 'rename');

// ── Editor ────────────────────────────────────────────────────────────
$router->get('/editor',                     'EditorController', 'index');
$router->post('/api/diagramas/save',        'EditorController', 'save');
$router->get('/api/diagramas/load',         'EditorController', 'load');

// ── Panel Maestro ─────────────────────────────────────────────────────
$router->get('/maestro',     'MaestroController', 'index');
$router->get('/api/maestro', 'MaestroController', 'api');
$router->post('/api/maestro','MaestroController', 'api');

// ── Panel Admin ───────────────────────────────────────────────────────
$router->get('/admin',       'AdminController', 'index');
$router->get('/api/admin',   'AdminController', 'api');
$router->post('/api/admin',  'AdminController', 'api');

// API del panel de espacio/trabajo del admin (mantiene endpoint activo)
$router->get('/api/admin-dashboard',   'AdminController', 'dashboardApi');
$router->post('/api/admin-dashboard',  'AdminController', 'dashboardApi');

// ── API Alumno ────────────────────────────────────────────────────────
$router->get('/api/alumno',  'AlumnoController', 'api');
$router->post('/api/alumno', 'AlumnoController', 'api');

// ── Configuración de usuario (tema / color) ───────────────────────────
$router->get('/api/user-config',  'DashboardController', 'getUserConfig');
$router->post('/api/user-config', 'DashboardController', 'saveUserConfig');

// ── Proyectos colaborativos ───────────────────────────────────────
$router->get('/proyectos',    'ProyectoController', 'index');
$router->get('/api/proyectos',  'ProyectoController', 'api');
$router->post('/api/proyectos', 'ProyectoController', 'api');

// ── Chat IA ──────────────────────────────────────────────────────────
$router->post('/api/chat',          'ChatController', 'proxy');
$router->get('/api/chat-status',    'ChatController', 'status');

// ── Archivos de proyecto ─────────────────────────────────────────────
$router->post('/api/proyectos/upload',    'ProyectoController', 'uploadFile');
$router->get('/api/proyectos/download',   'ProyectoController', 'downloadFile');
$router->get('/api/proyectos/view',       'ProyectoController', 'viewFile');
$router->post('/api/proyectos/del-file',  'ProyectoController', 'deleteFile');

// ── Observaciones ─────────────────────────────────────────────────────
$router->get('/api/observaciones',           'ProyectoController', 'getObservaciones');
$router->post('/api/observaciones',          'ProyectoController', 'saveObservacion');
$router->post('/api/observaciones/del',      'ProyectoController', 'deleteObservacion');
$router->post('/api/observaciones/reply',    'ProyectoController', 'replyObservacion');
$router->get('/api/observaciones/thread',    'ProyectoController', 'getThread');
$router->get('/api/proyectos/buscar-usuarios','ProyectoController', 'buscarUsuarios');
$router->post('/api/proyectos/invitar',       'ProyectoController', 'invitarUsuario');

// ── Permisos de miembros de proyecto (solo owner) ─────────────────────
$router->post('/api/proyectos/permisos',  'ProyectoController', 'updatePermisos');

// ── Tareas de proyecto ────────────────────────────────────────────────
$router->get('/api/tareas-proyecto',        'ProyectoController', 'getTareas');
$router->get('/api/tareas-proyecto/entregas', 'ProyectoController', 'getEntregasTarea');
$router->post('/api/tareas-proyecto',       'ProyectoController', 'saveTarea');
$router->post('/api/tareas-proyecto/del',   'ProyectoController', 'deleteTarea');
$router->post('/api/tareas-proyecto/entregar', 'ProyectoController', 'entregarTarea');
$router->post('/api/tareas-proyecto/calificar', 'ProyectoController', 'calificarEntregaTarea');

// ── Calificaciones ────────────────────────────────────────────────────
$router->post('/api/calificaciones',        'ProyectoController', 'saveCalificacion');
$router->get('/api/calificaciones',         'ProyectoController', 'getCalificaciones');

// ── Notificaciones ────────────────────────────────────────────────────
$router->get('/api/notificaciones',         'NotificacionController', 'getAll');
$router->post('/api/notificaciones/leer',   'NotificacionController', 'marcarLeida');
$router->post('/api/notificaciones/leer-todas', 'NotificacionController', 'marcarTodasLeidas');

// ── Bitácora de actividad ─────────────────────────────────────────────
$router->get('/api/bitacora',               'ProyectoController', 'getBitacora');

// ── Búsqueda global ───────────────────────────────────────────────────
$router->get('/api/busqueda',               'DashboardController', 'busquedaGlobal');

// ── Admin: Config global, SMTP, Auditoría, Backup ────────────────────
$router->get('/api/admin/config',           'AdminController', 'getConfig');
$router->post('/api/admin/config',          'AdminController', 'saveConfig');
$router->post('/api/admin/test-smtp',       'AdminController', 'testSmtp');
$router->get('/api/admin/auditoria',        'AdminController', 'getAuditoria');
$router->post('/api/admin/backup',          'AdminController', 'backupDB');
$router->post('/api/admin/modo-mantenimiento', 'AdminController', 'toggleMantenimiento');

// ── Duplicar diagrama ─────────────────────────────────────────────────
$router->post('/api/diagramas/duplicate',   'DashboardController', 'duplicate');

// ── Editor API (vista pública del editor con selección de diagramas) ──
$router->get('/editor-api', 'EditorController', 'apiView');

return $router;
?>
