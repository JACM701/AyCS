<?php
  require_once('includes/load.php');
  page_require_level(2);
?>
<?php
  $customer = find_by_id('clientes',(int)$_GET['id']);
  if(!$customer){
    $session->msg("d","ID de cliente no encontrado.");
    redirect('customers.php');
  }
?>
<?php
  $delete_id = delete_by_id('clientes',(int)$customer['Id_Cliente']);
  if($delete_id){
      $session->msg("s","Cliente eliminado.");
      redirect('customers.php');
  } else {
      $session->msg("d","Eliminación falló.");
      redirect('customers.php');
  }
?> 