<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idUser'])) {

    // Guardar la página que intentaba visitar
    $_SESSION['redirigir_a'] = $_SERVER['REQUEST_URI'];

    header("Location: login.php");
    exit;
}
?>
