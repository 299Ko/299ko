<?php
/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

switch ($catDisplay) {
    case 'root':
        // Categories Container
        ?>
        <div class="list-item-container">
            <article>
                <header>
            <div class="list-item-list">
                <div>Nom</div>
                <div>Nombre d'éléments</div>
                <div>Actions</div>
            </div>
                </header>
            <?php
            if (empty($this->imbricatedCategories)) {
                echo '<div class="list-item-list">Aucune catégorie. Ajoutez en une.</div>';
            } else {
                foreach ($this->imbricatedCategories as $cat) {
                    $cat->outputAsList();
                }
            }
            ?>
            <div id="list-item-endlist">
                <div id="categorie-add-form-container" class="list-item-list">
                    <form id="categorie-add-form" name="categorie-add-form" method="post" action="index.php?p=categories&action=add&plugin=<?php echo $this->pluginId ?>" >
                        <article>
                            <header><h4 id="head-add-cat">Ajouter une catégorie</h4></header>
                        <button class="btn alert" title="Annuler la catégorie enfant" id="category-child-delete"><i class="bi bi-folder-minus"></i></button>
                        <input type="hidden" name="categorie-parentId" id="categorie-parentId" value="0" />
                        <?php show::adminTokenField(); ?> 
                        <input type="hidden" name="categorie-plugin" id="categorie-plugin" value="<?php echo $this->pluginId ?>" />
                        <div class="input-field">
                            <label class="active" for="categorie-add-label">Nom de la catégorie</label>
                            <input type="text" name="categorie-add-label" id="categorie-add-label"/>
                        </div>
                        <button class="btn" type="submit" id="list-item-add-btn">Ajouter une catégorie</button>
                        </article>
                    </form>
                </div>
            </div>
            </article>
        </div>
        <?php
        break;
    case 'sub':
        // Categories
        echo '<div id="categorie-' . $this->id . '" name="' . $this->label . '" class="list-item-list ';
        if ($this->hasChildren) {
            echo 'hasChildren"><i style="left:' . ($this->depth * 15 + 5 ) . 'px;" class="bi bi-chevron-up list-item-toggle" title="Replier/Déplier les éléments enfants"></i>';
        } else {
            echo '">';
        }
        echo '<div style="padding-left:' . ($this->depth * 15 + 10) . 'px;">' . str_repeat("-", ($this->depth * 2)) . ' ' . $this->label . '</div>';
        echo '<div>' . count($this->items) . '</div>';
        echo '<div role="group">';
        echo '<a role="button" href="#" title="Ajouter une catégorie enfant" class="btn-add-categorie" data-id="' . $this->id . '"><i class="bi bi-folder-plus"></i></a>';
        echo '<a role="button" title="Editer la catégorie" href="index.php?p=categories&action=edit&plugin=' . $this->pluginId . '&id=' . $this->id . '"><i class="bi bi-pencil"></i></a>';
        echo '<a role="button" class="alert" title="Supprimer la catégorie" href="index.php?p=categories&action=del&plugin=' . $this->pluginId . '&id=' . $this->id . '&token=' . administrator::getToken() . '" onclick="if (!confirm(\'Supprimer cet élément ?\')) return false;"><i class="bi bi-folder-minus"></i></a></div>';
        echo '</div>';
        if ($this->hasChildren) {
            echo '<div class="toggle">';
            foreach ($this->children as $child) {
                $child->outputAsList();
            }
            echo '</div>';
        }
        break;
}

    