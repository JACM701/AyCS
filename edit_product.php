<?php
  $page_title = 'Editar producto';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
$product = find_by_id('productos',(int)$_GET['id']);
$all_categories = find_all('categories');

// Obtener la cantidad del inventario
$sql = "SELECT i.Cantidad FROM inventario i WHERE i.Id_Producto = '{$product['Id_Productos']}'";

// Depuración: Mostrar la consulta SQL para obtener inventario y el ID del producto
echo "Debug Inventory SQL: {$sql}<br>";
echo "Debug Product ID for Inventory: {$product['Id_Productos']}<br>";

$inventory = find_by_sql($sql);
$quantity = $inventory ? $inventory[0]['Cantidad'] : 0;

if(!$product){
  $session->msg("d","ID de producto no encontrado.");
  redirect('product.php');
}
?>
<?php
 if(isset($_POST['product'])){
    $req_fields = array('product-title','product-description','product-quantity','product-cost','product-categorie');
    validate_fields($req_fields);

   if(empty($errors)){
       $p_name  = remove_junk($db->escape($_POST['product-title']));
       $p_desc  = remove_junk($db->escape($_POST['product-description']));
       $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
       $p_cost  = remove_junk($db->escape($_POST['product-cost']));
       $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
       
       // Manejo de la imagen
       $p_photo = $product['Foto'];
       if(isset($_FILES['product-photo']) && $_FILES['product-photo']['size'] > 0){
         $p_photo = upload_image($_FILES['product-photo'], 'uploads/products/');
       }

       // Depuración: Mostrar variables antes de la consulta UPDATE
       echo "Debug: p_name = {$p_name}, p_desc = {$p_desc}, p_qty = {$p_qty}, p_cost = {$p_cost}, p_cat = {$p_cat}, p_photo = {$p_photo}, product_id = {$product['Id_Productos']}";
       exit(); // Detener ejecución para ver los valores

       $query   = "UPDATE productos SET";
       $query  .=" Nombre ='{$p_name}', Descripcion ='{$p_desc}',";
       $query  .=" Costo ='{$p_cost}', Foto ='{$p_photo}', Categoria ='{$p_cat}'";
       $query  .=" WHERE Id_Productos ='{$product['Id_Productos']}'";
       $result = $db->query($query);
       
       if($result && $db->affected_rows() === 1){
         // Actualizar inventario
         $query2 = "UPDATE inventario SET Cantidad ='{$p_qty}' WHERE Id_Producto ='{$product['Id_Productos']}'";
         if($db->query($query2)){
           $session->msg('s',"Producto actualizado exitosamente.");
           redirect('product.php', false);
         } else {
           // Manejo de errores detallado para la actualización del inventario
           $session->msg('d',' Error al actualizar el inventario: ' . $db->con->error);
           redirect('edit_product.php?id='.$product['Id_Productos'], false);
         }
       } else {
         $session->msg('d',' Lo siento, actualización falló.');
         redirect('edit_product.php?id='.$product['Id_Productos'], false);
       }
   } else{
       $session->msg("d", $errors);
       redirect('edit_product.php?id='.$product['Id_Productos'], false);
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
            <!-- Vista previa de la imagen -->
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>Imagen del Producto</strong>
              </div>
              <div class="panel-body text-center">
                <div class="product-image-preview">
                  <?php if($product['Foto']): ?>
                    <img src="uploads/products/<?php echo $product['Foto']; ?>" class="img-responsive img-thumbnail" id="image-preview" alt="Vista previa">
                  <?php else: ?>
                    <img src="uploads/products/no_image.jpg" class="img-responsive img-thumbnail" id="image-preview" alt="Sin imagen">
                  <?php endif; ?>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                  <label for="product-photo" class="btn btn-primary">
                    <i class="glyphicon glyphicon-camera"></i> Cambiar Imagen
                  </label>
                  <input type="file" name="product-photo" id="product-photo" style="display: none;" accept="image/*">
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <form method="post" action="edit_product.php?id=<?php echo (int)$product['Id_Productos'] ?>" enctype="multipart/form-data">
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
                    <label for="product-categorie">Categoría</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-list"></i>
                      </span>
                      <select class="form-control" name="product-categorie" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($all_categories as $cat): ?>
                          <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['Categoria'] === $cat['id']): echo "selected"; endif; ?>>
                            <?php echo remove_junk($cat['name']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
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
              </div>

              <div class="form-group">
                <label for="product-cost">Precio</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <input type="number" class="form-control" name="product-cost" value="<?php echo remove_junk($product['Costo']);?>" step="0.01" required>
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
document.getElementById('product-photo').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('image-preview').src = e.target.result;
    }
    reader.readAsDataURL(file);
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>
