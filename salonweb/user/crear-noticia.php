<?php
session_start();

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include("../includes/conexion.php");
include("../includes/nav.php");

$errores = [];
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $url_imagen = trim($_POST['url_imagen'] ?? '');

    if ($titulo === "" || $contenido === "") {
        $errores[] = "El título y el contenido son obligatorios.";
    }

    if ($url_imagen !== "" && !filter_var($url_imagen, FILTER_VALIDATE_URL)) {
        $errores[] = "La URL de la imagen no es válida.";
    }

    $imagen_subida = "";
    if (!empty($_FILES['imagen']['name'])) {

        $nombreArchivo = $_FILES['imagen']['name'];
        $rutaTemp = $_FILES['imagen']['tmp_name'];

        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $permitidas = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($extension, $permitidas)) {
            $errores[] = "La imagen debe ser JPG, JPEG, PNG o GIF.";
        } else {
            $imagen_subida = "uploads/" . uniqid() . "." . $extension;
            move_uploaded_file($rutaTemp, "../" . $imagen_subida);
        }
    }

    if (empty($errores)) {

        $imagen_final = $imagen_subida !== "" ? $imagen_subida : $url_imagen;

        $stmt = $conexion->prepare("
            INSERT INTO noticias (titulo, contenido, imagen, idUser)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sssi", $titulo, $contenido, $imagen_final, $_SESSION['idUser']);

        if ($stmt->execute()) {
            $exito = "Noticia creada correctamente.";
        } else {
            $errores[] = "Error al guardar la noticia.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear noticia</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class='form-container-noticia'>
    <h2>Crear noticia</h2>

    <?php if (!empty($errores)): ?>
        <div class='mensaje-error'>
            <?php foreach ($errores as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class='mensaje-ok'>
            <?= $exito ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">

        <label>Título:</label>
        <input type="text" name="titulo" required>

        <label>Contenido:</label>
        <textarea name="contenido" required></textarea>

        <label>Imagen desde URL:</label>
        <input type="text" name="url_imagen" placeholder="https://...">

        <label>Subir imagen desde tu ordenador:</label>
        <input type="file" name="imagen">

        <button type="submit">Publicar noticia</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
