<?php
require_once "../config/configDB.php";
require_once "../controllers/funciones.php";
session_start();
checkCSRF();

if (isset($_GET["type"])) {
    switch ($_GET['type']) {
        case "image/png"        : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
        case "image/jpeg"       : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
        case "image/gif"        : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
        case "video/mp4"        : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
        case "video/webm"       : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
        case "video/x-matroska" : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
        case "video/x-msvideo"  : header("Content-Type: ".$_GET['type']); readfile(RUTAPRIVADA."/".$_GET["name"]); break;
    }
}
?>
