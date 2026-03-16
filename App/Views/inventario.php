<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventarios - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-box-open me-2"></i> Inventarios</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Gestión de productos, categorías y stock</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName"></div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole"></div>
            </div>
            <a href="#" class="text-danger bg-light py-2 px-3 rounded" style="font-size: 0.8em; font-weight: 600;" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/ventas/public/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Inventarios</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="row g-4 justify-content-center">

            <!-- Productos -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/inventario/productos'">
                    <div class="module-icon-wrapper bg-light-green"><i class="fas fa-boxes"></i></div>
                    <div class="module-title">Productos</div>
                    <div class="module-desc">Catálogo de artículos, precios y variantes.</div>
                </div>
            </div>

            <!-- Categorías -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card" onclick="window.location.href='/ventas/public/inventario/categorias'">
                    <div class="module-icon-wrapper bg-light-blue"><i class="fas fa-tags"></i></div>
                    <div class="module-title">Categorías</div>
                    <div class="module-desc">Clasificación para tienda y e-commerce.</div>
                </div>
            </div>

            <!-- Bodegas -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-green"><i class="fas fa-warehouse"></i></div>
                    <div class="module-title">Bodegas</div>
                    <div class="module-desc">Ubicaciones físicas de almacenamiento.</div>
                </div>
            </div>

            <!-- Traslados -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-blue"><i class="fas fa-exchange-alt"></i></div>
                    <div class="module-title">Traslados</div>
                    <div class="module-desc">Movimientos de stock entre bodegas.</div>
                </div>
            </div>

            <!-- Entradas / Compras -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-green"><i class="fas fa-truck-loading"></i></div>
                    <div class="module-title">Entradas de Inventario</div>
                    <div class="module-desc">Registro de compras y abastecimiento.</div>
                </div>
            </div>

            <!-- Stock Actual -->
            <div class="col-md-4 col-sm-6">
                <div class="module-card">
                    <div class="module-icon-wrapper bg-light-blue"><i class="fas fa-clipboard-list"></i></div>
                    <div class="module-title">Stock Actual</div>
                    <div class="module-desc">Consulta de existencias por bodega.</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('pos_token');
            if(!token) { window.location.href = '/ventas/public/login'; return; }
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';
        });
        function logout() {
            localStorage.removeItem('pos_token');
            localStorage.removeItem('pos_usuario');
            window.location.href = '/ventas/public/login';
        }
    </script>
</body>
</html>
