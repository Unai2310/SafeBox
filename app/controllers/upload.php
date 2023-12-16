<?php
session_start();
require_once '../models/accesoDatosArchivo.php';
require_once '../config/configDB.php';
require_once '../models/archivos.php';
require_once 'funciones.php';

$dirdestino = RUTARCHIVOS;
$dirborrado = RUTABORRADO;
$respuesta = [];
$permitidas = ["png", "jpg", "jpeg", "gif", "mp3", "mp4", "pdf", "webm", "mkv", "avi", "txt", "json", "xml"];
$codigosErrorSubida= [ 
	UPLOAD_ERR_OK         => 'Subida correcta',  // Valor 0
    UPLOAD_ERR_INI_SIZE   => 'El tamaño del archivo excede el admitido por el servidor',  // directiva upload_max_filesize en php.ini
    UPLOAD_ERR_FORM_SIZE  => 'El tamaño del archivo excede el admitido por el cliente',  // directiva MAX_FILE_SIZE en el formulario HTML
    UPLOAD_ERR_PARTIAL    => 'El archivo no se pudo subir completamente',
    UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo para ser subido',
    UPLOAD_ERR_NO_TMP_DIR => 'No existe un directorio temporal donde subir el archivo',
    UPLOAD_ERR_CANT_WRITE => 'No se pudo guardar el archivo en disco',  // permisos
    UPLOAD_ERR_EXTENSION  => 'Una extensión PHP evito la subida del archivo',  // extensión PHP
]; 

$file = $_FILES['file'];
$respuesta["nombreviejo"] = $file['name'];
$extension = new SplFileInfo($respuesta["nombreviejo"]);
$file['name'] = uniqid(true).".".$extension->getExtension();
$tamanio=$file['size'];
$tipo=$file['type'];
$temp=$file['tmp_name'];
$error=$file['error'];

$fecha=getdate();
if ($fecha["minutes"] < 10) {
    $fecha["minutes"] = "0"+$fecha["minutes"];
}
$minbien = 
$fechaString=$fecha["mday"].",".$fecha["mon"].",".$fecha["year"].",".$fecha["hours"].",".$fecha["minutes"];

$respuesta["nombre"] = $file['name'];
$respuesta["error"] = $error;


if ($tamanio > 209715200) {
    $respuesta["error"] = "El archivo ".$respuesta["nombreviejo"]." supera el limite de 200MB";
} else if (!in_array($extension->getExtension(), $permitidas)) {
    $respuesta["error"] = "El archivo ".$respuesta["nombreviejo"]." no es de un formato admitido";
} if ($error > 0) {
    $respuesta["error"] = "El archivo ".$respuesta["nombreviejo"]." no se ha podido subir. ".$codigosErrorSubida[$error];
} else if (is_dir($dirdestino) && is_writable($dirdestino)) {
    if (file_exists($dirdestino."/".$file['name'])) {
        $file['name'] = uniqid(true).".".$extension->getExtension();
    } 
    if ($respuesta["error"] == 0) {
        move_uploaded_file($temp, $dirdestino."/".$file['name']);
        if (isset($_SESSION["id"])) {
            $db = AccesoDatosArchivo::getModelo();
            $archivo = new Archivo();
            $archivo->nombre = $file['name'];
            $archivo->usuario = $_SESSION["id"];
            $archivo->tipoArchivo = $tipo;
            $archivo->fechaSubida = $fechaString;
            $archivo->visibilidad = 1;
            $archivo->tamanio = $tamanio;
            $archivo->nombreog = $respuesta["nombreviejo"];
            $db->addArchivo($archivo);
        } else if (isset($_POST['tiempo'])){
            $tiempoborrado = sumaHoras($_POST['tiempo']);
            $horafechaborrado = explode(" ", $tiempoborrado);
            $fechaborrado = explode("-", $horafechaborrado[0]);
            $horaborrado = explode(":", $horafechaborrado[1]);
            $lineaponer = $fechaborrado[2].",".$fechaborrado[1].",".$fechaborrado[0].",".$horaborrado[0].",".$horaborrado[1].",".$dirdestino."/".$file['name']."\n";
            file_put_contents($dirborrado, $lineaponer, FILE_APPEND | LOCK_EX);
        } else {
            $respuesta["error"] = "El archivo ".$respuesta["nombreviejo"]." no se ha podido subir por un error interno.";
        }
    }
}
echo json_encode($respuesta);
?>