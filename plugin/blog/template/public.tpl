{% IF mode === list %}
    <ul class="items {% IF runPlugin.getConfigVal[hideContent] %}simple{% ENDIF %}">
        {% FOR k,v in news %}
            <li class="item">
                <div class="item-header">
                    <h2>
                        <a href="{{ v.url }}">{{ v.name }}</a>
                    </h2>
                    <span class="item-date"><i class="fa-solid fa-calendar-days"></i>{{ v.date }}</span>
                    <span class="item-categories"><i class="fa-solid fa-folder-open"></i>
                        {% FOR cat in v.cats %}
                            <a href="{{ cat.url }}">{{ cat.label }}</a>
                        {% ENDFOR %}
                    </span>
                    {% IF runPlugin.getConfigVal[comments] %}
                    {% IF v.commentsOff != 1 %}
                        <span class="item-comments"><i class="fa-solid fa-comment"></i>
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
{% ELSEIF mode == list_empty %}
    <p>Aucun élément n'a été trouvé.</p>
{% ENDIF %}
