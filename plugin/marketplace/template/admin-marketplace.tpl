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
 * admin-marketplace.tpl
 *
 * FR: Affiche la page d'accueil du marketplace.
 *     Cette page présente 5 plugins et 5 thèmes sélectionnés aléatoirement,
 *     ainsi que des liens vers les pages complètes des plugins et des thèmes.
 *
 * EN: Displays the marketplace homepage.
 *     This page shows 5 randomly selected plugins and 5 randomly selected themes,
 *     along with links to the full plugins and themes pages.
 *
 * Les données attendues dans $this->data :
 * - 'randomPlugins': tableau de 5 plugins sélectionnés aléatoirement.
 * - 'randomThemes': tableau de 5 thèmes sélectionnés aléatoirement.
 * - 'pluginsPageUrl': URL de la page complète des plugins.
 * - 'themesPageUrl': URL de la page complète des thèmes.
 */
?>
<h1>{{ Lang.marketplace.description }}</h1>
<aside class="note">
    <p>{{ Lang.marketplace.note }}</p>
</aside>
<div class="navigation-market">
    <ul>
        <li>
            <a href="{{ pluginsPageUrl }}" class="link">
                <?php
                // Affiche le lien vers la page complète des plugins
                ?>
                {{ Lang.marketplace.plugins }}
            </a>
        </li>
        <li>
            <a href="{{ themesPageUrl }}" class="link">
                <?php
                // Affiche le lien vers la page complète des thèmes
                ?>
                {{ Lang.marketplace.themes }}
            </a>
        </li>
    </ul>
</div>
<div class="home-list">
    <div class="section">
        <h2>{{ Lang.marketplace.featured_plugins }}</h2>
        <?php
        // Vérifie si 'randomPlugins' existe dans $this->data et n'est pas vide
        ?>
        {% if randomPlugins %}
            <ul class="featured-list">
                <?php
                // Parcourt le tableau des plugins transmis via $this->data
                ?>
                {% for plugin in randomPlugins %}
                    <li>
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
                    </li>
                {% endfor %}
            </ul>
            <p>
                <?php
                // Affiche le lien vers la page complète des plugins
                ?>
                <a href="{{ pluginsPageUrl }}" class="link">
                    {{ Lang.marketplace.view_all_plugins }}
                </a>
            </p>
        {% else %}
            <p>{{ Lang.marketplace.no_plugins }}</p>
        {% endif %}
    </div>

    <div class="section">
        <h2>{{ Lang.marketplace.featured_themes }}</h2>
        <?php
        // Vérifie si 'randomThemes' existe dans $this->data et n'est pas vide
        ?>
        {% if randomThemes %}
            <ul class="featured-list">
                <?php
                // Parcourt le tableau des thèmes transmis via $this->data
                ?>
                {% for theme in randomThemes %}
                    <li>
                        <?php
                        // Affiche le nom du thème en échappant le contenu pour éviter les injections XSS
                        ?>
                        {{ theme.name | default("Nom non défini") }}
                    </li>
                {% endfor %}
            </ul>
            <p>
                <?php
                // Affiche le lien vers la page complète des thèmes
                ?>
                <a href="{{ themesPageUrl }}" class="link">
                    {{ Lang.marketplace.view_all_themes }}
                </a>
            </p>
        {% else %}
            <p>{{ Lang.marketplace.no_themes }}</p>
        {% endif %}
    </div>
</div>
