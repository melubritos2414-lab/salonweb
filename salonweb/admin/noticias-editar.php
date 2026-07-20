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

if (!isset($_GET['id'])) {
    header("Location: noticias-administracion.php");
    exit;
}

$idNoticia = intval($_GET['id']);

$stmt = $conexion->prepare("
    SELECT titulo, contenido, imagen
    FROM noticias
    WHERE idNoticia = ?
");
$stmt->bind_param("i", $idNoticia);
$stmt->execute();
$resultado = $stmt->get_result();
$noticia = $resultado->fetch_assoc();
$stmt->close();

if (!$noticia) {
    header("Location: noticias-administracion.php");
    exit;
}

if (isset($_POST['guardar'])) {

    $titulo = trim($_POST['titulo']);
    $contenido  = trim($_POST['contenido']);
    $imagen = $noticia['imagen'];

    if ($titulo === "" || $contenido === "") {
        $errores[] = "El título y el contenido son obligatorios.";
    }

    if (!empty($_FILES['imagen_archivo']['name'])) {

        $tipo = mime_content_type($_FILES['imagen_archivo']['tmp_name']);
        if (!str_starts_with($tipo, "image")) {
            $errores[] = "El archivo subido no es una imagen válida.";
        } else {
            $nombreArchivo = time() . "_" . basename($_FILES['imagen_archivo']['name']);
            $rutaDestino = "../img/" . $nombreArchivo;

            if (move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], $rutaDestino)) {
                $imagen = $nombreArchivo;
            } else {
                $errores[] = "Error al subir la imagen.";
            }
        }

    } elseif (!empty($_POST['imagen_url'])) {

        $url = trim($_POST['imagen_url']);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errores[] = "La URL de la imagen no es válida.";
        } else {
            $imagen = $url;
        }
    }

    if (empty($errores)) {

        $stmt = $conexion->prepare("
            UPDATE noticias
            SET titulo = ?, contenido = ?, imagen = ?
            WHERE idNoticia = ?
        ");
        $stmt->bind_param("sssi", $titulo, $contenido, $imagen, $idNoticia);

        if ($stmt->execute()) {
            header("Location: noticias-administracion.php");
            exit;
        } else {
            $errores[] = "Error al guardar los cambios.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar noticia</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1 class="titulo-admin">Editar noticia</h1>

<div class="form-admin">

<?php if (!empty($errores)): ?>
    <div class="mensaje-error">
        <?php foreach ($errores as $e): ?>
            <p><?= $e ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Título:</label>
    <input type="text" name="titulo" value="<?= $noticia['titulo'] ?>" required>

    <label>Contenido:</label>
    <textarea name="contenido" required><?= $noticia['contenido'] ?></textarea>

    <p style="color:#fff; margin-top:10px;"><strong>Imagen actual:</strong></p>

    <?php if (str_starts_with($noticia['imagen'], "http")): ?>
        <img src="<?= $noticia['imagen'] ?>" class="img-mini">
    <?php else: ?>
        <img src="../img/<?= $noticia['imagen'] ?>" class="img-mini">
    <?php endif; ?>

    <label>Subir nueva imagen (archivo):</label>
    <input type="file" name="imagen_archivo" accept="image/*">

    <label>O usar URL de imagen:</label>
    <input type="text" name="imagen_url" placeholder="https://ejemplo.com/imagen.jpg">

    <button type="submit" name="guardar" class="btn-admin">Guardar cambios</button>
</form>

<br>
<a class="btn-accion" href="noticias-administracion.php">Volver</a>

</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>
