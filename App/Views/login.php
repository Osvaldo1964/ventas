<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <div class="glass-card p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold" style="color: var(--primary-color);">Sistema POS</h3>
                            <p class="text-muted small">Ingresa tus credenciales para operar</p>
                        </div>
                        <form id="loginForm">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" required placeholder="admin@tienda.com">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-semibold">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password" required placeholder="••••••••">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 border-0 shadow-sm" id="btnLogin">
                                Iniciar Sesión <i class="fas fa-arrow-right ms-2 fs-6"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/ventas/public/js/app.js"></script>
</body>
</html>
