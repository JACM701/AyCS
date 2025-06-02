<?php
require_once('includes/load.php');
page_require_level(2);

// Obtener el término de búsqueda
$search = isset($_GET['search']) ? $db->escape($_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$stock = isset($_GET['stock']) ? $_GET['stock'] : '';

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

if($category > 0) {
    $query .= " AND p.Id_Categoria = {$category}";
}

if($stock === 'low') {
    $query .= " AND i.Cantidad <= 5";
} elseif($stock === 'out') {
    $query .= " AND (i.Cantidad = 0 OR i.Cantidad IS NULL)";
}

$query .= " ORDER BY p.ID DESC";
$products = find_by_sql($query);

// Preparar la respuesta HTML
$html = '';
foreach ($products as $product) {
    $stock = isset($product['Cantidad']) ? (int)$product['Cantidad'] : 0;
    $stock_class = $stock <= 5 ? ($stock == 0 ? 'danger' : 'warning') : 'success';
    
    $html .= '<tr>';
    $html .= '<td class="text-center">' . $product['ID'] . '</td>';
    $html .= '<td>' . remove_junk($product['Nombre']) . '</td>';
    $html .= '<td>' . remove_junk($product['category_name'] ?? 'Sin categoría') . '</td>';
    $html .= '<td>' . remove_junk($product['provider_name'] ?? 'Sin proveedor') . '</td>';
    $html .= '<td>' . remove_junk($product['Descripcion'] ?? 'Sin descripción') . '</td>';
    $html .= '<td class="text-center"><span class="label label-' . $stock_class . '">' . $stock . '</span></td>';
    $html .= '<td class="text-center">$' . number_format(isset($product['Precio']) ? $product['Precio'] : 0, 2) . '</td>';
    $html .= '<td class="text-center">
                <div class="btn-group">
                    <a href="edit_product.php?id=' . (int)$product['ID'] . '" class="btn btn-warning btn-xs" title="Editar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=' . (int)$product['ID'] . '" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm(\'¿Está seguro de eliminar este producto?\');">
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                </div>
              </td>';
    $html .= '</tr>';
}

// Si no hay resultados
if(empty($products)) {
    $html = '<tr><td colspan="8" class="text-center">No se encontraron productos</td></tr>';
}

echo $html;
?> 