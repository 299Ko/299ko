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
        <div class="categories-container">
            <div class="categorie-list">
                <div>Nom</div>
                <div>Nombre d'éléments</div>
                <div>Actions</div>
            </div>
            <?php
            if (empty($this->imbricatedCategories)) {
                echo 'Aucune catégorie. <a href="index.php&p=categories&plugin=' . $this->pluginId . '>Ajoutez en une</a>';
            } else {
                foreach ($this->imbricatedCategories as $cat) {
                    $cat->outputAsList();
                }
                ?>
                <div id="categorie-endlist">
                    <div id="categorie-add-form-container" class="categorie-list">
                        <form id="categorie-add-form" name="categorie-add-form" method="post" action="index.php?p=categories&action=add&plugin=<?php echo $this->pluginId ?>" >
                            <h4>Ajouter une catégorie</h4>
                            <button class="alert" title="Annuler la catégorie enfant" id="category-child-delete"><i class="fa-solid fa-delete-left"></i></button>
                            <input type="hidden" name="categorie-parentId" id="categorie-parentId" value="0" />
                            <?php show::adminTokenField(); ?> 
                            <input type="hidden" name="categorie-plugin" id="categorie-plugin" value="<?php echo $this->pluginId ?>" />
                            <label for="category-add-label">Nom de la catégorie</label>
                            <input type="text" name="categorie-add-label" id="categorie-add-label"/>
                            <button type="submit" id="categorie-add-btn">Ajouter une catégorie</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
        break;
    case 'sub':
        // Categories
        echo '<div id="categorie-' . $this->id . '" name="' . $this->label . '" class="categorie-list ';
        if ($this->hasChildren) {
            echo 'hasChildren"><i class="fa-solid fa-caret-down categories-toggle"></i>';
        } else {
            echo '">';
        }
        echo '<div>' . str_repeat("-", ($this->depth * 2)) . ' ' . $this->label . '</div>';
        echo '<div>' . count($this->items) . '</div>';
        echo '<div class="buttons-bar"><button title="Ajouter une catégorie enfant" class="btn-add-categorie" data-id="' . $this->id . '"><i class="fa-solid fa-folder-plus"></i></button>';
        echo '<a class="button alert" title="Supprimer la catégorie" href="index.php?p=categories&action=del&plugin=' . $this->pluginId . '&id=' . $this->id . '&token=' . administrator::getToken() . '" onclick="if (!confirm(\'Supprimer cet élément ?\')) return false;"><i class="fa-solid fa-trash"></i></a></div>';
        echo '</div>';
        if ($this->hasChildren) {
            echo '<div class="categories-toggle">';
            foreach ($this->children as $child) {
                $child->outputAsList();
            }
            echo '</div>';
        }
        break;
}

    