<?php
require_once('includes/load.php');
page_require_level(1);

$sql = "CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_title` varchar(255) DEFAULT 'Bienvenido al Sistema',
  `banner_text` text DEFAULT 'Sistema de Gestión de Inventario',
  `banner_image` varchar(255) DEFAULT 'libs/images/default-banner.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if($db->query($sql)) {
    // Insertar registro inicial si la tabla está vacía
    $check = $db->query("SELECT COUNT(*) as count FROM settings");
    $row = $db->fetch_assoc($check);
    
    if($row['count'] == 0) {
        $insert = "INSERT INTO settings (banner_title, banner_text, banner_image) 
                  VALUES ('Bienvenido al Sistema', 'Sistema de Gestión de Inventario', 'libs/images/default-banner.jpg')";
        $db->query($insert);
    }
    
    $session->msg('s', 'Tabla settings creada exitosamente');
} else {
    $session->msg('d', 'Error al crear la tabla settings');
}

redirect('home.php', false);
?> 