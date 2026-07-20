<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../includes/conexion.php");
include("../includes/nav.php");

// Verificar ID
if (!isset($_GET['id'])) {
    header("Location: citas-admin.php");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos de la cita
$stmt = $conexion->prepare("SELECT * FROM citas_admin WHERE idCita_admin = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cita = $result->fetch_assoc();

if (!$cita) {
    echo "<p style='color:red; text-align:center;'>La cita no existe.</p>";
    exit;
}

// Actualizar cita
if (isset($_POST['actualizar'])) {

    $nombre = $_POST['nombre_cliente'];
    $apellido = $_POST['apellido_cliente'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $servicio = $_POST['servicio'];
    $fecha = $_POST['fecha_citas'];
    $hora = $_POST['hora'];
    $estado = $_POST['estado'];

    $update = $conexion->prepare("
        UPDATE citas_admin 
        SET nombre_cliente=?, apellido_cliente=?, telefono=?, email=?, servicio=?, fecha_citas=?, hora=?, estado=?
        WHERE idCita_admin=?
    ");

    $update->bind_param("ssssssssi", $nombre, $apellido, $telefono, $email, $servicio, $fecha, $hora, $estado, $id);

    if ($update->execute()) {
        echo "<script>
                alert('Cita actualizada correctamente.');
                window.location = 'citas-admin.php';
              </script>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $conexion->error . "</p>";
    }
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

<h1 class="titulo-admin">Editar Cita del Administrador</h1>

<div class="form-admin">

    <form method="POST">

        <label>Nombre del cliente:</label>
        <input type="text" name="nombre_cliente" class="input-gold" value="<?= $cita['nombre_cliente'] ?>" required>

        <label>Apellido del cliente:</label>
        <input type="text" name="apellido_cliente" class="input-gold" value="<?= $cita['apellido_cliente'] ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" class="input-gold" value="<?= $cita['telefono'] ?>" required>

        <label>Email:</label>
        <input type="email" name="email" class="input-gold" value="<?= $cita['email'] ?>" required>

        <label>Servicio:</label>
        <input type="text" name="servicio" class="input-gold" value="<?= $cita['servicio'] ?>" required>

        <label>Fecha:</label>
        <input type="date" name="fecha_citas" class="input-gold" value="<?= $cita['fecha_citas'] ?>" required>

        <label>Hora:</label>
        <input type="time" name="hora" class="input-gold" value="<?= $cita['hora'] ?>" required>

        <label>Estado:</label>
        <select name="estado" class="input-gold">
            <option value="pendiente" <?= $cita['estado']=="pendiente"?"selected":"" ?>>Pendiente</option>
            <option value="aceptada" <?= $cita['estado']=="aceptada"?"selected":"" ?>>Aceptada</option>
            <option value="rechazada" <?= $cita['estado']=="rechazada"?"selected":"" ?>>Rechazada</option>
            <option value="completada" <?= $cita['estado']=="completada"?"selected":"" ?>>Completada</option>
        </select>

        <button type="submit" name="actualizar" class="btn-outline-gold">Actualizar cita</button>

    </form>

</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
