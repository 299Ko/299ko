<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class CategoriesManager {

    protected $file;
    protected $pluginId;
    protected $categories;
    protected $imbricatedCategories;
    protected bool $isActive;

    public function __construct(string $pluginId) {
        $this->pluginId = $pluginId;
        $this->file = DATA_PLUGIN . $this->pluginId . '/categories.json';
        $this->setCategoriesStatus();
        if ($this->isActive()) {
            $this->getCategoriesFromMetas();
        }
    }

    public function isActive() {
        return $this->isActive;
    }

    public function getCategorie(int $id) {
        if (isset($this->categories[$id])) {
            return clone $this->categories[$id];
        }
        return false;
    }
    
    public function getCategories() {
        return $this->categories;
    }
    
    public function getNestedCategories() {
        return $this->imbricatedCategories;
    }

    public function isCategorieExist(int $categorieId) {
        return (isset($this->categories[$categorieId]));
    }

    public function createCategorie($label, int $parentId) {
        $cat = new Categorie($this->pluginId);
        $cat->label = $label;
        $cat->parentId = $parentId;
        $cat->id = $this->findNextId();
        $this->categories[$cat->id] = $cat;
        if ($parentId !== 0) {
            // Have a parent
            array_push($this->categories[$parentId]->childrenId, $cat->id);
            $this->categories[$parentId]->childrenId = array_values(array_unique($this->categories[$parentId]->childrenId));
        }
        $this->imbricateCategories();
        $this->saveCategories();
        return true;
    }

    public function saveCategorie(Categorie $categorie) {
        $oldCategorie = $this->categories[$categorie->id];
        if ($oldCategorie->parentId !== 0) {
            // We will modify old parent
            $key = array_search($categorie->id, $this->categories[$oldCategorie->parentId]->childrenId, true);
            if ($key !== false) {
                // Our categorie is here, we delete it
                unset($this->categories[$oldCategorie->parentId]->childrenId[$key]);
            }
        }
        if ($categorie->parentId !== 0) {
            // We will register the categorie in the new parent
            array_push($this->categories[$categorie->parentId]->childrenId, $categorie->id);
            $this->categories[$categorie->parentId]->childrenId = array_values(array_unique($this->categories[$categorie->parentId]->childrenId));
        }
        $this->categories[$categorie->id] = $categorie;
        $this->imbricateCategories();
        $this->saveCategories();
        return true;
    }

    public function deleteCategorie($id) {
        if (!isset($this->categories[$id])) {
            return false;
        }
        $cat = $this->categories[$id];
        foreach ($cat->childrenId as $childId) {
            // Childs Categories are affected to the categorie deleted parent
            $this->categories[$childId]->parentId = $cat->parentId;
        }
        if ($cat->parentId !== 0) {
            // Have a parent, we delete the categorie in here
            $key = array_search($id, $this->categories[$cat->parentId]->childrenId, true);
            if ($key !== false) {
                // Our categorie is here, we delete it
                unset($this->categories[$cat->parentId]->childrenId[$key]);
                // And we add the children in the parent
                array_push($this->categories[$cat->parentId]->childrenId, $cat->childrenId);
                $this->categories[$cat->parentId]->childrenId = array_values(array_unique($this->categories[$cat->parentId]->childrenId));
            }
        }
        //Delete the categorie
        unset($this->categories[$id]);
        $this->imbricateCategories();
        $this->saveCategories();
        return true;
    }

    protected function saveCategories() {
        $metas = [];
        foreach ($this->categories as $cat) {
            $metas[$cat->id] = $cat;
        }
        util::writeJsonFile($this->file, $metas);
    }

    public static function isPluginUseCategories($pluginId) {
        if (!pluginsManager::isExistPlugin($pluginId)) {
            return false;
        }
        return pluginsManager::getPluginInfoVal($pluginId, 'useCategories');
    }
    
    public static function isPluginUseMultiCategories($pluginId) {
        if (!pluginsManager::isExistPlugin($pluginId)) {
            return false;
        }
        return pluginsManager::getPluginInfoVal($pluginId, 'typeCategories') === 'multi';
    }

    protected function setCategoriesStatus() {
        $this->isActive = self::isPluginUseCategories($this->pluginId);        
    }

    protected function getCategoriesFromMetas() {
        $this->categories = [];
        if (!is_file($this->file)) {
            return;
        }
        $metas = util::readJsonFile($this->file);
        foreach ($metas as $k => $v) {
            $this->categories[$k] = new Categorie($this->pluginId, $k);
        }
        $this->imbricateCategories();
    }

    protected function imbricateCategories() {
        $categories = $this->categories;
        foreach ($categories as $categorie) {
            if ($categorie->parentId != 0) {
                foreach ($this->categories as $cat) {
                    $res = $cat->getCategorieById($categorie->parentId);
                    if (is_object($res) && get_class($res) === 'Categorie') {
                        $res->addChild($categorie);
                        unset($categories[$categorie->id]);
                        break 1;
                    }
                }
            }
        }
        $this->imbricatedCategories = $categories;
        $this->calcDepth($this->imbricatedCategories, 0);
    }
    
    protected function calcDepth(&$categories, int $depth = 0) {
        foreach ($categories as &$cat) {
            $cat->depth = $depth;
            if ($cat->hasChildren) {
                $this->calcDepth($cat->children, $depth + 1);
            }
        }
    }

    public function outputAsCheckbox($itemId) {
        $catDisplay = 'root';
        ob_start();
        require PLUGINS . 'categories/template/checkboxCategories.php';
        return ob_get_clean();
    }

    public function outputAsSelect($parentId, $categorieId) {
        $catDisplay = 'root';
        ob_start();
        require PLUGINS . 'categories/template/selectCategorie.php';
        return ob_get_clean();
    }
    
    public function outputAsSelectOne($itemId) {
        $catDisplay = 'root';
        ob_start();
        require PLUGINS . 'categories/template/selectOneCategorie.php';
        return ob_get_clean();
    }

    public function outputAsList() {
        $catDisplay = 'root';
        ob_start();
        require PLUGINS . 'categories/template/listCategories.php';
        return ob_get_clean();
    }

    public static function getAllCategoriesPluginId(string $pluginId) {
        $metas = self::getMetas($pluginId);
        return array_keys($metas);
    }

    public static function saveItemToCategories(string $pluginId, $itemId, array $categoriesId) {
        $metas = self::getMetas($pluginId);
        $categories = [];
        foreach ($metas as $cat) {
            $key = array_search($itemId, $cat['items'], true);
            if ($key !== false) {
                // Item is here. We delete it before see if it is in categorie anymore
                unset($cat['items'][$key]);
            }
            if (in_array($cat['id'], $categoriesId)) {
                // Categorie has been checked
                array_push($cat['items'], $itemId);
                $cat['items'] = array_values(array_unique($cat['items']));
            }
            $categories[$cat['id']] = $cat;
        }
        util::writeJsonFile(DATA_PLUGIN . $pluginId . '/categories.json', $categories);
    }

    protected static function getMetas($pluginId) {
        $file = DATA_PLUGIN . $pluginId . '/categories.json';
        if (!is_file($file)) {
            return [];
        }
        return util::readJsonFile($file);
    }

    protected function findNextId(): int {
        if (!file_exists($this->file)) {
            return 1;
        }
        $cats = util::readJsonFile($this->file);
        return max(array_column($cats, 'id')) + 1;
    }

}
