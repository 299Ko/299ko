<section id='fm-listview-container'>
    <header>
        <label id="custom-file-label" for="customFile">{{ Lang.filemanager.add-file }}</label>
        <input type="file" name="image_file" id="customFile" onchange="onSetFilename(this)">
        <input id='dirInput' type='hidden' value='{{ dir }}'>
        <button id="btnUpload" type="button" onclick="uploadFile()">{{ Lang.filemanager.send-file }}</button>
        <progress value="0" max="100" id="filesProgressAjax"></progress>
    </header>
    <div id='fm-breadcrumb-container'>
        <div id='fm-breadcrumb'>
            <a onclick='refreshView("Back%To%Home%")'><i class="fa-solid fa-house"></i></a>
            {% set fullPath = "" %}
            {% for item in dirParts %}
                {% if item != "" %}
                    {% set fullPath = fullPath ~ item ~ "/" %}
                    <a onclick="refreshView('{{ fullPath }}')">{{ item }}</a>
                {% endif %}
            {% endfor %}
        </div>
        <div id='fm-add-folder'>
            <a class="button" onClick='displayNewFolder()'><i class="fa-solid fa-folder-plus"></i> {{ Lang.filemanager.add-folder }}</a>
        </div>
    </div>

    <div id="fm-listview">
        <div id="fm-view-files">
            {% if dir != "" %}
                <div class='fm-thumb'>
                    <div class='fm-folder' onClick='refreshView("{{ dir }}/..")'>
                        <i class="fa-solid fa-arrow-turn-up"></i>
                        <p class='fm-title' title='{{ Lang.filemanager.go-up }}'>..</p>
                    </div>
                    <div class='fm-actions'>
                        {{ Lang.filemanager.parent-folder }}
                    </div>
                </div>
            {% endif %}
            {% for item in manager.getFolders() %}
                <div class='fm-thumb' id='{{ item.name }}'>
                    <div class='fm-folder' title='{{ item.name }}' onClick='refreshView("{{ dir }}/{{ item.name }}")'>
                        <i class="fa-regular fa-folder-open"></i>
                        <p class='fm-title' title='{{ item.name }}'>{{ item.name }}</p>
                    </div>
                    <div class='fm-actions'>
                        <a class="fm-link alert" href="javascript:deleteFolder('{{ item.name }}')"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
            {% endfor %}

            {% for item in manager.getFiles() %}
                <div class='fm-thumb' id='{{ item.name }}'>
                    {% if item.isPicture() %}
                        <div class='fm-picture'>
                            <a style="background-image: url('{{ item.getUrl() }}')" class="fm-link" href="{{ item.getUrl() }}" data-fancybox data-caption="{{ item.name }}" title="{{ item.name }}"></a>
                        </div>
                    {% else %}
                        <div class='fm-file'>
                            <i class="fa-regular fa-file" title="{{ item.name }}"></i>
                        </div>
                    {% endif %}
                    <p class='fm-title' title='{{ item.name }}'>{{ item.name }}</p>
                    <div class='fm-actions'>
                        <a class="fm-link" onClick="copyToClipboard('{{ item.getUrl() }}')"><i class="fa-regular fa-clipboard"></i></a>
                        <a class="fm-link" href="{{ item.getUrl() }}"><i class="fa-solid fa-link"></i></a>
                        <a class="fm-link alert" href="javascript:deleteFile('{{ item.name }}')"><i class="fa-solid fa-trash"></i></a>
                        {% if item.isPicture() && editor != false %}
                            <a onclick="insertImgInEditor('{{editor}}', '{{item.getRelUrl()}}')" ><i class="fa-solid fa-laptop-code"></i></a>
                        {% endif %}
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
</section>

<section id="fmAddAFolder">
    <header>
        {{ Lang.filemanager.add-folder }}
    </header>
    <label for="addFolderName">{{ Lang.filemanager.folder-name }}</label>
    <input id="addFolderName" name="addFolderName" type="text" />
    <button class="success" id="addFolderNameValidate" data-fancybox-close tabindex="-1" onClick="goCreateFolder(document.getElementById('addFolderName').value)">{{ Lang.filemanager.add-folder }}</button>
</section>

<form id="fmRedirectFlat" method="POST" action="{{ redirectUrl }}">
    <input type="text" id="fmCurrDir" name="fmCurrDir" value="{{ dir }}" />
    <input type="text" id="fmFolderToSee" name="fmFolderToSee" value="" />
