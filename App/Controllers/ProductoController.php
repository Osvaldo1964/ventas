<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class ProductoController extends Controller {

    // GET /api/productos
    public function index() {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();

        $stmt = $db->prepare(
            "SELECT p.id, p.codigo, p.nombre, p.descripcion, p.precio_venta, p.precio_costo,
                    p.permite_descuento, p.aplica_iva, p.iva_porcentaje, p.activo,
                    p.imagen_principal_url, c.nombre AS categoria
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.tenant_id = :tid
             ORDER BY p.nombre ASC"
        );
        $stmt->execute([':tid' => $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "data" => $stmt->fetchAll()]);
    }

    // GET /api/productos/{id}
    public function show($id) {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();

        $stmt = $db->prepare("SELECT * FROM productos WHERE id = :id AND tenant_id = :tid");
        $stmt->execute([':id' => $id, ':tid' => $payload['tenant_id']]);
        $producto = $stmt->fetch();

        if(!$producto) {
            $this->jsonResponse(["error" => true, "message" => "Producto no encontrado."], 404);
        }
        $this->jsonResponse(["error" => false, "data" => $producto]);
    }

    // POST /api/productos
    public function store() {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();

        if(empty($data['nombre']) || empty($data['precio_venta'])) {
            $this->jsonResponse(["error" => true, "message" => "Nombre y precio de venta son requeridos."], 422);
        }

        $imagen_url = $this->handleUpload('productos');

        $db = (new Database())->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO productos (tenant_id, categoria_id, codigo, nombre, descripcion,
                precio_costo, precio_venta, permite_descuento, aplica_iva, iva_porcentaje, imagen_principal_url, activo)
             VALUES (:tid, :cat, :cod, :nom, :desc, :costo, :venta, :descuento, :iva, :iva_pct, :img, 1)"
        );
        $stmt->execute([
            ':tid'      => $payload['tenant_id'],
            ':cat'      => $data['categoria_id'] ?? null,
            ':cod'      => $data['codigo'] ?? null,
            ':nom'      => $data['nombre'],
            ':desc'     => $data['descripcion'] ?? '',
            ':costo'    => $data['precio_costo'] ?? 0,
            ':venta'    => $data['precio_venta'],
            ':descuento'=> $data['permite_descuento'] ?? 1,
            ':iva'      => $data['aplica_iva'] ?? 0,
            ':iva_pct'  => $data['iva_porcentaje'] ?? 0,
            ':img'      => $imagen_url,
        ]);
        $this->jsonResponse(["error" => false, "message" => "Producto creado exitosamente.", "id" => $db->lastInsertId()], 201);
    }

    // PUT /api/productos/{id}
    public function update($id) {
        $payload = AuthMiddleware::authenticate();
        $data = $this->getPostData();
        $db = (new Database())->getConnection();

        // Verificar que el producto pertenece al tenant
        $check = $db->prepare("SELECT id, imagen_principal_url FROM productos WHERE id = :id AND tenant_id = :tid");
        $check->execute([':id' => $id, ':tid' => $payload['tenant_id']]);
        $producto = $check->fetch();
        if(!$producto) {
            $this->jsonResponse(["error" => true, "message" => "Producto no encontrado."], 404);
        }

        $imagen_url = $this->handleUpload('productos') ?: ($producto['imagen_principal_url'] ?? null);

        $stmt = $db->prepare(
            "UPDATE productos SET
                categoria_id = :cat, codigo = :cod, nombre = :nom, descripcion = :desc,
                precio_costo = :costo, precio_venta = :venta,
                permite_descuento = :descuento, aplica_iva = :iva, iva_porcentaje = :iva_pct,
                imagen_principal_url = :img
             WHERE id = :id AND tenant_id = :tid"
        );
        $stmt->execute([
            ':cat'      => $data['categoria_id'] ?? null,
            ':cod'      => $data['codigo'] ?? null,
            ':nom'      => $data['nombre'],
            ':desc'     => $data['descripcion'] ?? '',
            ':costo'    => $data['precio_costo'] ?? 0,
            ':venta'    => $data['precio_venta'],
            ':descuento'=> $data['permite_descuento'] ?? 1,
            ':iva'      => $data['aplica_iva'] ?? 0,
            ':iva_pct'  => $data['iva_porcentaje'] ?? 0,
            ':img'      => $imagen_url,
            ':id'       => $id,
            ':tid'      => $payload['tenant_id'],
        ]);
        $this->jsonResponse(["error" => false, "message" => "Producto actualizado exitosamente."]);
    }

    private function handleUpload($folder) {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $targetPath = "uploads/$folder/$fileName";
        $fullPath = $targetPath; // Relative to public/ since getcwd() is typically public/ or we handle it via index.php

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fullPath)) {
            return "/ventas/public/" . $targetPath;
        }
        return null;
    }

    // DELETE /api/productos/{id}
    public function destroy($id) {
        $payload = AuthMiddleware::authenticate();
        $db = (new Database())->getConnection();

        $check = $db->prepare("SELECT id FROM productos WHERE id = :id AND tenant_id = :tid");
        $check->execute([':id' => $id, ':tid' => $payload['tenant_id']]);
        if(!$check->fetch()) {
            $this->jsonResponse(["error" => true, "message" => "Producto no encontrado."], 404);
        }

        // Soft-delete (desactivar) en lugar de eliminar físicamente para preservar historial de ventas
        $stmt = $db->prepare("UPDATE productos SET activo = 0 WHERE id = :id AND tenant_id = :tid");
        $stmt->execute([':id' => $id, ':tid' => $payload['tenant_id']]);
        $this->jsonResponse(["error" => false, "message" => "Producto desactivado correctamente."]);
    }
}
