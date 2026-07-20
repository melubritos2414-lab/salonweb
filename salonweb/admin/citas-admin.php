<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../includes/conexion.php");
include("../includes/nav.php");

// ===============================
// INSERTAR NUEVA CITA DEL ADMIN
// ===============================
if (isset($_POST['crear'])) {

    $nombre = $_POST['nombre_cliente'];
    $apellido = $_POST['apellido_cliente'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $servicio = $_POST['servicio'];
    $fecha = $_POST['fecha_citas'];
    $hora = $_POST['hora'];

    // Estado inicial
    $estado = "pendiente";

    $sql = $conexion->prepare("
        INSERT INTO citas_admin 
        (nombre_cliente, apellido_cliente, telefono, email, servicio, fecha_citas, hora, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $sql->bind_param("ssssssss", $nombre, $apellido, $telefono, $email, $servicio, $fecha, $hora, $estado);

    if ($sql->execute()) {
        echo "<p style='color:green; text-align:center;'>Cita creada correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location = 'citas-admin.php';
                }, 1500);
              </script>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $conexion->error . "</p>";
    }
}

// ===============================
// OBTENER TODAS LAS CITAS DEL ADMIN
// ===============================
$stmt = $conexion->prepare("SELECT * FROM citas_admin ORDER BY fecha_citas ASC, hora ASC");
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas del Administrador</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1 class="titulo-admin">Citas del Administrador</h1>

<!-- ===============================
     FORMULARIO PARA CREAR CITA
     =============================== -->
<h2 class="titulo-admin">Crear nueva cita (Administrador)</h2>

<div class="form-admin">

    <form method="POST">

        <label>Nombre del cliente:</label>
        <input type="text" name="nombre_cliente" class="input-gold" required>

        <label>Apellido del cliente:</label>
        <input type="text" name="apellido_cliente" class="input-gold" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" class="input-gold" required>

        <label>Email:</label>
        <input type="email" name="email" class="input-gold" required>

        <label>Servicio:</label>
        <input type="text" name="servicio" class="input-gold" required>

        <label>Fecha de la cita:</label>
        <input type="date" name="fecha_citas" class="input-gold" required>

        <label>Hora:</label>
        <input type="time" name="hora" class="input-gold" required>

        <button type="submit" name="crear" class="btn-outline-gold">Crear cita</button>

    </form>

</div>

<br><hr><br>

<!-- ===============================
     TABLA DE CITAS DEL ADMIN
     =============================== -->

<table class="tabla-admin">
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Servicio</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

    <?php while ($cita = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $cita['idCita_admin'] ?></td>
        <td><?= $cita['nombre_cliente'] . " " . $cita['apellido_cliente'] ?></td>
        <td><?= $cita['telefono'] ?></td>
        <td><?= $cita['email'] ?></td>
        <td><?= $cita['servicio'] ?></td>
        <td><?= $cita['fecha_citas'] ?></td>
        <td><?= $cita['hora'] ?></td>
        <td><?= $cita['estado'] ?></td>

        <td>
            <a href="aceptar-cita.php?id=<?= $cita['idCita_admin'] ?>" class="btn-admin">Aceptar</a>
            <a href="editar-cita.php?id=<?= $cita['idCita_admin'] ?>" class="btn-admin">Editar</a>
            <a href="borrar-cita.php?id=<?= $cita['idCita_admin'] ?>" class="btn-admin" style="background:red;">Borrar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include("../includes/footer.php"); ?>

</body>
</html>