</form>
<script>
    function insertImgInEditor(editorId, imgUrl) {
        if (typeof processInsertImgInEditor === 'function') {
            processInsertImgInEditor(editorId, imgUrl);
        } else {
            insertAtCursor(editorId, imgUrl);
        }
        {% if ajaxView %}
            Fancybox.close(false);
        {% endif %}
    }
    function insertAtCursor(myFieldId, myValue) {
        let myField = document.getElementById(myFieldId);
    //IE support
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

    function displayNewFolder() {
        Fancybox.show([{src: "#fmAddAFolder", type: "inline"}]);
    }

    function goCreateFolder(folderName) {
        if (folderName === '') {
            Toastify({
                text: "{{ Lang.filemanager.folder-name-empty }}",
                className: "error"
            }).showToast();
            Fancybox.show([{src: "#fmAddAFolder", type: "inline"}]);
            return;
        }

        let formData = new FormData();
        formData.append('folderName', folderName);
        formData.append('dir', document.getElementById("dirInput").value);
        let xhr = new XMLHttpRequest();
        xhr.open("POST", '{{ createUrl }}', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const data = JSON.parse(this.responseText);
                if (data.success === 0) {
                    Toastify({
                        text: "{{ Lang.filemanager.folder-creation-failed }}",
                        className: "error"
                    }).showToast();
                } else {
                    Toastify({
                        text: "{{ Lang.filemanager.folder-created }}",
                        className: "success"
                    }).showToast();
                }
            }
        };
        xhr.send(formData);

        refreshView("{{ dir }}/" + folderName);
    }

    function copyToClipboard(text) {
        if (!navigator.clipboard) {
            Toastify({
                text: "{{ Lang.filemanager.clipboard-unavailable }}",
                className: "error"
            }).showToast();
        } else {
            navigator.clipboard.writeText(text).then(() => {
                Toastify({
                    text: "{{ Lang.filemanager.copied-to-clipboard }}",
                    className: "success"
                }).showToast();
            }, () => {
                Toastify({
                    text: "{{ Lang.filemanager.copy-failed }}",
                    className: "error"
                }).showToast();
            });
        }
    }

    function refreshView(folderName) {
        {% if ajaxView %}
            let data = "fmFolderToSee=" + folderName + "&token={{ token }}&editor={{ editor}}";
            Fancybox.close(false);
                new Fancybox([
                {
                    src: '{{ redirectAjaxUrl }}',
                    type: "ajax",
                    ajax : data
                },
                ],);
        {% else %}
            document.querySelector('#fmFolderToSee').value = folderName;
            document.querySelector('#fmRedirectFlat').submit();
        {% endif %}
    }

    function deleteFile(name) {
        if (!confirm("{{ Lang.confirm.deleteItem }}")) {
            return;
        }

        let formData = new FormData();
        formData.append('filename', name);
        formData.append('fmFolderToSee', document.getElementById("dirInput").value);
        let xhr = new XMLHttpRequest();
        xhr.open("POST", '{{ deleteUrl }}', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const data = JSON.parse(this.responseText);
                if (data.success === 0) {
                    Toastify({
                        text: "{{ Lang.core-item-not-deleted }}" + name,
                        className: "error"
                    }).showToast();
                } else {
                    Toastify({
                        text: "{{ Lang.core-item-deleted }}" + name,
                        className: "success"
                    }).showToast();
                    const item = document.getElementById(name);
                    fadeOut(item);
                    setTimeout(function () {
                        item.remove();
                    }, 1000);
                }
            }
        };
        xhr.send(formData);
    }

    function deleteFolder(name) {
        if (!confirm("{{ Lang.confirm.deleteItem }}")) {
            return;
        }

        let formData = new FormData();
        formData.append('foldername', name);
        formData.append('fmFolderToSee', document.getElementById("dirInput").value);
        let xhr = new XMLHttpRequest();
        xhr.open("POST", '{{ deleteUrl }}', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const data = JSON.parse(this.responseText);
                if (data.success === 0) {
                    Toastify({
                        text: "{{ Lang.core-item-not-deleted }}" + name,
                        className: "error"
                    }).showToast();
                } else {
                    Toastify({
                        text: "{{ Lang.core-item-deleted }}" + name,
                        className: "success"
                    }).showToast();
                    const item = document.getElementById(name);
                    fadeOut(item);
                    setTimeout(function () {
                        item.remove();
                    }, 1000);
                }
            }
        };
        xhr.send(formData);
    }

    function uploadFile() {
        const image_files = document.getElementById('customFile').files;
        document.getElementById("filesProgressAjax").style.visibility = 'visible';
        if (image_files.length) {
            document.getElementById("filesProgressAjax").style.display = "block";
            document.getElementById("btnUpload").innerHTML = '<i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>';
            document.getElementById("btnUpload").setAttribute('disabled', true);
            let formData = new FormData();
            formData.append('image', image_files[0]);
            formData.append('fmFolderToSee', document.getElementById("dirInput").value);
            let xhr = new XMLHttpRequest();
            xhr.open("POST", '{{ uploadUrl }}', true);
            xhr.upload.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    let percentComplete = e.loaded / e.total * 100;
                    document.getElementById("filesProgressAjax").value = percentComplete;
                }
            }, false);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const data = JSON.parse(this.responseText);
                    if (data.success === 0) {
                        Toastify({
                            text: "{{ Lang.filemanager.upload-failed }}",
                            className: "error"
                        }).showToast();
                    } else {
                        refreshView("{{ dir }}");
                    }
                }
            };
            xhr.send(formData);
        } else {
            Toastify({
                text: "{{ Lang.filemanager.no-file-selected }}",
                className: "error"
            }).showToast();
        }
    }
</script>
