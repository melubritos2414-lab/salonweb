<?php
session_start();

// Solo admins pueden entrar
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/conexion.php";
include "../includes/nav.php";

// ID del admin logueado
$id = $_SESSION['idUser'];

// Obtener datos personales desde users_data
$stmt = $conexion->prepare("SELECT nombre, apellidos, email, telefono FROM users_data WHERE idUser = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$admin = $resultado->fetch_assoc();

// Obtener usuario desde users_login
$stmt2 = $conexion->prepare("SELECT usuario FROM users_login WHERE idUser = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$resultado2 = $stmt2->get_result();
$login = $resultado2->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil (Admin)</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1 class="titulo-admin">Mi Perfil (Administrador)</h1>

<div class="form-admin">

    <form action="perfil-admin-actualizar.php" method="POST">

        <label>Usuario (login):</label>
        <input type="text" class="input-gold" value="<?= $login['usuario'] ?>" disabled>

        <label>Nombre:</label>
        <input type="text" name="nombre" class="input-gold" value="<?= $admin['nombre'] ?>" required>

        <label>Apellidos:</label>
        <input type="text" name="apellidos" class="input-gold" value="<?= $admin['apellidos'] ?>" required>

        <label>Email:</label>
        <input type="email" name="email" class="input-gold" value="<?= $admin['email'] ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" class="input-gold" value="<?= $admin['telefono'] ?>">

        <button type="submit" class="btn-admin">Actualizar perfil</button>

    </form>

</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>
