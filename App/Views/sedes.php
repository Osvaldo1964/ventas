<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedes - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="top-navbar">
    <div>
        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-building me-2"></i> Sedes / Sucursales</h4>
        <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size:0.9em;">Gestiona las ubicaciones físicas de tu negocio</span>
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
            <li class="breadcrumb-item"><a href="/ventas/public/configuracion">Configuración</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sedes</li>
        </ol>
    </nav>
</div>

<div class="container pb-5">

    <div class="card border-0 shadow-sm mt-4" style="border-radius:14px">
        <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-primary mb-0"><i class="fas fa-list me-2"></i>Listado de Sedes</h6>
            <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold" onclick="abrirModal()">
                <i class="fas fa-plus me-1"></i> Nueva Sede
            </button>
        </div>
        <div class="card-body px-0 py-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaSedes" style="font-size:0.9rem">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-4" style="width:80px">ID</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="sedesBody">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="mt-2 text-muted mb-0">Cargando sedes...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sede (Nueva/Editar) -->
<div class="modal fade" id="modalSede" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:16px">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nueva Sede</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="formSede">
                    <input type="hidden" id="sedeId">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre de la Sede <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-0 bg-light py-2 px-3" id="sedeNombre" placeholder="Ej: Sucursal Centro" required style="border-radius:10px">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Dirección</label>
                        <input type="text" class="form-control border-0 bg-light py-2 px-3" id="sedeDireccion" placeholder="Ej: Calle 10 # 5-20" style="border-radius:10px">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Teléfono</label>
                        <input type="text" class="form-control border-0 bg-light py-2 px-3" id="sedeTelefono" placeholder="Ej: 300 123 4567" style="border-radius:10px">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Estado</label>
                        <select class="form-select border-0 bg-light py-2 px-3" id="sedeEstado" style="border-radius:10px">
                            <option value="Activa">Activa</option>
                            <option value="Inactiva">Inactiva</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius:10px">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnGuardar" style="border-radius:10px">
                    <i class="fas fa-save me-1"></i> Guardar Sede
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let token = '', modalSede;
    
    document.addEventListener('DOMContentLoaded', async () => {
        token = localStorage.getItem('pos_token');
        if (!token) { window.location.href = '/ventas/public/login'; return; }
        
        const user = JSON.parse(localStorage.getItem('pos_usuario') || '{}');
        document.getElementById('userName').textContent = user.nombre || 'Administrador';
        document.getElementById('userRole').textContent = user.rol || 'Admin';

        modalSede = new bootstrap.Modal(document.getElementById('modalSede'));
        document.getElementById('btnGuardar').addEventListener('click', guardarSede);

        await cargarSedes();
    });

    async function api(url, method = 'GET', body = null) {
        const opts = { method, headers: { 'Authorization': 'Bearer ' + token } };
        if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const r = await fetch(url, opts);
        if (r.status === 401) logout();
        return r.json();
    }

    async function cargarSedes() {
        const res = await api('/ventas/public/api/sedes');
        const tbody = document.getElementById('sedesBody');
        if (res.error || !res.data.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No hay sedes registradas.</td></tr>';
            return;
        }
        tbody.innerHTML = res.data.map(s => `
            <tr>
                <td class="ps-4 fw-bold text-muted">#${s.id}</td>
                <td class="fw-bold text-primary">${s.nombre}</td>
                <td class="text-muted">${s.direccion || '-'}</td>
                <td class="text-muted">${s.telefono || '-'}</td>
                <td><span class="badge ${s.estado === 'Activa' ? 'bg-success' : 'bg-secondary'} bg-opacity-75">${s.estado}</span></td>
                <td class="text-end pe-4">
                    <button class="btn btn-light btn-sm rounded-circle me-1" onclick="abrirModal(${JSON.stringify(s).replace(/"/g, '&quot;')})" title="Editar">
                        <i class="fas fa-edit text-primary"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle" onclick="eliminarSede(${s.id})" title="Eliminar">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function abrirModal(datos = null) {
        document.getElementById('formSede').reset();
        if (datos) {
            document.getElementById('modalTitle').textContent = 'Editar Sede';
            document.getElementById('sedeId').value = datos.id;
            document.getElementById('sedeNombre').value = datos.nombre;
            document.getElementById('sedeDireccion').value = datos.direccion || '';
            document.getElementById('sedeTelefono').value = datos.telefono || '';
            document.getElementById('sedeEstado').value = datos.estado;
        } else {
            document.getElementById('modalTitle').textContent = 'Nueva Sede';
            document.getElementById('sedeId').value = '';
        }
        modalSede.show();
    }

    async function guardarSede() {
        const id = document.getElementById('sedeId').value;
        const nombre = document.getElementById('sedeNombre').value;
        const direccion = document.getElementById('sedeDireccion').value;
        const telefono = document.getElementById('sedeTelefono').value;
        const estado = document.getElementById('sedeEstado').value;

        if (!nombre) {
            Swal.fire('Atención', 'El nombre es obligatorio.', 'warning');
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/ventas/public/api/sedes/${id}` : '/ventas/public/api/sedes';
        
        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';

        try {
            const res = await api(url, method, { nombre, direccion, telefono, estado });
            if (res.error) {
                Swal.fire('Error', res.message, 'error');
            } else {
                Swal.fire({ icon: 'success', title: '¡Listo!', text: res.message, timer: 1500, showConfirmButton: false });
                modalSede.hide();
                cargarSedes();
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Sede';
        }
    }

    async function eliminarSede(id) {
        const confirm = await Swal.fire({
            title: '¿Eliminar sede?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirm.isConfirmed) return;

        const res = await api(`/ventas/public/api/sedes/${id}`, 'DELETE');
        if (res.error) {
            Swal.fire('Error', res.message, 'error');
        } else {
            Swal.fire({ icon: 'success', title: 'Eliminada', text: 'La sede ha sido removida.', timer: 1500, showConfirmButton: false });
            cargarSedes();
        }
    }

    function logout() {
        localStorage.removeItem('pos_token');
        localStorage.removeItem('pos_usuario');
        window.location.href = '/ventas/public/login';
    }
</script>

</body>
</html>
