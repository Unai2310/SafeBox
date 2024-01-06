<?php

require_once ('funciones.php');

function crudPostIngreso(){
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    if (!$db->existeUser($_POST["username"])) {
        $msgpass = "Hay algún error en los datos indicados";
        include_once "app/views/login.php";
    } else {
        $us = $db->getUsuario($_POST["username"]);
        if ($us->active == 0) {
            $msgpass = "El usuario no esta verificado. 
            Realizala <a href=\"?orden=revalidacion&id=".$us->id."&token=".$us->token."\" class=\"botonlink\">aqui</a>";
            include_once "app/views/login.php";
        } else if ($us->pwd != sha1($_POST["password"]) && $us->token != sha1($_POST["password"])) {
            $msgpass = "Hay algún error en los datos indicados";
            include_once "app/views/login.php";
        } else if ($us->token == sha1($_POST["password"])) {
            $msg = "Has inciado sesion con el token de  <strong>recuperación de contraseña</strong>. <br>
            Introduce la contraseña que quieras que sea tu nueva contraseña";
            $accion = "Restablecer";
            $identificador = $us->id;
            include_once "app/views/cambiocontraform.php";
        } else {
            if ($us->twoPhase == 1) {
                $msg = "Esta cuenta tiene activada la <strong>verificación en 2 pasos</strong>. <br>
                Hemos enviado un correo a la direccion ".$us->email.". <br>
                Introduce este codigo para poder iniciar sesión";
                $identificador = $us->id;
                $codigo = createTwoPhase();
                $db->addTwoPhase($us->id, $codigo);
                $html = file_get_contents("app/views/bodycorreoverificar.html");
                $partes = explode("&",$html);
                $htmlcompleto = $partes[0]."$codigo".$partes[1];
                $destinatarios = [$us->email];
                enviarCorreo($destinatarios,"Verificacion en 2 pasos", $htmlcompleto);
                $accion = "Verificar";
                include_once "app/views/twophaseform.php";
            } else {
                $_SESSION["id"] = $us->id;
                $_SESSION["nombre"] = $us->name;
                $_SESSION["username"] = $us->username;
                $_SESSION["email"] = $us->email;
                $_SESSION["cierresesion"] = "<a class=\"botonlink\" href=\"?orden=cerrar\">Cerrar Sesión</a>";
                $_SESSION["loginstatus"] = "Logeado como ".$_SESSION["username"];
                $db->modToken($us->id, "1");
                if (isset($_POST["recordar"])) {
                    $tokenSesion = generarTokenCookie();
                    $db->addCookieToken($us->id, $tokenSesion);
                    setcookie("recordar", $tokenSesion, time() + 60);
                }
                
                include_once "app/views/userarea.php";
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
        $msg = "Estas a punto de activar la <strong>verificación en 2 pasos</strong>. <br>
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
        $msg = "Estas a punto de desactivar la <strong>verificación en 2 pasos</strong>. <br>
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
            if (isset($_COOKIE["recordar"])) {
                setcookie("recordar", '', time()-1000);
            }
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
        La contraseña original seguira siendo valida para el inicio de sesión.";
        $error = "Introduce un correo valido";
        $accion = "Recuperar";
        include_once "app/views/twophaseform.php";
    } else if (!$db->existeEmail($_POST['codigo'])) {
        AccesoDatos::closeModelo();
        if (isset($_COOKIE["recordar"])) {
            setcookie("recordar", '', time()-1000);
        }
        session_destroy();
        header("Location: ./?orden=login");
    } else if (!$db->isActivo($db->getId($_POST['codigo'])[0])) {
        AccesoDatos::closeModelo();
        if (isset($_COOKIE["recordar"])) {
            setcookie("recordar", '', time()-1000);
        }
        session_destroy();
        header("Location: ./?orden=login");
    } else {
        $codigosincifrar = generarTokenCookie();
        $codigo = sha1($codigosincifrar);
        $html = file_get_contents("app/views/bodycorreorecuperarpwd.html");
        $partes = explode("&",$html);
        $htmlcompleto = $partes[0]."$codigosincifrar".$partes[1];
        $idus = $db->getId($_POST['codigo'])[0];
        $db->modToken($idus, $codigo);
        $destinatarios = [$_POST['codigo']];
        enviarCorreo($destinatarios,"Recuperacion de Contraseña", $htmlcompleto);
        AccesoDatos::closeModelo();
        if (isset($_COOKIE["recordar"])) {
            setcookie("recordar", '', time()-1000);
        }
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
        $_SESSION["loginstatus"] = "Logeado como ".$_SESSION["username"];
        $db->modToken($us->id, "1");
        if (isset($_POST["recordar"])) {
            $tokenSesion = generarTokenCookie();
            $db->addCookieToken($us->id, $tokenSesion);
            setcookie("recordar", $tokenSesion, time() + 60);
        }
        include_once "app/views/principal.php";
    } else {
        $msg = "Esta cuenta tiene activada la <strong>verificación en 2 pasos</strong>. <br>
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
    } else if (contraSegura($_POST["password"]) != "OK") {
        $msgpwd = "<br><small><b>".contraSegura($_POST["password"])."</b></small>";
        $nom = $us->name;
        $usr = $us->username;
        $eml = $us->email;
        include_once "app/views/registro.php";
    } else if ($us->pwd != sha1($_POST["reTxtPwd"])) {
        $msgpwd = "<br><small><b>Las contraseñas no coinciden.</b></small>";
        $nom = $us->name;
        $usr = $us->username;
        $eml = $us->email;
        include_once "app/views/registro.php";
    } else {
        $db->addUsuario($us);
        $eml = "Hemos enviado a la direccion ".$us->email." un correo con la activación de la cuenta";
        $destinatarios = [$us->email];
        enviarCorreo($destinatarios, "Bienvenido a SafeBox", getHtmlBody($db->getId($us->email), $us->token));
        include_once "app/views/postregistro.php";
    }
}

function crudPostRestablecerPwd() {
    checkCSRF();
    limpiarArrayEntrada($_POST);
    $db = AccesoDatos::getModelo();
    $us = $db->getUsuarioById($_POST["identificador"]);
    if ($_POST["pwd"] != $_POST["repwd"]) {
        $msg = "Has inciado sesion con el token de  <strong>recuperación de contraseña</strong>. <br>
        Introduce la contraseña que quieras que sea tu nueva contraseña";
        $accion = "Restablecer";
        $identificador = $us->id;
        $error = "Las contraseñas no coinciden";
        include_once "app/views/cambiocontraform.php";
    } else if (contraSegura($_POST["pwd"]) != "OK") {
        $msg = "Has inciado sesion con el token de  <strong>recuperación de contraseña</strong>. <br>
        Introduce la contraseña que quieras que sea tu nueva contraseña";
        $accion = "Restablecer";
        $identificador = $us->id;
        $error = contraSegura($_POST["pwd"]);
        include_once "app/views/cambiocontraform.php";
    } else {
        $db->modToken($us->id, "1");
        $db->modPwd($us->id, sha1($_POST["pwd"]));
        AccesoDatos::closeModelo();
        if (isset($_COOKIE["recordar"])) {
            setcookie("recordar", '', time()-1000);
        }
        session_destroy();
        header("Location: ./");
    }
}

function crudPostCambiarInfo() {
    checkCSRF();
    if ($_POST["password"] == "") {
        $dba = AccesoDatosArchivo::getModelo();
        $email=$_SESSION["email"];
        $nopwd="No pudes dejar este campo vacio";
        $espacio = getMegas($dba->getEspacioUsado($_SESSION["id"])[0]);
        $nofich = $dba->getnumFicheros($_SESSION["id"])[0];
        include_once "app/views/manejacuenta.php";
    } else if (contraSegura($_POST["password"]) != "OK") {
        $dba = AccesoDatosArchivo::getModelo();
        $email=$_SESSION["email"];
        $nopwd=contraSegura($_POST["password"]);
        $espacio = getMegas($dba->getEspacioUsado($_SESSION["id"])[0]);
        $nofich = $dba->getnumFicheros($_SESSION["id"])[0];
        include_once "app/views/manejacuenta.php";
    } else {
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
        $destinatarios = [$_SESSION["email"]];
        enviarCorreo($destinatarios,"Cambio de Contrseña", $htmlcompleto);
        $_SESSION["cambiopwd"] = sha1($_POST["password"]);
        $accion = "Cambiar";
        include_once "app/views/twophaseform.php";
    }
}

function crudEnviarArchivos() {
    checkCSRF();
    $dba = AccesoDatosArchivo::getModelo();
    $previewfoto = ["image/png", "image/jpeg", "image/gif"];
    $previewvideo = ["video/mp4", "video/webm", "video/x-matroska", "video/x-msvideo"];
    if ($_GET["order"] == "oldest") {
        $archivos = $dba->getArchivosOldest($_SESSION["id"]);
        $vistatodo = "";
    } else if ($_GET["order"] == "newest") {
        $archivos = $dba->getArchivosNewest($_SESSION["id"]);
        $vistatodo = "";  
    } else {
        header("Location: ./");
    }
    $tienepublic = false;
    foreach ($archivos as $value) {
        $visibilidad = "";
        $color = "";
        if ($dba->getVisivilidad($_SESSION["id"], $value->nombre) == 1) {
            $tienepublic = true;
            $datosAtag = "href='".URL.$value->nombre."'";
            if (in_array($value->tipoArchivo, $previewfoto)) {
                $preview = "<img class='previewimg' src='".URL.$value->nombre."'>";
            } else if(in_array($value->tipoArchivo, $previewvideo)) {
                $preview = "<video class='previewimg' preload='metadata' controls=''><source src='".URL.$value->nombre."'></video>";
            } else if ($value->tipoArchivo == "audio/mpeg") {
                $preview = "<audio controls><source src='".URL.$value->nombre."' type='audio/mpeg'></audio>";
            } else {
                $preview = "<img class='previewimg' src='/safebox/web/resources/nopreview.png'>";
            }

            $vistatodo .= 
            "<div class='columnasvista'>
                <a target='_blank' $datosAtag>
                    ".$preview."
                </a> 
                <br> 
                <a class='botonlink' target='_blank'  href='".URL.$value->nombre."'>".$value->nombreog."</a>
                <p> ".getFechaFancy($value->fechaSubida)."</p>
                <p> ".getMegas($value->tamanio)." </p>
                <input type='checkbox' class='chckbs' value='$value->id'> 
            </div>";
        }
    }
    if (!$tienepublic) {
        $vistatodo = "<h1>No hay archivos públicos disponibles para enviar</h1>";
    }
    include_once "app/views/envio.php";
}

function crudRecuperarContraseña() {
    $msg = "Para recuperar la contraseña de la cuenta es necesaria confirmacion. <br>
    Introduce el correo electronico asociado a tu cuenta para confirmar tu identidad. <br>
    El código que hay en el correo se podrá usar para iniciar sesión. <br>
    La contraseña original seguira siendo valida para el inicio de sesión.";
    $accion = "Recuperar";
    $recpwd = "Correo: ";
    include_once "app/views/twophaseform.php";
}

function crudManejarCuenta() {
    checkCSRF();
    $dba = AccesoDatosArchivo::getModelo();
    $email = $_SESSION["email"];
    $espacio = getMegas($dba->getEspacioUsado($_SESSION["id"])[0]);
    $nofich = $dba->getnumFicheros($_SESSION["id"])[0];
    include_once "app/views/manejacuenta.php";
}

function crudVistaArchivos() {
    checkCSRF();
    $dba = AccesoDatosArchivo::getModelo();
    $previewfoto = ["image/png", "image/jpeg", "image/gif"];
    $previewvideo = ["video/mp4", "video/webm", "video/x-matroska", "video/x-msvideo"];
    if ($_GET["order"] == "oldest") {
        $archivos = $dba->getArchivosOldest($_SESSION["id"]);
        $vistatodo = "";
    } else if ($_GET["order"] == "newest") {
        $archivos = $dba->getArchivosNewest($_SESSION["id"]);
        $vistatodo = "";  
    } else {
        header("Location: ./");
    }
    foreach ($archivos as $value) {
        $visibilidad = "";
        $color = "";
        if ($dba->getVisivilidad($_SESSION["id"], $value->nombre) == 0) {
            $visibilidad = "PRIVADO";
            $color = "red";
            $datosAtag = "href='".URL.$value->nombre."'";
            if (in_array($value->tipoArchivo, $previewfoto)) {
                $preview = "<img class='previewimg' src='app/helpers/send_file.php?name=".$value->nombre."&csrf=CAMBIO&type=".$value->tipoArchivo."'>";  
            } else {
                $datosAtag = "class='cambiocsrf' href='app/helpers/send_file.php?name=".$value->nombre."&csrf=CAMBIO&type=".$value->tipoArchivo."'";
                $preview = "<img class='previewimg' src='/safebox/web/resources/previewprivada.png' alt='Haz click Para Ver El Archivo'>";
            }
        } else {
            $visibilidad = "PUBLICO";
            $color = "green";
            $datosAtag = "href='".URL.$value->nombre."'";
            if (in_array($value->tipoArchivo, $previewfoto)) {
                $preview = "<img class='previewimg' src='".URL.$value->nombre."'>";
            } else if(in_array($value->tipoArchivo, $previewvideo)) {
                $preview = "<video class='previewimg' preload='metadata' controls=''><source src='".URL.$value->nombre."'></video>";
            } else if ($value->tipoArchivo == "audio/mpeg") {
                $preview = "<audio controls><source src='".URL.$value->nombre."' type='audio/mpeg'></audio>";
            } else {
                $preview = "<img class='previewimg' src='/safebox/web/resources/nopreview.png'>";
            }
        }
        
        $vistatodo .= 
        "<div class='columnasvista'>
            <a target='_blank' $datosAtag>
                ".$preview."
            </a> 
            <br> 
            <a class='botonlink' target='_blank'  href='".URL.$value->nombre."'>".$value->nombreog."</a>
            <p> ".getFechaFancy($value->fechaSubida)."</p>
            <p> ".getMegas($value->tamanio)." </p>
            <p style='font-size: 18px; color: ".$color."'>".$visibilidad."</p>
            <input type='checkbox' class='chckbs' value='$value->nombre' style='display: none;'> 
        </div>";
    }
    if (count($archivos) == 0) {
        $vistatodo = "<h1>Esta cuenta no tiene archivos subidos</h1>";
    }
    include_once "app/views/vista.php";
}

function crudInfoSafeBox() {
    include_once "app/views/info.php";
}

function crudBorrarCuenta() {
    checkCSRF();
    $db = AccesoDatos::getModelo();
    $dba = AccesoDatosArchivo::getModelo();
    $archivosBorrar = $dba->getArchivos($_SESSION["id"]);
    foreach ($archivosBorrar as $value) {
        if ($value->visibilidad == 0) {
            unlink(RUTAPRIVADA."/".$value->nombre);
        } else {
            unlink(RUTARCHIVOS."/".$value->nombre);
        }
    }
    unset($value);
    $dba->eliminaArchivos($_SESSION["id"]);
    $db->eliminarUsuario($_SESSION["id"]);
    AccesoDatos::closeModelo();
    AccesoDatosArchivo::closeModelo();
    if (isset($_COOKIE["recordar"])) {
        setcookie("recordar", '', time()-1000);
    }
    session_destroy();
    header("Location: ./");
}

function crudrevalidarUsuario() {
    $db = AccesoDatos::getModelo();
    $token = $db->getToken($_GET["id"]);
    $destinatarios = [$db->getEmail($_GET["id"])];
    enviarCorreo($destinatarios, "Bienvenido a SafeBox", getHtmlBody($db->getId($db->getEmail($_GET["id"])),$token));
    $eml = "Hemos enviado a la direccion asociada su cuenta un correo con la activación de la cuenta";
    include_once "app/views/postregistro.php";
}

function crudActivar() {
    checkCSRF();
    $msg = "Estas a punto de activar la <strong>verificación en 2 pasos</strong>. <br>
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
    $destinatarios = [$_SESSION["email"]];
    enviarCorreo($destinatarios,"Verificacion en 2 pasos", $htmlcompleto);
    include_once "app/views/twophaseform.php";
}

function cruddesactivar() {
    checkCSRF();
    $msg = "Estas a punto de desactivar la <strong>verificación en 2 pasos</strong>. <br>
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
    $destinatarios = [$_SESSION["email"]];
    enviarCorreo($destinatarios,"Verificacion en 2 pasos", $htmlcompleto);
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
    if (isset($_COOKIE["recordar"])) {
        setcookie("recordar", '', time()-1000);
    }
    session_destroy();
    header("Location: ./");
}