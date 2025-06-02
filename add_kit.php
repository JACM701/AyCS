<?php
  $page_title = 'Agregar Kit';
  require_once('includes/load.php');
  page_require_level(2);

  // Obtener lista de productos con información de inventario
  $sql = "SELECT p.ID, p.Nombre, p.Precio, i.Cantidad 
          FROM producto p 
          LEFT JOIN inventario i ON p.ID = i.Id_Producto 
          ORDER BY p.Nombre";
  $productos = find_by_sql($sql);
?>

<?php
 if(isset($_POST['add_kit'])){
    $req_fields = array('kit-name', 'kit-price');
    validate_fields($req_fields);

   if(empty($errors)){
       $k_name  = remove_junk($db->escape($_POST['kit-name']));
       $k_price = remove_junk($db->escape($_POST['kit-price']));
       
       $query  = "INSERT INTO kit (Nombre, Precio)";
       $query .= " VALUES ('{$k_name}', '{$k_price}')";
       
       if($db->query($query)){
         $kit_id = $db->insert_id();
         
         // Procesar los productos del kit
         if(isset($_POST['product_id']) && is_array($_POST['product_id'])){
           foreach($_POST['product_id'] as $key => $product_id){
             if(!empty($product_id)){
               $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
               
               // Actualizar el kit con el ID del producto
               $query2 = "INSERT INTO kit_producto (Id_Kit, Id_Producto, Cantidad) 
                         VALUES ('{$kit_id}', '{$product_id}', '{$quantity}')";
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

<!-- Agregar jQuery antes de nuestro script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
          <span>Agregar Nuevo Kit</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_kit.php" class="clearfix">
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <label for="kit-name">Nombre del Kit</label>
                <input type="text" class="form-control" name="kit-name" required>
              </div>
              <div class="col-md-6">
                <label for="kit-price">Precio Base</label>
                <div class="input-group">
                  <span class="input-group-addon">$</span>
                  <input type="number" class="form-control" name="kit-price" step="0.01" required>
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
                    <div class="col-md-6">
                      <select class="form-control" name="product_id[]">
                        <option value="">Seleccione un producto</option>
                        <?php foreach($productos as $product): ?>
                          <option value="<?php echo (int)$product['ID']; ?>">
                            <?php echo remove_junk($product['Nombre']); ?> - $<?php echo number_format($product['Precio'], 2); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <input type="number" class="form-control" name="quantity[]" placeholder="Cantidad" min="1" value="1">
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

.btn-primary {
    background: linear-gradient(135deg, #283593 0%, #1a237e 100%);
    border: none;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn i {
    margin-right: 8px;
}

.producto-item {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.producto-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .col-md-6, .col-md-4, .col-md-2 {
        margin-bottom: 15px;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar nuevo producto
    document.getElementById('add-product').addEventListener('click', function() {
        var newRow = `
            <div class="row producto-item">
                <div class="col-md-6">
                    <select class="form-control" name="product_id[]">
                        <option value="">Seleccione un producto</option>
                        <?php foreach($productos as $product): ?>
                            <option value="<?php echo (int)$product['ID']; ?>">
                                <?php echo remove_junk($product['Nombre']); ?> - $<?php echo number_format($product['Precio'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="quantity[]" placeholder="Cantidad" min="1" value="1">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-remove-product">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </div>
            </div>
        `;
        document.getElementById('productos-container').insertAdjacentHTML('beforeend', newRow);
    });

    // Eliminar producto
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-product')) {
            var items = document.querySelectorAll('.producto-item');
            if (items.length > 1) {
                e.target.closest('.producto-item').remove();
            }
        }
    });
});
</script>

<?php include_once('layouts/footer.php'); ?> 