<?php
  $page_title = 'Editar producto';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
$product = find_by_id('producto',(int)$_GET['id']);
$all_categories = find_all('categoria_producto');
$all_providers = find_all('proveedor');

// Obtener la cantidad del inventario
$sql = "SELECT i.Cantidad FROM inventario i WHERE i.Id_Producto = '{$product['ID']}'";

// Depuración: Mostrar la consulta SQL para obtener inventario y el ID del producto
// echo "Debug Inventory SQL: {$sql}<br>";
// echo "Debug Product ID for Inventory: {$product['ID']}<br>";

$inventory = find_by_sql($sql);
$quantity = $inventory ? $inventory[0]['Cantidad'] : 0;

if(!$product){
  $session->msg("d","ID de producto no encontrado.");
  redirect('product.php');
}
?>
<?php
 if(isset($_POST['product'])){
    $req_fields = array('product-title','product-description','product-quantity','product-price','product-category', 'product-provider');
    validate_fields($req_fields);

   if(empty($errors)){
       $p_name  = remove_junk($db->escape($_POST['product-title']));
       $p_desc  = remove_junk($db->escape($_POST['product-description']));
       $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
       $p_price  = remove_junk($db->escape($_POST['product-price']));
       $p_cat   = remove_junk($db->escape($_POST['product-category']));
       $p_provider = remove_junk($db->escape($_POST['product-provider']));
       
       $query   = "UPDATE producto SET";
       $query  .=" Nombre ='{$p_name}', Descripcion ='{$p_desc}', Precio ='{$p_price}', Id_Proveedor ='{$p_provider}', Id_Categoria ='{$p_cat}'";
       $query  .=" WHERE ID ='{$product['ID']}'";
       $result = $db->query($query);
       
       if($result && $db->affected_rows() === 1){
         // Actualizar inventario
         $query2 = "UPDATE inventario SET Cantidad ='{$p_qty}' WHERE Id_Producto ='{$product['ID']}'";
         if($db->query($query2)){
           $session->msg('s',"Producto actualizado exitosamente.");
           redirect('product.php', false);
         } else {
           // Manejo de errores detallado para la actualización del inventario
           $session->msg('d',' Error al actualizar el inventario: ' . $db->con->error);
           redirect('edit_product.php?id='.$product['ID'], false);
         }
       } else {
         $session->msg('d',' Lo siento, actualización falló.');
         redirect('edit_product.php?id='.$product['ID'], false);
       }
   } else{
       $session->msg("d", $errors);
       redirect('edit_product.php?id='.$product['ID'], false);
   }
 }
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
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-edit"></span>
          <span>Editar Producto</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <!-- Eliminada vista previa de la imagen y formulario de carga -->
            <!--
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>Imagen del Producto</strong>
              </div>
              <div class="panel-body text-center">
                <div class="product-image-preview" style="width: 150px; height: 150px; border: 1px solid #ddd; margin: 0 auto 10px; background-size: cover; background-position: center; background-image: url('uploads/products/<?php echo $product['Foto'] ? $product['Foto'] : 'no_image.jpg'; ?>');">
                </div>
                <div class="form-group" style="margin-top: 15px;">
                  <label for="product-photo" class="btn btn-primary">
                    <i class="glyphicon glyphicon-camera"></i> Cambiar Imagen
                  </label>
                  <input type="file" name="product-photo" id="product-photo" style="display: none;" accept="image/*">
                </div>
              </div>
            </div>
            -->
          </div>

          <div class="col-md-8">
            <form method="post" action="edit_product.php?id=<?php echo (int)$product['ID'] ?>" enctype="multipart/form-data">
              <div class="form-group">
                <label for="product-title">Nombre del Producto</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-tag"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['Nombre']);?>" required>
                </div>
              </div>

              <div class="form-group">
                <label for="product-description">Descripción</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-align-left"></i>
                  </span>
                  <textarea class="form-control" name="product-description" rows="3" required><?php echo remove_junk($product['Descripcion']);?></textarea>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="product-category">Categoría</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-list"></i>
                      </span>
                      <select class="form-control" name="product-category" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($all_categories as $cat): ?>
                          <option value="<?php echo (int)$cat['ID']; ?>" <?php if($product['Id_Categoria'] === $cat['ID']): echo "selected"; endif; ?>>
                            <?php echo remove_junk($cat['Nombre']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                   <div class="form-group">
                    <label for="product-provider">Proveedor</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-trademark"></i>
                      </span>
                       <select name="product-provider" class="form-control" required>
                          <option value="">Seleccionar proveedor</option>
                          <?php foreach ($all_providers as $provider): ?>
                            <option value="<?php echo (int)$provider['Id_Proveedor']; ?>" <?php if($product['Id_Proveedor'] === $provider['Id_Proveedor']): echo "selected"; endif; ?>>
                              <?php echo remove_junk($provider['Nombre']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                 <div class="col-md-3">
                   <div class="form-group">
                    <label for="product-quantity">Cantidad en Inventario</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-shopping-cart"></i>
                      </span>
                      <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($quantity); ?>" required>
                    </div>
                  </div>
                 </div>
                <div class="col-md-3">
                   <div class="form-group">
                    <label for="product-price">Precio</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                      </span>
                      <input type="number" class="form-control" name="product-price" id="product-price" value="<?php echo remove_junk($product['Precio']);?>" step="0.01" required>
                    </div>
                  </div>
                 </div>
              </div>

              <div class="form-group text-center" style="margin-top: 30px;">
                <button type="submit" name="product" class="btn btn-success">
                  <i class="glyphicon glyphicon-ok"></i> Guardar Cambios
                </button>
                <a href="product.php" class="btn btn-danger">
                  <i class="glyphicon glyphicon-remove"></i> Cancelar
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.product-image-preview {
  width: 100%;
  height: 250px;
  border: 2px dashed #ccc;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
  overflow: hidden;
}

.product-image-preview img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/* Añadir margen inferior a todos los form-groups dentro del formulario */
.panel-body .col-md-8 form .form-group {
  margin-bottom: 20px;
}

/* Asegurar que las columnas dentro de la fila del formulario tengan padding */
.panel-body .col-md-8 form .row > div[class*="col-"] {
  padding-left: 15px;
  padding-right: 15px;
}

.input-group-addon {
  background-color: #f8f9fa;
}

.btn-success {
  margin-right: 10px;
}

.panel-heading {
  background-color: #f8f9fa !important;
}

.panel {
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
  transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}

.panel:hover {
  box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}
</style>

<script>
$(document).ready(function() {
  // Script para calcular precios basados en porcentaje y actualizar margen/ganancia (Eliminado)
  // No hay cálculo de margen/ganancia ni precios basados en porcentaje con la nueva estructura de BD
});
</script>

<?php include_once('layouts/footer.php'); ?>
