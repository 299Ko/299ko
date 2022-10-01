<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

$action = $_GET['action'] ?? '';
$pluginId = $_GET['plugin'] ?? '';

switch ($action) {
    case 'add':
        $pluginForm = $_POST['categorie-plugin'] ?? '';
        $error = false;
        if ($pluginId === $pluginForm && CategoriesManager::isPluginUseCategories($pluginId) && $administrator->isAuthorized()) {
            $parentId = (int) $_POST['categorie-parentId'];
            $categorieLabel = $_POST['categorie-add-label'];
            $categoriesManager = new CategoriesManager($pluginId);
            if ($parentId === 0 || $categoriesManager->isCategorieExist($parentId)) {
                if ($categorieLabel == '') {
                    $categorieLabel = 'Nouvelle Catégorie';
                }
                $categoriesManager->createCategorie($categorieLabel, $parentId);
                show::msg('La catégorie ' . $categorieLabel . ' a bien été créée.', 'success');
            } else {
                show::msg('Une erreur s\'est produite lors de la création de la catégorie', 'error');
            }
        } else {
            show::msg('Une erreur s\'est produite lors de la création de la catégorie', 'error');
        }
        header('location:index.php?p=categories&plugin=' . $pluginId);
        die();
    case 'del':
        $id = $_GET['id'];
        if (!$administrator->isAuthorized() || !CategoriesManager::isPluginUseCategories($pluginId)) {
            core::getInstance()->error404();
        }
        $categoriesManager = new CategoriesManager($pluginId);
        if ($categoriesManager->isCategorieExist($id)) {
            if ($categoriesManager->deleteCategorie($id)) {
                show::msg('La catégorie a bien été supprimée.', 'success');
            } else {
                show::msg('Une erreur s\'est produite lors de la suppression de la catégorie', 'error');
            }
        }
        header('location:index.php?p=categories&plugin=' . $pluginId);
        die();

    default :
        if ($pluginId) {
            if (!CategoriesManager::isPluginUseCategories($pluginId)) {
                core::getInstance()->error404();
            }
            $categoriesManager = new CategoriesManager($pluginId);
        } else {
            $pluginsWithCategories = [];
            foreach ($pluginsManager->getPlugins() as $p) {
                if (CategoriesManager::isPluginUseCategories($p->getName())) {
                    $pluginsWithCategories[] = $p;
                }
            }
        }
        break;
}