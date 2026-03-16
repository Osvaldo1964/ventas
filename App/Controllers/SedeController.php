<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class SedeController extends Controller {

    // GET /api/sedes
    public function index() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM sedes WHERE tenant_id = ? ORDER BY id DESC");
        $stmt->execute([$tenant_id]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    // POST /api/sedes
    public function store() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $data = $this->getPostData();

        if (empty($data['nombre'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre de la sede es obligatorio"], 400);
        }

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO sedes (tenant_id, nombre, direccion, telefono, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $tenant_id,
            $data['nombre'],
            $data['direccion'] ?? null,
            $data['telefono'] ?? null,
            $data['estado'] ?? 'Activa'
        ]);

        $this->jsonResponse(["error" => false, "message" => "Sede creada exitosamente", "id" => $db->lastInsertId()]);
    }

    // PUT /api/sedes/{id}
    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $data = $this->getPostData();

        $db = (new Database())->getConnection();
        
        // Verificar pertenencia
        $check = $db->prepare("SELECT id FROM sedes WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $tenant_id]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Sede no encontrada"], 404);
        }

        $stmt = $db->prepare("UPDATE sedes SET nombre = ?, direccion = ?, telefono = ?, estado = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([
            $data['nombre'],
            $data['direccion'] ?? null,
            $data['telefono'] ?? null,
            $data['estado'] ?? 'Activa',
            $id,
            $tenant_id
        ]);

        $this->jsonResponse(["error" => false, "message" => "Sede actualizada"]);
    }

    // DELETE /api/sedes/{id}
    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();

        // Evitar eliminar si tiene registros asociados (ej: usuarios o cajas)
        // Por ahora simple, pero idealmente validar
        $stmt = $db->prepare("DELETE FROM sedes WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $tenant_id]);

        if ($stmt->rowCount() > 0) {
            $this->jsonResponse(["error" => false, "message" => "Sede eliminada"]);
        } else {
            $this->jsonResponse(["error" => true, "message" => "No se pudo eliminar la sede"], 400);
        }
    }
}
