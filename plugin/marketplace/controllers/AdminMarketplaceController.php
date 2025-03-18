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
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

defined('ROOT') or exit('Access denied!');

/**
 * AdminMarketplaceController
 *
 * This controller manages the marketplace homepage in the admin panel.
 * It ensures that the cache files for plugins and themes are valid and updated.
 * Then, it randomly selects 5 plugins and 5 themes to display.
 */
class AdminMarketplaceController extends AdminController
{
    public function index() {
        // Create an instance of CacheManager to handle cache operations
        $cacheManager = new CacheManager();

        // Retrieve the paths of the cache files for plugins and themes
        $pluginsCacheFile = $cacheManager->getCacheFile('plugin');
        $themesCacheFile  = $cacheManager->getCacheFile('theme');

        // Read the plugins cache file
        $pluginsData = util::readJsonFile($pluginsCacheFile);
        // If the plugins data is not an array or is empty, update the cache
        if (!is_array($pluginsData) || empty($pluginsData)) {
            $cacheManager->updateCacheFile('plugin');
            $pluginsData = util::readJsonFile($pluginsCacheFile);
        }

        // Read the themes cache file
        $themesData = util::readJsonFile($themesCacheFile);
        // If the themes data is not an array or is empty, update the cache
        if (!is_array($themesData) || empty($themesData)) {
            $cacheManager->updateCacheFile('theme');
            $themesData = util::readJsonFile($themesCacheFile);
        }

        // Randomly select 5 plugins from the cache data
        $plugins = isset($pluginsData['plugins']) ? $pluginsData['plugins'] : [];
        shuffle($plugins);
        $randomPlugins = array_slice($plugins, 0, 5);

        // Randomly select 5 themes from the cache data
        $themes = isset($themesData['themes']) ? $themesData['themes'] : [];
        shuffle($themes);
        $randomThemes = array_slice($themes, 0, 5);

        // Prepare the admin response using the marketplace template
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace');
        $tpl->set('router', ROUTER::getInstance());
        $tpl->set('randomPlugins', $randomPlugins);
        $tpl->set('randomThemes', $randomThemes);
        $tpl->set('pluginsPageUrl', ROUTER::getInstance()->generate('marketplace-plugins'));
        $tpl->set('themesPageUrl', ROUTER::getInstance()->generate('marketplace-themes'));
        $response->addTemplate($tpl);
        return $response;
    }
}
