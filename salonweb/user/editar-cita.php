<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

// Como este archivo está en /user/, hay que subir un nivel:
include("../includes/conexion.php");
include("../includes/nav.php");

$idUser = $_SESSION['idUser'];

if (!isset($_GET['id'])) {
    header("Location: citaciones.php");
    exit;
}

$idCita = intval($_GET['id']);

// Obtener cita
$stmt = $conexion->prepare("
    SELECT fecha_citas, hora, motivo_citas
    FROM citas
    WHERE idCita = ? AND idUser = ?
");
$stmt->bind_param("ii", $idCita, $idUser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<p style='color:red; text-align:center;'>Cita no encontrada.</p>";
    exit;
}

$cita = $result->fetch_assoc();
$hoy = date("Y-m-d");

// Validar que la cita sea futura
if ($cita['fecha_citas'] < $hoy) {
    echo "<p style='color:red; text-align:center;'>No puedes editar citas pasadas.</p>";
    exit;
}

// Guardar cambios
if (isset($_POST['guardar'])) {

    $fecha = $_POST['fecha_citas'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo_citas'];

    $stmt = $conexion->prepare("
        UPDATE citas
        SET fecha_citas = ?, hora = ?, motivo_citas = ?
        WHERE idCita = ? AND idUser = ?
    ");
    $stmt->bind_param("sssii", $fecha, $hora, $motivo, $idCita, $idUser);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Cita actualizada correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location = 'citaciones.php';
                }, 1500);
              </script>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error al actualizar la cita.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <!-- Subimos un nivel para el CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<section class="contenedor">

<h2 class="titulo-admin">Editar Cita</h2>

<form method="POST" class="form-admin">

    <label>Fecha:</label>
    <input type="date" name="fecha_citas" class="input-gold" value="<?= $cita['fecha_citas'] ?>" required>

    <label>Hora:</label>
    <input type="time" name="hora" class="input-gold" value="<?= $cita['hora'] ?>" required>

    <label>Motivo:</label>
    <textarea name="motivo_citas" class="input-gold" required><?= $cita['motivo_citas'] ?></textarea>

    <button type="submit" name="guardar" class="btn-outline-gold">Guardar cambios</button>

</form>

<br>
<!-- Volver a citaciones.php dentro de /user -->
<a class="btn-accion" href="citaciones.php">Volver</a>

</section>

<?php include("../includes/footer.php"); ?>

</body>
</html>
