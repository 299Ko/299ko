{% IF mode === list %}
    <a role="button" href="index.php?p=blog&action=edit"><i class="bi bi-file-earmark-plus"></i> Ajouter un article</a>
    <a role="button" target="_blank" href="{{ runPlugin.getPublicUrl }}rss.html"><i class="bi bi-rss"></i> Flux RSS</a>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {% FOR k,v in newsManager.getItems %}
                <tr>
                    <td>{{ v.getName }}</td>
                    <td>{{ util.formatDate[v.getDate, en, fr] }}</td>
                    <td>
                        <a role="button" class="small" href="index.php?p=blog&action=edit&id={{ v.getId }}">
                            <i class="bi bi-pencil"></i> Editer l'article</a>
                        {%  IF newsManager.countComments[v.getId] > 0 %}
                            <a role="button" class="small" href="index.php?p=blog&action=listcomments&id={{ v.getId }}">
                                <i class="bi bi-chat"></i> Commentaires ({{ newsManager.countComments[v.getId] }})</a>
                        {% ENDIF %}
                        <a href="index.php?p=blog&action=del&id={{ v.getId }}&token={{ administrator.getToken }}" 
                           onclick = "if (!confirm('Supprimer cet élément ?'))
                                   return false;" role="button" class="small alert"><i class="bi bi-trash2"></i> Supprimer</a>
                    </td>
                </tr>
            {% ENDFOR %}
        </tbody>
    </table>
    {% ELSEIF mode === edit %}
        <form method="post" action="index.php?p=blog&action=save" enctype="multipart/form-data">
            {{ show.adminTokenField }}
            <input type="hidden" name="id" value="{{ news.getId }}" />
            {% IF pluginsManager.isActivePlugin[galerie] %}
                <input type="hidden" name="imgId" value="{{ news.getImg }}" />
            {% ENDIF %}
            <h3>Paramètres</h3>
            <p>
                <input {% IF news.getDraft %}checked{% ENDIF %} type="checkbox" name="draft" id="draft" />
                <label for="draft">Ne pas publier (brouillon)</label>
            </p>
            {% IF runPlugin.getConfigVal[comments] %}
                <p>
                    <input {% IF news.getCommentsOff %}checked{% ENDIF %} type="checkbox" name="commentsOff" id="commentsOff" /> 
                    <label for="commentsOff">Désactiver les commentaires pour cet article</label>
                </p>
            {% ENDIF %}
            <h3>Contenu</h3>
            <p>
                <label>Titre</label><br>
                <input type="text" name="name" value="{{ news.getName }}" required="required" />
            </p>
            {%  IF showDate %}
                <p>
                    <label for="date">Date</label><br>
                    <input placeholder="Exemple : 2017-07-06 12:28:51" type="date" name="date" 
                           id="date" value="{{ news.getDate }}" required="required" />
                </p>
            {% ENDIF %}
            <p>
                <label for="content">Contenu</label><br>
                <textarea name="content" id="content" class="editor">{% HOOK.FILTER.beforeEditEditor[news.getContent] %}</textarea>
            </p>

            {% IF pluginsManager.isActivePlugin[galerie] %}
                {{ galerieDisplaySidebarModule[news.getImg] }}
            {% ENDIF %}
            {% HOOK.ACTION.adminEditingAnItem[runPlugin.getName, news.getId] %}
            {{ show.displayAdminSidebar }}
            <p><button type="submit" class="button">Enregistrer</button></p>
        </form>
        {% ELSEIF mode === listcomments %}
            <ul class="tabs_style">
                <li><a class="button" href="index.php?p=blog">Retour</a></li>
            </ul>
            <table>
                <tr>
                    <th>Commentaire</th>
                    <th></th>
                </tr>
                {% FOR k,v IN newsManager.getComments %}
                    <tr>
                        <td>
                            {{ v.getAuthor }} <i>{{ v.getAuthorMail }}</i> - {{ util.formatDate[v.getDate, en, fr] }}</b> :<br><br>
                            <form id="comment{{ v.getId }}" method="post" 
                                  action="index.php?p=blog&action=updatecomment&id={{ GET.id }}&idcomment={{ v.getId }}&token={{ administrator.getToken }}">
                                <textarea name="content{{ v.getId }}"><?php echo $v->getContent(); ?></textarea></form>
                        </td>
                        <td>
                            <a onclick="updateComment({{ v.getId }});" href="javascript:" class="button">Enregistrer</a>
                            <a href="index.php?p=blog&action=delcomment&id={{ GET.id }}&idcomment={{ v.getId }}&token={{ administrator.getToken }}" onclick = "if (!confirm('Supprimer cet élément ?'))
                                        return false;" class="button alert">Supprimer</a>
                        </td>
                    </tr>
                {% ENDFOR %}
            </table>
            <script>
                function updateComment(id) {
                    document.getElementById('comment' + id).submit();
                }
            </script>
            {% ENDIF %}
