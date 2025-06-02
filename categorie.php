<?php
  $page_title = 'Lista de categorías';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $all_categories = find_all('categoria_producto');
?>
<?php
 if(isset($_POST['add_cat'])){
   $req_field = array('categorie-name');
   validate_fields($req_field);
   $cat_name = remove_junk($db->escape($_POST['categorie-name']));
   if(empty($errors)){
      $sql  = "INSERT INTO categoria_producto (Nombre)";
      $sql .= " VALUES ('{$cat_name}')";
      if($db->query($sql)){
        $session->msg("s", "Categoría agregada exitosamente.");
        redirect('categorie.php',false);
      } else {
        $session->msg("d", "Lo siento, registro falló");
        redirect('categorie.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('categorie.php',false);
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
            <span>Agregar categoría</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="categorie.php">
            <div class="form-group">
                <input type="text" class="form-control" name="categorie-name" placeholder="Nombre de la categoría" required>
            </div>
            <button type="submit" name="add_cat" class="btn btn-primary">Agregar categoría</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Lista de categorías</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Categorías</th>
                    <th class="text-center" style="width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
              <?php if ($all_categories && is_array($all_categories)): ?>
                <?php foreach ($all_categories as $cat):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo remove_junk(ucfirst($cat['Nombre'])); ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a href="edit_categorie.php?id=<?php echo (int)$cat['ID'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar" onclick="return confirm('¿Está seguro de editar esta categoría?');">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_categorie.php?id=<?php echo (int)$cat['ID'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta categoría? Esta acción no se puede deshacer.');">
                          <span class="glyphicon glyphicon-trash"></span>
                        </a>
                      </div>
                    </td>
                </tr>
              <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-center">No se encontraron categorías.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
       </div>
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
    if(!confirm('¿Está seguro de editar esta categoría?')) {
      e.preventDefault();
    }
  });
  
  // Confirmación para eliminar
  $('.btn-danger').click(function(e) {
    if(!confirm('¿Está seguro de eliminar esta categoría? Esta acción no se puede deshacer.')) {
      e.preventDefault();
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>
