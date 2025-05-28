<?php
  $page_title = 'Panel de Control - Usuario';
  require_once('includes/load.php');
  page_require_level(3);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Panel de estadísticas principales -->
<div class="row">
  <?php
    $stats = [
      ['color' => 'bg-green',  'icon' => 'glyphicon-shopping-cart', 'value' => count_by_id('venta')['total'],     'label' => 'Ventas'],
      ['color' => 'bg-blue',   'icon' => 'glyphicon-user',          'value' => count_by_id('clientes')['total'],  'label' => 'Clientes']
    ];

    foreach ($stats as $stat):
  ?>
    <div class="col-md-6">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left <?= $stat['color']; ?>">
          <i class="glyphicon <?= $stat['icon']; ?>"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"><?= $stat['value']; ?></h2>
          <p class="text-muted"><?= $stat['label']; ?></p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Últimas Ventas -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Últimas Ventas</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql_ventas_recientes = "SELECT v.*, c.Nombre as ClienteNombre,
                    dv.Cantidad as detalle_cantidad, dv.Precio as detalle_precio,
                    p.Nombre as ProductoNombre, s.Nombre as ServicioNombre
                    FROM venta v
                    LEFT JOIN cliente c ON v.Id_Cliente = c.ID
                    LEFT JOIN detalle_venta dv ON v.ID = dv.Id_Venta
                    LEFT JOIN producto p ON dv.Id_Producto = p.ID
                    LEFT JOIN servicio s ON dv.Id_Servicio = s.ID
                    ORDER BY v.Fecha DESC LIMIT 5";
            $recent_sales = find_by_sql($sql_ventas_recientes);
            
            foreach ($recent_sales as $sale_data): ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="edit_sale.php?id=<?= (int)$sale_data['ID']; ?>">
                    <?= remove_junk($sale_data['ClienteNombre']); ?>
                  </a>
                </td>
                <td>
                  <?php
                    // Obtener valores de forma segura, usando 0 o null si son null o no existen
                    $producto_nombre = $sale_data['ProductoNombre'] ?? null;
                    $detalle_cantidad = $sale_data['detalle_cantidad'] ?? 0;
                    $detalle_precio = $sale_data['detalle_precio'] ?? 0;
                    $servicio_nombre = $sale_data['ServicioNombre'] ?? null;
                    $servicio_costo = $sale_data['ServicioCosto'] ?? 0;

                    $item_nombre = $producto_nombre ?? $servicio_nombre ?? 'N/A';
                    echo remove_junk($item_nombre);
                  ?>
                </td>
                <td>
                  <?php
                    // Sumar el precio del producto * cantidad si existe un producto
                    if (!empty($sale_data['ProductoNombre'])) {
                         echo (int)$detalle_cantidad; // Mostrar cantidad del detalle
                    } elseif (!empty($sale_data['ServicioNombre'])) { // Si es un servicio, la cantidad es 1
                         echo 1;
                    } else {
                         echo 0;
                    }
                  ?>
                </td>
                <td>
                   <?php
                    $precio_unitario = 0;
                    if (!empty($sale_data['ProductoNombre'])) {
                         $precio_unitario = $detalle_precio; // Usar el precio del detalle si es producto
                    } elseif (!empty($sale_data['ServicioNombre'])) { // Usar el costo del servicio si es servicio
                         $precio_unitario = $servicio_costo;
                    }
                    echo '$' . number_format($precio_unitario, 2);
                  ?>
                </td>
                <td>
                  <?php
                    $total = 0;
                    $detalle_cantidad = is_numeric($detalle_cantidad) ? (float) $detalle_cantidad : 0;
                    $detalle_precio = is_numeric($detalle_precio) ? (float) $detalle_precio : 0;
                    $servicio_costo = is_numeric($servicio_costo) ? (float) $servicio_costo : 0;

                    // Sumar el precio del producto * cantidad si existe un producto
                    if (!empty($sale_data['ProductoNombre'])) {
                         $total += $detalle_precio * $detalle_cantidad;
                    } elseif (!empty($sale_data['ServicioNombre'])) { // Sumar el costo del servicio si existe un servicio
                         $total += $servicio_costo; // El costo del servicio es el total para esa línea
                    }

                    echo '$' . number_format($total, 2);
                  ?>
                </td>
                <td><?= $sale_data['Fecha'] ?? ''; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Accesos rápidos -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-link"></span>
          <span>Accesos rápidos</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <a href="add_sale.php" class="btn btn-primary btn-block">
              <i class="glyphicon glyphicon-plus"></i> Nueva Venta
            </a>
          </div>
          <div class="col-md-6">
            <a href="sales.php" class="btn btn-info btn-block">
              <i class="glyphicon glyphicon-list"></i> Ver Ventas
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?> 