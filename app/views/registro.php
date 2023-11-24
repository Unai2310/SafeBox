<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/safebox/web/css/stylelog.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.6">
        <script src="/safebox/web/js/funciones.js"></script>
        <title>SafeBox</title>
    </head>

    <body style="background-image: url('/safebox/web/resources/texture3.png');">

    <div class="botonInicio">
		<a class="botonHome" href="./">Inicio</a>
	</div>

	<header>
		<div class="bienvenida">Registro de Usuarios</div>
	</header>

    <br>
    <br>
    <br>

    <form class="genericform" action="doregister.php" method="post" enctype="multipart/form-data" autocomplete="off">
        Usuario: <br> <input class="stylized" type="text" name="username" required> <br><small><b>No puede ser el mismo que tu email.</b></small> <br>
        E-Mail: <br> <input class="stylized" type="text" name="email" style="width: 50%;" required> <br> <br>
        Contraseña: <br> 
            <input id="txtPwd" class="pwd" type="password" name="password" required> 
            <input id="mostrarPwd" type="button" value="👁️" onclick="mostrarContrasena()"/>
        <br> <br>
        Confirmar Contraseña: <br> <input id="reTxtPwd" class="pwd" type="password" name="password" required> <br> <br>
        <br>
        <input type="submit" value="Register" name="submit">
        <br> <br>
            Ya tienes una cuenta en SafeBox?. Puedes iniciar sesión <a href="?orden=login" class="botonlink">aqui mismo</a>
        <br>
    </form>
    
    </body>
</html>
