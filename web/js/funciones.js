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
            maxFileSize: 1000000000,
            acceptedFiles: 'image/jpeg, image/png',
            addRemoveLinks: true,
            dictRemoveFile: "Quitar"
        })

        myDropzone.on('addedfile', file => {
            let fileData = new FormData();
            fileData.append('file', file);
            fetch('/safebox/app/controllers/upload.php', {
                method: 'POST',
                body: fileData
            }).then(res => res.json()).then(data => {
                console.log(data);
                const obj = JSON.parse(JSON.stringify(data));
                let arrtag = document.getElementsByTagName("span");
                let spanbueno = null;
                for (i = 0; i < arrtag.length; i++) {
                    if (arrtag[i].textContent == obj.loco) {
                        spanbueno = arrtag[i];
                    }
                }
                spanbueno.textContent = data.name;
                spanbueno.addEventListener("click", function() {
                    let copiar = this.textContent;
                    let input = document.createElement('input');
                    input.value = copiar;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove()
                    alert("Copiado");
                });
            })
        })
    }   
});

