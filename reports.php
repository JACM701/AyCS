<?php
  $page_title = 'Reportes';
  require_once('includes/load.php');
  page_require_level(2);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <!-- Panel de Reportes de Ventas -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-shopping-cart"></span>
          <span>Reportes de Ventas</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <a href="sales_report.php" class="list-group-item">
            <i class="glyphicon glyphicon-calendar"></i> Ventas por Fecha
          </a>
          <a href="monthly_sales.php" class="list-group-item">
            <i class="glyphicon glyphicon-stats"></i> Ventas Mensuales
          </a>
          <a href="daily_sales.php" class="list-group-item">
            <i class="glyphicon glyphicon-time"></i> Ventas Diarias
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel de Reportes de Inventario -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Reportes de Inventario</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-list-alt"></i> Productos por Categoría
          </a>
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-warning-sign"></i> Productos con Bajo Stock
          </a>
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-usd"></i> Valor Total del Inventario
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Panel de Reportes de Clientes -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-user"></span>
          <span>Reportes de Clientes</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-star"></i> Clientes Frecuentes
          </a>
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-usd"></i> Clientes por Gasto Total
          </a>
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-map-marker"></i> Distribución Geográfica
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel de Reportes de Servicios -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-wrench"></span>
          <span>Reportes de Servicios</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-stats"></i> Servicios más Populares
          </a>
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-usd"></i> Ingresos por Servicio
          </a>
          <a href="#" class="list-group-item">
            <i class="glyphicon glyphicon-calendar"></i> Servicios por Período
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.panel {
  margin-bottom: 20px;
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.panel-heading {
  background: linear-gradient(135deg, #283593 0%, #1a237e 100%) !important;
  border: none;
  border-radius: 8px 8px 0 0;
  padding: 15px;
}

.panel-heading strong {
  color: #ffffff;
  font-size: 16px;
  font-weight: 500;
}

.panel-heading .glyphicon {
  margin-right: 10px;
}

.list-group-item {
  border: none;
  border-bottom: 1px solid #eee;
  padding: 15px;
  transition: all 0.3s ease;
}

.list-group-item:last-child {
  border-bottom: none;
}

.list-group-item:hover {
  background-color: #f8f9fa;
  transform: translateX(5px);
}

.list-group-item i {
  margin-right: 10px;
  color: #283593;
}

.list-group-item:hover i {
  transform: scale(1.1);
}
</style>

<?php include_once('layouts/footer.php'); ?> 