<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cajas - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>

<div class="top-navbar">
    <div>
        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-cash-register me-2"></i> Cajas</h4>
        <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size:0.9em;">Gestión de sesiones de caja y movimientos</span>
    </div>
    <div class="d-flex align-items-center">
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
            <li class="breadcrumb-item active">Cajas</li>
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
        <div id="sesionBannerActions"></div>
    </div>

    <div class="row g-4">

        <!-- Columna izquierda: Abrir/Cerrar sesión -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-door-open me-2"></i>Sesión de Caja</h6>
                </div>
                <div class="card-body px-4">

                    <!-- Formulario Abrir -->
                    <div id="formAbrir">
                        <p class="text-muted small mb-3">Selecciona la caja e ingresa el dinero inicial para abrir tu turno.</p>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Caja <span class="text-danger">*</span></label>
                            <select class="form-select" id="cajaSelect">
                                <option value="">Cargando cajas...</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Base de apertura (efectivo inicial)</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">$</span>
                                <input type="number" class="form-control" id="baseApertura" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>
                        <button class="btn btn-success w-100 py-2 fw-bold" id="btnAbrir">
                            <i class="fas fa-lock-open me-2"></i>Abrir Caja
                        </button>
                    </div>

                    <!-- Formulario Cerrar (oculto) -->
                    <div id="formCerrar" style="display:none">
                        <div class="alert alert-success border-0 mb-3" style="border-radius:10px;background:rgba(0,176,116,0.08)">
                            <div class="small text-muted">Sesión activa en</div>
                            <div class="fw-bold" id="cajaActivaNombre"></div>
                            <div class="small text-muted mt-1">Apertura: <span id="cajaAperturaFecha"></span></div>
                            <div class="small">Base inicial: <strong id="cajaBaseApertura"></strong></div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Total declarado al cerrar</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">$</span>
                                <input type="number" class="form-control" id="totalDeclarado" placeholder="0.00" min="0" step="0.01">
                            </div>
                            <small class="text-muted">Cuenta el efectivo en caja y declaralo aquí.</small>
                        </div>
                        <button class="btn btn-danger w-100 py-2 fw-bold" id="btnCerrar">
                            <i class="fas fa-lock me-2"></i>Cerrar Caja
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Columna derecha: Movimientos + Sesiones recientes -->
        <div class="col-lg-7">

            <!-- Movimientos de la sesión activa -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px" id="panelMovimientos">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-exchange-alt me-2"></i>Movimientos (sesión actual)</h6>
                    <button class="btn btn-primary btn-sm px-3" id="btnNuevoMovimiento" style="display:none" onclick="modalMovimiento()">
                        <i class="fas fa-plus me-1"></i> Nuevo
                    </button>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-4">Fecha</th>
                                    <th>Tipo</th>
                                    <th>Concepto</th>
                                    <th class="text-end pe-4">Monto</th>
                                </tr>
                            </thead>
                            <tbody id="movimientosBody">
                                <tr><td colspan="4" class="text-center text-muted py-4">Abre una sesión para ver los movimientos</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sesiones recientes -->
            <div class="card border-0 shadow-sm" style="border-radius:14px">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-history me-2"></i>Historial de Sesiones</h6>
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

