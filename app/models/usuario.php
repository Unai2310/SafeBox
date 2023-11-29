<?php
class Usuario {

    private $id;
    private $name;
    private $username;	
    private $email;	
    private $pwd;
    private $active;
    private $twoPhase;
    private $token;
    private $twoPahseCode;
    private $sesion;

    
    function __set($name, $value)
   {
    if ( property_exists($this,$name)){
        $this->$name = $value;
    }
   }

   function &__get($name)
   {
       if ( property_exists($this,$name)){
           return $this->$name;
       }
   }

}