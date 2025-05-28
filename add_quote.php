<?php
$page_title = 'Agregar Cotización';
require_once('includes/load.php');

// Verificar nivel de usuario
page_require_level(1);

// Obtener lista de clientes
$sql = "SELECT Id_Cliente, Nombre, Apellido FROM clientes ORDER BY Nombre, Apellido";
$clientes = find_by_sql($sql);

// Obtener lista de productos (corregido el nombre de la tabla y columnas)
$sql = "SELECT p.ID, p.Nombre, p.Precio, c.name as category_name 
        FROM producto p 
        LEFT JOIN categories c ON p.Id_Categoria = c.id 
        ORDER BY p.Nombre";
$products = find_by_sql($sql);
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
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Nueva Cotización</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_quote.php" class="clearfix">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="client_id">Cliente</label>
                <select class="form-control" name="client_id" id="client_id">
                  <option value="">Seleccione un cliente</option>
                  <?php foreach($clientes as $cliente): ?>
                    <option value="<?php echo (int)$cliente['Id_Cliente']; ?>">
                      <?php echo remove_junk($cliente['Nombre'] . ' ' . $cliente['Apellido']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="quote_type">Tipo de Cotización</label>
                <select class="form-control" name="quote_type" id="quote_type" required>
                  <option value="">Seleccione tipo</option>
                  <option value="Producto">Producto</option>
                  <option value="Servicio">Servicio</option>
                  <option value="Mixto">Mixto</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <strong>Productos</strong>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product_id">Producto</label>
                        <select class="form-control" name="product_id[]" id="product_id">
                          <option value="">Seleccione producto</option>
                          <?php foreach($products as $product): ?>
                            <option value="<?php echo (int)$product['ID']; ?>" 
                                    data-price="<?php echo $product['Precio']; ?>">
                              <?php echo remove_junk($product['Nombre']); ?> - $<?php echo number_format($product['Precio'], 2); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="quantity">Cantidad</label>
                        <input type="number" class="form-control" name="quantity[]" min="1" value="1">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="price">Precio</label>
                        <input type="number" class="form-control" name="price[]" step="0.01" min="0">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="subtotal">Subtotal</label>
                        <input type="text" class="form-control" name="subtotal[]" readonly>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-success btn-block" id="add_item">Agregar</button>
                      </div>
                    </div>
                  </div>

                  <div id="items_list">
                    <!-- Los items se agregarán aquí dinámicamente -->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="discount_percentage">Descuento (%)</label>
                <input type="number" class="form-control" name="discount_percentage" min="0" max="100" value="0">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="total_amount">Total</label>
                <input type="text" class="form-control" name="total_amount" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="observations">Observaciones</label>
                <textarea class="form-control" name="observations" rows="3"></textarea>
              </div>
            </div>
          </div>

          <div class="form-group clearfix">
            <button type="submit" name="add_quote" class="btn btn-primary">Guardar Cotización</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Calcular subtotal cuando cambia cantidad o precio
  function calculateSubtotal() {
    // Recalcular subtotales para todos los items
    $('#items_list .item-row').each(function(){
      var quantity = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
      var price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
      var subtotal = quantity * price;
      $(this).find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
    });
    calculateTotal();
  }

  // Calcular total general
  function calculateTotal() {
    var total = 0;
    $('input[name="subtotal[]"]').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    var discount = parseFloat($('input[name="discount_percentage"]').val()) || 0;
    var discountAmount = total * (discount / 100);
    var finalTotal = total - discountAmount;
    $('input[name="total_amount"]').val(finalTotal.toFixed(2));
  }

  // Actualizar precio cuando se selecciona un producto (para el primer item)
  $('#product_id').change(function() {
    var price = $(this).find(':selected').data('price');
    // Encuentra el input de precio en la misma fila o sección que el selector
    $(this).closest('.row').find('input[name="price[]"]').val(price);
    calculateSubtotal();
  });

  // Calcular cuando cambian los valores (para el primer item)
  $('input[name="quantity[]"]:first, input[name="price[]"]:first, input[name="discount_percentage"]').on('input', function() {
    calculateSubtotal();
  });

  // Agregar nuevo item
  $('#add_item').click(function() {
    var itemHtml = `
      <div class="row item-row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Producto</label>
            <select class="form-control" name="product_id[]">
              <option value="">Seleccione producto</option>
              <?php foreach($products as $product): ?>
                <option value="<?php echo (int)$product['ID']; ?>" 
                        data-price="<?php echo $product['Precio']; ?>">
                  <?php echo remove_junk($product['Nombre']); ?> - $<?php echo number_format($product['Precio'], 2); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Cantidad</label>
            <input type="number" class="form-control" name="quantity[]" min="1" value="1">
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Precio</label>
            <input type="number" class="form-control" name="price[]" step="0.01" min="0">
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Subtotal</label>
            <input type="text" class="form-control" name="subtotal[]" readonly>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-danger btn-block remove-item">Eliminar</button>
          </div>
        </div>
      </div>
    `;
    $('#items_list').append(itemHtml);
    
    // Re-enlazar eventos para los nuevos inputs de cantidad y precio
    $('#items_list .item-row:last input[name="quantity[]"], #items_list .item-row:last input[name="price[]"]').on('input', calculateSubtotal);
    // Re-enlazar evento para actualizar precio al seleccionar producto en el nuevo item
    $('#items_list .item-row:last select[name="product_id[]"]').change(function(){
       var price = $(this).find(':selected').data('price');
       $(this).closest('.row').find('input[name="price[]"]').val(price);
       calculateSubtotal();
    });

  });

  // Eliminar item
  $(document).on('click', '.remove-item', function() {
    $(this).closest('.item-row').remove();
    calculateTotal();
  });
  
  // Inicializar cálculo de totales al cargar la página (si hay items precargados en el futuro)
  calculateSubtotal();
});
</script>

<?php include_once('layouts/footer.php'); ?>


                  
    