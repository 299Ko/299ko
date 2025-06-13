<!-- templates/admin/wiki-categories.tpl -->
<h1>Gestion des catégories</h1>
<div class="admin-menu">
    {{ adminMenu }}
</div>
<ul>
    {% for cat in categoriesTree %}
        <li>
            {{ cat.name }}
            <a href="{{ router.generate("admin-wiki-categories-edit") }}?id={{ cat.id }}"><i title="Editer" class="fa-solid fa-pen-to-square"></i></a>
            <a href="{{ router.generate("admin-wiki-categories-delete") }}?id={{ cat.id }}" onclick="return confirm('Supprimer cette catégorie ?');"><i title="Supprimer" class="fa-solid fa-trash"></i></a>
        </li>
        {% if cat.children %}
            <ul>
                {% for child in cat.children %}
                    <li>&nbsp;&nbsp;&nbsp;|-- {{ child.name }}
                        <a href="{{ router.generate("admin-wiki-categories-edit") }}?id={{ child.id }}"><i title="Editer" class="fa-solid fa-pen-to-square"></i></a>
                        <a href="{{ router.generate("admin-wiki-categories-delete") }}?id={{ child.id }}" onclick="return confirm('Supprimer cette catégorie ?');"><i title="Supprimer" class="fa-solid fa-trash"></i></a>
                    </li>
                {% endfor %}
            </ul>
        {% endif %}
    {% endfor %}
</ul>
<a href="{{ router.generate("admin-wiki-categories-edit") }}" class="button"><i title="Nouvelle catégorie" class="fa-solid fa-tags"></i> Nouvelle catégorie</a>