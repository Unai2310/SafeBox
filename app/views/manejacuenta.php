<?php
    $_SESSION["token"] = md5(uniqid(mt_rand(), true));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="/safebox/web/css/stylelog.css">
        <link rel="icon" type="image/png" href="/safebox/web/resources/favicon.png">
        <meta charset="UTF-8">
        <script src="/safebox/web/js/funciones.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=0.5">
        <title>SafeBox</title>
    </head>

    <body style="background-image: url('/safebox/web/resources/texture3.png');">

        <div class="botonInicio">
            <a class="botonHome" href="./">Inicio</a>
        </div>
        <div class="botonInicio">
            <a class="botonHome" href="?orden=login">Volver</a>
        </div>

        <header>
            <div class="bienvenida">Gestion Cuenta</div>
        </header>

    <div class="notesmall" style="margin-top: 25px;">
	    
        <h3 style="margin-bottom: 5px;">Uso de Espacio</h3><hr>
        <p>Espacio Usado:  <?= isset($espacio)?$espacio:'' ?></p>
        <p>Archivos Totales:  <?= isset($nofich)?$nofich:'' ?></p>
        <h3 style="margin-bottom: 5px;">Informacion de la cuenta</h3><hr>

        <form class="genericform" action="/safebox/" method="post" style="border: 0;">
            E-Mail: <?= isset($email)?$email:'' ?>
            <br>
            <br>
            Password:<input type="password" name="password" id="password" value="">
            <div style="font-size: 12px;"><i><?= isset($nopwd)?$nopwd:'Pon la nueva contraseña y pulsa cambiar' ?></i></div>
            <br>
            <input type="hidden" name="csrf" value="<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>">
            <input type="submit" value="Cambiar Contraseña" name="orden">
        </form>

        <h3 style="margin-bottom: 5px;">Eliminación de la cuenta</h3><hr>

        <p>Puedes eliminar tu cuenta de SafeBox Todos tus archivos se borraran tanto los públicos como los privados</p>

        <p>Si quieres eliminar tu cuenta pulsa <a class='linkbutton' href="#" onclick="confirmarBorrar('<?=$_SESSION['username']?>','<?=$_SESSION['token']?>');">aqui</a>.</p>

    </body>

</html>