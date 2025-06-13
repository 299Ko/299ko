    {{ adminMenu }}
<table border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Page Mère</th>
            <!-- <th>Date de Création</th>-->
            <th>Last update</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {% for page in pagesTree %}
        <tr>
            <td>{{ page.title }}</td>
            <td>{{ page.categoryName }}</td>
            <td>{{ page.parentTitle }}</td>
            <!-- <td>{{ page.created_at }}</td>-->
            <td>{{ page.updated_at }}</td>
            <td class="wikiAction">
                <a href="{{ router.generate("admin-wiki-edit") }}?page={{ page.filename }}"><i title="Editer" class="fa-solid fa-pen-to-square"></i></a>
                <a href="{{ router.generate("admin-wiki-delete") }}?page={{ page.filename }}" onclick="return confirm('Supprimer cette page ?');"><i title="Supprimer" class="fa-solid fa-trash"></i></a>
                <a href="{{ router.generate("admin-wiki-versions") }}?page={{ page.filename }}"><i title="Versions" class="fa-solid fa-history"></i></a>
            </td>
        </tr>
        {% if page.children %}
            {% for child in page.children %}
            <tr>
                <td>&nbsp;&nbsp;&nbsp;-- {{ child.title }}</td>
                <td>{{ child.categoryName }}</td>
                <td>{{ child.parentTitle }}</td>
                <!--<td>{{ child.created_at }}</td>-->
                <td>{{ child.updated_at }}</td>
                <td class="wikiAction">
                    <a href="{{ router.generate("admin-wiki-edit") }}?page={{ child.filename }}"><i title="Editer" class="fa-solid fa-pen-to-square"></i></a>
                    <a href="{{ router.generate("admin-wiki-delete") }}?page={{ child.filename }}" onclick="return confirm('Supprimer cette page ?');"><i title="Supprimer" class="fa-solid fa-trash"></i></a>
                    <a href="{{ router.generate("admin-wiki-versions") }}?page={{ child.filename }}"><i title="Versions" class="fa-solid fa-history"></i></a>
                </td>
            </tr>
            {% endfor %}
        {% endif %}
        {% endfor %}
    </tbody>
</table>
<a href="{{ router.generate("admin-wiki-edit") }}" class="button"><i title="Nouvelle page" class="fa-solid fa-file-lines"></i> Nouvelle Page</a>
