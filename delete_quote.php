<?php
  $page_title = 'Eliminar Cotización';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);
?>
<?php
  $quote = find_by_id('cotizacion',(int)$_GET['id']);
  if(!$quote){
    $session->msg("d","ID de cotización no encontrado.");
    redirect('quotes.php');
  }
?>
<?php
  $delete_id = delete_by_id('cotizacion',(int)$quote['ID']);
  if($delete_id){
      $session->msg("s","Cotización eliminada.");
      redirect('quotes.php');
  } else {
      $session->msg("d","Error al eliminar la cotización.");
      redirect('quotes.php');
  }
?> 