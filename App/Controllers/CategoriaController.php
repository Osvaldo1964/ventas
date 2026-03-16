<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class CategoriaController extends Controller {

    public function index() {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM categorias WHERE tenant_id = :tid ORDER BY nombre ASC");
        $stmt->execute([':tid' => $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    public function store() {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();
        if(empty($data['nombre'])) {
            $this->jsonResponse(["error" => true, "message" => "El nombre de la categoría es requerido."], 422);
        }
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO categorias (tenant_id, nombre, descripcion, imagen_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$payload['tenant_id'], $data['nombre'], $data['descripcion'] ?? '', $data['imagen_url'] ?? null]);
        $this->jsonResponse(["error" => false, "message" => "Categoría creada.", "id" => $db->lastInsertId()], 201);
    }

    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$data['nombre'], $data['descripcion'] ?? '', $id, $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "message" => "Categoría actualizada."]);
    }

    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "message" => "Categoría eliminada."]);
    }
}
