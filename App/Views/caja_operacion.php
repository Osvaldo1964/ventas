<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apertura / Cierre - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>

<div class="top-navbar">
    <div>
        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-cash-register me-2"></i> Apertura / Cierre de Caja</h4>
        <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size:0.9em;">Inicia o finaliza tu turno de trabajo</span>
    </div>
    <div class="d-flex align-items-center">
        <i class="far fa-bell text-muted me-4 fs-5" style="cursor: pointer;"></i>
        <div class="text-end me-3">
            <div class="fw-bold text-primary" id="userName"></div>
            <div class="text-muted" style="font-size:0.8em" id="userRole"></div>
        </div>
        <a href="#" class="text-danger bg-light py-2 px-3 rounded" style="font-size:0.8em;font-weight:600" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</div>

<div class="breadcrumb-nav">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/ventas/public/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/ventas/public/cajas">Caja</a></li>
            <li class="breadcrumb-item active">Apertura / Cierre</li>
        </ol>
    </nav>
</div>

<div class="container pb-5">

    <!-- Estado de sesión actual -->
    <div id="sesionBanner" class="alert d-flex align-items-center gap-3 mb-4 d-none" style="border-radius:12px;border:none">
        <div id="sesionBannerIcon" class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:48px;height:48px;font-size:1.3rem;flex-shrink:0"></div>
        <div class="flex-grow-1">
            <div class="fw-bold" id="sesionBannerTitle"></div>
            <div class="small text-muted" id="sesionBannerSub"></div>
        </div>
    </div>

    <div class="row g-4 justify-content-center">

        <!-- Columna Central: Abrir/Cerrar sesión -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:14px">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-door-open me-2"></i>Operación de Turno</h6>
                </div>
                <div class="card-body px-4 py-4">

                    <!-- Formulario Abrir -->
                    <div id="formAbrir">
                        <p class="text-muted small mb-4">Selecciona la caja asignada e ingresa la base en efectivo para abrir el turno.</p>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Caja <span class="text-danger">*</span></label>
                            <select class="form-select border-0 bg-light py-2" id="cajaSelect" style="border-radius:8px">
                                <option value="">Cargando cajas...</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Base de apertura (efectivo inicial)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted">$</span>
                                <input type="number" class="form-control border-0 bg-light py-2" id="baseApertura" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm" id="btnAbrir" style="border-radius:10px">
                            <i class="fas fa-lock-open me-2"></i>Abrir Caja y Empezar Turno
                        </button>
                    </div>

                    <!-- Formulario Cerrar (oculto) -->
                    <div id="formCerrar" style="display:none">
                        <div class="text-center mb-4">
                            <div class="icon-circle bg-success text-white mx-auto mb-3" style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:1.5rem">
                                <i class="fas fa-check"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Sesión en Curso</h5>
                            <p class="text-muted small" id="cajaActivaNombre"></p>
                        </div>

                        <div class="p-3 bg-light rounded mb-4" style="border-radius:12px">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Fecha Apertura:</span>
                                <span class="fw-semibold small" id="cajaAperturaFecha"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Base Inicial:</span>
                                <span class="text-primary fw-bold" id="cajaBaseApertura"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Total de efectivo contado al cerrar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted">$</span>
                                <input type="number" class="form-control border-0 bg-light py-2" id="totalDeclarado" placeholder="0.00" min="0" step="0.01">
                            </div>
                            <small class="text-muted" style="font-size:0.75rem">Cuenta el dinero físico en el cajón y digítalo aquí.</small>
                        </div>
                        
                        <button class="btn btn-danger w-100 py-3 fw-bold shadow-sm" id="btnCerrar" style="border-radius:10px">
                            <i class="fas fa-lock me-2"></i>Realizar Cierre de Caja
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Columna Historial -->
        <div class="col-lg-10 mt-2">
            <div class="card border-0 shadow-sm" style="border-radius:14px">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-history me-2"></i>Tus últimas sesiones</h6>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.85rem">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-4">Caja</th>
                                    <th>Cajero</th>
                                    <th>Apertura</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Diferencia</th>
                                </tr>
                            </thead>
                            <tbody id="sesionesBody">
                                <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let token = '', sesionActiva = null;
    const fmt = v => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(v);
    const fmtDate = d => d ? new Date(d).toLocaleString('es-CO', { dateStyle: 'short', timeStyle: 'short' }) : '-';

    document.addEventListener('DOMContentLoaded', async () => {
        token = localStorage.getItem('pos_token');
        if (!token) { window.location.href = '/ventas/public/login'; return; }
        const user = JSON.parse(localStorage.getItem('pos_usuario') || '{}');
        document.getElementById('userName').textContent = user.nombre || '';
        document.getElementById('userRole').textContent = user.rol || '';
        
        document.getElementById('btnAbrir').addEventListener('click', abrirSesion);
        document.getElementById('btnCerrar').addEventListener('click', cerrarSesion);
        
        await Promise.all([cargarCajas(), verificarSesionActiva(), cargarSesiones()]);
    });

    async function api(url, method = 'GET', body = null) {
        const opts = { method, headers: { 'Authorization': 'Bearer ' + token } };
        if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const r = await fetch(url, opts);
        if (r.status === 401) logout();
        return r.json();
    }

    async function cargarCajas() {
        const res = await api('/ventas/public/api/cajas');
        const sel = document.getElementById('cajaSelect');
        if (!res.error && res.data.length) {
            sel.innerHTML = res.data.map(c => `<option value="${c.id}">${c.nombre} — ${c.sede_nombre}</option>`).join('');
        } else {
            sel.innerHTML = '<option value="">No hay cajas registradas</option>';
        }
    }

    async function verificarSesionActiva() {
        const res = await api('/ventas/public/api/sesiones-caja/activa');
        sesionActiva = res.data;
        if (sesionActiva) {
            document.getElementById('formAbrir').style.display = 'none';
            document.getElementById('formCerrar').style.display = 'block';
            document.getElementById('cajaActivaNombre').textContent = sesionActiva.caja_nombre;
            document.getElementById('cajaAperturaFecha').textContent = fmtDate(sesionActiva.fecha_apertura);
            document.getElementById('cajaBaseApertura').textContent = fmt(sesionActiva.base_apertura);
            setBanner('open', `Sesión de Caja Activa`, `Empezaste hace ${calcularTiempo(sesionActiva.fecha_apertura)}`);
        } else {
            document.getElementById('formAbrir').style.display = 'block';
            document.getElementById('formCerrar').style.display = 'none';
            setBanner('closed', 'Caja cerrada', 'Debes abrir una sesión para operar hoy.');
        }
    }

    function setBanner(type, title, sub) {
        const banner = document.getElementById('sesionBanner');
        const icon = document.getElementById('sesionBannerIcon');
        const titleEl = document.getElementById('sesionBannerTitle');
        const subEl = document.getElementById('sesionBannerSub');
        banner.classList.remove('d-none', 'alert-success', 'alert-warning');
        if (type === 'open') {
            banner.classList.add('alert-success');
            banner.style.background = 'rgba(0,176,116,0.08)';
            icon.style.background = '#00b074';
            icon.innerHTML = '<i class="fas fa-play"></i>';
        } else {
            banner.classList.add('alert-warning');
            banner.style.background = 'rgba(255,193,7,0.08)';
            icon.style.background = '#ffc107';
            icon.innerHTML = '<i class="fas fa-stop"></i>';
        }
        titleEl.textContent = title;
        subEl.textContent = sub;
    }

    function calcularTiempo(fecha) {
        const diff = (new Date() - new Date(fecha)) / 1000;
        const h = Math.floor(diff / 3600);
        const m = Math.floor((diff % 3600) / 60);
        return h > 0 ? `${h}h ${m}m` : `${m} min`;
    }

    async function cargarSesiones() {
        const res = await api('/ventas/public/api/sesiones-caja');
        const tbody = document.getElementById('sesionesBody');
        if (!res.data || !res.data.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Sin sesiones registradas</td></tr>';
            return;
        }
        tbody.innerHTML = res.data.map(s => {
            const badge = s.estado === 'Abierta'
                ? '<span class="badge bg-success bg-opacity-75">Abierta</span>'
                : '<span class="badge bg-secondary bg-opacity-75">Cerrada</span>';
            const dif = s.diferencia !== null
                ? `<span class="${parseFloat(s.diferencia) >= 0 ? 'text-success' : 'text-danger'} fw-bold">${fmt(s.diferencia)}</span>`
                : '<span class="text-muted">-</span>';
            return `<tr>
                <td class="ps-4 fw-bold">${s.caja_nombre}</td>
                <td class="text-muted">${s.cajero_nombre}</td>
                <td class="text-muted" style="font-size:0.82rem">${fmtDate(s.fecha_apertura)}</td>
                <td>${badge}</td>
                <td class="text-end pe-4">${dif}</td>
            </tr>`;
        }).join('');
    }

    async function abrirSesion() {
        const cajaId = document.getElementById('cajaSelect').value;
        const base = document.getElementById('baseApertura').value || 0;
        if (!cajaId) { Swal.fire('Requerido', 'Selecciona una caja.', 'warning'); return; }
        const btn = document.getElementById('btnAbrir');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Abriendo...';
        try {
            const res = await api('/ventas/public/api/sesiones-caja/abrir', 'POST', { caja_id: cajaId, base_apertura: base });
            if (res.error) { Swal.fire('Error', res.message, 'error'); }
            else { 
                Swal.fire({ icon: 'success', title: '¡Caja abierta!', text: 'Mucha suerte hoy.', timer: 1500, showConfirmButton: false }); 
                await verificarSesionActiva(); await cargarSesiones(); 
            }
        } finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-lock-open me-2"></i>Abrir Caja y Empezar Turno'; }
    }

    async function cerrarSesion() {
        if (!sesionActiva) return;
        const declarado = document.getElementById('totalDeclarado').value;
        if (declarado === "") { Swal.fire('Requerido', 'Debes ingresar el total contado.', 'warning'); return; }
        
        const confirm = await Swal.fire({
            title: '¿Confirmar cierre?',
            text: "Una vez cerrada la caja no se podrán registrar más ventas en este turno.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, cerrar ahora'
        });

        if (!confirm.isConfirmed) return;

        const res = await api(`/ventas/public/api/sesiones-caja/${sesionActiva.id}/cerrar`, 'POST', { total_declarado: declarado });
        if (res.error) { Swal.fire('Error', res.message, 'error'); return; }
        
        const difSign = parseFloat(res.diferencia) >= 0 ? 'Sobrante' : 'Faltante';
        await Swal.fire({
            icon: 'success', title: '¡Caja cerrada!',
            html: `<table class="table table-sm text-start mt-2">
                     <tr><td>Total esperado</td><td class="text-end fw-bold">${fmt(res.total_esperado)}</td></tr>
                     <tr><td>Total declarado</td><td class="text-end fw-bold">${fmt(declarado)}</td></tr>
                     <tr><td>${difSign}</td><td class="text-end fw-bold ${parseFloat(res.diferencia) >= 0 ? 'text-success' : 'text-danger'}">${fmt(Math.abs(res.diferencia))}</td></tr>
                   </table>`,
            confirmButtonText: 'Aceptar'
        });
        sesionActiva = null;
        await verificarSesionActiva();
        await cargarSesiones();
    }

    function logout() {
        localStorage.removeItem('pos_token'); localStorage.removeItem('pos_usuario');
        window.location.href = '/ventas/public/login';
    }
</script>
</body>
</html>
