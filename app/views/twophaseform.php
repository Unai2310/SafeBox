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
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
		<title>SafeBox</title>
	</head>

	<body style="background-image: url('/safebox/web/resources/texture3.png');">

		<div class="botonInicio">
			<a class="botonHome" href="./">Inicio</a>
		</div>

		<header>
			<div class="bienvenida">Verificacion en 2 pasos</div>
		</header>

		<br>
		<br>
		<br>

		<form class="genericform" action="/safebox/" method="POST" autocomplete="off">
            <div style="font-size: 20px; font-family: 'Helvetica';">
                <?= isset($msg)?$msg:'' ?>
                <br>
                <?= isset($recpwd)?$recpwd:'Codigo: ' ?>
                <br> 
                <input class="stylized" type="text" name="codigo" required=""> 
				<input type="hidden" name="csrf" value="<?= isset($_SESSION["token"])?$_SESSION["token"]:'' ?>">
                <br> 
                <?= isset($error)?$error:'' ?>
                <br>
                <input type="submit" value="<?= isset($accion)?$accion:'' ?>" name="orden">
                <input type="hidden" value="<?= isset($identificador)?$identificador:'' ?>" name="identificador">
                <br>
                <br>
			</div>
		</form>
	</body>
</html>