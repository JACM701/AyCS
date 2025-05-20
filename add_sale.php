<?php
  $page_title = 'Agregar venta';
  require_once('includes/load.php');
  page_require_level(3);

  // Registrar cliente
  if (isset($_POST['add_cliente'])) {
    $req_fields = array('nombre', 'apellido', 'correo', 'numero', 'direccion');
    validate_fields($req_fields);

    if (empty($errors)) {
      $nombre = $db->escape($_POST['nombre']);
      $apellido = $db->escape($_POST['apellido']);
      $correo = $db->escape($_POST['correo']);
      $numero = $db->escape($_POST['numero']);
      $direccion = $db->escape($_POST['direccion']);

      $sql = "INSERT INTO clientes (Nombre, Apellido, Correo, Numero, Direccion) 
              VALUES ('{$nombre}', '{$apellido}', '{$correo}', '{$numero}', '{$direccion}')";

      if ($db->query($sql)) {
        $session->msg("s", "Cliente registrado exitosamente");
        redirect('add_sale.php', false);
      } else {
        $session->msg("d", "Error al registrar cliente");
        redirect('add_sale.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_sale.php', false);
    }
  }

  // Registrar venta
  if (isset($_POST['add_sale'])) {
    $req_fields = array('cliente_id', 'fecha');
    validate_fields($req_fields);

    if (empty($errors)) {
      $cliente_id = $db->escape((int)$_POST['cliente_id']);
      $producto_id = $db->escape((int)$_POST['producto_id']);
      $servicio_id = $db->escape((int)$_POST['servicio_id']);
      $fecha = $db->escape($_POST['fecha']);

      $sql = "INSERT INTO venta (Id_Cliente, Id_Productos, Id_Servicio, Fecha) 
              VALUES ('{$cliente_id}', '{$producto_id}', '{$servicio_id}', '{$fecha}')";

      if ($db->query($sql)) {
        if ($producto_id > 0) {
          $sql_inv = "UPDATE inventario SET Cantidad = Cantidad - 1 WHERE Id_Producto = '{$producto_id}'";
          $db->query($sql_inv);
        }
        $session->msg("s", "Venta registrada exitosamente");
        redirect('add_sale.php', false);
      } else {
        $session->msg("d", "Error al registrar venta");
        redirect('add_sale.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_sale.php', false);
    }
  }
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Formulario para registrar cliente -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong><span class="glyphicon glyphicon-user"></span> Registrar Cliente</strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_sale.php">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="form-group">
            <label>Apellido</label>
            <input type="text" class="form-control" name="apellido" required>
          </div>
          <div class="form-group">
            <label>Correo</label>
            <input type="email" class="form-control" name="correo" required>
          </div>
          <div class="form-group">
            <label>Numero</label>
            <input type="text" class="form-control" name="numero" required>
          </div>
          <div class="form-group">
            <label>Dirección</label>
            <input type="text" class="form-control" name="direccion" required>
          </div>
          <button type="submit" name="add_cliente" class="btn btn-success">Registrar Cliente</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Formulario para registrar venta -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong><span class="glyphicon glyphicon-th"></span> Nueva Venta</strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_sale.php">
          <div class="form-group">
            <label>Cliente</label>
            <select class="form-control" name="cliente_id" required>
              <option value="">Seleccione un cliente</option>
              <?php 
              $clientes = find_all('clientes');
              foreach ($clientes as $cliente): ?>
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
              foreach ($productos as $producto): ?>
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
              foreach ($servicios as $servicio): ?>
                <option value="<?php echo $servicio['Id_Servicio']; ?>">
                  <?php echo $servicio['Nombre']; ?> - $<?php echo $servicio['Costo']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Fecha</label>
            <input type="date" class="form-control" name="fecha" value="<?php echo date('Y-m-d'); ?>" readonly required>
          </div>

          <button type="submit" name="add_sale" class="btn btn-primary">Registrar Venta</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>