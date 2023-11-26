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

		<header>
			<div class="bienvenida">Bienvenido</div>
		</header>

		<br>
		<br>
		<br>

		<form class="genericform" action="dologin.php" method="post" enctype="multipart/form-data">
			<div style="font-size: 20px; font-family: 'Helvetica';">
                <?= isset($msg)?$msg:'' ?>
			</div>
		</form>
	</body>
</html>