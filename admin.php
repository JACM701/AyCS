<?php
  // Título de la página
  $page_title = 'Admin página de inicio';
  require_once('includes/load.php');

  // Verificar el nivel de permiso del usuario para ver esta página
  page_require_level(1);

  // Obtener conteos de diferentes entidades
  $c_clientes     = count_by_id('clientes');    // Total de clientes
  $c_productos    = count_by_id('productos');   // Total de productos
  $c_ventas       = count_by_id('venta');       // Total de ventas
  $c_servicios    = count_by_id('servicio');    // Total de servicios

  // Obtener datos para el dashboard - Últimas Ventas
  $sql_ventas_recientes = "SELECT v.*, c.Nombre as ClienteNombre, 
                          p.Nombre as ProductoNombre, s.Nombre as ServicioNombre, s.Costo as ServicioCosto,
                          p.Precio as ProductoPrecio, dv.Cantidad as detalle_cantidad, dv.Precio as detalle_precio
                          FROM venta v
                          LEFT JOIN cliente c ON v.Id_Cliente = c.ID
                          LEFT JOIN detalle_venta dv ON v.ID = dv.Id_Venta -- Unir con detalle_venta
                          LEFT JOIN producto p ON dv.Id_Producto = p.ID -- Unir detalle_venta con producto
                          LEFT JOIN servicio s ON dv.Id_Servicio = s.ID -- Unir detalle_venta con servicio
                          ORDER BY v.Fecha DESC LIMIT 5";
  $recent_sales = find_by_sql($sql_ventas_recientes);

  // Productos recientes
  $sql_productos_recientes = "SELECT p.*, i.Cantidad 
                             FROM producto p
                             LEFT JOIN inventario i ON p.ID = i.Id_Producto 
                             ORDER BY p.ID DESC LIMIT 5";
  $recent_products = find_by_sql($sql_productos_recientes);

  // Productos más vendidos
  $sql_productos_vendidos = "SELECT p.Nombre, COUNT(dv.Id_Producto) as total_vendido
                            FROM producto p
                            LEFT JOIN detalle_venta dv ON p.ID = dv.Id_Producto
                            GROUP BY p.ID
                            ORDER BY total_vendido DESC
                            LIMIT 5";
  $products_sold = find_by_sql($sql_productos_vendidos);

  // Array de estadísticas con los conteos obtenidos y clases de estilo
  $stats = [
    ['color' => 'bg-green',  'icon' => 'glyphicon-user',          'value' => isset($c_clientes['total']) ? $c_clientes['total'] : 0,   'label' => 'Clientes'],
    ['color' => 'bg-red',    'icon' => 'glyphicon-wrench',        'value' => isset($c_servicios['total']) ? $c_servicios['total'] : 0,  'label' => 'Servicios'], // Usando el conteo de servicios
    ['color' => 'bg-blue',   'icon' => 'glyphicon-shopping-cart', 'value' => isset($c_productos['total']) ? $c_productos['total'] : 0,  'label' => 'Productos'], // Usando el conteo de productos
    ['color' => 'bg-yellow', 'icon' => 'glyphicon-usd',           'value' => isset($c_ventas['total']) ? $c_ventas['total'] : 0,     'label' => 'Ventas'] // Usando el conteo de ventas
  ];
?>

<?php include_once('layouts/header.php'); ?>

<!-- Sección de mensajes -->
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Panel de estadísticas principales -->
<div class="row">
  <?php
    foreach ($stats as $stat):
  ?>
    <div class="col-md-3">
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

<!-- Sección de tablas informativas -->
<div class="row">
  <!-- Tabla de Productos más vendidos -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Productos más vendidos</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Total vendido</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as $product): ?>
              <tr>
                <td><?= remove_junk($product['Nombre']); ?></td>
                <td><?= (int)$product['total_vendido']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tabla de Últimas Ventas -->
  <div class="col-md-4">
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
            <?php foreach ($recent_sales as $sale_item): ?>
              <?php
                // Asegurarse de que $sale_item es un array válido. Si no, saltar.
                if (!is_array($sale_item)) {
                    continue;
                }
                // Usar una variable $sale_data para mayor claridad
                $sale_data = $sale_item;
              ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="edit_sale.php?id=<?= (int)($sale_data['Folio'] ?? 0);?>">
                    <?= remove_junk((string)($sale_data['ClienteNombre'] ?? 'Cliente Desconocido')); ?>
                  </a>
                </td>
                <td>
                  <?php 
                    $total = 0;
                    
                    // Obtener valores de forma segura, usando 0 o null si son null o no existen
                    $producto_nombre = $sale_data['ProductoNombre'] ?? null;
                    $detalle_cantidad = $sale_data['detalle_cantidad'] ?? 0;
                    $detalle_precio = $sale_data['detalle_precio'] ?? 0;
                    $servicio_nombre = $sale_data['ServicioNombre'] ?? null;
                    $servicio_costo = $sale_data['ServicioCosto'] ?? 0;

                    // Asegurar que cantidad y precio sean numéricos antes de multiplicar
                    $detalle_cantidad = is_numeric($detalle_cantidad) ? (float) $detalle_cantidad : 0;
                    $detalle_precio = is_numeric($detalle_precio) ? (float) $detalle_precio : 0;
                    $servicio_costo = is_numeric($servicio_costo) ? (float) $servicio_costo : 0;

                    // Sumar el precio del producto * cantidad si existe un producto
                    if ($producto_nombre !== null) {
                         $total += $detalle_precio * $detalle_cantidad;
                    } elseif ($servicio_nombre !== null) { // Sumar el costo del servicio si existe un servicio
                         $total += $servicio_costo;
                    }
                    
                    // Usar sprintf en lugar de number_format para evitar el aviso de deprecación
                    echo '$' . sprintf('%.2f', $total);
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

  <!-- Lista de Productos Recientes -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Productos recientes</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <?php foreach ($recent_products as $product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?= (int)$product['ID'];?>">
              <h4 class="list-group-item-heading">
                <?php if (isset($product['Foto']) && $product['Foto']): ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?= $product['Foto']; ?>" alt="">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                <?php endif; ?>
                <?= remove_junk($product['Nombre']); ?>
                <span class="label label-warning pull-right">
                  $<?= number_format(isset($product['Precio']) ? $product['Precio'] : 0, 2); ?>
                </span>
              </h4>
              <span class="list-group-item-text pull-right">
                Stock: <?= isset($product['Cantidad']) ? (int)$product['Cantidad'] : 0; ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
