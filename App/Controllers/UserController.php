<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class UserController extends Controller {
    public function getList() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        
        $query = "SELECT u.id, u.nombre, u.email, u.estado, u.rol_id, u.sede_id, r.nombre as rol, s.nombre as sede 
                  FROM usuarios u 
                  LEFT JOIN roles r ON u.rol_id = r.id
                  LEFT JOIN sedes s ON u.sede_id = s.id 
                  WHERE u.tenant_id = :tenant_id
                  ORDER BY u.id DESC";
                  
        $stmt = $db->prepare($query);
        $stmt->execute([':tenant_id' => $tenant_id]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    public function store() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $data = $this->getPostData();

        if (empty($data['nombre']) || empty($data['email']) || empty($data['password']) || empty($data['rol_id'])) {
            $this->jsonResponse(["error" => true, "message" => "Faltan campos obligatorios"], 400);
        }

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO usuarios (tenant_id, rol_id, sede_id, nombre, email, password_hash, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $tenant_id,
            $data['rol_id'],
            $data['sede_id'] ?? null,
            $data['nombre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['estado'] ?? 'Activo'
        ]);

        $this->jsonResponse(["error" => false, "message" => "Usuario creado exitosamente"]);
    }

    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $data = $this->getPostData();

        $db = (new Database())->getConnection();
        
        // Verificar pertenencia al tenant
        $check = $db->prepare("SELECT id FROM usuarios WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $tenant_id]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Usuario no encontrado"], 404);
        }

        $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol_id = ?, sede_id = ?, estado = ? WHERE id = ?";
        $params = [$data['nombre'], $data['email'], $data['rol_id'], $data['sede_id'] ?? null, $data['estado'], $id];

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Actualizar contraseña si se envía
        if (!empty($data['password'])) {
            $stmtPass = $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
            $stmtPass->execute([password_hash($data['password'], PASSWORD_DEFAULT), $id]);
        }

        $this->jsonResponse(["error" => false, "message" => "Usuario actualizado"]);
    }

    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();

        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $tenant_id]);

        if ($stmt->rowCount() > 0) {
            $this->jsonResponse(["error" => false, "message" => "Usuario eliminado"]);
        } else {
            $this->jsonResponse(["error" => true, "message" => "No se pudo eliminar el usuario o no existe"], 404);
        }
    }

    public function getRoles() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT id, nombre FROM roles WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    public function getSedes() {
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT id, nombre FROM sedes WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }
}
