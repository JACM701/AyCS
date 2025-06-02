<?php
  // Título de la página
  $page_title = 'Inicio';
  require_once('includes/load.php');

  // Verificar que el usuario tenga el nivel requerido (3 = usuario normal)
  page_require_level(3);

  // Obtener conteos de diferentes entidades (usando nombres de tabla singulares)
  $c_clientes     = count_by_id('cliente');    // Total de clientes
  $c_productos    = count_by_id('producto');   // Total de productos
  $c_ventas       = count_by_id('venta');       // Total de ventas
  $c_servicios    = count_by_id('servicio');    // Total de servicios


  // Obtener datos para el dashboard - Últimas Ventas
  // Consulta adaptada para tu base de datos actual (con ID en tabla venta y cliente, y tabla producto singular)
  $sql_ventas_recientes = "SELECT
                              v.ID as VentaID,
                              v.Fecha,
                              c.Nombre as ClienteNombre, -- Nombre del cliente
                              dv.Precio as PrecioUnitario, -- Precio unitario del item en la venta (desde detalle_venta)
                              dv.Cantidad, -- Cantidad del item vendido (desde detalle_venta)
                              COALESCE(p.Nombre, s.Nombre) as ItemNombre -- Nombre del producto o servicio
                          FROM venta v
                          LEFT JOIN cliente c ON v.Id_Cliente = c.ID -- Unir con la tabla cliente usando c.ID
                          LEFT JOIN detalle_venta dv ON v.ID = dv.Id_Venta -- Unir con la tabla detalle_venta usando v.ID
                          LEFT JOIN producto p ON dv.Id_Producto = p.ID -- Unir con producto singular usando ID
                          LEFT JOIN servicio s ON dv.Id_Servicio = s.ID -- Unir con la tabla servicio usando s.ID
                          ORDER BY v.Fecha DESC
                          LIMIT 5"; // Limitar a las últimas 5 ventas
  $recent_sales = find_by_sql($sql_ventas_recientes);

  // Productos recientes
  // Consulta adaptada para tu base de datos actual (tabla producto singular)
  $sql_productos_recientes = "SELECT
                                  p.ID as ProductoID,
                                  p.Nombre,
                                  p.Precio, -- Usamos Precio de la tabla producto
                                  p.Imagen, -- Usamos Imagen de la tabla producto
                                  i.Cantidad -- Usamos Cantidad de la tabla inventario
                              FROM producto p -- Nombre de tabla producto (singular)
                              LEFT JOIN inventario i ON p.ID = i.Id_Producto -- Unir con la tabla inventario
                              ORDER BY p.ID DESC -- Ordenar por ID de producto
                              LIMIT 5"; // Limitar a los últimos 5 productos
  $recent_products = find_by_sql($sql_productos_recientes);

  // Productos más vendidos
  $sql_productos_vendidos = "SELECT p.Nombre, COUNT(dv.Id_Producto) as total_vendido
                            FROM producto p -- Corregido a tabla producto (singular)
                            LEFT JOIN detalle_venta dv ON p.ID = dv.Id_Producto -- Unir con detalle_venta usando Id_Producto
                            GROUP BY p.ID -- Agrupar por p.ID
                            ORDER BY total_vendido DESC
                             LIMIT 5";
  $products_sold = find_by_sql($sql_productos_vendidos);

?>

<?php include_once('layouts/header.php'); ?>

<!-- Sección de mensajes -->
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
</div>

<!-- Panel de estadísticas principales -->
<div class="row">
  <?php
    // Array de estadísticas con los conteos obtenidos y clases de estilo
    $stats = [
      // ['color' => 'bg-green',  'icon' => 'glyphicon-user',          'value' => $c_clientes['total'] ?? 0,   'label' => 'Clientes'], // Usando el conteo de clientes
      ['color' => 'bg-red',    'icon' => 'glyphicon-wrench',        'value' => $c_servicios['total'] ?? 0,  'label' => 'Servicios'], // Usando el conteo de servicios
      ['color' => 'bg-blue',   'icon' => 'glyphicon-shopping-cart', 'value' => $c_productos['total'] ?? 0,  'label' => 'Productos'], // Usando el conteo de productos
      ['color' => 'bg-yellow', 'icon' => 'glyphicon-usd',           'value' => $c_ventas['total'] ?? 0,     'label' => 'Ventas'] // Usando el conteo de ventas
    ];

    // Mostrar las estadísticas en paneles
    foreach ($stats as $stat):
  ?>
    <div class="col-md-3">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left <?php echo $stat['color']; ?>">
          <i class="glyphicon <?php echo $stat['icon']; ?>"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"><?php echo $stat['value']; ?></h2>
          <p class="text-muted"><?php echo $stat['label']; ?></p>
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
                <td><?php echo remove_junk($product['Nombre']); ?></td>
                <td><?php echo (int)$product['total_vendido']; ?></td>
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
              <th>Item</th> <!-- Columna para Producto o Servicio -->
              <th>Cantidad</th> <!-- Nueva columna para cantidad -->
              <th>Precio Unitario</th> <!-- Mostrar Precio Unitario de detalle_venta -->
              <th>Total Línea</th> <!-- Mostrar Total de la línea (Precio*Cantidad) -->
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as $sale): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td>
                  <!-- Enlace a la edición de venta, usando el ID de la venta -->
                  <a href="edit_sale.php?id=<?php echo (int)$sale['VentaID'];?>">
                    <?php echo remove_junk($sale['ClienteNombre']); ?>
                  </a>
                </td>
                <td><?php echo remove_junk($sale['ItemNombre']); ?></td> <!-- Nombre del producto o servicio -->
                <td><?php echo (int)$sale['Cantidad']; ?></td> <!-- Cantidad vendida -->
                <td>$<?php echo number_format($sale['PrecioUnitario'], 2); ?></td> <!-- Precio Unitario -->
                <td>$<?php echo number_format($sale['PrecioUnitario'] * $sale['Cantidad'], 2); ?></td> <!-- Total de la línea -->
                <td><?php echo read_date($sale['Fecha']); ?></td>
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
            <!-- Enlace a la edición de producto, usando el ID del producto -->
            <a class="list-group-item clearfix" href="edit_product.php?id=<?php echo (int)$product['ProductoID'];?>">
              <h4 class="list-group-item-heading">
                <?php if ($product['Imagen']): ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['Imagen']; ?>" alt="">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                <?php endif; ?>
                <?php echo remove_junk($product['Nombre']); ?>
                <span class="label label-warning pull-right">
                  $<?php echo number_format($product['Precio'], 2); ?>
                </span>
              </h4>
              <span class="list-group-item-text pull-right">
                Stock: <?php echo (int)$product['Cantidad']; ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
