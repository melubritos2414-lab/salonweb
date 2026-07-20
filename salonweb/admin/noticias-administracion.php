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
   CREAR NOTICIA
   ============================================================ */
if (isset($_POST['crear'])) {

    $titulo = trim($_POST['titulo']);
    $contenido  = trim($_POST['contenido']);
    $imagen = "noticias1.jpg"; // imagen por defecto

    if ($titulo === "" || $contenido === "") {
        $errores[] = "El título y el contenido son obligatorios.";
    }

    /* ============================================================
       MANEJO DE IMAGEN
       ============================================================ */

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

    /* ============================================================
       INSERTAR NOTICIA
       ============================================================ */
    if (empty($errores)) {

        $stmt = $conexion->prepare("
            INSERT INTO noticias (titulo, contenido, imagen)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $titulo, $contenido, $imagen);

        if ($stmt->execute()) {
            $exito = "Noticia creada correctamente.";
        } else {
            $errores[] = "Error al crear la noticia.";
        }

        $stmt->close();
    }
}

/* ============================================================
   BORRAR NOTICIA
   ============================================================ */
if (isset($_GET['borrar'])) {

    $id = intval($_GET['borrar']);

    $stmt = $conexion->prepare("DELETE FROM noticias WHERE idNoticia = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $exito = "Noticia eliminada.";
    } else {
        $errores[] = "Error al eliminar la noticia.";
    }

    $stmt->close();
}

/* ============================================================
   OBTENER NOTICIAS
   ============================================================ */
$noticias = $conexion->query("SELECT * FROM noticias ORDER BY idNoticia DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Noticias</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">

<h1>Administración de Noticias</h1>

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

<h2>Crear nueva noticia</h2>

<form method="POST" class="admin-form" enctype="multipart/form-data">

    <label>Título:</label>
    <input type="text" name="titulo" required>

    <label>Contenido:</label>
    <textarea name="contenido" required></textarea>

    <label>Subir imagen (archivo):</label>
    <input type="file" name="imagen_archivo" accept="image/*">

    <label>O usar URL de imagen:</label>
    <input type="text" name="imagen_url" placeholder="https://ejemplo.com/imagen.jpg">

    <button type="submit" name="crear">Crear noticia</button>
</form>

<hr>

<h2>Noticias existentes</h2>

<table class="admin-table">
    <tr>
        <th>Título</th>
        <th>Contenido</th>
        <th>Imagen</th>
        <th>Acciones</th>
    </tr>

    <?php while ($n = $noticias->fetch_assoc()): ?>
    <tr>
        <td><?= $n['titulo'] ?></td>
        <td class="texto-corto"><?= $n['contenido'] ?></td>

        <td>
            <?php if (str_starts_with($n['imagen'], "http")): ?>
                <img src="<?= $n['imagen'] ?>" class="img-mini">
            <?php else: ?>
                <img src="../img/<?= $n['imagen'] ?>" class="img-mini">
            <?php endif; ?>
        </td>

        <td>
            <a class="btn-accion" href="noticias-editar.php?id=<?= $n['idNoticia'] ?>">Editar</a>
            <a class="btn-accion" href="noticias-administracion.php?borrar=<?= $n['idNoticia'] ?>" onclick="return confirm('¿Seguro?')">Borrar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>
