<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary d-inline-block"><i class="fas fa-users-cog me-2"></i> Usuarios</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Gestión de accesos y empleados</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName">Cargando...</div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole">Cargando...</div>
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
                <li class="breadcrumb-item active">Usuarios</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Listado de Usuarios</h5>
                <button class="btn btn-primary btn-sm px-3 custom-shadow" onclick="abrirModal()"><i class="fas fa-plus me-1"></i> Nuevo Usuario</button>
            </div>
            <div class="card-body px-0 py-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted" style="font-size: 0.85rem;">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Sede</th>
                                <th>Estado</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando usuarios...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle" style="color: var(--primary-color);">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="userForm">
                        <input type="hidden" id="userId">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" required placeholder="Ej. Juan Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Email (Usuario) <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" required placeholder="juan@ejemplo.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Contraseña <span id="passReq" class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" placeholder="Mín. 6 caracteres">
                            <small class="text-muted" id="passHint" style="display:none">Dejar en blanco para mantener la actual.</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Rol <span class="text-danger">*</span></label>
                                <select class="form-select" id="rol_id" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Sede</label>
                                <select class="form-select" id="sede_id">
                                    <option value="">Ninguna...</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label text-muted small fw-semibold">Estado</label>
                            <select class="form-select" id="estado">
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4" id="btnGuardar">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let modal;
        let token = localStorage.getItem('pos_token');

        document.addEventListener('DOMContentLoaded', async () => {
            if(!token) {
                window.location.href = '/ventas/public/login';
                return;
            }
            
            modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';

            await cargarUsuarios();
            await cargarSelects();

            document.getElementById('btnGuardar').addEventListener('click', guardarUsuario);
        });

        async function cargarUsuarios() {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando usuarios...</td></tr>';
            
            try {
                const response = await fetch('/ventas/public/api/usuarios', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const resData = await response.json();
                
                if(!response.ok || resData.error) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error: ${resData.message || 'Desconocido'}</td></tr>`;
                    if(response.status === 401) logout();
                    return;
                }

                const users = resData.data;
                if(users.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-5">No hay usuarios registrados</td></tr>`;
                    return;
                }

                tbody.innerHTML = '';
                users.forEach(u => {
                    const badgeClass = u.estado === 'Activo' ? 'bg-success' : 'bg-danger';
                    const userJson = JSON.stringify(u).replace(/"/g, '&quot;');
                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 text-muted">#${u.id}</td>
                            <td class="fw-bold" style="color: #2c3e50;">${u.nombre}</td>
                            <td class="text-muted" style="font-size: 0.9em;">${u.email}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">${u.rol}</span></td>
                            <td class="text-muted" style="font-size: 0.9em;">${u.sede || '-'}</td>
                            <td><span class="badge ${badgeClass} bg-opacity-75">${u.estado || 'Activo'}</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" onclick='abrirModal(${userJson})' style="width: 32px; height: 32px;"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-light text-danger rounded-circle" onclick="eliminarUsuario(${u.id})" style="width: 32px; height: 32px;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
            } catch(e) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error de conexión a la API</td></tr>`;
            }
        }

        async function cargarSelects() {
            try {
                const [resRoles, resSedes] = await Promise.all([
                    fetch('/ventas/public/api/roles', { headers: { 'Authorization': 'Bearer ' + token } }).then(r => r.json()),
                    fetch('/ventas/public/api/sedes', { headers: { 'Authorization': 'Bearer ' + token } }).then(r => r.json())
                ]);

                if(!resRoles.error) {
                    const sel = document.getElementById('rol_id');
                    resRoles.data.forEach(r => {
                        sel.innerHTML += `<option value="${r.id}">${r.nombre}</option>`;
                    });
                }

                if(!resSedes.error) {
                    const sel = document.getElementById('sede_id');
                    resSedes.data.forEach(s => {
                        sel.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
                    });
                }
            } catch(e) { console.error("Error cargando selectores", e); }
        }

        function abrirModal(u = null) {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = u ? u.id : '';
            document.getElementById('modalTitle').textContent = u ? 'Editar Usuario' : 'Nuevo Usuario';
            
            // Ajustes para password
            document.getElementById('passReq').style.display = u ? 'none' : 'inline';
            document.getElementById('passHint').style.display = u ? 'block' : 'none';
            document.getElementById('password').required = !u;

            if(u) {
                document.getElementById('nombre').value = u.nombre;
                document.getElementById('email').value = u.email;
                document.getElementById('estado').value = u.estado || 'Activo';
                document.getElementById('rol_id').value = u.rol_id || '';
                document.getElementById('sede_id').value = u.sede_id || '';
            }
            modal.show();
        }

        async function guardarUsuario() {
            const id = document.getElementById('userId').value;
            const data = {
                nombre: document.getElementById('nombre').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                rol_id: document.getElementById('rol_id').value,
                sede_id: document.getElementById('sede_id').value,
                estado: document.getElementById('estado').value
            };

            if(!data.nombre || !data.email || (!id && !data.password) || !data.rol_id) {
                Swal.fire('Error', 'Por favor complete los campos obligatorios.', 'error');
                return;
            }

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/ventas/public/api/usuarios/${id}` : '/ventas/public/api/usuarios';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(data)
                });
                const res = await response.json();

                if(!response.ok || res.error) {
                    Swal.fire('Error', res.message || 'Error al guardar', 'error');
                } else {
                    Swal.fire('Éxito', res.message, 'success');
                    modal.hide();
                    cargarUsuarios();
                }
            } catch(e) { Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'); }
        }

        async function eliminarUsuario(id) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/ventas/public/api/usuarios/${id}`, {
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const res = await response.json();

                    if(!response.ok || res.error) {
                        Swal.fire('Error', res.message || 'Error al eliminar', 'error');
                    } else {
                        Swal.fire('Eliminado', res.message, 'success');
                        cargarUsuarios();
                    }
                } catch(e) { Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'); }
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
