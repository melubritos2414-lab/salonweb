<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

include("../includes/conexion.php");
include("../includes/nav.php");

$idUser = $_SESSION['idUser'];

// Obtener citas del usuario
$stmt = $conexion->prepare("
    SELECT idCita, fecha_citas, hora, motivo_citas
    FROM citas
    WHERE idUser = ?
    ORDER BY fecha_citas ASC
");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();

$hoy = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citaciones</title>
    <!-- también subimos un nivel para el css -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<section class="contenedor">

    <h2 class="titulo-admin">Mis Citaciones</h2>

    <!-- FORMULARIO PARA CREAR CITA -->
    <div class="form-admin">

        <h3>Solicitar nueva cita</h3>

        <form method="POST">

            <label>Fecha de la cita:</label>
            <input type="date" name="fecha_citas" class="input-gold" required>

            <label>Hora de la cita:</label>
            <input type="time" name="hora" class="input-gold" required>

            <label>Motivo:</label>
            <textarea name="motivo_citas" class="input-gold" required></textarea>

            <button type="submit" name="crear" class="btn-outline-gold">Crear cita</button>

        </form>

        <?php
        if (isset($_POST['crear'])) {

            $fecha = $_POST['fecha_citas'];
            $hora = $_POST['hora'];
            $motivo = $_POST['motivo_citas'];

            // Insertar cita
            $sql = $conexion->prepare("
                INSERT INTO citas (idUser, fecha_citas, hora, motivo_citas)
                VALUES (?, ?, ?, ?)
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

    </div>

    <hr><br>

    <!-- LISTADO DE CITAS -->
    <h3>Mis citas programadas</h3>

    <table class="tabla-admin">
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Motivo</th>
            <th>Acciones</th>
        </tr>

        <?php while ($c = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($c['fecha_citas']) ?></td>
                <td><?= htmlspecialchars($c['hora']) ?></td>
                <td><?= htmlspecialchars($c['motivo_citas']) ?></td>

                <td>
                    <?php if ($c['fecha_citas'] >= $hoy): ?>
                        <!-- EDITAR (mismo nivel /user/) -->
                        <a class="btn-editar" href="editar-cita.php?id=<?= $c['idCita'] ?>">Editar</a>

                        <!-- BORRAR (mismo nivel /user/) -->
                        <a class="btn-borrar" href="borrar-cita.php?id=<?= $c['idCita'] ?>"
                           onclick="return confirm('¿Seguro que deseas borrar esta cita?');">
                           Borrar
                        </a>
                    <?php else: ?>
                        <span style="color:#aaa;">No disponible</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

</section>

<?php include("../includes/footer.php"); ?>

</body>
</html>
