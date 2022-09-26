<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
session_start();
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'common/config.php');
include_once(COMMON . 'util.class.php');
include_once(COMMON . 'core.class.php');
include_once(COMMON . 'pluginsManager.class.php');
include_once(COMMON . 'plugin.class.php');
include_once(COMMON . 'show.class.php');
include_once(COMMON . 'template.class.php');
$core = core::getInstance();
if (!$core->isInstalled()) {
    header('location:' . ROOT . 'install.php');
    die();
}
$pluginsManager = pluginsManager::getInstance();
foreach ($pluginsManager->getPlugins() as $plugin) {
    include_once($plugin->getLibFile());
    if ($plugin->getConfigVal('activate')) {
        foreach ($plugin->getHooks() as $name => $function) {
            $core->addHook($name, $function);
        }
    }
}
## $runPLugin représente le plugin en cours d'execution et s'utilise avec la classe plugin & pluginsManager
$runPlugin = $pluginsManager->getPlugin($core->getPluginToCall());

Template::addGlobal('COMMON', COMMON);
Template::addGlobal('DATA', DATA);
Template::addGlobal('UPLOAD', UPLOAD);
Template::addGlobal('DATA_PLUGIN', DATA_PLUGIN);
Template::addGlobal('THEMES', THEMES);
Template::addGlobal('PLUGINS', PLUGINS);
Template::addGlobal('THEME_PATH', THEMES . $core->getConfigVal('theme') . '/' );
Template::addGlobal('VERSION', VERSION);
Template::addGlobal('runPlugin', $runPlugin);