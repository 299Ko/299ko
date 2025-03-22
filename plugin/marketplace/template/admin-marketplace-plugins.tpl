<div class="msg info">
    <p>{{ Lang.marketplace.note }}</p>
    <a href="javascript:" class="msg-button-close"><i class="fa-solid fa-xmark"></i></a>
</div>
<section>
    <a href="{{ ROUTER.generate("admin-marketplace") }}" class="button"><i class="fa-solid fa-store"></i> {{ Lang.marketplace.home }}</a>
    <a href="{{ ROUTER.generate("marketplace-themes") }}" class="button"><i class="fa-solid fa-panorama"></i> {{ Lang.marketplace.themes }}</a>
</section>
<div class="market-list">
    {% if plugins %}
        {% for plugin in plugins %}
            <section class="market-item">
                <header>
                    <h2>
                        {% if plugin.icon %}
                            <i class="{{ plugin.icon }}"></i>
                        {% endif %}
                        {{ plugin.name }}
                    </h2>
                </header>
                <div>
                <div>
                    <strong>{{ Lang.marketplace.list_desc }} :</strong> {{ plugin.description }}
                </div>
                <div>
                    <strong>{{ Lang.marketplace.version }} :</strong> {{ plugin.version }}
                </div>
                <div>
                    <strong>{{ Lang.marketplace.author }} :</strong> {{ plugin.authorEmail }}
                </div>
                {% if plugin.authorWebsite %}
                    <div>
                        <strong>{{ Lang.marketplace.website }} :</strong>
                        <a href="{{ plugin.authorWebsite }}" target="_blank">
                            {{ plugin.authorWebsite }}
                        </a>
                    </div>
                {% endif %}
                </div>
                <footer>
                    {% if plugin.is_installed %}
                        {% if plugin.update_needed %}
                            <span class="update-icon" title="Mise à jour disponible / Update available">&#x21bb;</span>
                            <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ plugin.directory }}&type={% if plugin.type %}{{ plugin.type }}{% else %}plugin{% endif %}&commit={{ plugin.CommitGithubSHA }}" class="download-btn">
                                {{ Lang.marketplace.update }}
                            </a>
                        {% else %}
                            <span class="up-to-date-icon" title="Plugin à jour / Plugin is up-to-date">&#x2714;</span>
                        {% endif %}
                    {% else %}
                        <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ plugin.directory }}&type={% if plugin.type %}{{ plugin.type }}{% else %}plugin{% endif %}&commit={{ plugin.CommitGithubSHA }}" class="button">
                            {{ Lang.marketplace.install }}
                        </a>
                    {% endif %}
                </footer>
            </section>
        {% endfor %}
    {% else %}
        <p>{{ Lang.marketplace.no_plugins }}</p>
    {% endif %}
</div>
