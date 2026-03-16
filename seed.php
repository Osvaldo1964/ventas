<?php
// Script para sembrar datos básicos (Super Admin, Tenant, Sede y Roles)
$host = "localhost";
$db_name = "ventas_pos";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Verificar si ya existe el tenant
    $stmt = $conn->query("SELECT id FROM tenants LIMIT 1");
    if($stmt->fetch()) {
        echo "La base de datos ya tiene datos iniciales. Prueba acceder con admin@tienda.com y clave 123456\n";
        exit;
    }

    $conn->exec("INSERT INTO tenants (nombre_comercial, estado) VALUES ('Ferreteria Antigravity', 'Activo')");
    $tenant_id = $conn->lastInsertId();

    $conn->exec("INSERT INTO sedes (tenant_id, nombre, estado) VALUES ($tenant_id, 'Sede Principal', 'Activa')");
    $sede_id = $conn->lastInsertId();

    $permisos = json_encode([
        "inventarios" => ["leer"=>true, "crear"=>true, "editar"=>true, "eliminar"=>true],
        "ventas" => ["leer"=>true, "crear"=>true, "editar"=>true, "eliminar"=>true],
        "cajas" => ["leer"=>true, "crear"=>true, "editar"=>true, "eliminar"=>true],
        "reportes" => ["leer"=>true, "crear"=>true, "editar"=>true, "eliminar"=>true],
        "configuracion" => ["leer"=>true, "crear"=>true, "editar"=>true, "eliminar"=>true]
    ]);
    $stmt = $conn->prepare("INSERT INTO roles (tenant_id, nombre, permisos_json) VALUES (?, ?, ?)");
    $stmt->execute([$tenant_id, 'Admin', $permisos]);
    $rol_id = $conn->lastInsertId();

    $email = "admin@tienda.com";
    $pass = "123456"; 
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $stmtUsuario = $conn->prepare("INSERT INTO usuarios (tenant_id, rol_id, sede_id, nombre, email, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtUsuario->execute([$tenant_id, $rol_id, $sede_id, 'Super Administrador', $email, $hash]);

    echo "==========================================\n";
    echo "CREACIÓN DE DATOS INICIALES EXITOSA\n";
    echo "==========================================\n";
    echo "Puedes iniciar sesión en el frontend con:\n";
    echo "Email: admin@tienda.com\n";
    echo "Clave: 123456\n";
    echo "==========================================\n";

} catch(PDOException $e) {
    echo "Error al conectar a MySQL o insertar datos: " . $e->getMessage() . "\n";
}
