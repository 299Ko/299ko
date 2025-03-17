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

defined('ROOT') or exit('Access denied!');

$router = router::getInstance();

// Route pour la page d'accueil de la marketplace
$router->map('GET', '/admin/marketplace[/?]', 'AdminMarketplaceController#index', 'admin-marketplace');

// Route pour la liste complète des plugins
$router->map('GET', '/admin/marketplace/plugins[/?]', 'PluginsMarketController#index', 'marketplace-plugins');

// Route pour la liste complète des thèmes
$router->map('GET', '/admin/marketplace/themes[/?]', 'ThemesMarketController#index', 'marketplace-themes');

// Route pour installer ou mettre à jour (les deux contrôleurs utilisent installRelease)
$router->map('GET', '/admin/marketplace/install[/?]', 'PluginsMarketController#installRelease', 'marketplace-install-release');
