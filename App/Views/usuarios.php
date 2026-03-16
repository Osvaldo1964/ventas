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
                <button class="btn btn-primary btn-sm px-3 custom-shadow"><i class="fas fa-plus me-1"></i> Nuevo Usuario</button>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('pos_token');
            if(!token) {
                window.location.href = '/ventas/public/login';
                return;
            }
            
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';

            // Cargar usuarios desde la API
            try {
                const response = await fetch('/ventas/public/api/usuarios', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const resData = await response.json();
                
                const tbody = document.getElementById('usersTableBody');
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
                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 text-muted">#${u.id}</td>
                            <td class="fw-bold" style="color: #2c3e50;">${u.nombre}</td>
                            <td class="text-muted" style="font-size: 0.9em;">${u.email}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">${u.rol}</span></td>
                            <td class="text-muted" style="font-size: 0.9em;">${u.sede || '-'}</td>
                            <td><span class="badge ${badgeClass} bg-opacity-75">${u.estado || 'Activo'}</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" style="width: 32px; height: 32px;"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-light text-danger rounded-circle" style="width: 32px; height: 32px;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
            } catch(e) {
                document.getElementById('usersTableBody').innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error de conexión a la API</td></tr>`;
            }
        });

        function logout() {
            localStorage.removeItem('pos_token');
            localStorage.removeItem('pos_usuario');
            window.location.href = '/ventas/public/login';
        }
    </script>
</body>
</html>
