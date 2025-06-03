<?php
$page_title = 'Editar Cotización';
require_once('includes/load.php');

// Verificar nivel de usuario
page_require_level(1);

// Obtener el ID de la cotización a editar
$quote_id = (int)$_GET['id'];

// Obtener datos de la cotización
$sql = "SELECT c.*, cl.Nombre as cliente_nombre, cl.Numero_Telefono, cl.Direccion, cl.Correo 
        FROM cotizacion c 
        LEFT JOIN cliente cl ON c.Id_Cliente = cl.ID 
        WHERE c.ID = '{$quote_id}'";
$quote = find_by_sql($sql);

if(!$quote) {
    $session->msg("d", "Cotización no encontrada.");
    redirect('quotes.php', false);
}
$quote = $quote[0];

// Obtener items de la cotización
$sql = "SELECT dc.*, 
        CASE 
            WHEN dc.Id_Producto IS NOT NULL THEN p.Nombre 
            WHEN dc.Id_Servicio IS NOT NULL THEN s.Nombre 
        END as item_nombre,
        CASE 
            WHEN dc.Id_Producto IS NOT NULL THEN p.Precio 
            WHEN dc.Id_Servicio IS NOT NULL THEN s.Costo 
        END as precio_base
        FROM detalle_cotizacion dc 
        LEFT JOIN producto p ON dc.Id_Producto = p.ID 
        LEFT JOIN servicio s ON dc.Id_Servicio = s.ID 
        WHERE dc.Id_Cotizacion = '{$quote_id}'";
$items = find_by_sql($sql);

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

