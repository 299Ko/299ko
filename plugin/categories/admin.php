<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

$action = (string) $_GET['action'] ?? '';
$pluginId = (string) $_GET['plugin'] ?? '';

if ($pluginId === '' || !CategoriesManager::isPluginUseCategories($pluginId)) {
    // Plugin isnt use categories
    $action = '';
    $pluginId = '';
} else {
    $categoriesManager = new CategoriesManager($pluginId);
}

switch ($action) {
    case 'add':
        $pluginForm = $_POST['categorie-plugin'] ?? '';
        $error = false;
        if ($pluginId === $pluginForm && $administrator->isAuthorized()) {
            $parentId = (int) $_POST['categorie-parentId'];
            $categorieLabel = $_POST['categorie-add-label'];
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
        $id = (int) $_GET['id'];
        if (!$administrator->isAuthorized()) {
            core::getInstance()->error404();
        }
        if ($categoriesManager->isCategorieExist($id)) {
            if ($categoriesManager->deleteCategorie($id)) {
                show::msg('La catégorie a bien été supprimée.', 'success');
            } else {
                show::msg('Une erreur s\'est produite lors de la suppression de la catégorie', 'error');
            }
        }
        header('location:index.php?p=categories&plugin=' . $pluginId);
        die();
    case 'edit':
        $id = (int) $_GET['id'];
        if (!$categoriesManager->isCategorieExist($id)) {
            show::msg('La catégorie demandée n\'existe pas', 'error');
            header('location:index.php?p=categories&plugin=' . $pluginId);
            die();
        }
        $categorie = $categoriesManager->getCategorie($id);
        break;
    case 'save':
        if (!$administrator->isAuthorized()) {
            core::getInstance()->error404();
        }
        $id = (int) $_GET['id'];
        if (!$categoriesManager->isCategorieExist($id)) {
            show::msg('La catégorie demandée n\'existe pas', 'error');
            header('location:index.php?p=categories&plugin=' . $pluginId);
            die();
        }
        $label = $_POST['categorie-label'];
        $parentId = (int) $_POST['categorie-parent'];
        if ($parentId !== 0 && !$categoriesManager->isCategorieExist($parentId)) {
            show::msg('La catégorie parente n\'existe pas', 'error');
            header('location:index.php?p=categories&plugin=' . $pluginId);
            die();
        }
        $categorie = $categoriesManager->getCategorie($id);
        $categorie->parentId = $parentId;
        $categorie->label = $label;
        $categoriesManager->saveCategorie($categorie);
        show::msg('La catégorie a bien été modifiée', 'success');
        header('location:index.php?p=categories&plugin=' . $pluginId);
        die();
    default :
        $pluginsWithCategories = [];
        foreach ($pluginsManager->getPlugins() as $p) {
            if (CategoriesManager::isPluginUseCategories($p->getName())) {
                $pluginsWithCategories[] = $p;
            }
        }
        break;
}