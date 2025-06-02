<?php
  $page_title = 'Lista de proveedores';
  require_once('includes/load.php');
  page_require_level(2);
  
  $all_providers = find_all('proveedor');
?>
<?php
 if(isset($_POST['add_provider'])){
   $req_fields = array('provider-name', 'provider-number', 'provider-rfc');
   validate_fields($req_fields);
   
   if(empty($errors)){
      $name = remove_junk($db->escape($_POST['provider-name']));
      $number = remove_junk($db->escape($_POST['provider-number']));
      $rfc = remove_junk($db->escape($_POST['provider-rfc']));
      
      $sql = "INSERT INTO proveedor (Nombre, Telefono, RFC)";
      $sql .= " VALUES ('{$name}', '{$number}', '{$rfc}')";
      
      if($db->query($sql)){
        $session->msg("s", "Proveedor agregado exitosamente.");
        redirect('providers.php',false);
      } else {
        $session->msg("d", "Lo siento, registro falló");
        redirect('providers.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('providers.php',false);
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
  <div class="col-md-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Agregar proveedor</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="providers.php">
          <div class="form-group">
            <label>Nombre del proveedor</label>
            <input type="text" class="form-control" name="provider-name" placeholder="Nombre del proveedor" required>
          </div>
          <div class="form-group">
            <label>Número de teléfono</label>
            <input type="text" class="form-control" name="provider-number" placeholder="Número de teléfono" required>
          </div>
          <div class="form-group">
            <label>RFC</label>
            <input type="text" class="form-control" name="provider-rfc" placeholder="RFC" required>
          </div>
          <button type="submit" name="add_provider" class="btn btn-primary">Agregar proveedor</button>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Lista de proveedores</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>RFC</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($all_providers && is_array($all_providers)): ?>
              <?php foreach ($all_providers as $provider):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk(ucfirst($provider['Nombre'])); ?></td>
                <td><?php echo remove_junk($provider['Telefono']); ?></td>
                <td><?php echo remove_junk($provider['RFC']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_provider.php?id=<?php echo (int)$provider['ID'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar" onclick="return confirm('¿Está seguro de editar este proveedor?');">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_provider.php?id=<?php echo (int)$provider['ID'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este proveedor? Esta acción no se puede deshacer.');">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center">No se encontraron proveedores.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
.btn-group .btn {
  margin: 0 2px;
}

.btn-xs {
  padding: 4px 8px;
  font-size: 12px;
  line-height: 1.5;
  border-radius: 3px;
}

.btn-warning {
  color: #fff;
  background-color: #f0ad4e;
  border-color: #eea236;
}

.btn-danger {
  color: #fff;
  background-color: #d9534f;
  border-color: #d43f3a;
}

.btn:hover {
  opacity: 0.8;
}

.table > tbody > tr > td {
  vertical-align: middle;
}

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

<script>
$(document).ready(function() {
  // Inicializar tooltips
  $('[data-toggle="tooltip"]').tooltip();
  
  // Confirmación para editar
  $('.btn-warning').click(function(e) {
    if(!confirm('¿Está seguro de editar este proveedor?')) {
      e.preventDefault();
    }
  });
  
  // Confirmación para eliminar
  $('.btn-danger').click(function(e) {
    if(!confirm('¿Está seguro de eliminar este proveedor? Esta acción no se puede deshacer.')) {
      e.preventDefault();
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?> 