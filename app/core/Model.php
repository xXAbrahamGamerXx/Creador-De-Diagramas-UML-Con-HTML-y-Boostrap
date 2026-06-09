<?php
/**
 * app/core/Model.php — Modelo base
 *
 * Todos los modelos extienden esta clase.
 * Proporciona acceso centralizado a la conexión PDO.
 */
class Model {

    /** @var PDO Conexión compartida para todos los modelos */
    protected $conn;

    public function __construct() {
        try {
            $db         = new Database();
            $this->conn = $db->getConnection();
        } catch (Exception $e) {
            // Propagar como RuntimeException con mensaje claro
            throw new RuntimeException('No se pudo conectar a la base de datos: ' . $e->getMessage(), 0, $e);
        }
    }
}
?>
