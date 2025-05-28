<?php
$page_title = 'Reporte de Ventas Diarias';
require_once('includes/load.php');
// Verifica el nivel de permisos del usuario
page_require_level(2);

// Obtiene el año y mes actuales
$year  = date('Y');
$month = date('m');

// Consulta SQL para obtener las ventas diarias de productos (usando detalle_venta)
$query = "
    SELECT v.Fecha,
           p.Nombre AS product_name,
           dv.Precio AS product_price, -- Usar precio de detalle_venta
           SUM(dv.Cantidad) AS total_qty_sold, -- Sumar cantidad desde detalle_venta
           SUM(dv.Cantidad * dv.Precio) AS total_saleing_price -- Calcular total usando precio y cantidad de detalle_venta
    FROM venta v
    JOIN detalle_venta dv ON v.ID = dv.Id_Venta -- Unir con detalle_venta usando v.ID (corregido de v.Folio)
    JOIN producto p ON dv.Id_Producto = p.ID -- Unir con producto (singular) usando Id_Producto e ID
    WHERE YEAR(v.Fecha) = '{$year}' AND MONTH(v.Fecha) = '{$month}'
      AND dv.Id_Producto IS NOT NULL -- Solo considerar items que son productos
    GROUP BY v.Fecha, p.Nombre
    ORDER BY v.Fecha DESC
";

$sales = find_by_sql($query);
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
          <span class="glyphicon glyphicon-time"></span>
          <span>Reporte de Ventas Diarias</span>
        </strong>
      </div>
      <div class="panel-body">
        <!-- Formulario de selección de mes y año -->
        <form method="post" action="daily_sales.php" class="clearfix">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="month">Seleccionar Mes</label>
                <select class="form-control" name="month" id="month">
                  <option value="01">Enero</option>
                  <option value="02">Febrero</option>
                  <option value="03">Marzo</option>
                  <option value="04">Abril</option>
                  <option value="05">Mayo</option>
                  <option value="06">Junio</option>
                  <option value="07">Julio</option>
                  <option value="08">Agosto</option>
                  <option value="09">Septiembre</option>
                  <option value="10">Octubre</option>
                  <option value="11">Noviembre</option>
                  <option value="12">Diciembre</option>
                </select>
              </div>
            </div>
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
                <th>Fecha</th>
                <th class="text-center">Total Ventas</th>
                <th class="text-center">Productos Vendidos</th>
                <th class="text-center">Servicios Realizados</th>
                <th class="text-center">Total Ingresos</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center">1</td>
                <td>01/03/2024</td>
                <td class="text-center">5</td>
                <td class="text-center">3</td>
                <td class="text-center">2</td>
                <td class="text-center">$2,500.00</td>
              </tr>
              <tr>
                <td class="text-center">2</td>
                <td>02/03/2024</td>
                <td class="text-center">8</td>
                <td class="text-center">6</td>
                <td class="text-center">2</td>
                <td class="text-center">$4,200.00</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5" class="text-right"><strong>Total Mensual:</strong></td>
                <td class="text-center"><strong>$6,700.00</strong></td>
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