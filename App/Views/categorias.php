<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - POS Multi-tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/ventas/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-navbar">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-tags me-2"></i> Categorías</h4>
            <span class="text-muted ms-3 d-none d-md-inline-block" style="font-size: 0.9em;">Clasificación de productos para POS y E-commerce</span>
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
                <li class="breadcrumb-item active">Categorías</li>
            </ol>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <h6 class="mb-0 text-muted">Gestiona las categorías que verán tus clientes en la tienda online.</h6>
            <button class="btn btn-primary btn-sm px-3 ms-auto" id="btnNuevo"><i class="fas fa-plus me-1"></i> Nueva Categoría</button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body px-0 py-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted" style="font-size: 0.85rem;">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th style="width: 80px">Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="catBody">
                            <tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i> Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="catId">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="catNombre" placeholder="Ej. Herramientas, Electrodomésticos...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Descripción</label>
                        <textarea class="form-control" id="catDesc" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Imagen</label>
                        <input type="file" class="form-control" id="catImagen" accept="image/*">
                        <div id="imgPreview" class="mt-2 text-center" style="display:none">
                            <img src="" class="img-thumbnail" style="max-height: 100px">
                        </div>
                    </div>
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
        let token = '', modal, cats = [];

        document.addEventListener('DOMContentLoaded', async () => {
            token = localStorage.getItem('pos_token');
            if(!token) { window.location.href = '/ventas/public/login'; return; }
            const user = JSON.parse(localStorage.getItem('pos_usuario') || "{}");
            document.getElementById('userName').textContent = user.nombre || '';
            document.getElementById('userRole').textContent = user.rol || '';
            modal = new bootstrap.Modal(document.getElementById('modalCat'));
            await cargar();
            document.getElementById('btnNuevo').addEventListener('click', () => abrir());
            document.getElementById('btnGuardar').addEventListener('click', guardar);

            document.getElementById('catImagen').addEventListener('change', function() {
                const preview = document.getElementById('imgPreview');
                const img = preview.querySelector('img');
                if (this.files && this.files[0]) {
                    img.src = URL.createObjectURL(this.files[0]);
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            });
        });

        async function api(url, method = 'GET', body = null) {
            const opts = { method, headers: { 'Authorization': 'Bearer ' + token } };
            if(body) {
                if(body instanceof FormData) {
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

        async function cargar() {
            const res = await api('/ventas/public/api/categorias');
            cats = res.data || [];
            const tbody = document.getElementById('catBody');
            if(!cats.length) { tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-5">Sin categorías aún</td></tr>`; return; }
            tbody.innerHTML = '';
            cats.forEach(c => {
                const img = c.imagen_url ? `<img src="${c.imagen_url}" class="rounded" style="width:40px;height:40px;object-fit:cover">` : '<div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width:40px;height:40px"><i class="fas fa-image"></i></div>';
                tbody.innerHTML += `
                    <tr>
                        <td class="ps-4 text-muted">#${c.id}</td>
                        <td>${img}</td>
                        <td class="fw-bold" style="color:#2c3e50">${c.nombre}</td>
                        <td class="text-muted">${c.descripcion || '-'}</td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-light text-primary me-1 rounded-circle" style="width:32px;height:32px" onclick="editar(${c.id})"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger rounded-circle" style="width:32px;height:32px" onclick="eliminar(${c.id},'${c.nombre}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;
            });
        }

        function abrir(c = null) {
            document.getElementById('catId').value = '';
            document.getElementById('catNombre').value = '';
            document.getElementById('catDesc').value = '';
            document.getElementById('catImagen').value = '';
            document.getElementById('imgPreview').style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Nueva Categoría';
            if(c) {
                document.getElementById('modalTitle').textContent = 'Editar Categoría';
                document.getElementById('catId').value = c.id;
                document.getElementById('catNombre').value = c.nombre;
                document.getElementById('catDesc').value = c.descripcion || '';
                if (c.imagen_url) {
                    const preview = document.getElementById('imgPreview');
                    preview.querySelector('img').src = c.imagen_url;
                    preview.style.display = 'block';
                }
            }
            modal.show();
        }

        function editar(id) {
            const c = cats.find(x => x.id == id);
            if(c) abrir(c);
        }

        async function guardar() {
            const id = document.getElementById('catId').value;
            const nombre = document.getElementById('catNombre').value;
            const descripcion = document.getElementById('catDesc').value;
            const imagen = document.getElementById('catImagen').files[0];

            if(!nombre) { Swal.fire('Error', 'El nombre es requerido.', 'warning'); return; }

            const fd = new FormData();
            fd.append('nombre', nombre);
            fd.append('descripcion', descripcion);
            if(imagen) fd.append('imagen', imagen);

            const url = id ? `/ventas/public/api/categorias/${id}` : '/ventas/public/api/categorias';
            const method = 'POST'; // Usamos POST para multipart siempre
            
            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

            const res = await api(url, method, fd);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar';

            if(!res.error) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                modal.hide(); await cargar();
            } else { Swal.fire('Error', res.message, 'error'); }
        }

        async function eliminar(id, nombre) {
            const r = await Swal.fire({ icon: 'warning', title: `¿Eliminar "${nombre}"?`, showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Sí, eliminar' });
            if(!r.isConfirmed) return;
            const res = await api(`/ventas/public/api/categorias/${id}`, 'DELETE');
            if(!res.error) { Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false }); await cargar(); }
        }

        function logout() { localStorage.removeItem('pos_token'); localStorage.removeItem('pos_usuario'); window.location.href = '/ventas/public/login'; }
    </script>
</body>
</html>
