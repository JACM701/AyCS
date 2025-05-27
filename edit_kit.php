<?php
  $page_title = 'Editar Kit';
  require_once('includes/load.php');
  page_require_level(2);

  $all_products = find_all('productos');
  
  // Obtener el kit
  $kit_id = (int)$_GET['id'];
  $kit = find_by_id('kits', $kit_id);
  
  if(!$kit){
    $session->msg("d", "ID de kit no encontrado.");
    redirect('kits.php');
  }
  
  // Obtener los productos del kit
  $kit_items = find_kit_items($kit_id);
?>

<?php
 if(isset($_POST['edit_kit'])){
    $req_fields = array('kit-name', 'kit-description', 'kit-base-price', 'kit-camera-price');
    validate_fields($req_fields);

   if(empty($errors)){
       $k_name  = remove_junk($db->escape($_POST['kit-name']));
       $k_desc  = remove_junk($db->escape($_POST['kit-description']));
       $k_base_price = remove_junk($db->escape($_POST['kit-base-price']));
       $k_camera_price = remove_junk($db->escape($_POST['kit-camera-price']));
       
       $query  = "UPDATE kits SET ";
       $query .= "nombre = '{$k_name}', descripcion = '{$k_desc}', ";
       $query .= "precio_base = '{$k_base_price}', precio_por_camara = '{$k_camera_price}' ";
       $query .= "WHERE id = '{$kit_id}'";
       
       if($db->query($query)){
         // Eliminar productos existentes
         $query = "DELETE FROM kit_items WHERE kit_id = '{$kit_id}'";
         $db->query($query);
         
         // Procesar los productos del kit
         if(isset($_POST['product_id']) && is_array($_POST['product_id'])){
           foreach($_POST['product_id'] as $key => $product_id){
             if(!empty($product_id)){
               $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
               $per_camera = isset($_POST['per_camera'][$key]) ? 1 : 0;
               $is_service = isset($_POST['is_service'][$key]) ? 1 : 0;
               
               $query2 = "INSERT INTO kit_items (kit_id, producto_id, cantidad_base, cantidad_por_camara, es_por_camara, es_servicio)";
               $query2 .= " VALUES ('{$kit_id}', '{$product_id}', '{$quantity}', '0.00', '{$per_camera}', '{$is_service}')";
               $db->query($query2);
             }
           }
         }
         
         $session->msg('s',"Kit actualizado exitosamente.");
         redirect('kits.php', false);
       } else {
         $session->msg('d',' Lo siento, actualización falló.');
         redirect('edit_kit.php?id='.$kit_id, false);
       }
   } else{
       $session->msg("d", $errors);
       redirect('edit_kit.php?id='.$kit_id, false);
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
          <span class="glyphicon glyphicon-th"></span>
          <span>Editar Kit</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="edit_kit.php?id=<?php echo $kit_id; ?>" class="clearfix">
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="kit-name">Nombre del Kit</label>
                  <input type="text" class="form-control" name="kit-name" value="<?php echo remove_junk($kit['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="kit-base-price">Precio Base</label>
                  <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" name="kit-base-price" step="0.01" value="<?php echo remove_junk($kit['precio_base']); ?>" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="kit-description">Descripción</label>
                  <textarea class="form-control" name="kit-description" rows="3"><?php echo remove_junk($kit['descripcion']); ?></textarea>
                </div>
                <div class="col-md-6">
                  <label for="kit-camera-price">Precio por Cámara</label>
                  <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" name="kit-camera-price" step="0.01" value="<?php echo remove_junk($kit['precio_por_camara']); ?>" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <h4>Productos del Kit</h4>
                  <div id="productos-container">
                    <?php if(!empty($kit_items)): ?>
                      <?php foreach($kit_items as $item): ?>
                        <div class="row producto-item">
                          <div class="col-md-4">
                            <select class="form-control" name="product_id[]">
                              <option value="">Seleccione un producto</option>
                              <?php foreach($all_products as $product): ?>
                                <option value="<?php echo (int)$product['Id_Productos']; ?>" <?php if($product['Id_Productos'] == $item['producto_id']) echo 'selected'; ?>>
                                  <?php echo remove_junk($product['Nombre']); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <input type="number" class="form-control" name="quantity[]" placeholder="Cantidad" min="1" value="<?php echo (int)$item['cantidad_base']; ?>">
                          </div>
                          <div class="col-md-2">
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="per_camera[]" <?php if($item['es_por_camara']) echo 'checked'; ?>> Por cámara
                              </label>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="is_service[]" <?php if($item['es_servicio']) echo 'checked'; ?>> Es servicio
                              </label>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-remove-product">
                              <span class="glyphicon glyphicon-trash"></span>
                            </button>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="row producto-item">
                        <div class="col-md-4">
                          <select class="form-control" name="product_id[]">
                            <option value="">Seleccione un producto</option>
                            <?php foreach($all_products as $product): ?>
                              <option value="<?php echo (int)$product['Id_Productos']; ?>">
                                <?php echo remove_junk($product['Nombre']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <input type="number" class="form-control" name="quantity[]" placeholder="Cantidad" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="per_camera[]"> Por cámara
                            </label>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="is_service[]"> Es servicio
                            </label>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <button type="button" class="btn btn-danger btn-remove-product">
                            <span class="glyphicon glyphicon-trash"></span>
                          </button>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                  <button type="button" class="btn btn-success" id="add-product">
                    <span class="glyphicon glyphicon-plus"></span> Agregar Producto
                  </button>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <button type="submit" name="edit_kit" class="btn btn-primary">Actualizar Kit</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Agregar nuevo producto
  $('#add-product').click(function() {
    var newRow = $('.producto-item:first').clone();
    newRow.find('input').val('');
    newRow.find('select').val('');
    newRow.find('input[type="checkbox"]').prop('checked', false);
    $('#productos-container').append(newRow);
  });

  // Eliminar producto
  $(document).on('click', '.btn-remove-product', function() {
    if($('.producto-item').length > 1) {
      $(this).closest('.producto-item').remove();
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?> 