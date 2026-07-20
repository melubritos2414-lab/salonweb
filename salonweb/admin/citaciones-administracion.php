<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../includes/conexion.php");
include("../includes/nav.php");

// Obtener todas las citas creadas por usuarios
$sql = "SELECT idCita, idUser, fecha_citas, hora, motivo_citas
        FROM citas
        ORDER BY fecha_citas ASC, hora ASC";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas de Usuarios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1 class="titulo-admin">Citas de Usuarios</h1>

<table class="tabla-admin">
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Motivo</th>
        <th>Acciones</th>
    </tr>

    <?php while ($cita = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $cita['idCita'] ?></td>
        <td><?= $cita['idUser'] ?></td>
        <td><?= $cita['fecha_citas'] ?></td>
        <td><?= $cita['hora'] ?></td>
        <td><?= $cita['motivo_citas'] ?></td>

        <td>
            <a href="citaciones-editar.php?id=<?= $cita['idCita'] ?>" class="btn-admin">Editar</a>
            <a href="citaciones-borrar.php?id=<?= $cita['idCita'] ?>" class="btn-admin" style="background:red;">Borrar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include("../includes/footer.php"); ?>

</body>
</html>
