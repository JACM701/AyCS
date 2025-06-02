<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  // Obtener todas las categorías para el filtro
  $all_categories = find_all('categoria_producto');
  
  // Obtener parámetros de búsqueda y filtros
  $search = isset($_GET['search']) ? $db->escape($_GET['search']) : '';
  $category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
  $stock_filter = isset($_GET['stock']) ? $_GET['stock'] : '';
  
  // Construir la consulta base
  $query = "SELECT p.ID, p.Nombre, p.Descripcion, p.Precio, 
            i.Cantidad, cp.Nombre as category_name, 
            pr.Nombre as provider_name 
            FROM producto p 
            LEFT JOIN inventario i ON p.ID = i.Id_Producto 
            LEFT JOIN categoria_producto cp ON p.Id_Categoria = cp.ID 
            LEFT JOIN proveedor pr ON p.Id_Proveedor = pr.ID 
            WHERE 1=1";
  
  // Agregar condiciones de búsqueda y filtros
  if(!empty($search)) {
    $query .= " AND (p.Nombre LIKE '%{$search}%' OR p.Descripcion LIKE '%{$search}%')";
  }
  
  if($category_filter > 0) {
    $query .= " AND p.Id_Categoria = {$category_filter}";
  }
  
  if($stock_filter === 'low') {
    $query .= " AND i.Cantidad <= 5";
  } elseif($stock_filter === 'out') {
    $query .= " AND (i.Cantidad = 0 OR i.Cantidad IS NULL)";
  }
  
  $query .= " ORDER BY p.ID DESC";
            
  $products = find_by_sql($query);
?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
         <div class="pull-right">
           <a href="add_product.php" class="btn btn-primary">Agregar producto</a>
         </div>
        </div>
        <div class="panel-body">
          <!-- Formulario de búsqueda y filtros -->
          <form method="get" action="product.php" class="form-inline" style="margin-bottom: 20px;" id="searchForm">
            <div class="form-group">
              <input type="text" class="form-control" name="search" id="searchInput" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
              <select class="form-control" name="category" id="categoryFilter">
                <option value="0">Todas las categorías</option>
                <?php foreach ($all_categories as $cat): ?>
                  <option value="<?php echo (int)$cat['ID']; ?>" <?php echo $category_filter === (int)$cat['ID'] ? 'selected' : ''; ?>>
                    <?php echo remove_junk($cat['Nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="stock" id="stockFilter">
                <option value="">Todo el stock</option>
                <option value="low" <?php echo $stock_filter === 'low' ? 'selected' : ''; ?>>Stock bajo (≤5)</option>
                <option value="out" <?php echo $stock_filter === 'out' ? 'selected' : ''; ?>>Sin stock</option>
              </select>
            </div>
            <button type="submit" class="btn btn-info">Filtrar</button>
            <?php if(!empty($search) || $category_filter > 0 || !empty($stock_filter)): ?>
              <a href="product.php" class="btn btn-default">Limpiar filtros</a>
            <?php endif; ?>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th> Nombre </th>
                  <th> Categoría </th>
                  <th> Proveedor </th>
                  <th> Descripción </th>
                  <th class="text-center" style="width: 10%;"> Stock </th>
                  <th class="text-center" style="width: 10%;"> Precio </th>
                  <th class="text-center" style="width: 100px;"> Acciones </th>
                </tr>
              </thead>
              <tbody id="productsTableBody">
                <?php foreach ($products as $product):?>
                <tr>
                  <td class="text-center"><?php echo $product['ID'];?></td>
                  <td> <?php echo remove_junk($product['Nombre']); ?></td>
                  <td> <?php echo remove_junk($product['category_name'] ?? 'Sin categoría'); ?></td>
                  <td> <?php echo remove_junk($product['provider_name'] ?? 'Sin proveedor'); ?></td>
                  <td> <?php echo remove_junk($product['Descripcion'] ?? 'Sin descripción'); ?></td>
                  <td class="text-center"> 
                    <?php 
                      $stock = isset($product['Cantidad']) ? (int)$product['Cantidad'] : 0;
                      $stock_class = $stock <= 5 ? ($stock == 0 ? 'danger' : 'warning') : 'success';
                      echo "<span class='label label-{$stock_class}'>{$stock}</span>";
                    ?>
                  </td>
                  <td class="text-center"> $<?php echo number_format(isset($product['Precio']) ? $product['Precio'] : 0, 2); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_product.php?id=<?php echo (int)$product['ID'];?>" class="btn btn-warning btn-xs" title="Editar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_product.php?id=<?php echo (int)$product['ID'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Está seguro de eliminar este producto?');">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>
                </tr>
               <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<style>
.table-responsive {
  overflow-x: auto;
  margin-bottom: 20px;
}

.img-avatar {
  width: 50px;
  height: 50px;
  object-fit: cover;
}

.btn-group .btn {
  margin: 0 2px;
}

.table > tbody > tr > td {
  vertical-align: middle;
}

.form-inline .form-group {
  margin-right: 10px;
}

.label {
  font-size: 12px;
  padding: 5px 10px;
}

#searchInput {
  min-width: 200px;
}

.loading {
  position: relative;
}

.loading:after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.8) url('libs/images/loading.gif') center no-repeat;
  background-size: 50px;
}
</style>

<script>
$(document).ready(function() {
    let searchTimeout;
    const searchInput = $('#searchInput');
    const categoryFilter = $('#categoryFilter');
    const stockFilter = $('#stockFilter');
    const productsTableBody = $('#productsTableBody');
    
    // Función para realizar la búsqueda
    function performSearch() {
        const search = searchInput.val();
        const category = categoryFilter.val();
        const stock = stockFilter.val();
        
        // Mostrar indicador de carga
        productsTableBody.addClass('loading');
        
        // Realizar la petición AJAX
        $.get('search_products.php', {
            search: search,
            category: category,
            stock: stock
        })
        .done(function(response) {
            productsTableBody.html(response);
        })
        .fail(function() {
            productsTableBody.html('<tr><td colspan="8" class="text-center">Error al cargar los productos</td></tr>');
        })
        .always(function() {
            // Ocultar indicador de carga
            productsTableBody.removeClass('loading');
        });
    }
    
    // Evento de escritura en el campo de búsqueda
    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300); // Esperar 300ms después de que el usuario deje de escribir
    });
    
    // Evento de cambio en los filtros
    categoryFilter.on('change', performSearch);
    stockFilter.on('change', performSearch);
    
    // Prevenir el envío del formulario
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
});
</script>

<?php include_once('layouts/footer.php'); ?>
