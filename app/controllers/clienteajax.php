<?php

require_once '../models/accesoDatosUsuario.php';
require_once ('funciones.php');
require_once '../config/configDB.php';

$datos = [];

if (isset($_POST['action'])) {
    $db = AccesoDatos::getModelo();
    $action = $_POST['action'];

    if ($action == "existeUsuario") {
        $datos['ok'] = $db->existeUser($_POST['usuario']);
    } else if ($action == 'compruebaEmail') {
        if (regexEmail($_POST['email'])) {
            $datos['ok'] = regexEmail($_POST['email']);
            $datos['msg'] = "Introduce un correo valido";
        } 
        if ($db->existeEmail($_POST['email'])) {
            $datos['ok'] = $db->existeEmail($_POST['email']);
            $datos['msg'] = "El correo ya existe";
        }
    } 
}

echo json_encode($datos);