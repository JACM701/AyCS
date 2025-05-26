<?php
  require_once('includes/load.php');
  page_require_level(2);
?>
<?php
  if(isset($_POST['add_customer'])){
    $req_fields = array('customer-name','customer-phone','customer-email','customer-address');
    validate_fields($req_fields);

    if(empty($errors)){
      $name   = remove_junk($db->escape($_POST['customer-name']));
      $phone  = remove_junk($db->escape($_POST['customer-phone']));
      $email  = remove_junk($db->escape($_POST['customer-email']));
      $address = remove_junk($db->escape($_POST['customer-address']));

      $query  = "INSERT INTO clientes (";
      $query .=" Nombre, Numero, Correo, Direccion";
      $query .=") VALUES (";
      $query .=" '{$name}', '{$phone}', '{$email}', '{$address}'";
      $query .=")";
      
      if($db->query($query)){
        $session->msg('s',"Cliente agregado exitosamente.");
        redirect('customers.php', false);
      } else {
        $session->msg('d',' Lo siento, registro falló.');
        redirect('customers.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('customers.php',false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Agregar Nuevo Cliente</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_customer.php" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-user"></i>
                </span>
                <input type="text" class="form-control" name="customer-name" placeholder="Nombre del Cliente" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-phone"></i>
                </span>
                <input type="text" class="form-control" name="customer-phone" placeholder="Teléfono" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-envelope"></i>
                </span>
                <input type="email" class="form-control" name="customer-email" placeholder="Correo Electrónico" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-home"></i>
                </span>
                <input type="text" class="form-control" name="customer-address" placeholder="Dirección" required>
              </div>
            </div>
            <button type="submit" name="add_customer" class="btn btn-danger">Agregar Cliente</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?> 