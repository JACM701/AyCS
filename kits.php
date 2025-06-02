<?php
  $page_title = 'Lista de Kits';
  require_once('includes/load.php');
  page_require_level(2);

  // Obtener todos los kits con sus productos
  $sql = "SELECT k.*, 
          GROUP_CONCAT(p.Nombre SEPARATOR ', ') as productos,
          GROUP_CONCAT(kp.Cantidad SEPARATOR ', ') as cantidades
          FROM kit k
          LEFT JOIN kit_producto kp ON k.ID = kp.Id_Kit
          LEFT JOIN producto p ON kp.Id_Producto = p.ID
          GROUP BY k.ID
          ORDER BY k.ID DESC";
  $kits = find_by_sql($sql);
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
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Lista de Kits</span>
        </strong>
        <div class="pull-right">
          <a href="add_kit.php" class="btn btn-primary">Agregar Kit</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio Base</th>
              <th>Productos</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if($kits): ?>
              <?php foreach($kits as $kit): ?>
                <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo remove_junk($kit['Nombre']); ?></td>
                  <td><?php echo remove_junk($kit['Descripcion']); ?></td>
                  <td>$<?php echo number_format($kit['Precio'], 2); ?></td>
                  <td><?php echo remove_junk($kit['productos'] ?? 'Sin productos'); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_kit.php?id=<?php echo (int)$kit['ID'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                        <i class="glyphicon glyphicon-pencil"></i>
                      </a>
                      <a href="delete_kit.php?id=<?php echo (int)$kit['ID'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este kit?');">
                        <i class="glyphicon glyphicon-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center">No hay kits registrados</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
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
</style>

<?php include_once('layouts/footer.php'); ?> 