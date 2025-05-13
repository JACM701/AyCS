<?php
// Array para almacenar errores
$errors = array();

/**
 * Función para escapar caracteres especiales en una cadena
 * Previene inyección SQL
 * @param string $str Cadena a escapar
 * @return string Cadena escapada
 */
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}

/**
 * Función para eliminar caracteres HTML y formatear texto
 * @param string $str Cadena a limpiar
 * @return string Cadena limpia
 */
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}

/**
 * Función para capitalizar la primera letra de una cadena
 * @param string $str Cadena a formatear
 * @return string Cadena con primera letra mayúscula
 */
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}

/**
 * Función para validar campos del formulario
 * Verifica que los campos no estén vacíos
 * @param array $var Array con nombres de campos a validar
 * @return string|null Mensaje de error o null si todo está correcto
 */
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors = $field ." No puede estar en blanco.";
      return $errors;
    }
  }
}

/**
 * Función para mostrar mensajes de sesión
 * Genera alertas HTML con diferentes estilos
 * @param array $msg Array con mensajes a mostrar
 * @return string HTML con los mensajes formateados
 */
function display_msg($msg =''){
   $output = array();
   if(!empty($msg) && is_array($msg)) {
      foreach ($msg as $key => $value) {
         $output[] = "<div class=\"alert alert-{$key}\">";
         $output[] = "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
         $output[] = remove_junk(first_character($value));
         $output[] = "</div>";
      }
      return implode('', $output);
   } else {
     return "";
   }
}

/**
 * Función para redireccionar a otra página
 * @param string $url URL de destino
 * @param bool $permanent Si es una redirección permanente
 */
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }
    exit();
}

/**
 * Función para calcular precios totales y ganancias
 * @param array $totals Array con precios de venta y compra
 * @return array Array con total de ventas y ganancias
 */
function total_price($totals){
   $sum = 0;
   $sub = 0;
   foreach($totals as $total ){
     $sum += $total['total_saleing_price'];
     $sub += $total['total_buying_price'];
     $profit = $sum - $sub;
   }
   return array($sum,$profit);
}

/**
 * Función para formatear fecha y hora en formato legible
 * @param string $str Fecha a formatear
 * @return string|null Fecha formateada o null si no hay fecha
 */
function read_date($str){
     if($str)
      return date('d/m/Y g:i:s a', strtotime($str));
     else
      return null;
}

/**
 * Función para generar fecha y hora actual
 * @return string Fecha y hora actual formateada
 */
function make_date(){
  return strftime("%Y-%m-%d %H:%M:%S", time());
}

/**
 * Función para generar contador incremental
 * @return int Número incremental
 */
function count_id(){
  static $count = 1;
  return $count++;
}

/**
 * Función para generar cadena aleatoria
 * @param int $length Longitud de la cadena
 * @return string Cadena aleatoria
 */
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha))];
  return $str;
}

function upload_image($file, $target_dir) {
  if(!is_dir($target_dir)){
    mkdir($target_dir, 0777, true);
  }
  
  $target_file = $target_dir . basename($file["name"]);
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  
  // Verificar si es una imagen real
  $check = getimagesize($file["tmp_name"]);
  if($check === false) {
    return '';
  }
  
  // Verificar el tamaño del archivo (5MB máximo)
  if ($file["size"] > 5000000) {
    return '';
  }
  
  // Permitir ciertos formatos
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    return '';
  }
  
  // Generar nombre único
  $new_filename = uniqid() . '.' . $imageFileType;
  $target_file = $target_dir . $new_filename;
  
  if (move_uploaded_file($file["tmp_name"], $target_file)) {
    return $new_filename;
  } else {
    return '';
  }
}

?>
