<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

require_once PLUGINS . 'categories/lib/Categorie.php';
require_once PLUGINS . 'categories/lib/CategoriesManager.php';

function categoriesAdminToolsTemplates($params) {
    $pluginId = $params[0];
    if (CategoriesManager::isPluginUseCategories($pluginId)) {
        echo  '<a title="Gérer les catégories" id="cat_link" '
        . 'href="index.php?p=categories&plugin=' . $pluginId 
        . '"><i class="fa-solid fa-folder-tree"></i></a>';

    }
}

function categoriesAdminEditingAnItem($params) {
    $pluginId = $params[0];
    $itemId = $params[1] ?? -1;
    echo '<h3>Catégories</h3>';    
    if (CategoriesManager::isPluginUseCategories($pluginId)) {
        $catManager = new CategoriesManager($pluginId);
        $catManager->outputAsCheckbox($itemId);
    }
}

function categoriesAdminOnSaveItem($params) {
    $pluginId = $params[0];
    $itemId = $params[1];
    $cats = CategoriesManager::getAllCategoriesPluginId($pluginId);
    $choosenCats = categoriesSearchForCheckedCategories($cats);
    CategoriesManager::saveItemToCategories($pluginId, $itemId, $choosenCats);
    
}

function categoriesSearchForCheckedCategories($categoriesId) {
    $choosenCats = [];
    foreach ($_POST['categoriesCheckbox'] as $cat) {
        if (in_array($cat, $categoriesId)) {
            // Categorie exist
            $choosenCats[] = $cat;
        }
    }
    return $choosenCats;
}