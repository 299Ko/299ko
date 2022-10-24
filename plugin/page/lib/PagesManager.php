<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class PagesManager {

    protected array $items = [];
    protected array $nestedItems = [];
    protected string $file;

    public function __construct() {
        $this->file = DATA_PLUGIN . 'page/pages.json';
        $this->items = $this->loadPages();
        $this->nestedItems = $this->imbricateItems();
        $this->regenPositions();
        $this->savePages();
        $this->saveCategories();
        
    }

    public function output() {
        $pageDisplay = 'root';
        ob_start();
        require PLUGINS . 'page/template/pages.php';
        return ob_get_clean();
    }

    public function getItems() {
        return $this->items;
    }
    
    public static function addToNavigation() {
        $pageManager = new PagesManager();
        $pluginsManager = pluginsManager::getInstance();
        // Création d'items de navigation absents (plugins)
        foreach ($pluginsManager->getPlugins() as $k => $plugin)
            if ($plugin->getConfigVal('activate') && $plugin->getPublicFile() && $plugin->getName() != 'page') {
                $find = false;
                foreach ($pageManager->items as $pageItem) {
                    if ($pageItem->target == $plugin->getName())
                        $find = true;
                }
                if (!$find) {
                    $pageItem = new pageItem();
                    $pageItem->name = $plugin->getInfoVal('name');
                    $pageItem->position = $pageManager->makePosition();
                    $pageItem->isHomepage = false;
                    $pageItem->content = '';
                    $pageItem->isHidden = false;
                    $pageItem->file = '';
                    $pageItem->target = $plugin->getName();
                    $pageItem->noIndex = false;
                    $pageItem->type = PageItem::PLUGIN;
                    $pageManager->savePageItem($pageItem);
                }
            }
        // génération de la navigation
        foreach ($pageManager->items as $k => $pageItem) {
            if (!$pageItem->isHidden) {
                if ($pageItem->type == PageItem::PLUGIN && !$pluginsManager->isActivePlugin($pageItem->target)) {
                    // no item !
                } else {
                    $url = $pageItem->getUrl();
                    $pluginsManager->getPlugin('page')->addToNavigation($pageItem->name, $url, $pageItem->targetAttr, $pageItem->id, $pageItem->parent, $pageItem->cssClass);
                }
            }
        }
    }

    public function savePageItem(PageItem $pageItem) {
        $id = intval($pageItem->id);
        if ($id < 1)
            $pageItem->id = $this->makeId();
        $position = floatval($pageItem->position);
        if ($position < 0.5)
            $pageItem->position = $this->makePosition();

        $this->items[$pageItem->id] = $pageItem;
        return $this->savePages();
    }
    
    protected function savePages() {
        $metas = [];
        foreach ($this->items as $item) {
            if ($item->type !== PageItem::CATEGORIE) {
                $metas[$item->id] = $item;
            } 
        }
        return util::writeJsonFile($this->file, $metas);
    }
    
    protected function saveCategories() {
        $categoriesManager = new CategoriesManager('page');
        foreach ($this->items as $item) {
            if ($item->type === PageItem::CATEGORIE) {
                $categorie = $categoriesManager->getCategorie(str_replace('cat-', '',$item->id));
                $categorie->pluginArgs['position'] = $item->position;
                $categoriesManager->saveCategorie($categorie);
            }
        }
    }

    public function makePosition() {
        $pos = [0];
        foreach ($this->items as $pageItem) {
            $pos[] = $pageItem->position;
        }
        return max($pos) + 1;
    }
    
    protected function regenPositions() {
        $pos = 1;
        foreach ($this->nestedItems as &$item) {
            $item->position = $pos;
            if ($item->hasChildren) {
                $item->regenPositions();
            }
            $pos++;
        }
    }

    public function makeId() {
        $ids = [0];
        foreach ($this->items as $pageItem) {
            $ids[] = $pageItem->id;
        }
        return max($ids) + 1;
    }

    protected function loadPages() {
        $tmp = $this->loadCategories();
        if (!file_exists($this->file)) {
            return $tmp;
        }
        $items = util::readJsonFile($this->file);
        foreach ($items as $item) {
            $p = new PageItem($item['id']);
            $tmp[$item['id']] = $p;
        }
        uasort($tmp, fn($a, $b) => strcmp($a->position, $b->position));
        $data = [];
        $i = 0;
        $max = count($tmp);
        foreach ($tmp as &$d) {
            $i++;
            $d->position = $i;
            $data[$d->id] = $d;
        }
        return $data;
    }

    protected function loadCategories() {
        $categoriesManager = new CategoriesManager('page');
        $cats = $categoriesManager->getCategories();
        $data = [];
        foreach ($cats as $cat) {
            /** @var Categorie $cat */
            $p = new pageItem();
            $p->name = $cat->label;
            $p->isHidden = $cat->pluginArgs['hide'];
            $p->id = 'cat-' . $cat->id;
            $p->type = PageItem::CATEGORIE;
            $p->position = $cat->pluginArgs['position'] ?? 99999999999999;
            $p->parent = $cat->parentId;
            $data['cat-' . $cat->id] = $p;
        }
        return $data;
    }

    protected function imbricateItems() {
        $items = $this->items;
        foreach ($items as $item) {
            if ($item->parent !== 0 && $item->parent !== '') {
                echo $item->name . ' ';
                if ($item->type === PageItem::CATEGORIE) {
                    $item->parent = 'cat-' . $item->parent;
                }
                foreach ($this->items as $i) {
                    $res = $i->getItemById($item->parent);
                    if (is_object($res) && get_class($res) === 'PageItem') {
                        $res->addChild($item);
                        unset($items[$item->id]);
                        break 1;
                    }
                }
            }
        }
        $this->calcDepth($items, 0);
        return $items;
    }

    protected function calcDepth(&$pageItems, int $depth = 0) {
        foreach ($pageItems as &$page) {
            $page->depth = $depth;
            if ($page->hasChildren) {
                $this->calcDepth($page->children, $depth + 1);
            }
        }
    }

}
