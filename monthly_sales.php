<?php
  $page_title = 'Reporte de Ventas Mensuales';
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
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-stats"></span>
          <span>Reporte de Ventas Mensuales</span>
        </strong>
      </div>
      <div class="panel-body">
        <!-- Formulario de selección de año -->
        <form method="post" action="monthly_sales.php" class="clearfix">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="year">Seleccionar Año</label>
                <select class="form-control" name="year" id="year">
                  <option value="2024">2024</option>
                  <option value="2023">2023</option>
                  <option value="2022">2022</option>
                </select>
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
                <th>Mes</th>
                <th class="text-center">Total Ventas</th>
                <th class="text-center">Productos Vendidos</th>
                <th class="text-center">Servicios Realizados</th>
                <th class="text-center">Total Ingresos</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center">1</td>
                <td>Enero</td>
                <td class="text-center">25</td>
                <td class="text-center">18</td>
                <td class="text-center">7</td>
                <td class="text-center">$15,500.00</td>
              </tr>
              <tr>
                <td class="text-center">2</td>
                <td>Febrero</td>
                <td class="text-center">30</td>
                <td class="text-center">22</td>
                <td class="text-center">8</td>
                <td class="text-center">$18,750.00</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5" class="text-right"><strong>Total Anual:</strong></td>
                <td class="text-center"><strong>$34,250.00</strong></td>
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
