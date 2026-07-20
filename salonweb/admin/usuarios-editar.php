<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/conexion.php";

// Comprobar si llega el ID del usuario
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuarios-administracion.php");
    exit;
}

$idUser = intval($_GET['id']);

// Obtener datos actuales del usuario
$stmt = $conexion->prepare("
    SELECT d.nombre, d.apellidos, d.email, l.usuario, l.rol
    FROM users_data d
    INNER JOIN users_login l ON d.idUser = l.idUser
    WHERE d.idUser = ?
");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header("Location: usuarios-administracion.php");
    exit;
}

$errores = [];
$exito = "";

// Guardar cambios
if (isset($_POST['guardar'])) {

    $nombre       = trim($_POST['nombre']);
    $apellidos    = trim($_POST['apellidos']);
    $email        = trim($_POST['email']);
    $usuarioLogin = trim($_POST['usuario']);
    $rol          = trim($_POST['rol']);

    // Validaciones
    if ($nombre === "" || $apellidos === "" || $email === "" || $usuarioLogin === "") {
        $errores[] = "Todos los campos obligatorios deben completarse.";
    }

    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
        $errores[] = "El nombre solo puede contener letras.";
    }

    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $apellidos)) {
        $errores[] = "Los apellidos solo pueden contener letras.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no tiene un formato válido.";
    }

    if (!preg_match("/^[a-zA-Z0-9]+$/", $usuarioLogin)) {
        $errores[] = "El nombre de usuario solo puede contener letras y números.";
    }

    if (!in_array($rol, ["user", "admin"])) {
        $errores[] = "El rol seleccionado no es válido.";
    }

    // Evitar degradar al admin principal
    if ($idUser == 1 && $rol !== "admin") {
        $errores[] = "No puedes cambiar el rol del administrador principal.";
    }

    // Si no hay errores, actualizar
    if (empty($errores)) {

        // Actualizar users_data
        $stmt = $conexion->prepare("
            UPDATE users_data 
            SET nombre = ?, apellidos = ?, email = ?
            WHERE idUser = ?
        ");
        $stmt->bind_param("sssi", $nombre, $apellidos, $email, $idUser);
        $stmt->execute();
        $stmt->close();

        // Actualizar users_login
        $stmt = $conexion->prepare("
            UPDATE users_login
            SET usuario = ?, rol = ?
            WHERE idUser = ?
        ");
        $stmt->bind_param("ssi", $usuarioLogin, $rol, $idUser);
        $stmt->execute();
        $stmt->close();

        header("Location: usuarios-administracion.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">

<h1>Editar usuario</h1>

<?php if (!empty($errores)): ?>
    <div class="mensaje-error">
        <?php foreach ($errores as $e): ?>
            <p><?= $e ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" class="admin-form">

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>

    <label>Apellidos:</label>
    <input type="text" name="apellidos" value="<?= $usuario['apellidos'] ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $usuario['email'] ?>" required>

    <label>Usuario:</label>
    <input type="text" name="usuario" value="<?= $usuario['usuario'] ?>" required>

    <label>Rol:</label>
    <select name="rol">
        <option value="user" <?= $usuario['rol'] === 'user' ? 'selected' : '' ?>>Usuario</option>
        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
    </select>

    <button type="submit" name="guardar">Guardar cambios</button>
</form>

<br>
<a class="btn-accion" href="usuarios-administracion.php">Volver</a>

</div>

</body>
</html>
