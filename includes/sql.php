<?php
if (!defined('SITE_ROOT')) {
    die('Acceso directo no permitido');
}

require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Función para encontrar todas las filas de una tabla por nombre
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}
/*--------------------------------------------------------------*/
/* Función para ejecutar consultas SQL
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
 return $result_set;
}
/*--------------------------------------------------------------*/
/* Función para encontrar datos de una tabla por ID
/*--------------------------------------------------------------*/
function find_by_id($table,$id)
{
  global $db;
  $id = (int)$id;
    if(tableExists($table)){
          // Definir el nombre de la columna ID según la tabla en la nueva BD 'naycs'
          $id_column = 'ID'; // Por defecto es 'ID' en el nuevo esquema
          switch($table) {
            // Mantener casos especiales si hay tablas con IDs que no sean 'ID' en naycs.sql
            // case 'users':
            //   $id_column = 'id'; // si users.id no cambió a users.ID
            //   break;
            // ... otros casos si aplican
            case 'users': // user table still uses 'id'
              $id_column = 'id';
              break;
            case 'user_groups': // user_groups table still uses 'id'
              $id_column = 'id';
              break;
            case 'media': // media table still uses 'id'
              $id_column = 'id';
              break;
            case 'producto': // Agregar caso para la tabla producto singular
              $id_column = 'ID'; // Corregido a 'ID' según naycs.sql
              break;
            case 'usuario': // Agregar caso para la tabla usuario
              $id_column = 'ID'; // Usar 'ID' según naycs.sql
              break;
            case 'categoria_producto': // Usar 'ID' para categoria_producto
              $id_column = 'ID';
              break;
          }
          // La tabla es la misma que se pasa, ya que find_all no singulariza
          $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE {$id_column}='{$db->escape($id)}' LIMIT 1");
          if($result = $db->fetch_assoc($sql))
            return $result;
          else
            return null;
     }
}
/*--------------------------------------------------------------*/
/* Función para eliminar datos de una tabla por ID
/*--------------------------------------------------------------*/
function delete_by_id($table,$id)
{
  global $db;
  if(tableExists($table))
   {
    // Definir el nombre de la columna ID según la tabla en la nueva BD 'naycs'
    $id_column = 'ID'; // Por defecto es 'ID' en el nuevo esquema
    switch($table) {
      // Mantener casos especiales si hay tablas con IDs que no sean 'ID' en naycs.sql
      // case 'users':
      //   $id_column = 'id';
      //   break;
      // ... otros casos si aplican
      case 'users': // user table still uses 'id'
        $id_column = 'id';
        break;
      case 'user_groups': // user_groups table still uses 'id'
        $id_column = 'id';
        break;
      case 'media': // media table still uses 'id'
        $id_column = 'id';
        break;
      case 'producto': // Agregar caso para la tabla producto singular
        $id_column = 'Id_Productos';
        break;
      case 'categoria_producto': // Usar 'ID' para categoria_producto
        $id_column = 'ID';
        break;
    }

    $sql = "DELETE FROM ".$db->escape($table);
    $sql .= " WHERE {$id_column}=". $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
   }
}
/*--------------------------------------------------------------*/
/* Función para contar IDs por nombre de tabla
/*--------------------------------------------------------------*/
function count_by_id($table){
  global $db;
  if(tableExists($table))
  {
    // Definir el nombre de la columna ID según la tabla en la nueva BD 'naycs'
    $id_column = 'ID'; // Por defecto es 'ID' en el nuevo esquema
    switch($table) {
      // Mantener casos especiales si hay tablas con IDs que no sean 'ID' en naycs.sql
      // case 'users':
      //   $id_column = 'id';
      //   break;
      // ... otros casos si aplican
      case 'users': // user table still uses 'id'
        $id_column = 'id';
        break;
      case 'user_groups': // user_groups table still uses 'id'
        $id_column = 'id';
        break;
      case 'media': // media table still uses 'id'
        $id_column = 'id';
        break;
      case 'categoria_producto': // Usar 'ID' para categoria_producto
        $id_column = 'ID';
        break;
    }

    $sql = "SELECT COUNT({$id_column}) AS total FROM ".$db->escape($table);
    $result = $db->query($sql);
    return($db->fetch_assoc($result));
  }
}
/*--------------------------------------------------------------*/
/* Determinar si existe una tabla en la base de datos
/*--------------------------------------------------------------*/
function tableExists($table){
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Iniciar sesión con los datos proporcionados en $_POST,
 /* provenientes del formulario de inicio de sesión.
/*--------------------------------------------------------------*/
  function authenticate($username='', $password='') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql  = sprintf("SELECT ID,Usuario,Password,Id_Rol FROM usuario WHERE Usuario ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if($db->num_rows($result)){
      $user = $db->fetch_assoc($result);
      $password_request = sha1($password);
      if($password_request === $user['Password'] ){
        return $user['ID'];
      }
    }
   return false;
  }
  /*--------------------------------------------------------------*/
  /* Iniciar sesión con los datos proporcionados en $_POST,
  /* provenientes del formulario login_v2.php.
  /* Si usas este método, elimina la función authenticate.
 /*--------------------------------------------------------------*/
   function authenticate_v2($username, $password) {
     global $db;
     $username = $db->escape($username);
     // Si la contraseña en la BD NO está hasheada, usar la contraseña sin hashear
     $password = $db->escape($password);
     $sql = "SELECT ID, Usuario, Id_Rol FROM usuario WHERE Usuario = '{$username}' AND Contrasena = '{$password}' LIMIT 1";
     $result = $db->query($sql);
     if($db->num_rows($result) == 1) {
         return $db->fetch_assoc($result);
     }
     return false;
   }


  /*--------------------------------------------------------------*/
  /* Encontrar usuario actual por ID de sesión
  /*--------------------------------------------------------------*/
  function current_user() {
      static $current_user;
      global $db;
      if(!$current_user) {
          if(isset($_SESSION['user_id'])) {
              $user_id = intval($_SESSION['user_id']);
              // Buscar usuario en la tabla 'usuario' usando la columna 'ID'
              $current_user = find_by_id('usuario', $user_id);
          }
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Encontrar todos los usuarios
  /* Uniendo tabla de usuarios y roles
  /*--------------------------------------------------------------*/
  function find_all_user(){
      global $db;
      $results = array();
      $sql = "SELECT u.ID,u.Nombre,u.Usuario,u.Id_Rol,u.Estado,u.Ultimo_Acceso,";
      $sql .="r.Nombre as Nombre_Rol ";
      $sql .="FROM usuario u ";
      $sql .="LEFT JOIN rol r ";
      $sql .="ON r.ID=u.Id_Rol ORDER BY u.Nombre ASC";
      $result = find_by_sql($sql);
      return $result;
  }
  /*--------------------------------------------------------------*/
  /* Función para actualizar el último inicio de sesión de un usuario
  /*--------------------------------------------------------------*/

 function updateLastLogIn($user_id)
	{
		global $db;
    $date = make_date();
    $sql = "UPDATE usuario SET Ultimo_Acceso='{$date}' WHERE ID ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
	}

  /*--------------------------------------------------------------*/
  /* Encontrar nivel de grupo
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT ID FROM rol WHERE ID = '{$db->escape($level)}' LIMIT 1";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Función para verificar qué nivel de usuario tiene acceso a la página
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     
     //si el usuario no ha iniciado sesión
     if (!$session->isUserLoggedIn(true)):
            $session->msg('d','Por favor Iniciar sesión...');
            redirect('index.php', false);
     endif;
     
     //verificando nivel de usuario y nivel requerido
     if(isset($current_user['Id_Rol']) && $current_user['Id_Rol'] <= (int)$require_level):
            return true;
     else:
            $session->msg("d", "¡Lo siento!  no tienes permiso para ver la página.");
            redirect('home.php', false);
     endif;
   }
   /*--------------------------------------------------------------*/
   /* Función para encontrar todos los nombres de productos
   /* JOIN con tablas de categorías y medios
   /*--------------------------------------------------------------*/
  function join_product_table(){
     global $db;
     $sql  =" SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.media_id,p.date,c.name";
    $sql  .=" AS categorie,m.file_name AS image";
    $sql  .=" FROM products p";
    $sql  .=" LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql  .=" LEFT JOIN media m ON m.id = p.media_id";
    $sql  .=" ORDER BY p.id ASC";
    return find_by_sql($sql);

   }
  /*--------------------------------------------------------------*/
  /* Función para encontrar todos los nombres de productos
  /* Solicitud proveniente de ajax.php para sugerencia automática
  /*--------------------------------------------------------------*/

   function find_product_by_title($product_name){
     global $db;
     $p_name = remove_junk($db->escape($product_name));
     $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
     $result = find_by_sql($sql);
     return $result;
   }

  /*--------------------------------------------------------------*/
  /* Función para encontrar toda la información del producto por título
  /* Solicitud proveniente de ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
  }

  /*--------------------------------------------------------------*/
  /* Función para actualizar cantidad de producto
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = (int)$p_id;
    $sql = "UPDATE products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Función para mostrar productos recientemente agregados
  /*--------------------------------------------------------------*/
 function find_recent_product_added($limit){
   global $db;
   $sql   = " SELECT p.id,p.name,p.sale_price,p.media_id,c.name AS categorie,";
   $sql  .= "m.file_name AS image FROM products p";
   $sql  .= " LEFT JOIN categories c ON c.id = p.categorie_id";
   $sql  .= " LEFT JOIN media m ON m.id = p.media_id";
   $sql  .= " ORDER BY p.id DESC LIMIT ".$db->escape((int)$limit);
   return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
 /* Función para encontrar producto con mayor venta
 /*--------------------------------------------------------------*/
 function find_higest_saleing_product($limit){
   global $db;
   $sql  = "SELECT p.name, COUNT(s.product_id) AS totalSold, SUM(s.qty) AS totalQty";
   $sql .= " FROM sales s";
   $sql .= " LEFT JOIN products p ON p.id = s.product_id ";
   $sql .= " GROUP BY s.product_id";
   $sql .= " ORDER BY SUM(s.qty) DESC LIMIT ".$db->escape((int)$limit);
   return $db->query($sql);
 }
 /*--------------------------------------------------------------*/
 /* Función para encontrar todas las ventas
 /*--------------------------------------------------------------*/
 function find_all_sale(){
   global $db;
   $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name";
   $sql .= " FROM sales s";
   $sql .= " LEFT JOIN products p ON s.product_id = p.id";
   $sql .= " ORDER BY s.date DESC";
   return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
 /* Función para mostrar ventas recientes
 /*--------------------------------------------------------------*/
function find_recent_sale_added($limit){
  global $db;
  $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN products p ON s.product_id = p.id";
  $sql .= " ORDER BY s.date DESC LIMIT ".$db->escape((int)$limit);
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Función para generar informe de ventas entre dos fechas
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date,$end_date){
  global $db;
  $start_date  = date("Y-m-d", strtotime($start_date));
  $end_date    = date("Y-m-d", strtotime($end_date));
  $sql  = "SELECT s.date, p.name,p.sale_price,p.buy_price,";
  $sql .= "COUNT(s.product_id) AS total_records,";
  $sql .= "SUM(s.qty) AS total_sales,";
  $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price,";
  $sql .= "SUM(p.buy_price * s.qty) AS total_buying_price ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN products p ON s.product_id = p.id";
  $sql .= " WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
  $sql .= " GROUP BY DATE(s.date),p.name";
  $sql .= " ORDER BY DATE(s.date) DESC";
  return $db->query($sql);
}
/*--------------------------------------------------------------*/
/* Función para generar informe de ventas diarias
/*--------------------------------------------------------------*/
function  dailySales($year,$month){
  global $db;
  $sql  = "SELECT s.qty,";
  $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
  $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN products p ON s.product_id = p.id";
  $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
  $sql .= " GROUP BY DATE_FORMAT( s.date,  '%e' ),s.product_id";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Función para generar informe de ventas mensuales
/*--------------------------------------------------------------*/
function  monthlySales($year){
  global $db;
  $sql  = "SELECT s.qty,";
  $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
  $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN products p ON s.product_id = p.id";
  $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
  $sql .= " GROUP BY DATE_FORMAT( s.date,  '%c' ),s.product_id";
  $sql .= " ORDER BY date_format(s.date, '%c' ) ASC";
  return find_by_sql($sql);
}

?>
