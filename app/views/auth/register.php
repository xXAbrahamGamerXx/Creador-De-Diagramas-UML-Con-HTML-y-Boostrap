<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Cuenta — DiagramasUML</title>
<link href="<?= Assets::bootstrapCss() ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= Assets::bootstrapIcons() ?>">
<style>
* { box-sizing:border-box; }
body { background:linear-gradient(135deg,#0f0c29,#302b63,#24243e); min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; padding:20px; }
.card { background:rgba(255,255,255,.05); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,.1); border-radius:20px; padding:40px; width:100%; max-width:480px; box-shadow:0 25px 60px rgba(0,0,0,.5); }
.brand { text-align:center; margin-bottom:28px; }
.brand i { font-size:2.5rem; color:#667eea; margin-bottom:10px; display:block; }
.brand h2 { color:#fff; font-weight:700; margin:0; font-size:1.4rem; }
.brand p { color:rgba(255,255,255,.5); font-size:.82rem; margin:4px 0 0; }
.form-control { background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15); border-radius:10px; color:#fff; padding:11px 14px; font-size:.88rem; transition:all .2s; }
.form-control::placeholder { color:rgba(255,255,255,.3); }
.form-control:focus { background:rgba(255,255,255,.1); border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.2); color:#fff; outline:none; }
.form-label { color:rgba(255,255,255,.65); font-size:.8rem; font-weight:500; margin-bottom:5px; }
.mb-3 { margin-bottom:14px !important; }

/* Selector de rol */
.rol-selector { display:flex; gap:10px; margin-bottom:16px; }
.rol-btn { flex:1; padding:12px; border-radius:10px; border:2px solid rgba(255,255,255,.15); background:rgba(255,255,255,.05); color:rgba(255,255,255,.6); cursor:pointer; text-align:center; transition:all .2s; font-size:.85rem; }
.rol-btn:hover { border-color:rgba(102,126,234,.5); color:#fff; }
.rol-btn.active { border-color:#667eea; background:rgba(102,126,234,.2); color:#fff; }
.rol-btn i { display:block; font-size:1.5rem; margin-bottom:4px; }

/* Código maestro */
#codigoSection { overflow:hidden; max-height:0; transition:max-height .3s ease; }
#codigoSection.show { max-height:80px; }

.btn-register { background:linear-gradient(135deg,#667eea,#764ba2); border:none; border-radius:10px; color:#fff; font-weight:600; padding:12px; font-size:.92rem; width:100%; transition:all .2s; margin-top:6px; }
.btn-register:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(102,126,234,.4); }
.btn-register:disabled { opacity:.6; transform:none; }

.strength-bar { height:4px; border-radius:2px; background:#333; overflow:hidden; margin-top:6px; }
.strength-fill { height:100%; width:0; border-radius:2px; transition:all .3s; }

.link-area { text-align:center; margin-top:18px; color:rgba(255,255,255,.5); font-size:.83rem; }
.link-area a { color:#667eea; text-decoration:none; font-weight:600; }
.alert-custom { border-radius:10px; padding:11px 14px; font-size:.83rem; margin-bottom:16px; }
.alert-danger-custom  { background:rgba(220,53,69,.15); border:1px solid rgba(220,53,69,.3); color:#ff8a8a; }
.alert-success-custom { background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.3); color:#6ee7b7; }
.spinner-sm { width:14px; height:14px; border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:spin .6s linear infinite; display:inline-block; margin-right:6px; }
@keyframes spin { to{transform:rotate(360deg)} }
</style>
</head>
<body>
<div class="card">
    <div class="brand">
        <i class="bi bi-person-plus-fill"></i>
        <h2>Crear Cuenta</h2>
        <p>¿Eres alumno o maestro?</p>
    </div>

    <!-- Selector de rol -->
    <div class="rol-selector">
        <div class="rol-btn active" id="btnAlumno" onclick="seleccionarRol('alumno')">
            <i class="bi bi-person-fill"></i>
            Alumno
        </div>
        <div class="rol-btn" id="btnMaestro" onclick="seleccionarRol('maestro')">
            <i class="bi bi-person-badge-fill"></i>
            Maestro
        </div>
    </div>

    <div id="alertMsg" class="alert-custom d-none"></div>

    <form id="registerForm">
        <input type="hidden" id="rolSeleccionado" value="alumno">

        <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="nombre" placeholder="Tu nombre completo" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre de usuario</label>
            <input type="text" class="form-control" id="username" placeholder="sin espacios" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" placeholder="correo@ejemplo.com" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" placeholder="Mínimo 6 caracteres" required oninput="actualizarFortaleza(this.value)">
            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" class="form-control" id="confirmPassword" placeholder="Repite la contraseña" required>
        </div>

        <!-- Solo visible para maestros -->
        <div id="codigoSection" class="mb-3">
            <label class="form-label">Código de maestro <span style="color:#667eea">*</span></label>
            <input type="text" class="form-control" id="codigoMaestro" placeholder="Código proporcionado por el administrador">
            <small style="color:rgba(255,255,255,.35);font-size:.75rem">Solicita el código al administrador del sistema</small>
        </div>

        <button type="submit" class="btn-register" id="btnRegister">
            Crear Cuenta
        </button>
    </form>

    <div class="link-area">
        ¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/login">Inicia sesión</a>
    </div>
</div>

<script>
let rolActual = 'alumno';

function seleccionarRol(rol) {
    rolActual = rol;
    document.getElementById('rolSeleccionado').value = rol;
    document.getElementById('btnAlumno').classList.toggle('active', rol === 'alumno');
    document.getElementById('btnMaestro').classList.toggle('active', rol === 'maestro');
    const codigoSec = document.getElementById('codigoSection');
    if (rol === 'maestro') codigoSec.classList.add('show');
    else codigoSec.classList.remove('show');
}

function actualizarFortaleza(val) {
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;
    const colors = ['#dc3545','#fd7e14','#ffc107','#20c997','#198754'];
    const fill = document.getElementById('strengthFill');
    fill.style.width = (score/5*100) + '%';
    fill.style.background = colors[score-1] || '#dc3545';
}

document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn      = document.getElementById('btnRegister');
    const alertEl  = document.getElementById('alertMsg');
    const password = document.getElementById('password').value;
    const confirm  = document.getElementById('confirmPassword').value;

    alertEl.classList.add('d-none');

    if (password !== confirm) {
        alertEl.className = 'alert-custom alert-danger-custom';
        alertEl.textContent = 'Las contraseñas no coinciden';
        alertEl.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-sm"></span>Creando cuenta...';

    const body = {
        nombre:          document.getElementById('nombre').value.trim(),
        username:        document.getElementById('username').value.trim(),
        email:           document.getElementById('email').value.trim(),
        password:        password,
        rol:             rolActual,
        codigo_maestro:  document.getElementById('codigoMaestro').value.trim()
    };

    try {
        const res  = await fetch('<?= BASE_URL ?>/api/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();

        if (data.success) {
            alertEl.className = 'alert-custom alert-success-custom';
            alertEl.textContent = '✓ Cuenta creada. Redirigiendo...';
            alertEl.classList.remove('d-none');
            setTimeout(() => window.location.href = '<?= BASE_URL ?>/login', 1500);
        } else {
            alertEl.className = 'alert-custom alert-danger-custom';
            alertEl.textContent = data.error || 'Error al crear la cuenta';
            alertEl.classList.remove('d-none');
            btn.disabled = false;
            btn.innerHTML = 'Crear Cuenta';
        }
    } catch (err) {
        alertEl.className = 'alert-custom alert-danger-custom';
        alertEl.textContent = 'Error de conexión con el servidor';
        alertEl.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = 'Crear Cuenta';
    }
});
</script>
</body>
</html>
