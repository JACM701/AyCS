<?php
// Incluir archivo de configuración
require_once(LIB_PATH_INC.DS."config.php");

/**
 * Clase para manejar la conexión y operaciones con la base de datos MySQL
 */
class MySqli_DB {

    private $con;        // Variable para almacenar la conexión
    public $query_id;    // Variable para almacenar el resultado de las consultas

    /**
     * Constructor de la clase - Inicia la conexión a la base de datos
     */
    function __construct() {
      $this->db_connect();
    }

    /**
     * Función para establecer la conexión con la base de datos
     * Intenta conectarse al servidor MySQL y seleccionar la base de datos
     */
    public function db_connect()
    {
      $this->con = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
      if(!$this->con)
             {
               die(" Error en la conexión a la base de datos:". mysqli_connect_error());
             } else {
               $select_db = $this->con->select_db(DB_NAME);
                 if(!$select_db)
                 {
                   die("Error al seleccionar la base de datos". mysqli_connect_error());
                 }
             }
    }

    /**
     * Función para cerrar la conexión a la base de datos
     * Libera los recursos de la conexión
     */
    public function db_disconnect()
    {
      if(isset($this->con))
      {
        mysqli_close($this->con);
        unset($this->con);
      }
    }

    /**
     * Función para ejecutar consultas SQL
     * @param string $sql Consulta SQL a ejecutar
     * @return mixed Resultado de la consulta
     */
    public function query($sql)
       {
          if (trim($sql != "")) {
              $this->query_id = $this->con->query($sql);
          }
          if (!$this->query_id)
            // Solo para modo desarrollo
                  die("Error en esta consulta :<pre> " . $sql ."</pre>");
           // Para modo producción
            //  die("Error en la consulta");

           return $this->query_id;
       }

    /**
     * Funciones auxiliares para manejar resultados de consultas
     */
    
    /**
     * Obtiene una fila como array numérico y asociativo
     */
    public function fetch_array($statement)
    {
      return mysqli_fetch_array($statement);
    }

    /**
     * Obtiene una fila como objeto
     */
    public function fetch_object($statement)
    {
      return mysqli_fetch_object($statement);
    }

    /**
     * Obtiene una fila como array asociativo
     */
    public function fetch_assoc($statement)
    {
      return mysqli_fetch_assoc($statement);
    }

    /**
     * Obtiene el número de filas en un resultado
     */
    public function num_rows($statement)
    {
      return mysqli_num_rows($statement);
    }

    /**
     * Obtiene el ID generado en la última inserción
     */
    public function insert_id()
    {
      return mysqli_insert_id($this->con);
    }

    /**
     * Obtiene el número de filas afectadas en la última operación
     */
    public function affected_rows()
    {
      return mysqli_affected_rows($this->con);
    }

    /**
     * Función para escapar caracteres especiales en una cadena
     * Previene inyección SQL
     * @param string $str Cadena a escapar
     * @return string Cadena escapada
     */
    public function escape($str){
       return $this->con->real_escape_string($str);
     }

    /**
     * Función para procesar resultados en un bucle while
     * @param resource $loop Resultado de una consulta
     * @return array Array con todos los resultados
     */
    public function while_loop($loop){
     global $db;
       $results = array();
       while ($result = $this->fetch_array($loop)) {
          $results[] = $result;
       }
     return $results;
    }
}

// Crear una instancia global de la clase de base de datos
$db = new MySqli_DB();
?>