<!-- Modal Nuevo Movimiento -->
<div class="modal fade" id="modalMov" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-primary">Registrar Movimiento de Caja</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Tipo</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success flex-fill" id="btnTipoIngreso" onclick="setTipo('Ingreso')"><i class="fas fa-arrow-down me-1"></i>Ingreso</button>
                        <button class="btn btn-outline-danger flex-fill" id="btnTipoGasto" onclick="setTipo('Gasto')"><i class="fas fa-arrow-up me-1"></i>Gasto</button>
                    </div>
                    <input type="hidden" id="movTipo" value="Ingreso">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Monto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text text-muted">$</span>
                        <input type="number" class="form-control" id="movMonto" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Observación</label>
                    <input type="text" class="form-control" id="movObservacion" placeholder="Ej. Pago de arriendo, caja menor...">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary px-4" id="btnGuardarMov"><i class="fas fa-save me-1"></i>Guardar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let token = '', sesionActiva = null, modalMov;
    const fmt = v => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(v);
    const fmtDate = d => d ? new Date(d).toLocaleString('es-CO', { dateStyle: 'short', timeStyle: 'short' }) : '-';

    document.addEventListener('DOMContentLoaded', async () => {
        token = localStorage.getItem('pos_token');
        if (!token) { window.location.href = '/ventas/public/login'; return; }
        const user = JSON.parse(localStorage.getItem('pos_usuario') || '{}');
        document.getElementById('userName').textContent = user.nombre || '';
        document.getElementById('userRole').textContent = user.rol || '';
        modalMov = new bootstrap.Modal(document.getElementById('modalMov'));
        document.getElementById('btnAbrir').addEventListener('click', abrirSesion);
        document.getElementById('btnCerrar').addEventListener('click', cerrarSesion);
        document.getElementById('btnGuardarMov').addEventListener('click', guardarMovimiento);
        setTipo('Ingreso');
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
            // Hay sesión abierta
            document.getElementById('formAbrir').style.display = 'none';
            document.getElementById('formCerrar').style.display = 'block';
            document.getElementById('cajaActivaNombre').textContent = sesionActiva.caja_nombre;
            document.getElementById('cajaAperturaFecha').textContent = fmtDate(sesionActiva.fecha_apertura);
            document.getElementById('cajaBaseApertura').textContent = fmt(sesionActiva.base_apertura);
            document.getElementById('btnNuevoMovimiento').style.display = 'inline-block';
            setBanner('open', `Caja abierta: ${sesionActiva.caja_nombre}`, `Desde ${fmtDate(sesionActiva.fecha_apertura)}`);
            await cargarMovimientos(sesionActiva.id);
        } else {
            document.getElementById('formAbrir').style.display = 'block';
            document.getElementById('formCerrar').style.display = 'none';
            document.getElementById('btnNuevoMovimiento').style.display = 'none';
            setBanner('closed', 'No hay una sesión de caja abierta', 'Debes abrir una sesión para registrar ventas.');
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
            icon.innerHTML = '<i class="fas fa-cash-register"></i>';
        } else {
            banner.classList.add('alert-warning');
            banner.style.background = 'rgba(255,193,7,0.08)';
            icon.style.background = '#ffc107';
            icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
        }
        titleEl.textContent = title;
        subEl.textContent = sub;
    }

    async function cargarMovimientos(sesionId) {
        const res = await api(`/ventas/public/api/sesiones-caja/${sesionId}/movimientos`);
        const tbody = document.getElementById('movimientosBody');
        if (!res.data || !res.data.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Sin movimientos aún</td></tr>';
            return;
        }
        tbody.innerHTML = res.data.map(m => `
            <tr>
                <td class="ps-4 text-muted">${fmtDate(m.fecha)}</td>
                <td><span class="badge ${m.tipo === 'Ingreso' ? 'bg-success' : 'bg-danger'} bg-opacity-75">${m.tipo}</span></td>
                <td class="text-muted">${m.concepto_nombre || m.observacion || '-'}</td>
                <td class="text-end pe-4 fw-bold ${m.tipo === 'Ingreso' ? 'text-success' : 'text-danger'}">${m.tipo === 'Ingreso' ? '+' : '-'} ${fmt(m.monto)}</td>
            </tr>`).join('');
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
            else { Swal.fire({ icon: 'success', title: '¡Caja abierta!', timer: 1500, showConfirmButton: false }); await verificarSesionActiva(); await cargarSesiones(); }
        } finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-lock-open me-2"></i>Abrir Caja'; }
    }

    async function cerrarSesion() {
        if (!sesionActiva) return;
        const { value: declarado } = await Swal.fire({
            title: 'Cierre de Caja',
            html: `<p class="text-muted small">Ingresa el efectivo contado en la caja al momento del cierre:</p>
                   <div class="input-group"><span class="input-group-text">$</span>
                   <input id="swal-declarado" type="number" class="form-control" placeholder="0.00" min="0" step="0.01"></div>`,
            confirmButtonText: 'Cerrar Caja',
            confirmButtonColor: '#dc3545',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            preConfirm: () => document.getElementById('swal-declarado').value || 0
        });
        if (declarado === undefined) return;
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

    function setTipo(tipo) {
        document.getElementById('movTipo').value = tipo;
        document.getElementById('btnTipoIngreso').className = tipo === 'Ingreso'
            ? 'btn btn-success flex-fill' : 'btn btn-outline-success flex-fill';
        document.getElementById('btnTipoGasto').className = tipo === 'Gasto'
            ? 'btn btn-danger flex-fill' : 'btn btn-outline-danger flex-fill';
    }

    function modalMovimiento() { modalMov.show(); }

    async function guardarMovimiento() {
        if (!sesionActiva) return;
        const monto = document.getElementById('movMonto').value;
        if (!monto || monto <= 0) { Swal.fire('Requerido', 'Ingresa un monto válido.', 'warning'); return; }
        const res = await api(`/ventas/public/api/sesiones-caja/${sesionActiva.id}/movimientos`, 'POST', {
            tipo: document.getElementById('movTipo').value,
            monto: monto,
            observacion: document.getElementById('movObservacion').value
        });
        if (res.error) { Swal.fire('Error', res.message, 'error'); return; }
        modalMov.hide();
        document.getElementById('movMonto').value = '';
        document.getElementById('movObservacion').value = '';
        await cargarMovimientos(sesionActiva.id);
    }

    function logout() {
        localStorage.removeItem('pos_token'); localStorage.removeItem('pos_usuario');
        window.location.href = '/ventas/public/login';
    }
</script>
</body>
</html>
