{% IF mode === list %}
    <ul class="items {% IF runPlugin.getConfigVal[hideContent] %}simple{% ENDIF %}">
        {% FOR k,v in news %}
            <li class="item">
                <div class="item-header">
                    <h2>
                        <a href="{{ v.url }}">{{ v.name }}</a>
                    </h2>
                    <span class="item-date"><i class="bi bi-calendar-date"></i>{{ v.date }}</span>
                    <span class="item-categories"><i class="bi bi-folder2-open"></i>
                        {% FOR cat in v.cats %}
                            <a href="{{ cat.url }}">{{ cat.label }}</a>
                        {% ENDFOR %}
                    </span>
                    {% IF runPlugin.getConfigVal[comments] %}
                        {% IF v.commentsOff != 1 %}
                            <span class="item-comments"><i class="bi bi-chat"></i>
                                {{ newsManager.countComments[v.id] }} commentaire{% IF newsManager.countComments[v.id] > 1 %}s{% ENDIF %}
                            </span>
                        {% ENDIF %}
                    {% ENDIF %}
                </div>
                {% IF runPlugin.getConfigVal[hideContent] == 0 %}
                    {% IF pluginsManager.isActivePlugin[galerie] && galerie.searchByFileName[v.img] %}
                        <img class="featured" src="{{ v.imgUrl }}" alt="{{ v.img }}" />
                    {% ENDIF %}
                    {{ v.content }}
                {% ENDIF %}
            </li>
        {% ENDFOR %}
    </ul>
    <ul class="pagination">
        {% FOR k,v IN pagination %}
            <li><a href="{{ v.url }}">{{ v.num }}</a></li>
        {% ENDFOR %}
    </ul>
{% ELSEIF mode == read %}
    <div class="item-header">
        <span class="item-date">
            <i class="bi bi-calendar-date"></i> {{ util.formatDate[item.getDate, en, fr] }}
        </span>
        <span class="item-categories"><i class="bi bi-folder2-open"></i>
            {% FOR cat in cats %}
                <a href="{{ cat.url }}">{{ cat.label }}</a>
            {% ENDFOR %}
        </span>
        {% IF runPlugin.getConfigVal[comments] && item.getCommentsOn %}
            <span class="item-comments"><i class="bi bi-chat"></i>{{ newsManager.countComments }} commentaire{% IF newsManager.countComments[item.getId] > 1 %}s{% ENDIF %}</span>
        {% ENDIF %}
        <span class="item-return">
            <a href="{{ runPlugin.getPublicUrl }}">Retour à la liste</a>
        </span>
    </div>
    <div class="content">
        {% IF pluginsManager.isActivePlugin[galerie] && galerie.searchByFileName[item.getImg] %}
            <img class="featured" src="{{ item.getImgUrl }}" alt="{{ item.getName }}" />
        {% ENDIF %}
        {{ item.getContent }}
    </div>
    {% IF runPlugin.getConfigVal[comments] %}
        {% IF item.commentsOff != 1 %}
            <h2>Commentaires</h2>
            {% IF newsManager.countComments == 0 %}
                <p>Il n'y a pas de commentaires</p>
            {% ELSEIF newsManager.countComments > 0 %}
                {% FOR k,v in newsManager.getComments %}
                    <p class="comment" id="comment{{ v.getId }}">{{ nl2br[v.getContent] }}<br>
                        <span class="infos">{{ v.getAuthor }} | {{ util.formatDate[v.getDate, en, fr] }}</span>
                    </p>
                {% ENDFOR %}
            {% ENDIF %}
            <h2>Ajouter un commentaire</h2>
            <form method="post" action="{{ runPlugin.getPublicUrl }}send.html">
                <input type="hidden" name="id" value="{{ item.getId }}" />
                <input type="hidden" name="back" value="{{ runPlugin.getPublicUrl }}{{ util.strToUrl[item.getName] }}-{{ item.getId }}.html" />
                <p>
                    <label>Pseudo</label><br>
                    <input style="display:none;" type="text" name="_author" value="" />
                    <input type="text" name="author" required="required" />
                </p>
                <p><label>Email</label><br><input type="text" name="authorEmail" required="required" /></p>
                <p><label>Commentaire</label><br><textarea name="content" required="required"></textarea></p>
                    {{ antispamField }}
                <p><input type="submit" value="Enregistrer" /></p>
            </form>
        {% ENDIF %}
    {% ENDIF %}
{% ELSEIF mode == list_empty %}
    <p>Aucun élément n'a été trouvé.</p>
{% ENDIF %}
