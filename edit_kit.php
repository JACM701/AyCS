<?php
$page_title = 'Editar Kit';
require_once('includes/load.php');

// Verificar si el usuario está logueado
if (!$session->isUserLoggedIn()) {
    redirect('index.php', false);
}

// Verificar si se proporcionó un ID de kit
if (!isset($_GET['id'])) {
    $session->msg("d", "ID de kit no proporcionado.");
    redirect('kits.php', false);
}

$kit_id = (int)$_GET['id'];

// Obtener los detalles del kit
$kit = find_by_id('kit', $kit_id);
if (!$kit) {
    $session->msg("d", "Kit no encontrado.");
    redirect('kits.php', false);
}

// Obtener todos los productos disponibles
$sql = "SELECT p.*, i.Cantidad 
        FROM producto p 
        LEFT JOIN inventario i ON p.ID = i.Id_Producto 
        ORDER BY p.Nombre";
$productos = find_by_sql($sql);

// Obtener los productos del kit
$sql = "SELECT kp.*, p.Nombre as producto_nombre, p.Precio as producto_precio 
        FROM kit_producto kp 
        LEFT JOIN producto p ON kp.Id_Producto = p.ID 
        WHERE kp.Id_Kit = '{$kit_id}'";
$kit_productos = find_by_sql($sql);

$all_products = find_all('producto');
?>

