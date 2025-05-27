// Script para la vista previa del banner en profile.php
$(document).ready(function() {
    // Vista previa de la imagen del banner al seleccionar un archivo
    $('#banner_image').change(function(){
        let reader = new FileReader();

        reader.onload = (e) => {
            $('.banner-preview .main-banner').css('background-image', 'url(' + e.target.result + ')');
        }
        reader.readAsDataURL(this.files[0]);
    });

    // Actualizar texto de vista previa al escribir en los campos
    $('#banner_title').on('input', function() {
        $('.banner-preview h3').text($(this).val());
    });

    $('#banner_text').on('input', function() {
        $('.banner-preview p').text($(this).val());
    });
});

// Función para mantener abierto el submenú activo
document.addEventListener('DOMContentLoaded', function() {
    // Obtener la URL actual y asegurarse de que coincida con el href del enlace (ignorando el dominio, solo la ruta)
    var currentPath = window.location.pathname.replace(/\/$/, ''); // Eliminar la barra final si existe

    // Seleccionar todos los enlaces dentro de los submenús
    var submenuLinks = document.querySelectorAll('.submenu a');

    submenuLinks.forEach(function(link) {
        var linkPath = link.getAttribute('href').replace(/\/$/, ''); // Eliminar la barra final si existe en el href
        
        // Verificar si la ruta del enlace coincide con la ruta actual
        if (linkPath && currentPath.endsWith(linkPath)) { // Usar endsWith para manejar rutas relativas
            // Añadir clase 'active' al enlace actual
            link.classList.add('active');

            // Encontrar el submenú padre
            var submenu = link.closest('.submenu');
            if (submenu) {
                // Encontrar el elemento de menú padre (el que tiene el enlace .submenu-toggle)
                var parentMenuItem = submenu.closest('li');
                if (parentMenuItem) {
                    // Encontrar el enlace toggle dentro del padre y simular un clic
                    var toggleLink = parentMenuItem.querySelector('.submenu-toggle');
                    if (toggleLink) {
                        // Asegurarse de que el submenú se muestre si está oculto
                         submenu.style.display = 'block'; // Asegurar visibilidad

                        // Opcional: añadir clase 'active' al elemento padre si tu CSS la usa para resaltar
                        parentMenuItem.classList.add('active');

                         // Si necesitas simular un clic para que se active la lógica de otro script, descomenta la siguiente línea:
                         // toggleLink.click(); 
                    }
                }
            }
        }
    });
}); 