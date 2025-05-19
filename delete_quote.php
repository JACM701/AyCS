<?php
  $page_title = 'Eliminar Cotización';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);
?>
<?php
  $quote_id = delete_by_id('quotes',(int)$_GET['id']);
  if($quote_id){
      $session->msg("s","Cotización eliminada.");
      redirect('quotes.php');
  } else {
      $session->msg("d","Error al eliminar la cotización.");
      redirect('quotes.php');
  }
?> 