# Hoja de Ruta - Sistema POS Multi-tenant & E-commerce

## 1. Visión General del Proyecto
Plataforma integral que combina un Sistema de Punto de Venta (POS) administrativo con una Tienda en Línea (E-commerce) y futura App Móvil. El sistema está diseñado bajo un modelo SaaS (Software as a Service) multi-tenant, permitiendo gestionar múltiples negocios independientes desde una única instalación.

## 2. Stack Tecnológico
*   **Patrón Arquitectónico:** MVC (Modelo-Vista-Controlador).
*   **Backend / API:** PHP (Nativo estructurado).
*   **Base de Datos:** MySQL.
*   **Frontend Web:** HTML, CSS, Bootstrap, SweetAlert2, FontAwesome, Highcharts.
*   **Autenticación y Seguridad:** JWT (JSON Web Tokens) para las sesiones de API (vital para conectar la Web, POS y App Móvil) y OAuth (Google Login) para el e-commerce.

## 3. Arquitectura y Reglas de Negocio Clave

### A. Multi-tenancy (Multi-empresa)
*   **Concepto:** Cada negocio (Ferretería, Almacén, etc.) tendrá un identificador único definido como `tenant_id`.
*   **Aplicación:** Casi todas las tablas operativas de la BD llevarán este campo.
*   **Seguridad:** El backend inyectará este `tenant_id` en el Payload del JWT. En todas las consultas MySQL se agregará como filtro (`WHERE tenant_id = X`) para garantizar separación absoluta de la información.

### B. Control RBAC (Permisos Granulares CRUD)
*   **Concepto:** Permisos definidos a nivel de módulo y acción (Crear, Leer, Actualizar, Eliminar).
*   **Estructura:** Se guardarán como JSON y se validarán en dos capas:
    *   *Frontend:* Ocultando botones o menús a los que no se tenga acceso.
    *   *Backend (API):* Interceptando peticiones no autorizadas y devolviendo 403 Forbidden.

### C. Lógica de Cajas y Sesiones
*   **Cajeros Base:** 
    *   Al hacer login, si no tienen caja abierta hoy, deben abrirla.
    *   Si tienen una caja abierta de días anteriores, se bloquea el acceso al POS y se exige forzosamente el cuadre/cierre de la fecha anterior.
*   **Administradores:** Tienen acceso libre al Dashboard y al momento de entrar al POS, pueden seleccionar libremente en qué "Caja" o "Sede" desean operar transaccionalmente.

## 4. Estructura de Módulos (Backend & Frontend)

### 🛒 E-commerce (Público)
*   [ ] Catálogo de productos.
*   [ ] Categorías, imágenes, precios y características.
*   [ ] Carrito de compras.
*   [ ] Checkout (Integración de pasarelas: Tarjetas, PayPal, Efectivo).
*   [ ] Registro e inicio de sesión de clientes (Email/Password y Google Login).

### ⚙️ POS / Administrativo (Privado)

*   **1. Configuración:**
    *   [x] Datos de la tienda (Empresa, logos, tickets, slogan).
    *   [x] Sedes/Sucursales (Auto-creadas al crear tienda).
    *   [ ] Terceros (Clientes y Proveedores).
    *   [x] Roles y Permisos (RBAC con matriz de permisos JSON).
    *   [x] Usuarios del sistema (CRUD completo: crear, editar, eliminar).

*   **2. Inventarios:**
    *   [x] Categorías de productos (CRUD con imágenes y previsualización).
    *   [x] Items (Productos: precios, SKU, imagen principal, modal XL mejorado).
    *   [ ] Bodegas.
    *   [ ] Entradas y Salidas de inventario.
    *   [ ] Traslados entre bodegas.
    *   *Nota: El inventario cuenta con optimización de carga y esquema de BD validado.*

*   **3. Ventas (POS):**
    *   [ ] Interfaz de Cajero POS.
    *   [ ] Gestión de Promociones (Porcentaje, Montos, etc.).

*   **4. Caja:**
    *   [x] Menú unificado de Caja (Tarjetas de acceso rápido).
    *   [x] Gestión de cajas físicas (CRUD completo con asignación de sedes).
    *   [x] Apertura y Cierre de Sesiones (Lógica diferenciada Admin/Cajero).
    *   [ ] Conceptos de Caja (Maquetado inicial listo).
    *   [x] Registro de Entradas y Salidas extras (Ingresos/Gastos).
    *   [x] Auditoría de movimientos de efectivo.

*   **5. Reportes:**
    *   [ ] Desempeño y ventas por día.
    *   [ ] Rendimiento por Sede.
    *   [ ] Inventario Físico valorizado.
    *   [ ] Gráficos avanzados (Rentabilidades, tendencias) usando Highcharts.

## 5. Próximos Pasos en el Desarrollo
*   [x] Estructurar los directorios del patrón MVC.
*   [x] Diseñar el Script SQL oficial de la Base de Datos.
*   [x] Configurar la conexión a BD y la clase abstracta de Modelos.
*   [x] Implementar el sistema de Seguridad (Middleware y JWT).
*   [x] Desarrollo del Backend (API REST Core).
*   [x] Desarrollo del Frontend Web (Dashboard, Config, Inventarios, Cajas, Sedes).
*   [x] Estandarización de Interfaz (Breadcrumbs, Barra de navegación, Notificaciones).
*   [ ] Implementar el Módulo de Ventas POS (Punto de Venta).
*   [ ] Implementar el Módulo de Ventas POS (Punto de Venta).
*   [ ] Desarrollo del E-commerce frontend.
