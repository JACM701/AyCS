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