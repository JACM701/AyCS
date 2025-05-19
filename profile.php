<?php
  $page_title = 'Mi perfil';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);

  $user_id = (int)$_GET['id'];
  if(empty($user_id)):
    redirect('home.php',false);
  else:
    $user_p = find_by_id('users',$user_id);
    if(!$user_p) {
      $session->msg("d", "Usuario no encontrado.");
      redirect('home.php', false);
    }
  endif;

  // Inicializar variables del banner con valores por defecto
  $banner_title = 'Bienvenido al Sistema';
  $banner_text = 'Sistema de Gestión de Inventario';
  $banner_image = 'libs/images/default-banner.jpg';

  // Obtener configuración del banner si es admin
  if($user['user_level'] === '1') {
    // Verificar si la tabla settings existe
    $table_exists = $db->query("SHOW TABLES LIKE 'settings'");
    if($db->num_rows($table_exists) > 0) {
        // Obtener configuración actual
        $settings = $db->query("SELECT * FROM settings WHERE id = 1");
        if($settings && $db->num_rows($settings) > 0) {
            $settings = $db->fetch_assoc($settings);
            $banner_title = $settings['banner_title'];
            $banner_text = $settings['banner_text'];
            $banner_image = $settings['banner_image'];
        }
    }
  }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
   <div class="col-md-4">
       <div class="panel profile">
         <div class="jumbotron text-center bg-red">
            <img class="img-circle img-size-2" src="uploads/users/<?php echo isset($user_p['image']) ? $user_p['image'] : 'no_image.jpg'; ?>" alt="">
           <h3><?php echo isset($user_p['name']) ? first_character($user_p['name']) : 'Usuario'; ?></h3>
         </div>
        <?php if(isset($user_p['id']) && $user_p['id'] === $user['id']):?>
         <ul class="nav nav-pills nav-stacked">
          <li><a href="edit_account.php"> <i class="glyphicon glyphicon-edit"></i> Editar perfil</a></li>
         </ul>
       <?php endif;?>
       </div>
   </div>

   <?php if($user['user_level'] === '1'): ?>
   <div class="col-md-8">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-picture"></span>
           <span>Personalizar Banner del Sistema</span>
         </strong>
       </div>
       <div class="panel-body">
         <!-- Vista previa del banner -->
         <div class="banner-preview" style="margin-bottom: 20px;">
           <h4>Vista previa del banner actual:</h4>
           <div class="main-banner" style="height: 200px; background-image: url('<?php echo $banner_image; ?>');">
             <div class="banner-overlay">
               <div class="banner-content">
                 <h3 style="font-size: 24px;"><?php echo $banner_title; ?></h3>
                 <p style="font-size: 16px;"><?php echo $banner_text; ?></p>
               </div>
             </div>
           </div>
         </div>

         <form method="post" action="update_banner.php" enctype="multipart/form-data">
           <div class="form-group">
             <label for="banner_image">Imagen del Banner</label>
             <input type="file" class="form-control" name="banner_image" id="banner_image" accept="image/*">
             <small class="help-block">Tamaño recomendado: 1920x400 píxeles</small>
           </div>
           <div class="form-group">
             <label for="banner_title">Título del Banner</label>
             <input type="text" class="form-control" name="banner_title" id="banner_title" value="<?php echo $banner_title; ?>">
           </div>
           <div class="form-group">
             <label for="banner_text">Texto del Banner</label>
             <textarea class="form-control" name="banner_text" id="banner_text" rows="3"><?php echo $banner_text; ?></textarea>
           </div>
           <button type="submit" class="btn btn-primary">Actualizar Banner</button>
         </form>
       </div>
     </div>
   </div>
   <?php endif; ?>
</div>
<?php include_once('layouts/footer.php'); ?>
