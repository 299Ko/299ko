<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */

/**
 * This class is used to create nested Menu.
 * For the moment, only one menu is possible (static class)
 */
class Menu {

    /**
     * @var MenuItem Array
     */
    protected static $items = [];

    /**
     * Add an item to display in the menu
     * @param MenuItem $item
     */
    public static function addMenuItem(MenuItem $item) {
        self::$items[] = $item;
    }

    /**
     * Display the menu and all of his items
     * @param string ID to add
     * @param string CSS class to add
     */
    public static function output($id = false, $class = false) {
        if ($id || $class) {
            echo '<ul ';
            if ($id) {
                echo 'id="' . $id . '" ';
            }
            if ($class) {
                echo 'class="' . $class . '" ';
            }
            echo '>';
        }
        foreach (self::$items as $item) {
            $item->output();
        }
        if ($id || $class) {
            echo '</ul>';
        }
    }

}

/**
 * MenuItem is used to typehint all possible items when adding to Menu
 * @abstract
 */
abstract class MenuItem {

    /**
     * @var string Displayed Text
     */
    protected $label;

    /**
     * @var string Item Identifier
     */
    protected $id;

    /**
     * @var string CSS class
     */
    protected $class;

    /**
     * Display the item and all his children
     * @abstract
     */
    abstract public function output();

    /**
     * Create a MenuItem
     * 
     * @param string Displayed Text
     * @param string Item Identifier
     * @param string CSS class
     */
    public function __construct(string $label, $id = false, $class = false) {
        $this->label = $label;
        $this->id = $id;
        $this->class = $class;
    }

}

/**
 * A parent Item is a <li> parent with an <ul>
 * Children items are displayed in this <ul>
 */
class ParentItem extends MenuItem {

    /**
     * @var MenuItem Array
     */
    protected $items = [];

    /**
     * Add an item to display in this parent
     * @param MenuItem $item
     */
    public function addMenuItem(MenuItem $item) {
        $this->items[] = $item;
    }

    /**
     * Display this item and all his children
     * @return empty if not child
     */
    public function output() {
        if (empty($this->items)) {
            return;
        }
        echo '<li class="parent"><a href="javascript:" ';
        if ($this->id) {
            echo 'id="' . $this->id . '" ';
        }
        if ($this->id) {
            echo 'class="' . $this->class . '" ';
        }
        echo '>' . $this->label . '</a><ul>';
        foreach ($this->items as $item) {
            $item->output();
        }
        echo '</ul></li>';
    }

}

/**
 * Item as a <li> element with a <a> link
 * Label, ID and class are affected to the <a> element, not the <li>
 */
class Item extends MenuItem {

    /**
     * @var string Target href link
     */
    protected $target;
    
    /**
     * targetAttr, _self or _blank, same or new window
     * @var string Target Attribute
     */
    protected $targetAttr;

    public function __construct(string $label, string $target, $id = false, $class = false, $targetAttr = '_self') {
        parent::__construct($label, $id, $class);
        $this->target = $target;
        $this->targetAttr = $targetAttr;
    }

    public function output() {
        echo '<li><a href="' . $this->target . '" ';
        if ($this->id) {
            echo 'id="' . $this->id . '" ';
        }
        if ($this->class) {
            echo 'class="' . $this->class . '" ';
        }
        echo 'target="' . $this->targetAttr . '">';
        echo $this->label;
        echo '</a></li>';
    }

}
