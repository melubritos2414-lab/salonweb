<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

// Este archivo está en /user/, así que subimos un nivel:
include("../includes/conexion.php");

$idUser = $_SESSION['idUser'];

if (!isset($_GET['id'])) {
    header("Location: citaciones.php");
    exit;
}

$idCita = intval($_GET['id']);

// Obtener cita
$stmt = $conexion->prepare("
    SELECT fecha_citas
    FROM citas
    WHERE idCita = ? AND idUser = ?
");
$stmt->bind_param("ii", $idCita, $idUser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: citaciones.php");
    exit;
}

$cita = $result->fetch_assoc();
$hoy = date("Y-m-d");

// Validar fecha
if ($cita['fecha_citas'] < $hoy) {
    echo "<p style='color:red; text-align:center;'>No puedes borrar citas pasadas.</p>";
    exit;
}

// Borrar cita
$stmt = $conexion->prepare("DELETE FROM citas WHERE idCita = ? AND idUser = ?");
$stmt->bind_param("ii", $idCita, $idUser);

if ($stmt->execute()) {
    header("Location: citaciones.php?msg=delete_ok");
    exit;
} else {
    echo "<p style='color:red; text-align:center;'>Error al borrar la cita.</p>";
}
?>
