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

	if (isset($_SESSION["id"]) && !isset($_COOKIE["subida"])) {
		setcookie("subida", $_SESSION["id"], time() + 3600);
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

		<center> <div class="bienvenida">Almacena de forma segura todos  los recursos que quieras</div></center>

		<br>
		<br>

		<div class="contenerdorExpiracion">
			<span style="vertical-align: -webkit-baseline-middle;">Tiempo de retencion:</span>
			<button class="seclectorTiempo" id="1h" onclick="cambiarBoton(this.id)" style="background-color: #7ADDFF">1<br>Hora</button>
			<button class="seclectorTiempo" id="12h" onclick="cambiarBoton(this.id)" >12<br>Horas</button>
			<button class="seclectorTiempo" id="24h" onclick="cambiarBoton(this.id)" >1<br>Dia</button>
			<button class="seclectorTiempo" id="72h" onclick="cambiarBoton(this.id)" >3<br>Dias</button>
		</div>

		<!-- <center>
			<form action="app/views/" method="post" enctype="multipart/form-data" class="dropzone zonaClicable" id="dropzoneSubida">
				<input type="hidden" id="time" name="tiempo" value="1h">
				<input type="hidden" name="reqtype" value="fileupload">
				<div class="dz-default dz-message"><button class="botonSubida" type="button">Arrastra o selecciona los archivos</button></div>
			</form>
		</center> -->

		<center>
			<form action="" class="dropzone" id="dropzoneSubida">
				
			<div class="dz-message" data-dz-message>
				<span>Arrastra o selecciona los archivos</span>
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