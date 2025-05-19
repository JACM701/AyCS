<?php
require_once('includes/load.php');

// Verificar si el usuario ha iniciado sesión y tiene permisos de administrador
if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
// Assuming user_level is checked by page_require_level in header/included file, 
// but good to double-check here for direct access
$user = current_user();
if($user['user_level'] !== '1') { 
    $session->msg('d', '¡No tienes permiso para realizar esta acción!');
    redirect('home.php', false);
}

// Verificar si se ha enviado el formulario
if(isset($_POST['banner_title']) && isset($_POST['banner_text'])) {

    // Obtener los datos del formulario
    $banner_title = $db->escape($_POST['banner_title']);
    $banner_text = $db->escape($_POST['banner_text']);
    $banner_image = 'libs/images/default-banner.jpg'; // Valor por defecto

    // Verificar si se subió un archivo de imagen
    if(isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "libs/images/banners/";

        // Crear el directorio si no existe
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $session->msg('d', 'Error: No se pudo crear el directorio de subida.');
                redirect('profile.php?id=' . (int)$user['id'], false);
            }
        }
        
        $file_extension = strtolower(pathinfo($_FILES["banner_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "banner_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Verificar si es una imagen válida
        $check = getimagesize($_FILES["banner_image"]["tmp_name"]);
        if($check !== false) {
            // Intentar mover el archivo subido
            if(move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
                $banner_image = $target_file; // Usar la nueva ruta de la imagen
            } else {
                $session->msg('d', 'Error: No se pudo subir el archivo de imagen. Verifique permisos de la carpeta uploads/images/banners.');
                redirect('profile.php?id=' . (int)$user['id'], false);
            }
        } else {
            $session->msg('d', 'Error: El archivo subido no es una imagen válida.');
            redirect('profile.php?id=' . (int)$user['id'], false);
        }
    } else if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] !== UPLOAD_ERR_NO_FILE) {
         // Manejar otros errores de subida de archivo que no sean NO_FILE
         $phpFileUploadErrors = array(
            0 => 'No hay error, el archivo se cargó correctamente',
            1 => 'El archivo subido excede la directiva upload_max_filesize en php.ini',
            2 => 'El archivo subido excede la directiva MAX_FILE_SIZE que se especifica en el formulario HTML',
            3 => 'El archivo subido fue sólo parcialmente cargado',
            4 => 'No se subió ningún archivo',
            6 => 'Falta una carpeta temporal',
            7 => 'No se pudo escribir el archivo en el disco.',
            8 => 'Una extensión de PHP detuvo la carga del archivo.',
        );
        $session->msg('d', 'Error en la subida del archivo: ' . $phpFileUploadErrors[$_FILES['banner_image']['error']]);
        redirect('profile.php?id=' . (int)$user['id'], false);
    }

    // Verificar si ya existe un registro en la tabla settings
    $settings = $db->query("SELECT * FROM settings WHERE id = 1 LIMIT 1");
    
    $sql = "";
    if($db->num_rows($settings) > 0) {
        // Actualizar el registro existente
        $sql = "UPDATE settings SET 
                banner_title = '$banner_title',
                banner_text = '$banner_text'";
        
        // Solo actualizar la imagen si se subió una nueva (y fue procesada correctamente)
        if($banner_image != 'libs/images/default-banner.jpg') {
            $sql .= ", banner_image = '$banner_image'";
        }
        
        $sql .= " WHERE id = 1";

    } else {
        // Insertar un nuevo registro
        $sql = "INSERT INTO settings (banner_title, banner_text, banner_image) 
                VALUES ('$banner_title', '$banner_text', '$banner_image')";
    }
    
    // Ejecutar la consulta SQL
    if(!empty($sql)){
        if($db->query($sql)) {
            $session->msg('s', 'Banner actualizado exitosamente');
        } else {
            $session->msg('d', 'Error al actualizar el banner en la base de datos.');
        }
    } else {
         $session->msg('d', 'Error interno: Consulta SQL vacía.');
    }

} else {
    $session->msg('d', 'Error: Datos del formulario incompletos.');
}

// Redirigir de vuelta a la página de perfil después de intentar la actualización
redirect('profile.php?id=' . (int)$user['id'], false);

?> 