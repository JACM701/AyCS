<?php
  require_once('includes/load.php');
  page_require_level(2);

  // Lógica para procesar el formulario de agregar proveedor
  if (isset($_POST['add_provider'])) {
    $req_fields = array('provider-name', 'provider-number', 'provider-email', 'provider-rfc');
    validate_fields($req_fields);

    if (empty($errors)) {
      $name   = remove_junk($db->escape($_POST['provider-name']));
      $number = remove_junk($db->escape($_POST['provider-number']));
      $rfc    = remove_junk($db->escape($_POST['provider-rfc']));

      $query = "INSERT INTO proveedor (Nombre, Telefono, RFC) 
                VALUES ('{$name}', '{$number}', '{$rfc}')";

      if ($db->query($query)) {
        $session->msg('s', "Proveedor agregado exitosamente.");
      } else {
        $session->msg('d', "Error al registrar el proveedor.");
      }
    } else {
      // Si hay errores de validación en el modal
      $session->msg("d", $errors);
    }
    
    // Redirigir siempre de vuelta a add_product.php después de procesar
    redirect('add_product.php', false);
    exit(); // Asegurarse de que el script se detenga aquí
  } else {
      // Si alguien intenta acceder directamente a este archivo sin enviar el formulario
      redirect('add_product.php', false);
      exit();
  }
?> 