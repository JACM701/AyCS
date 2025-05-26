<?php
$page_title = 'Reporte de Ventas por Fecha';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-calendar"></span>
          <span>Reporte de Ventas por Fecha</span>
        </strong>
      </div>
      <div class="panel-body">
        <!-- Formulario de selección de fechas -->
        <form method="post" action="sale_report_process.php" class="clearfix">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="start-date">Fecha Inicial</label>
                <input type="date" class="form-control" name="start-date" id="start-date">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="end-date">Fecha Final</label>
                <input type="date" class="form-control" name="end-date" id="end-date">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" name="submit" class="btn btn-primary btn-block">Generar Reporte</button>
              </div>
            </div>
          </div>
        </form>

        <!-- Tabla de resultados (ejemplo) -->
        <div class="table-responsive" style="margin-top: 20px;">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Producto/Servicio</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Precio Unitario</th>
                <th class="text-center">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center">1</td>
                <td>2024-03-20</td>
                <td>Juan Pérez</td>
                <td>Producto A</td>
                <td class="text-center">2</td>
                <td class="text-center">$100.00</td>
                <td class="text-center">$200.00</td>
              </tr>
              <tr>
                <td class="text-center">2</td>
                <td>2024-03-20</td>
                <td>María López</td>
                <td>Servicio B</td>
                <td class="text-center">1</td>
                <td class="text-center">$150.00</td>
                <td class="text-center">$150.00</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6" class="text-right"><strong>Total:</strong></td>
                <td class="text-center"><strong>$350.00</strong></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.panel {
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

.form-control {
  border-radius: 4px;
  border: 1px solid #ddd;
  padding: 8px 12px;
}

.form-control:focus {
  border-color: #283593;
  box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
}

.btn-primary {
  background: linear-gradient(135deg, #283593 0%, #1a237e 100%);
  border: none;
  padding: 10px 20px;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table {
  margin-top: 20px;
}

.table thead th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
  color: #283593;
  font-weight: 600;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

.table tfoot {
  font-weight: bold;
  background-color: #f8f9fa;
}
</style>

<?php include_once('layouts/footer.php'); ?>
