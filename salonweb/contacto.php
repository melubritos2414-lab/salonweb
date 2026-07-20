<?php 
include("includes/conexion.php");
include("includes/nav.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="contacto-fondo">

<!-- BANNER CONTACTO -->
<section class="servicio">
    <h2>Contacto</h2>
</section>

<!-- INFORMACIÓN DE CONTACTO -->
<section class="contenedor">

    <p style="text-align:center; font-size:18px;">
        Estamos aquí para ayudarte. Escríbenos o visítanos.
    </p>

    <ul style="width:60%; margin:auto; font-size:18px; list-style:none; padding:0;">
        <li><strong>Teléfono:</strong> 123 456 789</li>
        <li><strong>Email:</strong> contacto@salonweb.com</li>
        <li><strong>Dirección:</strong> Calle Belleza 123, Bilbao</li>
    </ul>

</section>

<!-- FORMULARIO DE CONTACTO (solo visual) -->
<section class="contenedor">
    <form action="" method="POST">

        <label>Tu nombre:</label>
        <input type="text" name="nombre" required>

        <label>Tu email:</label>
        <input type="email" name="email" required>

        <label>Mensaje:</label>
        <textarea name="mensaje" required></textarea>

        <button type="submit" name="enviar">Enviar</button>

    </form>

    <?php
    if (isset($_POST['enviar'])) {
        echo "<p style='color:green; text-align:center; margin-top:15px;'>Mensaje enviado (simulado).</p>";
    }
    ?>
</section>

<?php include("includes/footer.php"); ?>

<script src="js/script.js"></script>
</body>
</html>
