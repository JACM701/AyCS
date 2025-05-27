<?php
  $page_title = 'Agregar Kit';
  require_once('includes/load.php');
  page_require_level(2);

  $all_products = find_all('productos');
?>

<?php
 if(isset($_POST['add_kit'])){
    $req_fields = array('kit-name', 'kit-description', 'kit-base-price', 'kit-camera-price');
    validate_fields($req_fields);

   if(empty($errors)){
       $k_name  = remove_junk($db->escape($_POST['kit-name']));
       $k_desc  = remove_junk($db->escape($_POST['kit-description']));
       $k_base_price = remove_junk($db->escape($_POST['kit-base-price']));
       $k_camera_price = remove_junk($db->escape($_POST['kit-camera-price']));
       
       $query  = "INSERT INTO kits (nombre, descripcion, precio_base, precio_por_camara)";
       $query .= " VALUES ('{$k_name}', '{$k_desc}', '{$k_base_price}', '{$k_camera_price}')";
       
       if($db->query($query)){
         $kit_id = $db->insert_id();
         
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
         
         $session->msg('s',"Kit agregado exitosamente.");
         redirect('kits.php', false);
       } else {
         $session->msg('d',' Lo siento, registro falló.');
         redirect('add_kit.php', false);
       }
   } else{
       $session->msg("d", $errors);
       redirect('add_kit.php', false);
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
          <span>Agregar Nuevo Kit</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_kit.php" class="clearfix">
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="kit-name">Nombre del Kit</label>
                  <input type="text" class="form-control" name="kit-name" required>
                </div>
                <div class="col-md-6">
                  <label for="kit-base-price">Precio Base</label>
                  <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" name="kit-base-price" step="0.01" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="kit-description">Descripción</label>
                  <textarea class="form-control" name="kit-description" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                  <label for="kit-camera-price">Precio por Cámara</label>
                  <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" name="kit-camera-price" step="0.01" value="450.00" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <h4>Productos del Kit</h4>
                  <div id="productos-container">
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
                  <button type="submit" name="add_kit" class="btn btn-primary">Guardar Kit</button>
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