<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conceptos de Caja - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="top-navbar">
    <div>
        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-tags me-2"></i> Conceptos de Caja</h4>
        <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size:0.9em;">Tipos de ingresos y egresos permitidos</span>
    </div>
    <div class="d-flex align-items-center">
        <i class="far fa-bell text-muted me-4 fs-5" style="cursor: pointer;"></i>
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
            <li class="breadcrumb-item"><a href="/ventas/public/cajas">Caja</a></li>
            <li class="breadcrumb-item active">Conceptos</li>
        </ol>
    </nav>
</div>

<div class="container pb-5">

    <div class="card border-0 shadow-sm mt-4 text-center p-5" style="border-radius:14px">
        <div class="mb-4">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px">
                <i class="fas fa-tools fa-3x text-primary opacity-50"></i>
            </div>
        </div>
        <h3 class="fw-bold text-primary">Módulo en Construcción</h3>
        <p class="text-muted max-w-500 mx-auto">
            Aquí podrás configurar los conceptos de movimientos (ej: Pagos a proveedores, Pago de arriendo, Retiros de socios) que aparecerán en el módulo de Ingresos/Gastos.
        </p>
        <div class="alert alert-primary d-inline-block px-4 py-3 mt-3" style="border-radius:12px;border:none">
            <i class="fas fa-info-circle me-2"></i> Estamos preparando este catálogo para la próxima actualización.
        </div>
        <div class="mt-4">
            <a href="/ventas/public/cajas" class="btn btn-outline-primary px-4 py-2 fw-semibold" style="border-radius:10px">
                <i class="fas fa-arrow-left me-2"></i> Volver al Menú de Caja
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const user = JSON.parse(localStorage.getItem('pos_usuario') || '{}');
        document.getElementById('userName').textContent = user.nombre || 'Administrador';
        document.getElementById('userRole').textContent = user.rol || 'Admin';
    });
    function logout() {
        localStorage.removeItem('pos_token');
        localStorage.removeItem('pos_usuario');
        window.location.href = '/ventas/public/login';
    }
</script>

</body>
</html>
