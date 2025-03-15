<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 * Marketplace Plugin for 299Ko CMS
 *
 * Ce plugin fournit une marketplace permettant aux utilisateurs
 * d’installer directement des plugins et des thèmes depuis GitHub.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

defined('ROOT') or exit('Access denied!');

function marketplaceInstall() {
    // Create likes.json
    $likesFile = DATA_PLUGIN . 'marketplace' . DS . 'likes.json';
    if (!file_exists($likesFile)) {
        $likesData = ["plugins" => [], "themes" => []];
        util::writeJsonFile($likesFile, $likesData);
    }
}

/**
 * Fonction de pagination simplifiée.
 * Affiche les pages de résultats en ne prenant en compte que le terme de recherche et la page.
 *
 * @param string $type Type d'élément ('plugins' ou 'themes')
 * @param array $paginationData Tableau avec 'current' et 'total'
 * @param string $search Terme de recherche
 * @return string HTML de la pagination
 */
function renderPagination($type, $paginationData, $search) {
    $html = '<div class="pagination">';
    for ($i = 1; $i <= $paginationData['total']; $i++) {
        $active = ($i == $paginationData['current']) ? 'active' : '';
        $query = http_build_query([
            'search' => $search,
            'page'   => $i
        ]);
        $url = ROUTER::getInstance()->generate('admin-marketplace') . '?' . $query;
        $html .= '<a class="' . $active . '" href="' . $url . '">' . $i . '</a>';
    }
    $html .= '</div>';
    return $html;
}
