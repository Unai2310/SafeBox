<?php
    $_SESSION["token"] = md5(uniqid(mt_rand(), true));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="/safebox/web/css/stylelog.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.5">
        <title>SafeBox</title>
    </head>

    <body style="background-image: url('/safebox/web/resources/texture3.png');">

        <div class="botonInicio">
            <a class="botonHome" href="./">Inicio</a>
        </div>
        <div class="botonInicio">
            <a class="botonHome" href="?orden=cerrar">Cerrar Sesion</a>
        </div>

        <header>
        <div class="bienvenida">Area del Usuario</div>
        </header>

        <br>
        <br>
        <br>
        <br>

        <center><ul>
            <li class="home"><a class ="botonlink" href="?orden=vista&order=oldest&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>">Ver Archivos</a></li>
            <li class="home"><a class ="botonlink" href="?orden=envio&order=oldest&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>">Enviar Archivos</a></li>
            <li class="home"><a class ="botonlink" href="?orden=cuenta&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>">Gestionar Cuenta</a></li>
        </ul></center>

        <div class="notetiny" style="margin-top: 25px;">
            <?php
                $db = AccesoDatos::getModelo();
                $us = $db->getUsuarioById($_SESSION["id"]);
                if ($us->twoPhase == 0) {
                    $msgTwoPhase = "Activa la <a class=\"botonlink\" href=\"?orden=activar&csrf=".$_SESSION["token"]."\">verificacion</a> en dos pasos para aumentar la seguridad de tu cuenta";
                } else {
                    $msgTwoPhase = "Desactivar la <a class=\"botonlink\" href=\"?orden=desactivar&csrf=".$_SESSION["token"]."\">verificacion</a> en dos pasos. Esto disminuirá la seguridad de la cuenta";
                }
            ?>
            <?= isset($msgTwoPhase)?$msgTwoPhase:'' ?>
		</div>
    </body>

</html>