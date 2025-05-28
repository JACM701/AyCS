<?php
  $page_title = 'Editar Cotización';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);

  // Obtener la cotización
  $quote_id = (int)$_GET['id'];
  $quote = find_by_id('quotes', $quote_id);
  if(!$quote) {
    $session->msg("d", "Cotización no encontrada.");
    redirect('quotes.php');
  }

  // Obtener lista de clientes
  $sql = "SELECT Id_Cliente, Nombre, Apellido FROM clientes ORDER BY Nombre, Apellido";
  $clientes = find_by_sql($sql);

  // Obtener lista de productos (corregido el nombre de la tabla y columnas)
  $sql = "SELECT p.ID, p.Nombre, p.Precio, c.name as category_name 
          FROM producto p 
          LEFT JOIN categories c ON p.Id_Categoria = c.id 
          ORDER BY p.Nombre";
  $products = find_by_sql($sql);

  // Obtener items de la cotización (corregido el nombre de la tabla y columnas)
  $sql = "SELECT qi.*, p.Nombre as product_name, p.Precio as sale_price 
          FROM quote_items qi 
          LEFT JOIN producto p ON qi.product_id = p.ID 
          WHERE qi.quote_id = {$quote_id}";
  $quote_items = find_by_sql($sql);
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
          <span>Editar Cotización</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="update_quote.php" class="clearfix">
          <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="client_id">Cliente</label>
                <select class="form-control" name="client_id" id="client_id">
                  <option value="">Seleccione un cliente</option>
                  <?php foreach($clientes as $cliente): ?>
                    <option value="<?php echo (int)$cliente['Id_Cliente']; ?>" 
                            <?php if($cliente['Id_Cliente'] == $quote['client_id']) echo 'selected'; ?>>
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
                  <option value="Producto" <?php if($quote['quote_type'] == 'Producto') echo 'selected'; ?>>Producto</option>
                  <option value="Servicio" <?php if($quote['quote_type'] == 'Servicio') echo 'selected'; ?>>Servicio</option>
                  <option value="Mixto" <?php if($quote['quote_type'] == 'Mixto') echo 'selected'; ?>>Mixto</option>
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
                  <div id="items_list">
                    <?php foreach($quote_items as $item): ?>
                      <div class="row item-row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Producto</label>
                            <select class="form-control" name="product_id[]">
                              <option value="">Seleccione producto</option>
                              <?php foreach($products as $product): ?>
                                <option value="<?php echo (int)$product['ID']; ?>" 
                                        data-price="<?php echo $product['Precio']; ?>"
                                        <?php if(isset($item['product_id']) && $item['product_id'] === $product['ID']) echo 'selected'; ?>>
                                  <?php echo remove_junk($product['Nombre']); ?> - $<?php echo number_format($product['Precio'], 2); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Cantidad</label>
                            <input type="number" class="form-control" name="quantity[]" min="1" value="<?php echo $item['quantity']; ?>">
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Precio</label>
                            <input type="number" class="form-control" name="price[]" step="0.01" min="0" value="<?php echo $item['price']; ?>">
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Subtotal</label>
                            <input type="text" class="form-control" name="subtotal[]" readonly value="<?php echo $item['quantity'] * $item['price']; ?>">
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block remove-item">Eliminar</button>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product_id">Producto</label>
                        <select class="form-control" name="product_id[]" id="product_id_template">
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
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="discount_percentage">Descuento (%)</label>
                <input type="number" class="form-control" name="discount_percentage" min="0" max="100" value="<?php echo $quote['discount_percentage']; ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="total_amount">Total</label>
                <input type="text" class="form-control" name="total_amount" readonly value="<?php echo $quote['total_amount']; ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="observations">Observaciones</label>
                <textarea class="form-control" name="observations" rows="3"><?php echo $quote['observations']; ?></textarea>
              </div>
            </div>
          </div>

          <div class="form-group clearfix">
            <button type="submit" name="update_quote" class="btn btn-primary">Actualizar Cotización</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Calcular subtotal para una fila de item específica
  function calculateItemSubtotal(itemRow) {
    var quantity = parseFloat(itemRow.find('input[name="quantity[]"]').val()) || 0;
    var price = parseFloat(itemRow.find('input[name="price[]"]').val()) || 0;
    var subtotal = quantity * price;
    itemRow.find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
  }

  // Calcular total general sumando subtotales de todos los items
  function calculateTotal() {
    var total = 0;
    $('#items_list .item-row input[name="subtotal[]"]').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    
    var discount = parseFloat($('input[name="discount_percentage"]').val()) || 0;
    var discountAmount = total * (discount / 100);
    var finalTotal = total - discountAmount;
    $('input[name="total_amount"]').val(finalTotal.toFixed(2));
  }

  // Evento input para recalcular subtotal y total cuando cambian cantidad o precio de un item
  $(document).on('input', '#items_list .item-row input[name="quantity[]"], #items_list .item-row input[name="price[]"]', function() {
    calculateItemSubtotal($(this).closest('.item-row'));
    calculateTotal();
  });

  // Evento change para actualizar precio cuando se selecciona un producto en un item existente
  $(document).on('change', '#items_list .item-row select[name="product_id[]"]', function() {
    var price = $(this).find(':selected').data('price');
    $(this).closest('.item-row').find('input[name="price[]"]').val(price);
    calculateItemSubtotal($(this).closest('.item-row'));
    calculateTotal();
  });

  // Calcular cuando cambia el descuento
  $('input[name="discount_percentage"]').on('input', function() {
    calculateTotal();
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
    
    // Inicializar subtotal para el nuevo item y recalcular total
    calculateItemSubtotal($('#items_list .item-row:last'));
    calculateTotal();
  });

  // Eliminar item
  $(document).on('click', '.remove-item', function() {
    $(this).closest('.item-row').remove();
    calculateTotal(); // Recalcular total después de eliminar
  });
  
  // Inicializar cálculos al cargar la página para los items existentes
  $('#items_list .item-row').each(function(){
      calculateItemSubtotal($(this));
  });
  calculateTotal();
});
</script>

<?php include_once('layouts/footer.php'); ?> 