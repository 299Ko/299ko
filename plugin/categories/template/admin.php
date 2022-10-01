<?php
/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

include_once(ROOT . 'admin/header.php');

switch ($action) {
    default :
        if ($pluginId) {
            echo '<h3>Catégories du plugin ' . $pluginId . '</h3>';
            echo $categoriesManager->outputAsList();
        } else {
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom du plugin</th>
                        <th>Gérer les catégories</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pluginsWithCategories as $p) { ?>
                        <tr>
                            <td><?php echo $p->getName(); ?></td>
                            <td><a href="index.php?p=categories&plugin=<?php echo $p->getName(); ?>">Gérer les catégories</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }

        break;
}

include_once(ROOT . 'admin/footer.php');
