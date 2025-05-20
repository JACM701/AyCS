<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  $query = "SELECT p.*, i.Cantidad, c.name as category_name 
            FROM productos p 
            LEFT JOIN inventario i ON p.Id_Productos = i.Id_Producto
            LEFT JOIN categories c ON p.Categoria = c.id
            ORDER BY p.Id_Productos DESC";
  $products = $db->query($query);
?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
         <div class="pull-right">
           <a href="add_product.php" class="btn btn-primary">Agregar producto</a>
         </div>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th> Imagen</th>
                <th> Nombre </th>
                <th> Categoría </th>
                <th> Descripción </th>
                <th class="text-center" style="width: 10%;"> Stock </th>
                <th class="text-center" style="width: 10%;"> Costo </th>
                <th class="text-center" style="width: 100px;"> Acciones </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product):?>
              <tr>
                <td class="text-center"><?php echo $product['Id_Productos'];?></td>
                <td>
                  <?php if($product['Foto']): ?>
                    <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['Foto']; ?>" alt="">
                  <?php else: ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php endif; ?>
                </td>
                <td> <?php echo remove_junk($product['Nombre']); ?></td>
                <td> <?php echo remove_junk($product['category_name'] ?? 'Sin categoría'); ?></td>
                <td> <?php echo remove_junk($product['Descripcion']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['Cantidad']); ?></td>
                <td class="text-center"> $<?php echo number_format($product['Costo'], 2); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['Id_Productos'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                     <a href="delete_product.php?id=<?php echo (int)$product['Id_Productos'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Está seguro de eliminar este producto?');">
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
