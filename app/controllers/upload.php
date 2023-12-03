<?php

require_once '../models/accesoDatosArchivo.php';
require_once '../config/configDB.php';
require_once '../models/archivos.php';

$dirdestino = RUTARCHIVOS;

$file = $_FILES['file'];
$extension = new SplFileInfo($file['full_path']);
$file['name'] = uniqid(true).".".$extension->getExtension();
$tamanio=$file['size'];
$tipo=$file['type'];
$temp=$file['tmp_name'];
$error=$file['error'];

$fecha=getdate();
$fechaString=$fecha["mday"]."/".$fecha["mon"]."/".$fecha["year"]."/".$fecha["hours"]."/".$fecha["minutes"];

if (is_dir($dirdestino) && is_writable($dirdestino)) {
    if (file_exists($dirdestino."/".$file['name'])) {
        $file['name'] = uniqid(true).".".$extension->getExtension();
    } else if (move_uploaded_file($temp, $dirdestino."/".$file['name']) == true) {
        $db = AccesoDatosArchivo::getModelo();
        $archivo = new Archivo();
        $archivo->nombre = $file['name'];
        $archivo->usuario = $_COOKIE["subida"];
        $archivo->tipoArchivo = $tipo;
        $archivo->fechaSubida = $fechaString;
        $archivo->visibilidad = 1;
        $archivo->tamanio = $tamanio;
        $db->addArchivo($archivo);
        echo json_encode($file);
    } 
}
?>