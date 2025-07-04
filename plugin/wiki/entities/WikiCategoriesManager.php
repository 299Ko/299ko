<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

/**
 * Gestionnaire des catégories du Wiki
 * 
 * Étend le gestionnaire de catégories standard pour le plugin Wiki
 */
class WikiCategoriesManager extends CategoriesManager {

    protected string $pluginId = 'wiki';
    protected string $name = 'categories';
    protected string $className = 'WikiCategory';
    protected bool $nested = true;
    protected bool $chooseMany = true;

    /**
     * Obtenir l'URL d'ajout de catégorie
     * 
     * @return string URL d'ajout
     */
    public function getAddCategoryUrl():string {
        return router::getInstance()->generate('admin-wiki-add-category');
    }

    /**
     * Obtenir l'URL de suppression de catégorie
     * 
     * @return string URL de suppression
     */
    public function getDeleteUrl() :string {
        return router::getInstance()->generate('admin-wiki-delete-category');
    }

    /**
     * Obtenir l'URL d'affichage AJAX de la liste
     * 
     * @return string URL AJAX
     */
    public function getAjaxDisplayListUrl():string {
        return router::getInstance()->generate('admin-wiki-list-ajax-categories');
    }

    /**
     * Obtenir l'URL d'édition de catégorie
     * 
     * @return string URL d'édition
     */
    public function getEditUrl():string {
        return router::getInstance()->generate('admin-wiki-edit-category');
    }

    /**
     * Générer l'affichage de la liste des catégories
     * 
     * @return string HTML de la liste
     */
    public function outputAsList() {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'wiki-categories-list');
        
        $tpl->set('catDisplay', 'root');
        $tpl->set('this', $this);
        $tpl->set('categoriesManager', $this);
        
        return $tpl->output();
    }

    /**
     * Obtenir la hiérarchie des parents d'une catégorie sous forme de chaîne
     * 
     * @param object $category Catégorie à analyser
     * @return string Hiérarchie des parents séparée par " / "
     */
    public function getParentHierarchy($category) {
        if ($category->parentId == 0) {
            return '';
        }
        
        $hierarchy = [];
        $currentParentId = $category->parentId;
        
        while ($currentParentId > 0) {
            $parent = $this->getCategory($currentParentId);
            if (!$parent) {
                break;
            }
            $hierarchy[] = $parent->label;
            $currentParentId = $parent->parentId;
        }
        
        return implode(' / ', array_reverse($hierarchy));
    }

    /**
     * Synchroniser les catégories avec les pages wiki pour mettre à jour les compteurs
     * 
     * @return void
     */
    public function syncWithWikiPages() {
        $wikiPageManager = new WikiPageManager();
        $wikiPages = $wikiPageManager->getItems();
        
        foreach ($this->categories as $category) {
            $category->items = [];
        }
        
        foreach ($wikiPages as $wikiPage) {
            $pageId = $wikiPage->getId();
            $pageCategories = $wikiPage->categories ?? [];
            
            if (isset($pageCategories['categories']) && is_array($pageCategories['categories'])) {
                $categoryIds = [];
                foreach ($pageCategories['categories'] as $catId => $catData) {
                    if (isset($this->categories[$catId])) {
                        $categoryIds[] = (int)$catId;
        }
                }
                
                if (!empty($categoryIds)) {
                    self::saveItemToCategories($pageId, $categoryIds);
                }
            }
        }
        
        $this->getCategoriesFromMetas();
    }

    /**
     * Obtenir le nombre total d'éléments d'une catégorie incluant tous les enfants
     * 
     * @param object $category Catégorie à analyser
     * @return int Nombre total d'éléments
     */
    public function getTotalItemsCount($category) {
        $total = count($category->items);
        
        if ($category->hasChildren) {
            foreach ($category->children as $child) {
                $total += $this->getTotalItemsCount($child);
            }
        }
        
        return $total;
    }

    /**
     * Générer un sélecteur de catégories pour la sélection de parent
     * 
     * @param int $selectedParentId ID du parent sélectionné
     * @param string $fieldName Nom du champ
     * @return string HTML du sélecteur
     */
    public function outputAsParentSelect($selectedParentId, $fieldName = "parentId") {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'selectParentCategory');
        
        $tpl->set('catDisplay', 'root');
        $tpl->set('selectedParentId', $selectedParentId);
        $tpl->set('fieldName', $fieldName);
        $tpl->set('categoriesManager', $this);
        
        return $tpl->output();
    }
}