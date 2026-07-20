<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina = basename($_SERVER['PHP_SELF']);

// Detectar si estamos en /admin
$base = "";

if (strpos($_SERVER['PHP_SELF'], "/admin/") !== false) {
    $base = "../";
} else {
    $base = "";
}
?>

<nav class="navbar">
    <ul>

        <!-- PÁGINAS PÚBLICAS (si NO hay sesión) -->
        <?php if (!isset($_SESSION['idUser'])): ?>
            <li><a href="/salonweb/index.php" class="<?= $pagina == 'index.php' ? 'activo' : '' ?>">Inicio</a></li>
            <li><a href="/salonweb/noticias.php" class="<?= $pagina == 'noticias.php' ? 'activo' : '' ?>">Noticias</a></li>
            <li><a href="/salonweb/login.php" class="<?= $pagina == 'login.php' ? 'activo' : '' ?>">Login</a></li>
            <li><a href="/salonweb/registro.php" class="<?= $pagina == 'registro.php' ? 'activo' : '' ?>">Registro</a></li>
        <?php endif; ?>

        <!-- USUARIO NORMAL -->
        <?php if (isset($_SESSION['idUser']) && $_SESSION['rol'] === 'user'): ?>
            <li><a href="/salonweb/index.php" class="<?= $pagina == 'index.php' ? 'activo' : '' ?>">Inicio</a></li>
            <li><a href="/salonweb/noticias.php" class="<?= $pagina == 'noticias.php' ? 'activo' : '' ?>">Noticias</a></li>
            <li><a href="/salonweb/user/citaciones.php" class="<?= $pagina == 'citaciones.php' ? 'activo' : '' ?>">Mis Citas</a></li>
            <li><a href="/salonweb/user/perfil.php" class="<?= $pagina == 'perfil.php' ? 'activo' : '' ?>">Perfil</a></li>
            <li><a href="/salonweb/user/crear-noticia.php" class="<?= $pagina == 'crear-noticia.php' ? 'activo' : '' ?>">Crear noticia</a></li>

        <?php endif; ?>

        <!-- ADMIN -->
        <?php if (isset($_SESSION['idUser']) && $_SESSION['rol'] === 'admin'): ?>
            <li><a href="/salonweb/admin/panel.php" class="<?= $pagina == 'panel.php' ? 'activo' : '' ?>">Panel</a></li>
            <li><a href="/salonweb/admin/usuarios-administracion.php" class="<?= $pagina == 'usuarios-administracion.php' ? 'activo' : '' ?>">Usuarios</a></li>
            <li><a href="/salonweb/admin/citaciones-administracion.php" class="<?= $pagina == 'citaciones-administracion.php' ? 'activo' : '' ?>">Citaciones</a></li>
            <li><a href="/salonweb/admin/noticias-administracion.php" class="<?= $pagina == 'noticias-administracion.php' ? 'activo' : '' ?>">Noticias Admin</a></li>
        <?php endif; ?>

        <!-- CERRAR SESIÓN (solo si hay sesión) -->
        <?php if (isset($_SESSION['idUser'])): ?>
            <li><a href="/salonweb/logout.php">Cerrar sesión</a></li>
        <?php endif; ?>

    </ul>
</nav>
