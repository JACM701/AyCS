<?php
  // Título de la página
  $page_title = 'Admin página de inicio';
  require_once('includes/load.php');

  // Verificar el nivel de permiso del usuario para ver esta página
  page_require_level(1);

  // Obtener conteos de diferentes entidades
  $c_categorie     = count_by_id('categories');    // Total de categorías
  $c_product       = count_by_id('products');      // Total de productos
  $c_sale          = count_by_id('sales');         // Total de ventas
  $c_user          = count_by_id('users');         // Total de usuarios

  // Obtener datos para el dashboard
  $products_sold   = find_higest_saleing_product(10);     // Top 10 productos más vendidos
  $recent_products = find_recent_product_added(5);        // 5 productos recientemente añadidos
  $recent_sales    = find_recent_sale_added(5);           // 5 ventas recientes
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
      ['color' => 'bg-green',  'icon' => 'glyphicon-user',          'value' => $c_user['total'],      'label' => 'Usuarios'],
      ['color' => 'bg-red',    'icon' => 'glyphicon-list',          'value' => $c_categorie['total'], 'label' => 'Categorías'],
      ['color' => 'bg-blue',   'icon' => 'glyphicon-shopping-cart', 'value' => $c_product['total'],   'label' => 'Productos'],
      ['color' => 'bg-yellow', 'icon' => 'glyphicon-usd',           'value' => $c_sale['total'],      'label' => 'Ventas']
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
              <th>Título</th>
              <th>Total vendido</th>
              <th>Cantidad total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as $product): ?>
              <tr>
                <td><?= remove_junk(first_character($product['name'])); ?></td>
                <td><?= (int)$product['totalSold']; ?></td>
                <td><?= (int)$product['totalQty']; ?></td>
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
              <th>Producto</th>
              <th>Fecha</th>
              <th>Venta total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as $sale): ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="edit_sale.php?id=<?= (int)$sale['id']; ?>">
                    <?= remove_junk(first_character($sale['name'])); ?>
                  </a>
                </td>
                <td><?= remove_junk(ucfirst($sale['date'])); ?></td>
                <td>$<?= remove_junk(first_character($sale['price'])); ?></td>
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
          <span>Productos recientemente añadidos</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <?php foreach ($recent_products as $product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?= (int)$product['id']; ?>">
              <h4 class="list-group-item-heading">
                <?php if ($product['media_id'] === '0'): ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?= $product['image']; ?>" alt="">
                <?php endif; ?>
                <?= remove_junk(first_character($product['name'])); ?>
                <span class="label label-warning pull-right">
                  $<?= (int)$product['sale_price']; ?>
                </span>
              </h4>
              <span class="list-group-item-text pull-right">
                <?= remove_junk(first_character($product['categorie'])); ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
