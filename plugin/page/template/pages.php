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
            <article>
                <header>
                    <div class="list-item-list">
                        <div>Nom</div>
                        <div>Adresse</div>
                        <div>Type</div>
                        <div>Position</div>
                        <div>Actions</div>
                    </div>
                </header>
                <?php
                if (empty($this->nestedItems)) {
                    echo '<div class="list-item-list">Aucune page. Ajoutez en une.</div>';
                } else {
                    foreach ($this->nestedItems as $page) {
                        $page->output();
                    }
                }
                ?>
            </article>
        </div>

        <?php
        break;
    case 'sub':
        // Pages
        echo '<div id="page-' . $this->id . '" name="' . $this->name . '" class="list-item-list ';
        if ($this->hasChildren) {
            echo 'hasChildren"><i style="left:' . ($this->depth * 15 + 5 ) . 'px;" class="bi bi-chevron-up list-item-toggle" title="Replier/Déplier les éléments enfants"></i>';
        } else {
            echo '">';
        }
        echo '<div style="padding-left:' . ($this->depth * 15 + 10) . 'px;">' . str_repeat("-", ($this->depth * 2)) . ' ' . $this->name . '</div><div>';
        if ($this->type === PageItem::CATEGORIE) {
            echo count($this->children ?? []) . ' élément(s)';
        } else {
            echo '<input readonly="readonly" class="small" type="text" value="' . $this->getUrl() . '" />';
        }
        echo '</div><div>';
        if ($this->type === PageItem::CATEGORIE) {
            echo '<i class="bi bi-folder2-open" title="Catégorie"></i>';
        } elseif ($this->type === PageItem::URL) {
            echo '<i class="bi bi-link-45deg" title="Lien"></i>';
        } elseif ($this->type === PageItem::PLUGIN) {
            echo '<i class="bi bi-puzzle" title="Plugin"></i>';
        } elseif ($this->type === PageItem::PAGE) {
            echo '<i class="bi bi-file-text" title="Page"></i>';
        }

        echo '</div><div role="verticalgroup">';
        echo '<a role="button" class="up small" href="index.php?p=page&action=up&id=' . $this->id . '&token=' . administrator::getToken() . '"><i class="bi bi-arrow-up-circle" title="Monter l\'élément"></i></a>';
        echo '<a role="button" class="down small" href="index.php?p=page&action=down&id=' . $this->id . '&token=' . administrator::getToken() . '"><i class="bi bi-arrow-down-circle" title="Descendre l\'élément"></i></a>';

        echo '</div><div role="group">';
        if ($this->type === PageItem::CATEGORIE) {
            echo '<a role="button" class="small" title="Editer la catégorie" href="index.php?p=categories&action=edit&plugin=page&id=' . str_replace('cat-', '', $this->id) . '"><i class="bi bi-pencil"></i></a>';
            echo '<a role="button" class="small alert" title="Supprimer la catégorie" href="index.php?p=page&action=del&plugin=page&id=' . str_replace('cat-', '', $this->id) . '&token=' . administrator::getToken() . '" onclick="if (!confirm(\'Supprimer cet élément ?\')) return false;"><i class="bi bi-trash2"></i></a>';
        } else {
            echo '<a role="button" class="small" title="Editer l\'élément" href="index.php?p=page&amp;action=edit&amp;id=' . $this->id . '"><i class="bi bi-pencil"></i></a>';
            if (!$this->isHomepage && $this->type !== PageItem::PLUGIN) {
                echo '<a role="button" class="small alert" href="index.php?p=page&action=del&id=' . $this->id . '&token=' . administrator::getToken() . 'onclick="if (!confirm(\'Supprimer cet élément ?\')) return false;"><i class="bi bi-trash2"></i></a>';
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