<div class="msg info">
    <p>{{ Lang.marketplace.note }}</p>
    <a href="javascript:" class="msg-button-close"><i class="fa-solid fa-xmark"></i></a>
</div>
<section>
    <a href="{{ ROUTER.generate("admin-marketplace") }}" class="button"><i class="fa-solid fa-store"></i> {{ Lang.marketplace.home }}</a>
    <a href="{{ ROUTER.generate("marketplace-plugins") }}" class="button"><i class="fa-solid fa-panorama"></i> {{ Lang.marketplace.plugins }}</a>
</section>
<div class="market-list">
    {% if themes %}
        {% for theme in themes %}
            <section class="market-item">
                <header>
                    <h2>
                        {% if theme.icon %}
                            <i class="{{ theme.icon }}"></i>
                        {% endif %}
                        {{ theme.name }}
                    </h2>
                </header>
                <div>
                    <div>
                        <strong>{{ Lang.marketplace.list_desc }} :</strong> {{ theme.description }}
                    </div>
                    <div>
                        <strong>{{ Lang.marketplace.version }} :</strong> {{ theme.version }}
                    </div>
                    <div>
                        <strong>{{ Lang.marketplace.author }} :</strong> {{ theme.authorEmail }}
                    </div>
                    {% if theme.authorWebsite %}
                        <div>
                            <strong>{{ Lang.marketplace.website }} :</strong>
                            <a href="{{ theme.authorWebsite }}" target="_blank">
                                {{ theme.authorWebsite }}
                            </a>
                        </div>
                    {% endif %}
                </div>
                <footer>
                    {% if theme.is_installed %}
                        {% if theme.update_needed %}
                            <span class="update-icon" title="Mise à jour disponible / Update available">&#x21bb;</span>
                            <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ theme.directory }}&type={% if theme.type %}{{ theme.type }}{% else %}theme{% endif %}&commit={{ theme.CommitGithubSHA }}" class="download-btn">
                                {{ Lang.marketplace.update }}
                            </a>
                        {% else %}
                            <span class="up-to-date-icon" title="Theme à jour / Theme is up-to-date">&#x2714;</span>
                        {% endif %}
                    {% else %}
                        <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ theme.directory }}&type={% if theme.type %}{{ theme.type }}{% else %}theme{% endif %}&commit={{ theme.CommitGithubSHA }}" class="button">
                            {{ Lang.marketplace.install }}
                        </a>
                    {% endif %}
                </footer>
            </section>
        {% endfor %}
    {% else %}
        <p>{{ Lang.marketplace.no_themes }}</p>
    {% endif %}
</div>
