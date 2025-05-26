<?php
  $page_title = 'Agregar producto';
  require_once('includes/load.php');
  page_require_level(2);

  // Obtener proveedores y categorías
  $providers = find_all('proveedores');
  $categories = find_all('categories');

  // Registro de proveedor
  if (isset($_POST['add_provider'])) {
    $req_fields = array('provider-name', 'provider-number', 'provider-email', 'provider-rfc');
    validate_fields($req_fields);

    if (empty($errors)) {
      $name   = remove_junk($db->escape($_POST['provider-name']));
      $number = remove_junk($db->escape($_POST['provider-number']));
      $email  = remove_junk($db->escape($_POST['provider-email']));
      $rfc    = remove_junk($db->escape($_POST['provider-rfc']));

      $query = "INSERT INTO proveedores (Nombre, Número, Correo, RFC) 
                VALUES ('{$name}', '{$number}', '{$email}', '{$rfc}')";

      if ($db->query($query)) {
        $session->msg('s', "Proveedor agregado exitosamente.");
        redirect('add_product.php', false);
      } else {
        $session->msg('d', "Error al registrar el proveedor.");
      }
    } else {
      $session->msg("d", $errors);
    }
  }

  // Registro de producto
  if (isset($_POST['add_product'])) {
    $req_fields = array('product-title', 'product-description', 'product-quantity', 'product-cost', 'product-provider', 'product-category');
    validate_fields($req_fields);

    if (empty($errors)) {
      $p_name     = remove_junk($db->escape($_POST['product-title']));
      $p_desc     = remove_junk($db->escape($_POST['product-description']));
      $p_qty      = remove_junk($db->escape($_POST['product-quantity']));
      $p_cost     = remove_junk($db->escape($_POST['product-cost']));
      $p_provider = remove_junk($db->escape($_POST['product-provider']));
      $p_category = remove_junk($db->escape($_POST['product-category']));
      $p_photo    = '';

      if (isset($_FILES['product-photo']) && $_FILES['product-photo']['size'] > 0) {
        $p_photo = upload_image($_FILES['product-photo'], 'uploads/products/');
      }

      $query  = "INSERT INTO productos (Nombre, Descripcion, Costo, Foto, Id_Proveedor, Categoria) ";
      $query .= "VALUES ('{$p_name}', '{$p_desc}', '{$p_cost}', '{$p_photo}', '{$p_provider}', '{$p_category}')";

      if ($db->query($query)) {
        $product_id = $db->insert_id();
        $query2 = "INSERT INTO inventario (Id_Producto, Cantidad) VALUES ('{$product_id}', '{$p_qty}')";

        if ($db->query($query2)) {
          $session->msg('s', "Producto agregado exitosamente.");
          redirect('add_product.php', false);
        } else {
          $session->msg('d', 'Error al agregar al inventario.');
          redirect('product.php', false);
        }
      } else {
        $session->msg('d', 'Lo siento, el registro falló.');
        redirect('product.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_product.php', false);
    }
  }
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12"><?php echo display_msg($msg); ?></div>
</div>

<!-- FORMULARIO PARA REGISTRAR PRODUCTO -->
<div class="row">
  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th"></span> Agregar producto</strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_product.php" class="clearfix" enctype="multipart/form-data">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-th-large"></i></span>
              <input type="text" class="form-control" name="product-title" placeholder="Nombre del producto" required>
            </div>
          </div>

          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-th-large"></i></span>
              <textarea class="form-control" name="product-description" placeholder="Descripción del producto" required></textarea>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-shopping-cart"></i></span>
                  <input type="number" class="form-control" name="product-quantity" placeholder="Cantidad" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                  <input type="number" class="form-control" name="product-cost" placeholder="Costo" required>
                  <span class="input-group-addon">.00</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-picture"></i></span>
                  <input type="file" class="form-control" name="product-photo" accept="image/*">
                </div>
              </div>
            </div>
          </div>

          <!-- Proveedor -->
          <div class="form-group">
            <div class="row">
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-trademark"></i></span>
                  <select name="product-provider" class="form-control" required>
                    <option value="">Seleccionar proveedor</option>
                    <?php foreach ($providers as $provider): ?>
                      <option value="<?php echo (int)$provider['Id_Proveedor']; ?>">
                        <?php echo remove_junk($provider['Nombre']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addProviderModal">
                  Agregar proveedor
                </button>
              </div>
            </div>
          </div>

          <!-- Categoría -->
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
              <select name="product-category" class="form-control" required>
                <option value="">Seleccionar categoría</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo (int)$category['id']; ?>">
                    <?php echo remove_junk($category['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <button type="submit" name="add_product" class="btn btn-danger btn-lg btn-block">Agregar producto</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA REGISTRAR PROVEEDOR (fuera del formulario principal) -->
<div id="addProviderModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addProviderModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="add_product.php" class="clearfix">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title" id="addProviderModalLabel">Agregar proveedor</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <input type="text" class="form-control" name="provider-name" placeholder="Nombre del proveedor" required>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="provider-number" placeholder="Número de teléfono" required>
          </div>
          <div class="form-group">
            <input type="email" class="form-control" name="provider-email" placeholder="Correo electrónico" required>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="provider-rfc" placeholder="RFC" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_provider" class="btn btn-success btn-lg btn-block">Registrar proveedor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>