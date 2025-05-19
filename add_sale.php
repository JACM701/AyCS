<?php
  $page_title = 'Agregar venta';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php

  if(isset($_POST['add_sale'])){
    $req_fields = array('cliente_id', 'producto_id', 'servicio_id', 'fecha');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $cliente_id = $db->escape((int)$_POST['cliente_id']);
      $producto_id = $db->escape((int)$_POST['producto_id']);
      $servicio_id = $db->escape((int)$_POST['servicio_id']);
      $fecha = $db->escape($_POST['fecha']);

      $sql = "INSERT INTO venta (";
      $sql .= "Id_Cliente, Id_Productos, Id_Servicio, Fecha";
      $sql .= ") VALUES (";
      $sql .= "'{$cliente_id}', '{$producto_id}', '{$servicio_id}', '{$fecha}'";
      $sql .= ")";

      if($db->query($sql)){
        // Actualizar inventario
        if($producto_id > 0) {
          $sql_inv = "UPDATE inventario SET Cantidad = Cantidad - 1 WHERE Id_Producto = '{$producto_id}'";
          $db->query($sql_inv);
        }
        $session->msg('s',"Venta agregada exitosamente");
        redirect('add_sale.php', false);
      } else {
        $session->msg('d','Lo siento, registro falló.');
        redirect('add_sale.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_sale.php',false);
    }
  }

?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Nueva Venta</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_sale.php">
          <div class="form-group">
            <label>Cliente</label>
            <select class="form-control" name="cliente_id" required>
              <option value="">Seleccione un cliente</option>
              <?php 
              $clientes = find_all('clientes');
              foreach($clientes as $cliente): ?>
                <option value="<?php echo $cliente['Id_Cliente']; ?>">
                  <?php echo $cliente['Nombre'] . ' ' . $cliente['Apellido']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Producto</label>
            <select class="form-control" name="producto_id">
              <option value="">Seleccione un producto (opcional)</option>
              <?php 
              $productos = find_all('productos');
              foreach($productos as $producto): ?>
                <option value="<?php echo $producto['Id_Productos']; ?>">
                  <?php echo $producto['Nombre']; ?> - $<?php echo $producto['Costo']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Servicio</label>
            <select class="form-control" name="servicio_id">
              <option value="">Seleccione un servicio (opcional)</option>
              <?php 
              $servicios = find_all('servicio');
              foreach($servicios as $servicio): ?>
                <option value="<?php echo $servicio['Id_Servicio']; ?>">
                  <?php echo $servicio['Nombre']; ?> - $<?php echo $servicio['Costo']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Fecha</label>
            <input type="date" class="form-control" name="fecha" required>
          </div>

          <button type="submit" name="add_sale" class="btn btn-primary">Registrar Venta</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>