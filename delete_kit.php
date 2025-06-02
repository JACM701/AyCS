<?php
  $page_title = 'Eliminar Kit';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>

<?php
  $kit = find_by_id('kit',(int)$_GET['id']);
  if(!$kit){
    $session->msg("d","ID de kit no encontrado.");
    redirect('kits.php');
  }
?>

<?php
  if(isset($_POST['delete_kit'])){
    $kit_id = (int)$_GET['id'];
    
    // Primero eliminar los productos asociados
    $query = "DELETE FROM kit_producto WHERE Id_Kit = '{$kit_id}'";
    $db->query($query);
    
    // Luego eliminar el kit
    $query = "DELETE FROM kit WHERE ID = '{$kit_id}'";
    $db->query($query);
    
    if($db->query($query)){
      $session->msg("s","Kit eliminado exitosamente.");
      redirect('kits.php');
    } else {
      $session->msg("d","Error al eliminar el kit.");
      redirect('kits.php');
    }
  }
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
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Eliminar Kit</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="delete_kit.php?id=<?php echo (int)$kit['ID']; ?>">
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <h4>¿Está seguro que desea eliminar el kit "<?php echo remove_junk($kit['Nombre']); ?>"?</h4>
                  <p>Esta acción no se puede deshacer.</p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <button type="submit" name="delete_kit" class="btn btn-danger">Eliminar Kit</button>
                  <a href="kits.php" class="btn btn-default">Cancelar</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?> 