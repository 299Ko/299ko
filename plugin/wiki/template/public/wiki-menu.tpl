{# Template pour le menu du wiki #}
<div class="wiki-search">
    <form method="get" action="{{ baseUrl }}" class="search-form">
        <div class="search-input-wrapper">
            <input type="text" name="q" placeholder="Rechercher dans le wiki..." value="{{ searchQuery }}" class="search-input">
            <button type="submit" class="search-button" aria-label="Rechercher">
                <i class="fas fa-magnifying-glass"></i>
            </button>
        </div>
    </form>
</div>

<ul class="wiki-menu">
    {# Afficher les catégories principales #}
    {% FOR category IN menuData.categories %}
        <li class="wiki-menu-category">
            <button type="button" class="wiki-menu-category-button" data-category-id="{{ category.id }}">
                <i class="fas fa-folder"></i>
                {{ category.name }}
            </button>
            
            {# Afficher les pages de la catégorie #}
            {% IF category.pages %}
                <ul class="wiki-menu-pages">
                    {% FOR page IN category.pages %}
                        <li>
                            <a href="{{ baseUrl }}?page={{ page.id }}">
                                <i class="fas fa-file"></i>
                                {{ page.title }}
                            </a>
                        </li>
                    {% ENDFOR %}
                </ul>
            {% ENDIF %}
            
            {# Afficher les sous-catégories #}
            {% IF category.children %}
                <ul class="wiki-menu-subcategories">
                    {% FOR subcategory IN category.children %}
                        <li class="wiki-menu-category">
                            <button type="button" class="wiki-menu-category-button" data-category-id="{{ subcategory.id }}">
                                <i class="fas fa-folder"></i>
                                {{ subcategory.name }}
                            </button>
                            
                            {# Afficher les pages de la sous-catégorie #}
                            {% IF subcategory.pages %}
                                <ul class="wiki-menu-pages">
                                    {% FOR page IN subcategory.pages %}
                                        <li>
                                            <a href="{{ baseUrl }}?page={{ page.id }}">
                                                <i class="fas fa-file"></i>
                                                {{ page.title }}
                                            </a>
                                        </li>
                                    {% ENDFOR %}
                                </ul>
                            {% ENDIF %}
                        </li>
                    {% ENDFOR %}
                </ul>
            {% ENDIF %}
        </li>
    {% ENDFOR %}
</ul> 