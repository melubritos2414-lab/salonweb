<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Este archivo está en /user/, así que subimos un nivel:
include("../includes/conexion.php");
include("../includes/nav.php");

if (!isset($_SESSION['idUser'])) {
    header("Location: ../login.php?msg=login_required");
    exit;
}

$idUser = $_SESSION['idUser'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nueva Cita</title>
    <!-- Subimos un nivel para el CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<section class="contenedor">
    <h2 class="titulo-admin">Crear Nueva Cita</h2>

    <form method="POST" class="form-admin">

        <label>Fecha de la cita:</label>
        <input type="date" name="fecha_citas" class="input-gold" required>

        <label>Hora de la cita:</label>
        <input type="time" name="hora" class="input-gold" required>

        <label>Motivo:</label>
        <textarea name="motivo_citas" class="input-gold" required></textarea>

        <button type="submit" name="guardar" class="btn-outline-gold">Crear cita</button>

    </form>

<?php
if (isset($_POST['guardar'])) {

    $fecha = $_POST['fecha_citas'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo_citas'];

    $sql = $conexion->prepare("
        INSERT INTO citas (idUser, fecha_citas, hora, motivo_citas, estado)
        VALUES (?, ?, ?, ?, 'pendiente')
    ");
    $sql->bind_param("isss", $idUser, $fecha, $hora, $motivo);

    if ($sql->execute()) {
        echo "<p style='color:green; text-align:center;'>Cita creada correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location = 'citaciones.php';
                }, 1500);
              </script>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $conexion->error . "</p>";
    }
}
?>
</section>

<?php include("../includes/footer.php"); ?>

</body>
</html>
