<!-- templates/public/wiki-view.tpl -->
<style>
.wiki {
    display: flex;
    flex-direction: row-reverse;
    flex-wrap: wrap;
    align-content: stretch;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0 20px;
}

.wiki-content {
    flex: 1;
    margin-right: 20px;
}
.wiki-navigation {
    width: 250px;
}
</style>
<aside class="wiki-navigation">
<form method="get" action="{{ baseUrl }}">
    <input type="text" name="q" placeholder="Rechercher…" value="{{ Votre recherche }}">
    <button type="submit">Rechercher</button>
</form>
       <h2>Navigation</h2>
        {{ menu }}
</aside>
<div class="wiki-content">
{% if pages %}
    <ul>
        {% for page in pages %}
            <li>
                <a href="{{ baseUrl }}?page={{ page.filename }}">{{ page.title }}</a>
            </li>
        {% endfor %}
    </ul>
{% else %}
    <p>Aucune page trouvée.</p>
{% endif %}</div>

