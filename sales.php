<?php
  // Título de la página
  $page_title = 'Lista de ventas';
  // Cargar archivos necesarios
  require_once('includes/load.php');
  // Verificar el nivel de permiso del usuario (nivel 3 requerido)
  page_require_level(3);
?>
<?php
// Obtener todas las ventas con información relacionada
$sql = "SELECT v.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido, 
        p.Nombre as ProductoNombre, s.Nombre as ServicioNombre, s.Costo as ServicioCosto,
        p.Precio as ProductoPrecio
        FROM venta v
        LEFT JOIN clientes c ON v.Id_Cliente = c.Id_Cliente
        LEFT JOIN producto p ON v.Id_Productos = p.ID
        LEFT JOIN servicio s ON v.Id_Servicio = s.ID
        ORDER BY v.Fecha DESC";
$sales = find_by_sql($sql);
?>
<?php include_once('layouts/header.php'); ?>
<!-- Sección de mensajes -->
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<!-- Contenedor principal de la tabla de ventas -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <!-- Encabezado del panel con título y botón de agregar -->
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Todas las ventas</span>
        </strong>
        <div class="pull-right">
          <a href="add_sale.php" class="btn btn-primary">Agregar venta</a>
        </div>
      </div>
      <!-- Cuerpo del panel con la tabla de ventas -->
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <!-- Encabezados de la tabla -->
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Cliente</th>
              <th>Producto</th>
              <th>Servicio</th>
              <th class="text-center" style="width: 15%;">Precio Unitario</th>
              <th class="text-center" style="width: 15%;">Fecha</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <!-- Cuerpo de la tabla con los datos de ventas -->
          <tbody>
             <?php foreach ($sales as $sale):?>
             <tr>
               <!-- Número de registro -->
               <td class="text-center"><?php echo count_id();?></td>
               <!-- Nombre del cliente -->
               <td><?php echo remove_junk($sale['ClienteNombre'] . ' ' . $sale['ClienteApellido']); ?></td>
               <!-- Nombre del producto -->
               <td><?php echo remove_junk($sale['ProductoNombre']); ?></td>
               <!-- Nombre del servicio -->
               <td><?php echo remove_junk($sale['ServicioNombre']); ?></td>
               <!-- Precio total -->
               <td class="text-center">
                 <?php 
                   $precio = 0;
                   if (isset($sale['ProductoNombre'])) {
                     $precio = $sale['ProductoPrecio'];
                   } elseif (!empty($sale['ServicioCosto'])) {
                     $precio = $sale['ServicioCosto'];
                   }
                   echo remove_junk(number_format($precio, 2));
                 ?>
               </td>
               <!-- Fecha de la venta -->
               <td class="text-center"><?php echo read_date($sale['Fecha']); ?></td>
               <!-- Botones de acción (editar y eliminar) -->
               <td class="text-center">
                  <div class="btn-group">
                     <!-- Botón para editar venta -->
                     <a href="edit_sale.php?id=<?php echo (int)$sale['ID'];?>" class="btn btn-warning btn-xs"  title="Editar" data-toggle="tooltip">
                       <span class="glyphicon glyphicon-edit"></span>
                     </a>
                     <!-- Botón para eliminar venta -->
                     <a href="delete_sale.php?id=<?php echo (int)$sale['ID'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                       <span class="glyphicon glyphicon-trash"></span>
                     </a>
                  </div>
               </td>
             </tr>
             <?php endforeach;?>
           </tbody>
         </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>