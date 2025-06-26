// Función para mostrar el loader
function showLoader() {
    const loader = document.querySelector('.loader-wrapper');
    if (loader) {
        loader.classList.remove('hidden');
    }
}

// Función para ocultar el loader
function hideLoader() {
    const loader = document.querySelector('.loader-wrapper');
    if (loader) {
        loader.classList.add('hidden');
    }
}

// Mostrar loader cuando se inicia la carga de la página
document.addEventListener('DOMContentLoaded', function() {
    showLoader();
});

// Ocultar loader cuando la página está completamente cargada
window.addEventListener('load', function() {
    hideLoader();
});

// Interceptar todas las navegaciones AJAX para mostrar/ocultar el loader
document.addEventListener('click', function(e) {
    // Si el clic es en un enlace
    if (e.target.tagName === 'A' || e.target.closest('a')) {
        showLoader();
    }
});

// Interceptar envíos de formularios
document.addEventListener('submit', function(e) {
    showLoader();
});

// Interceptar llamadas AJAX
let originalFetch = window.fetch;
window.fetch = function() {
    showLoader();
    return originalFetch.apply(this, arguments)
        .then(response => {
            hideLoader();
            return response;
        })
        .catch(error => {
            hideLoader();
            throw error;
        });
};

// Intercept all link clicks to show loader, except for Bootstrap collapse links

document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function (e) {
        // NO mostrar loader si es un enlace de colapso de Bootstrap o href empieza con #
        if (
            link.getAttribute('data-bs-toggle') === 'collapse' ||
            (link.getAttribute('href') && link.getAttribute('href').startsWith('#'))
        ) {
            // Si por alguna razón el loader se muestra, lo ocultamos
            setTimeout(hideLoader, 100);
            return;
        }
        // Solo mostrar loader si el link realmente navega a otra página
        showLoader();
    });
});
