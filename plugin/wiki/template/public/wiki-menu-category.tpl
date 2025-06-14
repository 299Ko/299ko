{# Template pour les sous-catégories du menu #}
{# Debug: {{ category|json_encode }} #}
<li class="wiki-menu-category">
    <button type="button" class="wiki-menu-category-button" data-category-id="{{ category.id }}">
        <i class="fas fa-folder"></i>
        {{ category.name }}
    </button>
    
    {# Pages de la catégorie #}
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
    
    {# Sous-catégories récursives #}
    <ul class="wiki-menu-subcategories">
        {% FOR child IN category.children %}
            {% INCLUDE wiki/template/public/wiki-menu-category.tpl WITH category = child %}
        {% ENDFOR %}
    </ul>
</li> 