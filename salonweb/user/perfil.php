<?php
session_start();

include("../includes/conexion.php");
include("../includes/nav.php");

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$idUser = $_SESSION['idUser'];

// OBTENER DATOS DEL USUARIO
$sql = $conexion->prepare("SELECT * FROM users_data WHERE idUser = ?");
$sql->bind_param("i", $idUser);
$sql->execute();
$result = $sql->get_result();
$usuario = $result->fetch_assoc();

// MENSAJES
$mensaje = "";
$mensajePass = "";

// ACTUALIZAR DATOS PERSONALES
if (isset($_POST['guardar_datos'])) {

    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $sexo = $_POST['sexo'];

    $update = $conexion->prepare("UPDATE users_data 
        SET nombre=?, apellidos=?, telefono=?, fecha_nacimiento=?, direccion=?, sexo=?
        WHERE idUser=?");

    $update->bind_param("ssssssi", $nombre, $apellidos, $telefono, $fecha_nacimiento, $direccion, $sexo, $idUser);

    if ($update->execute()) {
        $mensaje = "Datos actualizados correctamente.";
    } else {
        $mensaje = "Error al actualizar los datos.";
    }
}

// CAMBIAR CONTRASEÑA
if (isset($_POST['cambiar_pass'])) {

    $pass_actual = $_POST['pass_actual'];
    $pass_nueva = $_POST['pass_nueva'];
    $pass_confirmar = $_POST['pass_confirmar'];

    $sqlPass = $conexion->prepare("SELECT password FROM users_login WHERE idUser = ?");
    $sqlPass->bind_param("i", $idUser);
    $sqlPass->execute();
    $resPass = $sqlPass->get_result()->fetch_assoc();

    if (!password_verify($pass_actual, $resPass['password'])) {
        $mensajePass = "La contraseña actual es incorrecta.";
    } elseif ($pass_nueva != $pass_confirmar) {
        $mensajePass = "Las contraseñas nuevas no coinciden.";
    } else {

        $passHash = password_hash($pass_nueva, PASSWORD_DEFAULT);

        $updatePass = $conexion->prepare("UPDATE users_login SET password=? WHERE idUser=?");
        $updatePass->bind_param("si", $passHash, $idUser);

        if ($updatePass->execute()) {
            $mensajePass = "Contraseña actualizada correctamente.";
        } else {
            $mensajePass = "Error al actualizar la contraseña.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="perfil-wrapper">

    <h2 class="titulo-perfil">Mi Perfil</h2>

    <!-- MENSAJES -->
    <?php if ($mensaje != ""): ?>
        <div class="mensaje-exito"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($mensajePass != ""): ?>
        <div class="mensaje-error"><?= $mensajePass ?></div>
    <?php endif; ?>

    <!-- TARJETA DE INFORMACIÓN -->
    <div class="perfil-card">
        <h3>Información Personal</h3>

        <form method="POST">

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>

            <label>Apellidos:</label>
            <input type="text" name="apellidos" value="<?= $usuario['apellidos'] ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?= $usuario['telefono'] ?>">

            <label>Fecha de nacimiento:</label>
            <input type="date" name="fecha_nacimiento" value="<?= $usuario['fecha_nacimiento'] ?>">

            <label>Dirección:</label>
            <input type="text" name="direccion" value="<?= $usuario['direccion'] ?>">

            <label>Sexo:</label>
            <select name="sexo">
                <option value="M" <?= $usuario['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= $usuario['sexo'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                <option value="O" <?= $usuario['sexo'] == 'O' ? 'selected' : '' ?>>Otro</option>
            </select>

            <button type="submit" name="guardar_datos" class="btn-form">Guardar cambios</button>
        </form>
    </div>

    <!-- CAMBIAR CONTRASEÑA -->
    <div class="perfil-card">
        <h3>Cambiar Contraseña</h3>

        <form method="POST">

            <label>Contraseña actual:</label>
            <input type="password" name="pass_actual" required>

            <label>Nueva contraseña:</label>
            <input type="password" name="pass_nueva" required>

            <label>Confirmar nueva contraseña:</label>
            <input type="password" name="pass_confirmar" required>

            <button type="submit" name="cambiar_pass" class="btn-form">Actualizar contraseña</button>
        </form>
    </div>

</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
