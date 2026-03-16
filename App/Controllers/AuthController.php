<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Helpers\JWT;

class AuthController extends Controller {
    
    public function login() {
        $data = $this->getPostData();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $tenant_id = $data['tenant_id'] ?? null; // Si entran por subdominio, esto vendría implícito

        if (!$email || !$password) {
            $this->jsonResponse(["error" => true, "message" => "Faltan credenciales"], 400);
        }

        $db = (new Database())->getConnection();

        // 1. Validar usuario (Si no pasan tenant_id, buscamos globalmente si el email es único o asumimos subdominio)
        // Para simplificar, asumiremos que pasan el tenant_id o que el email es universal.
        $query = "SELECT u.id, u.tenant_id, u.nombre, u.password_hash, u.rol_id, r.nombre as rol_nombre, r.permisos_json 
                  FROM usuarios u 
                  INNER JOIN roles r ON u.rol_id = r.id 
                  WHERE u.email = :email";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->jsonResponse(["error" => true, "message" => "Credenciales incorrectas"], 401);
        }

        // 2. Regla de Negocio: Validar Cajas para Cajeros ($user['rol_nombre'] != 'Admin')
        $cajaStatus = 'ok';
        $pendingCaja = null;

        if (strtolower($user['rol_nombre']) !== 'admin') {
            $cajaQuery = "SELECT id, caja_id, fecha_apertura, estado 
                          FROM sesiones_caja 
                          WHERE tenant_id = :tenant_id AND usuario_id = :usuario_id AND estado = 'Abierta' 
                          ORDER BY fecha_apertura DESC LIMIT 1";
            $cajaStmt = $db->prepare($cajaQuery);
            $cajaStmt->execute([
                ':tenant_id' => $user['tenant_id'],
                ':usuario_id' => $user['id']
            ]);
            $caja = $cajaStmt->fetch();

            if (!$caja) {
                // No tiene caja abierta hoy -> Debe abrir
                $cajaStatus = 'require_open_caja';
            } else {
                // Verificar si es de hoy o de días pasados
                $fecha_apertura = date('Y-m-d', strtotime($caja['fecha_apertura']));
                $fecha_hoy = date('Y-m-d');

                if ($fecha_apertura < $fecha_hoy) {
                    $cajaStatus = 'require_close_caja';
                    $pendingCaja = $caja;
                }
            }
        }

        // 3. Generar JWT Payload
        $payload = [
            'tenant_id' => $user['tenant_id'],
            'usuario_id' => $user['id'],
            'rol_id' => $user['rol_id'],
            'rol_nombre' => $user['rol_nombre'],
            'permisos' => json_decode($user['permisos_json'], true)
        ];

        $token = JWT::encode($payload);

        $this->jsonResponse([
            "error" => false,
            "message" => "Login exitoso",
            "token" => $token,
            "usuario" => [
                "nombre" => $user['nombre'],
                "rol" => $user['rol_nombre']
            ],
            "caja_status" => $cajaStatus,  // 'ok', 'require_open_caja', 'require_close_caja'
            "pending_caja" => $pendingCaja
        ], 200);
    }
}
