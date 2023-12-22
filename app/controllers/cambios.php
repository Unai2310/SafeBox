<?php
session_start();
require_once '../models/accesoDatosArchivo.php';
require_once '../config/configDB.php';
require_once '../models/archivos.php';
require_once 'funciones.php';

checkCSRF();

if ($_POST['file'] > 0) {
    $dirpublica = RUTARCHIVOS;
    $dirprivada = RUTAPRIVADA;

    $archivos = explode(",", $_POST['file']);

    $db = AccesoDatosArchivo::getModelo();

    if ($_POST['op'] == "cambio") {
        foreach ($archivos as &$value) {
            if ($db->getVisivilidad($_SESSION["id"], $value) == 0) {
                $db->cambiarVisibilidad($_SESSION["id"], 1, $value);
                rename($dirprivada."/".$value, $dirpublica."/".$value);
            } else {
                $extension = new SplFileInfo($value);
                $namenuevo = uniqid(true).".".$extension->getExtension();
                $db->cambiarVisibilidad($_SESSION["id"], 0, $value);
                $db->cambiarNombreFich($_SESSION["id"], $namenuevo, $value);
                rename($dirpublica."/".$value, $dirprivada."/".$namenuevo);
            }
        }
    } else if ($_POST['op'] == "borrado") {
        foreach ($archivos as &$value) {
            if ($db->getVisivilidad($_SESSION["id"], $value) == 0) {
                $db->eliminaArchivo($_SESSION["id"], $value);
                unlink(RUTAPRIVADA."/".$value);
            } else {
                $db->eliminaArchivo($_SESSION["id"], $value);
                unlink(RUTARCHIVOS."/".$value);
            }
        }
    } else if ($_POST['op'] == "envio") {
        $emails = explode(",", $_POST['email']);
        $validemails = [];
        foreach ($emails as &$value) {
            if (!regexEmail($value)) {
                array_push($validemails, $value);
            }
        }
        $codigo = "<strong>".$_SESSION["username"]."</strong> desde su cuenta de <strong>SafeBox</strong> quiere compartir contigo estos archivos</p>";
        foreach ($archivos as &$value) {
            $archivo = $db->getArchivo($value);
            $codigo .= " <p><a href='https://flo.no-ip.info/uploads/".$archivo->nombre."' class='btn'>".$archivo->nombre."</a></p>";
        }
        
        $html = file_get_contents("/home/unai/safebox/app/views/bodycorreocompartir.html");
        $partes = explode("&",$html);
        $htmlcompleto = $partes[0]."$codigo".$partes[1];
        $destinatarios = $validemails;
        enviarCorreo($destinatarios,"Envio de Ficheros", $htmlcompleto);
    }
}
echo json_encode($_POST);