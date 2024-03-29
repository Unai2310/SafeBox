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
			<div class="bienvenida">Inicio de Sesión</div>
		</header>

		<br>
		<br>
		<br>

		<form class="genericform" action="/safebox/" method="POST" autocomplete="off">
			Usuario: 
			<br> 
			<input class="stylized" type="text" name="username" required=""> 
			<br> 
			<br>
			<br>
			Contraseña: 
			<br> 
			<input class="stylized" type="password" name="password" required=""> 
			<br> 
			<?= isset($msgpass)?$msgpass:'' ?>
			<br>
			<input class="stylized" id="recuerda" type="checkbox" value="1" name="recordar"><label for="recuerda">Recordarme</label>
			<br>
			<input type="submit" value="Login" name="orden">
			<br>
			<br>
			<div style="font-size: 16px; font-family: 'Helvetica';">No tienes cuenta? <a class="botonlink" href="?orden=registrar"> Registrate </a>
				<br> 
				<a class="botonlink" href="?orden=recuperar">Olvidaste tu contraseña?</a>
			</div>
		</form>
	</body>
</html>