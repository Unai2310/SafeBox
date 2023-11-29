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

    //Añadir /////////////////////////////////////////////////////////////////////////////////
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

    public function addTwoPhase($id, $codigo) {

        $stmt_moduser   = $this->dbh->prepare("UPDATE usuarios SET twoPahseCode = ? WHERE id = ?");
        if ( $stmt_moduser == false) die ($this->dbh->error);

        $stmt_moduser->bind_param("si",$codigo,$id);
        $stmt_moduser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

    public function addCookieToken($id, $sesion) {

        $stmt_moduser   = $this->dbh->prepare("UPDATE usuarios SET sesion = ? WHERE id = ?");
        if ( $stmt_moduser == false) die ($this->dbh->error);

        $stmt_moduser->bind_param("si",$sesion,$id);
        $stmt_moduser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }
    //////////////////////////////////////////////////////////////////////////////////////////

    //Obtener ////////////////////////////////////////////////////////////////////////////////
    public function getUsuario($user){
        $usuario = false;
        
        $stmt_usuario = $this->dbh->prepare("SELECT * FROM usuarios WHERE username = ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("s",$user);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $usuario = $result->fetch_object('usuario');
        }

        return $usuario;
    }

    public function getTwoPhase($id) {

        $token = "";

        $stmt_usuario = $this->dbh->prepare("SELECT twoPahseCode FROM usuarios WHERE id = ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("i",$id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $token = $result->fetch_row();
        }

        return $token;
    }

    public function getUsuarioById($id){
        $usuario = false;
        
        $stmt_usuario = $this->dbh->prepare("SELECT * FROM usuarios WHERE id = ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("i",$id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $usuario = $result->fetch_object('usuario');
        }

        return $usuario;
    }

    public function getId ($correo) {
        $id = "";

        $stmt_usuario = $this->dbh->prepare("SELECT id FROM usuarios WHERE email LIKE ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("s",$correo);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $id = $result->fetch_row();
        }

        return $id;
    }

    public function getToken($id) {
        $token = "";

        $stmt_usuario = $this->dbh->prepare("SELECT token FROM usuarios WHERE id = ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("s",$id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $token = $result->fetch_row();
        }

        return $token[0];
    }

    public function getEmail($id) {
        $email = "";

        $stmt_usuario = $this->dbh->prepare("SELECT email FROM usuarios WHERE id = ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("s",$id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $email = $result->fetch_row();
        }

        return $email[0];
    }
    //////////////////////////////////////////////////////////////////////////////////////////

    //Activacion /////////////////////////////////////////////////////////////////////////////
    function activarUsuario ($id) {
        $stmt_moduser   = $this->dbh->prepare("UPDATE usuarios SET active = 1 WHERE id = ?");
        if ( $stmt_moduser == false) die ($this->dbh->error);

        $stmt_moduser->bind_param("i", $id);
        $stmt_moduser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

    function modTwoPhase ($id, $activo) {
        $stmt_moduser   = $this->dbh->prepare("UPDATE usuarios SET twoPhase = ? WHERE id = ?");
        if ( $stmt_moduser == false) die ($this->dbh->error);

        $stmt_moduser->bind_param("si", $activo, $id);
        $stmt_moduser->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }
    //////////////////////////////////////////////////////////////////////////////////////////

    //Validacion /////////////////////////////////////////////////////////////////////////////
    public function validaToken($id, $token) {
        
        $stmt_usuario = $this->dbh->prepare("SELECT id FROM usuarios WHERE id = ? AND token LIKE ? LIMIT 1");
        if ( $stmt_usuario == false) die ($this->dbh->error);
  
        $stmt_usuario->bind_param("is",$id, $token);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $user = $result->fetch_row();
        }
        if (isset($user)) {
            if ($this->activarUsuario($id)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function validaSesion($sesion) {
        $user = "";
        
        $stmt_usuario = $this->dbh->prepare("SELECT id FROM usuarios WHERE sesion LIKE ? LIMIT 1");
        if ( $stmt_usuario == false) die ($this->dbh->error);
  
        $stmt_usuario->bind_param("s",$sesion);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $user = $result->fetch_row();
        }
        if (isset($user)) {
            return true;
        } else {
            return false;
        }
    }

    public function isActivo($id) {
        $valid = "";

        $stmt_usuario = $this->dbh->prepare("SELECT active FROM usuarios WHERE id = ?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        
        $stmt_usuario->bind_param("s",$id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $valid = $result->fetch_row();
        }

        if ($valid[0] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function existeUser (String $username) {
        $user = false;
        
        $stmt_usuario = $this->dbh->prepare("SELECT * FROM usuarios WHERE username =?");
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
        
        $stmt_usuario = $this->dbh->prepare("SELECT * FROM usuarios WHERE email =?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
  
        $stmt_usuario->bind_param("s",$email);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $user = $result->fetch_object();
        }
        
        return $user;
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    
}