<?php
$page_title = 'Agregar Cotización';
require_once('includes/load.php');

// Verificar nivel de usuario
page_require_level(1);

// Obtener lista de clientes
$sql = "SELECT ID, Nombre FROM cliente ORDER BY Nombre";
$clientes = find_by_sql($sql);

// Obtener lista de productos (corregido el nombre de la tabla y columnas)
$sql = "SELECT p.ID, p.Nombre, p.Precio FROM producto p ORDER BY p.Nombre";
$products = find_by_sql($sql);

// Obtener lista de servicios (tabla servicio, columnas ID, Nombre, Costo)
$sql_servicios = "SELECT s.ID, s.Nombre, s.Costo FROM servicio s ORDER BY s.Nombre";
$services = find_by_sql($sql_servicios);
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
                <select class="form-control" name="client_id" id="client_id" required>
                  <option value="">Seleccione un cliente</option>
                  <?php foreach($clientes as $cliente): ?>
                    <option value="<?php echo (int)$cliente['ID']; ?>">
                      <?php echo remove_junk($cliente['Nombre']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="quote_date">Fecha de Cotización</label>
                <input type="date" class="form-control" name="quote_date" id="quote_date" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <strong>Items de Cotización</strong>
                </div>
                <div class="panel-body">
                  <div id="items_list">
                    <!-- Los items se agregarán aquí dinámicamente -->
                  </div>
                  
                  <button type="button" class="btn btn-success pull-right" id="add_item_btn">Agregar Item</button>
                </div>
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
  // Cargar productos y servicios para usar en los items dinámicos
  const products = <?php echo json_encode($products); ?>;
  const services = <?php echo json_encode($services); ?>;

  // Función para agregar un nuevo item al formulario
  $('#add_item_btn').click(function() {
    var itemHtml = `
      <div class="row item-row" style="margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 5px;">
        <div class="col-md-4">
          <div class="form-group">
            <label>Tipo</label>
            <select class="form-control item-type" name="item_type[]">
              <option value="product">Producto</option>
              <option value="service">Servicio</option>
            </select>
          </div>
        </div>
        <div class="col-md-4 item-select-container">
           <div class="form-group">
             <label>Seleccionar Producto</label>
             <select class="form-control item-select" name="product_id[]" required>
               <option value="">Seleccione producto</option>
               ${products.map(p => `<option value="${p.ID}" data-price="${p.Precio}">${p.Nombre} - $${parseFloat(p.Precio).toFixed(2)}</option>`).join('')}
             </select>
           </div>
        </div>
         <div class="col-md-2">
           <div class="form-group">
             <label>Cantidad</label>
             <input type="number" class="form-control item-quantity" name="quantity[]" min="1" value="1" required>
           </div>
         </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Precio Unitario</label>
            <input type="number" class="form-control item-unit-price" name="unit_price[]" step="0.01" min="0" required>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Descuento (%)</label>
            <input type="number" class="form-control item-discount" name="discount[]" step="0.01" min="0" max="100" value="0">
          </div>
        </div>
         <div class="col-md-2">
           <div class="form-group">
             <label>Precio Total Item</label>
             <input type="text" class="form-control item-total-price" readonly>
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
  });

  // Cambiar selector de producto a servicio y viceversa
  $(document).on('change', '.item-type', function() {
    var itemType = $(this).val();
    var itemRow = $(this).closest('.item-row');
    var selectContainer = itemRow.find('.item-select-container');
    selectContainer.empty(); // Limpiar el contenido actual

    if (itemType === 'product') {
      var productSelectHtml = `
         <div class="form-group">
           <label>Seleccionar Producto</label>
           <select class="form-control item-select" name="product_id[]" required>
             <option value="">Seleccione producto</option>
             ${products.map(p => `<option value="${p.ID}" data-price="${p.Precio}">${p.Nombre} - $${parseFloat(p.Precio).toFixed(2)}</option>`).join('')}
           </select>
         </div>
      `;
      selectContainer.append(productSelectHtml);
       // Restaurar campos relacionados si existen
       itemRow.find('input[name="quantity[]"]').val(1).prop('required', true).show();
       itemRow.find('input[name="unit_price[]"]').val('').prop('required', true).show();

    } else if (itemType === 'service') {
      var serviceSelectHtml = `
         <div class="form-group">
           <label>Seleccionar Servicio</label>
           <select class="form-control item-select" name="service_id[]" required>
             <option value="">Seleccione servicio</option>
             ${services.map(s => `<option value="${s.ID}" data-price="${s.Costo}">${s.Nombre} - $${parseFloat(s.Costo).toFixed(2)}</option>`).join('')}
           </select>
         </div>
      `;
      selectContainer.append(serviceSelectHtml);
      // Para servicios, la cantidad suele ser 1 y el precio unitario es el costo del servicio
      // Ocultar cantidad y usar el costo del servicio como precio unitario
      itemRow.find('input[name="quantity[]"]').val(1).prop('required', false).hide();
      itemRow.find('input[name="unit_price[]"]').val('').prop('required', false).hide(); // El precio se obtiene del select
    }
     // Restablecer precio unitario y total del item al cambiar tipo
     itemRow.find('.item-unit-price').val('');
     itemRow.find('.item-total-price').val('0.00');
     calculateQuoteTotal(); // Recalcular total de la cotización
  });

  // Actualizar Precio Unitario y Precio Total Item cuando se selecciona un producto o servicio dinámicamente
  $(document).on('change', '.item-select', function() {
    var selectedPrice = $(this).find(':selected').data('price');
    var itemRow = $(this).closest('.item-row');
    var itemType = itemRow.find('.item-type').val();

    if(itemType === 'product') {
      itemRow.find('.item-unit-price').val(selectedPrice);
    } else if (itemType === 'service') {
        // Para servicios, el precio unitario en la BD es el costo total del servicio
        // Podemos mostrarlo como precio unitario o calcularlo directamente
        // Lo pondremos en Precio Total Item y Precio Unitario si queremos mostrarlo desglosado
        itemRow.find('.item-unit-price').val(selectedPrice); // Mostrar costo del servicio como precio unitario
        itemRow.find('.item-quantity').val(1); // Cantidad siempre 1 para servicios
    }
     calculateItemTotal(itemRow); // Calcular total del item
     calculateQuoteTotal(); // Recalcular total de la cotización
  });

  // Calcular Precio Total Item cuando cambia cantidad, precio unitario o descuento
  $(document).on('input', '.item-quantity, .item-unit-price, .item-discount', function() {
     var itemRow = $(this).closest('.item-row');
     calculateItemTotal(itemRow);
     calculateQuoteTotal(); // Recalcular total de la cotización
  });

  // Función para calcular el total de un item
  function calculateItemTotal(itemRow) {
    var quantity = parseFloat(itemRow.find('.item-quantity').val()) || 0;
    var unitPrice = parseFloat(itemRow.find('.item-unit-price').val()) || 0;
    var discountPercentage = parseFloat(itemRow.find('.item-discount').val()) || 0;
    var total = quantity * unitPrice;
    var discountAmount = total * (discountPercentage / 100);
    var itemTotal = total - discountAmount;
    itemRow.find('.item-total-price').val(itemTotal.toFixed(2));
  }

  // Función para calcular el total general de la cotización sumando los totales de los items
  function calculateQuoteTotal() {
    var quoteTotal = 0;
    $('.item-total-price').each(function() {
      quoteTotal += parseFloat($(this).val()) || 0;
    });
     // Si hay un campo de descuento total en el formulario, lo aplicaríamos aquí.
     // Pero como no existe en naycs.sql -> cotizacion, no lo hacemos.
     // Si quisiéramos mostrar un total general con descuento en la interfaz, sería solo para visualización.
  }

  // Eliminar item
  $(document).on('click', '.remove-item', function() {
    $(this).closest('.item-row').remove();
    calculateQuoteTotal(); // Recalcular total de la cotización al eliminar item
  });
  
  // Lógica de guardado de la cotización al enviar el formulario
  $('form').submit(function(event) {
      // No prevenir el envío por defecto para que el PHP pueda procesar
      // Validaciones adicionales si es necesario
  });

  // Procesamiento del formulario en PHP (la lógica de inserción se manejará aquí)
  <?php
  if (isset($_POST['add_quote'])) {
      $session->msg('i', 'Procesando cotización...'); // Mensaje de depuración

      $client_id = remove_junk($db->escape($_POST['client_id']));
      $quote_date = remove_junk($db->escape($_POST['quote_date']));
      $observations = remove_junk($db->escape($_POST['observations'] ?? ''));

      if (empty($client_id) || empty($quote_date)) {
          $session->msg('d', 'Faltan campos obligatorios (Cliente, Fecha).');
          redirect('add_quote.php', false);
          exit();
      }

      $query_cotizacion = "INSERT INTO cotizacion (Id_Cliente, Fecha) VALUES ('{$client_id}', '{$quote_date}')";
      
      if ($db->query($query_cotizacion)) {
          $cotizacion_id = $db->insert_id();
          $session->msg('s', 'Cotización guardada exitosamente.');

          // Procesar e insertar items
          if (isset($_POST['item_type']) && is_array($_POST['item_type'])) {
              foreach ($_POST['item_type'] as $key => $item_type) {
                  $product_id = ($item_type === 'product' && isset($_POST['product_id'][$key])) ? $db->escape($_POST['product_id'][$key]) : null;
                  $service_id = ($item_type === 'service' && isset($_POST['service_id'][$key])) ? $db->escape($_POST['service_id'][$key]) : null;
                  $quantity = isset($_POST['quantity'][$key]) ? (float)$_POST['quantity'][$key] : 0;
                  $unit_price = isset($_POST['unit_price'][$key]) ? (float)$_POST['unit_price'][$key] : 0;
                  $discount = isset($_POST['discount'][$key]) ? (float)$_POST['discount'][$key] : 0;

                  // Calcular precio total del item (cantidad * precio unitario - descuento)
                  $total_item_price = ($quantity * $unit_price) * (1 - ($discount / 100));
                  
                  // Insertar en detalle_cotizacion
                  $query_detalle = "INSERT INTO detalle_cotizacion (Id_Cotizacion, Id_Producto, Id_Servicio, Precio, Descuento) ";
                  $query_detalle .= "VALUES ('{$cotizacion_id}', ";
                  $query_detalle .= "'" . ($product_id !== null ? $product_id : 'NULL') . "', "; // Usar NULL si no es producto
                  $query_detalle .= "'" . ($service_id !== null ? $service_id : 'NULL') . "', "; // Usar NULL si no es servicio
                  $query_detalle .= "'{$db->escape($total_item_price)}', '{$db->escape($discount)}')";

                  if (!$db->query($query_detalle)) {
                      $session->msg('d', 'Error al guardar detalle de item (' . ($item_type === 'product' ? 'Producto' : 'Servicio') . ') para cotización ID: ' . $cotizacion_id);
                  }
              }
          }

          // Si hay observaciones y queremos guardarlas (requiere añadir columna Observaciones a tabla cotizacion)
          // Si se añade la columna: UPDATE cotizacion SET Observaciones = '{$db->escape($observations)}' WHERE ID = '{$cotizacion_id}';

          redirect('quotes.php', false);
          exit();

      } else {
          $session->msg('d', 'Error al guardar la cotización principal.');
          redirect('add_quote.php', false);
          exit();
      }
  }
  ?>

});
</script>

<?php include_once('layouts/footer.php'); ?>


                  
    