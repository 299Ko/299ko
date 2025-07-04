<?php

/**
 * @copyright (C) 2024, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

require_once PLUGINS . 'wiki/entities/wikiPage.php';

require_once PLUGINS . 'wiki/entities/wikiPageManager.php';
require_once PLUGINS . 'wiki/entities/WikiCategoriesManager.php';
require_once PLUGINS . 'wiki/entities/WikiCategory.php';
require_once PLUGINS . 'wiki/entities/wikiPageHistory.php';
require_once PLUGINS . 'wiki/entities/WikiActivityManager.php';

/**
 * Fonction d'installation du plugin Wiki
 * 
 * @return void
 */
function wikiInstall() {
    // Installation du plugin Wiki
}

/**
 * Hook pour ajouter des éléments dans le head de la page publique
 * 
 * @return string Code HTML à ajouter dans le head
 */
function wikiEndFrontHead() {
    $core = core::getInstance();
    $output = '<script src="' . $core->getConfigVal('siteUrl') . '/plugin/wiki/template/public.js"></script>' . "\n";
    
    return $output;
}

/**
 * Shortcode pour créer des liens vers des pages Wiki
 * 
 * @param array $attributes Attributs du shortcode ['id' => int, 'name' => string]
 * @return string Code HTML du lien
 */
function wikiLinkShortcode(array $attributes):string {
    $wikiPageManager = new WikiPageManager();
    $wikiPage = $wikiPageManager->create((int) $attributes['id']);
    if (!$wikiPage) {
        return '';
    }
    if (!isset($attributes['name'])) {
        $attributes['name'] = $wikiPage->getName();
    }
    return '<a href="' . router::getInstance()->generate('wiki-read', ['id' => $wikiPage->getId(), 'name' => $wikiPage->getSlug()]) . '">' . $attributes['name'] . '</a>';
}

/**
 * Hook exécuté avant le lancement du plugin
 * 
 * @return void
 */
function wikiBeforeRunPlugin() {
    ContentParser::addShortcode('wikiLink', 'wikiLinkShortcode');
    
    foreach (glob(PLUGINS . 'wiki/controllers/' . "*.php") as $file) {
        include_once $file;
    }
}




## Code relatif au plugin

