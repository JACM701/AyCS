<?php
  $page_title = 'Panel de Control - Usuario';
  require_once('includes/load.php');
  page_require_level(3);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Panel de estadísticas principales -->
<div class="row">
  <?php
    $stats = [
      ['color' => 'bg-green',  'icon' => 'glyphicon-shopping-cart', 'value' => count_by_id('venta')['total'],     'label' => 'Ventas'],
      ['color' => 'bg-blue',   'icon' => 'glyphicon-user',          'value' => count_by_id('clientes')['total'],  'label' => 'Clientes']
    ];

    foreach ($stats as $stat):
  ?>
    <div class="col-md-6">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left <?= $stat['color']; ?>">
          <i class="glyphicon <?= $stat['icon']; ?>"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"><?= $stat['value']; ?></h2>
          <p class="text-muted"><?= $stat['label']; ?></p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Últimas Ventas -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Últimas Ventas</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql_ventas_recientes = "SELECT v.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido, 
                                   p.Nombre as ProductoNombre, s.Nombre as ServicioNombre, s.Costo as ServicioCosto,
                                   p.Precio as ProductoPrecio
                                   FROM venta v
                                   LEFT JOIN clientes c ON v.Id_Cliente = c.Id_Cliente
                                   LEFT JOIN producto p ON v.Id_Productos = p.ID
                                   LEFT JOIN servicio s ON v.Id_Servicio = s.ID
                                   ORDER BY v.Fecha DESC LIMIT 5";
            $recent_sales = find_by_sql($sql_ventas_recientes);
            
            foreach ($recent_sales as $sale): ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="edit_sale.php?id=<?= (int)$sale['Folio']; ?>">
                    <?= remove_junk($sale['ClienteNombre'] . ' ' . $sale['ClienteApellido']); ?>
                  </a>
                </td>
                <td>
                  <?php 
                    $total = 0;
                    if(isset($sale['ProductoNombre'])) $total += $sale['ProductoPrecio'];
                    if(isset($sale['ServicioNombre'])) $total += $sale['ServicioCosto'];
                    echo '$' . number_format($total, 2);
                  ?>
                </td>
                <td><?= $sale['Fecha']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Accesos rápidos -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-link"></span>
          <span>Accesos rápidos</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <a href="add_sale.php" class="btn btn-primary btn-block">
              <i class="glyphicon glyphicon-plus"></i> Nueva Venta
            </a>
          </div>
          <div class="col-md-6">
            <a href="sales.php" class="btn btn-info btn-block">
              <i class="glyphicon glyphicon-list"></i> Ver Ventas
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?> 