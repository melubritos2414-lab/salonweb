<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../includes/conexion.php");

// Verificar si recibimos el ID
if (!isset($_GET['id'])) {
    header("Location: citas-admin.php");
    exit;
}

$id = intval($_GET['id']);

// Borrar la cita del administrador
$stmt = $conexion->prepare("DELETE FROM citas_admin WHERE idCita_admin = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirigir con mensaje
    echo "<script>
            alert('Cita eliminada correctamente.');
            window.location = 'citas-admin.php';
          </script>";
} else {
    echo "<script>
            alert('Error al eliminar la cita.');
            window.location = 'citas-admin.php';
          </script>";
}
?>
