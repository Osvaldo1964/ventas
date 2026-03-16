<?php
namespace App\Middlewares;

use App\Helpers\JWT;

class AuthMiddleware {
    public static function authenticate() {
        // Compatibilidad para diferentes servidores web
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }
        
        $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["error" => true, "message" => "Token no proporcionado o inválido."]);
            exit;
        }

        $token = $matches[1];
        $payload = JWT::decode($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(["error" => true, "message" => "Token expirado o firma inválida."]);
            exit;
        }

        return $payload; // Retorna array: ['tenant_id' => X, 'usuario_id' => Y, 'rol' => Z]
    }
}
