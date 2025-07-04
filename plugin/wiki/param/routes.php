<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

$router = router::getInstance();

// Public
$router->map('GET', '/wiki[/?]', 'WikiListController#home', 'wiki-home');
$router->map('GET', '/wiki/cat-[*:name]-[i:id]/[i:page][/?]', 'WikiListController#categoryPage', 'wiki-category-page');
$router->map('GET', '/wiki/cat-[*:name]-[i:id].html', 'WikiListController#category', 'wiki-category');
$router->map('GET', '/wiki/[*:name]-[i:id].html', 'WikiReadController#read', 'wiki-read');

$router->map('GET', '/wiki/[i:page][/?]', 'WikiListController#page', 'wiki-page');

// Categories
$router->map('POST', '/admin/wiki/addCategory', 'WikiAdminCategoriesController#addCategory', 'admin-wiki-add-category');
$router->map('POST', '/admin/wiki/deleteCategory', 'WikiAdminCategoriesController#deleteCategory', 'admin-wiki-delete-category');
$router->map('POST', '/admin/wiki/editCategory', 'WikiAdminCategoriesController#editCategory', 'admin-wiki-edit-category');
$router->map('POST', '/admin/wiki/saveCategory', 'WikiAdminCategoriesController#saveCategory', 'admin-wiki-save-category');
$router->map('GET', '/admin/wiki/listAjaxCategories', 'WikiAdminCategoriesController#listAjaxCategories', 'admin-wiki-list-ajax-categories');
$router->map('GET', '/admin/wiki/getCategoriesSelect', 'WikiAdminCategoriesController#getCategoriesSelect', 'admin-wiki-get-categories-select');

// Configuration
$router->map('POST', '/admin/wiki/saveConfig', 'WikiAdminConfigController#saveConfig', 'admin-wiki-save-config');

// Pages
$router->map('GET', '/admin/wiki[/?]', 'WikiAdminPagesController#list', 'admin-wiki-list');
$router->map('POST', '/admin/wiki/deletePage', 'WikiAdminPagesController#deletePage', 'admin-wiki-delete-page');
$router->map('GET', '/admin/wiki/editPage/[i:id]?', 'WikiAdminPagesController#editPage', 'admin-wiki-edit-page');
$router->map('POST', '/admin/wiki/savePage', 'WikiAdminPagesController#savePage', 'admin-wiki-save-page');

// History
$router->map('GET', '/admin/wiki/history/[i:id]', 'WikiAdminHistoryController#showHistory', 'admin-wiki-history');
$router->map('GET', '/admin/wiki/version/[i:id]/[i:version]', 'WikiAdminHistoryController#showVersion', 'admin-wiki-view-version');
$router->map('POST', '/admin/wiki/restoreVersion', 'WikiAdminHistoryController#restoreVersion', 'admin-wiki-restore-version');



