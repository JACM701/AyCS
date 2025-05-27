<?php
  $page_title = 'Gestión de Kits';
  require_once('includes/load.php');
  page_require_level(2);

  $all_kits = find_all('kits');
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Kits Disponibles</span>
        </strong>
        <a href="add_kit.php" class="btn btn-primary pull-right">Agregar Kit</a>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th class="text-center">Precio Base</th>
              <th class="text-center">Precio por Cámara</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_kits as $kit):?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk($kit['nombre']); ?></td>
              <td><?php echo remove_junk($kit['descripcion']); ?></td>
              <td class="text-center">$<?php echo number_format($kit['precio_base'], 2); ?></td>
              <td class="text-center">$<?php echo number_format($kit['precio_por_camara'], 2); ?></td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="edit_kit.php?id=<?php echo (int)$kit['id'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-edit"></span>
                  </a>
                  <a href="delete_kit.php?id=<?php echo (int)$kit['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Estás seguro de eliminar este kit?');">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?> 