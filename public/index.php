<?php
/**
 * Punto de Entrada Principal (Front Controller)
 */

// Cabeceras CORS básica para API (si se conectan desde una App Móvil o un dominio distinto)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Autocarga de clases basada en el namespace 'App\'
spl_autoload_register(function($class) {
    // $class viene como por ejemplo: App\Core\Application
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../App/';
    
    // Si la clase no usa el prefijo 'App\', pasa al siguiente autoloader
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Obtenemos el nombre relativo de la clase
    $relative_class = substr($class, $len);
    
    // Reemplazamos separadores de namespace por separadores de directorio y añadimos .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Application;

$app = new Application();

// ==== REGISTRO DE RUTAS ====

// Rutas Web (Vistas)
$app->router->get('/', [\App\Controllers\WebController::class, 'login']);
$app->router->get('/login', [\App\Controllers\WebController::class, 'login']);
$app->router->get('/dashboard', [\App\Controllers\WebController::class, 'dashboard']);
$app->router->get('/configuracion', [\App\Controllers\WebController::class, 'configuracion']);
$app->router->get('/configuracion/usuarios', [\App\Controllers\WebController::class, 'usuarios']);
$app->router->get('/configuracion/tiendas', [\App\Controllers\WebController::class, 'tiendas']);
$app->router->get('/configuracion/roles', [\App\Controllers\WebController::class, 'roles']);
$app->router->get('/inventario', [\App\Controllers\WebController::class, 'inventario']);
$app->router->get('/inventario/productos', [\App\Controllers\WebController::class, 'productos']);
$app->router->get('/inventario/categorias', [\App\Controllers\WebController::class, 'categorias']);
$app->router->get('/configuracion/sedes', [\App\Controllers\WebController::class, 'sedes']);
$app->router->get('/cajas', [\App\Controllers\WebController::class, 'cajas']);
$app->router->get('/cajas/operacion', [\App\Controllers\WebController::class, 'cajaOperacion']);
$app->router->get('/cajas/gestion', [\App\Controllers\WebController::class, 'cajaGestion']);
$app->router->get('/cajas/conceptos', [\App\Controllers\WebController::class, 'cajaConceptos']);

// Rutas API
$app->router->post('/api/login', [\App\Controllers\AuthController::class, 'login']);
$app->router->get('/api/usuarios', [\App\Controllers\UserController::class, 'getList']);
$app->router->post('/api/usuarios', [\App\Controllers\UserController::class, 'store']);
$app->router->put('/api/usuarios/{id}', [\App\Controllers\UserController::class, 'update']);
$app->router->delete('/api/usuarios/{id}', [\App\Controllers\UserController::class, 'destroy']);
$app->router->get('/api/roles', [\App\Controllers\RoleController::class, 'index']);
$app->router->post('/api/roles', [\App\Controllers\RoleController::class, 'store']);
$app->router->put('/api/roles/{id}', [\App\Controllers\RoleController::class, 'update']);
$app->router->delete('/api/roles/{id}', [\App\Controllers\RoleController::class, 'destroy']);
$app->router->get('/api/sedes', [\App\Controllers\SedeController::class, 'index']);
$app->router->post('/api/sedes', [\App\Controllers\SedeController::class, 'store']);
$app->router->put('/api/sedes/{id}', [\App\Controllers\SedeController::class, 'update']);
$app->router->delete('/api/sedes/{id}', [\App\Controllers\SedeController::class, 'destroy']);
$app->router->get('/api/tiendas', [\App\Controllers\TenantController::class, 'index']);
$app->router->get('/api/tiendas/{id}', [\App\Controllers\TenantController::class, 'show']);
$app->router->post('/api/tiendas', [\App\Controllers\TenantController::class, 'store']);
$app->router->put('/api/tiendas/{id}', [\App\Controllers\TenantController::class, 'update']);
$app->router->delete('/api/tiendas/{id}', [\App\Controllers\TenantController::class, 'destroy']);
$app->router->post('/api/tiendas/{id}/logo', [\App\Controllers\TenantController::class, 'uploadLogo']);
$app->router->get('/api/productos', [\App\Controllers\ProductoController::class, 'index']);
$app->router->get('/api/productos/{id}', [\App\Controllers\ProductoController::class, 'show']);
$app->router->post('/api/productos', [\App\Controllers\ProductoController::class, 'store']);
$app->router->post('/api/productos/{id}', [\App\Controllers\ProductoController::class, 'update']);
$app->router->put('/api/productos/{id}', [\App\Controllers\ProductoController::class, 'update']);
$app->router->delete('/api/productos/{id}', [\App\Controllers\ProductoController::class, 'destroy']);
$app->router->get('/api/categorias', [\App\Controllers\CategoriaController::class, 'index']);
$app->router->post('/api/categorias', [\App\Controllers\CategoriaController::class, 'store']);
$app->router->post('/api/categorias/{id}', [\App\Controllers\CategoriaController::class, 'update']);
$app->router->put('/api/categorias/{id}', [\App\Controllers\CategoriaController::class, 'update']);
$app->router->delete('/api/categorias/{id}', [\App\Controllers\CategoriaController::class, 'destroy']);

// Cajas
$app->router->get('/api/cajas', [\App\Controllers\CajaController::class, 'index']);
$app->router->post('/api/cajas', [\App\Controllers\CajaController::class, 'storeCaja']);
$app->router->put('/api/cajas/{id}', [\App\Controllers\CajaController::class, 'updateCaja']);
$app->router->delete('/api/cajas/{id}', [\App\Controllers\CajaController::class, 'destroyCaja']);
$app->router->get('/api/sesiones-caja', [\App\Controllers\CajaController::class, 'sesiones']);
$app->router->get('/api/sesiones-caja/activa', [\App\Controllers\CajaController::class, 'sesionActiva']);
$app->router->post('/api/sesiones-caja/abrir', [\App\Controllers\CajaController::class, 'abrirSesion']);
$app->router->post('/api/sesiones-caja/{id}/cerrar', [\App\Controllers\CajaController::class, 'cerrarSesion']);
$app->router->get('/api/sesiones-caja/{id}/movimientos', [\App\Controllers\CajaController::class, 'movimientosDeSesion']);
$app->router->post('/api/sesiones-caja/{id}/movimientos', [\App\Controllers\CajaController::class, 'registrarMovimiento']);

// Arrancamos la aplicación
$app->run();
