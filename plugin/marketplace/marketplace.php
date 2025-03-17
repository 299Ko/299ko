<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 *
 * Marketplace Plugin for 299Ko CMS
 *
 * Ce plugin fournit une marketplace permettant aux utilisateurs
 * d’installer directement des plugins et des thèmes depuis GitHub.
 *
 * This plugin provides a marketplace that allows users to install
 * plugins and themes directly from GitHub.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

defined('ROOT') or exit('Access denied!');

/**
 * marketplaceInstall
 *
 * FR: Initialise le système de cache en créant le dossier et le fichier JSON
 * EN: Initialize the caching system by creating the cache folder and JSON file.
 */
function marketplaceInstall() {
    // Chemin du dossier cache
    // Path for the cache folder
    $cacheDir = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS;
    // Chemin du fichier JSON qui contient la liste des plugins
    // Path to the JSON file that stores the list of plugins
    $pluginsFile = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS . 'plugins.json';
    $themesFile = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS . 'themes.json';


    // Créer le dossier cache s'il n'existe pas (création récursive)
    // Create the cache directory if it does not exist (recursive creation enabled)
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    // Si le fichier JSON n'existe pas, le créer avec un tableau vide
    // If the JSON file does not exist, create it with an empty array
    if (!file_exists($pluginsFile)) {
        $pluginsData = [];
        util::writeJsonFile($pluginsFile, $pluginsData);
    }

    // Si le fichier JSON n'existe pas, le créer avec un tableau vide
    // If the JSON file does not exist, create it with an empty array
    if (!file_exists($themesFile)) {
        $themesData = [];
        util::writeJsonFile($themesFile, $themesData);
    }
}

/**
 * renderPagination
 *
 * FR: Fonction de pagination simplifiée qui affiche des liens de pagination
 *     en fonction du terme de recherche et de la page courante.
 * EN: Simple pagination function that outputs pagination links based on the search term and current page.
 *
 * @param string $type         Type d'élément ('plugins' ou 'themes') / Type of element ('plugins' or 'themes')
 * @param array  $paginationData Tableau contenant 'current' et 'total' / Array containing 'current' and 'total'
 * @param string $search       Terme de recherche / Search term
 * @return string              HTML généré pour la pagination / Generated HTML for pagination
 */
function renderPagination($type, $paginationData, $search) {
    $html = '<div class="pagination">';
    // Boucle pour générer un lien pour chaque page
    // Loop to generate a link for each page
    for ($i = 1; $i <= $paginationData['total']; $i++) {
        // Déterminer la classe active pour la page courante
        // Determine the active class for the current page
        $active = ($i == $paginationData['current']) ? 'active' : '';
        // Générer la query string pour le lien
        // Build the query string for the URL
        $query = http_build_query([
            'search' => $search,
            'page'   => $i
        ]);
        // Générer l'URL de la route de la marketplace admin
        // Generate the URL for the admin marketplace route
        $url = ROUTER::getInstance()->generate('admin-marketplace') . '?' . $query;
        $html .= '<a class="' . $active . '" href="' . $url . '">' . $i . '</a>';
    }
    $html .= '</div>';
    return $html;
}
