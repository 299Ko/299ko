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
 * CacheManager
 *
 * This class handles the creation, reading, and updating of the cache files used
 * by the marketplace system. It ensures that the cache directory exists,
 * initializes the configuration file (config.json), and updates cache files
 * (plugins.json and themes.json) by querying the GitHub API.
 */
class CacheManager {
    protected $cacheDir;
    protected $configFile;
    protected $pluginsCacheFile;
    protected $themesCacheFile;
    protected $config;
    protected $baseCacheConfigFile;

    public function __construct() {
        // Define cache directory and file paths
        $this->cacheDir = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS;
        $this->configFile = DATA_PLUGIN . 'marketplace' . DS . 'config.json';
        $this->pluginsCacheFile = $this->cacheDir . 'plugins.json';
        $this->themesCacheFile = $this->cacheDir . 'themes.json';
        $this->baseCacheConfigFile = PLUGINS . 'marketplace' . DS . 'param'. DS . 'config.json';
        // Initialize the cache directory and configuration file
        $this->initCache();
        $this->initConfig();
    }

    /**
     * Initialize the cache directory.
     * Creates the directory if it does not exist.
     */
    protected function initCache() {
        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0755, true)) {
                die("Error creating cache directory.");
            }
        }
    }

    /**
     * Initialize or read the configuration file (config.json).
     * If the file does not exist, it is created with default values from baseCacheConfigFile.
     * If it exists, update it without overwriting existing values.
     */
    protected function initConfig() {
        // Load default configuration from the base cache config file
        if (file_exists($this->baseCacheConfigFile)) {
            $defaultConfig = json_decode(file_get_contents($this->baseCacheConfigFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error reading base cache config file.");
            }
        } else {
            die("Base cache config file not found.");
        }

        if (!file_exists($this->configFile)) {
            // Inform that the config file is missing or invalid, then create it
            echo ("Configuration file missing or invalid. Creating default configuration.");
            file_put_contents($this->configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
            $this->config = $defaultConfig;
        } else {
            $existingConfig = json_decode(file_get_contents($this->configFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error reading config.json file.");
            }

            // Merge existing config with default config (preserve existing values)
            $this->config = array_replace_recursive($defaultConfig, $existingConfig);
            file_put_contents($this->configFile, json_encode($this->config, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Get the configuration array.
     *
     * @return array The configuration data.
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Get the cache file path based on the type.
     *
     * @param string $type Either 'plugin' or 'theme'.
     * @return string The path to the cache file.
     */
    public function getCacheFile($type) {
        return ($type === 'plugin') ? $this->pluginsCacheFile : $this->themesCacheFile;
    }

    /**
     * Update the cache file by querying the GitHub API.
     * If the cache file exists and its 'last_updated' timestamp is within the valid duration,
     * no update is performed.
     *
     * @param string $type Either 'plugin' or 'theme'.
     */
    public function updateCacheFile($type) {
        $cacheFile = $this->getCacheFile($type);
        $config = $this->getConfig();
        $cacheDuration = isset($config['cacheDuration']) ? (int)$config['cacheDuration'] : 3600;

        // Check if cache file exists and is still valid based on the last_updated timestamp
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if (isset($data['last_updated']) && (strtotime($data['last_updated']) > time() - $cacheDuration)) {
                return; // Cache is valid; no update needed
            }
        }

        // Retrieve the repository for the specified type
        $repo = $config['repos'][$type] ?? null;
        if (!$repo) {
            die("Repository not defined for type $type in config.");
        }
        $baseUrl = "https://api.github.com/repos/{$repo}/contents";

        // Make a request to the GitHub API for repository contents
        list($response, $httpCode) = $this->callGitHubRequest($baseUrl, $config);
        if ($httpCode != 200) {
            die("GitHub API error: HTTP $httpCode");
        }
        $rootContents = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("JSON decode error: " . json_last_error_msg());
        }

        $items = [];
        // Iterate over each item (directory) in the repository root
        foreach ($rootContents as $item) {
            if ($item['type'] === 'dir') {
                // Build the URL for the directory's info file (infos.json)
                $infosUrl = "{$baseUrl}/{$item['name']}/param/infos.json?ref=main";
                list($infosResponse, $infosHttpCode) = $this->callGitHubRequest($infosUrl, $config);
                if ($infosHttpCode == 200) {
                    $infosData = json_decode($infosResponse, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($infosData['content'])) {
                        $decodedContent = base64_decode($infosData['content']);
                        $infos = json_decode($decodedContent, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            // Add additional details to the cache item
                            $infos['directory'] = $item['name'];
                            $infos['CommitGithubSHA'] = $this->getLastCommitSHA($item['name'], $config, $repo);
                            $infos['type'] = $infos['type'] ?? $type;
                            $items[] = $infos;
                        }
                    }
                }
            }
        }

        // Build the data structure with a timestamp and the items array
        $data = [
            'last_updated' => date('c'),
            $type . 's' => $items
        ];
        // Write the updated cache data to the cache file
        file_put_contents($cacheFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Make a cURL request to a specified URL using the provided configuration.
     *
     * @param string $url The URL to request.
     * @param array $config The configuration array with GitHub token.
     * @return array An array containing the response and HTTP status code.
     */
    private function callGitHubRequest($url, $config) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: token " . $this->buildRemoteToken($config),
            "Accept: application/vnd.github.v3+json",
            "User-Agent: PHP-Download-System"
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            die("cURL error: " . curl_error($ch));
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$response, $httpCode];
    }

    /**
     * Get the last commit SHA for a specific directory in the repository.
     *
     * @param string $directory The directory name.
     * @param array $config The configuration array.
     * @param string $repo The repository identifier.
     * @return string The last commit SHA or an empty string if not found.
     */
    private function getLastCommitSHA($directory, $config, $repo) {
        $commitsUrl = "https://api.github.com/repos/{$repo}/commits?path={$directory}&per_page=1";
        list($commitsResponse, $httpCode) = $this->callGitHubRequest($commitsUrl, $config);
        if ($httpCode != 200) {
            die("GitHub API error (commits for $directory): HTTP $httpCode");
        }
        $commitsData = json_decode($commitsResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($commitsData)) {
            return '';
        }
        return $commitsData[0]['sha'] ?? '';
    }

    /**
     * Construit et déchiffre le token d'authentification en assemblant les deux parties.
     * Vérifie que la constante KEY est définie.
     *
     * Ici, nous utilisons str_rot13 comme transformation réversible native.
     *
     * @param array $config
     * @return string Le token original.
     */
    private function buildRemoteToken(array $config) {
        if (!defined('KEY')) {
            die("installation non legitime");
        }
        $encrypted = $config['remote_api_key_1'] . $config['remote_api_key_2'];
        $decrypted = str_rot13($encrypted);
        return $decrypted;
    }
}  