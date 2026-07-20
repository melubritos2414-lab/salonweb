<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/conexion.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: citaciones-administracion.php");
    exit;
}

$idCita = intval($_GET['id']);

$stmt = $conexion->prepare("DELETE FROM citas WHERE idCita = ?");
$stmt->bind_param("i", $idCita);
$stmt->execute();
$stmt->close();

header("Location: citaciones-administracion.php");
exit;
?>
