<?php
// Evitar múltiples conexiones
if (!isset($conexion)) {

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "salonweb";

    $conexion = new mysqli($host, $user, $pass, $db);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Asegurar caracteres especiales (ñ, tildes, etc.)
    $conexion->set_charset("utf8mb4");
}
?>
