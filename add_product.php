<?php
  $page_title = 'Agregar producto';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
 if(isset($_POST['add_product'])){
   $req_fields = array('product-title','product-description','product-quantity','product-cost');
   validate_fields($req_fields);
   if(empty($errors)){
     $p_name  = remove_junk($db->escape($_POST['product-title']));
     $p_desc  = remove_junk($db->escape($_POST['product-description']));
     $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
     $p_cost  = remove_junk($db->escape($_POST['product-cost']));
     $p_photo = '';
     
     if(isset($_FILES['product-photo']) && $_FILES['product-photo']['size'] > 0){
       $p_photo = upload_image($_FILES['product-photo'], 'uploads/products/');
     }

     $query  = "INSERT INTO productos (";
     $query .=" Nombre, Descripcion, Costo, Foto";
     $query .=") VALUES (";
     $query .=" '{$p_name}', '{$p_desc}', '{$p_cost}', '{$p_photo}'";
     $query .=")";

     if($db->query($query)){
       // Insertar en inventario
       $product_id = $db->insert_id();
       $query2 = "INSERT INTO inventario (Id_Producto, Cantidad) VALUES ('{$product_id}', '{$p_qty}')";
       
       if($db->query($query2)){
         $session->msg('s',"Producto agregado exitosamente. ");
         redirect('add_product.php', false);
       } else {
         $session->msg('d',' Error al agregar al inventario.');
         redirect('product.php', false);
       }
     } else {
       $session->msg('d',' Lo siento, registro falló.');
       redirect('product.php', false);
     }

   } else{
     $session->msg("d", $errors);
     redirect('add_product.php',false);
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
  <div class="col-md-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Agregar producto</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_product.php" class="clearfix" enctype="multipart/form-data">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" placeholder="Nombre del producto">
               </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <textarea class="form-control" name="product-description" placeholder="Descripción del producto"></textarea>
               </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                     </span>
                     <input type="number" class="form-control" name="product-quantity" placeholder="Cantidad">
                  </div>
                 </div>
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" class="form-control" name="product-cost" placeholder="Costo">
                     <span class="input-group-addon">.00</span>
                  </div>
                 </div>
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-picture"></i>
                     </span>
                     <input type="file" class="form-control" name="product-photo">
                   </div>
                 </div>
               </div>
              </div>
              <button type="submit" name="add_product" class="btn btn-danger">Agregar producto</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>