<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
  $product_id = (int)$_GET['id'];
  $product = find_by_id('productos', $product_id);
  
  if(!$product){
    $session->msg("d","ID de producto no encontrado.");
    redirect('product.php');
  }
?>
<?php
  // Lógica para eliminar el producto solo si el stock es cero Y no tiene ventas asociadas

  // Obtener la cantidad actual del inventario
  $inventory_item = find_by_id('inventario', $product_id);

  if($inventory_item) {
    $current_stock = (int)$inventory_item['Cantidad'];

    if ($current_stock === 0) {
      // Si el stock es cero, verificar si tiene ventas asociadas
      $sql_sales_check = "SELECT COUNT(*) AS total_ventas FROM venta WHERE Id_Productos = '{$product_id}'";
      $result_sales_check = $db->query($sql_sales_check);
      $sales_count = $db->fetch_assoc($result_sales_check);
      $total_ventas = (int)$sales_count['total_ventas'];

      if ($total_ventas === 0) {
        // Si no tiene ventas asociadas y el stock es cero, proceder con la eliminación completa

        // Eliminar el registro del inventario
        $delete_inventory = delete_by_id('inventario', $product_id); // Usará Id_Producto

        if($delete_inventory){
          // Luego eliminar el producto de la tabla productos
          $delete_product = delete_by_id('productos', $product_id); // Usará Id_Productos

          if($delete_product){
            // Eliminar la imagen si existe
            if($product['Foto'] && $product['Foto'] != 'no_image.jpg'){
              $image_path = 'uploads/products/'.$product['Foto'];
              if(file_exists($image_path)){
                unlink($image_path);
              }
            }
            $session->msg("s","Producto \"" . remove_junk($product['Nombre']) . "\" eliminado completamente.");
            redirect('product.php');
          } else {
            // Si falla la eliminación del producto, intentar revertir la eliminación del inventario si es posible
            $session->msg("d","Error al eliminar el producto de la tabla productos.");
            redirect('product.php');
          }
        } else {
          $session->msg("d","Error al eliminar el registro de inventario.");
          redirect('product.php');
        }

      } else {
        // Si tiene ventas asociadas, no se puede eliminar
        $session->msg("d","No se puede eliminar el producto \"" . remove_junk($product['Nombre']) . "\" porque tiene {$total_ventas} venta(s) asociada(s).");
        redirect('product.php');
      }

    } else {
      // Si el stock no es cero, no se puede eliminar
      $session->msg("w","El producto \"" . remove_junk($product['Nombre']) . "\" tiene stock ({$current_stock}). No se puede eliminar completamente.");
      redirect('product.php');
    }

  } else {
    // Si no existe un registro en inventario, verificamos si tiene ventas asociadas.
    // Si no hay inventario y hay ventas, el registro en venta está huérfano o el inventario se borró manualmente.
    // En este caso, aún no permitimos la eliminación de productos si hay ventas.
    $sql_sales_check = "SELECT COUNT(*) AS total_ventas FROM venta WHERE Id_Productos = '{$product_id}'";
    $result_sales_check = $db->query($sql_sales_check);
    $sales_count = $db->fetch_assoc($result_sales_check);
    $total_ventas = (int)$sales_count['total_ventas'];

    if ($total_ventas === 0) {
        // Si no hay inventario ni ventas, eliminamos el producto y la imagen
        $delete_product = delete_by_id('productos', $product_id); // Usará Id_Productos
        if ($delete_product) {
            // Eliminar la imagen si existe
            if ($product['Foto'] && $product['Foto'] != 'no_image.jpg') {
                $image_path = 'uploads/products/' . $product['Foto'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
             $session->msg("s","Producto \"" . remove_junk($product['Nombre']) . "\" eliminado completamente (no se encontró registro de inventario).");
            redirect('product.php');
        } else {
            $session->msg("d","Error al intentar eliminar completamente el producto (no se encontró registro de inventario).");
            redirect('product.php');
        }
    } else {
       // Si tiene ventas asociadas, no se puede eliminar.
       $session->msg("d","No se puede eliminar el producto \"" . remove_junk($product['Nombre']) . "\" porque tiene {$total_ventas} venta(s) asociada(s).");
       redirect('product.php');
    }
  }
?>
