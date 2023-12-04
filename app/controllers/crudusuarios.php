<?php

require_once ('funciones.php');

function crudPostIngreso(){
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    if (!$db->existeUser($_POST["username"])) {
        $msgname = "El usuario no existe";
        include_once "app/views/login.php";
    } else {
        $us = $db->getUsuario($_POST["username"]);
        if ($us->active == 0) {
            $msgname = "El usuario no esta verificado. 
            Realizala <a href=\"?orden=revalidacion&id=".$us->id."&token=".$us->token."\" class=\"botonlink\">aqui</a>";
            include_once "app/views/login.php";
        } else if ($us->pwd != sha1($_POST["password"])) {
            $msgpass = "La contraseña no es correcta";
            $user=$_POST["username"];
            include_once "app/views/login.php";
        } else {
            if ($us->twoPhase == 1) {
                $msg = "Esta cuenta tiene activada la <strong>verificación en dos pasos</strong>. <br>
                Hemos enviado un correo a la direccion ".$us->email.". <br>
                Introduce este codigo para poder iniciar sesión";
                $identificador = $us->id;
                $codigo = createTwoPhase();
                $db->addTwoPhase($us->id, $codigo);
                $html = file_get_contents("app/views/bodycorreoverificar.html");
                $partes = explode("&",$html);
                $htmlcompleto = $partes[0]."$codigo".$partes[1];
                enviarCorreo($us->email,"Verificacion en 2 pasos", $htmlcompleto);
                $accion = "Verificar";
                include_once "app/views/twophaseform.php";
            } else {
                $_SESSION["id"] = $us->id;
                $_SESSION["nombre"] = $us->name;
                $_SESSION["username"] = $us->username;
                $_SESSION["email"] = $us->email;
                $_SESSION["cierresesion"] = "<a class=\"botonlink\" href=\"?orden=cerrar\">Cerrar Sesión</a>";

                if (isset($_POST["recordar"])) {
                    $tokenSesion = generarTokenCookie();
                    $db->addCookieToken($us->id, $tokenSesion);
                    setcookie("recordar", $tokenSesion, time() + 60);
                }
                
                include_once "app/views/principal.php";
            }
        }
    }
}

function crudPostActivar() {
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    $us = $db->getUsuarioById($_POST["identificador"]);
    if ($db->getTwoPhase($_POST["identificador"])[0] == $_POST["codigo"]) {
        $db->modTwoPhase($_SESSION["id"],1);
        include_once "app/views/principal.php";
    } else {
        $msg = "Estas a punto de activar la <strong>verificación en dos pasos</strong>. <br>
        Hemos enviado un correo a la direccion ".$_SESSION["email"].". <br>
        Introduce este codigo para activar la verificación";
        $identificador = $us->id;
        $error = "El codigo no es correcto";
        $accion = "Activar";
        include_once "app/views/twophaseform.php";
    }
}

function crudPostDesactivar() {
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    $us = $db->getUsuarioById($_POST["identificador"]);
    if ($db->getTwoPhase($_POST["identificador"])[0] == $_POST["codigo"]) {
        $db->modTwoPhase($_SESSION["id"],0);
        unset($_SESSION["twophasemsg"]);
        include_once "app/views/principal.php";
    } else {
        $msg = "Estas a punto de desactivar la <strong>verificación en dos pasos</strong>. <br>
        Hemos enviado un correo a la direccion ".$_SESSION["email"].". <br>
        Introduce este codigo para activar la verificación. <br>
        Ten en cuenta que la seguridad de la cuenta baja considerablemente.";
        $identificador = $us->id;
        $error = "El codigo no es correcto";
        $accion = "Desactivar";
        include_once "app/views/twophaseform.php";
    }
}

function crudPostCambiarPwd() {
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    $us = $db->getUsuarioById($_POST["identificador"]);
    if(isset($_SESSION["cambiopwd"])) {
        if ($db->getTwoPhase($_POST["identificador"])[0] == $_POST["codigo"]) {
            $db->modPwd($_POST["identificador"], $_SESSION["cambiopwd"]);
            AccesoDatos::closeModelo();
            session_destroy();
            header("Location: ./?orden=login");
        } else {
            $msg = "Para cambiar la contraseña de la cuenta es necesaria confirmacion. <br>
            Hemos enviado un correo a la direccion ".$_SESSION["email"].". <br>
            Introduce este codigo para poder cambiar la contraseña";
            $identificador = $us->id;
            $error = "El codigo no es correcto";
            $accion = "Cambiar";
            include_once "app/views/twophaseform.php";
        }
    }   
}

