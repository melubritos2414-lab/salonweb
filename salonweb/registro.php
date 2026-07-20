<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/conexion.php");
include("includes/nav.php");

$errores = [];
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre      = trim($_POST['nombre'] ?? '');
    $apellidos   = trim($_POST['apellidos'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $telefono    = trim($_POST['telefono'] ?? '');
    $fecha_nac   = trim($_POST['fecha_nac'] ?? '');
    $direccion   = trim($_POST['direccion'] ?? '');
    $sexo        = trim($_POST['sexo'] ?? '');

    $usuario     = trim($_POST['usuario'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $password2   = trim($_POST['password2'] ?? '');

    // Validaciones
    if ($nombre === '' || $apellidos === '' || $email === '' || $telefono === '' || 
        $fecha_nac === '' || $usuario === '' || $password === '' || $password2 === '') {
        $errores[] = "Todos los campos marcados como obligatorios deben completarse.";
    }

    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
        $errores[] = "El nombre solo puede contener letras.";
    }

    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $apellidos)) {
        $errores[] = "Los apellidos solo pueden contener letras.";
    }

    if (!preg_match("/^[0-9]{9}$/", $telefono)) {
        $errores[] = "El teléfono debe contener 9 números.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no tiene un formato válido.";
    }

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_nac)) {
        $errores[] = "La fecha de nacimiento no tiene un formato válido.";
    }

    // Validación de dirección
    if ($direccion !== "" && strlen($direccion) < 3) {
        $errores[] = "La dirección es demasiado corta.";
    }

    // Validación de sexo
    $sexos_validos = ["Mujer", "Hombre", "Otro"];
    if (!in_array($sexo, $sexos_validos)) {
        $errores[] = "El sexo seleccionado no es válido.";
    }

    // Validación de usuario
    if (!preg_match("/^[a-zA-Z0-9]+$/", $usuario)) {
        $errores[] = "El nombre de usuario solo puede contener letras y números.";
    }

    // Validación de contraseña
    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener mínimo 6 caracteres.";
    }

    // Si no hay errores, continuar
    if (empty($errores)) {

        // Verificar email duplicado
        $stmt = $conexion->prepare("SELECT idUser FROM users_data WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores[] = "Ya existe un usuario registrado con ese email.";
        }
        $stmt->close();

        // Verificar usuario duplicado
        $stmt = $conexion->prepare("SELECT idUser FROM users_login WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores[] = "El nombre de usuario ya está en uso.";
        }
        $stmt->close();

        // Si sigue sin errores, insertar
        if (empty($errores)) {

            // INSERTAR EN users_login
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $rol = "user";

            $stmt = $conexion->prepare("
                INSERT INTO users_login (usuario, password, rol)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $usuario, $password_hash, $rol);

            if ($stmt->execute()) {

                // Obtener idUser generado
                $idUser = $conexion->insert_id;
                $stmt->close();

                // INSERTAR EN users_data
                $stmt2 = $conexion->prepare("
                    INSERT INTO users_data (idUser, nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt2->bind_param("isssssss", $idUser, $nombre, $apellidos, $email, $telefono, $fecha_nac, $direccion, $sexo);

                if ($stmt2->execute()) {

                    echo "<div class='mensaje-ok' style='text-align:center; padding:10px; color:green; font-weight:bold;'>
                            Registro completado correctamente. Serás redirigido al login en unos segundos.
                          </div>";

                    echo "<script>
                            setTimeout(function(){
                                window.location.href = 'login.php';
                            }, 2000);
                          </script>";

                    exit;

                } else {
                    $errores[] = "Error al registrar los datos personales.";
                }

            } else {
                $errores[] = "Error al registrar los datos de inicio de sesión.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-container-registro">
    <h2>Crear una cuenta</h2>

    <?php if (!empty($errores)): ?>
        <div class="mensaje-error">
            <?php foreach ($errores as $e): ?>
                <p><?= $e; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">

        <label>Nombre*:</label>
        <input type="text" name="nombre" required>

        <label>Apellidos*:</label>
        <input type="text" name="apellidos" required>

        <label>Email*:</label>
        <input type="email" name="email" required>

        <label>Teléfono*:</label>
        <input type="text" name="telefono" required>

        <label>Fecha de nacimiento*:</label>
        <input type="date" name="fecha_nac" required>

        <label>Dirección:</label>
        <input type="text" name="direccion">

        <div class="sexo-container">
            <input type="radio" id="sexo_mujer" name="sexo" value="Mujer" required>
            <label for="sexo_mujer" class="sexo-btn">Mujer</label>

            <input type="radio" id="sexo_hombre" name="sexo" value="Hombre">
            <label for="sexo_hombre" class="sexo-btn">Hombre</label>

            <input type="radio" id="sexo_otro" name="sexo" value="Otro">
            <label for="sexo_otro" class="sexo-btn">Otro</label>
        </div>

        <hr>

        <label>Usuario*:</label>
        <input type="text" name="usuario" required>

        <label>Contraseña*:</label>
        <input type="password" name="password" required>

        <label>Repetir contraseña*:</label>
        <input type="password" name="password2" required>

        <button type="submit">Registrarme</button>

        <p>
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>

    </form>
</div>

<?php include("includes/footer.php"); ?>

</body>
</html>
