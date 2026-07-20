<?php

// Sanitizar texto para evitar inyecciones
function limpiar($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

// Mostrar mensajes bonitos con clases CSS
function mensaje($texto, $tipo = "info") {

    $clases = [
        "info"  => "mensaje-info",
        "error" => "mensaje-error",
        "ok"    => "mensaje-ok"
    ];

    $clase = $clases[$tipo] ?? "mensaje-info";

    echo "<div class='$clase'>$texto</div>";
}

// Verificar si el usuario está logueado
function usuarioLogueado() {
    return isset($_SESSION['idUser']);
}

// Redirigir a otra página
function redirigir($url) {
    header("Location: $url");
    exit;
}

?>
