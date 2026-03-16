<?php
namespace App\Core;

class Router {
    protected array $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
    }

    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }

    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_GET['url'] ?? '/';
        $path = '/' . trim($path, '/');

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routePath => $callback) {
            // Convertir {param} en grupo de captura regex
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Quitar coincidencia completa, dejar solo grupos

                if (is_array($callback)) {
                    $controller = new $callback[0]();
                    $action = $callback[1];
                    return call_user_func_array([$controller, $action], $matches);
                }
                return call_user_func_array($callback, $matches);
            }
        }

        http_response_code(404);
        echo json_encode(["error" => true, "message" => "Endpoint no encontrado: $method $path"]);
    }
}