function crudPostRecuperarPwd() {
    limpiarArrayEntrada($_POST);
    checkCSRF();
    $db = AccesoDatos::getModelo();
    if (regexEmail($_POST['codigo'])) {
        $msg = "Para recuperar la contraseña de la cuenta es necesaria confirmacion. <br>
        Introduce el correo electronico asociado a tu cuenta para confirmar tu identidad. <br>
        El código que hay en el correo se podrá usar para iniciar sesión. <br>
        Cambia la contraseña en area de usuario nada mas inicies sesión con el nuevo token.";
        $error = "Introduce un correo valido";
        $accion = "Recuperar";
        include_once "app/views/twophaseform.php";
    } else if (!$db->existeEmail($_POST['codigo'])) {
        $msg = "Para recuperar la contraseña de la cuenta es necesaria confirmacion. <br>
        Introduce el correo electronico asociado a tu cuenta para confirmar tu identidad. <br>
        El código que hay en el correo se podrá usar para iniciar sesión. <br>
        Cambia la contraseña en area de usuario nada mas inicies sesión con el nuevo token.";
        $error = "No es un correo asociado a ninguna cuenta";
        $accion = "Recuperar";
        include_once "app/views/twophaseform.php";
    } else {
        $codigosincifrar = generarTokenCookie();
        $codigo = sha1($codigosincifrar);
        $html = file_get_contents("app/views/bodycorreorecuperarpwd.html");
        $partes = explode("&",$html);
        $htmlcompleto = $partes[0]."$codigosincifrar".$partes[1];
        $idus = $db->getId($_POST['codigo'])[0];
        $db->modPwd($idus, $codigo);
        enviarCorreo($_POST['codigo'],"Recuperacion de Contraseña", $htmlcompleto);
        AccesoDatos::closeModelo();
        session_destroy();
        header("Location: ./?orden=login");
    }


}

function crudPostVerificar(){
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    $us = $db->getUsuarioById($_POST["identificador"]);
    if ($db->getTwoPhase($_POST["identificador"])[0] == $_POST["codigo"]) {
        $_SESSION["id"] = $us->id;
        $_SESSION["nombre"] = $us->name;
        $_SESSION["username"] = $us->username;
        $_SESSION["email"] = $us->email;
        $_SESSION["cierresesion"] = "<a class=\"botonlink\" href=\"?orden=cerrar\">Cerrar Sesión</a>";

        if (isset($_POST["recordar"])) {
            $tokenSesion = generarTokenCookie();
            $db->addCookieToken($us->id, $tokenSesion);
            setcookie("recordar", $tokenSesion, time() + 60);
        }
        include_once "app/views/principal.php";
    } else {
        $msg = "Esta cuenta tiene activada la <strong>verificación en dos pasos</strong>. <br>
        Hemos enviado un correo a la direccion ".$us->email.". <br>
        Introduce este codigo para poder iniciar sesión";
        $identificador = $us->id;
        $error = "El codigo no es correcto";
        $accion = "Verificar";
        include_once "app/views/twophaseform.php";
    }
}

function crudPostRegistro(){
    limpiarArrayEntrada($_POST);
    $us = new usuario();
    $us->name = $_POST["name"];
    $us->username = $_POST["username"];
    $us->email = $_POST["email"];
    $us->pwd = sha1($_POST["password"]);
    $us->active = 0;
    $us->twoPhase = 0;
    $us->token = generarTokenCorreo();
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
    } else {
        $db->addUsuario($us);
        $eml = $us->email;
        enviarCorreo($eml, "Bienvenido a SafeBox", getHtmlBody($db->getId($eml), $us->token));
        include_once "app/views/postregistro.php";
    }
}

function crudPostCambiarInfo() {
    checkCSRF();
    if ($_POST["password"] != "") {
        $msg = "Para cambiar la contraseña de la cuenta es necesaria confirmacion. <br>
        Hemos enviado un correo a la direccion ".$_SESSION["email"].". <br>
        Introduce este codigo para poder cambiar la contraseña";
        $db = AccesoDatos::getModelo();
        $identificador = $_SESSION["id"];
        $codigo = createTwoPhase();
        $db->addTwoPhase($identificador, $codigo);
        $html = file_get_contents("app/views/bodycorreocambiarpwd.html");
        $partes = explode("&",$html);
        $htmlcompleto = $partes[0]."$codigo".$partes[1];
        enviarCorreo($_SESSION["email"],"Cambio de Contrseña", $htmlcompleto);
        $_SESSION["cambiopwd"] = sha1($_POST["password"]);
        $accion = "Cambiar";
        include_once "app/views/twophaseform.php";
    } else {
        $email=$_SESSION["email"];
        $nopwd="No pudes dejar este campo vacio";
        include_once "app/views/manejacuenta.php";
    }
    
    
}

