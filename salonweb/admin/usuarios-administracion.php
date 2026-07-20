<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/conexion.php";
include "../includes/nav.php";

$errores = [];
$exito = "";

/* ============================================================
   CREAR USUARIO (ADMIN)
   ============================================================ */
if (isset($_POST['crear'])) {

    $nombre            = trim($_POST['nombre']);
    $apellidos         = trim($_POST['apellidos']);
    $email             = trim($_POST['email']);
    $telefono          = trim($_POST['telefono']);
    $fecha_nacimiento  = trim($_POST['fecha_nacimiento']);
    $direccion         = trim($_POST['direccion']);
    $sexo              = trim($_POST['sexo']);

    $usuario           = trim($_POST['usuario']);
    $password_plain    = trim($_POST['password']);
    $rol               = trim($_POST['rol']);

    // Validaciones
    if ($nombre === "" || $apellidos === "" || $email === "" || $telefono === "" ||
        $fecha_nacimiento === "" || $usuario === "" || $password_plain === "") {
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

    if (!preg_match("/^[0-9]{9}$/", $telefono)) {
        $errores[] = "El teléfono debe contener 9 números.";
    }

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_nacimiento)) {
        $errores[] = "La fecha de nacimiento no tiene un formato válido.";
    }

    // SEXO corregido
    $sexos_validos = ["Hombre", "Mujer", "Otro"];
    if (!in_array($sexo, $sexos_validos)) {
        $errores[] = "El sexo seleccionado no es válido.";
    }

    if (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        $errores[] = "El nombre de usuario solo puede contener letras y números.";
    }

    // Si no hay errores
    if (empty($errores)) {

        // Email duplicado
        $stmt = $conexion->prepare("SELECT idUser FROM users_data WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errores[] = "Ya existe un usuario con ese email.";
        }
        $stmt->close();

        // Usuario duplicado (corregido)
        $stmt = $conexion->prepare("SELECT idUser FROM users_login WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errores[] = "El nombre de usuario ya está en uso.";
        }
        $stmt->close();

        if (empty($errores)) {

            // 1️⃣ Insertar primero en users_login
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

            $stmt = $conexion->prepare("
                INSERT INTO users_login (usuario, password, rol)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $usuario, $password_hash, $rol);

            if ($stmt->execute()) {

                // Obtener idUser generado
                $idUser = $conexion->insert_id;
                $stmt->close();

                // 2️⃣ Insertar en users_data
                $stmt2 = $conexion->prepare("
                    INSERT INTO users_data (idUser, nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt2->bind_param("isssssss", $idUser, $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo);

                if ($stmt2->execute()) {
                    $exito = "Usuario creado correctamente.";
                } else {
                    $errores[] = "Error al registrar los datos personales.";
                }

                $stmt2->close();

            } else {
                $errores[] = "Error al registrar los datos de inicio de sesión.";
            }
        }
    }
}

/* ============================================================
   BORRAR USUARIO
   ============================================================ */
if (isset($_GET['borrar'])) {

    $idUser = $_GET['borrar'];

    if ($idUser == 1) {
        $errores[] = "No puedes borrar el administrador principal.";
    } else {

        $stmt = $conexion->prepare("DELETE FROM users_login WHERE idUser = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $stmt->close();

        $stmt = $conexion->prepare("DELETE FROM users_data WHERE idUser = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $stmt->close();

        $exito = "Usuario eliminado correctamente.";
    }

    header("Location: usuarios-administracion.php");
    exit;
}

/* ============================================================
   OBTENER USUARIOS
   ============================================================ */
$usuarios = $conexion->query("
    SELECT d.idUser, d.nombre, d.apellidos, d.email, l.usuario, l.rol
    FROM users_data d
    INNER JOIN users_login l ON d.idUser = l.idUser
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">

<h1>Administración de Usuarios</h1>

<?php if (!empty($errores)): ?>
    <div class="mensaje-error">
        <?php foreach ($errores as $e): ?>
            <p><?= $e ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($exito): ?>
    <div class="mensaje-ok">
        <?= $exito ?>
    </div>
<?php endif; ?>

<h2>Crear nuevo usuario</h2>

<form method="POST" class="admin-form">

    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <label>Apellidos:</label>
    <input type="text" name="apellidos" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" required>

    <label>Fecha de nacimiento:</label>
    <input type="date" name="fecha_nacimiento" required>

    <label>Dirección:</label>
    <input type="text" name="direccion">

    <label>Sexo:</label>
    <select name="sexo" required>
        <option value="Hombre">Hombre</option>
        <option value="Mujer">Mujer</option>
        <option value="Otro">Otro</option>
    </select>

    <label>Usuario:</label>
    <input type="text" name="usuario" required>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <label>Rol:</label>
    <select name="rol">
        <option value="user">Usuario</option>
        <option value="admin">Administrador</option>
    </select>

    <button type="submit" name="crear">Crear usuario</button>
</form>

<h2>Usuarios registrados</h2>

<div class="usuarios-grid">

<?php while ($u = $usuarios->fetch_assoc()): ?>
    <div class="usuario-card">
        <h2><?= $u['nombre'] ?> <?= $u['apellidos'] ?></h2>
        <p><strong>Email:</strong> <?= $u['email'] ?></p>
        <p><strong>Usuario:</strong> <?= $u['usuario'] ?></p>
        <p><strong>Rol:</strong> <?= $u['rol'] ?></p>

        <div class="acciones-usuario">
            <a class="btn-editar" href="usuarios-editar.php?id=<?= $u['idUser'] ?>">Editar</a>
            <a class="btn-eliminar" href="usuarios-administracion.php?borrar=<?= $u['idUser'] ?>"
               onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">
               Borrar
            </a>
        </div>
    </div>
<?php endwhile; ?>

</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>
