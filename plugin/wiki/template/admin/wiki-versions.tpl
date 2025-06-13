<!-- templates/admin/wiki-versions.tpl -->
<h1>Historique des versions pour la page : {{ page.title }}</h1>
<div class="admin-menu">
    {{ adminMenu }}
</div>
{% if versions %}
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Date de mise à jour</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {% for version in versions %}
            <tr>
                <td>{{ version.versionIndex }}</td>
                <td>{{ version.title }}</td>
                <td>{{ version.updated_at }}</td>
                <td>
                    <a href="{{ router.generate("admin-wiki-versions-view") ~ "?page=" ~ page.filename ~ "&version=" ~ version.versionIndex }}">Voir</a>
                    <a href="{{ router.generate("admin-wiki-versions-restore") ~ "?page=" ~ page.filename ~ "&version=" ~ version.versionIndex }}" onclick="return confirm('Restaurer cette version ?')">Restaurer</a>
                </td>
            </tr>
            {% endfor %}
        </tbody>
    </table>
{% else %}
    <p>Aucune version précédente.</p>
{% endif %}
<a href="{{ router.generate("admin-wiki-edit") ~ "?page=" ~ page.filename }}">Retour à l'édition</a>
