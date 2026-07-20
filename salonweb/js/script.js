// Confirmación de carga del script
console.log("Script cargado correctamente");

// Función para mostrar un pequeño mensaje visual al enviar formularios
document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("submit", () => {
            console.log("Formulario enviado");

            // Efecto visual rápido
            form.style.opacity = "0.6";
            form.style.transition = "0.3s";
        });
    });
});
