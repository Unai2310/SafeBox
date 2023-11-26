<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería mysqli
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
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

    //Añadir usuario
    public function addUsuario($user):bool{

        $stmt_creauser  = $this->dbh->prepare(
            "INSERT INTO `usuarios`  (`name`, `username`, `email`, `pwd`, `active`, `twoPhase`, `token`)".
            "Values(?,?,?,?,?,?,?)");
        if ( $stmt_creauser == false) die ($this->dbh->error);

        $stmt_creauser->bind_param("ssssiis",$user->name,$user->username,$user->email,$user->pwd,$user->active,$user->twoPhase,$user->token);
        $stmt_creauser->execute();
        $resu = ($this->dbh->affected_rows == 1);
        return $resu;
    }
    
    public function existeUser (String $username) {
        $user = false;
        
        $stmt_usuario = $this->dbh->prepare("select * from usuarios where username =?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
  
        $stmt_usuario->bind_param("s",$username);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $user = $result->fetch_object();
        }
        
        return $user;
    }

    public function existeEmail (String $email) {
        $user = false;
        
        $stmt_usuario = $this->dbh->prepare("select * from usuarios where email =?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
  
        $stmt_usuario->bind_param("s",$email);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $user = $result->fetch_object();
        }
        
        return $user;
    }
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    
}