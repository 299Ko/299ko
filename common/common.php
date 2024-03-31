<?php

/**
 * @copyright (C) 2024, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
session_start();
defined('ROOT') or exit('No direct script access allowed');

include_once(ROOT . 'common/config.php');

// Autoload class in COMMON directory
spl_autoload_register(function ($class) {
	$className = strtolower($class);
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(COMMON));
	foreach ($iterator as $file)
	{
		if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
			$fileName = strtolower($file->getFilename());
			if ($fileName === $className . '.php' || $fileName === $className . '.class.php') {
				include_once($file);
				break;
			}
		}
	}
});

$router = router::getInstance();
$core = core::getInstance();

$pluginsManager = pluginsManager::getInstance();
foreach ($pluginsManager->getPlugins() as $plugin) {
    if ($plugin->getConfigVal('activate')) {
        $plugin->loadLangFile();
        $plugin->loadRoutes();
		if ($plugin->getLibFile() !== false) {
			include_once($plugin->getLibFile());
		}
        foreach ($plugin->getHooks() as $name => $function) {
            $core->addHook($name, $function);
        }
    }
}

lang::loadLanguageFile(THEMES . $core->getConfigVal('theme') . '/langs/');

## $runPLugin représente le plugin en cours d'execution et s'utilise avec la classe plugin & pluginsManager
$runPlugin = $pluginsManager->getPlugin($core->getPluginToCall());

Template::addGlobal('COMMON', COMMON);
Template::addGlobal('DATA', DATA);
Template::addGlobal('UPLOAD', UPLOAD);
Template::addGlobal('DATA_PLUGIN', DATA_PLUGIN);
Template::addGlobal('THEMES', THEMES);
Template::addGlobal('PLUGINS', PLUGINS);
Template::addGlobal('THEME_PATH', THEMES . $core->getConfigVal('theme') . '/');
Template::addGlobal('SITE_URL', $core->getConfigVal('siteUrl'));
Template::addGlobal('ADMIN_URL', $router->generate('admin'));
Template::addGlobal('VERSION', VERSION);
Template::addGlobal('runPlugin', $runPlugin);
Template::addGlobal('ROUTER', $router);
Template::addGlobal('pluginsManager', $pluginsManager);
Template::addGlobal('CORE', $core);
Template::addGlobal('ADMIN_PATH', ADMIN_PATH);
