<?php
  $page_title = 'Listado de Cotizaciones';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);

  // Obtener todas las cotizaciones
  $sql = "SELECT q.ID, q.Id_Cliente, q.Fecha, c.Nombre as ClienteNombre 
          FROM cotizacion q 
          LEFT JOIN cliente c ON q.Id_Cliente = c.ID 
          ORDER BY q.Fecha DESC";
  $quotes = find_by_sql($sql);
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
          <span>Listado de Cotizaciones</span>
        </strong>
        <div class="pull-right">
          <a href="add_quote.php" class="btn btn-primary">Agregar Cotización</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($quotes as $quote): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td>
                  <?php 
                    if(isset($quote['ClienteNombre'])) {
                      echo remove_junk($quote['ClienteNombre']);
                    } else {
                      echo 'Cliente Desconocido';
                    }
                  ?>
                </td>
                <td><?php echo read_date($quote['Fecha']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_quote.php?id=<?php echo (int)$quote['ID'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_quote.php?id=<?php echo (int)$quote['ID'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Estás seguro de eliminar esta cotización?');">
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