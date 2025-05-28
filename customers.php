<?php
  require_once('includes/load.php');
  page_require_level(2);

  $all_customers = find_all('cliente');
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
          <span>Clientes</span>
        </strong>
        <a href="add_customer.php" class="btn btn-info pull-right">Agregar Cliente</a>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th> Nombre </th>
              <th> Teléfono </th>
              <th> Correo </th>
              <th> Dirección </th>
              <th class="text-center" style="width: 100px;"> Acciones </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_customers as $customer):?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk($customer['Nombre']); ?></td>
              <td><?php echo remove_junk($customer['Numero']); ?></td>
              <td><?php echo remove_junk($customer['Correo']); ?></td>
              <td><?php echo remove_junk($customer['Direccion']); ?></td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="edit_customer.php?id=<?php echo (int)$customer['ID'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                    <i class="glyphicon glyphicon-pencil"></i>
                  </a>
                  <a href="delete_customer.php?id=<?php echo (int)$customer['ID'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar">
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