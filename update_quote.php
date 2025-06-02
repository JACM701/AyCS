<?php
  $page_title = 'Actualizar Cotización';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);
?>
<?php
  if(isset($_POST['update_quote'])){
    $quote_id = (int)$_POST['quote_id'];
    $client_id = (int)$_POST['client_id'];
    $quote_date = remove_junk($db->escape($_POST['quote_date']));
    $observations = remove_junk($db->escape($_POST['observations'] ?? ''));

    // Actualizar la cotización
    $sql = "UPDATE cotizacion SET 
            Id_Cliente = {$client_id},
            Fecha = '{$quote_date}',
            Observaciones = '{$observations}'
            WHERE ID = {$quote_id}";
    
    if($db->query($sql)){
      // Eliminar items existentes
      $sql = "DELETE FROM detalle_cotizacion WHERE Id_Cotizacion = {$quote_id}";
      $db->query($sql);

      // Insertar nuevos items
      if(isset($_POST['item_type']) && is_array($_POST['item_type'])) {
          $item_types = $_POST['item_type'];
          $product_ids = $_POST['product_id'] ?? [];
          $service_ids = $_POST['service_id'] ?? [];
          $quantities = $_POST['quantity'];
          $unit_prices = $_POST['unit_price'];

          for($i = 0; $i < count($item_types); $i++) {
            if(isset($item_types[$i], $quantities[$i], $unit_prices[$i])) {
              $item_type = $item_types[$i];
              $quantity = (int)$quantities[$i];
              $unit_price = (float)$unit_prices[$i];
              $total_price = $quantity * $unit_price;

              // Determinar si es producto o servicio
              $product_id = ($item_type === 'product' && isset($product_ids[$i])) ? (int)$product_ids[$i] : null;
              $service_id = ($item_type === 'service' && isset($service_ids[$i])) ? (int)$service_ids[$i] : null;

              // Insertar en detalle_cotizacion
              $sql = "INSERT INTO detalle_cotizacion (Id_Cotizacion, Id_Producto, Id_Servicio, Precio) 
                      VALUES ({$quote_id}, " . 
                      ($product_id ? $product_id : "NULL") . ", " . 
                      ($service_id ? $service_id : "NULL") . ", 
                      {$total_price})";
              
              if(!$db->query($sql)) {
                $session->msg("d", "Error al guardar detalle de item.");
                redirect('edit_quote.php?id='.$quote_id, false);
                exit();
              }
            }
          }
      }

      $session->msg("s", "Cotización actualizada exitosamente.");
      redirect('quotes.php', false);
    } else {
      $session->msg("d", "Error al actualizar la cotización.");
      redirect('edit_quote.php?id='.$quote_id, false);
    }
  }
?> 