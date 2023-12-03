<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__."/../composer/vendor/autoload.php";

function enviarCorreo($correo, $subject, $body) {
    $mail = new PHPMailer(true);
    $db = AccesoDatos::getModelo();

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                       
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = EMAIL;                 
        $mail->Password   = EMAILPWD;                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        $mail->Port       = 587;                                    

        //Recipients
        $mail->setFrom('safebox074@gmail.com', 'Equipo de Safebox');
        $mail->addAddress($correo, 'SafeBox');    

        //Content
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;//'Bienvenido a SafeBox';
        $mail->Body = $body;//getHtmlBody($db->getId($correo), $token);

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function generarTokenCorreo() {
    return bin2hex(openssl_random_pseudo_bytes(32));
}

function generarTokenCookie() {
    return bin2hex(openssl_random_pseudo_bytes(32));
}

function getHtmlBody($id, $token) {
    $html = file_get_contents("app/views/bodycorreovalidar.html");
    $partes = explode("&",$html);
    return $partes[0]."href=\"http://flo.no-ip.info/safebox/?orden=validar&id=".$id[0]."&token=".$token."\"".$partes[1];
}

function regexEmail($email) {
    $regex = '/^([a-zA-Z0-9_@.#&+-\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/';
    if (preg_match($regex,$email)) {
        return false;
    } else {
        return true;
    }
}

function createTwoPhase() {
    $abc = "0123456789";
    $cadena = "";
    for ($i = 0;$i<6;$i++) {
        $cadena .= $abc[rand(0, 9)];
    }
    return $cadena;
}

function checkCSRF(){
    if ( !isset($_REQUEST['csrf']) || $_REQUEST['csrf'] != $_SESSION['token']){
        header("Location: ./");
        exit();
    }
}
