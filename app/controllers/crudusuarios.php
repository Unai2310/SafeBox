<?php

require_once ('funciones.php');
function crudPostRegistro(){
    limpiarArrayEntrada($_POST);
    $us = new usuario();
    $us->name = $_POST["name"];
    $us->username = $_POST["username"];
    $us->email = $_POST["email"];
    $us->pwd = sha1($_POST["password"]);
    $us->active = 0;
    $us->twoPhase = 0;
    $us->token = generarToken();
    $db = AccesoDatos::getModelo();
    if ($us->name == "" || $us->username == "" || $us->email == ""|| $us->pwd == "" || $_POST["reTxtPwd"] == "") {
        $msgnom = "<br><small><b>Hay algun campo vacio, por favor rellenalos todos para poder continuar.</b></small>";
        include_once "app/views/registro.php";
    } else if ($us->pwd != sha1($_POST["reTxtPwd"])) {
        $msgpwd = "<br><small><b>Las contraseñas no coinciden.</b></small>";
        $nom = $us->name;
        $usr = $us->username;
        $eml = $us->email;
        include_once "app/views/registro.php";
    } else if ($db->existeUser($us->username)) {
        $msgusr = "<br><small><b>El usuario ya existe. Elige otro nombre.</b></small>";
        $nom = $us->name;
        $eml = $us->email;
        include_once "app/views/registro.php";
    } else if ($db->existeEmail($us->email)) {
        $msgeml = "<br><small><b>El correo ya existe.</b></small>";
        $nom = $us->name;
        $usr = $us->username;
        include_once "app/views/registro.php";
    } else {
        $db->addUsuario($us);
        $eml = $us->email;
        enviarCorreo($eml, $us->token);
        include_once "app/views/postregistro.php";
    }
}

function crudvalidarUsuario() {
    if (isset($_GET["id"]) && isset($_GET["token"])) {
        $db = AccesoDatos::getModelo();
        if ($db->isActivo($_GET["id"])) {
            $msg = "Esta cuenta ya esta verificada en <strong>SafeBox</strong>. <br>
            Ya puedes <a href=\"?orden=login\" class=\"botonlink\">Iniciar Sesión</a>. 
            O empezar a subir archivos en <a href=\"/\" class=\"botonlink\">SafeBox</a>";
            include_once "app/views/postvalidacion.php";
        } else if ($db->validaToken($_GET["id"], $_GET["token"])) {
            $msg = "Se ha verificado la cuenta en <strong>SafeBox</strong>. <br>
            Ya puedes <a href=\"?orden=login\" class=\"botonlink\">Iniciar Sesión</a>. 
            O empezar a subir archivos en <a href=\"/\" class=\"botonlink\">SafeBox</a>";
            include_once "app/views/postvalidacion.php";
        } else {
            $msg = "Ha fallado la verificacion de la cuenta en <strong>SafeBox</strong>. <br>
            <a href=\"?orden=revalidacion&id=".$_GET["id"]."&token=".$_GET["token"]."\" class=\"botonlink\">Pulsa aqui</a> para reenviar el correo y probar de nuevo. <br>
            O a sube archivos de forma temporal en <a href=\"/\" class=\"botonlink\">SafeBox</a>";
            include_once "app/views/postvalidacion.php";
        } 
    }
}

function crudrevalidarUsuario() {
    $db = AccesoDatos::getModelo();
    $eml = $db->getEmail($_GET["id"]);
    enviarCorreo($eml, $db->getToken($_GET["id"]));
    include_once "app/views/postregistro.php";
}