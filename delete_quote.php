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
  // Iniciar transacción
  $db->query('START TRANSACTION');
  
  try {
    // Primero eliminar los registros relacionados en detalle_cotizacion
    $sql = "DELETE FROM detalle_cotizacion WHERE Id_Cotizacion = '{$quote['ID']}'";
    if(!$db->query($sql)) {
      throw new Exception("Error al eliminar los detalles de la cotización");
    }
    
    // Luego eliminar la cotización principal
    $sql = "DELETE FROM cotizacion WHERE ID = '{$quote['ID']}'";
    if(!$db->query($sql)) {
      throw new Exception("Error al eliminar la cotización");
    }
    
    // Si todo salió bien, confirmar la transacción
    $db->query('COMMIT');
    $session->msg("s","Cotización eliminada exitosamente.");
    redirect('quotes.php');
    
  } catch (Exception $e) {
    // Si hubo algún error, revertir la transacción
    $db->query('ROLLBACK');
    $session->msg("d","Error al eliminar la cotización: " . $e->getMessage());
    redirect('quotes.php');
  }
?> 