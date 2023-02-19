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
    
    public function createHomepage() {
        foreach ($this->items as $pageItem) {
            if ($pageItem->isHomepage)
                return $pageItem;
        }
        return false;
    }

    public static function addToNavigation() {
        $pageManager = new PagesManager();
        $pluginsManager = pluginsManager::getInstance();
        // Création d'items de navigation absents (plugins)
        foreach ($pluginsManager->getPlugins() as $k => $plugin)
            if ($plugin->getConfigVal('activate') && $plugin->getPublicFile() && $plugin->getName() != 'page') {
                $find = false;
                foreach ($pageManager->items as $pageItem) {
                    if ($pageItem->target === $plugin->getName())
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
        $newPagesManager = new self();
        foreach ($newPagesManager->nestedItems as $item) {
            $item->addToNavigation();
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
    
    public function delPage(PageItem $pageItem) {
        if ($pageItem->isHomepage === false && $this->countPages() > 1) {
            if ($pageItem->parent !== 0) {
                $parent = str_replace('cat-', '', $pageItem->parent);
                CategoriesManager::deleteItemFromCategories('page', $pageItem->id, $parent);
            }
            unset($this->items[$pageItem->id]);
            return $this->savePages();
        }
        return false;
    }
    
    public function countPages():int {
        $nbPages = 0;
        foreach ($this->items as $item) {
            if ($item->type === PageItem::PAGE || $item->type === PageItem::PLUGIN) {
                $nbPages++;
            }
        }
        return $nbPages;
    }

    protected function savePages() {
        $metas = [];
        foreach ($this->items as $item) {
            if ($item->type !== PageItem::CATEGORIE) {
                $metas[$item->id] = $item;
            }
            if ($item->parent && $item->parent !== 0 && $item->type !== PageItem::CATEGORIE) {
                $parent = str_replace('cat-', '', $item->parent);
                CategoriesManager::saveItemToCategories('page', $item->id, $parent);
            }
        }
        return util::writeJsonFile($this->file, $metas);
    }

    protected function saveCategories() {
        $categoriesManager = new CategoriesManager('page');
        foreach ($this->items as $item) {
            if ($item->type === PageItem::CATEGORIE) {
                $categorie = $categoriesManager->getCategorie(str_replace('cat-', '', $item->id));
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

    public function isUnlocked(PageItem $obj) {
        if ($obj->password == '')
            return true;
        elseif (isset($_SESSION['pagePassword']) && sha1($obj->id) . $obj->password . sha1($_SERVER['REMOTE_ADDR']) == $_SESSION['pagePassword'])
            return true;
        else
            return false;
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
            $p->isHidden = $cat->pluginArgs['hide'] ?? false;
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

    public function listTemplates() {
        $core = core::getInstance();
        $data = [];
        $items = util::scanDir(THEMES . $core->getConfigVal('theme') . '/', array('header.php', 'footer.php', 'style.css', '404.php', 'functions.php'));
        foreach ($items['file'] as $file) {
            if (in_array(util::getFileExtension($file), array('htm', 'html', 'txt', 'php')))
                $data[] = $file;
        }
        return $data;
    }

}
