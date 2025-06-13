<?php
/**
 * Wiki Plugin Core Initialization File
 * --------------------------------------
 * This file initializes common settings, autoloads necessary classes,
 * performs a direct access check, and defines global utility functions to be used
 * throughout the Wiki plugin.
 *
 * @package WikiPlugin
 */

if (!defined('ROOT')) {
    exit('Access denied!');
}

if (!defined('WIKI_LOADED')) {
    define('WIKI_LOADED', true);
}

// Basic autoloading of common classes (could be replaced by a more sophisticated autoloader)
require_once __DIR__ . '/entities/WikiPage.php';
require_once __DIR__ . '/entities/WikiCategoryCMS.php';
require_once __DIR__ . '/entities/WikiCategoryManager.php';
require_once __DIR__ . '/entities/WikiPageManager.php';

// Detect administration mode based on URL.
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    define('ADMIN_MODE', true);
} else {
    define('ADMIN_MODE', false);
}

/**
 * Ensures a directory exists. Creates it if necessary.
 *
 * @param string $path The directory path.
 * @param int $permissions Permissions (default 0755).
 * @return void
 */
function ensureDirectoryExists($path, $permissions = 0755) {
    if (!is_dir($path)) {
        mkdir($path, $permissions, true);
    }
}

/**
 * Returns the Router instance.
 *
 * @return Router
 */
function getRouter() {
    return ROUTER::getInstance();
}

/**
 * Builds an AdminResponse with a common structure.
 *
 * @param string $plugin Plugin name.
 * @param string $templateName Template identifier.
 * @param array $params Additional parameters to assign to the template.
 * @return AdminResponse
 */
function buildAdminResponse($plugin, $templateName, array $params = []) {
    $response = new AdminResponse();
    $tpl = $response->createPluginTemplate($plugin, $templateName);
    // Set common parameters
    $tpl->set('adminMenu', AdminWikiController::getAdminMenu());
    $tpl->set('router', getRouter());
    foreach ($params as $key => $value) {
        $tpl->set($key, $value);
    }
    $response->addTemplate($tpl);
    return $response;
}

/**
 * Wiki installation function.
 * Creates required directories and the default page if they do not exist.
 */
function wikiInstall() {
    $wikiDir = DATA_PLUGIN . 'wiki' . DS;
    if (!is_dir($wikiDir)) {
        if (!mkdir($wikiDir, 0755, true)) {
            die("Error creating Wiki directory.");
        }
    }
    $pagesDir = $wikiDir . 'pages' . DS;
    if (!is_dir($pagesDir)) {
        if (!mkdir($pagesDir, 0755, true)) {
            die("Error creating pages directory.");
        }
    }
    $configPath = PLUGINS . 'wiki' . DS . 'param' . DS . 'config.json';
    $defaultPageTitle = "Accueil Docs"; // default title
    if (file_exists($configPath)) {
        $config = json_decode(file_get_contents($configPath), true);
        if (isset($config['defaultPage'])) {
            $defaultPageTitle = $config['defaultPage'];
        }
    }
    $defaultFilename = 'default.json';
    $pageFile = $pagesDir . $defaultFilename;
    if (!file_exists($pageFile)) {
        $defaultData = [
            "filename"   => $defaultFilename,
            "title"      => $defaultPageTitle,
            "content"    => "Bienvenue sur votre Wiki. Modifiez ce contenu via l'administration.",
            "category"   => "",
            "parent"     => "",
            "draft"      => false,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "versions"   => []
        ];
        $written = file_put_contents($pageFile, json_encode($defaultData, JSON_PRETTY_PRINT));
        if ($written === false) {
            $error = error_get_last();
            die("Error writing file $pageFile: " . $error['message']);
        } else {
            echo "default.json created successfully.<br>";
        }
    } else {
        echo "default.json already exists.<br>";
    }
    echo "Wiki plugin installation completed successfully.";
}

/**
 * Hook executed before running the Wiki plugin.
 *
 * Ensures the storage directory exists.
 *
 * @return bool
 */
function wikiBeforeRunPlugin() {
    $configPath = PLUGINS . 'wiki' . DS . 'config.json';
    if (file_exists($configPath)) {
        $config = json_decode(file_get_contents($configPath), true);
    } else {
        $config = [
            "defaultPage" => "Accueil",
            "storageDir"  => "wiki",
            "versionLimit" => 2
        ];
    }
    $storageDir = DATA_PLUGIN . $config['storageDir'] . DS;
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }
    return true;
}
