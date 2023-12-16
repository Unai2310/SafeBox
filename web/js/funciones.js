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

function cambiarVisibilidad() {
    if (confirm("¿Quieres cambiar la visibilidad los archivos marcados?")) {
        let chkbs = document.getElementsByClassName("chckbs");
        let token = document.getElementById("csrftoken").value;
        let marcados = [];
        for(i=0;i<chkbs.length;i++) {
            if (chkbs[i].checked == true) {
                marcados.push(chkbs[i].value);
            }
        }
        let fileData = new FormData();
        fileData.append('file', marcados);
        fileData.append('csrf', token);
        fileData.append('op', "cambio");
        fetch('/safebox/app/controllers/cambios.php', {
            method: 'POST',
            body: fileData
        }).then(res => res.json()).then(data => {
            const obj = JSON.parse(JSON.stringify(data));
            document.location.href="?orden=vista&order=oldest&csrf="+obj.csrf;
        })
    }
}

function borrarArchivos() {
    if (confirm("¿Quieres seguro eliminar los archivos marcados?")) {
        let chkbs = document.getElementsByClassName("chckbs");
        let token = document.getElementById("csrftoken").value;
        let marcados = [];
        for(i=0;i<chkbs.length;i++) {
            if (chkbs[i].checked == true) {
                marcados.push(chkbs[i].value);
            }
        }
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
                    spanbueno.addEventListener("mouseover",(event) => {
                        let textog = spanbueno.textContent;
                        spanbueno.textContent = "HAZ CLICK PARA COPIAR";
                        setTimeout(() => {
                            spanbueno.textContent = textog;
                        }, 1000);
                        },
                        false,);
                } else {
                    alert(obj.error);
                    myDropzone.removeFile(file);
                }
            })
        })
    }   
});

