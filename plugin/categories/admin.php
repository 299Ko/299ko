<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

$action = $_GET['action'] ?? '';

switch ($action) {
    default :
        $pluginId = $_GET['plugin'] ?? false;
        if ($pluginId) {
            if (!CategoriesManager::isPluginUseCategories($pluginId)) {
                core::getInstance()->error404();
            }
            $categoriesManager = new CategoriesManager($pluginId);
        } else {
            $pluginsWithCategories = [];
            foreach ($pluginsManager->getPlugins() as $p) {
                if (CategoriesManager::isPluginUseCategories($p->getName())) {
                    $pluginsWithCategories[] = $p;
                }
            }
        }
        break;
}