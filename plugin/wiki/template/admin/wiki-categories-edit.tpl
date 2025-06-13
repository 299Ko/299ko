<!-- templates/admin/wiki-categories-edit.tpl -->
<h1>{{ category.id ? "Modifier la catégorie" : "Nouvelle catégorie" }}</h1>
<div class="admin-menu">
    {{ adminMenu }}
</div>
<form method="post" action="{{ router.generate("admin-wiki-categories-save") }}">
    <input type="hidden" name="id" value="{{ category.id }}">
    <label>Nom :</label>
    <input type="text" name="name" value="{{ category.name }}">
    <br>
    <label>Catégorie parente :</label>
    <select name="parent">
        <option value="">-- Aucune --</option>
        {% for cat in allCategories %}
            {% if cat.id != category.id %}
                <option value="{{ cat.id }}" {% if category.parent == cat.id %}selected{% endif %}>{{ cat.name }}</option>
            {% endif %}
        {% endfor %}
    </select>
    <br>
    <button type="submit">Enregistrer</button>
</form>