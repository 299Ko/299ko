<?php
// plugin/wiki/param/routes.php
defined('ROOT') or exit('Access denied!');
$router = ROUTER::getInstance();

// Routes côté public
$router->map('GET', '/wiki[/?]', 'WikiPageController#view', 'wiki-view');

// Routes côté admin pour la gestion des pages
$router->map('GET', '/admin/wiki[/?]', 'AdminWikiController#index', 'admin-wiki');
$router->map('GET', '/admin/wiki/edit[/?]', 'AdminWikiController#edit', 'admin-wiki-edit');
$router->map('POST', '/admin/wiki/save[/?]', 'AdminWikiController#save', 'admin-wiki-save');
$router->map('GET', '/admin/wiki/delete[/?]', 'AdminWikiController#delete', 'admin-wiki-delete');

// Routes côté admin pour l'historique des versions
$router->map('GET', '/admin/wiki/versions[/?]', 'AdminWikiVersionsController#index', 'admin-wiki-versions');
$router->map('GET', '/admin/wiki/versions/restore[/?]', 'AdminWikiVersionsController#restore', 'admin-wiki-versions-restore');
$router->map('GET', '/admin/wiki/versions/view[/?]', 'AdminWikiVersionsController#viewVersion', 'admin-wiki-versions-view');



// Routes côté admin pour la gestion des catégories
$router->map('GET', '/admin/wiki/categories[/?]', 'AdminWikiCategoriesController#index', 'admin-wiki-categories');
$router->map('GET', '/admin/wiki/categories/edit[/?]', 'AdminWikiCategoriesController#edit', 'admin-wiki-categories-edit');
$router->map('POST', '/admin/wiki/categories/save[/?]', 'AdminWikiCategoriesController#save', 'admin-wiki-categories-save');
$router->map('GET', '/admin/wiki/categories/delete[/?]', 'AdminWikiCategoriesController#delete', 'admin-wiki-categories-delete');

// Routes côté admin pour les paramètres
$router->map('GET', '/admin/wiki/parameters[/?]', 'AdminWikiParametersController#index', 'admin-wiki-parameters');
$router->map('POST', '/admin/wiki/parameters/save[/?]', 'AdminWikiParametersController#save', 'admin-wiki-parameters-save');