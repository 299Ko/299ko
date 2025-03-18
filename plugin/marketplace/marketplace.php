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

// Inclusion du fichier contenant la classe CacheManager
require_once 'controllers/CacheManager.php';


/**
 * Fonction d'installation du plugin Marketplace.
 *
 * Cette fonction instancie la classe CacheManager pour initialiser le cache.
 * La création du cache (répertoire et fichier de configuration) est effectuée
 * automatiquement dans le constructeur de la classe CacheManager.
 *
 * @return void
*/
function marketplaceInstall() {
    // Instanciation de CacheManager : le constructeur crée le dossier de cache
    // et initialise le fichier de configuration (config.json)
    $cacheManager = new CacheManager();

    // Mise à jour du cache pour les plugins
    $cacheManager->updateCacheFile('plugin');

    // Mise à jour du cache pour les thèmes
    $cacheManager->updateCacheFile('theme');

    // Message d'information (optionnel)
    echo "Installation du plugin Marketplace terminée avec succès.";
}

