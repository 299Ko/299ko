<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class PageItem implements JsonSerializable {
    
    public $id;
    public $name;
    public $position;
    public $isHomepage;
    public $content;
    public $isHidden;
    public $file;
    public $mainTitle;
    public $metaDescriptionTag;
    public $metaTitleTag;
    public $target;
    public $targetAttr;
    public $noIndex;
    public $parent;
    public $cssClass;
    public $password;
    public $img;
    public $type;
    
    public $children;
    
    public int $depth = 0;
    
    public $isChild = false;
    public $hasChildren = false;
    
    const PAGE      = 'page';
    const CATEGORIE = 'categorie';
    const URL       = 'url';
    const PLUGIN    = 'plugin';
    
    const FILE = DATA_PLUGIN . 'page/pages.json';
    
    public function __construct($id = false) {
        if ($id !== false) {
            $metas = util::readJsonFile(self::FILE);
            if (!isset($metas[$id])) {
                return false;
            }
            foreach ($metas[$id] as $k => $v){
                $this->$k = $v;
            }
        }
        return true;
    }
    
    public function output() {
        $pageDisplay = 'sub';
        require PLUGINS . 'page/template/pages.php';
    }
    
    public function getItemById($id) {
        if ($id === $this->id) {
            // We search this item
            return $this;
        }
        if (empty($this->children)) {
            // No child
            return false;
        }
        foreach ($this->children as $parent) {
            // Search in childs
            $res = $parent->getItemById($id);
            if (is_object($res) && get_class($res) === get_class($this)) {
                return $res;
            }
        }
        return false;
    }
    
    public function addChild(PageItem $item) {
        $item->isChild = true;
        $this->hasChildren = true;
        $this->children[$item->id] = $item;
    }
    
    public function addToNavigation($parentItem = false) {
        if ($this->type === self::CATEGORIE) {
            $item = new ParentItem($this->name, $this->id, $this->cssClass);
            foreach ($this->children as $child) {
                $item = $child->addToNavigation($item);
            }
        } else {
            $item = new Item($this->name, $this->getUrl(), $this->id, $this->cssClass, $this->targetAttr);
        }
        if ($parentItem === false) {
            Menu::addMenuItem($item);
            return;
        } else {
            $parentItem->addMenuItem($item);
            return $parentItem;
        }
    }
    
    public function getUrl() {
        switch ($this->type) {
            case self::CATEGORIE:
                return '';
            case self::PAGE:
                if ($this->isHomepage) {
                    return util::urlBuild('');
                }
                return util::urlBuild('/page/' . util::strToUrl(preg_replace ("#\<i.+\<\/i\>#i", '', $this->name)) . '-' . $this->id . '.html');
            case self::URL:
                return $this->target;
            case self::PLUGIN:
                return util::urlBuild($this->target) . '/';
        }
    }
    
    public function jsonSerialize() {
        return
                [
                    'name' => $this->name,
                    'id' => $this->id,
                    'position' => $this->position,
                    'isHomepage' => $this->isHomepage,
                    'content' => $this->content,
                    'isHidden' => $this->isHidden,
                    'file' => $this->file,
                    'mainTitle' => $this->mainTitle,
                    'metaDescriptionTag' => $this->metaDescriptionTag,
                    'metaTitleTag' => $this->metaTitleTag,
                    'target' => $this->target,
                    'targetAttr' => $this->targetAttr,
                    'noIndex' => $this->noIndex,
                    'parent' => $this->parent,
                    'cssClass' => $this->cssClass,
                    'password' => $this->password,
                    'img' => $this->img,
                    'type' => $this->type];
    }
    
    public function regenPositions() {
        $pos = 1;
        foreach ($this->children as &$item) {
            $item->position = $pos;
            if ($item->hasChildren) {
                $item->regenPositions();
            }
            $pos++;
        }
    }
    
}