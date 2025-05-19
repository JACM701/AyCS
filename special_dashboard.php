<?php
  $page_title = 'Panel de Control - Usuario Especial';
  require_once('includes/load.php');
  page_require_level(2);
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
      ['color' => 'bg-blue',   'icon' => 'glyphicon-shopping-cart', 'value' => count_by_id('productos')['total'],  'label' => 'Productos'],
      ['color' => 'bg-yellow', 'icon' => 'glyphicon-list-alt',     'value' => count_by_id('categorie')['total'],  'label' => 'Categorías']
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

<!-- Productos recientes -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Productos recientes</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <?php
          $sql_productos_recientes = "SELECT p.*, i.Cantidad 
                                     FROM productos p 
                                     LEFT JOIN inventario i ON p.Id_Productos = i.Id_Producto 
                                     ORDER BY p.Id_Productos DESC LIMIT 5";
          $recent_products = find_by_sql($sql_productos_recientes);
          
          foreach ($recent_products as $product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?= (int)$product['Id_Productos']; ?>">
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
          <div class="col-md-4">
            <a href="add_product.php" class="btn btn-primary btn-block">
              <i class="glyphicon glyphicon-plus"></i> Agregar Producto
            </a>
          </div>
          <div class="col-md-4">
            <a href="product.php" class="btn btn-info btn-block">
              <i class="glyphicon glyphicon-list"></i> Ver Productos
            </a>
          </div>
          <div class="col-md-4">
            <a href="media.php" class="btn btn-success btn-block">
              <i class="glyphicon glyphicon-picture"></i> Galería
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?> 