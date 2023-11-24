<?php
session_start();

if (isset($_GET["orden"])) {
    switch ($_GET['orden']) {
        case "login" : require_once "app/views/login.php"; break;
        case "registrar" : require_once "app/views/registro.php"; break;
    }
} else {
    require_once "app/views/principal.php";
}

?>