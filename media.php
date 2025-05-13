<?php
  $page_title = 'Gestión de Imágenes';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
  if(isset($_POST['submit'])) {
    if(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
      $upload_dir = 'uploads/products/';
      if(!is_dir($upload_dir)){
        mkdir($upload_dir, 0777, true);
      }
      
      $file_name = $_FILES['file_upload']['name'];
      $file_type = $_FILES['file_upload']['type'];
      $file_tmp = $_FILES['file_upload']['tmp_name'];
      $file_size = $_FILES['file_upload']['size'];
      $description = remove_junk($db->escape($_POST['description']));
      
      // Validar tipo de archivo
      $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
      if(!in_array($file_type, $allowed_types)) {
        $session->msg('d', 'Solo se permiten imágenes (JPG, PNG, GIF)');
        redirect('media.php');
      }
      
      // Validar tamaño (5MB máximo)
      if($file_size > 5000000) {
        $session->msg('d', 'La imagen no debe superar los 5MB');
        redirect('media.php');
      }
      
      // Generar nombre único
      $new_file_name = uniqid() . '_' . $file_name;
      $upload_path = $upload_dir . $new_file_name;
      
      if(move_uploaded_file($file_tmp, $upload_path)) {
        // Guardar en la base de datos
        $sql = "INSERT INTO media (file_name, file_type, description) VALUES ('{$new_file_name}', '{$file_type}', '{$description}')";
        if($db->query($sql)) {
          $session->msg('s', 'Imagen subida exitosamente.');
          redirect('media.php');
        } else {
          $session->msg('d', 'Error al guardar en la base de datos.');
          redirect('media.php');
        }
      } else {
        $session->msg('d', 'Error al subir la imagen.');
        redirect('media.php');
      }
    } else {
      $session->msg('d', 'Por favor seleccione una imagen.');
      redirect('media.php');
    }
  }

  // Procesar edición de archivo
  if(isset($_POST['edit_file'])) {
    $id = (int)$_POST['media_id'];
    $new_file_name = remove_junk($db->escape($_POST['file_name']));
    $description = remove_junk($db->escape($_POST['description']));
    
    // Obtener el archivo actual
    $current_file = find_by_id('media', $id);
    if($current_file) {
      $old_path = 'uploads/products/' . $current_file['file_name'];
      $new_path = 'uploads/products/' . $new_file_name;
      
      // Renombrar el archivo físico
      if(rename($old_path, $new_path)) {
        // Actualizar en la base de datos
        $sql = "UPDATE media SET file_name = '{$new_file_name}', description = '{$description}' WHERE id = '{$id}'";
        if($db->query($sql)) {
          $session->msg('s', 'Archivo actualizado exitosamente.');
        } else {
          $session->msg('d', 'Error al actualizar en la base de datos.');
        }
      } else {
        $session->msg('d', 'Error al renombrar el archivo.');
      }
    } else {
      $session->msg('d', 'Archivo no encontrado.');
    }
    redirect('media.php');
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
      <div class="panel-heading clearfix">
        <div class="pull-left">
          <span class="glyphicon glyphicon-camera"></span>
          <span>Gestión de Imágenes</span>
        </div>
        <div class="pull-right">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
            <span class="glyphicon glyphicon-upload"></span> Subir Nueva Imagen
          </button>
        </div>
      </div>
      <div class="panel-body">
        <div class="row">
          <?php 
          $media_files = find_all('media');
          if($media_files && is_array($media_files)):
            foreach ($media_files as $media_file): 
          ?>
          <div class="col-md-3 col-sm-4 col-xs-6">
            <div class="thumbnail">
              <img src="uploads/products/<?php echo $media_file['file_name'];?>" class="img-responsive" style="height: 200px; object-fit: cover;"/>
              <div class="caption">
                <p class="text-center">
                  <strong><?php echo $media_file['file_name'];?></strong>
                </p>
                <p class="text-center">
                  <small>Tipo: <?php echo $media_file['file_type'];?></small>
                </p>
                <p class="text-center description" id="desc-<?php echo $media_file['id'];?>">
                  <?php echo $media_file['description'] ? $media_file['description'] : 'Sin descripción'; ?>
                </p>
                <div class="text-center">
                  <button class="btn btn-info btn-xs edit-file" data-id="<?php echo $media_file['id'];?>" data-file="<?php echo htmlspecialchars($media_file['file_name']);?>" data-desc="<?php echo htmlspecialchars($media_file['description']);?>">
                    <span class="glyphicon glyphicon-edit"></span> Editar
                  </button>
                  <a href="delete_media.php?id=<?php echo (int) $media_file['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta imagen?');">
                    <span class="glyphicon glyphicon-trash"></span> Eliminar
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php 
            endforeach;
          else:
          ?>
          <div class="col-md-12">
            <div class="alert alert-info">
              No hay imágenes disponibles. Sube una imagen para comenzar.
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para subir imagen -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Subir Nueva Imagen</h4>
      </div>
      <form action="media.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label>Seleccionar Imagen</label>
            <input type="file" name="file_upload" class="form-control" accept="image/*" required>
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <input type="text" name="description" class="form-control" placeholder="Ingrese una descripción para la imagen">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" name="submit" class="btn btn-primary">Subir</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para editar archivo -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Archivo</h4>
      </div>
      <form action="media.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="media_id" id="edit_media_id">
          <div class="form-group">
            <label>Nombre del Archivo</label>
            <input type="text" name="file_name" id="edit_file_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <input type="text" name="description" id="edit_description" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" name="edit_file" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.thumbnail {
  margin-bottom: 20px;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 10px;
  transition: all 0.3s ease;
}

.thumbnail:hover {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

.description {
  min-height: 40px;
  margin: 10px 0;
}

.panel-heading {
  padding: 15px;
  background-color: #f5f5f5;
  border-bottom: 1px solid #ddd;
}

.panel-heading .glyphicon {
  margin-right: 5px;
}
</style>

<script>
$(document).ready(function(){
  $('.edit-file').click(function(){
    var id = $(this).data('id');
    var file = $(this).data('file');
    var desc = $(this).data('desc');
    $('#edit_media_id').val(id);
    $('#edit_file_name').val(file);
    $('#edit_description').val(desc);
    $('#editModal').modal('show');
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>
