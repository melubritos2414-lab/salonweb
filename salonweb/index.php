<?php
session_start();
$rol = $_SESSION['rol'] ?? 'visitante';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salón de Belleza</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <ul>
            <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'activo':'' ?>">Inicio</a></li>
            <li><a href="noticias.php" class="<?= basename($_SERVER['PHP_SELF'])=='noticias.php'?'activo':'' ?>">Noticias</a></li>

            <?php if ($rol === 'visitante'): ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="registro.php">Registro</a></li>

            <?php elseif ($rol === 'user'): ?>
                <li><a href="citaciones.php">Citaciones</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>

            <?php elseif ($rol === 'admin'): ?>
                <li><a href="admin/usuarios-administracion.php">Usuarios</a></li>
                <li><a href="admin/citaciones-administracion.php">Citaciones</a></li>
                <li><a href="admin/noticias-administracion.php">Noticias Admin</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="hero">
    <div class="hero-texto">
        <h1>Bienvenida al Salón de Belleza</h1>
        <p>Tu momento, tu lugar</p>
        <p>Creamos tu look con estilo</p>
    </div>
</section>

    <!-- SERVICIOS -->
    <div class="hero-servicios">
        <div class="servicio-box tarjeta">
            <img src="img/peinado.jpg" alt="">
            <h3>Corte & Peinado</h3>
            <p>Transformamos tu look con estilo.</p>
        </div>

        <div class="servicio-box tarjeta">
            <img src="img/coloracion.jpg" alt="">
            <h3>Coloración</h3>
            <p>Mechas, balayage, tintes y más.</p>
        </div>

        <div class="servicio-box tarjeta">
            <img src="img/manicura.jpg" alt="">
            <h3>Manicura</h3>
            <p>Uñas perfectas para cada ocasión.</p>
        </div>
    </div>

    <div style="text-align:center; margin-top:25px;">
       <a href="user/crear-cita.php" class="btn">Solicito cita</a>

    </div>

    <?php include("includes/footer.php"); ?>
    <script src="js/script.js"></script>

</body>
</html>
