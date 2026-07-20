<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['rol'] !== 'admin') {
    die("<div class='mensaje-error'>Acceso denegado</div>");
}
?>
