<?php
/**
 * app/core/Database.php — Clase de conexión PDO (núcleo MVC)
 *
 * Configuración:
 *   host     → Servidor MySQL (normalmente localhost en XAMPP)
 *   db_name  → Nombre de la base de datos
 *   username → Usuario MySQL
 *   password → Contraseña MySQL
 *
 * Para cambiar la conexión edita solo las 4 constantes de arriba.
 * NO codifiques credenciales en otros archivos — siempre importa esta clase.
 */
class Database {
    private $host     = 'localhost';       // Host MySQL. En XAMPP local siempre es 'localhost'
    private $db_name  = 'diagramas_db';    // Nombre de la BD (debe coincidir con CREATE DATABASE del SQL)
    private $username = 'root';             // Usuario MySQL. En XAMPP por defecto es 'root'
    private $password = '';                 // Contraseña MySQL. En XAMPP por defecto está vacía
    public  $conn;

    /**
     * Abre y devuelve la conexión PDO.
     * Lanza PDOException si no puede conectar (el llamador la captura).
     */
    public function getConnection() {
        $this->conn = null;
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $this->conn = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Lanza excepciones en lugar de retornar false
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // fetchAll() devuelve arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Preparadas reales de MySQL (más seguras)
        ]);
        return $this->conn;
    }

    /** Devuelve solo los parámetros de conexión (sin contraseña) para diagnóstico */
    public function getInfo() {
        return ['host' => $this->host, 'db' => $this->db_name, 'user' => $this->username];
    }
}
?>