function crudRecuperarContraseña() {
    $msg = "Para recuperar la contraseña de la cuenta es necesaria confirmacion. <br>
    Introduce el correo electronico asociado a tu cuenta para confirmar tu identidad. <br>
    El código que hay en el correo se podrá usar para iniciar sesión. <br>
    Cambia la contraseña en area de usuario nada mas inicies sesión con el nuevo token.";
    $accion = "Recuperar";
    $recpwd = "Correo: ";
    include_once "app/views/twophaseform.php";
}

function crudManejarCuenta() {
    checkCSRF();
    $email = $_SESSION["email"];
    include_once "app/views/manejacuenta.php";
}

function crudrevalidarUsuario() {
    $db = AccesoDatos::getModelo();
    $eml = $db->getEmail($_GET["id"]);
    $token = $db->getToken($_GET["id"]);
    enviarCorreo($eml, "Bienvenido a SafeBox", getHtmlBody($db->getId($eml),$token));
    include_once "app/views/postregistro.php";
}

function crudActivar() {
    checkCSRF();
    $msg = "Estas a punto de activar la <strong>verificación en dos pasos</strong>. <br>
    Hemos enviado un correo a la direccion ".$_SESSION["email"].". <br>
    Introduce este codigo para activar la verificación";
    $identificador = $_SESSION["id"];
    $accion = "Activar";
    $codigo = createTwoPhase();
    $db = AccesoDatos::getModelo();
    $db->addTwoPhase($_SESSION["id"], $codigo);
    $html = file_get_contents("app/views/bodycorreoverificar.html");
    $partes = explode("&",$html);
    $htmlcompleto = $partes[0]."$codigo".$partes[1];
    enviarCorreo($_SESSION["email"],"Verificacion en 2 pasos", $htmlcompleto);
    include_once "app/views/twophaseform.php";
}

function cruddesactivar() {
    checkCSRF();
    $msg = "Estas a punto de desactivar la <strong>verificación en dos pasos</strong>. <br>
    Hemos enviado un correo a la direccion ".$_SESSION["email"].". <br>
    Introduce este codigo para activar la verificación. <br>
    Ten en cuenta que la seguridad de la cuenta baja considerablemente.";
    $identificador = $_SESSION["id"];
    $accion = "Desactivar";
    $codigo = createTwoPhase();
    $db = AccesoDatos::getModelo();
    $db->addTwoPhase($_SESSION["id"], $codigo);
    $html = file_get_contents("app/views/bodycorreoverificar.html");
    $partes = explode("&",$html);
    $htmlcompleto = $partes[0]."$codigo".$partes[1];
    enviarCorreo($_SESSION["email"],"Verificacion en 2 pasos", $htmlcompleto);
    include_once "app/views/twophaseform.php";
}

function crudvalidarUsuario() {
    if (isset($_GET["id"]) && isset($_GET["token"])) {
        $db = AccesoDatos::getModelo();
        if ($db->isActivo($_GET["id"])) {
            $msg = "Esta cuenta ya esta verificada en <strong>SafeBox</strong>. <br>
            Ya puedes <a href=\"?orden=login\" class=\"botonlink\">Iniciar Sesión</a>. 
            O empezar a subir archivos en <a href=\"/\" class=\"botonlink\">SafeBox</a>";
            include_once "app/views/forminformativo.php";
        } else if ($db->validaToken($_GET["id"], $_GET["token"])) {
            $msg = "Se ha verificado la cuenta en <strong>SafeBox</strong>. <br>
            Ya puedes <a href=\"?orden=login\" class=\"botonlink\">Iniciar Sesión</a>. 
            O empezar a subir archivos en <a href=\"/\" class=\"botonlink\">SafeBox</a>";
            include_once "app/views/forminformativo.php";
        } else {
            $msg = "Ha fallado la verificacion de la cuenta en <strong>SafeBox</strong>. <br>
            <a href=\"?orden=revalidacion&id=".$_GET["id"]."&token=".$_GET["token"]."\" class=\"botonlink\">Pulsa aqui</a> para reenviar el correo y probar de nuevo. <br>
            O a sube archivos de forma temporal en <a href=\"/\" class=\"botonlink\">SafeBox</a>";
            include_once "app/views/forminformativo.php";
        } 
    }
}

function crudTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
    header("Location: ./");
}