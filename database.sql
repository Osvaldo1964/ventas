CREATE DATABASE IF NOT EXISTS ventas_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ventas_pos;

-- --------------------------------------------------------
-- TABLAS GLOBALES (SaaS)
-- --------------------------------------------------------

CREATE TABLE tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comercial VARCHAR(150) NOT NULL,
    dominio_subdominio VARCHAR(100) UNIQUE NULL,
    estado ENUM('Activo', 'Inactivo', 'Suspendido') DEFAULT 'Activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- MODULO: CONFIGURACION
-- --------------------------------------------------------

CREATE TABLE store_settings (
    tenant_id INT PRIMARY KEY,
    razon_social VARCHAR(200) NOT NULL,
    nit VARCHAR(50) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(100),
    logo_url VARCHAR(255),
    moneda VARCHAR(10) DEFAULT 'COP',
    zona_horaria VARCHAR(50) DEFAULT 'America/Bogota',
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(50),
    estado ENUM('Activa', 'Inactiva') DEFAULT 'Activa',
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    permisos_json JSON COMMENT 'Estructura RBAC CRUD por modulo',
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    rol_id INT NOT NULL,
    sede_id INT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);

CREATE TABLE terceros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    tipo_tercero ENUM('Cliente', 'Proveedor', 'Ambos') DEFAULT 'Cliente',
    tipo_documento VARCHAR(20),
    numero_documento VARCHAR(50),
    nombre_completo VARCHAR(200) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(50),
    direccion VARCHAR(255),
    google_id VARCHAR(255) NULL COMMENT 'Para Oauth del E-commerce',
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- MODULO: INVENTARIOS
-- --------------------------------------------------------

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen_url VARCHAR(255),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    categoria_id INT,
    codigo_barras VARCHAR(100),
    sku VARCHAR(100),
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio_compra DECIMAL(15,2) DEFAULT 0.00,
    precio_venta DECIMAL(15,2) NOT NULL,
    impuesto_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    mostrar_en_ecommerce BOOLEAN DEFAULT TRUE,
    imagen_principal_url VARCHAR(255),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE bodegas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    sede_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(255),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);

CREATE TABLE inventario_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    producto_id INT NOT NULL,
    bodega_id INT NOT NULL,
    cantidad DECIMAL(15,2) DEFAULT 0.00,
    stock_minimo DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (bodega_id) REFERENCES bodegas(id),
    UNIQUE KEY (producto_id, bodega_id) -- Un producto solo tiene 1 registro de stock por bodega
);

CREATE TABLE movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    tipo_movimiento ENUM('Entrada_Compra', 'Traslado_Salida', 'Traslado_Entrada', 'Ajuste_Ingreso', 'Ajuste_Egreso') NOT NULL,
    bodega_origen_id INT NULL,
    bodega_destino_id INT NULL,
    usuario_id INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacion TEXT,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (bodega_origen_id) REFERENCES bodegas(id),
    FOREIGN KEY (bodega_destino_id) REFERENCES bodegas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE mov_inventario_detalle (
    movimiento_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad DECIMAL(15,2) NOT NULL,
    costo_unitario DECIMAL(15,2) DEFAULT 0.00,
    PRIMARY KEY (movimiento_id, producto_id),
    FOREIGN KEY (movimiento_id) REFERENCES movimientos_inventario(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- --------------------------------------------------------
-- MODULO: CAJAS
-- --------------------------------------------------------

CREATE TABLE cajas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    sede_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL COMMENT 'Ej: Caja 1, Caja Principal',
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);

CREATE TABLE sesiones_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    caja_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado ENUM('Abierta', 'Cerrada') DEFAULT 'Abierta',
    fecha_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME NULL,
    base_apertura DECIMAL(15,2) DEFAULT 0.00,
    total_ingresos DECIMAL(15,2) DEFAULT 0.00,
    total_gastos DECIMAL(15,2) DEFAULT 0.00,
    total_declarado DECIMAL(15,2) NULL COMMENT 'Lo contado por el cajero al cerrar',
    diferencia DECIMAL(15,2) NULL COMMENT 'Sobrante o Faltante',
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (caja_id) REFERENCES cajas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE conceptos_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    tipo ENUM('Ingreso', 'Gasto') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE movimientos_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    sesion_caja_id INT NOT NULL,
    concepto_id INT NULL COMMENT 'Null si es venta automatica',
    tipo ENUM('Ingreso', 'Gasto') NOT NULL,
    monto DECIMAL(15,2) NOT NULL,
    referencia_venta_id INT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacion TEXT,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (sesion_caja_id) REFERENCES sesiones_caja(id),
    FOREIGN KEY (concepto_id) REFERENCES conceptos_caja(id)
);

-- --------------------------------------------------------
-- MODULO: VENTAS
-- --------------------------------------------------------

CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    nombre VARCHAR(150),
    tipo ENUM('Porcentaje', 'Monto_Fijo') NOT NULL,
    valor DECIMAL(15,2) NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    sede_id INT NOT NULL,
    usuario_id INT NULL COMMENT 'Null si origen es Ecommerce',
    cliente_id INT NOT NULL COMMENT 'FK Terceros',
    sesion_caja_id INT NULL COMMENT 'Null si origen es Ecommerce',
    origen_venta ENUM('POS', 'ECOMMERCE') NOT NULL,
    estado ENUM('Completada', 'Pendiente_Pago', 'Cancelada') DEFAULT 'Completada',
    subtotal DECIMAL(15,2) NOT NULL,
    impuestos DECIMAL(15,2) DEFAULT 0.00,
    descuento_total DECIMAL(15,2) DEFAULT 0.00,
    total DECIMAL(15,2) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL COMMENT 'Efectivo, Tarjeta, Transferencia, PayPal',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (sede_id) REFERENCES sedes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (cliente_id) REFERENCES terceros(id),
    FOREIGN KEY (sesion_caja_id) REFERENCES sesiones_caja(id)
);

CREATE TABLE ventas_detalle (
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad DECIMAL(15,2) NOT NULL,
    precio_unitario DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (venta_id, producto_id),
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);
