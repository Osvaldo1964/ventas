<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja - Menú Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2b3990;
            --accent-color: #00b074;
            --bg-glass: rgba(255, 255, 255, 0.9);
        }
        
        body {
            background: #f8fafc;
        }

        .menu-header {
            text-align: center;
            padding: 40px 0 20px;
        }

        .menu-header h1 {
            color: var(--primary-color);
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 5px;
        }

        .menu-header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .card-menu {
            border: none;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            height: 100%;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border-left: 4px solid transparent;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .card-menu:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-left-color: var(--accent-color);
        }

        .icon-wrapper {
            width: 70px;
            height: 70px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #64748b;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .card-menu:hover .icon-wrapper {
            background: rgba(0, 176, 116, 0.1);
            color: var(--accent-color);
            transform: scale(1.1);
        }

        .card-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.25rem;
            margin-bottom: 0;
            text-align: center;
        }

        .btn-back {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 8px 20px;
            border-radius: 10px;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            margin-bottom: 30px;
        }

        .btn-back:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

    </style>
</head>
<body>

<div class="top-navbar">
    <div>
        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-cash-register me-2"></i> Caja</h4>
        <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size:0.9em;">Apertura, cierre y movimientos de efectivo.</span>
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
            <li class="breadcrumb-item active">Caja</li>
        </ol>
    </nav>
</div>

<div class="container pb-5">

    <div class="row g-4 justify-content-center">
        <!-- Apertura / Cierre -->
        <div class="col-md-4 col-sm-6">
            <div class="card-menu" onclick="window.location.href='/ventas/public/cajas/operacion'">
                <div class="icon-wrapper">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3 class="card-title">Apertura / Cierre</h3>
            </div>
        </div>

        <!-- Ingresos / Gastos -->
        <div class="col-md-4 col-sm-6">
            <div class="card-menu" onclick="window.location.href='/ventas/public/cajas/operacion'">
                <div class="icon-wrapper">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3 class="card-title">Ingresos / Gastos</h3>
            </div>
        </div>

        <!-- Auditoría -->
        <div class="col-md-4 col-sm-6">
            <div class="card-menu" onclick="window.location.href='/ventas/public/cajas/operacion'">
                <div class="icon-wrapper">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">Auditoría</h3>
            </div>
        </div>

        <!-- Gestión de Cajas -->
        <div class="col-md-4 col-sm-6">
            <div class="card-menu" onclick="window.location.href='/ventas/public/cajas/gestion'">
                <div class="icon-wrapper">
                    <i class="fas fa-desktop"></i>
                </div>
                <h3 class="card-title">Gestión de Cajas</h3>
            </div>
        </div>

        <!-- Conceptos Caja -->
        <div class="col-md-4 col-sm-6">
            <div class="card-menu" onclick="window.location.href='/ventas/public/cajas/conceptos'">
                <div class="icon-wrapper">
                    <i class="fas fa-tag"></i>
                </div>
                <h3 class="card-title">Conceptos Caja</h3>
            </div>
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
