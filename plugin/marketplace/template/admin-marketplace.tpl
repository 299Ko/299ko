<div class="msg info">
    <p>{{ Lang.marketplace.note }}</p>
    <a href="javascript:" class="msg-button-close"><i class="fa-solid fa-xmark"></i></a>
</div>
<section>
    <a href="{{ pluginsPageUrl }}" class="button">
        <i title="Plugins" class="fa-solid fa-puzzle-piece"></i> {{ Lang.marketplace.plugins }}
    </a>
    <a href="{{ themesPageUrl }}" class="button">
        <i class="fa-solid fa-panorama"></i> {{ Lang.marketplace.themes }}
    </a>
</section>
<div class="home-list">
    <section>
        <h2>{{ Lang.marketplace.featured_plugins }}</h2>
        {% if randomPlugins %}
            <ul class="featured-list">
                {% for plugin in randomPlugins %}
                    <li>
                        {% if plugin.icon %}
                            <i class="{{ plugin.icon }}"></i>
                        {% endif %}
                        {% if plugin.name %}
                            {{ plugin.name }}
                        {% else %}
                            Nom non défini
                        {% endif %}
                    </li>
                {% endfor %}
            </ul>
            <p>
                <a href="{{ pluginsPageUrl }}">
                    {{ Lang.marketplace.view_all_plugins }}
                </a>
            </p>
        {% else %}
            <p>{{ Lang.marketplace.no_plugins }}</p>
        {% endif %}
    </section>

    <section>
        <h2>{{ Lang.marketplace.featured_themes }}</h2>
        {% if randomThemes %}
            <ul class="featured-list">
                {% for theme in randomThemes %}
                    <li>
                        {{ theme.name }}
                    </li>
                {% endfor %}
            </ul>
            <p>
                <a href="{{ themesPageUrl }}">
                    {{ Lang.marketplace.view_all_themes }}
                </a>
            </p>
        {% else %}
            <p>{{ Lang.marketplace.no_themes }}</p>
        {% endif %}
    </section>
</div>