<?php
session_start();
include "includes/conexion.php";

// Obtener todas las noticias
$stmt = $conexion->prepare("
    SELECT idNoticia, titulo, contenido, imagen
    FROM noticias
    ORDER BY idNoticia DESC
");
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Noticias</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/nav.php"; ?>

<h1 class="titulo-admin">Noticias</h1>

<div class="noticias-grid">

    <?php while ($n = $resultado->fetch_assoc()): ?>

        <div class="noticia-card">

            <h2 class="noticia-titulo"><?= $n['titulo'] ?></h2>

            <p class="noticia-texto"><?= $n['contenido'] ?></p>

            <?php
            $img = $n['imagen'];

            // Detectar si es URL externa
            if (strpos($img, "http") === 0) {
                $rutaImagen = $img;
            }
            // Detectar si es imagen subida por usuario (uploads/)
            else if (strpos($img, "uploads/") === 0) {
                $rutaImagen = "/salonweb/" . $img;
            }
            // Detectar si es imagen antigua del admin (carpeta img/)
            else {
                $rutaImagen = "/salonweb/img/" . $img;
            }
            ?>

            <?php if (!empty($img)): ?>
                <img class="noticia-img" src="<?= $rutaImagen ?>" alt="Imagen de la noticia">
            <?php endif; ?>

        </div>

    <?php endwhile; ?>

</div>

<?php include "includes/footer.php"; ?> 

</body>
</html>
