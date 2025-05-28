<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  // Consulta adaptada para la base de datos actual
  $query = "SELECT p.ID as ID, p.Nombre, p.Descripcion, p.Precio, p.Imagen,
            i.Cantidad,
            c.name as category_name,
            pr.Nombre as provider_name 
            FROM producto p
            LEFT JOIN inventario i ON p.ID = i.Id_Producto
            LEFT JOIN categories c ON p.Id_Categoria = c.id
            LEFT JOIN proveedores pr ON p.Id_Proveedor = pr.Id_Proveedor
            ORDER BY p.ID DESC";
            
  $products = find_by_sql($query);
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
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th> Imagen</th>
                  <th> Nombre </th>
                  <th> Categoría </th>
                  <th> Proveedor </th>
                  <th> Descripción </th>
                  <th class="text-center" style="width: 10%;"> Stock </th>
                  <th class="text-center" style="width: 10%;"> Costo </th>
                  <th class="text-center" style="width: 100px;"> Acciones </th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $product):?>
                <tr>
                  <td class="text-center"><?php echo $product['ID'];?></td>
                  <td>
                    <?php if($product['Imagen']): ?>
                      <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['Imagen']; ?>" alt="">
                    <?php else: ?>
                      <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                    <?php endif; ?>
                  </td>
                  <td> <?php echo remove_junk($product['Nombre']); ?></td>
                  <td> <?php echo remove_junk($product['category_name'] ?? 'Sin categoría'); ?></td>
                  <td> <?php echo remove_junk($product['provider_name'] ?? 'Sin proveedor'); ?></td>
                  <td> <?php echo remove_junk($product['Descripcion']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['Cantidad']); ?></td>
                  <td class="text-center"> $<?php echo number_format($product['Precio'], 2); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_product.php?id=<?php echo (int)$product['ID'];?>" class="btn btn-warning btn-xs" title="Editar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_product.php?id=<?php echo (int)$product['ID'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Está seguro de eliminar este producto?');">
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
  </div>

<style>
.table-responsive {
  overflow-x: auto;
  margin-bottom: 20px;
}

.img-avatar {
  width: 50px;
  height: 50px;
  object-fit: cover;
}

.btn-group .btn {
  margin: 0 2px;
}

.table > tbody > tr > td {
  vertical-align: middle;
}
</style>

<?php include_once('layouts/footer.php'); ?>
