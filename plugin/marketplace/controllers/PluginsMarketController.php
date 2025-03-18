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
 * PluginsMarketController
 *
 * This controller manages the plugins page in the admin panel.
 * It updates the plugins cache from GitHub, displays a list of plugins with their
 * installation and update statuses, and handles the installation of plugin releases.
 */
class PluginsMarketController extends AdminController
{
    public function index() {
        // Update the plugins cache
        $cacheManager = new CacheManager();
        $cacheManager->updateCacheFile('plugin');
        $pluginsCacheFile = $cacheManager->getCacheFile('plugin');

        // Read the plugins cache file
        $data = util::readJsonFile($pluginsCacheFile);
        if (!is_array($data)) {
            die("Error reading plugins.json file.");
        }
        $plugins = $data['plugins'] ?? [];

        // For each plugin, determine if it is installed and if an update is needed
        foreach ($plugins as &$plugin) {
            $pluginDir = PLUGINS . $plugin['directory'] . DS;
            $plugin['is_installed'] = is_dir($pluginDir);
            if ($plugin['is_installed']) {
                $commitFile = $pluginDir . 'commit.sha';
                $localSHA = file_exists($commitFile) ? trim(file_get_contents($commitFile)) : '';
                $plugin['local_sha'] = $localSHA;
                $plugin['update_needed'] = ($localSHA !== $plugin['CommitGithubSHA']);
            } else {
                $plugin['update_needed'] = false;
            }
        }
        unset($plugin);

        // Prepare the admin response with the plugins marketplace template
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace-plugins');
        $tpl->set('router', ROUTER::getInstance());
        $tpl->set('pluginsList', $plugins);
        $response->addTemplate($tpl);
        return $response;
    }

    public function installRelease() {
        // Retrieve parameters from GET request
        $type = isset($_GET['type']) ? $_GET['type'] : 'plugin';
        if (!isset($_GET['folder'])) {
            die("No folder specified.");
        }
        $folder = $_GET['folder'];
        $commitSHA = isset($_GET['commit']) ? $_GET['commit'] : '';

        // Retrieve configuration using CacheManager
        $cacheManager = new CacheManager();
        $config = $cacheManager->getConfig();
        if (!$config) {
            die("Error reading config.json file.");
        }
        if (!isset($config['repos'][$type])) {
            die("Unknown type.");
        }

        // Use MarketplaceInstaller to handle the installation process
        $installer = new MarketplaceInstaller();
        $router = ROUTER::getInstance();
        $installer->installRelease($type, $folder, $commitSHA, $config, $router);
    }
}