<?php
 if(isset($_POST['edit_kit'])){
    $req_fields = array('kit-name', 'kit-description', 'kit-price');
    validate_fields($req_fields);

   if(empty($errors)){
       $name = remove_junk($db->escape($_POST['kit-name']));
       $description = remove_junk($db->escape($_POST['kit-description']));
       $price = remove_junk($db->escape($_POST['kit-price']));
       
       // Validación adicional de campos
       if(empty($name) || empty($price)) {
           $session->msg("d", "El nombre y el precio son campos obligatorios.");
           redirect('edit_kit.php?id='.$kit_id, false);
       }
       
       if(!is_numeric($price) || $price <= 0) {
           $session->msg("d", "El precio debe ser un número positivo.");
           redirect('edit_kit.php?id='.$kit_id, false);
       }
       
       // Verificar que haya al menos un producto seleccionado
       if(!isset($_POST['producto_id']) || !is_array($_POST['producto_id']) || empty(array_filter($_POST['producto_id']))){
           $session->msg("d", "Debe seleccionar al menos un producto para el kit.");
           redirect('edit_kit.php?id='.$kit_id, false);
       }
       
       // Iniciar transacción
       $db->query("START TRANSACTION");
       
       try {
           // Actualizar información básica del kit
           $sql = "UPDATE kit SET 
                   Nombre = '{$name}', 
                   Descripcion = '{$description}', 
                   Precio = '{$price}' 
                   WHERE ID = '{$kit_id}'";
           
           if(!$db->query($sql)) {
               throw new Exception("Error al actualizar el kit: " . $db->error());
           }
           
           // Eliminar productos existentes
           $sql = "DELETE FROM kit_producto WHERE Id_Kit = '{$kit_id}'";
           if(!$db->query($sql)) {
               throw new Exception("Error al eliminar productos existentes: " . $db->error());
           }
           
           // Validar y preparar los productos antes de insertarlos
           $productos_a_insertar = array();
           foreach($_POST['producto_id'] as $index => $producto_id){
               if(!empty($producto_id)){
                   $cantidad = isset($_POST['cantidad'][$index]) ? (int)$_POST['cantidad'][$index] : 1;
                   
                   // Validación más estricta de la cantidad
                   if($cantidad <= 0 || !is_numeric($cantidad)) {
                       throw new Exception("La cantidad debe ser un número positivo");
                   }
                   
                   // Verificar que el producto existe y está activo
                   $producto = find_by_id('producto', $producto_id);
                   if(!$producto) {
                       throw new Exception("El producto con ID {$producto_id} no existe");
                   }
                   
                   // Verificar que no se duplique el producto en el kit
                   if(isset($productos_a_insertar[$producto_id])) {
                       throw new Exception("No puede agregar el mismo producto más de una vez");
                   }
                   
                   $productos_a_insertar[$producto_id] = array(
                       'Id_Kit' => $kit_id,
                       'Id_Producto' => $producto_id,
                       'Cantidad' => $cantidad
                   );
               }
           }
           
           // Insertar todos los productos en una sola consulta
           if(!empty($productos_a_insertar)) {
               $values = array();
               foreach($productos_a_insertar as $producto) {
                   $values[] = "('{$producto['Id_Kit']}', '{$producto['Id_Producto']}', '{$producto['Cantidad']}')";
               }
               
               $sql = "INSERT INTO kit_producto (Id_Kit, Id_Producto, Cantidad) VALUES " . implode(',', $values);
               
               if(!$db->query($sql)){
                   throw new Exception("Error al insertar productos: " . $db->error());
               }
           }
           
           // Confirmar transacción
           $db->query("COMMIT");
           $session->msg("s", "Kit actualizado exitosamente.");
           redirect('kits.php', false);
           
       } catch (Exception $e) {
           // Revertir transacción en caso de error
           $db->query("ROLLBACK");
           error_log("Error en la transacción: " . $e->getMessage());
           $session->msg("d", "Error al actualizar el kit: " . $e->getMessage());
           redirect('edit_kit.php?id='.$kit_id, false);
       }
   } else {
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
          <form method="post" action="edit_kit.php?id=<?php echo (int)$kit_id; ?>" class="clearfix" id="editKitForm">
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="kit-name">Nombre del Kit</label>
                  <input type="text" class="form-control" name="kit-name" id="kit-name" value="<?php echo remove_junk($kit['Nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="kit-price">Precio Base</label>
                  <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" name="kit-price" id="kit-price" step="0.01" value="<?php echo remove_junk($kit['Precio']); ?>" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <label for="kit-description">Descripción</label>
                  <textarea class="form-control" name="kit-description" id="kit-description" rows="3"><?php echo remove_junk($kit['Descripcion']); ?></textarea>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <h4>Productos del Kit</h4>
                  <div id="productos-container">
                    <?php if(!empty($kit_productos)): ?>
                      <?php foreach($kit_productos as $index => $item): ?>
                        <div class="row producto-item">
                          <div class="col-md-8">
                            <label for="producto-<?php echo $index; ?>">Producto</label>
                            <select class="form-control" name="producto_id[]" id="producto-<?php echo $index; ?>">
                              <option value="">Seleccione un producto</option>
                              <?php foreach($all_products as $product): ?>
                                <option value="<?php echo (int)$product['ID']; ?>" <?php if($product['ID'] == $item['Id_Producto']) echo 'selected'; ?>>
                                  <?php echo remove_junk($product['Nombre']); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <label for="cantidad-<?php echo $index; ?>">Cantidad</label>
                            <input type="number" class="form-control" name="cantidad[]" id="cantidad-<?php echo $index; ?>" value="<?php echo (int)$item['Cantidad']; ?>" min="1">
                          </div>
                          <div class="col-md-2">
                            <label for="remove-<?php echo $index; ?>" class="sr-only">Eliminar producto</label>
                            <button type="button" class="btn btn-danger btn-remove-product" id="remove-<?php echo $index; ?>">
                              <span class="glyphicon glyphicon-trash"></span>
                            </button>
                          </div>
                        </div>
                      <?php endforeach; ?>
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
                  <button type="submit" name="edit_kit" class="btn btn-primary">Guardar Cambios</button>
                  <a href="kits.php" class="btn btn-default">Cancelar</a>
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
    var productCounter = <?php echo !empty($kit_productos) ? count($kit_productos) : 0; ?>;
    
    // Función para validar el formulario
    function validateForm() {
        var isValid = true;
        var errorMessage = '';
        
        // Validar nombre y precio
        if($('#kit-name').val().trim() === '') {
            errorMessage += 'El nombre del kit es obligatorio.\n';
            isValid = false;
        }
        
        var price = parseFloat($('#kit-price').val());
        if(isNaN(price) || price <= 0) {
            errorMessage += 'El precio debe ser un número positivo.\n';
            isValid = false;
        }
        
        // Validar productos
        var hasProducts = false;
        var productIds = new Set();
        
        $('.producto-item').each(function() {
            var productoId = $(this).find('select[name="producto_id[]"]').val();
            var cantidad = parseInt($(this).find('input[name="cantidad[]"]').val());
            
            if(productoId) {
                hasProducts = true;
                
                if(productIds.has(productoId)) {
                    errorMessage += 'No puede agregar el mismo producto más de una vez.\n';
                    isValid = false;
                }
                productIds.add(productoId);
                
                if(isNaN(cantidad) || cantidad <= 0) {
                    errorMessage += 'La cantidad debe ser un número positivo.\n';
                    isValid = false;
                }
            }
        });
        
        if(!hasProducts) {
            errorMessage += 'Debe seleccionar al menos un producto.\n';
            isValid = false;
        }
        
        if(!isValid) {
            alert(errorMessage);
        }
        
        return isValid;
    }
    
    // Validar formulario antes de enviar
    $('#editKitForm').on('submit', function(e) {
        if(!validateForm()) {
            e.preventDefault();
        }
    });
    
    // Agregar nuevo producto
    $('#add-product').click(function() {
        var newRow = `
            <div class="row producto-item">
                <div class="col-md-8">
                    <label for="producto-${productCounter}">Producto</label>
                    <select class="form-control" name="producto_id[]" id="producto-${productCounter}">
                        <option value="">Seleccione un producto</option>
                        <?php foreach($all_products as $product): ?>
                            <option value="<?php echo (int)$product['ID']; ?>">
                                <?php echo remove_junk($product['Nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="cantidad-${productCounter}">Cantidad</label>
                    <input type="number" class="form-control" name="cantidad[]" id="cantidad-${productCounter}" value="1" min="1">
                </div>
                <div class="col-md-2">
                    <label for="remove-${productCounter}" class="sr-only">Eliminar producto</label>
                    <button type="button" class="btn btn-danger btn-remove-product" id="remove-${productCounter}">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </div>
            </div>
        `;
        
        $('#productos-container').append(newRow);
        productCounter++;
    });
    
    // Eliminar producto
    $(document).on('click', '.btn-remove-product', function() {
        $(this).closest('.producto-item').remove();
    });
});
</script>

<?php include_once('layouts/footer.php'); ?> 