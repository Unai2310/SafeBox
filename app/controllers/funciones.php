<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__."/../composer/vendor/autoload.php";

function enviarCorreo($correo, $token) {
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
        $mail->Subject = 'Bienvenido a SafeBox';
        $mail->Body = getHtmlBody($db->getId($correo), $token);

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function generarToken() {
    return bin2hex(openssl_random_pseudo_bytes(32));
}

function getHtmlBody($id, $token) {
    $html = file_get_contents("app/views/bodycorreo.html");
    $partes = explode("&",$html);
    return $partes[0]."href=\"http://flo.no-ip.info/safebox/?orden=validar&id=".$id[0]."&token=".$token."\"".$partes[1];
}