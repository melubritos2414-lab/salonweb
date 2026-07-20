<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../includes/conexion.php");

if (!isset($_GET['id'])) {
    header("Location: citas-admin.php");
    exit;
}

$id = intval($_GET['id']);

// Cambiar estado a "aceptada"
$stmt = $conexion->prepare("UPDATE citas_admin SET estado = 'aceptada' WHERE idCita_admin = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
            alert('Cita aceptada correctamente.');
            window.location = 'citas-admin.php';
          </script>";
} else {
    echo "<script>
            alert('Error al aceptar la cita.');
            window.location = 'citas-admin.php';
          </script>";
}
?>
