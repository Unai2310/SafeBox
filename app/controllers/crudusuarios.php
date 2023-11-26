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