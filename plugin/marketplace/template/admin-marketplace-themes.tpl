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
 * admin-marketplace-themes.tpl
 *
 * FR: Affiche la liste complète des thèmes du marketplace.
 * EN: Displays the full list of marketplace themes.
 *
 * Les données sont fournies par le contrôleur via la variable $this->data['themesList'].
 * Data is provided by the controller via $this->data['themesList'].
 */
$themes = $this->data['themesList'] ?? [];
?>
<h1>Liste des Thèmes / Themes List</h1>
            <div class="navigation-market">
            <ul><li><a href="<?php echo  $this->data['router']->generate('admin-marketplace'); ?>" class="nav-link">Accueil Market Place</a></li>
            <li><a href="<?php echo $this->data['router']->generate('marketplace-plugins'); ?>" class="link">
                Plugins</a></li>
                </ul>
                </div>
<?php if (!empty($themes)): ?>
    <?php foreach ($themes as $theme): ?>
        <div class="item">
            <h2>
                <?php
                // FR: Affiche l'icône du thème s'il est définie (si applicable)
                // EN: Display the theme icon if defined (if applicable)
                if (!empty($theme['icon'])) {
                    echo '<i class="' . htmlspecialchars($theme['icon']) . '"></i> ';
                }
                // FR: Affiche le nom du thème ou un message par défaut
                // EN: Display the theme name or a default message
                echo htmlspecialchars($theme['name'] ?? 'Nom non défini');
                ?>
            </h2>
            <div class="info">
                <strong>Description :</strong> <?php echo htmlspecialchars($theme['description'] ?? ''); ?>
            </div>
            <div class="info">
                <strong>Version :</strong> <?php echo htmlspecialchars($theme['version'] ?? ''); ?>
            </div>
            <div class="info">
                <strong>Auteur / Author:</strong> <?php echo htmlspecialchars($theme['authorEmail'] ?? ''); ?>
            </div>
            <?php if (!empty($theme['authorWebsite'])): ?>
                <div class="info">
                    <strong>Site web / Website:</strong>
                    <a href="<?php echo htmlspecialchars($theme['authorWebsite']); ?>" target="_blank">
                        <?php echo htmlspecialchars($theme['authorWebsite']); ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="info">
                <strong>Type :</strong> <?php echo htmlspecialchars($theme['type'] ?? ''); ?>
            </div>
            <div class="info">
                <strong>Dossier / Directory:</strong> <?php echo htmlspecialchars($theme['directory']); ?>
            </div>

            <div class="actions">
                <?php if ($theme['is_installed']): ?>
                    <?php if ($theme['update_needed']): ?>
                        <!-- FR: Thème installé et mise à jour disponible
                             EN: Theme installed and update available -->
                        <span class="update-icon" title="Mise à jour disponible / Update available">&#x21bb;</span>
                        <a href="<?php echo $this->data['router']->generate('marketplace-install-release')
                            . '?folder=' . urlencode($theme['directory'])
                            . '&type=' . urlencode($theme['type'] ?? 'theme')
                            . '&commit=' . urlencode($theme['CommitGithubSHA']); ?>" class="download-btn">
                            Mettre à niveau / Update
                        </a>
                    <?php else: ?>
                        <!-- FR: Thème installé et à jour
                             EN: Theme installed and up-to-date -->
                        <span class="up-to-date-icon" title="Thème à jour / Theme is up-to-date">&#x2714;</span>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- FR: Thème non installé
                         EN: Theme not installed -->
                    <a href="<?php echo $this->data['router']->generate('marketplace-install-release')
                        . '?folder=' . urlencode($theme['directory'])
                        . '&type=' . urlencode($theme['type'] ?? 'theme')
                        . '&commit=' . urlencode($theme['CommitGithubSHA']); ?>" class="download-btn">
                        Installer / Install
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun thème trouvé. / No themes found.</p>
<?php endif; ?>
