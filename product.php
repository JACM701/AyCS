<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  // Consulta adaptada para la base de datos actual (naycs)
  // Usando tabla 'producto', 'inventario', 'categoria_producto', 'proveedor'
  $query = "SELECT ";
  $query .= "p.ID as ID, p.Nombre, p.Descripcion, p.Precio, ";
  $query .= "i.Cantidad, ";
  $query .= "cp.Nombre as category_name, ";
  $query .= "pr.Nombre as provider_name ";
  $query .= "FROM producto p ";
  $query .= "LEFT JOIN inventario i ON p.ID = i.Id_Producto ";
  $query .= "LEFT JOIN categoria_producto cp ON p.Id_Categoria = cp.ID ";
  $query .= "LEFT JOIN proveedor pr ON p.Id_Proveedor = pr.ID ";
  $query .= "ORDER BY p.ID DESC";
            
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
                  <th> Nombre </th>
                  <th> Categoría </th>
                  <th> Proveedor </th>
                  <th> Descripción </th>
                  <th class="text-center" style="width: 10%;"> Stock </th>
                  <th class="text-center" style="width: 10%;"> Precio </th>
                  <th class="text-center" style="width: 100px;"> Acciones </th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $product):?>
                <tr>
                  <td class="text-center"><?php echo $product['ID'];?></td>
                  <td> <?php echo remove_junk($product['Nombre']); ?></td>
                  <td> <?php echo remove_junk($product['category_name'] ?? 'Sin categoría'); ?></td>
                  <td> <?php echo remove_junk($product['provider_name'] ?? 'Sin proveedor'); ?></td>
                  <td> <?php echo remove_junk($product['Descripcion'] ?? 'Sin descripción'); ?></td>
                  <td class="text-center"> <?php echo remove_junk(isset($product['Cantidad']) ? $product['Cantidad'] : 0); ?></td>
                  <td class="text-center"> $<?php echo number_format(isset($product['Precio']) ? $product['Precio'] : 0, 2); ?></td>
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
