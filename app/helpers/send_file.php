<?php
require_once "../config/configDB.php";
require_once "../controllers/funciones.php";
require_once "../models/accesoDatosArchivo.php";
session_start();
checkCSRF();

if (isset($_GET["type"]) && isset($_SESSION['id'])) {
    $db = AccesoDatosArchivo::getModelo();
    if ($_SESSION['id'] == $db->getPropietario($_GET["name"])[0]) {
        switch ($_GET['type']) {
            case "image/png"        : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "image/jpeg"       : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "image/gif"        : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "video/mp4"        : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "video/webm"       : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "video/x-matroska" : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "video/x-msvideo"  : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
            case "audio/mpeg"       : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break; 
            case "application/pdf"  : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break; 
            case "text/plain"       : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break; 
            case "application/json" : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break; 
            case "application/xml"  : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;  
        }
    } else {
        header("Location: ./");
        exit();
    }
}
?>
