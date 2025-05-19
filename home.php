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

  // Obtener datos para actividad reciente (últimas 5 ventas)
  $sql_ventas_recientes = "SELECT v.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido,
                          p.Nombre as ProductoNombre, s.Nombre as ServicioNombre, s.Costo as ServicioCosto,
                          p.Costo as ProductoCosto
                          FROM venta v
                          LEFT JOIN clientes c ON v.Id_Cliente = c.Id_Cliente
                          LEFT JOIN productos p ON v.Id_Productos = p.Id_Productos
                          LEFT JOIN servicio s ON v.Id_Servicio = s.Id_Servicio
                          ORDER BY v.Fecha DESC LIMIT 5";
  $recent_activity = find_by_sql($sql_ventas_recientes);
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
          <span>Actividad Reciente (Últimas Ventas)</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="activity-list">
          <?php if ($recent_activity): ?>
            <?php foreach ($recent_activity as $activity): ?>
              <div class="activity-item">
                <i class="glyphicon glyphicon-shopping-cart"></i>
                <div class="activity-content">
                  <h4>Nueva Venta: #<?php echo remove_junk($activity['Folio']); ?></h4>
                  <p>Cliente: <?php echo remove_junk($activity['ClienteNombre'] . ' ' . $activity['ClienteApellido']); ?></p>
                  <p>
                    <?php
                      $items = [];
                      if ($activity['ProductoNombre']) {
                          $items[] = remove_junk($activity['ProductoNombre']);
                      }
                      if ($activity['ServicioNombre']) {
                          $items[] = remove_junk($activity['ServicioNombre']);
                      }
                      echo 'Artículos/Servicios: ' . (empty($items) ? 'Ninguno' : implode(', ', $items));
                    ?>
                  </p>
                  <small>Fecha: <?php echo read_date($activity['Fecha']); ?></small>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="activity-item">
              <div class="activity-content">
                <h4>No hay actividad reciente.</h4>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
