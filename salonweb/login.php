<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/conexion.php");
include("includes/nav.php");

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === '' || $password === '') {
        $errores[] = "Debes completar todos los campos.";
    }

    if (empty($errores)) {

        $stmt = $conexion->prepare("
            SELECT users_login.idUser, users_login.password, users_login.rol, users_data.nombre
            FROM users_login
            INNER JOIN users_data ON users_login.idUser = users_data.idUser
            WHERE usuario = ?
        ");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {

            $fila = $resultado->fetch_assoc();

            if (password_verify($password, $fila['password'])) {

                // Guardar datos en sesión
                $_SESSION['idUser'] = $fila['idUser'];
                $_SESSION['rol'] = $fila['rol'];
                $_SESSION['usuario'] = $usuario;
                $_SESSION['nombre'] = $fila['nombre'];

                // Si venía de una página protegida
                if (isset($_SESSION['redirigir_a'])) {
                    $destino = $_SESSION['redirigir_a'];
                    unset($_SESSION['redirigir_a']);
                    header("Location: $destino");
                    exit;
                }

                // Redirección según rol
                if ($_SESSION['rol'] === 'admin') {
                    header("Location: admin/panel.php");
                    exit;
                } else {
                    header("Location: user/perfil.php");
                    exit;
                }

            } else {
                $errores[] = "La contraseña es incorrecta.";
            }

        } else {
            $errores[] = "El usuario no existe.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-container">

    <h2>Iniciar sesión</h2>

    <?php if (!empty($errores)): ?>
        <div class="mensaje-error">
            <?php foreach ($errores as $e): ?>
                <p><?= $e; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">

        <label>Usuario:</label>
        <input type="text" name="usuario" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn-form">Entrar</button>

        <p style="text-align:center; margin-top:10px;">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>

    </form>

</div>

<?php include("includes/footer.php"); ?>

</body>
</html>
