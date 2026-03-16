<?php
namespace App\Controllers;

class WebController {
    public function render($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: " . $view;
        }
    }
    
    public function login() {
        $this->render('login');
    }
    
    public function dashboard() {
        $this->render('dashboard');
    }

    public function configuracion() {
        $this->render('configuracion');
    }

    public function usuarios() {
        $this->render('usuarios');
    }

    public function tiendas() {
        $this->render('tiendas');
    }

    public function inventario() {
        $this->render('inventario');
    }

    public function productos() {
        $this->render('productos');
    }

    public function categorias() {
        $this->render('categorias');
    }

    public function cajas() {
        $this->render('cajas');
    }
}
