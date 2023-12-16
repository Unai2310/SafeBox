<?php
class Archivo {

    private $id;
    private $nombre;
    private $usuario;	
    private $tipoArchivo;	
    private $fechaSubida;
    private $visibilidad;
    private $tamanio;
    private $nombreog;
    
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