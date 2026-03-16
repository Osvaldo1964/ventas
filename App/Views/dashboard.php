<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary d-inline-block">Sistema POS Antigravity</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Selecciona un módulo para comenzar</span>
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

    <div class="container mt-4">
        <div class="row g-4 justify-content-center">
            
            <!-- Modulo Inventarios -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/inventario'">
                    <div class="module-icon-wrapper bg-light-green">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="module-title">Inventarios</div>
                    <div class="module-desc">Gestión de productos, bodegas y traslados.</div>
                </div>
            </div>

            <!-- Modulo Ventas -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="module-title">Ventas</div>
                    <div class="module-desc">Punto de venta POS y gestión de promociones.</div>
                </div>
            </div>

            <!-- Modulo Caja -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="module-title">Caja</div>
                    <div class="module-desc">Apertura, cierre y movimientos de efectivo.</div>
                </div>
            </div>

            <!-- Modulo Reportes -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="module-title">Reportes</div>
                    <div class="module-desc">Ventas, análisis avanzado e inventario físico.</div>
                </div>
            </div>

            <!-- Modulo E-commerce -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-green">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="module-title">E-commerce</div>
                    <div class="module-desc">Tienda online pública y pedidos.</div>
                </div>
            </div>

            <!-- Modulo Configuración -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/configuracion'">
                    <div class="module-icon-wrapper bg-light-blue">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="module-title">Configuración</div>
                    <div class="module-desc">Parámetros del sistema y usuarios.</div>
                </div>
            </div>

        </div>
    </div>

    <!-- Protector de Auth y Script -->
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
                } catch(e) {
                    console.error("Error parseando user", e);
                }
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
