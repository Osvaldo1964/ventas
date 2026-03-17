<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary d-inline-block">Configuración</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Parámetros del sistema y usuarios.</span>
        </div>
        <div class="d-flex align-items-center">
            <i class="far fa-bell text-muted me-4 fs-5" style="cursor: pointer;"></i>
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName">Cargando...</div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole">Cargando...</div>
            </div>
            <a href="#" class="text-danger bg-light py-2 px-3 rounded text-decoration-none" style="font-size: 0.8em; font-weight: 600; margin-left: 10px;" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/ventas/public/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Configuración</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">

        <div class="row g-4 justify-content-center">
            
            <!-- Datos de la Tienda -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/configuracion/tiendas'">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="module-title">Datos de la Tienda</div>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/configuracion/usuarios'">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="module-title">Usuarios</div>
                </div>
            </div>

            <!-- Terceros -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/configuracion/terceros'">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="module-title">Terceros</div>
                </div>
            </div>

            <!-- Permisos -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/configuracion/roles'">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="module-title">Permisos</div>
                </div>
            </div>

            <!-- Sedes -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/configuracion/sedes'">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="module-title">Sedes</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('pos_token');
            if(!token) {
                window.location.href = '/ventas/public/login';
            } else {
                try {
                    const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
                    document.getElementById('userName').textContent = user.nombre || 'Administrador';
                    document.getElementById('userRole').textContent = user.rol || 'Admin';
                } catch(e) {}
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
