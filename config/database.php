<?php
/**
 * config/database.php
 *
 * Este archivo existe solo para compatibilidad con herramientas externas
 * que busquen la configuración en config/.
 *
 * La clase Database real está en app/core/Database.php y es cargada
 * automáticamente por el bootstrap.
 *
 * Para cambiar las credenciales de la base de datos,
 * edita app/core/Database.php o usa el Panel de Administración.
 */

// Si la clase ya fue cargada por el bootstrap, no hacer nada.
if (!class_exists('Database')) {
    require_once __DIR__ . '/../app/core/Database.php';
}
?>
