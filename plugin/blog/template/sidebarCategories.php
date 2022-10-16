<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

?>
<ul class="cat-list">
<?php
foreach ($categories as $categorie) {
    ?>
    <li class="cat-item"><a href="<?php echo blogCreateCategorieUrl($categorie); ?>"><?php echo $categorie->label ?></a>
    <?php
    if ($categorie->hasChildren) {
        $categories = $categorie->children;
        require PLUGINS . 'blog/template/sidebarCategories.php';
    }
    ?></li><?php
}
?> </ul>