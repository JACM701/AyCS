<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  $query = "SELECT p.*, i.Cantidad, c.name as category_name, pr.Nombre as provider_name 
            FROM productos p 
            LEFT JOIN inventario i ON p.Id_Productos = i.Id_Producto
            LEFT JOIN categories c ON p.Categoria = c.id
            LEFT JOIN proveedores pr ON p.Id_Proveedor = pr.Id_Proveedor
            ORDER BY p.Id_Productos DESC";
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
                  <th class="text-center" style="width: 10%;"> Stock </th>
                  <th class="text-center" style="width: 10%;"> Costo </th>
                  <th class="text-center" style="width: 10%;"> Precio Público </th>
                  <th class="text-center" style="width: 10%;"> Precio Instalador </th>
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
                  <td> <?php echo remove_junk($product['provider_name'] ?? 'Sin proveedor'); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['Cantidad']); ?></td>
                  <td class="text-center"> $<?php echo number_format($product['Costo'], 2); ?></td>
                  <td class="text-center"> $<?php echo number_format($product['Precio_Publico'], 2); ?></td>
                  <td class="text-center"> $<?php echo number_format($product['Precio_Instalador'], 2); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <button type="button" class="btn btn-info btn-xs" onclick="toggleDetails(<?php echo $product['Id_Productos']; ?>)">
                        <i class="glyphicon glyphicon-plus"></i>
                      </button>
                      <a href="edit_product.php?id=<?php echo (int)$product['Id_Productos'];?>" class="btn btn-warning btn-xs" title="Editar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_product.php?id=<?php echo (int)$product['Id_Productos'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Está seguro de eliminar este producto?');">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>
                </tr>
                <tr id="details-<?php echo $product['Id_Productos']; ?>" class="details-row" style="display: none;">
                  <td colspan="10">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="panel panel-default">
                          <div class="panel-body">
                            <div class="row">
                              <div class="col-md-6">
                                <h5><strong>Descripción:</strong></h5>
                                <p><?php echo remove_junk($product['Descripcion']); ?></p>
                              </div>
                              <div class="col-md-6">
                                <h5><strong>Detalles de Precios:</strong></h5>
                                <table class="table table-bordered">
                                  <tr>
                                    <td>Margen de Utilidad:</td>
                                    <td><?php echo number_format($product['Margen_Utilidad'], 2); ?>%</td>
                                  </tr>
                                  <tr>
                                    <td>Ganancia:</td>
                                    <td>$<?php echo number_format($product['Ganancia'], 2); ?></td>
                                  </tr>
                                  <tr>
                                    <td>% Aumento:</td>
                                    <td>
                                      <?php
                                        $cost = $product['Costo'];
                                        $public_price = $product['Precio_Publico'];
                                        $installer_price = $product['Precio_Instalador'];
                                        
                                        $public_increase = 0;
                                        $installer_increase = 0;
                                        
                                        if ($cost > 0) {
                                            $public_increase = (($public_price - $cost) / $cost) * 100;
                                            $installer_increase = (($installer_price - $cost) / $cost) * 100;
                                        }
                                        
                                        echo "Público: " . number_format($public_increase, 2) . "%<br>";
                                        echo "Instalador: " . number_format($installer_increase, 2) . "%";
                                      ?>
                                    </td>
                                  </tr>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
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

.details-row {
  background-color: #f9f9f9;
}

.details-row .panel {
  margin-bottom: 0;
  border: none;
  box-shadow: none;
}

.details-row .panel-body {
  padding: 15px;
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

<script>
function toggleDetails(id) {
  const detailsRow = document.getElementById('details-' + id);
  const button = event.currentTarget.querySelector('i');
  
  if (detailsRow.style.display === 'none') {
    detailsRow.style.display = 'table-row';
    button.classList.remove('glyphicon-plus');
    button.classList.add('glyphicon-minus');
  } else {
    detailsRow.style.display = 'none';
    button.classList.remove('glyphicon-minus');
    button.classList.add('glyphicon-plus');
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>
