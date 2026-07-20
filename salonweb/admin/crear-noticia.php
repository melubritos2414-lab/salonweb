<?php
session_start();
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../includes/verificar_sesion.php";
include "../includes/conexion.php";
include "../includes/nav.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Noticia</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<section class="servicio">
    <h2>Publicar Noticia</h2>
</section>

<section class="contenedor">
    <form action="" method="POST" enctype="multipart/form-data">

        <label>Título:</label>
        <input type="text" name="titulo" required>

        <label>Imagen (subir archivo):</label>
        <input type="file" name="imagen" accept="image/*" required>

        <label>Texto:</label>
        <textarea name="texto" required></textarea>

        <button type="submit" name="publicar" class="btn-noticia">Publicar</button>

    </form>

    <?php
    if (isset($_POST['publicar'])) {

        $titulo = trim($_POST['titulo']);
        $texto  = trim($_POST['texto']);
        $fecha  = date("Y-m-d");
        $idUser = $_SESSION['idUser'];

        /* ============================
           PROCESAR IMAGEN
        ============================ */
        $imagen = $_FILES['imagen']['name'];
        $tmp    = $_FILES['imagen']['tmp_name'];

        // Evitar nombres repetidos
        $nombreSeguro = time() . "_" . $imagen;
        $ruta = "../img/" . $nombreSeguro;

        move_uploaded_file($tmp, $ruta);

        /* ============================
           INSERTAR NOTICIA
        ============================ */
        $sql = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $titulo, $nombreSeguro, $texto, $fecha, $idUser);

        if ($stmt->execute()) {
            echo "<div class='mensaje-ok'>Noticia publicada correctamente.</div>";
        } else {
            echo "<div class='mensaje-error'>Error: " . $conexion->error . "</div>";
        }
    }
    ?>
</section>

<?php include "../includes/footer.php"; ?>

</body>
</html>
