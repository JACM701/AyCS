<?php
$page_title = 'Agregar Cotización';
require_once('includes/load.php');

// Verificar nivel de usuario
page_require_level(1);

// Registrar cliente
if(isset($_POST['add_cliente'])) {
    $req_fields = array('nombre', 'correo', 'direccion');
    validate_fields($req_fields);

    if(empty($errors)) {
        $nombre = $db->escape($_POST['nombre']);
        $correo = $db->escape($_POST['correo']);
        $numero = $db->escape($_POST['numero']);
        $direccion = $db->escape($_POST['direccion']);

        $sql = "INSERT INTO cliente (Nombre, Correo, Numero_Telefono, Direccion) 
                VALUES ('{$nombre}', '{$correo}', '{$numero}', '{$direccion}')";

        if($db->query($sql)) {
            $session->msg('s', "Cliente registrado exitosamente");
            redirect('add_quote.php', false);
        } else {
            $session->msg('d', "Error al registrar cliente");
            redirect('add_quote.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_quote.php', false);
    }
}

// Registrar producto
if(isset($_POST['add_product'])) {
    $req_fields = array('product-title', 'product-description', 'product-quantity', 'product-price', 'product-category');
    validate_fields($req_fields);

    if(empty($errors)) {
        $p_name = remove_junk($db->escape($_POST['product-title']));
        $p_desc = remove_junk($db->escape($_POST['product-description']));
        $p_qty = remove_junk($db->escape($_POST['product-quantity']));
        $p_price = remove_junk($db->escape($_POST['product-price']));
        $p_category = remove_junk($db->escape($_POST['product-category']));

        $query = "INSERT INTO producto (Nombre, Descripcion, Precio, Id_Categoria) 
                 VALUES ('{$p_name}', '{$p_desc}', '{$p_price}', '{$p_category}')";

        if($db->query($query)) {
            $product_id = $db->insert_id();
            $query2 = "INSERT INTO inventario (Id_Producto, Cantidad) VALUES ('{$product_id}', '{$p_qty}')";

            if($db->query($query2)) {
                $session->msg('s', "Producto agregado exitosamente");
                redirect('add_quote.php', false);
            } else {
                $session->msg('d', 'Error al agregar al inventario');
                redirect('add_quote.php', false);
            }
        } else {
            $session->msg('d', 'Error al agregar el producto');
            redirect('add_quote.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_quote.php', false);
    }
}

// Obtener lista de clientes
$sql = "SELECT ID, Nombre, Numero_Telefono, Direccion, Correo FROM cliente ORDER BY Nombre";
$clientes = find_by_sql($sql);

// Obtener lista de productos con información de inventario
$sql = "SELECT p.ID, p.Nombre, p.Precio, i.Cantidad 
        FROM producto p 
        LEFT JOIN inventario i ON p.ID = i.Id_Producto 
        ORDER BY p.Nombre";
$productos = find_by_sql($sql);

// Obtener lista de servicios
$sql_servicios = "SELECT ID, Nombre, Costo FROM servicio ORDER BY Nombre";
$servicios = find_by_sql($sql_servicios);

// Obtener categorías para el formulario de productos
$categorias = find_all('categoria_producto');

// Obtener lista de kits
$sql_kits = "SELECT k.ID, k.Nombre, k.Descripcion, k.Precio 
             FROM kit k 
             ORDER BY k.Nombre";
$kits = find_by_sql($sql_kits);

// Obtener productos de un kit específico
if(isset($_GET['kit_id'])) {
    $kit_id = (int)$_GET['kit_id'];
    $sql_kit_products = "SELECT p.ID, p.Nombre, p.Precio, kp.Cantidad 
                        FROM kit_producto kp 
                        JOIN producto p ON kp.Id_Producto = p.ID 
                        WHERE kp.Id_Kit = '{$kit_id}'";
    $kit_products = find_by_sql($sql_kit_products);
}

// Procesar el formulario cuando se envía
if(isset($_POST['add_quote'])) {
    $req_fields = array('cliente_id', 'fecha');
    validate_fields($req_fields);

    if(empty($errors)) {
        $cliente_id = (int)$_POST['cliente_id'];
        $fecha = $db->escape($_POST['fecha']);
        $telefono = $db->escape($_POST['cliente_telefono']);
        $correo = $db->escape($_POST['cliente_correo']);
        $direccion = $db->escape($_POST['cliente_direccion']);

        // Iniciar transacción
        $db->query('START TRANSACTION');

        try {
            // Insertar la cotización
            $sql = "INSERT INTO cotizacion (Id_Cliente, Fecha) 
                    VALUES ('{$cliente_id}', '{$fecha}')";
            
            if($db->query($sql)) {
                $cotizacion_id = $db->insert_id();
                
                // Procesar los items
                if(isset($_POST['items']) && is_array($_POST['items'])) {
                    foreach($_POST['items'] as $item) {
                        // Validar tipo de item (producto o servicio)
                        $tipo = isset($item['tipo']) ? $item['tipo'] : 'producto';
                        $producto_id = isset($item['producto_id']) ? (int)$item['producto_id'] : null;
                        $servicio_id = isset($item['servicio_id']) ? (int)$item['servicio_id'] : null;
                        $cantidad = isset($item['cantidad']) ? (int)$item['cantidad'] : 1;
                        $precio = isset($item['precio']) ? (float)$item['precio'] : 0;

                        if ($tipo === 'producto' && $producto_id) {
                            $sql = "INSERT INTO detalle_cotizacion (Id_Cotizacion, Id_Producto, Precio) " .
                                   " VALUES ('{$cotizacion_id}', '{$producto_id}', '{$precio}')";
                        } elseif ($tipo === 'servicio' && $servicio_id) {
                            $sql = "INSERT INTO detalle_cotizacion (Id_Cotizacion, Id_Servicio, Precio) " .
                                   " VALUES ('{$cotizacion_id}', '{$servicio_id}', '{$precio}')";
                        } else {
                            continue; // Saltar si no hay datos válidos
                        }

                        if(!$db->query($sql)) {
                            throw new Exception("Error al insertar detalle de cotización");
                        }
                    }
                }
                
                // Confirmar transacción
                $db->query('COMMIT');
                $session->msg('s', "Cotización agregada exitosamente.");
                redirect('quotes.php', false);
            } else {
                throw new Exception("Error al crear la cotización");
            }
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $db->query('ROLLBACK');
            $session->msg('d', "Error al agregar la cotización: " . $e->getMessage());
            redirect('add_quote.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_quote.php', false);
    }
}
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
          <span>Nueva Cotización</span>
        </strong>
        <div class="pull-right">
          <a href="quotes.php" class="btn btn-default">Volver</a>
        </div>
      </div>
      <div class="panel-body">
        <form method="post" action="add_quote.php" class="clearfix">
          <!-- Información del Cliente -->
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Información del Cliente</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="cliente_id">Cliente</label>
                        <div class="input-group">
                          <select class="form-control" name="cliente_id" id="cliente_id" required>
                            <option value="">Seleccione un cliente</option>
                            <?php foreach($clientes as $cliente): ?>
                              <option value="<?php echo (int)$cliente['ID']; ?>" 
                                      data-telefono="<?php echo htmlspecialchars($cliente['Numero_Telefono'] ?? ''); ?>"
                                      data-direccion="<?php echo htmlspecialchars($cliente['Direccion'] ?? ''); ?>"
                                      data-correo="<?php echo htmlspecialchars($cliente['Correo'] ?? ''); ?>">
                                <?php echo remove_junk($cliente['Nombre']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <span class="input-group-btn">
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addCustomerModal">
                              <i class="glyphicon glyphicon-plus"></i>
                            </button>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" id="cliente_telefono" name="cliente_telefono">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Correo</label>
                            <input type="email" class="form-control" id="cliente_correo" name="cliente_correo">
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" class="form-control" id="cliente_direccion" name="cliente_direccion">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Items de la Cotización -->
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-info">
                <div class="panel-heading clearfix">
                  <h3 class="panel-title">Items de la Cotización</h3>
                </div>
                <div class="panel-body">
                  <!-- Selección de Kit -->
                  <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="kit_id">Kit (opcional)</label>
                        <select class="form-control" id="kit_id" name="kit_id">
                          <option value="">Seleccione un kit</option>
                          <?php foreach($kits as $kit): ?>
                            <option value="<?php echo (int)$kit['ID']; ?>">
                              <?php echo remove_junk($kit['Nombre']); ?> - $<?php echo number_format($kit['Precio'], 2); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Formulario para agregar items -->
                  <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product_id">Producto</label>
                        <select class="form-control" id="product_id" name="product_id">
                          <option value="">Seleccione un producto</option>
                          <?php foreach($productos as $producto): ?>
                            <option value="<?php echo (int)$producto['ID']; ?>" 
                                    data-row='<?php echo json_encode([
                                      "nombre" => $producto['Nombre'],
                                      "precio" => $producto['Precio'],
                                      "stock" => $producto['Cantidad']
                                    ]); ?>'>
                              <?php echo remove_junk($producto['Nombre']); ?> - $<?php echo number_format($producto['Precio'], 2); ?> (Stock: <?php echo $producto['Cantidad']; ?>)
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" id="add-item">
                          <i class="glyphicon glyphicon-plus"></i> Agregar
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Tabla de items -->
                  <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th>Cantidad</th>
                          <th>Precio Unitario</th>
                          <th>Subtotal</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="3" class="text-right"><strong>Total:</strong></td>
                          <td colspan="2"><span id="total">$0.00</span></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group clearfix">
            <button type="submit" name="add_quote" class="btn btn-primary">Guardar Cotización</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.panel {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    margin-bottom: 20px;
}

.panel:hover {
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}

.panel-info {
    border-color: #bce8f1;
}

.panel-info > .panel-heading {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #31708f;
    padding: 15px;
}

.panel-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    line-height: 1.4;
}

.form-group {
    margin-bottom: 20px;
}

.input-group-addon {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #283593;
}

.form-control {
    border: 1px solid #e9ecef;
    padding: 10px;
    height: auto;
}

.form-control:focus {
    border-color: #283593;
    box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
}

.btn {
    padding: 10px 20px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #283593 0%, #1a237e 100%);
    border: none;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn i {
    margin-right: 8px;
}

.item-row {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.item-row:last-child {
    border-bottom: none;
}

#add_item {
    margin-top: -5px;
}

@media (max-width: 768px) {
    .col-md-6, .col-md-4, .col-md-2, .col-md-1 {
        margin-bottom: 15px;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<!-- Agregar jQuery antes de nuestro script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    let rowCounter = 0; // Contador para filas únicas
    
    // Función para actualizar los campos del cliente
    function updateClienteFields(selectedOption) {
        var telefono = selectedOption.data('telefono') || '';
        var correo = selectedOption.data('correo') || '';
        var direccion = selectedOption.data('direccion') || '';
        
        console.log('Datos del cliente:', {
            telefono: telefono,
            correo: correo,
            direccion: direccion
        });
        
        $('#cliente_telefono').val(telefono);
        $('#cliente_correo').val(correo);
        $('#cliente_direccion').val(direccion);
    }

    // Inicializar campos cuando se carga la página
    var initialOption = $('#cliente_id option:selected');
    if (initialOption.val()) {
        updateClienteFields(initialOption);
    }

    // Manejar cambios en el select de cliente
    $('#cliente_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        updateClienteFields(selectedOption);
    });

    // Función para agregar una fila de producto
    function addProductRow(productData, cantidad) {
        rowCounter++;
        var subtotal = productData.precio * cantidad;
        var newRow = `
            <tr data-row-id="${rowCounter}">
                <td>${productData.nombre}</td>
                <td>${cantidad}</td>
                <td>$${parseFloat(productData.precio).toFixed(2)}</td>
                <td>$${subtotal.toFixed(2)}</td>
                <td>
                    <input type="hidden" name="items[${rowCounter}][producto_id]" value="${productData.id}">
                    <input type="hidden" name="items[${rowCounter}][cantidad]" value="${cantidad}">
                    <input type="hidden" name="items[${rowCounter}][precio]" value="${productData.precio}">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="glyphicon glyphicon-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#items-table tbody').append(newRow);
        updateTotal();
    }

    // Manejar la selección de kit
    $('#kit_id').on('change', function() {
        var kitId = $(this).val();
        if(kitId) {
            // Limpiar la tabla actual
            $('#items-table tbody').empty();
            rowCounter = 0; // Reiniciar el contador
            
            // Hacer la petición AJAX para obtener los productos del kit
            $.get('get_kit_products.php', {kit_id: kitId}, function(response) {
                if(response.success) {
                    // Agregar cada producto del kit a la tabla
                    response.products.forEach(function(product) {
                        addProductRow({
                            id: product.id,
                            nombre: product.nombre,
                            precio: product.precio
                        }, product.cantidad);
                    });
                }
            });
        }
    });

    // Manejar la adición de items individuales
    $('#add-item').on('click', function(e) {
        e.preventDefault();
        
        var productId = $('#product_id').val();
        var cantidad = $('#cantidad').val();
        
        if (!productId || !cantidad) {
            alert('Por favor seleccione un producto y especifique la cantidad');
            return;
        }
        
        var selectedOption = $('#product_id option:selected');
        var productData = JSON.parse(selectedOption.attr('data-row'));
        
        if (!productData) {
            alert('Error al obtener datos del producto');
            return;
        }
        
        addProductRow(productData, parseInt(cantidad));
        
        // Limpiar campos
        $('#product_id').val('');
        $('#cantidad').val('1');
    });

    // Manejar la eliminación de items
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateTotal();
    });

    // Actualizar el total
    function updateTotal() {
        var total = 0;
        $('#items-table tbody tr').each(function() {
            var subtotal = parseFloat($(this).find('td:eq(3)').text().replace('$', ''));
            total += subtotal;
        });
        $('#total').text('$' + total.toFixed(2));
    }

    // Manejar el envío del formulario
    $('form').on('submit', function(event) {
        if ($('#items-table tbody tr').length === 0) {
            event.preventDefault();
            alert('Por favor agregue al menos un item a la cotización');
            return;
        }

        // Validar cliente
        if (!$('#cliente_id').val()) {
            event.preventDefault();
            alert('Por favor seleccione un cliente');
            return;
        }

        // Validar fecha
        if (!$('#fecha').val()) {
            event.preventDefault();
            alert('Por favor seleccione una fecha');
            return;
        }
    });
});
</script>

<!-- Modal para agregar cliente -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addCustomerModalLabel">Agregar Nuevo Cliente</h4>
            </div>
            <form method="post" action="add_quote.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="numero">Teléfono</label>
                        <input type="text" class="form-control" name="numero" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" name="direccion" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="add_cliente" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar producto -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addProductModalLabel">Agregar Nuevo Producto</h4>
            </div>
            <form method="post" action="add_quote.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="product-title">Nombre del Producto</label>
                        <input type="text" class="form-control" name="product-title" required>
                    </div>
                    <div class="form-group">
                        <label for="product-description">Descripción</label>
                        <textarea class="form-control" name="product-description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="product-quantity">Cantidad</label>
                        <input type="number" class="form-control" name="product-quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="product-price">Precio</label>
                        <input type="number" class="form-control" name="product-price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="product-category">Categoría</label>
                        <select class="form-control" name="product-category" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo (int)$cat['ID']; ?>">
                                    <?php echo remove_junk($cat['Nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>


                  
    