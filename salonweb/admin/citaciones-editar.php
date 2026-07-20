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

$stmt = $conexion->prepare("
    SELECT fecha_citas, hora, motivo_citas, estado
    FROM citas
    WHERE idCita = ?
");
$stmt->bind_param("i", $idCita);
$stmt->execute();
$result = $stmt->get_result();
$cita = $result->fetch_assoc();

if (!$cita) {
    header("Location: citaciones-administracion.php");
    exit;
}

if (isset($_POST['guardar'])) {

    $fecha  = $_POST['fecha_citas'];
    $hora   = $_POST['hora'];
    $motivo = $_POST['motivo_citas'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("
        UPDATE citas
        SET fecha_citas = ?, hora = ?, motivo_citas = ?, estado = ?
        WHERE idCita = ?
    ");
    $stmt->bind_param("ssssi", $fecha, $hora, $motivo, $estado, $idCita);
    $stmt->execute();
    $stmt->close();

    header("Location: citaciones-administracion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">

<h1>Editar Cita</h1>

<form method="POST" class="admin-form">

    <label>Fecha:</label>
    <input type="date" name="fecha_citas" value="<?= $cita['fecha_citas'] ?>" required>

    <label>Hora:</label>
    <input type="time" name="hora" value="<?= $cita['hora'] ?>" required>

    <label>Motivo:</label>
    <input type="text" name="motivo_citas" value="<?= $cita['motivo_citas'] ?>" required>

    <label>Estado:</label>
    <select name="estado">
        <option value="pendiente" <?= $cita['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="aceptada" <?= $cita['estado'] === 'aceptada' ? 'selected' : '' ?>>Aceptada</option>
        <option value="rechazada" <?= $cita['estado'] === 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
    </select>

    <button type="submit" name="guardar">Guardar cambios</button>
</form>

<a class="btn-accion" href="citaciones-administracion.php">Volver</a>

</div>

</body>
</html>
