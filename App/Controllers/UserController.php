<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class UserController extends Controller {
    public function getList() {
        // Intercepta si no hay token válido y devuelve 401
        $payload = AuthMiddleware::authenticate();
        $tenant_id = $payload['tenant_id'];

        $db = (new Database())->getConnection();
        
        // El listado SÓLO trae usuarios del tenant correspondiente
        $query = "SELECT u.id, u.nombre, u.email, u.estado, r.nombre as rol, s.nombre as sede 
                  FROM usuarios u 
                  LEFT JOIN roles r ON u.rol_id = r.id
                  LEFT JOIN sedes s ON u.sede_id = s.id 
                  WHERE u.tenant_id = :tenant_id
                  ORDER BY u.id DESC";
                  
        $stmt = $db->prepare($query);
        $stmt->execute([':tenant_id' => $tenant_id]);
        $users = $stmt->fetchAll();
        
        $this->jsonResponse(["error" => false, "data" => $users]);
    }
}
