<?php
  require_once('includes/load.php'); // Asegurarse de que load.php esté incluido
?>
<!DOCTYPE html>
  <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title><?php if (!empty($page_title))
           echo remove_junk($page_title);
            else echo "Sistema simple de inventario";?>
    </title>
	
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="libs/css/main.css" />
    <link rel="stylesheet" href="libs/css/custom.css" />
  </head>
  <body>
  <?php  if ($session->isUserLoggedIn(true)): // Verificar si hay una sesión de usuario activa ?>
    <?php $user = current_user(); // Obtener los datos del usuario logueado ?>
    <?php if ($user): // Verificar si se obtuvieron los datos del usuario correctamente ?>
    <header id="header">
      <div class="logo pull-left"> AyCS - Inventario </div>
      <div class="header-content">
      <div class="header-date pull-left">
        <strong><?php
        date_default_timezone_set('America/Mexico_City');
        echo "<strong>" . date("d/m/Y  g:i a") . "</strong>";
        ?></strong>
      </div>
      <div class="pull-right clearfix">
        <ul class="info-menu list-inline list-unstyled">
          <li class="profile">
            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
              <img src="uploads/users/<?php echo isset($user['image']) && !empty($user['image']) ? $user['image'] : 'no_image.jpg';?>" alt="user-image" class="img-circle img-inline">
              <span><?php echo remove_junk(ucfirst(isset($user['name']) ? $user['name'] : '')); ?> <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                  <a href="profile.php?id=<?php echo isset($user['id']) ? (int)$user['id'] : '';?>">
                      <i class="glyphicon glyphicon-user"></i>
                      Perfil
                  </a>
              </li>
             <li class="last">
                 <a href="logout.php">
                     <i class="glyphicon glyphicon-off"></i>
                     Salir
                 </a>
             </li>
           </ul>
          </li>
        </ul>
      </div>
     </div>
    </header>

    <div class="sidebar">
      <?php if(isset($user['user_level']) && $user['user_level'] === '1'): // Verificar si user_level existe y es 1 ?>
        <!-- admin menu -->
      <?php include_once('admin_menu.php');?>

      <?php elseif(isset($user['user_level']) && $user['user_level'] === '2'): // Verificar si user_level existe y es 2 ?>
        <!-- Special user -->
      <?php include_once('special_menu.php');?>

      <?php elseif(isset($user['user_level']) && $user['user_level'] === '3'): // Verificar si user_level existe y es 3 ?>
        <!-- User menu -->
      <?php include_once('user_menu.php');?>

      <?php endif;?>

   </div>
    <?php endif; // Fin de la verificación if ($user) ?>
  <?php endif; // Fin de la verificación if ($session->isUserLoggedIn) ?>

<div class="page">
  <div class="container-fluid">
