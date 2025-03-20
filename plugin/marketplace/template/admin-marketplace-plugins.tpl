<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 *
 * admin-marketplace-plugins.tpl
 *
 * FR: Affiche la liste complète des plugins du marketplace.
 * EN: Displays the full list of marketplace plugins.
 *
 * Les données sont fournies par le contrôleur via la variable $this->data['pluginsList'].
 */
?>
{% set plugins = pluginsList %}

<h1>{{ Lang.marketplace.list_plugins }}</h1>
<aside class="note">
    <p>{{ Lang.marketplace.note }}</p>
</aside>
<div class="navigation-market">
    <ul>
        <li>
            <a href="{{ ROUTER.generate("admin-marketplace") }}" class="nav-link">Accueil Market Place</a>
        </li>
        <li>
            <a href="{{ ROUTER.generate("marketplace-themes") }}" class="link">Thèmes</a>
        </li>
    </ul>
</div>
<div class="plugin-list">
    {% if plugins %}
        {% for plugin in plugins %}
            <div class="item">
                <h2>
                    <?php
                    // FR: Affiche l'icône du plugin s'il est définie
                    // EN: Display the plugin icon if defined
                    ?>
                    {% if plugin.icon %}
                        <i class="{{ plugin.icon }}"></i>
                    {% endif %}
                    <?php
                    // FR: Affiche le nom du plugin ou un message par défaut
                    // EN: Display the plugin name or a default message
                    ?>
                    {% if plugin.name %}
                        {{ plugin.name }}
                    {% else %}
                        Nom non défini
                    {% endif %}
                </h2>
                <div class="info">
                    <strong>{{ Lang.marketplace.list_desc }} :</strong> {{ plugin.description }}
                </div>
                <div class="info">
                    <strong>{{ Lang.marketplace.version }} :</strong> {{ plugin.version }}
                </div>
                <div class="info">
                    <strong>{{ Lang.marketplace.author }} :</strong> {{ plugin.authorEmail }}
                </div>
                {% if plugin.authorWebsite %}
                    <div class="info">
                        <strong>{{ Lang.marketplace.website }} :</strong>
                        <a href="{{ plugin.authorWebsite }}" target="_blank">
                            {{ plugin.authorWebsite }}
                        </a>
                    </div>
                {% endif %}
                <div class="actions">
                    {% if plugin.is_installed %}
                        {% if plugin.update_needed %}
                            <?php
                            // FR: Plugin installé et mise à jour disponible
                            // EN: Plugin installed and update available
                            ?>
                            <span class="update-icon" title="Mise à jour disponible / Update available">&#x21bb;</span>
                            <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ plugin.directory }}&type={% if plugin.type %}{{ plugin.type }}{% else %}plugin{% endif %}&commit={{ plugin.CommitGithubSHA }}" class="download-btn">
                                {{ Lang.marketplace.update }}
                            </a>
                        {% else %}
                            <?php
                            // FR: Plugin installé et à jour
                            // EN: Plugin installed and up-to-date
                            ?>
                            <span class="up-to-date-icon" title="Plugin à jour / Plugin is up-to-date">&#x2714;</span>
                        {% endif %}
                    {% else %}
                        <?php
                        // FR: Plugin non installé
                        // EN: Plugin not installed
                        ?>
                        <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ plugin.directory }}&type={% if plugin.type %}{{ plugin.type }}{% else %}plugin{% endif %}&commit={{ plugin.CommitGithubSHA }}" class="download-btn">
                            {{ Lang.marketplace.install }}
                        </a>
                    {% endif %}
                </div>
            </div>
        {% endfor %}
    {% else %}
        <p>{{ Lang.marketplace.no_plugins }}</p>
    {% endif %}
</div>
