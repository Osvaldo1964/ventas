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
                                <th class="ps-4">Imagen</th>
                                <th>Código</th>
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
                            <tr><td colspan="10" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando productos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear / Editar Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formProducto">
                        <input type="hidden" id="productoId">
                        <div class="row g-4">
                            <!-- Columna Izquierda: Imagen y básica -->
                            <div class="col-lg-4 border-end">
                                <div class="mb-4 text-center">
                                    <label class="form-label text-muted small fw-semibold d-block mb-3">Imagen del Producto</label>
                                    <div id="imgPreviewWrapper" class="mx-auto bg-light rounded d-flex align-items-center justify-content-center border" style="width: 200px; height: 200px; overflow: hidden; position: relative;">
                                        <img id="imgPreview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                        <i id="imgPlaceholder" class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                    <div class="mt-3">
                                        <input type="file" class="form-control form-control-sm" id="productoImagen" accept="image/*">
                                        <small class="text-muted mt-1 d-block">Recomendado: 800x800px</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-semibold">Código de Barras / SKU</label>
                                    <input type="text" class="form-control bg-light border-0" id="codigo" placeholder="Ej. PRD-001">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-semibold">Categoría</label>
                                    <select class="form-select bg-light border-0" id="categoria_id">
                                        <option value="">-- Sin categoría --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Columna Derecha: Detalles y Precios -->
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-muted small fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre" required placeholder="Nombre del artículo...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small fw-semibold">Descripción del Producto</label>
                                        <textarea class="form-control" id="descripcion" rows="3" placeholder="Descripción detallada para el catálogo o e-commerce..."></textarea>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-3" style="font-size: 0.85rem;"><i class="fas fa-coins me-2 text-primary"></i> Costos y Precios</h6>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-semibold">Precio de Costo</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-0">$</span>
                                                        <input type="number" class="form-control border-0" id="precio_costo" min="0" step="0.01" value="0">
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label text-muted small fw-semibold">Precio de Venta <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-0">$</span>
                                                        <input type="number" class="form-control border-0 fw-bold text-primary" id="precio_venta" min="0" step="0.01" required placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-3" style="font-size: 0.85rem;"><i class="fas fa-percent me-2 text-primary"></i> Impuestos y Desc.</h6>
                                                <div class="mb-3">
                                                    <div class="row">
                                                        <div class="col-7">
                                                            <label class="form-label text-muted small fw-semibold">Aplica IVA</label>
                                                            <select class="form-select border-0" id="aplica_iva">
                                                                <option value="0">No</option>
                                                                <option value="1">Sí</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-5">
                                                            <label class="form-label text-muted small fw-semibold">%</label>
                                                            <input type="number" class="form-control border-0" id="iva_porcentaje" min="0" max="100" value="19">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label text-muted small fw-semibold">Permite Descuento</label>
                                                    <select class="form-select border-0" id="permite_descuento">
                                                        <option value="1">Sí</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary px-4 fw-bold" id="btnGuardar"><i class="fas fa-save me-1"></i> Guardar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let token = '';
        let modalProducto;
        let allProductos = [];
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

            document.getElementById('productoImagen').addEventListener('change', function() {
                const preview = document.getElementById('imgPreview');
                const placeholder = document.getElementById('imgPlaceholder');
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    preview.style.display = 'none';
                    placeholder.style.display = 'block';
                }
            });
        });

        async function api(url, method = 'GET', body = null) {
            const opts = { method, headers: { 'Authorization': 'Bearer ' + token } };
            if (body) {
                if (body instanceof FormData) {
                    opts.body = body;
                } else {
                    opts.headers['Content-Type'] = 'application/json';
                    opts.body = JSON.stringify(body);
                }
            }
            const r = await fetch(url, opts);
            if(r.status === 401) logout();
            return r.json();
        }

        async function cargarCategorias() {
            try {
                const res = await api('/ventas/public/api/categorias');
                categorias = res.data || [];
                const sel = document.getElementById('categoria_id');
                categorias.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.nombre}</option>`);
            } catch(e) {}
        }

        async function cargarProductos() {
            const tbody = document.getElementById('productosBody');
            try {
                const res = await api('/ventas/public/api/productos');
                allProductos = res.data || [];
                renderProductos(allProductos);
            } catch(e) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-4">Error de conexión</td></tr>`;
            }
        }

        function renderProductos(lista) {
            const tbody = document.getElementById('productosBody');
            if(!lista.length) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-5">No hay productos registrados</td></tr>`;
                return;
            }
            tbody.innerHTML = '';
            lista.forEach(p => {
                const badge = p.activo == 1 ? 'bg-success' : 'bg-secondary';
                const estado = p.activo == 1 ? 'Activo' : 'Inactivo';
                const img = p.imagen_principal_url ? `<img src="${p.imagen_principal_url}" class="rounded" style="width:40px;height:40px;object-fit:cover">` : '<div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width:40px;height:40px"><i class="fas fa-box small"></i></div>';
                
                tbody.innerHTML += `
                    <tr>
                        <td class="ps-4">${img}</td>
                        <td class="text-muted" style="font-size:0.85em">${p.codigo || '-'}</td>
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
            
            const preview = document.getElementById('imgPreview');
            const placeholder = document.getElementById('imgPlaceholder');
            preview.style.display = 'none';
            placeholder.style.display = 'block';

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

                if (p.imagen_principal_url) {
                    preview.src = p.imagen_principal_url;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
            }
            modalProducto.show();
        }

        async function editar(id) {
            const res = await api(`/ventas/public/api/productos/${id}`);
            if(!res.error) abrirModal(res.data);
        }

        async function guardar() {
            const id = document.getElementById('productoId').value;
            const fd = new FormData();
            
            fd.append('codigo', document.getElementById('codigo').value);
            fd.append('nombre', document.getElementById('nombre').value);
            fd.append('descripcion', document.getElementById('descripcion').value);
            fd.append('categoria_id', document.getElementById('categoria_id').value || null);
            fd.append('precio_costo', document.getElementById('precio_costo').value || 0);
            fd.append('precio_venta', document.getElementById('precio_venta').value);
            fd.append('aplica_iva', document.getElementById('aplica_iva').value);
            fd.append('iva_porcentaje', document.getElementById('iva_porcentaje').value || 0);
            fd.append('permite_descuento', document.getElementById('permite_descuento').value);
            
            const imgFile = document.getElementById('productoImagen').files[0];
            if (imgFile) fd.append('imagen', imgFile);

            if(!document.getElementById('nombre').value || !document.getElementById('precio_venta').value) {
                Swal.fire('Campos requeridos', 'Debes completar nombre y precio de venta.', 'warning');
                return;
            }

            const url = id ? `/ventas/public/api/productos/${id}` : '/ventas/public/api/productos';
            const method = 'POST'; // multipart/form-data requires POST
            
            const btn = document.getElementById('btnGuardar');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

            const res = await api(url, method, fd);
            btn.disabled = false;
            btn.innerHTML = originalText;

            if(!res.error) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                modalProducto.hide();
                await cargarProductos();
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
            const res = await api(`/ventas/public/api/productos/${id}`, 'DELETE');
            if(!res.error) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                await cargarProductos();
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
