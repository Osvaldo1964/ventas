<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-boxes me-2"></i> Productos</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Catálogo de artículos</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold text-primary" id="userName"></div>
                <div class="text-muted" style="font-size: 0.8em;" id="userRole"></div>
            </div>
            <a href="#" class="text-danger bg-light py-2 px-3 rounded" style="font-size: 0.8em; font-weight: 600;" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <div class="breadcrumb-nav">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/ventas/public/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/ventas/public/inventario">Inventarios</a></li>
                <li class="breadcrumb-item active">Productos</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <!-- Filtros / Buscador -->
        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <input type="text" class="form-control" id="searchInput" placeholder="🔍  Buscar por nombre o código..." style="max-width: 320px;">
            <button class="btn btn-primary btn-sm px-3 ms-auto" id="btnNuevo"><i class="fas fa-plus me-1"></i> Nuevo Producto</button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body px-0 py-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="productosTable">
                        <thead class="table-light text-muted" style="font-size: 0.85rem;">
                            <tr>
                                <th class="ps-4">Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Precio Venta</th>
                                <th>IVA</th>
                                <th>Estado</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productosBody">
                            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando productos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear / Editar Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formProducto">
                        <input type="hidden" id="productoId">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Código</label>
                                <input type="text" class="form-control" id="codigo" placeholder="Ej. PRD-001">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-muted small fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" required placeholder="Nombre del artículo...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-semibold">Descripción</label>
                                <textarea class="form-control" id="descripcion" rows="2" placeholder="Descripción breve (se muestra en e-commerce)..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Categoría</label>
                                <select class="form-select" id="categoria_id">
                                    <option value="">-- Sin categoría --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Precio Costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_costo" min="0" step="0.01" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Precio Venta <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_venta" min="0" step="0.01" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Aplica IVA</label>
                                <select class="form-select" id="aplica_iva">
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">% IVA</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="iva_porcentaje" min="0" max="100" value="19">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Permite Descuento</label>
                                <select class="form-select" id="permite_descuento">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary px-4" id="btnGuardar"><i class="fas fa-save me-1"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let token = '';
        let modalProducto;
        let categorias = [];

        document.addEventListener('DOMContentLoaded', async () => {
            token = localStorage.getItem('pos_token');
            if(!token) { window.location.href = '/ventas/public/login'; return; }
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';

            modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));

            await cargarCategorias();
            await cargarProductos();

            document.getElementById('btnNuevo').addEventListener('click', () => abrirModal());
            document.getElementById('btnGuardar').addEventListener('click', guardar);

            document.getElementById('searchInput').addEventListener('input', filtrar);
        });

        async function apiGet(url) {
            const r = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
            if(r.status === 401) logout();
            return r.json();
        }
        async function apiPost(url, data, method = 'POST') {
            const r = await fetch(url, {
                method, headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if(r.status === 401) logout();
            return r.json();
        }

        async function cargarCategorias() {
            try {
                const res = await apiGet('/ventas/public/api/categorias');
                categorias = res.data || [];
                const sel = document.getElementById('categoria_id');
                categorias.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.nombre}</option>`);
            } catch(e) {}
        }

        async function cargarProductos() {
            const tbody = document.getElementById('productosBody');
            try {
                const res = await apiGet('/ventas/public/api/productos');
                if(res.error || !res.data.length) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-5">No hay productos registrados</td></tr>`;
                    return;
                }
                renderProductos(res.data);
            } catch(e) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error de conexión</td></tr>`;
            }
        }

        let allProductos = [];
        async function cargarProductosConData() {
            const res = await apiGet('/ventas/public/api/productos');
            allProductos = res.data || [];
            renderProductos(allProductos);
        }
        document.addEventListener('DOMContentLoaded', cargarProductosConData);

        function renderProductos(lista) {
            const tbody = document.getElementById('productosBody');
            if(!lista.length) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-5">No hay productos registrados</td></tr>`;
                return;
            }
            tbody.innerHTML = '';
            lista.forEach(p => {
                const badge = p.activo == 1 ? 'bg-success' : 'bg-secondary';
                const estado = p.activo == 1 ? 'Activo' : 'Inactivo';
                tbody.innerHTML += `
                    <tr data-nombre="${(p.nombre||'').toLowerCase()}" data-codigo="${(p.codigo||'').toLowerCase()}">
                        <td class="ps-4 text-muted" style="font-size:0.85em">${p.codigo || '-'}</td>
                        <td class="fw-bold" style="color:#2c3e50">${p.nombre}</td>
                        <td class="text-muted" style="font-size:0.9em">${p.categoria || '-'}</td>
                        <td class="text-end text-muted" style="font-size:0.9em">$${parseFloat(p.precio_costo||0).toLocaleString('es-CO')}</td>
                        <td class="text-end fw-bold text-primary">$${parseFloat(p.precio_venta).toLocaleString('es-CO')}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">${p.aplica_iva == 1 ? p.iva_porcentaje+'%' : 'No'}</span></td>
                        <td><span class="badge ${badge} bg-opacity-75">${estado}</span></td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" style="width:32px;height:32px" onclick="editar(${p.id})" title="Editar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger rounded-circle" style="width:32px;height:32px" onclick="eliminar(${p.id},'${p.nombre}')" title="Desactivar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;
            });
        }

        function filtrar() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const filtrados = allProductos.filter(p =>
                (p.nombre || '').toLowerCase().includes(q) || (p.codigo || '').toLowerCase().includes(q)
            );
            renderProductos(filtrados);
        }

        function abrirModal(p = null) {
            document.getElementById('formProducto').reset();
            document.getElementById('productoId').value = '';
            document.getElementById('modalTitle').textContent = 'Nuevo Producto';
            if(p) {
                document.getElementById('modalTitle').textContent = 'Editar Producto';
                document.getElementById('productoId').value = p.id;
                document.getElementById('codigo').value = p.codigo || '';
                document.getElementById('nombre').value = p.nombre;
                document.getElementById('descripcion').value = p.descripcion || '';
                document.getElementById('categoria_id').value = p.categoria_id || '';
                document.getElementById('precio_costo').value = p.precio_costo || 0;
                document.getElementById('precio_venta').value = p.precio_venta;
                document.getElementById('aplica_iva').value = p.aplica_iva;
                document.getElementById('iva_porcentaje').value = p.iva_porcentaje;
                document.getElementById('permite_descuento').value = p.permite_descuento;
            }
            modalProducto.show();
        }

        async function editar(id) {
            const res = await apiGet(`/ventas/public/api/productos/${id}`);
            if(!res.error) abrirModal(res.data);
        }

        async function guardar() {
            const id = document.getElementById('productoId').value;
            const body = {
                codigo: document.getElementById('codigo').value,
                nombre: document.getElementById('nombre').value,
                descripcion: document.getElementById('descripcion').value,
                categoria_id: document.getElementById('categoria_id').value || null,
                precio_costo: parseFloat(document.getElementById('precio_costo').value) || 0,
                precio_venta: parseFloat(document.getElementById('precio_venta').value),
                aplica_iva: document.getElementById('aplica_iva').value,
                iva_porcentaje: parseFloat(document.getElementById('iva_porcentaje').value) || 0,
                permite_descuento: document.getElementById('permite_descuento').value,
            };
            if(!body.nombre || !body.precio_venta) {
                Swal.fire('Campos requeridos', 'Debes completar nombre y precio de venta.', 'warning');
                return;
            }
            const url = id ? `/ventas/public/api/productos/${id}` : '/ventas/public/api/productos';
            const method = id ? 'PUT' : 'POST';
            const res = await apiPost(url, body, method);
            if(!res.error) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                modalProducto.hide();
                await cargarProductosConData();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }

        async function eliminar(id, nombre) {
            const confirm = await Swal.fire({
                icon: 'warning',
                title: '¿Desactivar producto?',
                text: `"${nombre}" quedará inactivo y no aparecerá en ventas.`,
                showCancelButton: true,
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
            });
            if(!confirm.isConfirmed) return;
            const res = await apiPost(`/ventas/public/api/productos/${id}`, {}, 'DELETE');
            if(!res.error) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                await cargarProductosConData();
            }
        }

        function logout() {
            localStorage.removeItem('pos_token');
            localStorage.removeItem('pos_usuario');
            window.location.href = '/ventas/public/login';
        }
    </script>
</body>
</html>
