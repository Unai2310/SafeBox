function cambiarBoton (nombre) {
    var botones = document.getElementsByClassName("seclectorTiempo");
    for(i=0;i<botones.length;i++) {
        document.getElementById(botones[i].id).style = "background-color: rgb(204 232 237)";
    }
    document.getElementById(nombre).style = "background-color: #7ADDFF";
    document.getElementById("time").value = nombre;
}

function mostrarContrasena(){
    var tipo = document.getElementById("txtPwd");
    var reTipo = document.getElementById("reTxtPwd");
    if(tipo.type == "password"){
        tipo.type = "text";
        reTipo.type = "text";
    }else{
        tipo.type = "password";
        reTipo.type = "password";
    }
}

function confirmarBorrar(nombre,csrf){
    if (confirm("¿Quieres eliminar tu cuenta con username:  "+nombre+"?")) {
        document.location.href="?orden=borrar&csrf="+csrf;
    }
}

function eliminarCorreo(elemento) {
    var id=elemento.parentNode.getAttribute("id");
    node=document.getElementById(id);
    node.parentNode.removeChild(node);
}

function aniadirCorreo() {
    var correo=document.getElementById("txtCorreo").value;
    if(correo.length>0) {
        if(find_li(correo) && /^([a-zA-Z0-9_@.#&+-\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/.test(correo)) {
            var li=document.createElement('li');
            li.id=correo;
            li.innerHTML="<span id="+correo+"> "+correo+" <img src='/safebox/web/resources/Borrar.ico' width='16px' heigth='16px' onclick='eliminarCorreo(this)'></span>";
            document.getElementById("listacorreos").appendChild(li);
            document.getElementById("txtCorreo").style.borderColor = "#142c3c";
        } else {
            document.getElementById("txtCorreo").style.borderColor = "#a33232";
        }
    } else {
        document.getElementById("txtCorreo").style.borderColor = "#a33232";
    }
    document.getElementById("txtCorreo").value = "";
    return false;
}
   
function find_li(contenido) {
    var el = document.getElementById("listacorreos").getElementsByTagName("li");
    for (var i=0; i<el.length; i++) {
        if(el[i].id==contenido)
            return false;
    }
    return true;
}

function enviarArchivos() {
    let correos = document.getElementById("listacorreos").getElementsByTagName("li");
    let chkbs = document.getElementsByClassName("chckbs");
    let marcados = [];
    let marcadosemail = [];
    for(i=0;i<chkbs.length;i++) {
        if (chkbs[i].checked == true) {
            marcados.push(chkbs[i].value);
        }
    }
    for(i=0;i<correos.length;i++) {
        marcadosemail.push(correos[i].id);
    }
    if (marcados.length > 0 && marcadosemail.length > 0) {
        if (confirm("¿Quieres enviar los archivos marcados a los destinatarios indicados?")) {
            let token = document.getElementById("csrftoken").value;
            let fileData = new FormData();
            fileData.append('file', marcados);
            fileData.append('email', marcadosemail);
            fileData.append('csrf', token);
            fileData.append('op', "envio");
            fetch('/safebox/app/controllers/cambios.php', {
                method: 'POST',
                body: fileData
            }).then(res => res.json()).then(data => {
                alert("Correo Enviado con exito");
            })
        }
    } else {
        alert("No hay indicados archivos o destinatarios");
    }
}

function cambiarVisibilidad() {
    let chkbs = document.getElementsByClassName("chckbs");
    let marcados = [];
    for(i=0;i<chkbs.length;i++) {
        if (chkbs[i].checked == true) {
            marcados.push(chkbs[i].value);
        }
    }
    if (marcados.length > 0) {
        if (confirm("¿Quieres cambiar la visibilidad los archivos marcados?")) {
            let token = document.getElementById("csrftoken").value;
            let fileData = new FormData();
            fileData.append('file', marcados);
            fileData.append('csrf', token);
            fileData.append('op', "cambio");
            fetch('/safebox/app/controllers/cambios.php', {
                method: 'POST',
                body: fileData
            }).then(res => res.json()).then(data => {
                //console.log(data)
                const obj = JSON.parse(JSON.stringify(data));
                document.location.href="?orden=vista&order=oldest&csrf="+obj.csrf;
            })
        }
    } else {
        alert("No hay archivos marcados");
    }
}

function borrarArchivos() {
    let chkbs = document.getElementsByClassName("chckbs");
    let marcados = [];
    for(i=0;i<chkbs.length;i++) {
        if (chkbs[i].checked == true) {
            marcados.push(chkbs[i].value);
        }
    }
    if (marcados.length > 0) {
        if (confirm("¿Quieres seguro eliminar los archivos marcados?")) {
            let token = document.getElementById("csrftoken").value;
            let fileData = new FormData();
            fileData.append('file', marcados);
            fileData.append('csrf', token);
            fileData.append('op', "borrado");
            fetch('/safebox/app/controllers/cambios.php', {
                method: 'POST',
                body: fileData
            }).then(res => res.json()).then(data => {
                const obj = JSON.parse(JSON.stringify(data));
                document.location.href="?orden=vista&order=oldest&csrf="+obj.csrf;
            })
        } 
    } else {
        alert("No hay archivos marcados");
    }
}

function editarArchivos(num) {
    let chckbs = document.getElementsByClassName("chckbs");
    for(i=0;i<chckbs.length;i++) {
        chckbs[i].style.display="";
    }

    document.getElementById('opselec').style.display = "";

    if (num == 1) {
        document.getElementById('borrado').removeAttribute('onclick');
        document.getElementById('borrado').textContent = "Volver";
        document.getElementById('borrado').setAttribute('onclick', 'noeditarArchivos()');
        document.getElementById('seleccionatext').textContent = "Marca los archivos que quieras cambiar de visibilidad";
        document.getElementById('cambio').removeAttribute('onclick');
        document.getElementById('cambio').setAttribute('onclick', 'cambiarVisibilidad()');
    } else if (num == 2){
        document.getElementById('cambio').removeAttribute('onclick');
        document.getElementById('cambio').textContent = "Volver";
        document.getElementById('cambio').setAttribute('onclick', 'noeditarArchivos()');
        document.getElementById('seleccionatext').textContent = "Marca los archivos que quieras eliminar";
        document.getElementById('borrado').removeAttribute('onclick');
        document.getElementById('borrado').setAttribute('onclick', 'borrarArchivos()');
    }
}

function noeditarArchivos() {
    let chkbs = document.getElementsByClassName("chckbs");
    for(i=0;i<chkbs.length;i++) {
        chkbs[i].style.display="none";
    }

    document.getElementById('opselec').style.display = "none";
    document.getElementById('seleccionatext').textContent = "Selecciona para...";
    document.getElementById('cambio').textContent = "Cambiar Acceso";
    document.getElementById('cambio').removeAttribute('onclick');
    document.getElementById('cambio').setAttribute('onclick', 'editarArchivos(1)');
    document.getElementById('borrado').textContent = "Borrar";
    document.getElementById('borrado').removeAttribute('onclick');
    document.getElementById('borrado').setAttribute('onclick', 'editarArchivos(2)');
}

function selTodo() {
    let chkbs = document.getElementsByClassName("chckbs");
    for(i=0;i<chkbs.length;i++) {
        chkbs[i].checked = true;
    }
}

function selNada() {
    let chkbs = document.getElementsByClassName("chckbs");
    for(i=0;i<chkbs.length;i++) {
        chkbs[i].checked = false;
    }
}

function cambioCSRF() {
    let csrf = document.getElementById("csrftoken");

    let imgs = document.getElementsByTagName("img");
    for(i=0;i<imgs.length;i++) {
        let urlimg = imgs[i].src;
        if (urlimg.indexOf("CAMBIO") != -1) {
            imgs[i].src = urlimg.replace("CAMBIO", csrf.value);
        }
    }

    let vidsmp3 = document.getElementsByClassName("cambiocsrf");
    for(i=0;i<vidsmp3.length;i++) {
        let urlmal = vidsmp3[i].href;
        vidsmp3[i].href = urlmal.replace("CAMBIO", csrf.value);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById('dropzoneSubida')) {
        let myDropzone = new Dropzone('.dropzone', {
            url: '/safebox', 
            maxFileSize: 209715200,
            acceptedFiles: 'image/jpeg, image/png, image/gif, audio/mpeg, video/mp4, application/pdf, video/webm, text/plain, application/json, application/xml, video/x-msvideo, video/x-matroska',
            addRemoveLinks: true,
            dictRemoveFile: "Quitar",
            parallelUploads: 1
        })

        myDropzone.on('addedfile', file => {
            let tiempo = document.getElementById("time").value;
            let fileData = new FormData();
            fileData.append('file', file);
            fileData.append('tiempo', tiempo);
            fetch('/safebox/app/controllers/upload.php', {
                method: 'POST',
                body: fileData
            }).then(res => res.json()).then(data => {
                const obj = JSON.parse(JSON.stringify(data));
                if (obj.error == 0) {
                    let arrtag = document.getElementsByTagName("span");
                    let spanbueno = null;
                    for (i = 0; i < arrtag.length; i++) {
                        if (arrtag[i].textContent == obj.nombreviejo) {
                            spanbueno = arrtag[i];
                        }
                    }
                    spanbueno.textContent = obj.nombre;
                    spanbueno.addEventListener("click", function() {
                        let copiar = this.textContent;
                        navigator.clipboard.writeText("https://flo.no-ip.info/uploads/"+copiar);
                        //navigator.clipboard.writeText("http://localhost/uploads/"+copiar);
                    });
                } else {
                    alert(obj.error);
                    myDropzone.removeFile(file);
                }
            })
        })
    }   
});

