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
            "INSERT INTO `archivos` (`nombre`, `usuario`, `tipoArchivo`, `fechaSubida`, `visibilidad`, `tamanio`, `nombreog`)".
            "Values(?,?,?,?,?,?,?)");
        if ( $stmt_crearchivo == false) die ($this->dbh->error);

        $stmt_crearchivo->bind_param("sississ",$archivo->nombre,$archivo->usuario,$archivo->tipoArchivo,$archivo->fechaSubida,
        $archivo->visibilidad,$archivo->tamanio,$archivo->nombreog);
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

    public function getArchivo($id) {
        $stmt_archivo  = $this->dbh->prepare("SELECT * FROM archivos WHERE id = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("i",$id);
        $stmt_archivo->execute();
        $result = $stmt_archivo->get_result();
        if ( $result ){
            $archivo = $result->fetch_object('archivo');
        }
        return $archivo;
    }

    public function getVisivilidad($id, $nombre) {
        $visi = "";
        $stmt_archivo  = $this->dbh->prepare("SELECT visibilidad FROM archivos WHERE nombre = ? AND usuario = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("si", $nombre, $id);
        $stmt_archivo->execute();
        $result = $stmt_archivo->get_result();
        if ( $result ){
            $visi = $result->fetch_row();
        }

        return $visi[0];
    }

    public function getArchivosNewest($usuario) {
        $archivos = [];
        $stmt_archivo  = $this->dbh->prepare("SELECT * FROM archivos WHERE usuario = ? order by 5 DESC");
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

    public function getPropietario($nombrearchivo) {
        $propietario = "";

        $stmt_archivo = $this->dbh->prepare("SELECT usuario FROM archivos where nombre = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);
        
        $stmt_archivo->bind_param("s",$nombrearchivo);
        $stmt_archivo->execute();
        $result = $stmt_archivo->get_result();

        if ( $result ){
            $propietario = $result->fetch_row();
        }

        return $propietario;
    }

    public function getArchivosOldest($usuario) {
        $archivos = [];
        $stmt_archivo  = $this->dbh->prepare("SELECT * FROM archivos WHERE usuario = ? order by 5");
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

    public function getEspacioUsado($id) {
        $espacio = "";

        $stmt_archivo = $this->dbh->prepare("SELECT sum(tamanio) FROM archivos where usuario = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);
        
        $stmt_archivo->bind_param("i",$id);
        $stmt_archivo->execute();
        $result = $stmt_archivo->get_result();

        if ( $result ){
            $espacio = $result->fetch_row();
        }

        return $espacio;
    }

    public function getnumFicheros($id) {
        $num = "";

        $stmt_archivo = $this->dbh->prepare("SELECT count(id) FROM archivos where usuario = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);
        
        $stmt_archivo->bind_param("i",$id);
        $stmt_archivo->execute();
        $result = $stmt_archivo->get_result();
        
        if ( $result ){
            $num = $result->fetch_row();
        }

        return $num;
    }

    //////////////////////////////////////////////////////////////////////////////////////////

    //Cambio /////////////////////////////////////////////////////////////////////////////

    function cambiarVisibilidad ($id, $valor, $nombre) {
        $stmt_archivo   = $this->dbh->prepare("UPDATE archivos SET visibilidad = ? WHERE usuario = ? AND nombre = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("iis", $valor, $id, $nombre);
        $stmt_archivo->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

    function cambiarNombreFich ($id, $valor, $nombre) {
        $stmt_archivo   = $this->dbh->prepare("UPDATE archivos SET nombre = ? WHERE usuario = ? AND nombre = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("sis", $valor, $id, $nombre);
        $stmt_archivo->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

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

    public function eliminaArchivo($usuario, $nombre) {
        $stmt_archivo = $this->dbh->prepare("DELETE FROM archivos WHERE usuario = ? AND nombre = ?");
        if ( $stmt_archivo == false) die ($this->dbh->error);

        $stmt_archivo->bind_param("is",$usuario, $nombre);
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