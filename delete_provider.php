<?php
  $page_title = 'Eliminar proveedor';
  require_once('includes/load.php');
  page_require_level(2);
?>
<?php
  $provider = find_by_id('proveedor',(int)$_GET['id']);
  if(!$provider){
    $session->msg("d","ID de proveedor no encontrado.");
    redirect('providers.php');
  }
?>
<?php
  if(isset($_POST['delete_provider'])){
    // Verificar si hay productos asociados al proveedor
    $sql = "SELECT COUNT(*) as total FROM producto WHERE Id_Proveedor = '{$provider['ID']}'";
    $result = $db->query($sql);
    $row = $db->fetch_assoc($result);
    
    if($row['total'] > 0){
      $session->msg("d", "No se puede eliminar el proveedor porque tiene productos asociados. Por favor, elimine o reasigne los productos primero.");
      redirect('providers.php', false);
    } else {
      $delete_id = delete_by_id('proveedor',(int)$provider['ID']);
      if($delete_id){
        $session->msg("s","Proveedor eliminado.");
        redirect('providers.php');
      } else {
        $session->msg("d","Eliminación falló.");
        redirect('providers.php');
      }
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
          <span class="glyphicon glyphicon-trash"></span>
          <span>Eliminar proveedor</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="delete_provider.php?id=<?php echo (int)$provider['ID']; ?>">
            <div class="form-group">
              <label>Nombre del proveedor</label>
              <input type="text" class="form-control" value="<?php echo remove_junk(ucfirst($provider['Nombre'])); ?>" readonly>
            </div>
            <div class="form-group">
              <label>Número de teléfono</label>
              <input type="text" class="form-control" value="<?php echo remove_junk($provider['Telefono']); ?>" readonly>
            </div>
            <div class="form-group">
              <label>RFC</label>
              <input type="text" class="form-control" value="<?php echo remove_junk($provider['RFC']); ?>" readonly>
            </div>
            <div class="form-group clearfix">
              <button type="submit" name="delete_provider" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este proveedor? Esta acción no se puede deshacer.');">Eliminar proveedor</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.panel {
  border: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
  transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}

.panel:hover {
  box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}

.panel-heading {
  background-color: #f8f9fa !important;
  border-bottom: 1px solid #e9ecef;
}

.form-control[readonly] {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  border: none;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
  transform: translateY(-1px);
}
</style>

<?php include_once('layouts/footer.php'); ?> 