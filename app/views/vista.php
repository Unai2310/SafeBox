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
        <div class="bienvenida">Vista de Archivos</div>
        </header>

        <br>

        <div class="notesmall">
            <p>Todos los archivos subidos por <b><?= isset($_SESSION["username"])?$_SESSION["username"]:'' ?></b>
            <p style="font-size: 16px;">Ordenar por: 
            <a href="?orden=vista&order=oldest&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>" class="botonlink">Viejo</a> 
            <a href="?orden=vista&order=newest&csrf=<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>" class="botonlink">Nuevo</a>
            </p></p>

            <p id="seleccionatext" style="margin-bottom: 5px;">Selecciona para...</p>

            <button class="botoncambio" id="cambio" onclick="editarArchivos(1)">Cambiar Acceso</button>

            <button class="botoncambio" id="borrado" onclick="editarArchivos(2)">Borrar</button>

            <input type="hidden" id="csrftoken" value="<?=$_SESSION['token']?>">

            <p style="font-size: 14px; display: none;" class="opcioneseleccion" id="opselec">
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
    </body>
</html>