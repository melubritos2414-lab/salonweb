<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/conexion.php";

$id = $_SESSION['idUser'];

$nombre    = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$email     = $_POST['email'];
$telefono  = $_POST['telefono'];

// Actualizar datos personales
$stmt = $conexion->prepare("
    UPDATE users_data 
    SET nombre = ?, apellidos = ?, email = ?, telefono = ?
    WHERE idUser = ?
");
$stmt->bind_param("ssssi", $nombre, $apellidos, $email, $telefono, $id);
$stmt->execute();

echo "<p style='color:green; text-align:center;'>Perfil actualizado correctamente.</p>";
echo "<script>
        setTimeout(function() {
            window.location = 'perfil-admin.php';
        }, 1500);
      </script>";
?>
