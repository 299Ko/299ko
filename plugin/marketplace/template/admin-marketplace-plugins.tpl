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
 * admin-marketplace-plugins.tpl
 *
 * FR: Affiche la liste complète des plugins du marketplace.
 * EN: Displays the full list of marketplace plugins.
 *
 * Les données sont fournies par le contrôleur via la variable $this->data['pluginsList'].
 * Data is provided by the controller via $this->data['pluginsList'].
 */
$plugins = $this->data['pluginsList'] ?? [];
?>
<h1>Liste des Plugins / Plugins List</h1>
            <div class="navigation-market">
            <ul><li><a href="<?php echo  $this->data['router']->generate('admin-marketplace'); ?>" class="nav-link">Accueil Market Place</a></li>
            <li><a href="<?php echo $this->data['router']->generate('marketplace-themes'); ?>" class="link">
                Thèmes</a></li>
                </ul>
                </div>
<?php if (!empty($plugins)): ?>
    <?php foreach ($plugins as $plugin): ?>
        <div class="item">
            <h2>
                <?php
                // FR: Affiche l'icône du plugin s'il est définie
                // EN: Display the plugin icon if defined
                if (!empty($plugin['icon'])) {
                    echo '<i class="' . htmlspecialchars($plugin['icon']) . '"></i> ';
                }
                // FR: Affiche le nom du plugin ou un message par défaut
                // EN: Display the plugin name or a default message
                echo htmlspecialchars($plugin['name'] ?? 'Nom non défini');
                ?>
            </h2>
            <div class="info">
                <strong>Description :</strong> <?php echo htmlspecialchars($plugin['description'] ?? ''); ?>
            </div>
            <div class="info">
                <strong>Version :</strong> <?php echo htmlspecialchars($plugin['version'] ?? ''); ?>
            </div>
            <div class="info">
                <strong>Auteur / Author:</strong> <?php echo htmlspecialchars($plugin['authorEmail'] ?? ''); ?>
            </div>
            <?php if (!empty($plugin['authorWebsite'])): ?>
                <div class="info">
                    <strong>Site web / Website:</strong>
                    <a href="<?php echo htmlspecialchars($plugin['authorWebsite']); ?>" target="_blank">
                        <?php echo htmlspecialchars($plugin['authorWebsite']); ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="info">
                <strong>Type :</strong> <?php echo htmlspecialchars($plugin['type'] ?? ''); ?>
            </div>
            <div class="info">
                <strong>Dossier / Directory:</strong> <?php echo htmlspecialchars($plugin['directory']); ?>
            </div>

            <div class="actions">
                <?php if ($plugin['is_installed']): ?>
                    <?php if ($plugin['update_needed']): ?>
                        <!-- FR: Plugin installé et mise à jour disponible
                             EN: Plugin installed and update available -->
                        <span class="update-icon" title="Mise à jour disponible / Update available">&#x21bb;</span>
                        <a href="<?php echo $this->data['router']->generate('marketplace-install-release')
                            . '?folder=' . urlencode($plugin['directory'])
                            . '&type=' . urlencode($plugin['type'] ?? 'plugin')
                            . '&commit=' . urlencode($plugin['CommitGithubSHA']); ?>" class="download-btn">
                            Mettre à niveau / Update
                        </a>
                    <?php else: ?>
                        <!-- FR: Plugin installé et à jour
                             EN: Plugin installed and up-to-date -->
                        <span class="up-to-date-icon" title="Plugin à jour / Plugin is up-to-date">&#x2714;</span>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- FR: Plugin non installé
                         EN: Plugin not installed -->
                    <a href="<?php echo $this->data['router']->generate('marketplace-install-release')
                        . '?folder=' . urlencode($plugin['directory'])
                        . '&type=' . urlencode($plugin['type'] ?? 'plugin')
                        . '&commit=' . urlencode($plugin['CommitGithubSHA']); ?>" class="download-btn">
                        Installer / Install
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun plugin trouvé. / No plugins found.</p>
<?php endif; ?>
