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
    public $pluginId;
    public $categories;
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

    public static function isPluginUseCategories($pluginId) {
        return pluginsManager::getPluginInfoVal($pluginId, 'useCategories');
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
        $this->categories = $categories;
    }

    public function outputAsCheckbox($itemId) {
        $catDisplay = 'root';
        require PLUGINS . 'categories/template/checkboxCategories.php';
    }
    
    public function outputAsList() {
        $catDisplay = 'root';
        require PLUGINS . 'categories/template/listCategories.php';
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

}
