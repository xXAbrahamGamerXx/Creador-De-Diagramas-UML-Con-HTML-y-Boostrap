<?php
/**
 * app/models/UserModel.php — Modelo de Usuarios
 *
 * Gestiona operaciones de BD relacionadas con la tabla `usuarios`.
 * Extrae toda la lógica de datos que antes estaba en api/login.php y api/register.php.
 */
class UserModel extends Model {

    /**
     * Busca un usuario por username o email (para login).
     *
     * @param string $usernameOrEmail
     * @return array|null  Fila de la BD o null si no existe / está inactivo
     */
    public function findByUsernameOrEmail(string $usernameOrEmail): ?array {
        $stmt = $this->conn->prepare(
            "SELECT * FROM usuarios WHERE (username = :u1 OR email = :u2) AND activo = TRUE"
        );
        $stmt->bindParam(':u1', $usernameOrEmail);
        $stmt->bindParam(':u2', $usernameOrEmail);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Registra el último acceso del usuario.
     *
     * @param int $id
     */
    public function updateLastAccess(int $id): void {
        $this->conn->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id")
                   ->execute([':id' => $id]);
    }

    /**
     * Verifica si un username o email ya existe.
     *
     * @param string $username
     * @param string $email
     * @return bool
     */
    public function existsUsernameOrEmail(string $username, string $email): bool {
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE username = :u OR email = :e");
        $stmt->execute([':u' => $username, ':e' => $email]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Crea un nuevo usuario en la BD.
     *
     * @param array $datos  [username, email, password_plain, nombre_completo, rol]
     * @return int  ID del nuevo usuario
     */
    public function crear(array $datos): int {
        $hash   = password_hash($datos['password'], PASSWORD_DEFAULT);
        $nombre = trim($datos['nombre'] ?? $datos['username']);
        $stmt   = $this->conn->prepare(
            "INSERT INTO usuarios (username, email, password, nombre_completo, rol)
             VALUES (:username, :email, :password, :nombre, :rol)"
        );
        $stmt->execute([
            ':username' => $datos['username'],
            ':email'    => $datos['email'],
            ':password' => $hash,
            ':nombre'   => $nombre,
            ':rol'      => $datos['rol'],
        ]);
        return (int) $this->conn->lastInsertId();
    }

    /**
     * Obtiene todos los usuarios (para panel admin).
     *
     * @return array
     */
    public function getAll(): array {
        try {
            $stmt = $this->conn->query(
                "SELECT id, username, email, nombre_completo, rol, activo, fecha_registro, ultimo_acceso
                 FROM usuarios ORDER BY fecha_registro DESC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("UserModel::getAll — " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene estadísticas de usuarios (para admin).
     *
     * @return array
     */
    public function getStats(): array {
        try {
            $stats = [];
            foreach ([
                'total'    => "SELECT COUNT(*) FROM usuarios",
                'activos'  => "SELECT COUNT(*) FROM usuarios WHERE activo = 1",
                'alumnos'  => "SELECT COUNT(*) FROM usuarios WHERE rol = 'alumno'",
                'maestros' => "SELECT COUNT(*) FROM usuarios WHERE rol = 'maestro'",
                'admins'   => "SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'",
            ] as $key => $sql) {
                $stats[$key] = (int) $this->conn->query($sql)->fetchColumn();
            }
            return $stats;
        } catch (Exception $e) {
            return ['total' => 0, 'activos' => 0, 'alumnos' => 0, 'maestros' => 0, 'admins' => 0];
        }
    }

    /**
     * Actualiza el rol de un usuario (solo admin).
     */
    public function updateRol(int $id, string $rol): bool {
        try {
            $stmt = $this->conn->prepare("UPDATE usuarios SET rol = :rol WHERE id = :id");
            $stmt->execute([':rol' => $rol, ':id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("UserModel::updateRol — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Activa/desactiva un usuario (solo admin).
     */
    public function toggleActivo(int $id, bool $activo): bool {
        try {
            $stmt = $this->conn->prepare("UPDATE usuarios SET activo = :a WHERE id = :id");
            $stmt->execute([':a' => (int)$activo, ':id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("UserModel::toggleActivo — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario (solo admin).
     */
    public function eliminar(int $id): bool {
        try {
            $this->conn->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("UserModel::eliminar — " . $e->getMessage());
            return false;
        }
    }
}
?>
