<?php
  $page_title = 'Inventario';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);

  // Obtener todas las categorías para el filtro
  $categories = find_all('categories');

  // Obtener la categoría seleccionada del filtro
  $selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

  // Construir la consulta SQL base
  $sql = "SELECT p.*, i.Cantidad, c.name as category_name 
          FROM productos p 
          LEFT JOIN inventario i ON p.Id_Productos = i.Id_Producto 
          LEFT JOIN categories c ON p.Categoria = c.id";

  // Agregar filtro de categoría si se seleccionó una
  if($selected_category > 0) {
    $sql .= " WHERE p.Categoria = {$selected_category}";
  }

  $sql .= " ORDER BY p.Nombre";
  $products = find_by_sql($sql);
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
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Inventario de Productos</span>
        </strong>
        <div class="pull-right">
          <a href="add_product.php" class="btn btn-primary">Agregar Producto</a>
        </div>
      </div>
      <div class="panel-body">
        <!-- Filtro de categorías -->
        <div class="row">
          <div class="col-md-4">
            <form method="get" action="inventory.php" class="form-inline">
              <div class="form-group">
                <label for="category">Filtrar por categoría:</label>
                <select name="category" class="form-control" onchange="this.form.submit()">
                  <option value="0">Todas las categorías</option>
                  <?php foreach($categories as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>" <?php if($selected_category == $cat['id']) echo 'selected'; ?>>
                      <?php echo remove_junk($cat['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </form>
          </div>
        </div>
        <hr>
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Imagen</th>
              <th>Producto</th>
              <th>Categoría</th>
              <th class="text-center">Stock</th>
              <th class="text-center">Precio</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td>
                  <?php if($product['Foto']): ?>
                    <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['Foto']; ?>" alt="">
                  <?php else: ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php endif; ?>
                </td>
                <td><?php echo remove_junk($product['Nombre']); ?></td>
                <td><?php echo remove_junk($product['category_name'] ?? 'Sin categoría'); ?></td>
                <td class="text-center">
                  <?php 
                    $stock = (int)$product['Cantidad'];
                    if($stock <= 5) {
                      echo '<span class="label label-danger">'.$stock.'</span>';
                    } elseif($stock <= 10) {
                      echo '<span class="label label-warning">'.$stock.'</span>';
                    } else {
                      echo '<span class="label label-success">'.$stock.'</span>';
                    }
                  ?>
                </td>
                <td class="text-center">$<?php echo number_format($product['Costo'], 2); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['Id_Productos'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['Id_Productos'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?> 