<?php 
include("includes/conexion.php");
include("includes/nav.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- FONDO DE SERVICIOS -->
<section class="servicio">
    <h2>Servicios del Salón</h2>
</section>

<!-- TARJETAS DE SERVICIOS -->
<section class="destacados contenedor">

    <div class="tarjetas">

        <div class="tarjeta">
            <h3>Corte & Peinado</h3>
            <p>Estilos modernos, clásicos y personalizados.</p>
        </div>

        <div class="tarjeta">
            <h3>Coloración</h3>
            <p>Balayage, mechas, tintes y tratamientos de color.</p>
        </div>

        <div class="tarjeta">
            <h3>Manicura & Pedicura</h3>
            <p>Uñas perfectas con técnicas profesionales.</p>
        </div>

        <div class="tarjeta">
            <h3>Tratamientos Faciales</h3>
            <p>Cuidado de la piel con productos premium.</p>
        </div>

    </div>

</section>

<?php include("includes/footer.php"); ?>

<script src="js/script.js"></script>
</body>
</html>
