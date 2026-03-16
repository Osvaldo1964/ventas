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

        $imagen_url = $this->handleUpload('categorias');

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO categorias (tenant_id, nombre, descripcion, imagen_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$payload['tenant_id'], $data['nombre'], $data['descripcion'] ?? '', $imagen_url]);
        $this->jsonResponse(["error" => false, "message" => "Categoría creada.", "id" => $db->lastInsertId()], 201);
    }

    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();
        $db = (new Database())->getConnection();

        // Verificar pertenencia
        $check = $db->prepare("SELECT id, imagen_url FROM categorias WHERE id = ? AND tenant_id = ?");
        $check->execute([$id, $payload['tenant_id']]);
        $categoria = $check->fetch();
        if (!$categoria) {
            $this->jsonResponse(["error" => true, "message" => "Categoría no encontrada."], 404);
        }

        $imagen_url = $this->handleUpload('categorias') ?: ($categoria['imagen_url'] ?? null);

        $stmt = $db->prepare("UPDATE categorias SET nombre = ?, descripcion = ?, imagen_url = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$data['nombre'], $data['descripcion'] ?? '', $imagen_url, $id, $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "message" => "Categoría actualizada."]);
    }

    private function handleUpload($folder) {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $targetPath = "uploads/$folder/$fileName";
        $fullPath = $targetPath;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fullPath)) {
            return "/ventas/public/" . $targetPath;
        }
        return null;
    }

    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "message" => "Categoría eliminada."]);
    }
}
