<?php
session_start();

require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/usuario.php';
require_once 'app/controllers/crudusuarios.php';
require_once 'app/models/accesoDatos.php';


if (isset($_GET["orden"])) {
    switch ($_GET['orden']) {
        case "login"        : require_once "app/views/login.php"; break;
        case "registrar"    : require_once "app/views/registro.php"; break;
        case "validar"      : crudvalidarUsuario(); break;
        case "revalidacion" : crudrevalidarUsuario(); break;
        case "cerrar"       : crudTerminar(); break;
        case "activar"      : crudActivar(); break;
        case "desactivar"   : cruddesactivar(); break;
    }
} else  if (isset($_POST["orden"])){
    switch ($_POST['orden']) {
        case "Login"        : crudPostIngreso(); break;
        case "Registrar"    : crudPostRegistro(); break;
        case "Verificar"    : crudPostVerificar(); break;
    }
} else {
    require_once "app/views/principal.php";
}

?>