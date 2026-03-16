<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles y Permisos - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            --secondary-bg: #f8f9fc;
        }
        body { background-color: var(--secondary-bg); }
        .perm-table th { 
            background-color: #f1f4f9; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            color: #5a5c69;
            border: none;
            padding: 12px 15px;
        }
        .perm-table td { border-color: #f1f4f9; padding: 12px 15px; }
        .module-row:hover { background-color: #fdfdfe; }
        .module-icon { 
            width: 32px; height: 32px; 
            background: #eef2f7; 
            border-radius: 8px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            margin-right: 12px; 
            color: #4e73df;
            font-size: 0.9rem;
        }
        .module-name { font-weight: 600; color: #4e5e7a; font-size: 0.95rem; }
        
        /* Modern Switch Styling */
        .form-check-input:checked { background-color: #1cc88a; border-color: #1cc88a; }
        .perm-check { 
            width: 1.25rem; 
            height: 1.25rem; 
            cursor: pointer; 
            border-radius: 4px !important;
        }

        .role-item {
            border: none !important;
            border-radius: 10px !important;
            margin-bottom: 8px;
            transition: all 0.2s;
            background: transparent;
            font-weight: 500;
            color: #5a5c69;
        }
        .role-item:hover {
            background-color: #fff !important;
            transform: translateX(5px);
            color: var(--primary-color) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .role-item.active {
            background-color: #fff !important;
            color: var(--primary-color) !important;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.1);
            border-left: 4px solid var(--primary-color) !important;
        }
        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(78, 115, 223, 0.2);
            color: white;
        }
        .card-editor {
            border-radius: 16px !important;
            border: none !important;
            overflow: hidden;
        }
        .editor-header {
            background: #fff;
            border-bottom: 1px solid #f1f4f9;
            padding: 24px;
        }
    </style>
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary d-inline-block"><i class="fas fa-user-shield me-2"></i> Control de Accesos</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Define qué pueden hacer tus empleados</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName">Cargando...</div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole">Cargando...</div>
            </div>
            <a href="#" class="text-danger bg-light py-2 px-3 rounded text-decoration-none" style="font-size: 0.8em; font-weight: 600;" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <div class="breadcrumb-nav">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/ventas/public/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/ventas/public/configuracion">Configuración</a></li>
                <li class="breadcrumb-item active">Roles y Permisos</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="row g-4">
            <!-- Listado de Roles -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 16px; background-color: rgba(255,255,255,0.6); backdrop-filter: blur(10px);">
                    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                        <h6 class="mb-0 fw-bold text-dark">Roles del Sistema</h6>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" onclick="nuevoRol()"><i class="fas fa-plus small me-1"></i> Crear</button>
                    </div>
                    <div id="rolesList" class="list-group list-group-flush">
                        <!-- Roles se cargan aquí -->
                    </div>
                </div>
            </div>

            <!-- Matriz de Permisos -->
            <div class="col-lg-8">
                <div class="card shadow-sm card-editor" style="display: none;" id="permEditor">
                    <div class="editor-header">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="mb-0 fw-bold" id="editorTitle">Configurar Permisos</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2" id="roleBadge">Editando</span>
                        </div>
                        <p class="text-muted small mb-0">Personaliza el acceso para el perfil: <span id="roleNameTitle" class="fw-bold text-primary"></span></p>
                    </div>
                    <div class="card-body p-4">
                        <input type="hidden" id="editRoleId">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">NOMBRE DEL PERFIL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-id-card text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 shadow-none p-3" id="roleNameInput" placeholder="Ej. Supervisor de Ventas">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-3">
                            <label class="form-label small fw-bold text-muted mb-0"><i class="fas fa-th-list me-1"></i> MATRIZ DE PERMISOS</label>
                            <div>
                                <button class="btn btn-link text-decoration-none small me-2 p-0" onclick="toggleAllPerms(true)">Seleccionar Todo</button>
                                <button class="btn btn-link text-decoration-none small text-muted p-0" onclick="toggleAllPerms(false)">Desmarcar Todo</button>
                            </div>
                        </div>

                        <div class="table-responsive rounded-3 overflow-hidden border">
                            <table class="table align-middle perm-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">MÓDULO</th>
                                        <th class="text-center">VER</th>
                                        <th class="text-center">CREAR</th>
                                        <th class="text-center">EDITAR</th>
                                        <th class="text-center">BORRAR</th>
                                    </tr>
                                </thead>
                                <tbody id="permMatrixBody">
                                    <!-- Módulos cargados dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <button class="btn btn-link text-danger text-decoration-none fw-bold" id="btnDeleteRole" onclick="eliminarRol()"><i class="fas fa-trash-alt me-1"></i> Eliminar este perfil</button>
                            <button class="btn btn-premium px-5 shadow" onclick="guardarRol()"><i class="fas fa-save me-2"></i> Guardar Configuración</button>
                        </div>
                    </div>
                </div>
                
                <div id="noRoleSelected" class="text-center py-5 text-muted">
                    <i class="fas fa-hand-pointer fa-3x mb-3 opacity-25"></i>
                    <p>Selecciona un rol o crea uno nuevo para configurar sus permisos.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const token = localStorage.getItem('pos_token');
        const modulos = [
            { id: 'dashboard', val: 'Dashboard', icon: 'fas fa-chart-line' },
            { id: 'tiendas', val: 'Tiendas', icon: 'fas fa-store' },
            { id: 'usuarios', val: 'Usuarios', icon: 'fas fa-users-cog' },
            { id: 'roles', val: 'Roles y Permisos', icon: 'fas fa-user-shield' },
            { id: 'categorias', val: 'Categorías', icon: 'fas fa-tags' },
            { id: 'productos', val: 'Productos', icon: 'fas fa-box-open' },
            { id: 'cajas', val: 'Gestión de Cajas', icon: 'fas fa-cash-register' },
            { id: 'ventas', val: 'Ventas (POS)', icon: 'fas fa-shopping-cart' },
            { id: 'reportes', val: 'Reportes', icon: 'fas fa-file-invoice-dollar' }
        ];

        let currentRoles = [];

        document.addEventListener('DOMContentLoaded', () => {
            if(!token) window.location.href = '/ventas/public/login';
            
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';

            initMatrix();
            cargarRoles();
        });

        function initMatrix() {
            const tbody = document.getElementById('permMatrixBody');
            tbody.innerHTML = '';
            modulos.forEach(mod => {
                tbody.innerHTML += `
                    <tr class="module-row">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="module-icon"><i class="${mod.icon}"></i></div>
                                <span class="module-name">${mod.val}</span>
                            </div>
                        </td>
                        <td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-mod="${mod.id}" data-action="v"></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-mod="${mod.id}" data-action="c"></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-mod="${mod.id}" data-action="e"></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-mod="${mod.id}" data-action="d"></td>
                    </tr>
                `;
            });
        }

        function toggleAllPerms(selected) {
            document.querySelectorAll('.perm-check').forEach(ck => {
                ck.checked = selected;
            });
        }

        async function cargarRoles() {
            try {
                const res = await fetch('/ventas/public/api/roles', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                if(data.error) throw new Error(data.message);
                
                currentRoles = data.data;
                const list = document.getElementById('rolesList');
                list.innerHTML = '';
                
                currentRoles.forEach(rol => {
                    const btn = document.createElement('button');
                    btn.className = 'list-group-item list-group-item-action role-item py-3 d-flex justify-content-between align-items-center mb-1';
                    btn.id = `role-btn-${rol.id}`;
                    btn.innerHTML = `<span><i class="fas fa-id-badge me-2 opacity-50"></i> ${rol.nombre}</span> <i class="fas fa-chevron-right small opacity-25"></i>`;
                    btn.onclick = () => editarRol(rol);
                    list.appendChild(btn);
                });
            } catch(e) { console.error(e); }
        }

        function editarRol(rol) {
            // Manejo de clase activa
            document.querySelectorAll('.role-item').forEach(el => el.classList.remove('active'));
            document.getElementById(`role-btn-${rol.id}`).classList.add('active');

            document.getElementById('noRoleSelected').style.display = 'none';
            document.getElementById('permEditor').style.display = 'block';
            document.getElementById('btnDeleteRole').style.display = 'block';
            document.getElementById('roleBadge').textContent = 'Editando';
            document.getElementById('roleBadge').className = 'badge bg-primary bg-opacity-10 text-primary px-3 py-2';
            
            document.getElementById('editRoleId').value = rol.id;
            document.getElementById('roleNameInput').value = rol.nombre;
            document.getElementById('roleNameTitle').textContent = rol.nombre;
            
            // Limpiar matriz
            document.querySelectorAll('.perm-check').forEach(ck => ck.checked = false);
            
            // Cargar permisos
            if(rol.permisos) {
                Object.keys(rol.permisos).forEach(modId => {
                    const modPerms = rol.permisos[modId];
                    Object.keys(modPerms).forEach(action => {
                        if(modPerms[action]) {
                            const ck = document.querySelector(`.perm-check[data-mod="${modId}"][data-action="${action}"]`);
                            if(ck) ck.checked = true;
                        }
                    });
                });
            }
        }

        function nuevoRol() {
            document.querySelectorAll('.role-item').forEach(el => el.classList.remove('active'));
            
            document.getElementById('noRoleSelected').style.display = 'none';
            document.getElementById('permEditor').style.display = 'block';
            document.getElementById('btnDeleteRole').style.display = 'none';
            document.getElementById('roleBadge').textContent = 'Nuevo';
            document.getElementById('roleBadge').className = 'badge bg-success bg-opacity-10 text-success px-3 py-2';
            
            document.getElementById('editRoleId').value = '';
            document.getElementById('roleNameInput').value = '';
            document.getElementById('roleNameTitle').textContent = 'Nuevo Perfil';
            document.querySelectorAll('.perm-check').forEach(ck => ck.checked = false);
        }

        async function guardarRol() {
            const id = document.getElementById('editRoleId').value;
            const nombre = document.getElementById('roleNameInput').value;
            
            if(!nombre) return Swal.fire('Atención', 'El nombre del rol es obligatorio', 'warning');
            
            const permisos = {};
            document.querySelectorAll('.perm-check').forEach(ck => {
                const mod = ck.dataset.mod;
                const act = ck.dataset.action;
                if(!permisos[mod]) permisos[mod] = {v:false, c:false, e:false, d:false};
                if(ck.checked) permisos[mod][act] = true;
            });

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/ventas/public/api/roles/${id}` : '/ventas/public/api/roles';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ nombre, permisos })
                });
                const data = await res.json();
                
                if(data.error) throw new Error(data.message);
                
                Swal.fire('Éxito', data.message, 'success');
                cargarRoles();
                if(!id) nuevoRol(); // Reset si es nuevo
            } catch(e) { Swal.fire('Error', e.message, 'error'); }
        }

        async function eliminarRol() {
            const id = document.getElementById('editRoleId').value;
            if(!id) return;

            const confirm = await Swal.fire({
                title: '¿Eliminar este rol?',
                text: "No se podrá eliminar si tiene usuarios asignados.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            });

            if(confirm.isConfirmed) {
                try {
                    const res = await fetch(`/ventas/public/api/roles/${id}`, {
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const data = await res.json();
                    if(data.error) throw new Error(data.message);
                    
                    Swal.fire('Eliminado', data.message, 'success');
                    document.getElementById('permEditor').style.display = 'none';
                    document.getElementById('noRoleSelected').style.display = 'block';
                    cargarRoles();
                } catch(e) { Swal.fire('Error', e.message, 'error'); }
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
