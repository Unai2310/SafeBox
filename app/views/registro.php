<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/safebox/web/css/stylelog.css">
        <link rel="icon" type="image/png" href="/safebox/web/resources/favicon.png">
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
            Nombre: <br> <input class="stylized" type="text" name="name" value="<?= isset($nom)?$nom:'' ?>" required>  <br> <br>
            Usuario: <br> <input id=usuario class="stylized" type="text" name="username" value="<?= isset($usr)?$usr:'' ?>"  required> <br>
            <small><b><span id=msgusr></span></b></small> <br>
            E-Mail: <br> <input id=mail class="stylized" type="text" name="email" style="width: 50%;" value="<?= isset($eml)?$eml:'' ?>" required> <br>
            <small><b><span id=msgeml></span></b></small> <br> <br>
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
        <script>
            let txtUsuario = document.getElementById('usuario');
            txtUsuario.addEventListener("blur", function() {
                exiteUsuario(txtUsuario.value)
            }, false)

            let txtEmail = document.getElementById('mail');
            txtEmail.addEventListener("blur", function() {
                exiteEmail(txtEmail.value)
            }, false)

            function exiteUsuario(usuario) {
                let url = "app/controllers/clienteajax.php";
                let formData = new FormData();
                formData.append("action", "existeUsuario");
                formData.append("usuario", usuario);

                fetch(url, {
                    method: 'POST',
                    body:formData
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        document.getElementById('usuario').value = '';
                        document.getElementById('msgusr').innerHTML = 'El usuario ya existe'
                    } else {
                        document.getElementById('msgusr').innerHTML = ''
                    }
                }) 
            }

            function exiteEmail(email) {
                let url = "app/controllers/clienteajax.php";
                let formData = new FormData();
                formData.append("action", "compruebaEmail");
                formData.append("email", email);

                fetch(url, {
                    method: 'POST',
                    body:formData
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        document.getElementById('mail').value = '';
                        document.getElementById('msgeml').innerHTML = data.msg
                    } else {
                        document.getElementById('msgeml').innerHTML = ''
                    }
                }) 
            }
        </script>
    </body>
</html>
