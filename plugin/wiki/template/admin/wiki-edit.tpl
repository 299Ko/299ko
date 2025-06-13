<!-- templates/admin/wiki-edit.tpl -->
<div class="admin-menu">
    {{ adminMenu }}
</div>
{% if page.filename %}
    <p><a href="{{ router.generate("admin-wiki-versions") }}?page={{ page.filename }}">Voir les versions</a></p>
{% endif %}
<form method="post" action="{{ router.generate("admin-wiki-save") }}">
    <input type="hidden" name="filename" value="{{ page.filename }}">
    <label>Titre :</label>
    <input type="text" name="title" value="{{ page.title }}">
    <br>
    <label>Catégorie :</label>
    <select name="category">
        <option value="">-- Sélectionner --</option>
        {% for cat in categories %}
            <option value="{{ cat.id }}" {% if page.category == cat.id %}selected{% endif %}>{{ cat.name }}</option>
        {% endfor %}
    </select>
    <br>
    <label>Parent :</label>
    <select name="parent">
        <option value="">-- Aucune --</option>
        {% for p in parentPages %}
            <option value="{{ p.filename }}" {% if page.parent == p.filename %}selected{% endif %}>
                {{ p.title }}
            </option>
        {% endfor %}
    </select>
    <br>
    <label>Brouillon :</label>
    <input type="checkbox" name="draft" value="1" {% if page.draft %}checked{% endif %}>
    <br>
    <label>Contenu :</label>
    {{ contentEditor }}
    <br>
    <button type="submit">Enregistrer</button>
</form>
