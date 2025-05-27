<?php
  // Título de la página
  $page_title = 'Admin página de inicio';
  require_once('includes/load.php');

  // Verificar el nivel de permiso del usuario para ver esta página
  page_require_level(1);

  // Obtener conteos de diferentes entidades
  $c_clientes     = count_by_id('clientes');    // Total de clientes
  $c_productos    = count_by_id('productos');   // Total de productos
  $c_ventas       = count_by_id('venta');       // Total de ventas
  $c_servicios    = count_by_id('servicio');    // Total de servicios

  // Obtener datos para el dashboard
  $sql_ventas_recientes = "SELECT v.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido, 
                          p.Nombre as ProductoNombre, s.Nombre as ServicioNombre, s.Costo as ServicioCosto,
                          p.Costo as ProductoCosto
                          FROM venta v
                          LEFT JOIN clientes c ON v.Id_Cliente = c.Id_Cliente
                          LEFT JOIN productos p ON v.Id_Productos = p.Id_Productos
                          LEFT JOIN servicio s ON v.Id_Servicio = s.Id_Servicio
                          ORDER BY v.Fecha DESC LIMIT 5";
  $recent_sales = find_by_sql($sql_ventas_recientes);

  // Productos recientes
  $sql_productos_recientes = "SELECT p.*, i.Cantidad 
                             FROM productos p 
                             LEFT JOIN inventario i ON p.Id_Productos = i.Id_Producto 
                             ORDER BY p.Id_Productos DESC LIMIT 5";
  $recent_products = find_by_sql($sql_productos_recientes);

  // Productos más vendidos
  $sql_productos_vendidos = "SELECT p.Nombre, COUNT(v.Id_Productos) as total_vendido
                            FROM productos p
                            LEFT JOIN venta v ON p.Id_Productos = v.Id_Productos
                            GROUP BY p.Id_Productos
                            ORDER BY total_vendido DESC
                            LIMIT 5";
  $products_sold = find_by_sql($sql_productos_vendidos);
?>

<?php include_once('layouts/header.php'); ?>

<!-- Sección de mensajes -->
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Panel de estadísticas principales -->
<div class="row">
  <?php
    $stats = [
      ['color' => 'bg-green',  'icon' => 'glyphicon-user',          'value' => $c_clientes['total'],   'label' => 'Clientes'],
      ['color' => 'bg-red',    'icon' => 'glyphicon-wrench',        'value' => $c_servicios['total'],  'label' => 'Servicios'],
      ['color' => 'bg-blue',   'icon' => 'glyphicon-shopping-cart', 'value' => $c_productos['total'],  'label' => 'Productos'],
      ['color' => 'bg-yellow', 'icon' => 'glyphicon-usd',           'value' => $c_ventas['total'],     'label' => 'Ventas']
    ];

    foreach ($stats as $stat):
  ?>
    <div class="col-md-3">
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

<!-- Sección de tablas informativas -->
<div class="row">
  <!-- Tabla de Productos más vendidos -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Productos más vendidos</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Total vendido</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as $product): ?>
              <tr>
                <td><?= remove_junk($product['Nombre']); ?></td>
                <td><?= (int)$product['total_vendido']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tabla de Últimas Ventas -->
  <div class="col-md-4">
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
            <?php foreach ($recent_sales as $sale): ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="edit_sale.php?id=<?= (int)$sale['Folio'];?>">
                    <?= remove_junk($sale['ClienteNombre'] . ' ' . $sale['ClienteApellido']); ?>
                  </a>
                </td>
                <td>
                  <?php 
                    $total = 0;
                    if($sale['ProductoNombre']) $total += $sale['ProductoCosto'];
                    if($sale['ServicioNombre']) $total += $sale['ServicioCosto'];
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

  <!-- Lista de Productos Recientes -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Productos recientes</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <?php foreach ($recent_products as $product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?= (int)$product['Id_Productos'];?>">
              <h4 class="list-group-item-heading">
                <?php if ($product['Foto']): ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?= $product['Foto']; ?>" alt="">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                <?php endif; ?>
                <?= remove_junk($product['Nombre']); ?>
                <span class="label label-warning pull-right">
                  $<?= number_format($product['Costo'], 2); ?>
                </span>
              </h4>
              <span class="list-group-item-text pull-right">
                Stock: <?= (int)$product['Cantidad']; ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
