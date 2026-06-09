<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión — DiagramasUML</title>
<link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
<style>
* { box-sizing:border-box; }
body { background:linear-gradient(135deg,#0f0c29,#302b63,#24243e); min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; padding:20px; }
.card { background:rgba(255,255,255,.05); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,.1); border-radius:20px; padding:40px; width:100%; max-width:440px; box-shadow:0 25px 60px rgba(0,0,0,.5); }
.brand { text-align:center; margin-bottom:32px; }
.brand i { font-size:3rem; color:#667eea; margin-bottom:12px; display:block; }
.brand h2 { color:#fff; font-weight:700; margin:0; font-size:1.5rem; }
.brand p { color:rgba(255,255,255,.5); font-size:.85rem; margin:4px 0 0; }
.form-control { background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15); border-radius:10px; color:#fff; padding:12px 16px; width:100%; font-size:.9rem; transition:all .2s; }
.form-control::placeholder { color:rgba(255,255,255,.35); }
.form-control:focus { background:rgba(255,255,255,.1); border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.2); color:#fff; outline:none; }
.form-label { color:rgba(255,255,255,.7); font-size:.82rem; font-weight:500; margin-bottom:6px; display:block; }
.btn-login { background:linear-gradient(135deg,#667eea,#764ba2); border:none; border-radius:10px; color:#fff; font-weight:600; padding:13px; font-size:.95rem; width:100%; transition:all .2s; cursor:pointer; }
.btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(102,126,234,.4); }
.btn-login:disabled { opacity:.6; transform:none; cursor:not-allowed; }
.btn-emergency { background:linear-gradient(135deg,#dc3545,#9b1a26); border:none; border-radius:10px; color:#fff; font-weight:600; padding:12px; font-size:.9rem; width:100%; transition:all .2s; cursor:pointer; }
.btn-emergency:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(220,53,69,.4); }
.btn-emergency:disabled { opacity:.6; transform:none; cursor:not-allowed; }
.link-area { text-align:center; margin-top:20px; color:rgba(255,255,255,.5); font-size:.85rem; }
.link-area a { color:#667eea; text-decoration:none; font-weight:600; }
.link-area a:hover { text-decoration:underline; }
.alert-custom { border-radius:10px; padding:12px 16px; font-size:.85rem; margin-bottom:16px; }
.alert-danger-custom { background:rgba(220,53,69,.15); border:1px solid rgba(220,53,69,.3); color:#ff8a8a; }
.alert-warn-custom  { background:rgba(245,158,11,.12); border:1px solid rgba(245,158,11,.35); color:#fcd34d; }
.alert-ok-custom    { background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.35); color:#6ee7b7; }
.divider { display:flex; align-items:center; gap:12px; margin:20px 0; }
.divider::before,.divider::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.1); }
.divider span { color:rgba(255,255,255,.3); font-size:.75rem; white-space:nowrap; }
.spinner-sm { width:16px; height:16px; border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:spin .6s linear infinite; display:inline-block; margin-right:6px; vertical-align:middle; }
@keyframes spin { to{transform:rotate(360deg)} }

/* Panel de emergencia */
.emergency-panel { border:1px solid rgba(220,53,69,.35); border-radius:14px; background:rgba(220,53,69,.06); overflow:hidden; }
.emergency-header { background:rgba(220,53,69,.18); padding:12px 16px; display:flex; align-items:center; gap:10px; cursor:pointer; user-select:none; }
.emergency-header i { font-size:1.1rem; color:#ff8a8a; }
.emergency-header span { color:#ff8a8a; font-size:.85rem; font-weight:600; flex:1; }
.emergency-header .toggle-icon { color:rgba(255,255,255,.4); font-size:.8rem; transition:transform .2s; }
.emergency-body { padding:16px; display:none; }
.emergency-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.3); border-radius:20px; padding:4px 12px; font-size:.72rem; color:#fcd34d; margin-bottom:14px; }
.db-error-banner { background:rgba(220,53,69,.12); border:1px solid rgba(220,53,69,.3); border-radius:10px; padding:10px 14px; font-size:.8rem; color:#ff8a8a; margin-bottom:16px; display:flex; align-items:flex-start; gap:8px; }
.db-error-banner i { margin-top:1px; flex-shrink:0; }
</style>
</head>
<body>
<div class="card">
    <div class="brand">
        <i class="bi bi-diagram-3-fill"></i>
        <h2>DiagramasUML</h2>
        <p>Inicia sesión para continuar</p>
    </div>

    <div id="alertMsg" class="alert-custom alert-danger-custom" style="display:none"></div>

    <!-- ── Formulario normal ─────────────────────────────── -->
    <form id="loginForm" autocomplete="on">
        <div class="mb-3">
            <label class="form-label">Usuario o Email</label>
            <input type="text" class="form-control" id="username" placeholder="tu_usuario" required autocomplete="username">
        </div>
        <div class="mb-4">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" placeholder="••••••••" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn-login" id="btnLogin">Iniciar Sesión</button>
    </form>

    <div class="divider"><span>¿no tienes cuenta?</span></div>
    <div class="link-area">
        <a href="<?= BASE_URL ?>/register">Crear cuenta nueva</a>
    </div>

    <!-- ── Acceso de emergencia oculto ────────────────────── -->
    <!-- Enlace sutil, solo visible si sabes que existe -->
    <div style="text-align:center;margin-top:28px">
        <button id="btnShowEmergencyUnlock" onclick="mostrarDesbloqueEmergencia()"
            style="background:none;border:none;color:rgba(255,255,255,.12);font-size:.68rem;cursor:pointer;letter-spacing:.05em;transition:color .3s"
            onmouseover="this.style.color='rgba(255,100,100,.4)'" onmouseout="this.style.color='rgba(255,255,255,.12)'">
            ⬡ acceso técnico
        </button>
    </div>

    <!-- Panel de desbloqueo (paso 1: contraseña para ver el panel real) -->
    <div id="emergencyUnlockPanel" style="display:none;margin-top:16px">
        <div style="border:1px solid rgba(220,53,69,.3);border-radius:12px;background:rgba(220,53,69,.05);overflow:hidden">
            <div style="background:rgba(220,53,69,.15);padding:10px 16px;display:flex;align-items:center;gap:8px">
                <i class="bi bi-shield-lock" style="color:#ff8a8a;font-size:1rem"></i>
                <span style="color:#ff8a8a;font-size:.82rem;font-weight:600">Verificación de seguridad</span>
            </div>
            <div style="padding:14px 16px">
                <p style="color:rgba(255,200,200,.6);font-size:.75rem;margin-bottom:12px">
                    Introduce la contraseña de acceso técnico para continuar.
                </p>
                <div id="alertUnlock" class="alert-custom alert-danger-custom" style="display:none;margin-bottom:10px;font-size:.78rem"></div>
                <form id="unlockForm" autocomplete="off">
                    <input type="password" class="form-control" id="unlockPassword"
                        placeholder="Contraseña de acceso técnico"
                        style="margin-bottom:10px" autocomplete="new-password">
                    <button type="submit" class="btn-emergency" style="font-size:.82rem;padding:9px">
                        <i class="bi bi-unlock-fill me-2"></i>Verificar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel real de emergencia (paso 2, solo visible tras verificación) -->
    <div id="emergencyPanel" style="display:none;margin-top:12px">
        <div style="border:1px solid rgba(220,53,69,.4);border-radius:12px;background:rgba(220,53,69,.07);overflow:hidden">
            <div style="background:rgba(220,53,69,.2);padding:10px 16px;display:flex;align-items:center;gap:8px">
                <i class="bi bi-shield-exclamation" style="color:#ff6b6b;font-size:1.1rem"></i>
                <span style="color:#ff8a8a;font-size:.85rem;font-weight:700">Acceso de Emergencia — Sin BD</span>
            </div>
            <div style="padding:14px 16px">
                <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);border-radius:20px;padding:3px 10px;font-size:.7rem;color:#fcd34d;margin-bottom:12px">
                    <i class="bi bi-exclamation-triangle-fill"></i> Solo Superadmin · Sesión 30 min
                </div>
                <div id="dbErrorBanner" style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.25);border-radius:8px;padding:9px 12px;font-size:.78rem;color:#ff8a8a;margin-bottom:12px;display:none">
                    <i class="bi bi-database-x me-1"></i><strong>BD no disponible:</strong>
                    <span id="dbErrorMsg"></span>
                </div>
                <div id="alertEmergency" class="alert-custom alert-danger-custom" style="display:none;margin-bottom:10px;font-size:.78rem"></div>
                <form id="emergencyForm" autocomplete="off">
                    <div class="mb-2">
                        <input type="text" class="form-control" id="eUsername" placeholder="Usuario de emergencia" autocomplete="off" style="font-size:.85rem">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="ePassword" placeholder="Contraseña de emergencia" autocomplete="new-password" style="font-size:.85rem">
                    </div>
                    <button type="submit" class="btn-emergency" id="btnEmergency" style="font-size:.85rem;padding:10px">
                        <i class="bi bi-shield-lock-fill me-2"></i>Entrar en modo emergencia
                    </button>
                </form>
                <p style="color:rgba(255,150,150,.4);font-size:.7rem;margin-top:8px;line-height:1.5">
                    La sesión expira en 30 min y solo permite reparar la configuración de BD.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// ── Login normal ───────────────────────────────────────────────────
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnLogin');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-sm"></span>Verificando...';
    hideAlert('alertMsg');

    try {
        const res  = await fetch(BASE_URL + '/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value
            })
        });
        const data = await res.json();

        if (data.success) {
            sessionStorage.removeItem('maestro_nav_state');
            sessionStorage.removeItem('dash_nav_state');
            btn.innerHTML = '✓ Redirigiendo...';
            window.location.href = data.redirect || BASE_URL + '/dashboard';
        } else {
            // Detectar si el error es por BD caída
            if (data.error && (data.error.includes('BD') || data.error.includes('base de datos') ||
                data.error.includes('SQLSTATE') || data.error.includes('Connection') ||
                data.error.includes('servidor'))) {
                mostrarPanelEmergencia(data.error);
            }
            showAlert('alertMsg', data.error || 'Usuario o contraseña incorrectos', 'danger');
            btn.disabled = false;
            btn.innerHTML = 'Iniciar Sesión';
        }
    } catch (err) {
        // Si fetch falla totalmente (servidor caído), sugerir emergencia
        mostrarPanelEmergencia('No se pudo conectar con el servidor. ¿Está el servidor activo?');
        showAlert('alertMsg', 'Error de conexión con el servidor', 'danger');
        btn.disabled = false;
        btn.innerHTML = 'Iniciar Sesión';
    }
});

// ── Login de emergencia ────────────────────────────────────────────
document.getElementById('emergencyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnEmergency');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-sm"></span>Verificando...';
    hideAlert('alertEmergency');

    try {
        const res  = await fetch(BASE_URL + '/api/emergency-login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: document.getElementById('eUsername').value.trim(),
                password: document.getElementById('ePassword').value
            })
        });
        const data = await res.json();

        if (data.success) {
            btn.innerHTML = '✓ Acceso concedido...';
            showAlert('alertEmergency', '✓ ' + (data.message || 'Sesión de emergencia iniciada.'), 'ok');
            setTimeout(() => { window.location.href = data.redirect || BASE_URL + '/admin'; }, 1200);
        } else {
            showAlert('alertEmergency', data.error || 'Credenciales incorrectas', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i>Entrar en modo emergencia';
        }
    } catch (err) {
        showAlert('alertEmergency', 'Error de conexión', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i>Entrar en modo emergencia';
    }
});

