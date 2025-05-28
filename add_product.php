<?php
  $page_title = 'Agregar producto';
  require_once('includes/load.php');
  page_require_level(2);

  // Obtener proveedores y categorías
  $providers = find_all('proveedor');
  $categories = find_all('categoria_producto');

  // Registro de proveedor
  if (isset($_POST['add_provider'])) {
    $req_fields = array('provider-name', 'provider-number', 'provider-rfc');
    validate_fields($req_fields);

    if (empty($errors)) {
      $name   = remove_junk($db->escape($_POST['provider-name']));
      $number = remove_junk($db->escape($_POST['provider-number']));
      $rfc    = remove_junk($db->escape($_POST['provider-rfc']));

      $query = "INSERT INTO proveedor (Nombre, Telefono, RFC) 
                VALUES ('{$name}', '{$number}', '{$rfc}')";

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
    $req_fields = array('product-title', 'product-description', 'product-quantity', 'product-price', 'product-provider', 'product-category');
    validate_fields($req_fields);

    if (empty($errors)) {
      $p_name     = remove_junk($db->escape($_POST['product-title']));
      $p_desc     = remove_junk($db->escape($_POST['product-description']));
      $p_qty      = remove_junk($db->escape($_POST['product-quantity']));
      $p_price     = remove_junk($db->escape($_POST['product-price']));
      $p_provider = remove_junk($db->escape($_POST['product-provider']));
      $p_category = remove_junk($db->escape($_POST['product-category']));

      $query  = "INSERT INTO producto (";
      $query .= "Nombre, Descripcion, Precio, Id_Proveedor, Id_Categoria)";
      $query .= " VALUES (";
      $query .= "'{$p_name}', '{$p_desc}', '{$p_price}', '{$p_provider}', '{$p_category}'";
      $query .= ")";

      if ($db->query($query)) {
        $product_id = $db->insert_id();
        $query2 = "INSERT INTO inventario (Id_Producto, Cantidad) VALUES ('{$product_id}', '{$p_qty}')";

        if ($db->query($query2)) {
          $session->msg('s', "Producto agregado exitosamente.");
          redirect('product.php', false);
        } else {
          $session->msg('d', 'Error al agregar al inventario.');
          redirect('add_product.php', false);
        }
      } else {
        $session->msg('d', 'Lo siento, el registro falló.');
        redirect('add_product.php', false);
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
            <label for="product-title">Nombre del producto</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-th-large"></i></span>
              <input type="text" class="form-control" name="product-title" placeholder="Nombre del producto" required>
            </div>
          </div>

          <div class="form-group">
            <label for="product-description">Descripción</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-align-left"></i></span>
              <textarea class="form-control" name="product-description" placeholder="Descripción del producto" required></textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                 <label for="product-quantity">Cantidad</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-shopping-cart"></i></span>
                  <input type="number" class="form-control" name="product-quantity" placeholder="Cantidad" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                 <label for="product-price">Precio</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                  <input type="number" class="form-control" name="product-price" id="product-price" placeholder="Precio" step="0.01" require>
                  <span class="input-group-addon">.00</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Proveedor -->
          <div class="form-group">
             <label for="product-provider">Proveedor</label>
            <div class="row">
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-trademark"></i></span>
                  <select name="product-provider" class="form-control" required>
                    <option value="">Seleccionar proveedor</option>
                    <?php foreach ($providers as $provider): ?>
                      <option value="<?php echo (int)$provider['ID']; ?>">
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
             <label for="product-category">Categoría</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
              <select name="product-category" class="form-control" required>
                <option value="">Seleccionar categoría</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo (int)$category['ID']; ?>">
                    <?php echo remove_junk($category['Nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <button type="submit" name="add_product" class="btn btn-primary pull-right">Agregar producto</button>
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
            <label for="provider-name">Nombre del proveedor</label>
            <input type="text" class="form-control" name="provider-name" placeholder="Nombre del proveedor" required>
          </div>
          <div class="form-group">
            <label for="provider-number">Número de teléfono</label>
            <input type="text" class="form-control" name="provider-number" placeholder="Número de teléfono" required>
          </div>
          <div class="form-group">
             <label for="provider-rfc">RFC</label>
            <input type="text" class="form-control" name="provider-rfc" placeholder="RFC" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_provider" class="btn btn-primary">Registrar proveedor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.custom-file-upload {
    display: inline-block;
    position: relative;
}

.custom-file-upload label {
    margin-bottom: 0;
    cursor: pointer;
}

#image-preview {
    transition: all 0.3s ease;
}

#image-preview:hover {
    border-color: #2196F3;
}

#file-name {
    color: #666;
    font-style: italic;
}
</style>

<script>
document.getElementById('product-photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Actualizar nombre del archivo
        document.getElementById('file-name').textContent = file.name;
        
        // Vista previa de la imagen
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            preview.style.backgroundImage = 'url(' + e.target.result + ')';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('file-name').textContent = 'Ninguna imagen seleccionada';
        document.getElementById('image-preview').style.backgroundImage = 'none';
    }
});

// Script para calcular precios basados en porcentaje
$('#product-cost, #product-public-percent, #product-installer-percent').on('input', function() {
  var cost = parseFloat($('#product-cost').val()) || 0;
  var publicPercent = parseFloat($('#product-public-percent').val()) || 0;
  var installerPercent = parseFloat($('#product-installer-percent').val()) || 0;
  
  // Calcular Precio Público si se introduce porcentaje y costo
  if (cost > 0 && publicPercent >= 0) {
    var publicPrice = cost * (1 + (publicPercent / 100));
    $('#product-public-price').val(publicPrice.toFixed(2));
  } else if (cost > 0 && publicPercent < 0) {
       $('#product-public-price').val('');
  }
   else {
    $('#product-public-price').val('');
  }

  // Calcular Precio Instalador si se introduce porcentaje y costo
   if (cost > 0 && installerPercent >= 0) {
    var installerPrice = cost * (1 + (installerPercent / 100));
    $('#product-installer-price').val(installerPrice.toFixed(2));
  } else if (cost > 0 && installerPercent < 0) {
       $('#product-installer-price').val('');
  }
   else {
    $('#product-installer-price').val('');
  }

});
</script>

<?php include_once('layouts/footer.php'); ?>