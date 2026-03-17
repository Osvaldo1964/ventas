<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bodegas - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-warehouse me-2"></i> Bodegas</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Ubicaciones de almacenamiento por sede</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName"></div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole"></div>
            </div>
            <a href="#" class="text-danger bg-light py-2 px-3 rounded" style="font-size: 0.8em; font-weight: 600;" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <div class="breadcrumb-nav">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/ventas/public/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/ventas/public/inventario">Inventarios</a></li>
                <li class="breadcrumb-item active">Bodegas</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <h6 class="mb-0 text-muted">Gestiona los espacios donde almacenas tus productos físicos.</h6>
            <button class="btn btn-primary btn-sm px-3 ms-auto" id="btnNuevo"><i class="fas fa-plus me-1"></i> Nueva Bodega</button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body px-0 py-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted" style="font-size: 0.85rem;">
                            <tr>
                                <th class="ps-4">Sede Asociada</th>
                                <th>Nombre de la Bodega</th>
                                <th>Ubicación / Referencia</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="bodBody">
                            <tr><td colspan="4" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalBod" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nueva Bodega</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bodId">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Sede Principal <span class="text-danger">*</span></label>
                        <select class="form-select" id="bodSede">
                            <!-- Options cargadas dinamicamente -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Nombre de la Bodega <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="bodNombre" placeholder="Ej. BODEGA PRINCIPAL, ANAQUEL 3...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Ubicación / Referencia</label>
                        <textarea class="form-control" id="bodUbi" rows="2" placeholder="Ej. Zona trasera del local, Calle 10..."></textarea>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary px-4" id="btnGuardar"><i class="fas fa-save me-1"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let token = '', modal, bodegas = [], sedes = [];

        document.addEventListener('DOMContentLoaded', async () => {
            token = localStorage.getItem('pos_token');
            if(!token) { window.location.href = '/ventas/public/login'; return; }
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';
            
            modal = new bootstrap.Modal(document.getElementById('modalBod'));
            
            await cargarSedes();
            await cargar();
            
            document.getElementById('btnNuevo').addEventListener('click', () => abrir());
            document.getElementById('btnGuardar').addEventListener('click', guardar);
        });

        async function api(url, method = 'GET', body = null) {
            const opts = { 
                method, 
                headers: { 
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                } 
            };
            if(body) opts.body = JSON.stringify(body);
            const r = await fetch(url, opts);
            if(r.status === 401) logout();
            return r.json();
        }

        async function cargarSedes() {
            const res = await api('/ventas/public/api/sedes');
            sedes = res.data || [];
            const select = document.getElementById('bodSede');
            select.innerHTML = '<option value="" disabled selected>Seleccione una sede...</option>';
            sedes.forEach(s => {
                select.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
            });
        }

        async function cargar() {
            const res = await api('/ventas/public/api/bodegas');
            bodegas = res.data || [];
            const tbody = document.getElementById('bodBody');
            
            if(!bodegas.length) { 
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-5">No existen bodegas creadas para tu sede/negocio.</td></tr>`; 
                return; 
            }
            
            tbody.innerHTML = '';
            bodegas.forEach(b => {
                tbody.innerHTML += `
                    <tr>
                        <td class="ps-4 fw-medium text-secondary" style="font-size: 0.9em;"><i class="fas fa-building me-2 text-muted"></i>${b.sede_nombre || 'Sede Desconocida'}</td>
                        <td class="fw-bold" style="color:#2c3e50">${b.nombre}</td>
                        <td class="text-muted" style="font-size: 0.9em;">${b.ubicacion || '<span class="fst-italic">Sin ubicación especificada</span>'}</td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" style="width:32px;height:32px" onclick="editar(${b.id})" title="Editar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger rounded-circle" style="width:32px;height:32px" onclick="eliminar(${b.id},'${b.nombre.replace("'", "\\'")}')" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;
            });
        }

        function abrir(b = null) {
            document.getElementById('bodId').value = '';
            document.getElementById('bodSede').value = '';
            document.getElementById('bodNombre').value = '';
            document.getElementById('bodUbi').value = '';
            
            document.getElementById('modalTitle').textContent = 'Nueva Bodega';
            
            // Si solo hay una sede, preseleccionarla para ayudar al usuario
            if(sedes.length === 1 && !b) {
                document.getElementById('bodSede').value = sedes[0].id;
            }
            
            if(b) {
                document.getElementById('modalTitle').textContent = 'Editar Bodega';
                document.getElementById('bodId').value = b.id;
                document.getElementById('bodSede').value = b.sede_id;
                document.getElementById('bodNombre').value = b.nombre;
                document.getElementById('bodUbi').value = b.ubicacion || '';
            }
            modal.show();
        }

        function editar(id) {
            const b = bodegas.find(x => x.id == id);
            if(b) abrir(b);
        }

        async function guardar() {
            const id = document.getElementById('bodId').value;
            const data = {
                sede_id: document.getElementById('bodSede').value,
                nombre: document.getElementById('bodNombre').value,
                ubicacion: document.getElementById('bodUbi').value
            };

            if(!data.sede_id) { Swal.fire('Error', 'Debes seleccionar una Sede.', 'warning'); return; }
            if(!data.nombre) { Swal.fire('Error', 'El nombre de la bodega es requerido.', 'warning'); return; }

            const url = id ? `/ventas/public/api/bodegas/${id}` : '/ventas/public/api/bodegas';
            const method = id ? 'PUT' : 'POST';
            
            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> ...';

            const res = await api(url, method, data);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar';

            if(!res.error) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                modal.hide(); await cargar();
            } else { Swal.fire('Error', res.message, 'error'); }
        }

        async function eliminar(id, nombre) {
            const r = await Swal.fire({ 
                icon: 'warning', 
                title: `¿Eliminar la bodega "${nombre}"?`, 
                text: "Si tiene movimientos o existencias ligadas no podrás eliminarla.",
                showCancelButton: true, 
                confirmButtonColor: '#dc3545', 
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            if(!r.isConfirmed) return;
            
            const res = await api(`/ventas/public/api/bodegas/${id}`, 'DELETE');
            if(!res.error) { 
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false }); 
                await cargar(); 
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }

        function logout() { localStorage.removeItem('pos_token'); localStorage.removeItem('pos_usuario'); window.location.href = '/ventas/public/login'; }
    </script>
</body>
</html>
