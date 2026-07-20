<?php
session_start();

if (!isset($_SESSION['idUser'])) {
    header("Location: ../login.php");
    exit;
}

include("../includes/nav.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1 class="titulo-admin">Panel Principal</h1>

<div class="panel-grid">

    <!-- ============================
         SECCIÓN PARA USUARIOS
         ============================ -->
    <?php if ($_SESSION['rol'] === 'user'): ?>

        <!-- MIS CITAS -->
        <a href="../user/citaciones.php" class="panel-btn">
            <i>📅</i>
            Mis Citas
            <p>Consulta tus citas programadas</p>
        </a>

        <!-- CREAR CITA -->
        <a href="../user/crear-cita.php" class="panel-btn">
            <i>✏️</i>
            Crear Nueva Cita
            <p>Agenda una nueva cita</p>
        </a>

        <!-- NOTICIAS (usuario) -->
        <a href="../user/noticias.php" class="panel-btn">
            <i>📰</i>
            Noticias
            <p>Lee las últimas novedades</p>
        </a>

    <?php endif; ?>
    <!-- ============================
         SECCIÓN PARA ADMINISTRADORES
         ============================ -->
    <?php if ($_SESSION['rol'] === 'admin'): ?>

        <!-- ADMINISTRAR USUARIOS -->
        <a href="usuarios-administracion.php" class="panel-btn">
            <i>👥</i>
            Administrar Usuarios
            <p>Gestiona cuentas y permisos</p>
        </a>

        <!-- ADMINISTRAR NOTICIAS -->
        <a href="noticias-administracion.php" class="panel-btn">
            <i>🗂️</i>
            Administrar Noticias
            <p>Edita o elimina noticias</p>
        </a>

       <!-- CITAS DE USUARIOS -->
<a href="citaciones-administracion.php" class="panel-btn">
    <i>📅</i>
    Citas de Usuarios
    <p>Ver y gestionar citas creadas por usuarios</p>
</a>

<!-- CITAS DEL ADMIN -->
<a href="citas-admin.php" class="panel-btn">
    <i>🗂️</i>
    Citas del Administrador
    <p>Ver y gestionar citas creadas por el administrador</p>
</a>


    <?php endif; ?>

    <!-- ============================
         OPCIONES COMUNES (ambos roles)
         ============================ -->

    <!-- PERFIL -->
    <a href="perfil-admin.php" class="panel-btn">
        <i>👤</i>
        Mi Perfil
        <p>Ver y editar tu información</p>
    </a>

    <!-- LOGOUT -->
    <li><a href="/salonweb/logout.php"></a></li>
    <a href="/salonweb/logout.php" class="panel-btn">
        <i>📚</i>
        cerrar sesión
        <p>Salir del panel</p>
    </a>

</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
