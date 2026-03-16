<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class TenantController extends Controller {

    private function isGlobalAdmin($tenant_id) {
        return $tenant_id == 1;
    }

    // GET /api/tiendas
    public function index() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();

        if ($this->isGlobalAdmin($tenant_id)) {
            $stmt = $db->query("SELECT * FROM tenants ORDER BY id ASC");
            $this->jsonResponse(["error" => false, "global_admin" => true, "data" => $stmt->fetchAll()]);
        } else {
            $stmt = $db->prepare("SELECT * FROM tenants WHERE id = ?");
            $stmt->execute([$tenant_id]);
            $this->jsonResponse(["error" => false, "global_admin" => false, "data" => $stmt->fetchAll()]);
        }
    }

    // GET /api/tiendas/{id}
    public function show($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();

        // Solo puede ver su propia tienda salvo que sea global admin
        if (!$this->isGlobalAdmin($tenant_id) && $id != $tenant_id) {
            $this->jsonResponse(["error" => true, "message" => "Acceso denegado."], 403);
        }

        $stmt = $db->prepare("SELECT * FROM tenants WHERE id = ?");
        $stmt->execute([$id]);
        $tienda = $stmt->fetch();

        if (!$tienda) $this->jsonResponse(["error" => true, "message" => "Tienda no encontrada."], 404);
        $this->jsonResponse(["error" => false, "data" => $tienda]);
    }

    // POST /api/tiendas/{id}/logo  (subida de logo)
    public function uploadLogo($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];

        if (!$this->isGlobalAdmin($tenant_id) && $id != $tenant_id) {
            $this->jsonResponse(["error" => true, "message" => "No autorizado."], 403);
        }

        if (empty($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(["error" => true, "message" => "No se recibió ningún archivo válido."], 422);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($_FILES['logo']['tmp_name']);
        if (!in_array($mime, $allowed)) {
            $this->jsonResponse(["error" => true, "message" => "Formato de imagen no permitido. Usa JPG, PNG o WebP."], 422);
        }

        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $filename = 'tenant_' . $id . '.' . strtolower($ext);
        $destDir = __DIR__ . '/../../public/uploads/logos/';
        $destPath = $destDir . $filename;

        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $destPath)) {
            $this->jsonResponse(["error" => true, "message" => "No se pudo guardar el archivo."], 500);
        }

        $logoUrl = '/ventas/public/uploads/logos/' . $filename;

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE tenants SET logo_url = ? WHERE id = ?");
        $stmt->execute([$logoUrl, $id]);

        $this->jsonResponse(["error" => false, "message" => "Logo actualizado.", "logo_url" => $logoUrl]);
    }

    // POST /api/tiendas  (solo Super Admin global)
    public function store() {
        $payload = AuthMiddleware::authenticate();
        if (!$this->isGlobalAdmin($payload['tenant_id'])) {
            $this->jsonResponse(["error" => true, "message" => "No autorizado."], 403);
        }

        $data = $this->getPostData();

        // Validaciones mínimas
        if (empty($data['nombre_comercial'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre comercial es requerido."], 422);
        }
        if (empty($data['admin_nombre']) || empty($data['admin_email']) || empty($data['admin_password'])) {
            $this->jsonResponse(["error" => true, "message" => "Debes completar los datos del Administrador de la tienda."], 422);
        }

        $db = (new Database())->getConnection();

        try {
            $db->beginTransaction();

            // 1. Crear el Tenant
            $stmt = $db->prepare(
                "INSERT INTO tenants (nombre_comercial, slogan, razon_social, nit, direccion, telefono, email_contacto, estado)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo')"
            );
            $stmt->execute([
                $data['nombre_comercial'],   $data['slogan'] ?? null,
                $data['razon_social'] ?? null, $data['nit'] ?? null,
                $data['direccion'] ?? null,    $data['telefono'] ?? null,
                $data['email_contacto'] ?? null,
            ]);
            $tenant_id = $db->lastInsertId();

            // 2. Crear una Sede Principal para la tienda
            $stmtSede = $db->prepare("INSERT INTO sedes (tenant_id, nombre, estado) VALUES (?, 'Sede Principal', 'Activa')");
            $stmtSede->execute([$tenant_id]);
            $sede_id = $db->lastInsertId();

            // 3. Crear el Rol Admin con permisos completos para esa tienda
            $permisos = json_encode([
                "inventarios"  => ["leer" => true, "crear" => true, "editar" => true, "eliminar" => true],
                "ventas"       => ["leer" => true, "crear" => true, "editar" => true, "eliminar" => true],
                "cajas"        => ["leer" => true, "crear" => true, "editar" => true, "eliminar" => true],
                "reportes"     => ["leer" => true, "crear" => true, "editar" => true, "eliminar" => true],
                "configuracion"=> ["leer" => true, "crear" => true, "editar" => true, "eliminar" => true],
                "tienda"       => ["leer" => true, "crear" => false, "editar" => true, "eliminar" => false],
                "usuarios"     => ["leer" => true, "crear" => true, "editar" => true, "eliminar" => true],
            ]);
            $stmtRol = $db->prepare("INSERT INTO roles (tenant_id, nombre, permisos_json) VALUES (?, 'Admin', ?)");
            $stmtRol->execute([$tenant_id, $permisos]);
            $rol_id = $db->lastInsertId();

            // 4. Crear el usuario Administrador de la tienda
            $passwordHash = password_hash($data['admin_password'], PASSWORD_DEFAULT);
            $stmtUser = $db->prepare(
                "INSERT INTO usuarios (tenant_id, rol_id, sede_id, nombre, email, password_hash, estado)
                 VALUES (?, ?, ?, ?, ?, ?, 'Activo')"
            );
            $stmtUser->execute([
                $tenant_id, $rol_id, $sede_id,
                $data['admin_nombre'], $data['admin_email'], $passwordHash
            ]);

            $db->commit();

            $this->jsonResponse([
                "error"   => false,
                "message" => "Tienda creada exitosamente con su Administrador.",
                "id"      => $tenant_id,
                "admin"   => ["email" => $data['admin_email'], "password" => $data['admin_password']]
            ], 201);

        } catch (\Exception $e) {
            $db->rollBack();
            $this->jsonResponse(["error" => true, "message" => "Error al crear la tienda: " . $e->getMessage()], 500);
        }
    }

    // PUT /api/tiendas/{id}
    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];

        // Global admin puede editar cualquiera; usuario normal solo la suya
        if (!$this->isGlobalAdmin($tenant_id) && $id != $tenant_id) {
            $this->jsonResponse(["error" => true, "message" => "No autorizado para editar esta tienda."], 403);
        }

        $data = $this->getPostData();
        if (empty($data['nombre_comercial'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre comercial es requerido."], 422);
        }

        $db = (new Database())->getConnection();
        $stmt = $db->prepare(
            "UPDATE tenants SET nombre_comercial = ?, slogan = ?, razon_social = ?, nit = ?,
             direccion = ?, telefono = ?, email_contacto = ?, estado = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['nombre_comercial'], $data['slogan'] ?? null,
            $data['razon_social'] ?? null, $data['nit'] ?? null,
            $data['direccion'] ?? null, $data['telefono'] ?? null,
            $data['email_contacto'] ?? null, $data['estado'] ?? 'Activo',
            $id,
        ]);
        $this->jsonResponse(["error" => false, "message" => "Tienda actualizada correctamente."]);
    }

    // DELETE /api/tiendas/{id}  (solo Super Admin)
    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        if (!$this->isGlobalAdmin($payload['tenant_id'])) {
            $this->jsonResponse(["error" => true, "message" => "Solo el Super Admin puede eliminar tiendas."], 403);
        }
        if ($id == 1) {
            $this->jsonResponse(["error" => true, "message" => "No puedes eliminar la tienda principal del sistema."], 422);
        }
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE tenants SET estado = 'Inactivo' WHERE id = ?");
        $stmt->execute([$id]);
        $this->jsonResponse(["error" => false, "message" => "Tienda desactivada correctamente."]);
    }
}
