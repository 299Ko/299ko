<script>
    function insertAtCursor(myField, myValue) {
    //IE support
    alert(myField);
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
}

    function onSetFilename(data) {
        let fileName = data.value.split("\\").pop();
        document.getElementById("custom-file-label").innerText = fileName;
    }

    function uploadFile() {
        const image_files = document.getElementById('customFile').files;
        document.getElementById("filesProgressAjax").style.visibility = 'visible';
        document.getElementById("btnUpload").setAttribute('aria-busy', true);
        document.getElementById("btnUpload").setAttribute('disabled', true);
        if (image_files.length) {
            let formData = new FormData();
            formData.append('image', image_files[0]);
            let xhr = new XMLHttpRequest();
            xhr.open("POST", '{{ formSubmitUrl }}', true);
            xhr.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    let percentComplete = e.loaded / e.total * 100;
                    document.getElementById("filesProgressAjax").value = percentComplete;
                }
            }, false);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const data = JSON.parse(this.responseText);
                    if (data.success === 0) {
                        alert("Image Uploading failed. Try again..");
                    } else {
                        Fancybox.close(true);
                        document.getElementById('filesmanagerMediaManagerButton' + data.button).click();
                    }
                }
            };
            xhr.send(formData);
        } else {
            alert("No image selected");
        }
    }
</script>