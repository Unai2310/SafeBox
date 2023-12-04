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

document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById('dropzoneSubida')) {
        let myDropzone = new Dropzone('.dropzone', {
            url: '/safebox', 
            maxFileSize: 209715200,
            acceptedFiles: 'image/jpeg, image/png, image/gif, audio/mpeg, video/mp4, application/pdf, video/webm'+
            'text/plain, application/json, application/xml, video/x-msvideo, video/x-matroska',
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
                console.log(data);
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
                        //navigator.clipboard.writeText("http://flo.no-ip.info/uploads/"+copiar);
                        navigator.clipboard.writeText("http://localhost/uploads/"+copiar);
                    });
                } else {
                    alert(obj.error);
                    myDropzone.removeFile(file);
                }
            })
        })
    }   
});

