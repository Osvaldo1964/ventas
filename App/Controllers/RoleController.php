<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class RoleController extends Controller {
    
    public function index() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM roles WHERE tenant_id = ? ORDER BY id DESC");
        $stmt->execute([$tenant_id]);
        $roles = $stmt->fetchAll();
        
        // Decodificar JSON para el frontend si es necesario
        foreach($roles as &$role) {
            $role['permisos'] = json_decode($role['permisos_json'], true);
        }
        
        $this->jsonResponse(["error" => false, "data" => $roles]);
    }

    public function store() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $data = $this->getPostData();

        if (empty($data['nombre'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre del rol es obligatorio"], 400);
        }

        $permisos_json = json_encode($data['permisos'] ?? []);

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO roles (tenant_id, nombre, permisos_json) VALUES (?, ?, ?)");
        $stmt->execute([$tenant_id, $data['nombre'], $permisos_json]);

        $this->jsonResponse(["error" => false, "message" => "Rol creado exitosamente"]);
    }

    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $data = $this->getPostData();

        $db = (new Database())->getConnection();
        
        // Verificar pertenencia
        $check = $db->prepare("SELECT id FROM roles WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $tenant_id]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Rol no encontrado"], 404);
        }

        $permisos_json = json_encode($data['permisos'] ?? []);

        $stmt = $db->prepare("UPDATE roles SET nombre = ?, permisos_json = ? WHERE id = ?");
        $stmt->execute([$data['nombre'], $permisos_json, $id]);

        $this->jsonResponse(["error" => false, "message" => "Rol actualizado correctamente"]);
    }

    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();

        // Verificar si hay usuarios usando este rol
        $checkUsers = $db->prepare("SELECT id FROM usuarios WHERE rol_id = ? LIMIT 1");
        $checkUsers->execute([$id]);
        if ($checkUsers->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "No se puede eliminar el rol porque tiene usuarios asociados"], 400);
        }

        $stmt = $db->prepare("DELETE FROM roles WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $tenant_id]);

        if ($stmt->rowCount() > 0) {
            $this->jsonResponse(["error" => false, "message" => "Rol eliminado correctamente"]);
        } else {
            $this->jsonResponse(["error" => true, "message" => "No se pudo eliminar el rol"], 404);
        }
    }
}
