<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/conexion.php";

// Verificar datos obligatorios
if (
    !isset($_POST['nombre_cliente'], $_POST['apellido_cliente'], $_POST['telefono'],
            $_POST['servicio'], $_POST['fecha_citas'], $_POST['hora'])
) {
    echo "<p style='color:red; text-align:center;'>Error: Faltan datos obligatorios.</p>";
    exit;
}

$nombre   = $_POST['nombre_cliente'];
$apellido = $_POST['apellido_cliente'];
$telefono = $_POST['telefono'];
$email    = $_POST['email'];
$servicio = $_POST['servicio'];
$fecha    = $_POST['fecha_citas'];
$hora     = $_POST['hora'];
$motivo   = $_POST['motivo_citas'];
$estado   = $_POST['estado'];

// Insertar cita en la tabla del admin
$stmt = $conexion->prepare("
    INSERT INTO citas_admin (nombre_cliente, apellido_cliente, telefono, email, servicio,
                             fecha_citas, hora, motivo_citas, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssssssss", $nombre, $apellido, $telefono, $email, $servicio,
                  $fecha, $hora, $motivo, $estado);

if ($stmt->execute()) {
    echo "<p style='color:green; text-align:center;'>Cita creada correctamente.</p>";
    echo "<script>
            setTimeout(function() {
                window.location = 'citaciones-administracion.php';
            }, 1500);
          </script>";
} else {
    echo "<p style='color:red; text-align:center;'>Error: " . $conexion->error . "</p>";
}
?>
