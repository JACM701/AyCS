<?php
  $page_title = 'Actualizar Cotización';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);
?>
<?php
  if(isset($_POST['update_quote'])){
    $quote_id = (int)$_POST['quote_id'];
    $client_id = $_POST['client_id'] ? (int)$_POST['client_id'] : null;
    // Si no se selecciona un cliente existente, usar el nombre del cliente del formulario
    $client_name = null;
    if(!$client_id && isset($_POST['client_name'])) {
        $client_name = remove_junk($db->escape($_POST['client_name']));
    }

    $quote_type = remove_junk($db->escape($_POST['quote_type']));
    $discount_percentage = (float)$_POST['discount_percentage'];
    $total_amount = (float)$_POST['total_amount'];
    $observations = remove_junk($db->escape($_POST['observations']));

    // Actualizar la cotización (incluyendo client_name si aplica)
    $sql = "UPDATE quotes SET 
            client_id = ". ($client_id ? $client_id : "NULL") .",
            client_name = ". ($client_name ? "'{$client_name}'" : "NULL") .",
            quote_type = '{$quote_type}',
            discount_percentage = {$discount_percentage},
            total_amount = {$total_amount},
            observations = '{$observations}'
            WHERE id = {$quote_id}";
    
    if($db->query($sql)){
      // Eliminar items existentes
      $sql = "DELETE FROM quote_items WHERE quote_id = {$quote_id}";
      $db->query($sql);

      // Insertar nuevos items
      if(isset($_POST['product_id']) && is_array($_POST['product_id'])) {
          $product_ids = $_POST['product_id'];
          $quantities = $_POST['quantity'];
          $prices = $_POST['price'];

          for($i = 0; $i < count($product_ids); $i++) {
            // Asegurarse de que los índices existen y los valores no están vacíos para evitar warnings
            if(isset($product_ids[$i], $quantities[$i], $prices[$i]) && $product_ids[$i] !== '' && $quantities[$i] !== '' && $prices[$i] !== '') {
              $product_id = (int)$product_ids[$i];
              $quantity = (int)$quantities[$i];
              $price = (float)$prices[$i];

              // Insertar solo product_id por ahora. service_id y otros campos pueden agregarse si es necesario.
              $sql = "INSERT INTO quote_items (quote_id, product_id, quantity, price) 
                      VALUES ({$quote_id}, {$product_id}, {$quantity}, {$price})";
              $db->query($sql);
            }
          }
      }

      $session->msg("s", "Cotización actualizada exitosamente.");
      redirect('quotes.php', false);
    } else {
      $session->msg("d", "Error al actualizar la cotización: " . $db->con->error);
      redirect('edit_quote.php?id='.$quote_id, false);
    }
  }
?> 