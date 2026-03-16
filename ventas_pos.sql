-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-03-2026 a las 19:53:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ventas_pos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bodegas`
--

CREATE TABLE `bodegas` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL COMMENT 'Ej: Caja 1, Caja Principal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `tenant_id`, `sede_id`, `nombre`) VALUES
(1, 2, 2, 'Caja Principal'),
(2, 2, 3, 'Caja Centro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `tenant_id`, `nombre`, `descripcion`, `imagen_url`) VALUES
(1, 2, 'HERRAMIENTAS', 'HERRAMIENTAS DE CONSTRUCCION', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conceptos_caja`
--

CREATE TABLE `conceptos_caja` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `tipo` enum('Ingreso','Gasto') NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_stock`
--

CREATE TABLE `inventario_stock` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `bodega_id` int(11) NOT NULL,
  `cantidad` decimal(15,2) DEFAULT 0.00,
  `stock_minimo` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `sesion_caja_id` int(11) NOT NULL,
  `concepto_id` int(11) DEFAULT NULL COMMENT 'Null si es venta automatica',
  `tipo` enum('Ingreso','Gasto') NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `referencia_venta_id` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `tipo_movimiento` enum('Entrada_Compra','Traslado_Salida','Traslado_Entrada','Ajuste_Ingreso','Ajuste_Egreso') NOT NULL,
  `bodega_origen_id` int(11) DEFAULT NULL,
  `bodega_destino_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_inventario_detalle`
--

CREATE TABLE `mov_inventario_detalle` (
  `movimiento_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(15,2) NOT NULL,
  `costo_unitario` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_costo` decimal(15,2) DEFAULT 0.00,
  `precio_compra` decimal(15,2) DEFAULT 0.00,
  `precio_venta` decimal(15,2) NOT NULL,
  `permite_descuento` tinyint(1) DEFAULT 1,
  `aplica_iva` tinyint(1) DEFAULT 0,
  `iva_porcentaje` decimal(5,2) DEFAULT 0.00,
  `activo` tinyint(1) DEFAULT 1,
  `impuesto_porcentaje` decimal(5,2) DEFAULT 0.00,
  `mostrar_en_ecommerce` tinyint(1) DEFAULT 1,
  `imagen_principal_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `tenant_id`, `categoria_id`, `codigo`, `codigo_barras`, `sku`, `nombre`, `descripcion`, `precio_costo`, `precio_compra`, `precio_venta`, `permite_descuento`, `aplica_iva`, `iva_porcentaje`, `activo`, `impuesto_porcentaje`, `mostrar_en_ecommerce`, `imagen_principal_url`) VALUES
(1, 2, 1, '', NULL, NULL, 'PINTURA ROJA', '', 960.00, 0.00, 1500.00, 1, 1, 19.00, 1, 0.00, 1, '/ventas/public/uploads/productos/69b850a35fabc.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `tipo` enum('Porcentaje','Monto_Fijo') NOT NULL,
  `valor` decimal(15,2) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `permisos_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Estructura RBAC CRUD por modulo' CHECK (json_valid(`permisos_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `tenant_id`, `nombre`, `permisos_json`) VALUES
