<?php
  require_once('includes/load.php');
  page_require_level(2);
?>
<?php
  $customer = find_by_id('clientes',(int)$_GET['id']);
  if(!$customer){
    $session->msg("d","ID de cliente no encontrado.");
    redirect('customers.php');
  }
?>
<?php
  if(isset($_POST['edit_customer'])){
    $req_fields = array('customer-name','customer-phone','customer-email','customer-address');
    validate_fields($req_fields);

    if(empty($errors)){
      $name   = remove_junk($db->escape($_POST['customer-name']));
      $phone  = remove_junk($db->escape($_POST['customer-phone']));
      $email  = remove_junk($db->escape($_POST['customer-email']));
      $address = remove_junk($db->escape($_POST['customer-address']));

      $query  = "UPDATE clientes SET ";
      $query .= "Nombre='{$name}', Numero='{$phone}', Correo='{$email}', Direccion='{$address}'";
      $query .= " WHERE Id_Cliente='{$customer['Id_Cliente']}'";
      
      if($db->query($query)){
        $session->msg('s',"Cliente actualizado exitosamente.");
        redirect('customers.php', false);
      } else {
        $session->msg('d',' Lo siento, actualización falló.');
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
          <span>Editar Cliente</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="edit_customer.php?id=<?php echo (int)$customer['Id_Cliente'] ?>" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-user"></i>
                </span>
                <input type="text" class="form-control" name="customer-name" value="<?php echo remove_junk($customer['Nombre']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-phone"></i>
                </span>
                <input type="text" class="form-control" name="customer-phone" value="<?php echo remove_junk($customer['Numero']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-envelope"></i>
                </span>
                <input type="email" class="form-control" name="customer-email" value="<?php echo remove_junk($customer['Correo']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-home"></i>
                </span>
                <input type="text" class="form-control" name="customer-address" value="<?php echo remove_junk($customer['Direccion']); ?>" required>
              </div>
            </div>
            <button type="submit" name="edit_customer" class="btn btn-danger">Actualizar Cliente</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?> 