// Procesar el formulario cuando se envía
if(isset($_POST['edit_quote'])) {
    $req_fields = array('cliente_id', 'fecha');
    validate_fields($req_fields);

    if(empty($errors)) {
        $cliente_id = (int)$_POST['cliente_id'];
        $fecha = $db->escape($_POST['fecha']);
        $telefono = $db->escape($_POST['cliente_telefono'] ?? '');
        $correo = $db->escape($_POST['cliente_correo'] ?? '');
        $direccion = $db->escape($_POST['cliente_direccion'] ?? '');

        // Iniciar transacción
        $db->query('START TRANSACTION');

        try {
            // Actualizar la cotización
            $sql = "UPDATE cotizacion SET 
                    Id_Cliente = '{$cliente_id}', 
                    Fecha = '{$fecha}'
                    WHERE ID = '{$quote_id}'";
            
            if($db->query($sql)) {
                // Eliminar items existentes
                $sql = "DELETE FROM detalle_cotizacion WHERE Id_Cotizacion = '{$quote_id}'";
                $db->query($sql);
                
                // Procesar los nuevos items
                if(isset($_POST['items']) && is_array($_POST['items'])) {
                    foreach($_POST['items'] as $item) {
                        // Validar que los campos requeridos existan
                        if(!isset($item['id']) || !isset($item['tipo']) || !isset($item['precio'])) {
                            continue; // Saltar items inválidos
                        }

                        $item_id = (int)$item['id'];
                        $tipo = $db->escape($item['tipo']);
                        $precio = (float)$item['precio'];
                        
                        // Construir la consulta según el tipo de item
                        if($tipo === 'producto') {
                            $sql = "INSERT INTO detalle_cotizacion (Id_Cotizacion, Id_Producto, Precio) 
                                   VALUES ('{$quote_id}', '{$item_id}', '{$precio}')";
                        } elseif($tipo === 'servicio') {
                            // Verificar que el servicio existe antes de insertar
                            $check_service = "SELECT ID FROM servicio WHERE ID = '{$item_id}'";
                            $service_exists = $db->query($check_service);
                            
                            if($db->num_rows($service_exists) > 0) {
                                $sql = "INSERT INTO detalle_cotizacion (Id_Cotizacion, Id_Servicio, Precio) 
                                       VALUES ('{$quote_id}', '{$item_id}', '{$precio}')";
                            } else {
                                throw new Exception("El servicio con ID {$item_id} no existe");
                            }
                        } else {
                            continue; // Saltar si el tipo no es válido
                        }
                        
                        if(!$db->query($sql)) {
                            throw new Exception("Error al insertar detalle de item");
                        }
                    }
                }
                
                // Confirmar transacción
                $db->query('COMMIT');
                $session->msg('s', "Cotización actualizada exitosamente.");
                redirect('quotes.php', false);
            } else {
                throw new Exception("Error al actualizar la cotización");
            }
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $db->query('ROLLBACK');
            $session->msg('d', "Error al actualizar la cotización: " . $e->getMessage());
            redirect('edit_quote.php?id='.$quote_id, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_quote.php?id='.$quote_id, false);
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
          <span>Editar Cotización</span>
        </strong>
        <div class="pull-right">
          <a href="quotes.php" class="btn btn-default">Volver</a>
        </div>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_quote.php?id=<?php echo $quote_id; ?>" class="clearfix">
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
                                      data-correo="<?php echo htmlspecialchars($cliente['Correo'] ?? ''); ?>"
                                      <?php echo ($cliente['ID'] == $quote['Id_Cliente']) ? 'selected' : ''; ?>>
                                <?php echo remove_junk($cliente['Nombre']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" id="cliente_telefono" name="cliente_telefono" value="<?php echo htmlspecialchars($quote['Numero_Telefono'] ?? ''); ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Correo</label>
                            <input type="email" class="form-control" id="cliente_correo" name="cliente_correo" value="<?php echo htmlspecialchars($quote['Correo'] ?? ''); ?>">
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" class="form-control" id="cliente_direccion" name="cliente_direccion" value="<?php echo htmlspecialchars($quote['Direccion'] ?? ''); ?>">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo date('Y-m-d', strtotime($quote['Fecha'])); ?>" required>
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
                          <th>Producto/Servicio</th>
                          <th>Cantidad</th>
                          <th>Precio Unitario</th>
                          <th>Subtotal</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group clearfix">
            <button type="submit" name="edit_quote" class="btn btn-primary">Guardar Cambios</button>
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

<script>
$(document).ready(function() {
    // Función para actualizar campos del cliente
    function updateClienteFields(selectedOption) {
        if(selectedOption.length) {
            $('#cliente_telefono').val(selectedOption.data('telefono') || '');
            $('#cliente_correo').val(selectedOption.data('correo') || '');
            $('#cliente_direccion').val(selectedOption.data('direccion') || '');
        } else {
            $('#cliente_telefono').val('');
            $('#cliente_correo').val('');
            $('#cliente_direccion').val('');
        }
    }

    // Manejar cambios en el select de cliente
    $('#cliente_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        updateClienteFields(selectedOption);
    });

    // Inicializar campos del cliente al cargar la página
    var initialSelectedOption = $('#cliente_id option:selected');
    updateClienteFields(initialSelectedOption);

    // Manejar la adición de items
    $('#add-item').click(function() {
        var productId = $('#product_id').val();
        var productData = $('#product_id option:selected').data('row');
        
        if (!productId) {
            alert('Por favor seleccione un producto');
            return;
        }

        var cantidad = parseInt($('#cantidad').val()) || 1;
        var precio = parseFloat(productData.precio);
        var subtotal = cantidad * precio;

        var newRow = `
            <tr>
                <td>${productData.nombre}</td>
                <td>${cantidad}</td>
                <td>$${precio.toFixed(2)}</td>
                <td>$${subtotal.toFixed(2)}</td>
                <td>
                    <input type="hidden" name="items[][id]" value="${productId}">
                    <input type="hidden" name="items[][tipo]" value="producto">
                    <input type="hidden" name="items[][precio]" value="${precio}">
                    <button type="button" class="btn btn-danger btn-xs remove-item">
                        <i class="glyphicon glyphicon-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#items-table tbody').append(newRow);
        
        // Limpiar campos
        $('#product_id').val('');
        $('#cantidad').val('1');
    });

    // Manejar la eliminación de items
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
    });

    // Cargar items existentes
    <?php foreach($items as $item): ?>
    var existingRow = `
        <tr>
            <td>${<?php echo json_encode($item['item_nombre']); ?>}</td>
            <td>1</td>
            <td>$${<?php echo $item['precio_base']; ?>.toFixed(2)}</td>
            <td>$${<?php echo $item['Precio']; ?>.toFixed(2)}</td>
            <td>
                <input type="hidden" name="items[][id]" value="${<?php echo $item['Id_Producto'] ? $item['Id_Producto'] : $item['Id_Servicio']; ?>}">
                <input type="hidden" name="items[][tipo]" value="${<?php echo $item['Id_Producto'] ? "'producto'" : "'servicio'"; ?>}">
                <input type="hidden" name="items[][precio]" value="${<?php echo $item['Precio']; ?>}">
                <button type="button" class="btn btn-danger btn-xs remove-item">
                    <i class="glyphicon glyphicon-trash"></i>
                </button>
            </td>
        </tr>
    `;
    $('#items-table tbody').append(existingRow);
    <?php endforeach; ?>
});
</script>

<?php include_once('layouts/footer.php'); ?> 