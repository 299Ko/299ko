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
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Nombre d'éléments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($this->categories as $cat) {
                        $cat->outputAsList();
                    }
                    ?>
                </tbody>
                <tfoot id="categorie-endlist">
                    <tr id="categorie-add-form-container">
                        <td colspan="3">
                            <form id="categorie-add-form" name="categorie-add-form" method="post" action="index.php&p=categories&action=add&plugin= <?php echo $this->pluginId ?>" >
                                <h4>Ajouter une catégorie</h4>
                                <button class="alert" title="Annuler la catégorie enfant" id="category-child-delete"><i class="fa-solid fa-delete-left"></i></button>
                                <input type="hidden" name="categorie-parentId" id="categorie-parentId" value="0" />
                                <?php show::adminTokenField(); ?> 
                                <input type="hidden" name="categorie-plugin" id="categorie-plugin" value="<?php echo $this->pluginId ?>" />
                                <label for="category-add-label">Nom de la catégorie</label>
                                <input type="text" name="categorie-add-label" id="categorie-add-label"/>
                                <button type="submit" id="categorie-add-btn">Ajouter une catégorie</button>
                            </form>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
        break;
    case 'sub':
        // Categories
        echo '<tr id="categorie-' . $this->id . '" name="' . $this->label . '"><td>';
        echo str_repeat("-", ($this->depth * 2)) . ' ' . $this->label . '</td>';
        echo '<td>' . count($this->items) . '</td>';
        echo '<td><button title="Ajouter une catégorie enfant" class="btn-add-categorie" data-id="' . $this->id . '"><i class="fa-solid fa-folder-plus"></i></button></td>';
        if ($this->hasChildren) {
            foreach ($this->children as $child) {
                $child->outputAsList();
            }
        }
        echo "</tr>";
        break;
}

    