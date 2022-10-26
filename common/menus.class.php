<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
class Menu {

    protected static $items = [];

    public static function addMenuItem(MenuItem $item) {
        self::$items[] = $item;
    }

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

abstract class MenuItem {

    protected $label;
    protected $id;
    protected $class;

    abstract public function output();

    public function __construct(string $label, $id = false, $class = false) {
        $this->label = $label;
        $this->id = $id;
        $this->class = $class;
    }

}

class ParentItem extends MenuItem {

    protected $items = [];

    public function addMenuItem(MenuItem $item) {
        $this->items[] = $item;
    }

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

class Item extends MenuItem {

    protected $target;
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
