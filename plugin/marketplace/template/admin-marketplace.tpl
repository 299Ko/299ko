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
 */

/**
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
<h1>Marketplace - Accueil / Marketplace - Home</h1>
        <div class="navigation-market">
            <ul><li><a href="<?php echo htmlspecialchars($this->data['pluginsPageUrl']); ?>" class="link">
                Plugins
            </a></li>
            <li><a href="<?php echo htmlspecialchars($this->data['themesPageUrl']); ?>" class="link">
                Thèmes</a></li>
                </ul>
                </div>
<div class="section">
    <h2>Plugins en vedette / Featured Plugins</h2>
    <!-- Vérifie si 'randomPlugins' existe dans $this->data et n'est pas vide -->
    <?php if (!empty($this->data['randomPlugins'])): ?>
        <ul class="featured-list">
            <!-- Parcourt le tableau des plugins transmis via $this->data -->
            <?php foreach ($this->data['randomPlugins'] as $plugin): ?>
                <li><?php
                // FR: Affiche l'icône du plugin s'il est définie
                // EN: Display the plugin icon if defined
                if (!empty($plugin['icon'])) {
                    echo '<i class="' . htmlspecialchars($plugin['icon']) . '"></i> ';
                }
                // FR: Affiche le nom du plugin ou un message par défaut
                // EN: Display the plugin name or a default message
                echo htmlspecialchars($plugin['name'] ?? 'Nom non défini');
                ?>
                    <?php
                        // Affiche le nom du plugin en échappant le contenu pour éviter les injections XSS
                        echo htmlspecialchars($plugin['name'] ?? 'Nom non défini');
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p>
            <!-- Affiche le lien vers la page complète des plugins -->
            <a href="<?php echo htmlspecialchars($this->data['pluginsPageUrl']); ?>" class="link">
                Voir tous les plugins / View all plugins
            </a>
        </p>
    <?php else: ?>
        <p>Aucun plugin disponible.</p>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Thèmes en vedette / Featured Themes</h2>
    <!-- Vérifie si 'randomThemes' existe dans $this->data et n'est pas vide -->
    <?php if (!empty($this->data['randomThemes'])): ?>
        <ul class="featured-list">
            <!-- Parcourt le tableau des thèmes transmis via $this->data -->
            <?php foreach ($this->data['randomThemes'] as $theme): ?>
                <li>
                    <?php
                        // Affiche le nom du thème en échappant le contenu pour éviter les injections XSS
                        echo htmlspecialchars($theme['name'] ?? 'Nom non défini');
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p>
            <!-- Affiche le lien vers la page complète des thèmes -->
            <a href="<?php echo htmlspecialchars($this->data['themesPageUrl']); ?>" class="link">
                Voir tous les thèmes / View all themes
            </a>
        </p>
    <?php else: ?>
        <p>Aucun thème disponible.</p>
    <?php endif; ?>
</div>
