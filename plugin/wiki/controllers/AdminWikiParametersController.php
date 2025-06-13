<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 *
 * Wiki Plugin for 299Ko CMS
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 *
 * AdminWikiParametersController.php
 *
 * This controller manages the Wiki plugin settings.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class AdminWikiParametersController extends AdminController {
    protected $configFile;

    /**
     * Constructor.
     *
     * Sets the configuration file path and creates it (with an empty JSON object) if it does not exist.
     */
    public function __construct() {
        $this->configFile = DATA_PLUGIN . 'wiki' . DS . 'config.json';
        if (!file_exists($this->configFile)) {
            file_put_contents($this->configFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Display the configuration settings in the admin panel.
     *
     * @return AdminResponse
     */
    public function index() {
        $config = json_decode(file_get_contents($this->configFile), true);
        $response = buildAdminResponse('wiki', 'admin/wiki-parameters', [
            'config' => $config
        ]);
        $response->setTitle("Wiki Settings");
        return $response;
    }

    /**
     * Save the updated configuration settings.
     */
    public function save() {
        $data = $_POST;
        $existingConfig = [];
        if (file_exists($this->configFile)) {
            $existingConfig = json_decode(file_get_contents($this->configFile), true);
            if (!is_array($existingConfig)) {
                $existingConfig = [];
            }
        }
        $newConfig = array_merge($existingConfig, $data);
        file_put_contents($this->configFile, json_encode($newConfig, JSON_PRETTY_PRINT));
        header("Location: " . getRouter()->generate('admin-wiki-parameters'));
        exit;
    }
}