// ── Helpers UI — emergencia ────────────────────────────────────────
// La contraseña de desbloqueo es la misma de emergencia (primer factor).
// Esto evita que cualquiera sepa que el panel existe.
const UNLOCK_KEY = 'emrg_unlocked'; // sessionStorage flag

function mostrarDesbloqueEmergencia() {
    // Ocultar el botón sutil
    document.getElementById('btnShowEmergencyUnlock').style.display = 'none';
    document.getElementById('emergencyUnlockPanel').style.display = 'block';
    setTimeout(() => document.getElementById('unlockPassword').focus(), 50);
}

// Paso 1: verificar contraseña de desbloqueo en el servidor
document.getElementById('unlockForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const pass = document.getElementById('unlockPassword').value;
    const btn  = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-sm"></span>Verificando...';
    hideAlert('alertUnlock');

    try {
        const res  = await fetch(BASE_URL + '/api/emergency-unlock', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ unlock_password: pass })
        });
        const data = await res.json();
        if (data.success) {
            // Mostrar panel real
            document.getElementById('emergencyUnlockPanel').style.display = 'none';
            document.getElementById('emergencyPanel').style.display = 'block';
        } else {
            showAlert('alertUnlock', data.error || 'Contraseña incorrecta', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-unlock-fill me-2"></i>Verificar';
        }
    } catch (err) {
        showAlert('alertUnlock', 'Error de conexión', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-unlock-fill me-2"></i>Verificar';
    }
});

// Mostrar error de BD en el banner (llamado internamente si BD falla)
function mostrarPanelEmergencia(dbError) {
    if (dbError) {
        const banner = document.getElementById('dbErrorBanner');
        if (banner) { banner.style.display = 'block'; document.getElementById('dbErrorMsg').textContent = dbError; }
    }
}

function showAlert(id, msg, type) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = msg;
    el.className = 'alert-custom alert-' + (type === 'ok' ? 'ok' : type === 'warn' ? 'warn' : 'danger') + '-custom';
    el.style.display = 'block';
}
function hideAlert(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}
</script>
</body>
</html>
