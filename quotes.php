<?php
  $page_title = 'Listado de Cotizaciones';
  require_once('includes/load.php');
  // Verificar nivel de usuario
  page_require_level(1);

  // Obtener todas las cotizaciones
  $sql = "SELECT q.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido 
          FROM quotes q 
          LEFT JOIN clientes c ON q.client_id = c.Id_Cliente 
          ORDER BY q.quote_date DESC";
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
              <th>Tipo</th>
              <th class="text-center">Subtotal</th>
              <th class="text-center">Descuento</th>
              <th class="text-center">Total</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($quotes as $quote): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td>
                  <?php 
                    if($quote['client_id']) {
                      echo remove_junk($quote['ClienteNombre'] . ' ' . $quote['ClienteApellido']);
                    } else {
                      echo remove_junk($quote['client_name']);
                    }
                  ?>
                </td>
                <td><?php echo read_date($quote['quote_date']); ?></td>
                <td><?php echo remove_junk($quote['quote_type']); ?></td>
                <td class="text-center">$<?php echo number_format($quote['subtotal'], 2); ?></td>
                <td class="text-center"><?php echo $quote['discount_percentage']; ?>%</td>
                <td class="text-center">$<?php echo number_format($quote['total_amount'], 2); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_quote.php?id=<?php echo (int)$quote['id'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_quote.php?id=<?php echo (int)$quote['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Estás seguro de eliminar esta cotización?');">
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