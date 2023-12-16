<?php
	if (isset($_COOKIE["recordar"])) {
		$db = AccesoDatos::getModelo();
		if ($db->validaSesion($_COOKIE["recordar"])) {
			$us = $db->getUsuarioBySesion($_COOKIE["recordar"]);
			$_SESSION["id"] = $us->id;
			$_SESSION["nombre"] = $us->name;
			$_SESSION["username"] = $us->username;
			$_SESSION["email"] = $us->email;
			$_SESSION["cierresesion"] = "<a class=\"botonlink\" href=\"?orden=cerrar\">Cerrar Sesión</a>";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="/safebox/web/css/style.css">
		<script src="/safebox/web/js/funciones.js"></script>
		<script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
		<link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
		<meta name="theme-color" content="#CCE8ED">
		<meta charset="UTF-8">
		<meta name="description" content="Almacenamiento de recurso online seguro.">
		<meta name="viewport" content="width=device-width, initial-scale=0.5, user-scalable=no">
		<title>SafeBox</title>
	</head>

	<body style="background-image: url('/safebox/web/resources/texture3.png');">

		<header>
			<img src="/safebox/web/resources/safebox.png" style="margin: auto; display: block;">
		</header>

		<br>
		<br>

		<center> <div class="bienvenida">Almacena de forma segura todos los recursos que quieras</div></center>
		<br>
		<center> <div class="bienvenida">El tamaño máximo son 200MB</div></center>

		<br>
		<br>

		<?php
			if (!isset($_SESSION["id"])) {
				$botones = file_get_contents("app/views/botonesretencion.html");
			} 
        ?>

		<?= isset($botones)?$botones:'' ?>

		<center>
			<form action="" class="dropzone" id="dropzoneSubida">
				<input type="hidden" id="time" name="tiempo" value="1">
				<div class="dz-message" data-dz-message>
					<span>Arrastra o selecciona los archivos</span><br>
					<span>Haz click en el nombre para copiar su dirección</span>
				</div>
			</form>
		</center>

		<br>
		<br>

		<center>
			<div class="filalinks"> 
				<a href="?orden=login" class="botonlink"><?= isset($_SESSION["nombre"])?$_SESSION["nombre"]:'Login' ?></a> | 
				<?= isset($_SESSION["cierresesion"])?$_SESSION["cierresesion"]:'<a href="?orden=registrar" class="botonlink">Registrar</a>' ?> | 
				<a href="" class="botonlink">Informacion</a>  | 
				<a href="#" class="botonlink" id="changeTheme">Modo oscuro</a>
			</div>
		</center>

	</body>
</html>