<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
  $product = find_by_id('productos',(int)$_GET['id']);
  if(!$product){
    $session->msg("d","ID de producto no encontrado");
    redirect('product.php');
  }
?>
<?php
  // Primero eliminar el registro del inventario
  $delete_inventory = delete_by_id('inventario',(int)$product['Id_Productos'], 'Id_Producto');
  
  if($delete_inventory){
    // Luego eliminar el producto
    $delete_id = delete_by_id('productos',(int)$product['Id_Productos']);
    if($delete_id){
      // Eliminar la imagen si existe
      if($product['Foto'] && $product['Foto'] != 'no_image.jpg'){
        $image_path = 'uploads/products/'.$product['Foto'];
        if(file_exists($image_path)){
          unlink($image_path);
        }
      }
      $session->msg("s","Producto eliminado exitosamente");
      redirect('product.php');
    } else {
      $session->msg("d","Error al eliminar el producto");
      redirect('product.php');
    }
  } else {
    $session->msg("d","Error al eliminar el inventario");
    redirect('product.php');
  }
?>
