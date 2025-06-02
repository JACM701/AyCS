<?php
  $page_title = 'Editar proveedor';
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
 if(isset($_POST['edit_provider'])){
   $req_fields = array('provider-name', 'provider-number', 'provider-rfc');
   validate_fields($req_fields);
   
   if(empty($errors)){
     $name = remove_junk($db->escape($_POST['provider-name']));
     $number = remove_junk($db->escape($_POST['provider-number']));
     $rfc = remove_junk($db->escape($_POST['provider-rfc']));
     
     $sql = "UPDATE proveedor SET";
     $sql .= " Nombre = '{$name}',";
     $sql .= " Telefono = '{$number}',";
     $sql .= " RFC = '{$rfc}'";
     $sql .= " WHERE ID = '{$provider['ID']}'";
     
     if($db->query($sql)){
       $session->msg("s", "Proveedor actualizado exitosamente.");
       redirect('providers.php', false);
     } else {
       $session->msg("d", "Lo siento, actualización falló.");
       redirect('edit_provider.php?id='.$provider['ID'], false);
     }
   } else {
     $session->msg("d", $errors);
     redirect('edit_provider.php?id='.$provider['ID'], false);
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
          <span class="glyphicon glyphicon-edit"></span>
          <span>Editar proveedor</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="edit_provider.php?id=<?php echo (int)$provider['ID'] ?>" class="clearfix">
            <div class="form-group">
              <label>Nombre del proveedor</label>
              <input type="text" class="form-control" name="provider-name" value="<?php echo remove_junk(ucfirst($provider['Nombre'])); ?>" required>
            </div>
            <div class="form-group">
              <label>Número de teléfono</label>
              <input type="text" class="form-control" name="provider-number" value="<?php echo remove_junk($provider['Telefono']); ?>" required>
            </div>
            <div class="form-group">
              <label>RFC</label>
              <input type="text" class="form-control" name="provider-rfc" value="<?php echo remove_junk($provider['RFC']); ?>" required>
            </div>
            <button type="submit" name="edit_provider" class="btn btn-primary">Actualizar proveedor</button>
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

.panel-title {
  font-size: 16px;
  font-weight: 600;
  color: #283593;
}

.form-control:focus {
  border-color: #283593;
  box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
}

.btn-primary {
  background: linear-gradient(135deg, #283593 0%, #1a237e 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #1a237e 0%, #0d1642 100%);
  transform: translateY(-1px);
}
</style>

<?php include_once('layouts/footer.php'); ?> 