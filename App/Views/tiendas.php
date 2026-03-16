<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiendas y Empresas - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
    <style>
        .logo-preview-wrap {
            width: 120px; height: 120px; border-radius: 12px;
            border: 2px dashed #d0d7e2; display: flex; align-items: center;
            justify-content: center; overflow: hidden; cursor: pointer;
            transition: border-color 0.3s;
        }
        .logo-preview-wrap:hover { border-color: var(--primary-color); }
        .logo-preview-wrap img { width: 100%; height: 100%; object-fit: contain; border-radius: 10px; }
        .logo-placeholder { text-align: center; color: #b0bec5; }
        .logo-placeholder i { font-size: 2rem; }
        .logo-placeholder p { font-size: 0.75rem; margin: 4px 0 0; }
    </style>
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary d-inline-block"><i class="fas fa-store me-2"></i> Tiendas Registradas</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Gestión de parámetros de empresas SaaS</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName"></div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole"></div>
            </div>
            <a href="#" class="text-danger bg-light py-2 px-3 rounded text-decoration-none" style="font-size: 0.8em; font-weight: 600;" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/ventas/public/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/ventas/public/configuracion">Configuración</a></li>
                <li class="breadcrumb-item active">Tiendas</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Datos de Tiendas</h5>
                <button class="btn btn-primary btn-sm px-3" id="btnNuevaTienda" style="display:none;" onclick="abrirModal()">
                    <i class="fas fa-plus me-1"></i> Nueva Tienda
                </button>
            </div>
            <div class="card-body px-0 py-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted" style="font-size: 0.85rem;">
                            <tr>
                                <th class="ps-4" style="width:60px">Logo</th>
                                <th>Nombre Comercial</th>
                                <th>Slogan</th>
                                <th>NIT</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tiendasTableBody">
                            <tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear / Editar Tienda -->
    <div class="modal fade" id="modalTienda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nueva Tienda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="tiendaId">
                    <div class="row g-3">

                        <!-- Logo + Slogan columna izquierda -->
                        <div class="col-md-3 d-flex flex-column align-items-center">
                            <label class="form-label text-muted small fw-semibold text-center mb-2">Logo de la Tienda</label>
                            <div class="logo-preview-wrap" onclick="document.getElementById('logoInput').click()" id="logoWrap">
                                <div class="logo-placeholder" id="logoPlaceholder">
                                    <i class="fas fa-image"></i>
                                    <p>Clic para subir<br>JPG / PNG / WebP</p>
                                </div>
                                <img id="logoPreview" src="" style="display:none">
                            </div>
                            <input type="file" id="logoInput" accept="image/*" style="display:none" onchange="previewLogo(this)">
                            <small class="text-muted mt-2 text-center" style="font-size:0.75em">Se muestra en reportes,<br>tickets de venta y e-commerce</small>
                        </div>

                        <!-- Campos derecha -->
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label text-muted small fw-semibold">Nombre Comercial <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_comercial" required placeholder="Ej. Ferretería El Perno">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label text-muted small fw-semibold">Razón Social</label>
                                    <input type="text" class="form-control" id="razon_social" placeholder="Nombre legal">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-semibold">Slogan <span class="text-muted fw-normal">(Aparece en reportes y e-commerce)</span></label>
                                    <input type="text" class="form-control" id="slogan" placeholder="Ej. La mejor calidad al mejor precio...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-semibold">NIT</label>
                                    <input type="text" class="form-control" id="nit" placeholder="900.123.456-7">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-semibold">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" placeholder="+57 300 000 0000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-semibold">Email de Contacto</label>
                                    <input type="email" class="form-control" id="email_contacto" placeholder="info@tienda.com">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label text-muted small fw-semibold">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" placeholder="Calle / Carrera...">
                                </div>
                                <div class="col-md-4" id="estadoGroup" style="display:none">
                                    <label class="form-label text-muted small fw-semibold">Estado</label>
                                    <select class="form-select" id="estado">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Suspendido">Suspendido</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Admin de la Tienda (solo al crear) -->
                        <div class="col-12" id="adminSection">
                            <hr class="my-1">
                            <div class="d-flex align-items-center gap-2 mb-2 mt-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white bg-primary" style="width:28px;height:28px;font-size:0.8rem;flex-shrink:0"><i class="fas fa-user-shield"></i></div>
                                <div>
                                    <div class="fw-bold text-primary" style="font-size: 0.9rem;">Administrador de la Tienda</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">Este usuario tendrá acceso completo a la gestión de esta tienda (usuarios, config, inventario).</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="admin_nombre" placeholder="Ej. Juan Pérez">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-semibold">Email (usuario) <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="admin_email" placeholder="admin@tienda.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-semibold">Contraseña temporal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="admin_password" placeholder="Contraseña inicial">
                                        <button class="btn btn-outline-secondary" type="button" onclick="generarPassword()"><i class="fas fa-random"></i></button>
                                    </div>
                                    <small class="text-muted" style="font-size:0.73rem">El admin deberá cambiarla al ingresar por primera vez.</small>
                                </div>
                            </div>
                        </div>

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
        let token = '', modal, isGlobalAdmin = false, allTiendas = [];

        document.addEventListener('DOMContentLoaded', async () => {
            token = localStorage.getItem('pos_token');
            if(!token) { window.location.href = '/ventas/public/login'; return; }
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';
            modal = new bootstrap.Modal(document.getElementById('modalTienda'));
            document.getElementById('btnGuardar').addEventListener('click', guardar);
            await cargar();
        });

        async function api(url, method = 'GET', body = null) {
            const opts = { method, headers: { 'Authorization': 'Bearer ' + token } };
            if(body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
            const r = await fetch(url, opts);
            if(r.status === 401) logout();
            return r.json();
        }

        async function cargar() {
            const tbody = document.getElementById('tiendasTableBody');
            try {
                const res = await api('/ventas/public/api/tiendas');
                if(res.error) { tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${res.message}</td></tr>`; return; }
                isGlobalAdmin = res.global_admin;
                allTiendas = res.data;
                if(isGlobalAdmin) document.getElementById('btnNuevaTienda').style.display = 'inline-block';
                if(!allTiendas.length) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-5">Sin tiendas registradas</td></tr>`;
                    return;
                }
                tbody.innerHTML = '';
                allTiendas.forEach(t => {
                    const badge = t.estado === 'Activo' ? 'bg-success' : 'bg-secondary';
                    const logoCell = t.logo_url
                        ? `<img src="${t.logo_url}" style="width:40px;height:40px;object-fit:contain;border-radius:6px;border:1px solid #eee">`
                        : `<span class="text-muted" style="font-size:0.75em">Sin logo</span>`;
                    const deleteBtn = isGlobalAdmin
                        ? `<button class="btn btn-sm btn-light text-danger rounded-circle" style="width:32px;height:32px"
                              onclick="eliminar(${t.id},'${t.nombre_comercial}')" title="Desactivar"><i class="fas fa-trash"></i></button>` : '';
                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4">${logoCell}</td>
                            <td>
                                <div class="fw-bold" style="color:#2c3e50">${t.nombre_comercial}</div>
                            </td>
                            <td class="text-muted" style="font-size:0.85em;font-style:italic">${t.slogan || '-'}</td>
                            <td class="text-muted" style="font-size:0.9em">${t.nit || '-'}</td>
                            <td class="text-muted" style="font-size:0.9em">${t.telefono || '-'}</td>
                            <td><span class="badge ${badge} bg-opacity-75">${t.estado || 'Activo'}</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" style="width:32px;height:32px"
                                    onclick="editar(${t.id})" title="Editar"><i class="fas fa-edit"></i></button>
                                ${deleteBtn}
                            </td>
                        </tr>`;
                });
            } catch(e) {
                document.getElementById('tiendasTableBody').innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error de conexión</td></tr>`;
            }
        }

        function previewLogo(input) {
            if(input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('logoPreview').src = e.target.result;
                    document.getElementById('logoPreview').style.display = 'block';
                    document.getElementById('logoPlaceholder').style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function generarPassword() {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789@#!';
            let pass = '';
            for(let i = 0; i < 10; i++) pass += chars[Math.floor(Math.random() * chars.length)];
            document.getElementById('admin_password').value = pass;
        }

        function abrirModal(t = null) {
            ['nombre_comercial','razon_social','slogan','nit','telefono','email_contacto','direccion','tiendaId',
             'admin_nombre','admin_email','admin_password'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('estado').value = 'Activo';
            document.getElementById('estadoGroup').style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Nueva Tienda';
            document.getElementById('logoPreview').style.display = 'none';
            document.getElementById('logoPlaceholder').style.display = 'flex';
            document.getElementById('logoInput').value = '';
            // Sección admin: visible solo al crear
            document.getElementById('adminSection').style.display = t ? 'none' : 'block';

            if(t) {
                document.getElementById('modalTitle').textContent = 'Editar Tienda';
                document.getElementById('tiendaId').value = t.id;
                document.getElementById('nombre_comercial').value = t.nombre_comercial || '';
                document.getElementById('razon_social').value = t.razon_social || '';
                document.getElementById('slogan').value = t.slogan || '';
                document.getElementById('nit').value = t.nit || '';
                document.getElementById('telefono').value = t.telefono || '';
                document.getElementById('email_contacto').value = t.email_contacto || '';
                document.getElementById('direccion').value = t.direccion || '';
                document.getElementById('estado').value = t.estado || 'Activo';
                document.getElementById('estadoGroup').style.display = 'block';
                if(t.logo_url) {
                    document.getElementById('logoPreview').src = t.logo_url + '?t=' + Date.now();
                    document.getElementById('logoPreview').style.display = 'block';
                    document.getElementById('logoPlaceholder').style.display = 'none';
                }
            }
            modal.show();
        }

        async function editar(id) {
            const res = await api(`/ventas/public/api/tiendas/${id}`);
            if(!res.error) abrirModal(res.data);
            else Swal.fire('Error', res.message, 'error');
        }

        async function guardar() {
            const id = document.getElementById('tiendaId').value;
            if(!document.getElementById('nombre_comercial').value) {
                Swal.fire('Requerido', 'El nombre comercial es obligatorio.', 'warning'); return;
            }

            const btn = document.getElementById('btnGuardar');
            btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                // Armar cuerpo del request
                const body = {
                    nombre_comercial: document.getElementById('nombre_comercial').value,
                    slogan: document.getElementById('slogan').value,
                    razon_social: document.getElementById('razon_social').value,
                    nit: document.getElementById('nit').value,
                    telefono: document.getElementById('telefono').value,
                    email_contacto: document.getElementById('email_contacto').value,
                    direccion: document.getElementById('direccion').value,
                    estado: document.getElementById('estado').value,
                };

                // Si es nueva tienda, añadir datos del admin
                if(!id) {
                    body.admin_nombre   = document.getElementById('admin_nombre').value;
                    body.admin_email    = document.getElementById('admin_email').value;
                    body.admin_password = document.getElementById('admin_password').value;
                }

                const url = id ? `/ventas/public/api/tiendas/${id}` : '/ventas/public/api/tiendas';
                const res = await api(url, id ? 'PUT' : 'POST', body);

                if(res.error) { Swal.fire('Error', res.message, 'error'); return; }

                const tiendaId = id || res.id;

                // Subir logo si hay archivo seleccionado
                const logoFile = document.getElementById('logoInput').files[0];
                if(logoFile) {
                    const formData = new FormData();
                    formData.append('logo', logoFile);
                    const logoResp = await fetch(`/ventas/public/api/tiendas/${tiendaId}/logo`, {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token },
                        body: formData
                    });
                    const logoData = await logoResp.json();
                    if(logoData.error) {
                        Swal.fire('Advertencia', 'Tienda guardada, pero el logo no pudo subirse: ' + logoData.message, 'warning');
                        modal.hide(); await cargar(); return;
                    }
                }

                modal.hide();
                await cargar();

                // Si es tienda nueva, mostrar credenciales del admin creado
                if(!id && res.admin) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Tienda creada exitosamente!',
                        html: `<p>${res.message}</p>
                               <div class="alert alert-warning text-start mt-2 mb-0" style="font-size:0.9em">
                                 <strong><i class="fas fa-key me-1"></i> Credenciales del Administrador:</strong><br>
                                 Email: <code>${res.admin.email}</code><br>
                                 Contraseña: <code>${res.admin.password}</code><br>
                                 <small>Compártelas con el admin de la tienda. Deberá cambiarlas al ingresar.</small>
                               </div>`,
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                }

            } finally {
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar';
            }
        }

        async function eliminar(id, nombre) {
            const confirm = await Swal.fire({
                icon: 'warning', title: `¿Desactivar "${nombre}"?`,
                text: 'La tienda quedará inactiva.',
                showCancelButton: true, confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, desactivar', cancelButtonText: 'Cancelar'
            });
            if(!confirm.isConfirmed) return;
            const res = await api(`/ventas/public/api/tiendas/${id}`, 'DELETE');
            if(!res.error) { Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false }); await cargar(); }
            else Swal.fire('Error', res.message, 'error');
        }

        function logout() {
            localStorage.removeItem('pos_token'); localStorage.removeItem('pos_usuario');
            window.location.href = '/ventas/public/login';
        }
    </script>
</body>
</html>
