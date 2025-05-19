<?php
  $page_title = 'Home Page';
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}

  // Verificar si la tabla settings existe
  $table_exists = $db->query("SHOW TABLES LIKE 'settings'");
  if($db->num_rows($table_exists) == 0) {
      $banner_title = 'Bienvenido al Sistema';
      $banner_text = 'Sistema de Gestión de Inventario';
      $banner_image = 'libs/images/default-banner.jpg';
  } else {
      // Obtener configuración actual
      $settings = $db->query("SELECT * FROM settings WHERE id = 1");
      if($settings && $db->num_rows($settings) > 0) {
          $settings = $db->fetch_assoc($settings);
          $banner_title = $settings['banner_title'];
          $banner_text = $settings['banner_text'];
          $banner_image = $settings['banner_image'];
      } else {
          $banner_title = 'Bienvenido al Sistema';
          $banner_text = 'Sistema de Gestión de Inventario';
          $banner_image = 'libs/images/default-banner.jpg';
      }
  }
?>
<?php include_once('layouts/header.php'); ?>

<div class="home-container">
  <!-- Banner Principal -->
  <div class="main-banner" style="background-image: url('<?php echo $banner_image; ?>');">
    <div class="banner-overlay">
      <div class="banner-content">
        <h1><?php echo $banner_title; ?></h1>
        <p><?php echo $banner_text; ?></p>
      </div>
    </div>
  </div>

  <!-- Sección de Accesos Rápidos -->
  <div class="quick-access">
    <div class="row">
      <div class="col-md-3">
        <div class="quick-access-item">
          <i class="glyphicon glyphicon-shopping-cart"></i>
          <h3>Ventas</h3>
          <p>Gestionar ventas y transacciones</p>
          <a href="sales.php" class="btn btn-primary">Ir a Ventas</a>
        </div>
      </div>
      <div class="col-md-3">
        <div class="quick-access-item">
          <i class="glyphicon glyphicon-list-alt"></i>
          <h3>Inventario</h3>
          <p>Control de productos y stock</p>
          <a href="inventory.php" class="btn btn-primary">Ver Inventario</a>
        </div>
      </div>
      <div class="col-md-3">
        <div class="quick-access-item">
          <i class="glyphicon glyphicon-user"></i>
          <h3>Clientes</h3>
          <p>Gestión de clientes</p>
          <a href="customers.php" class="btn btn-primary">Ver Clientes</a>
        </div>
      </div>
      <div class="col-md-3">
        <div class="quick-access-item">
          <i class="glyphicon glyphicon-stats"></i>
          <h3>Reportes</h3>
          <p>Estadísticas y reportes</p>
          <a href="reports.php" class="btn btn-primary">Ver Reportes</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección de Actividad Reciente -->
  <div class="recent-activity">
    <div class="panel">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-time"></span>
          <span>Actividad Reciente</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="activity-list">
          <!-- Aquí irá el contenido dinámico de actividad reciente -->
          <div class="activity-item">
            <i class="glyphicon glyphicon-shopping-cart"></i>
            <div class="activity-content">
              <h4>Nueva Venta Realizada</h4>
              <p>Se ha registrado una nueva venta</p>
              <small>Hace 5 minutos</small>
            </div>
          </div>
          <!-- Más items de actividad... -->
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
