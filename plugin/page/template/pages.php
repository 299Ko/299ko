<?php
/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

switch ($pageDisplay) {
    case 'root':
        // Pages Container
        ?>
        <div class="list-item-container">
            <div class="list-item-list">
                <div>Nom</div>
                <div>Adresse</div>
                <div>Type</div>
                <div>Position</div>
                <div>Actions</div>
            </div>
            <?php
            if (empty($this->nestedItems)) {
                echo '<div class="list-item-list">Aucune page. Ajoutez en une.</div>';
            } else {
                foreach ($this->nestedItems as $page) {
                    $page->output();
                }
            }
            ?>
        </div>
        <?php
        break;
    case 'sub':
        // Pages
        echo '<div id="page-' . $this->id . '" name="' . $this->name . '" class="list-item-list ';
        if ($this->hasChildren) {
            echo 'hasChildren"><i style="left:' . ($this->depth * 15 + 5 ) . 'px;" class="fa-solid fa-caret-down list-item-toggle"></i>';
        } else {
            echo '">';
        }
        echo '<div style="padding-left:' . $this->depth * 15 . 'px;">' . str_repeat("-", ($this->depth * 2)) . ' ' . $this->name . '</div><div>';
        if ($this->type === PageItem::CATEGORIE) {
            echo count($this->children) . ' élément(s)';
        } else {
            echo '<input readonly="readonly" type="text" value="' . $this->getUrl() . '" />';
        }
        echo '</div><div>';
        echo $this->type;
        echo $this->position;
        
        echo '</div><div>';
        echo '<a class="up" href="index.php?p=page&action=up&id=' . $this->id . '&token='.administrator::getToken() .'"><i class="fa-solid fa-circle-up"></i></a>';
        echo '<a class="down" href="index.php?p=page&action=down&id=' . $this->id . '&token='.administrator::getToken() .'"><i class="fa-solid fa-circle-down"></i></a>';

        echo '</div><div class="buttons-bar">';
        if ($this->type === PageItem::CATEGORIE) {
            echo '<a class="button" title="Editer la catégorie" href="index.php?p=categories&action=edit&plugin=page&id=' . str_replace('cat-', '', $this->id) . '"><i class="fa-solid fa-pencil"></i></a>';
            echo '<a class="button alert" title="Supprimer la catégorie" href="index.php?p=page&action=del&plugin=page&id=' . str_replace('cat-', '', $this->id) . '&token=' . administrator::getToken() . '" onclick="if (!confirm(\'Supprimer cet élément ?\')) return false;"><i class="fa-solid fa-trash"></i></a>';
        } else {
            echo '<a class="button" title="Editer l\'élément" href="index.php?p=page&amp;action=edit&amp;id=' . $this->id . '"><i class="fa-solid fa-pencil"></i></a>';
            if (!$this->isHomepage && $this->type !== PageItem::PLUGIN) {
                echo '<a class="button alert" href="index.php?p=page&action=del&id=' . $this->id . '&token=' . administrator::getToken() . 'onclick="if (!confirm(\'Supprimer cet élément ?\')) return false;"><i class="fa-solid fa-trash"></i></a>';
            }
        }


        echo '</div></div>';
        if ($this->hasChildren) {
            echo '<div class="toggle">';
            foreach ($this->children as $child) {
                $child->output();
            }
            echo '</div>';
        }
        break;
}