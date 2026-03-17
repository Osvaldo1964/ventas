<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class BodegaController extends Controller {

    public function index() {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        
        $sql = "SELECT b.id, b.nombre, b.ubicacion, b.sede_id, s.nombre as sede_nombre 
                FROM bodegas b 
                LEFT JOIN sedes s ON b.sede_id = s.id 
                WHERE b.tenant_id = :tid 
                ORDER BY b.nombre ASC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([':tid' => $payload['tenant_id']]);
        
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    public function store() {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();

        if (empty($data['nombre']) || empty($data['sede_id'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre y la sede son requeridos."], 422);
        }

        $db = (new Database())->getConnection();
        
        // Verificar que la sede pertenece al tenant
        $checkSede = $db->prepare("SELECT id FROM sedes WHERE id = ? AND tenant_id = ?");
        $checkSede->execute([$data['sede_id'], $payload['tenant_id']]);
        if (!$checkSede->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Sede no válida o no pertenece al comercio."], 403);
        }

        $stmt = $db->prepare("INSERT INTO bodegas (tenant_id, sede_id, nombre, ubicacion) VALUES (?, ?, ?, ?)");
        
        $sede_id = $data['sede_id'];
        $nombre = mb_strtoupper($data['nombre'], 'UTF-8'); // Almacenar en mayúsculas por convención
        $ubicacion = $data['ubicacion'] ?? null;

        $stmt->execute([
            $payload['tenant_id'], 
            $sede_id, 
            $nombre, 
            $ubicacion
        ]);
        
        $this->jsonResponse(["error" => false, "message" => "Bodega creada exitosamente.", "id" => $db->lastInsertId()], 201);
    }

    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();
        $db = (new Database())->getConnection();

        // Verificar pertenencia de la bodega
        $check = $db->prepare("SELECT id FROM bodegas WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $payload['tenant_id']]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Bodega no encontrada."], 404);
        }

        if (empty($data['nombre']) || empty($data['sede_id'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre y la sede son requeridos."], 422);
        }

        // Verificar que la sede pertenece al tenant
        $checkSede = $db->prepare("SELECT id FROM sedes WHERE id = ? AND tenant_id = ?");
        $checkSede->execute([$data['sede_id'], $payload['tenant_id']]);
        if (!$checkSede->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Sede no válida o no pertenece al comercio."], 403);
        }

        $stmt = $db->prepare("UPDATE bodegas SET sede_id = ?, nombre = ?, ubicacion = ? WHERE id = ? AND tenant_id = ?");
        
        $sede_id = $data['sede_id'];
        $nombre = mb_strtoupper($data['nombre'], 'UTF-8');
        $ubicacion = $data['ubicacion'] ?? null;

        $stmt->execute([
            $sede_id, 
            $nombre, 
            $ubicacion, 
            $id, 
            $payload['tenant_id']
        ]);
        
        $this->jsonResponse(["error" => false, "message" => "Bodega actualizada exitosamente."]);
    }

    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        
        // Verificar pertenencia
        $check = $db->prepare("SELECT id FROM bodegas WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $payload['tenant_id']]);
        if (!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Bodega no encontrada."], 404);
        }

        try {
            $stmt = $db->prepare("DELETE FROM bodegas WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$id, $payload['tenant_id']]);
            $this->jsonResponse(["error" => false, "message" => "Bodega eliminada exitosamente."]);
        } catch (\PDOException $e) {
            // Error de restricción de clave foránea
            if ($e->getCode() == 23000) {
                $this->jsonResponse(["error" => true, "message" => "No se puede eliminar esta bodega porque tiene movimientos o stock asociado."], 409);
            }
            $this->jsonResponse(["error" => true, "message" => "Error al eliminar la bodega."], 500);
        }
    }
}
