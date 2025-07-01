// Función para mostrar el loader
function showLoader() {
    const loader = document.querySelector('.loader-wrapper');
    if (loader) loader.classList.remove('hidden');
}

// Función para ocultar el loader
function hideLoader() {
    const loader = document.querySelector('.loader-wrapper');
    if (loader) loader.classList.add('hidden');
}

// Oculta el loader cuando la página está completamente cargada
window.addEventListener('load', hideLoader);

// Muestra el loader al enviar formularios
document.addEventListener('submit', function(e) {
    showLoader();
});

// Muestra el loader al hacer clic en enlaces que navegan
document.addEventListener('click', function(e) {
    const link = e.target.closest('a');
    if (
        link &&
        !link.getAttribute('data-bs-toggle') &&
        !(link.getAttribute('href') && link.getAttribute('href').startsWith('#')) &&
        link.target !== '_blank' // <-- No mostrar loader si abre en nueva pestaña
    ) {
        showLoader();
    }
});

// Intercepta fetch para AJAX
if (window.fetch) {
    const originalFetch = window.fetch;
    window.fetch = function() {
        showLoader();
        return originalFetch.apply(this, arguments)
            .finally(hideLoader);
    };
}
