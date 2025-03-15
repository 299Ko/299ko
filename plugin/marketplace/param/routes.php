<?php
/**
 * Routes for Marketplace plugin
 *
 * Définition des routes pour l'administration de la marketplace.
 */

defined('ROOT') or exit('Access denied!');

$router = router::getInstance();

// Route admin pour afficher la page marketplace principale
$router->map('GET', '/admin/marketplace[/?]', 'MarketplaceAdminController#index', 'admin-marketplace');

// Nouvelle route pour afficher la liste complète des plugins
$router->map('GET', '/admin/marketplace/plugins[/?]', 'MarketplaceAdminController#listPlugins', 'marketplace-plugins');

// Nouvelle route pour afficher la liste complète des thèmes
$router->map('GET', '/admin/marketplace/themes[/?]', 'MarketplaceAdminController#listThemes', 'marketplace-themes');

// Nouvelle route pour afficher la zone officielle (avec plugins et thèmes officiels)
$router->map('GET', '/admin/marketplace/official[/?]', 'MarketplaceAdminController#officialZone', 'marketplace-official');

// Endpoint pour l'action like (accessible en POST)
$router->map('POST', '/marketplace/like', 'MarketplaceAdminController#likeRelease', 'marketplace-like');
