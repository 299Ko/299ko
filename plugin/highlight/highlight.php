<?php

/**
 * @copyright (C) 2022, 299Ko/HighLight
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

## Fonction d'installation

function highlightInstall() {
    
}

function highlightGetThemeCSSUrl($theme) {
    $t = 'default';
    foreach (highlightGetThemes() as $k => $v) {
        if ($theme === $k) {
            $t = $theme;
        }
    }
    return 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/' . $t . '.min.css';
}

function highlightGetThemes() {
    return [
        'default' => 'Default',
        'a11y-dark' => 'A 11 Y Dark',
        'a11y-light' => 'A 11 Y Light',
        'arta' => 'Arta',
        'github' => 'GitHub',
        'github-dark' => 'GitHub Dark',
        'monokai-sublime' => 'Monokai Sublime',
        'vs' => 'VS',
        'vs2015' => 'VS 2015'
    ];
}

function highlightEndFrontHead() {
    $plugin = pluginsManager::getInstance()->getPlugin('highlight');
    echo '<link rel="stylesheet" href="' . highlightGetThemeCSSUrl($plugin->getConfigVal('theme')) . '" type="text/css"/>';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>';
}

function highlightEndFrontBody() {
    echo "<script>hljs.highlightAll();</script>";
}