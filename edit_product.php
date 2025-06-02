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

// Validar que el producto existe
if(!$product){
  $session->msg("d","ID de producto no encontrado.");
  redirect('product.php');
}

// Obtener la cantidad del inventario de forma segura
$quantity = 0;
if(isset($product['ID'])) {
    $sql = "SELECT i.Cantidad FROM inventario i WHERE i.Id_Producto = '{$product['ID']}'";
    $inventory = find_by_sql($sql);
    $quantity = $inventory && isset($inventory[0]['Cantidad']) ? (int)$inventory[0]['Cantidad'] : 0;
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
       $p_price = remove_junk($db->escape($_POST['product-price']));
       $p_cat   = remove_junk($db->escape($_POST['product-category']));
       $p_provider = remove_junk($db->escape($_POST['product-provider']));
       
       // Primero actualizar la tabla producto
       $query = "UPDATE producto SET 
                Nombre = '{$p_name}', 
                Descripcion = '{$p_desc}', 
                Precio = '{$p_price}', 
                Id_Proveedor = '{$p_provider}', 
                Id_Categoria = '{$p_cat}' 
                WHERE ID = '{$product['ID']}'";
       
       $result = $db->query($query);
       
       if($result){
           // Luego actualizar el inventario
           $query2 = "UPDATE inventario SET Cantidad = '{$p_qty}' WHERE Id_Producto = '{$product['ID']}'";
           $result2 = $db->query($query2);
           
           if($result2){
               $session->msg('s', "Producto actualizado exitosamente.");
               redirect('product.php', false);
           } else {
               $session->msg('d', 'Error al actualizar el inventario: ' . $db->con->error);
               redirect('edit_product.php?id='.$product['ID'], false);
           }
       } else {
           $session->msg('d', 'Error al actualizar el producto: ' . $db->con->error);
           redirect('edit_product.php?id='.$product['ID'], false);
       }
   } else {
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
          <div class="col-md-12">
            <form method="post" action="edit_product.php?id=<?php echo (int)$product['ID'] ?>" class="clearfix" id="editProductForm">
              <!-- Información Básica -->
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Información Básica</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="product-title">Nombre del Producto</label>
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-tag"></i>
                          </span>
                          <input type="text" class="form-control" name="product-title" id="product-title" value="<?php echo remove_junk($product['Nombre']);?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="product-price">Precio</label>
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-usd"></i>
                          </span>
                          <input type="number" class="form-control" name="product-price" id="product-price" value="<?php echo remove_junk($product['Precio']);?>" step="0.01" min="0" required>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="product-description">Descripción</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-align-left"></i>
                      </span>
                      <textarea class="form-control" name="product-description" id="product-description" rows="3" required><?php echo remove_junk($product['Descripcion']);?></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Inventario y Categorización -->
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Inventario y Categorización</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product-quantity">Cantidad en Inventario</label>
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-shopping-cart"></i>
                          </span>
                          <input type="number" class="form-control" name="product-quantity" id="product-quantity" value="<?php echo remove_junk($quantity); ?>" min="0" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product-category">Categoría</label>
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-list"></i>
                          </span>
                          <select class="form-control" name="product-category" id="product-category" required>
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($all_categories as $cat): ?>
                              <option value="<?php echo (int)$cat['ID']; ?>" <?php if(isset($product['Id_Categoria']) && $product['Id_Categoria'] === $cat['ID']): echo "selected"; endif; ?>>
                                <?php echo remove_junk($cat['Nombre']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product-provider">Proveedor</label>
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-trademark"></i>
                          </span>
                          <select name="product-provider" id="product-provider" class="form-control" required>
                            <option value="">Seleccionar proveedor</option>
                            <?php foreach ($all_providers as $provider): ?>
                              <option value="<?php echo (int)$provider['ID']; ?>" <?php if(isset($product['Id_Proveedor']) && $product['Id_Proveedor'] === $provider['ID']): echo "selected"; endif; ?>>
                                <?php echo remove_junk($provider['Nombre']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botones de Acción -->
              <div class="form-group text-center" style="margin-top: 30px;">
                <button type="submit" name="product" class="btn btn-success btn-lg">
                  <i class="glyphicon glyphicon-ok"></i> Guardar Cambios
                </button>
                <a href="product.php" class="btn btn-danger btn-lg">
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
.panel {
  border: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
  transition: all 0.3s cubic-bezier(.25,.8,.25,1);
  margin-bottom: 20px;
}

.panel:hover {
  box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}

.panel-info {
  border-color: #bce8f1;
}

.panel-info > .panel-heading {
  background-color: #d9edf7;
  border-color: #bce8f1;
  color: #31708f;
}

.panel-title {
  font-size: 18px;
  font-weight: 600;
}

.form-group {
  margin-bottom: 20px;
}

.input-group-addon {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  color: #283593;
}

.form-control {
  border: 1px solid #e9ecef;
  padding: 10px;
  height: auto;
}

.form-control:focus {
  border-color: #283593;
  box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
}

.btn {
  padding: 10px 20px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}

.btn-success {
  background: linear-gradient(135deg, #28a745 0%, #218838 100%);
  border: none;
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  border: none;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn i {
  margin-right: 8px;
}

textarea.form-control {
  min-height: 100px;
}

@media (max-width: 768px) {
  .col-md-6, .col-md-4 {
    margin-bottom: 15px;
  }
  
  .btn {
    width: 100%;
    margin-bottom: 10px;
  }
}
</style>

<script>
$(document).ready(function() {
  // Validación del formulario
  $('#editProductForm').on('submit', function(e) {
    var isValid = true;
    
    // Validar campos requeridos
    $(this).find('[required]').each(function() {
      if (!$(this).val()) {
        isValid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    
    // Validar precio y cantidad
    var price = $('#product-price').val();
    var quantity = $('#product-quantity').val();
    
    if (price < 0) {
      isValid = false;
      $('#product-price').addClass('is-invalid');
    }
    
    if (quantity < 0) {
      isValid = false;
      $('#product-quantity').addClass('is-invalid');
    }
    
    if (!isValid) {
      e.preventDefault();
      alert('Por favor, complete todos los campos requeridos correctamente.');
    }
  });
  
  // Limpiar clases de validación al cambiar valores
  $('input, select, textarea').on('change', function() {
    $(this).removeClass('is-invalid');
  });
  
  // Formatear precio al escribir
  $('#product-price').on('input', function() {
    var value = $(this).val();
    if (value < 0) {
      $(this).val(0);
    }
  });
  
  // Formatear cantidad al escribir
  $('#product-quantity').on('input', function() {
    var value = $(this).val();
    if (value < 0) {
      $(this).val(0);
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>
