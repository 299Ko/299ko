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
 * admin-marketplace-themes.tpl
 *
 * FR: Affiche la liste complète des thèmes du marketplace.
 * EN: Displays the full list of marketplace themes.
 *
 * Les données sont fournies par le contrôleur via la variable $this->data['themesList'].
 */
?>
{% set themes = themesList %}

<h1>{{ Lang.marketplace.list_themes }}</h1>
<aside class="note">
    <p>{{ Lang.marketplace.note }}</p>
</aside>
<div class="navigation-market">
    <ul>
        <li>
            <a href="{{ ROUTER.generate("admin-marketplace") }}" class="nav-link">Accueil Market Place</a>
        </li>
        <li>
            <a href="{{ ROUTER.generate("marketplace-plugins") }}" class="link">{{ Lang.marketplace.plugins }}</a>
        </li>
    </ul>
</div>
<div class="plugin-list">
    {% if themes %}
        {% for theme in themes %}
            <div class="item">
                <h2>
                    <?php
                    // FR: Affiche l'icône du thème s'il est définie (si applicable)
                    // EN: Display the theme icon if defined (if applicable)
                    ?>
                    {% if theme.icon %}
                        <i class="{{ theme.icon }}"></i>
                    {% endif %}
                    <?php
                    // FR: Affiche le nom du thème ou un message par défaut
                    // EN: Display the theme name or a default message
                    ?>
                    {% if theme.name %}
                        {{ theme.name }}
                    {% else %}
                        Nom non défini
                    {% endif %}
                </h2>
                <div class="info">
                    <strong>{{ Lang.marketplace.list_desc }} :</strong> {{ theme.description }}
                </div>
                <div class="info">
                    <strong>{{ Lang.marketplace.list_version }} :</strong> {{ theme.version }}
                </div>
                <div class="info">
                    <strong>{{ Lang.marketplace.Author }} :</strong> {{ theme.authorEmail }}
                </div>
                {% if theme.authorWebsite %}
                    <div class="info">
                        <strong>{{ Lang.marketplace.website }} :</strong>
                        <a href="{{ theme.authorWebsite }}" target="_blank">
                            {{ theme.authorWebsite }}
                        </a>
                    </div>
                {% endif %}
                <div class="actions">
                    {% if theme.is_installed %}
                        {% if theme.update_needed %}
                            <?php
                            // FR: Thème installé et mise à jour disponible
                            // EN: Theme installed and update available
                            ?>
                            <span class="update-icon" title="Mise à jour disponible / Update available">&#x21bb;</span>
                            <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ theme.directory }}&type={% if theme.type %}{{ theme.type }}{% else %}theme{% endif %}&commit={{ theme.CommitGithubSHA }}" class="download-btn">
                                {{ Lang.marketplace.update }}
                            </a>
                        {% else %}
                            <?php
                            // FR: Thème installé et à jour
                            // EN: Theme installed and up-to-date
                            ?>
                            <span class="up-to-date-icon" title="Thème à jour / Theme is up-to-date">&#x2714;</span>
                        {% endif %}
                    {% else %}
                        <?php
                        // FR: Thème non installé
                        // EN: Theme not installed
                        ?>
                        <a href="{{ ROUTER.generate("marketplace-install-release") }}?folder={{ theme.directory }}&type={% if theme.type %}{{ theme.type }}{% else %}theme{% endif %}&commit={{ theme.CommitGithubSHA }}" class="download-btn">
                            {{ Lang.marketplace.install }}
                        </a>
                    {% endif %}
                </div>
            </div>
        {% endfor %}
    {% else %}
        <p>{{ Lang.marketplace.no_themes }}</p>
    {% endif %}
</div>
