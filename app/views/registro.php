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

    <form class="genericform" action="/safebox/" method="POST" autocomplete="off">

        Nombre: <br> <input class="stylized" type="text" name="name" value="<?= isset($nom)?$nom:'' ?>" required> <?= isset($msgnom)?$msgnom:'' ?> <br> <br>
        Usuario: <br> <input class="stylized" type="text" name="username" value="<?= isset($usr)?$usr:'' ?>"  required> <?= isset($msgusr)?$msgusr:'' ?> <br> <br>
        E-Mail: <br> <input class="stylized" type="text" name="email" style="width: 50%;" value="<?= isset($eml)?$eml:'' ?>" required> <?= isset($msgeml)?$msgeml:'' ?> <br> <br>
        Contraseña: <br> 
            <input id="txtPwd" type="password" name="password" class="pwd" required> 
            <input id="mostrarPwd" type="button" value="👁️" onclick="mostrarContrasena()"/>
        <br> <br>
        Confirmar Contraseña: <br> <input id="reTxtPwd" type="password" name="reTxtPwd" class="pwd" required> <?= isset($msgpwd)?$msgpwd:'' ?> <br> <br>
        <br>
        <input type="submit" value="Registrar" name="orden">
        <br> <br>
            Ya tienes una cuenta en SafeBox? Puedes iniciar sesión <a href="?orden=login" class="botonlink">aqui mismo</a>
        <br>
    </form>
    
    </body>
</html>
