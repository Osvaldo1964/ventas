<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terceros - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-user-friends me-2"></i> Terceros</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Gestión de Clientes y Proveedores</span>
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
                <li class="breadcrumb-item"><a href="/ventas/public/configuracion">Configuración</a></li>
                <li class="breadcrumb-item active">Terceros</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <h6 class="mb-0 text-muted">Añade y actualiza tus clientes y proveedores.</h6>
            <button class="btn btn-primary btn-sm px-3 ms-auto" id="btnNuevo"><i class="fas fa-plus me-1"></i> Nuevo Tercero</button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body px-0 py-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted" style="font-size: 0.85rem;">
                            <tr>
                                <th class="ps-4">Documento</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Email / Teléfono</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="terBody">
                            <tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalTer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nuevo Tercero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="terId">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-semibold">Tipo de Tercero <span class="text-danger">*</span></label>
                            <select class="form-select" id="terTipoTercero">
                                <option value="Cliente">Cliente</option>
                                <option value="Proveedor">Proveedor</option>
                                <option value="Ambos">Ambos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label class="form-label text-muted small fw-semibold">Tipo Doc</label>
                            <select class="form-select" id="terTipoDoc">
                                <option value="CC">CC</option>
                                <option value="NIT">NIT</option>
                                <option value="CE">CE</option>
                                <option value="TI">TI</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label text-muted small fw-semibold">N° Documento</label>
                            <input type="text" class="form-control" id="terNumDoc" placeholder="Ej. 10203040">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="terNombre" placeholder="Nombre completo o Razón social">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" id="terTel" placeholder="Ej. 300...">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-semibold">Email</label>
                            <input type="email" class="form-control text-lowercase" id="terEmail" placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Dirección</label>
                        <textarea class="form-control" id="terDir" rows="2" placeholder="Dirección de correspondencia"></textarea>
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
        let token = '', modal, terceros = [];

        document.addEventListener('DOMContentLoaded', async () => {
            token = localStorage.getItem('pos_token');
            if(!token) { window.location.href = '/ventas/public/login'; return; }
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';
            modal = new bootstrap.Modal(document.getElementById('modalTer'));
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

        async function cargar() {
            const res = await api('/ventas/public/api/terceros');
            terceros = res.data || [];
            const tbody = document.getElementById('terBody');
            if(!terceros.length) { tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-5">No hay terceros registrados</td></tr>`; return; }
            tbody.innerHTML = '';
            
            const badges = {
                'Cliente': '<span class="badge bg-primary bg-opacity-10 text-primary">Cliente</span>',
                'Proveedor': '<span class="badge bg-success bg-opacity-10 text-success">Proveedor</span>',
                'Ambos': '<span class="badge bg-warning bg-opacity-10 text-warning">Ambos</span>'
            };

            terceros.forEach(t => {
                const docInfo = t.numero_documento ? `${t.tipo_documento || ''} ${t.numero_documento}` : '<span class="text-muted small">N/A</span>';
                const contactInfo = [t.telefono, t.email].filter(Boolean).join('<br>') || '<span class="text-muted small">Sin datos de contacto</span>';
                
                tbody.innerHTML += `
                    <tr>
                        <td class="ps-4 fw-medium text-secondary" style="font-size: 0.9em;">${docInfo}</td>
                        <td class="fw-bold" style="color:#2c3e50">${t.nombre_completo}</td>
                        <td>${badges[t.tipo_tercero] || t.tipo_tercero}</td>
                        <td class="text-muted" style="font-size: 0.9em;">${contactInfo}</td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" style="width:32px;height:32px" onclick="editar(${t.id})" title="Editar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger rounded-circle" style="width:32px;height:32px" onclick="eliminar(${t.id},'${t.nombre_completo.replace("'", "\\'")}')" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;
            });
        }

        function abrir(t = null) {
            document.getElementById('terId').value = '';
            document.getElementById('terTipoTercero').value = 'Cliente';
            document.getElementById('terTipoDoc').value = 'CC';
            document.getElementById('terNumDoc').value = '';
            document.getElementById('terNombre').value = '';
            document.getElementById('terTel').value = '';
            document.getElementById('terEmail').value = '';
            document.getElementById('terDir').value = '';
            
            document.getElementById('modalTitle').textContent = 'Nuevo Tercero';
            
            if(t) {
                document.getElementById('modalTitle').textContent = 'Editar Tercero';
                document.getElementById('terId').value = t.id;
                document.getElementById('terTipoTercero').value = t.tipo_tercero;
                if(t.tipo_documento) document.getElementById('terTipoDoc').value = t.tipo_documento;
                document.getElementById('terNumDoc').value = t.numero_documento || '';
                document.getElementById('terNombre').value = t.nombre_completo;
                document.getElementById('terTel').value = t.telefono || '';
                document.getElementById('terEmail').value = t.email || '';
                document.getElementById('terDir').value = t.direccion || '';
            }
            modal.show();
        }

        function editar(id) {
            const t = terceros.find(x => x.id == id);
            if(t) abrir(t);
        }

        async function guardar() {
            const id = document.getElementById('terId').value;
            const data = {
                tipo_tercero: document.getElementById('terTipoTercero').value,
                tipo_documento: document.getElementById('terTipoDoc').value,
                numero_documento: document.getElementById('terNumDoc').value,
                nombre_completo: document.getElementById('terNombre').value,
                telefono: document.getElementById('terTel').value,
                email: document.getElementById('terEmail').value,
                direccion: document.getElementById('terDir').value
            };

            if(!data.nombre_completo) { Swal.fire('Error', 'El nombre completo es requerido.', 'warning'); return; }

            const url = id ? `/ventas/public/api/terceros/${id}` : '/ventas/public/api/terceros';
            const method = id ? 'PUT' : 'POST';
            
            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

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
                title: `¿Eliminar "${nombre}"?`, 
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true, 
                confirmButtonColor: '#dc3545', 
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            if(!r.isConfirmed) return;
            
            const res = await api(`/ventas/public/api/terceros/${id}`, 'DELETE');
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
