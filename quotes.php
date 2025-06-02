<?php
  $page_title = 'Listado de Cotizaciones';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);

  // Obtener todas las cotizaciones
  $sql = "SELECT c.*, 
          COUNT(dc.ID) as total_items,
          DATE_FORMAT(c.Fecha, '%d/%m/%Y') as fecha_formateada,
          cl.Nombre as client_name
          FROM cotizacion c 
          LEFT JOIN detalle_cotizacion dc ON c.ID = dc.Id_Cotizacion 
          LEFT JOIN cliente cl ON c.Id_Cliente = cl.ID
          GROUP BY c.ID 
          ORDER BY c.Fecha DESC";
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
              <th>Items</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($quotes as $quote): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td><?php echo remove_junk($quote['client_name']); ?></td>
                <td><?php echo $quote['fecha_formateada']; ?></td>
                <td class="text-center"><?php echo $quote['total_items']; ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="view_quote.php?id=<?php echo (int)$quote['ID']; ?>" class="btn btn-xs btn-info" data-toggle="tooltip" title="Ver">
                      <i class="glyphicon glyphicon-eye-open"></i>
                    </a>
                    <a href="edit_quote.php?id=<?php echo (int)$quote['ID']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_quote.php?id=<?php echo (int)$quote['ID']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar">
                      <i class="glyphicon glyphicon-trash"></i>
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