(1, 1, 'Admin', '{\"dashboard\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"tiendas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"usuarios\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"roles\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"categorias\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"productos\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"cajas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"ventas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"reportes\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true}}'),
(2, 2, 'Admin', '{\"inventarios\":{\"leer\":true,\"crear\":true,\"editar\":true,\"eliminar\":true},\"ventas\":{\"leer\":true,\"crear\":true,\"editar\":true,\"eliminar\":true},\"cajas\":{\"leer\":true,\"crear\":true,\"editar\":true,\"eliminar\":true},\"reportes\":{\"leer\":true,\"crear\":true,\"editar\":true,\"eliminar\":true},\"configuracion\":{\"leer\":true,\"crear\":true,\"editar\":true,\"eliminar\":true},\"tienda\":{\"leer\":true,\"crear\":false,\"editar\":true,\"eliminar\":false},\"usuarios\":{\"leer\":true,\"crear\":true,\"editar\":true,\"eliminar\":true}}'),
(3, 1, 'Supervisor de Caja', '{\"dashboard\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"tiendas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"usuarios\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"roles\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"categorias\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"productos\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"cajas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"ventas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"reportes\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true}}'),
(4, 1, 'Cajero', '{\"dashboard\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"tiendas\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"usuarios\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"roles\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"categorias\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"productos\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"cajas\":{\"v\":true,\"c\":false,\"e\":true,\"d\":false},\"ventas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"reportes\":{\"v\":true,\"c\":true,\"e\":true,\"d\":false}}'),
(5, 2, 'Cajeros', '{\"dashboard\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"tiendas\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"usuarios\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"roles\":{\"v\":false,\"c\":false,\"e\":false,\"d\":false},\"categorias\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"productos\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"cajas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"ventas\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true},\"reportes\":{\"v\":true,\"c\":true,\"e\":true,\"d\":true}}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `estado` enum('Activa','Inactiva') DEFAULT 'Activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id`, `tenant_id`, `nombre`, `direccion`, `telefono`, `estado`) VALUES
(1, 1, 'Sede Principal', NULL, NULL, 'Activa'),
(2, 2, 'Sede Principal', NULL, NULL, 'Activa'),
(3, 2, 'Sucursal Centro', 'Calle 19', '300200300', 'Activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_caja`
--

CREATE TABLE `sesiones_caja` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('Abierta','Cerrada') DEFAULT 'Abierta',
  `fecha_apertura` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `base_apertura` decimal(15,2) DEFAULT 0.00,
  `total_ingresos` decimal(15,2) DEFAULT 0.00,
  `total_gastos` decimal(15,2) DEFAULT 0.00,
  `total_declarado` decimal(15,2) DEFAULT NULL COMMENT 'Lo contado por el cajero al cerrar',
  `diferencia` decimal(15,2) DEFAULT NULL COMMENT 'Sobrante o Faltante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sesiones_caja`
--

INSERT INTO `sesiones_caja` (`id`, `tenant_id`, `caja_id`, `usuario_id`, `estado`, `fecha_apertura`, `fecha_cierre`, `base_apertura`, `total_ingresos`, `total_gastos`, `total_declarado`, `diferencia`) VALUES
(1, 2, 1, 3, 'Abierta', '2026-03-16 12:22:15', NULL, 5000.00, 0.00, 0.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_settings`
--

CREATE TABLE `store_settings` (
  `tenant_id` int(11) NOT NULL,
  `razon_social` varchar(200) NOT NULL,
  `nit` varchar(50) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `moneda` varchar(10) DEFAULT 'COP',
  `zona_horaria` varchar(50) DEFAULT 'America/Bogota'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `nombre_comercial` varchar(150) NOT NULL,
  `slogan` varchar(200) DEFAULT NULL,
  `razon_social` varchar(100) DEFAULT NULL,
  `nit` varchar(50) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `dominio_subdominio` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','Suspendido') DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tenants`
--

INSERT INTO `tenants` (`id`, `nombre_comercial`, `slogan`, `razon_social`, `nit`, `direccion`, `telefono`, `email_contacto`, `logo_url`, `dominio_subdominio`, `estado`, `fecha_registro`) VALUES
(1, 'Ferreteria Antigravity', 'Tu tecnología al alcance', '', '', '', '', '', '/ventas/public/uploads/logos/tenant_1.png', NULL, 'Activo', '2026-03-16 13:09:52'),
(2, 'Ferreteria Antigravity trr', 'Lo mejor', 'prueba', '900.900.900-1', 'CRA 6 N 29-32', '3126338367', 'kk@correo.com', '/ventas/public/uploads/logos/tenant_2.jpeg', NULL, 'Activo', '2026-03-16 14:27:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terceros`
--

CREATE TABLE `terceros` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `tipo_tercero` enum('Cliente','Proveedor','Ambos') DEFAULT 'Cliente',
  `tipo_documento` varchar(20) DEFAULT NULL,
  `numero_documento` varchar(50) DEFAULT NULL,
  `nombre_completo` varchar(200) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL COMMENT 'Para Oauth del E-commerce'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `sede_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `tenant_id`, `rol_id`, `sede_id`, `nombre`, `email`, `password_hash`, `estado`, `fecha_creacion`) VALUES
(1, 1, 1, 1, 'Super Administrador', 'admin@tienda.com', '$2y$10$G8wo0R94lUyBY7qteYhGDu9DDfuBO3I0o2h8SMgW0.rFZgSnffrp.', 'Activo', '2026-03-16 13:09:52'),
(2, 2, 2, 2, 'Juan Perez', 'admin_f@tienda.com', '$2y$10$Ds.BnbtE19MerbPUtu90YenWc9Rd36UVV2DtDA1GGiaweUdz3SiF2', 'Activo', '2026-03-16 14:27:32'),
(3, 2, 5, 2, 'Carlos Diaz', 'carlos@tiendaf.com', '$2y$10$udSUB3L5faIJDYN39cJiouItN80n9VoDMdfUs9p8YoDscFknN520K', 'Activo', '2026-03-16 16:32:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'Null si origen es Ecommerce',
  `cliente_id` int(11) NOT NULL COMMENT 'FK Terceros',
  `sesion_caja_id` int(11) DEFAULT NULL COMMENT 'Null si origen es Ecommerce',
  `origen_venta` enum('POS','ECOMMERCE') NOT NULL,
  `estado` enum('Completada','Pendiente_Pago','Cancelada') DEFAULT 'Completada',
  `subtotal` decimal(15,2) NOT NULL,
  `impuestos` decimal(15,2) DEFAULT 0.00,
  `descuento_total` decimal(15,2) DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL COMMENT 'Efectivo, Tarjeta, Transferencia, PayPal',
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_detalle`
--

CREATE TABLE `ventas_detalle` (
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(15,2) NOT NULL,
  `precio_unitario` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bodegas`
--
ALTER TABLE `bodegas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `sede_id` (`sede_id`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `sede_id` (`sede_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indices de la tabla `conceptos_caja`
--
ALTER TABLE `conceptos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indices de la tabla `inventario_stock`
--
ALTER TABLE `inventario_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_id` (`producto_id`,`bodega_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `bodega_id` (`bodega_id`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `sesion_caja_id` (`sesion_caja_id`),
  ADD KEY `concepto_id` (`concepto_id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `bodega_origen_id` (`bodega_origen_id`),
  ADD KEY `bodega_destino_id` (`bodega_destino_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `mov_inventario_detalle`
--
ALTER TABLE `mov_inventario_detalle`
  ADD PRIMARY KEY (`movimiento_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indices de la tabla `sesiones_caja`
--
ALTER TABLE `sesiones_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `caja_id` (`caja_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `store_settings`
--
ALTER TABLE `store_settings`
  ADD PRIMARY KEY (`tenant_id`);

--
-- Indices de la tabla `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dominio_subdominio` (`dominio_subdominio`);

--
-- Indices de la tabla `terceros`
--
ALTER TABLE `terceros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `sede_id` (`sede_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `sesion_caja_id` (`sesion_caja_id`);

--
-- Indices de la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD PRIMARY KEY (`venta_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bodegas`
--
ALTER TABLE `bodegas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `conceptos_caja`
--
ALTER TABLE `conceptos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_stock`
--
ALTER TABLE `inventario_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sesiones_caja`
--
ALTER TABLE `sesiones_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `terceros`
--
ALTER TABLE `terceros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bodegas`
--
ALTER TABLE `bodegas`
  ADD CONSTRAINT `bodegas_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bodegas_ibfk_2` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`);

--
-- Filtros para la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD CONSTRAINT `cajas_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cajas_ibfk_2` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`);

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `conceptos_caja`
--
ALTER TABLE `conceptos_caja`
  ADD CONSTRAINT `conceptos_caja_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inventario_stock`
--
ALTER TABLE `inventario_stock`
  ADD CONSTRAINT `inventario_stock_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventario_stock_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `inventario_stock_ibfk_3` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas` (`id`);

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_caja_ibfk_2` FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja` (`id`),
  ADD CONSTRAINT `movimientos_caja_ibfk_3` FOREIGN KEY (`concepto_id`) REFERENCES `conceptos_caja` (`id`);

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`bodega_origen_id`) REFERENCES `bodegas` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_3` FOREIGN KEY (`bodega_destino_id`) REFERENCES `bodegas` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_4` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `mov_inventario_detalle`
--
ALTER TABLE `mov_inventario_detalle`
  ADD CONSTRAINT `mov_inventario_detalle_ibfk_1` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos_inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mov_inventario_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD CONSTRAINT `promociones_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD CONSTRAINT `sedes_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones_caja`
--
ALTER TABLE `sesiones_caja`
  ADD CONSTRAINT `sesiones_caja_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sesiones_caja_ibfk_2` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `sesiones_caja_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `store_settings`
--
ALTER TABLE `store_settings`
  ADD CONSTRAINT `store_settings_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `terceros`
--
ALTER TABLE `terceros`
  ADD CONSTRAINT `terceros_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_4` FOREIGN KEY (`cliente_id`) REFERENCES `terceros` (`id`),
  ADD CONSTRAINT `ventas_ibfk_5` FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja` (`id`);

--
-- Filtros para la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD CONSTRAINT `ventas_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
