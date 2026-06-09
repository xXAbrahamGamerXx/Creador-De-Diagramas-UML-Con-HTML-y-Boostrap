══════════════════════════════════════════════════════════════════
  DiagramasUML — Arquitectura MVC
  Versión: 2.0 MVC
══════════════════════════════════════════════════════════════════

ESTRUCTURA DEL PROYECTO
───────────────────────
DiagramasMVC/
├── index.php                    ← PUNTO DE ENTRADA ÚNICO (Front Controller)
├── .htaccess                    ← Redirige todo el tráfico a index.php
│
├── app/                         ← CÓDIGO PRINCIPAL (MVC)
│   ├── bootstrap.php            ← Carga clases, define constantes globales
│   ├── routes.php               ← Registro de todas las rutas URL
│   │
│   ├── core/                    ← NÚCLEO — clases base del framework
│   │   ├── Database.php         ← Conexión PDO (edita aquí las credenciales)
│   │   ├── Session.php          ← Gestión de sesiones y roles
│   │   ├── FileManager.php      ← Gestión de archivos JSON de diagramas
│   │   ├── Controller.php       ← Controlador base (render, json, redirigir)
│   │   ├── Model.php            ← Modelo base (conexión PDO)
│   │   └── Router.php           ← Enrutador HTTP
│   │
│   ├── models/                  ← MODELOS — lógica de datos y BD
│   │   ├── UserModel.php        ← Operaciones sobre tabla `usuarios`
│   │   └── DiagramModel.php     ← Operaciones sobre tabla `diagramas`
│   │
│   ├── controllers/             ← CONTROLADORES — reciben peticiones HTTP
│   │   ├── AuthController.php   ← Login, registro, logout
│   │   ├── DashboardController.php ← Panel alumno + API diagramas
│   │   ├── EditorController.php ← Editor + API guardar/cargar
│   │   ├── AlumnoController.php ← API grupos y tareas del alumno
│   │   ├── MaestroController.php ← Panel maestro + API maestro
│   │   └── AdminController.php  ← Panel admin + API administración
│   │
│   └── views/                   ← VISTAS — HTML que se muestra al usuario
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── dashboard/
│       │   └── index.php        ← Panel del alumno
│       ├── editor/
│       │   └── index.php        ← Editor de diagramas UML
│       ├── maestro/
│       │   └── index.php        ← Panel del maestro
│       └── admin/
│           └── index.php        ← Panel de administración
│
├── public/                      ← ARCHIVOS PÚBLICOS (servidos directamente)
│   ├── assets/
│   │   ├── css/style.css
│   │   ├── js/editor.js
│   │   └── img/                 ← SVGs de elementos UML
│   └── uploads/                 ← Archivos JSON de diagramas por usuario
│       ├── .htaccess            ← Bloquea acceso directo
│       ├── usuario_1/
│       ├── usuario_2/
│       └── ...
│
├── config/
│   └── database.php             ← Alias → apunta a app/core/Database.php
│
└── basededatos+info/
    ├── Base/
    │   └── diagramas_v2.sql     ← Script SQL para crear las tablas
    └── ...                      ← Documentación del proyecto


INSTALACIÓN EN XAMPP
─────────────────────
1. Copia la carpeta DiagramasMVC/ dentro de htdocs/
   Ej: C:\xampp\htdocs\DiagramasMVC\

2. Asegúrate de que mod_rewrite esté habilitado en Apache.
   En httpd.conf busca: LoadModule rewrite_module...  (quitar el #)
   Y en la sección de htdocs asegura: AllowOverride All

3. Abre phpMyAdmin y ejecuta el archivo:
   basededatos+info/Base/diagramas_v2.sql

4. Si necesitas cambiar las credenciales de la BD, edita:
   app/core/Database.php  (o usa el Panel de Administración)

5. Accede al sistema:
   http://localhost/DiagramasMVC/


RUTAS DEL SISTEMA
──────────────────
Vista             URL
──────────────    ───────────────────────────────────
Login             /login
Registro          /register
Dashboard alumno  /dashboard
Editor            /editor  o  /editor?id=N&tipo=usecase
Panel maestro     /maestro
Panel admin       /admin

API JSON          Método  URL
────────────────  ──────  ─────────────────────────────
Login             POST    /api/login
Registro          POST    /api/register
Logout            GET     /logout
Listar diagramas  GET     /api/diagramas
Guardar diagrama  POST    /api/diagramas/save
Cargar diagrama   GET     /api/diagramas/load?id=N
Eliminar diagrama POST    /api/diagramas/delete
Duplicar diagrama POST    /api/diagramas/duplicate
API Alumno        GET/POST /api/alumno?action=...
API Maestro       GET/POST /api/maestro?action=...
API Admin         GET/POST /api/admin?action=...


DIFERENCIAS RESPECTO A LA VERSIÓN SIN MVC
───────────────────────────────────────────
La funcionalidad es IDÉNTICA. Los cambios son únicamente de organización:

Sin MVC (antes)         Con MVC (ahora)              Qué hace
──────────────────      ──────────────────────────   ─────────────────────────
login.php               AuthController::loginView    Muestra el formulario
api/login.php           AuthController::login        Procesa el login (JSON)
register.php            AuthController::registerView Vista de registro
api/register.php        AuthController::register     Procesa el registro
dashboard.php           DashboardController::index   Vista del dashboard
api/get_diagrams.php    DashboardController::getDiagramas  Lista diagramas
api/delete_diagram.php  DashboardController::delete  Elimina diagrama
api/duplicate_diagram.php DashboardController::duplicate  Duplica diagrama
editor.php              EditorController::index      Vista del editor
api/save_diagram.php    EditorController::save       Guarda diagrama
api/load_diagram.php    EditorController::load       Carga diagrama
maestro.php             MaestroController::index     Vista del maestro
api/maestro_api.php     MaestroController::api       API del maestro
admin.php               AdminController::index       Vista del admin
api/admin_api.php       AdminController::api         API del admin
api/alumno_api.php      AlumnoController::api        API del alumno
includes/session.php    app/core/Session.php         Gestión de sesiones
includes/functions.php  app/models/DiagramModel.php  Lógica de diagramas
includes/FileManager.php app/core/FileManager.php    Gestión de archivos
config/database.php     app/core/Database.php        Conexión a BD


USUARIOS DE PRUEBA (password: password)
─────────────────────────────────────────
Ver basededatos+info/UsuariosPrueba.txt
