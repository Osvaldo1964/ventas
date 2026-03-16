<?php
namespace App\Config;

class Database {
    private string $host = "localhost";
    private string $db_name = "ventas_pos";
    private string $username = "root";
    private string $password = "";
    public ?\PDO $conn = null;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch(\PDOException $exception) {
            // Retorna json en caso de error para evitar ensuciar la salida REST
            header('Content-Type: application/json');
            echo json_encode(["error" => true, "message" => "Error de Conexión BD: " . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}
