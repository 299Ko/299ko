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
        if (empty($this->imbricatedCategories)) {
            echo 'Aucune catégorie. <a href="index.php?p=categories&plugin=' . $this->pluginId . '"> Ajoutez en une</a>';
            return;
        }
        ?>
        <select name="categorie-one">
            <option value="0" <?php if ($parentId == 0) echo ' selected'; ?> >Aucune catégorie</option>
        
        <?php
        foreach ($this->imbricatedCategories as $cat) {
            $cat->outputAsSelectOne($itemId);
        }
        ?>
        </select>
        <?php
        break;
    case 'sub':
        // Categories
        ?>
        <option value="<?php echo $this->id; ?>" <?php if (in_array($itemId, $this->items)) echo ' selected'; ?> >
            <?php echo str_repeat("-", ($this->depth * 2)) . ' ' . $this->label; ?>
        </option>
        <?php if ($this->hasChildren) {
            foreach ($this->children as $child) {
                $child->outputAsSelectOne($itemId);
            }
        }
        break;
}