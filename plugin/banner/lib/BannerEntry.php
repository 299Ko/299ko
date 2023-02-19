<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class BannerEntry implements JsonSerializable {

    public $id;
    public $title;
    public $bouton;
    public $label;
    public $imageHref;
    public $order;

    public function jsonSerialize(): mixed {
        return
                ['id' => $this->id,
                    'title' => $this->title,
                    'bouton' => $this->bouton,
                    'label' => $this->label,
                    'imageHref' => $this->imageHref,
                    'order' => $this->order];
    }
    
    public function hydrateByArray(array $config) {
        $this->id = $config['id'];
        $this->title = $config['title'];
        $this->bouton = $config['bouton'];
        $this->label = $config['label'];
        $this->imageHref = $config['imageHref'];
        $this->order = $config['order'];
    }

}
