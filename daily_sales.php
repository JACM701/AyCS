<?php
$page_title = 'Venta diaria';
require_once('includes/load.php');
// Verifica el nivel de permisos del usuario
page_require_level(3);

// Obtiene el año y mes actuales
$year  = date('Y');
$month = date('m');

// Consulta SQL para obtener las ventas y los productos
$query = "
    SELECT v.Folio, v.Fecha, p.Nombre AS product_name, p.Costo AS product_cost, COUNT(v.Id_Productos) AS qty, 
           (COUNT(v.Id_Productos) * p.Costo) AS total_saleing_price
    FROM venta v
    JOIN productos p ON v.Id_Productos = p.Id_Productos
    WHERE YEAR(v.Fecha) = '{$year}' AND MONTH(v.Fecha) = '{$month}'
    GROUP BY v.Folio, p.Id_Productos, v.Fecha
    ORDER BY v.Fecha DESC
";

$sales = find_by_sql($query);
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Venta diaria</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Descripción del producto</th>
              <th class="text-center" style="width: 15%;">Cantidad vendida</th>
              <th class="text-center" style="width: 15%;">Total</th>
              <th class="text-center" style="width: 15%;">Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sales as $sale):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk($sale['product_name']); ?></td>
                <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
                <td class="text-center"><?php echo remove_junk($sale['total_saleing_price']); ?></td>
                <td class="text-center"><?php echo date("d/m/Y", strtotime($sale['Fecha'])); ?></td>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>