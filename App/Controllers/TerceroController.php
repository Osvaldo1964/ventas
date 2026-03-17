<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class TerceroController extends Controller {

    public function index() {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM terceros WHERE tenant_id = :tid ORDER BY nombre_completo ASC");
        $stmt->execute([':tid' => $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    public function store() {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();

        if (empty($data['nombre_completo']) || empty($data['tipo_tercero'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre y el tipo de tercero son requeridos."], 422);
        }

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO terceros (tenant_id, tipo_tercero, tipo_documento, numero_documento, nombre_completo, email, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $tipo_tercero = $data['tipo_tercero'] ?? 'Cliente';
        $tipo_documento = $data['tipo_documento'] ?? null;
        $numero_documento = $data['numero_documento'] ?? null;
        $nombre_completo = mb_strtoupper($data['nombre_completo'], 'UTF-8');
        $email = isset($data['email']) ? mb_strtolower($data['email'], 'UTF-8') : null;
        $telefono = $data['telefono'] ?? null;
        $direccion = $data['direccion'] ?? null;

        $stmt->execute([
            $payload['tenant_id'], 
            $tipo_tercero, 
            $tipo_documento, 
            $numero_documento, 
            $nombre_completo, 
            $email, 
            $telefono, 
            $direccion
        ]);
        
        $this->jsonResponse(["error" => false, "message" => "Tercero creado exitosamente.", "id" => $db->lastInsertId()], 201);
    }

    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();
        $db = (new Database())->getConnection();

        // Verificar pertenencia
        $check = $db->prepare("SELECT id FROM terceros WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $payload['tenant_id']]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Tercero no encontrado."], 404);
        }

        if (empty($data['nombre_completo']) || empty($data['tipo_tercero'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre y el tipo de tercero son requeridos."], 422);
        }

        $stmt = $db->prepare("UPDATE terceros SET tipo_tercero = ?, tipo_documento = ?, numero_documento = ?, nombre_completo = ?, email = ?, telefono = ?, direccion = ? WHERE id = ? AND tenant_id = ?");
        
        $tipo_tercero = $data['tipo_tercero'];
        $tipo_documento = $data['tipo_documento'] ?? null;
        $numero_documento = $data['numero_documento'] ?? null;
        $nombre_completo = mb_strtoupper($data['nombre_completo'], 'UTF-8');
        $email = isset($data['email']) ? mb_strtolower($data['email'], 'UTF-8') : null;
        $telefono = $data['telefono'] ?? null;
        $direccion = $data['direccion'] ?? null;

        $stmt->execute([
            $tipo_tercero, 
            $tipo_documento, 
            $numero_documento, 
            $nombre_completo, 
            $email, 
            $telefono, 
            $direccion, 
            $id, 
            $payload['tenant_id']
        ]);
        
        $this->jsonResponse(["error" => false, "message" => "Tercero actualizado exitosamente."]);
    }

    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        
        // Verificar pertenencia
        $check = $db->prepare("SELECT id FROM terceros WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $payload['tenant_id']]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Tercero no encontrado."], 404);
        }

        // Ideally, we should check if this third party is used in sales/purchases before deleting
        // For now, we proceed with deletion if the foreign key constraints allow it (or if cascade is set, though usually it restricts)
        try {
            $stmt = $db->prepare("DELETE FROM terceros WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$id, $payload['tenant_id']]);
            $this->jsonResponse(["error" => false, "message" => "Tercero eliminado exitosamente."]);
        } catch (\PDOException $e) {
            // Check for foreign key constraint violation (SQLSTATE 23000)
            if ($e->getCode() == 23000) {
                $this->jsonResponse(["error" => true, "message" => "No se puede eliminar este tercero porque tiene registros asociados (ej. ventas)."], 409);
            }
            $this->jsonResponse(["error" => true, "message" => "Error al eliminar el tercero."], 500);
        }
    }
}
