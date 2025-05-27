<?php
  $page_title = 'Eliminar Kit';
  require_once('includes/load.php');
  page_require_level(2);
?>

<?php
  $kit_id = (int)$_GET['id'];
  if(empty($kit_id)):
    redirect('kits.php');
  endif;
  
  $kit = find_by_id('kits', $kit_id);
  if(!$kit):
    redirect('kits.php');
  endif;
?>

<?php
  if(isset($_POST['delete_kit'])){
    $query = "DELETE FROM kit_items WHERE kit_id = '{$kit_id}'";
    if($db->query($query)){
      $query = "DELETE FROM kits WHERE id = '{$kit_id}'";
      if($db->query($query)){
        $session->msg("s", "Kit eliminado exitosamente.");
        redirect('kits.php');
      } else {
        $session->msg("d", "Error al eliminar el kit.");
        redirect('kits.php');
      }
    } else {
      $session->msg("d", "Error al eliminar los productos del kit.");
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
          <form method="post" action="delete_kit.php?id=<?php echo $kit_id; ?>" class="clearfix">
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <p>¿Está seguro que desea eliminar el kit "<?php echo remove_junk($kit['nombre']); ?>"?</p>
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