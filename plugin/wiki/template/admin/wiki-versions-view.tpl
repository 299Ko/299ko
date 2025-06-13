<!-- templates/admin/wiki-versions-view.tpl -->
<h1>Détail de la version pour la page : {{ page.title }}</h1>
<div class="admin-menu">
    {{ adminMenu }}
</div>
<p><strong>Titre:</strong> {{ version.title }}</p>
<p><strong>Contenu:</strong></p>
<div style="border:1px solid #ccc; padding:10px;">
    {{ version.content }}
</div>
<p><strong>Date de mise à jour:</strong> {{ version.updated_at }}</p>
<p><strong>Date de création:</strong> {{ version.created_at }}</p>
<p>
    <a href="{{ router.generate("admin-wiki-versions-restore") }}?page={{ page.filename }}&version={{ versionIndex }}" onclick="return confirm('Restaurer cette version ?')">Restaurer cette version</a>
    | <a href="{{ router.generate("admin-wiki-versions") }}?page={{ page.filename }}">Retour à l'historique</a>
</p>
