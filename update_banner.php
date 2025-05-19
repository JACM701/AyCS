<?php
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
if($user['user_level'] !== '1') { redirect('home.php', false);}

// Verificar si la tabla settings existe
$table_exists = $db->query("SHOW TABLES LIKE 'settings'");
if($db->num_rows($table_exists) == 0) {
    // Crear la tabla settings si no existe
    $sql = "CREATE TABLE settings (
        id INT(11) NOT NULL AUTO_INCREMENT,
        banner_title VARCHAR(255) NOT NULL,
        banner_text TEXT NOT NULL,
        banner_image VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    )";
    $db->query($sql);
}

// Procesar la imagen del banner
$banner_image = 'libs/images/default-banner.jpg';
if(isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
    $target_dir = "libs/images/banners/";
    if(!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES["banner_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = "banner_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Verificar si es una imagen válida
    $check = getimagesize($_FILES["banner_image"]["tmp_name"]);
    if($check !== false) {
        if(move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
            $banner_image = $target_file;
        }
    }
}

// Obtener los datos del formulario
$banner_title = $db->escape($_POST['banner_title']);
$banner_text = $db->escape($_POST['banner_text']);

// Verificar si ya existe un registro en la tabla settings
$settings = $db->query("SELECT * FROM settings WHERE id = 1");
if($db->num_rows($settings) > 0) {
    // Actualizar el registro existente
    $sql = "UPDATE settings SET 
            banner_title = '{$banner_title}',
            banner_text = '{$banner_text}'";
    
    // Solo actualizar la imagen si se subió una nueva
    if($banner_image != 'libs/images/default-banner.jpg') {
        $sql .= ", banner_image = '{$banner_image}'";
    }
    
    $sql .= " WHERE id = 1";
} else {
    // Insertar un nuevo registro
    $sql = "INSERT INTO settings (banner_title, banner_text, banner_image) 
            VALUES ('{$banner_title}', '{$banner_text}', '{$banner_image}')";
}

if($db->query($sql)) {
    $session->msg('s', 'Banner actualizado exitosamente');
} else {
    $session->msg('d', 'Error al actualizar el banner');
}

redirect('home.php', false);
?> 