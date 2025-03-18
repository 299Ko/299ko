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
 * ThemesMarketController
 *
 * This controller manages the themes page in the admin panel.
 * It updates the themes cache from GitHub, displays a list of themes with their
 * installation and update statuses, and handles the installation of theme releases.
 */
class ThemesMarketController extends AdminController
{
    public function index() {
        // Update the themes cache
        $cacheManager = new CacheManager();
        $cacheManager->updateCacheFile('theme');
        $themesCacheFile = $cacheManager->getCacheFile('theme');

        // Read the themes cache file
        $data = util::readJsonFile($themesCacheFile);
        if (!is_array($data)) {
            die("Error reading themes.json file.");
        }
        $themes = $data['themes'] ?? [];

        // For each theme, determine if it is installed and if an update is needed
        foreach ($themes as &$theme) {
            $themeDir = ROOT . 'theme' . DS . $theme['directory'] . DS;
            $theme['is_installed'] = is_dir($themeDir);
            if ($theme['is_installed']) {
                $commitFile = $themeDir . 'commit.sha';
                $localSHA = file_exists($commitFile) ? trim(file_get_contents($commitFile)) : '';
                $theme['local_sha'] = $localSHA;
                $theme['update_needed'] = ($localSHA !== $theme['CommitGithubSHA']);
            } else {
                $theme['update_needed'] = false;
            }
        }
        unset($theme);

        // Prepare the admin response with the themes marketplace template
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace-themes');
        $tpl->set('router', ROUTER::getInstance());
        $tpl->set('themesList', $themes);
        $response->addTemplate($tpl);
        return $response;
    }

    public function installRelease() {
        // Retrieve parameters from GET request
        $type = isset($_GET['type']) ? $_GET['type'] : 'theme';
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
