<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería mysqli
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatosArchivo {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatosArchivo();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
    private function __construct(){

        $this->dbh = new mysqli(DB_SERVER,DB_USER,DB_PASSWD,DATABASE);
         
      if ( $this->dbh->connect_error){
         die(" Error en la conexión ".$this->dbh->connect_errno);
        } 

    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh->close();
            self::$modelo = null; // Borro el objeto.
        }
    }

    //Añadir /////////////////////////////////////////////////////////////////////////////////
    public function addArchivo($archivo):bool{

        $stmt_crearchivo  = $this->dbh->prepare(
            "INSERT INTO `archivos` (`nombre`, `usuario`, `tipoArchivo`, `fechaSubida`, `visibilidad`, `tamanio`)".
            "Values(?,?,?,?,?,?)");
        if ( $stmt_crearchivo == false) die ($this->dbh->error);

        $stmt_crearchivo->bind_param("sissis",$archivo->nombre,$archivo->usuario,$archivo->tipoArchivo,$archivo->fechaSubida,
        $archivo->visibilidad,$archivo->tamanio);
        $stmt_crearchivo->execute();
        $resu = ($this->dbh->affected_rows == 1);
        return $resu;
    }
    //////////////////////////////////////////////////////////////////////////////////////////

    //Obtener ////////////////////////////////////////////////////////////////////////////////
    public function getArchivos($usuario) {
        $archivos = [];
        $stmt_archivo  = $this->dbh->prepare("SELECT * FROM archivos WHERE usuario = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("i",$usuario);
        $stmt_archivo->execute();
        $result = $stmt_archivo->get_result();
        if ( $result ){
            while ( $archivo = $result->fetch_object('archivo') ){
                $archivos[]= $archivo;
            }
        }
        return $archivos;
    }

    //////////////////////////////////////////////////////////////////////////////////////////

    //Activacion /////////////////////////////////////////////////////////////////////////////

    //CODIGO

    //////////////////////////////////////////////////////////////////////////////////////////

    //Validacion /////////////////////////////////////////////////////////////////////////////

    //CODIGO

    //////////////////////////////////////////////////////////////////////////////////////////

    //Eliminacion ////////////////////////////////////////////////////////////////////////////
    public function eliminaArchivos($usuario) {
        $stmt_archivo = $this->dbh->prepare("DELETE FROM archivos WHERE usuario = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("i",$usuario);
        $stmt_archivo->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;

    }

    //////////////////////////////////////////////////////////////////////////////////////////
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    
}