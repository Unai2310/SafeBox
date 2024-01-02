<?php
    $_SESSION["token"] = md5(uniqid(mt_rand(), true));
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="/safebox/web/css/stylelog.css">
		<script src="/safebox/web/js/funciones.js"></script>
		<link rel="icon" type="image/png" href="/safebox/web/resources/favicon.png">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
		<title>SafeBox</title>
	</head>

	<body style="background-image: url('/safebox/web/resources/texture3.png');">

		<div class="botonInicio">
			<a class="botonHome" href="./">Inicio</a>
		</div>

		<header>
			<div class="bienvenida">Cambio de contraseña</div>
		</header>

		<br>
		<br>
		<br>

		<form class="genericform" action="/safebox/" method="POST" autocomplete="off">
            <div style="font-size: 20px; font-family: 'Helvetica';">
                <?= isset($msg)?$msg:'' ?>
                <br>
                Nueva Contraseña:
                <br> 
                <input class="stylized" type="password" id="txtPwd" name="pwd" required=""> 
				<br>
				Repetir Contraseña:
                <br> 
                <input class="stylized" type="password" id="reTxtPwd" name="repwd" required=""> 
                <br> 
                <?= isset($error)?$error:'' ?>
                <br>
                <input type="submit" value="<?= isset($accion)?$accion:'' ?>" name="orden">
                <input type="hidden" value="<?= isset($identificador)?$identificador:'' ?>" name="identificador">
				<input type="hidden" name="csrf" value="<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>">
                <br>
                <br>
			</div>
		</form>
	</body>
</html>