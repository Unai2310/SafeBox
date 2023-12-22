<?php
    $_SESSION["token"] = md5(uniqid(mt_rand(), true));
?>
<!DOCTYPE html>
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="/safebox/web/css/stylelog.css">
        <script src="/safebox/web/js/funciones.js"></script>
		<meta charset="UTF-8">
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
        <div class="bienvenida">Envio de Archivos</div>
        </header>

        <br>

        <div class="notesmall">
            <p>Selecciona los archivos que quieras enviar
            <p style="font-size: 16px;">Ordenar por: 
            <a href="?orden=envio&order=oldest&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>" class="botonlink">Viejo</a> 
            <a href="?orden=envio&order=newest&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>" class="botonlink">Nuevo</a>
            </p></p>

            <p id="seleccionatext" style="margin-bottom: 5px;">Indica los correos de destino</p>

            <input id="txtCorreo" class="stylized" type="text">
            <button class="botoncambio" id="cambio" onclick="return aniadirCorreo()">Añadir Correo</button>
            <ul id="listacorreos">
                
            </ul>
            <hr>
            <button class="botoncambio" id="borrado" onclick="enviarArchivos()">Enviar Seleccionados</button>

            <input type="hidden" id="csrftoken" value="<?=$_SESSION['token']?>">

            <p style="font-size: 14px;" class="opcioneseleccion" id="opselec">
                <a href="#" class="botonlink" id="seltodo" onclick="selTodo()">Seleccionar Todo </a> 
                <a href="#" class="botonlink" id="selnada" onclick="selNada()"> Deseleccionar Todo</a>
            </p>

            <p id="mensajefinal" style="font-size: 18px; color: green;"></p>

        </div>

        <br>
        <br>

        <div class="rejilla" id="results">
            <?= isset($vistatodo)?$vistatodo:'' ?>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                cambioCSRF();
            });
        </script>
    </body>
</html>