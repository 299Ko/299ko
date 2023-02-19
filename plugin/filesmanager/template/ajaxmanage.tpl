<div id="filesmanagerFiles">
    <article>
        <header>
            <label id="custom-file-label" for="customFile">Ajouter un fichier</label>
            <input type="file" name="image_file" id="customFile" onchange="onSetFilename(this)">
            <progress value="0" max="100" id="filesProgressAjax"></progress>
            <button id="btnUpload" type="button" onclick="uploadFile()">Upload File</button>
        </header>

    {% if files %}
        {% FOR fileid,file IN files %}

            <div class="filesmanagerBlockFile {% if file.isPicture %} filesmanagerIsPic" style="background-image: url('{{ file.url }}'){% endif %}">
                <div class="filesmanagerFileDetails">
                    <span class="filesmanagerDetailsName">{{file.originalName}}</span>
                    <div class="filesmanagerDetailsLinks">
                        <a onclick="navigator.clipboard.writeText({{file.url}});
                            return false;" title="{{file.originalName}}"><i class="bi bi-clipboard-check"></i>
                        </a>
                    </div>
                    
                </div>
                <div>
                    
                </div>
                <div>

                </div>

            </div>
            
        {% ENDFOR %}
            <div id="filesmanagerEndBlocksFile">
                
            </div>

    {% else %}
        rrr
    {% endif %}
    </article>
</div>


fff