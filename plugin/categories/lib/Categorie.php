<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class Categorie implements JsonSerializable {

    public $id;
    public $pluginId;
    protected $file;
    public $items = [];
    public $label = '';
    public int $parentId;
    protected array $childrenId = [];
    protected array $children = [];
    public bool $isChild = false;
    public bool $hasChildren = false;
    public int $depth = 0;

    public function __construct(string $pluginId, int $id = -1) {
        $this->id = $id;
        $this->pluginId = $pluginId;
        $this->file = DATA_PLUGIN . $this->pluginId . '/categories.json';
        if ($this->id !== -1) {
            $metas = util::readJsonFile($this->file);
            $this->items = $metas[$this->id]['items'] ?? [];
            $this->label = $metas[$this->id]['label'];
            $this->childrenId = $metas[$this->id]['childrenId'];
            $this->parentId = $metas[$this->id]['parentId'];
        }
    }

    public function jsonSerialize() {
        return ['items' => [$this->items],
            'label' => $this->label];
    }

    public function outputToAdmin($mode = self::CHECKBOX) {
        $catDisplay = 'sub';
        require 'categories.php';
    }
    
    public function outputAsCheckbox($itemId) {
        $catDisplay = 'sub';
        require PLUGINS . 'categories/template/checkboxCategories.php';
    }
    
    public function outputAsList() {
        $catDisplay = 'sub';
        require PLUGINS . 'categories/template/listCategories.php';
    }
    
        public function getCategorieById(int $id) {
        if ($id === $this->id) {
            // We search this categorie
            return $this;
        }
        if (empty($this->children)) {
            // No child
            return false;
        }
        foreach ($this->children as $parent) {
            // Search in childs
            $res = $parent->getCommentById($id);
            if (is_object($res) && get_class($res) === get_class($this)) {
                return $res;
            }
        }
        return false;
    }
    
    public function addChild(Categorie $categorie) {
        $categorie->isChild = true;
        $categorie->depth = $this->depth + 1;
        $this->hasChildren = true;
        $this->children[$categorie->id] = $categorie;
    }

}