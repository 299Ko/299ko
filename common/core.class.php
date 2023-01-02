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
defined('ROOT') OR exit('No direct script access allowed');

class core {

    private static $instance = null;
    private $config;
    private $themes;
    private $pluginToCall;
    private $js;
    private $css;

    /**
     * @var array Filters Hook
     */
    protected static $hooksFilters = [];

    /**
     * @var array Action Hook
     */
    protected static $hooksAction = [];

    /**
     * Core Constructor (singleton)
     * Dont use this in your plugin, use core::getInstance()
     */
    public function __construct() {
        // Timezone
        date_default_timezone_set(date_default_timezone_get());
        // Construction du tableau de configuration
        // Exemple : array('siteName' => 'val', 'siteUrl' => 'val2')
        $this->config = util::readJsonFile(DATA . 'config.json', true);
        // Réglage de l'error reporting suivant le paramètre debug
        if ($this->config && $this->config['debug']) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        } else
            error_reporting(E_ERROR | E_PARSE);
        // Liste des thèmes
        $temp = util::scanDir(THEMES);
        foreach ($temp['dir'] as $k => $v) {
            $this->themes[$v] = util::readJsonFile(THEMES . $v . '/infos.json', true);
        }
        // On détermine le plugin que l'on doit executer suivant le mode (public ou admin)
        if (isset($_GET['p'])) {
            define('ISHOMEPAGE', false);
            $this->pluginToCall = $_GET['p'];
        } else {
            define('ISHOMEPAGE', true);
            if (ROOT == './') {
                $this->pluginToCall = $this->getConfigVal('defaultPlugin');
            } else {
                $this->pluginToCall = $this->getConfigVal('defaultAdminPlugin');
            }
        }
        $this->css[] = FONTICON;
        $this->css[] = FANCYCSS;
        $this->js[] = FANCYJS;
    }

    /**
     * Return Core Instance
     * @return \self
     */
    public static function getInstance() {
        if (is_null(self::$instance))
            self::$instance = new core();
        return self::$instance;
    }

    /**
     * Return installed themes
     * @return array Themes
     */
    public function getThemes() {
        return $this->themes;
    }

    /**
     * Return Core Config
     * @return array Config
     */
    public function getconfig() {
        return $this->config;
    }

    /**
     * Return a Core Config Value
     * @param string Config Key
     * @return mixed Config Value
     */
    public function getConfigVal($k) {
        if (isset($this->config[$k]))
            return $this->config[$k];
        else
            return false;
    }

    /**
     * Return a Theme Info Value
     * @param string Info Key
     * @return mixed Theme Info Value
     */
    public function getThemeInfo($k) {
        return $this->themes[$this->getConfigVal('theme')][$k] ?? false;
    }

    /**
     * Return the current called Plugin
     * @return string Plugin Name
     */
    public function getPluginToCall() {
        return $this->pluginToCall;
    }

    /**
     * Return the JS Core array
     * @return array JS files
     */
    public function getJs() {
        return $this->js;
    }

    /**
     * Return the CSS Core array
     * @return array CSS files
     */
    public function getCss() {
        return $this->css;
    }

    /**
     * Return if 299 is installed or not
     * @return boolean
     */
    public function isInstalled() {
        if (!file_exists(DATA . 'config.json'))
            return false;
        else
            return true;
    }

    /**
     * Generate and return Site URL
     * @return string Site URL
     */
    public function makeSiteUrl() {
        $siteUrl = str_replace(array('install.php', '/admin/index.php'), array('', ''), $_SERVER['SCRIPT_NAME']);
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            $isSecure = true;
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
            $isSecure = true;
        $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
        $siteUrl = $REQUEST_PROTOCOL . '://' . $_SERVER['HTTP_HOST'] . $siteUrl;
        $pos = mb_strlen($siteUrl) - 1;
        if ($siteUrl[$pos] == '/')
            $siteUrl = substr($siteUrl, 0, -1);
        return $siteUrl;
    }

    /**
     * Add a Hook
     * 
     * @deprecated since version 2.0
     * @see Action or Filter Hooks
     * @todo Change all Hooks to new Hooks (hooks.php with action and filters)
     * @param string name
     * @param string Callback to Call
     */
    public function addHook($name, $function) {
        $this->hooks[$name][] = $function;
    }

    /**
     * Add an Action Hook
     * 
     * @param string Hook Name
     * @param string Callback
     */
    public static function registerHookAction(string $name, $callback) {
        self::$hooksAction[$name][] = $callback;
    }

    /**
     * Add a Filter Hook
     * 
     * @param string Hook Name
     * @param string Callback
     */
    public static function registerHookFilter(string $name, $callback) {
        self::$hooksFilters[$name][] = $callback;
    }

    /**
     * Run a Hook Action. You can pass mixed content to $params
     * Hook Action never return content
     * 
     * @param string Hook name
     * @param mixed  Params to pass to the callback
     */
    public static function executeHookAction(string $name, $params = null) {
        if (!isset(self::$hooksAction[$name]) || !is_array(self::$hooksAction[$name])) {
            return 0;
        }
        foreach (self::$hooksAction[$name] as $callback) {
            if (is_array($callback)) {
                if (is_array($params)) {
                    call_user_func_array([$callback[0], $callback[1]], $params);
                } else {
                    call_user_func([$callback[0], $callback[1]], $params);
                }
            } else {
                if (is_array($params)) {
                    call_user_func_array($callback, $params);
                } else {
                    call_user_func($callback, $params);
                }
            }
        }
        return;
    }

    /**
     * Run a Filter Hook.
     * Content will be returned ever and ever by the hooks
     * Content and Params are available in the callback in an array
     * 
     * @param string Hook name to run
     * @param mixed  Content to modify
     * @param mixed  Params to pass to the callback
     * @return mixed Filtered Content by Hooks
     */
    public static function executeHookFilter(string $name, $content, $params = null) {
        if (!isset(self::$hooksFilters[$name]) || !is_array(self::$hooksFilters[$name])) {
            return $content;
        }
        foreach (self::$hooksFilters[$name] as $callback) {
            if (is_array($callback)) {
                $content = call_user_func_array([$callback[0], $callback[1]], [$content, $params]);
            } else {
                $content = call_user_func_array($callback, [$content, $params]);
            }
        }
        return $content;
    }

    /**
     * Permet d'appeler un hook
     * Si un paramètre est fourni, celui-ci sera passé de fonction en fonction Hook de filtre).
     * Sinon, la valeur de retour sera concaténé à chaque fonction (Hook d'action).
     * 
     * @deprecated since version 2.0
     * @see Action or Filter Hooks
     * @param   string  Nom du hook
     * @param   mixed   Paramètres
     * @return  mixed
     */
    public function callHook($name, $params = null) {
        if ($params === null) {
            // Action
            $return = '';
            if (isset($this->hooks[$name])) {
                foreach ($this->hooks[$name] as $function) {
                    $return .= call_user_func($function);
                }
            }
            return $return;
        }
        // Filter
        if (isset($this->hooks[$name])) {
            foreach ($this->hooks[$name] as $function) {
                $params = call_user_func($function, $params);
            }
        }
        return $params;
    }

    ## Detecte le mode de l'administration

    public function detectAdminMode() {
        if (isset($_GET['action']) && $_GET['action'] == 'login')
            return 'login';
        elseif (isset($_GET['action']) && $_GET['action'] == 'logout')
            return 'logout';
        elseif (isset($_GET['action']) && $_GET['action'] == 'lostpwd')
            return 'lostpwd';
        elseif (!isset($_GET['p']))
            return 'plugin';
        elseif (isset($_GET['p']))
            return 'plugin';
    }

    /**
     * Call a 404 error
     * 
     * @global Plugin $runPlugin
     * @param string 404 Error Main Title
     */
    public function error404($mainTitle = '404') {
        $core = $this;
        global $runPlugin;
        if ($runPlugin)
            $runPlugin->setMainTitle('Error 404 :(');
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        include_once(THEMES . $this->getConfigVal('theme') . '/header.php');
        include_once(THEMES . $this->getConfigVal('theme') . '/404.php');
        include_once(THEMES . $this->getConfigVal('theme') . '/footer.php');
        die();
    }

    /**
     * Save a config get by $core->getConfig()
     * 
     * @param array Core Config
     * @param array Optional Config array
     * @return boolean Saved or not
     */
    public function saveConfig($val, array $append = []) {
        $config = util::readJsonFile(DATA . 'config.json', true);
        $config = array_merge($config, $append);
        foreach ($config as $k => $v)
            if (isset($val[$k])) {
                $config[$k] = $val[$k];
            }
        if (util::writeJsonFile(DATA . 'config.json', $config)) {
            $this->config = util::readJsonFile(DATA . 'config.json', true);
            return true;
        } else
            return false;
    }

    /**
     * 299Ko Installation
     * @return boolean 299Ko is correctly installed
     */
    public function install() {
        $install = true;
        @chmod(ROOT . '.htaccess', 0604);
        if (!file_exists(ROOT . '.htaccess')) {
            $rewriteBase = str_replace(array('index.php', 'install.php', 'admin/'), '', $_SERVER['PHP_SELF']);
            $temp = "Options -Indexes\nOptions +FollowSymlinks\nRewriteEngine On\nRewriteBase " . $rewriteBase . "\nRewriteRule ^admin/$  admin/ [L]\nRewriteRule ^([a-z-0-9_]+)/$  index.php?p=$1 [L]\nErrorDocument 404 /index.php?p=404";
            if (!@file_put_contents(ROOT . '.htaccess', $temp, 0604))
                $install = false;
        }
        if (!is_dir(DATA) && (!@mkdir(DATA) || !@chmod(DATA, 0755)))
            $install = false;
        if ($install) {
            if (!file_exists(DATA . '.htaccess')) {
                if (!@file_put_contents(DATA . '.htaccess', "deny from all", 0604))
                    $install = false;
            }
            if (!is_dir(DATA_PLUGIN) && (!@mkdir(DATA_PLUGIN) || !@chmod(DATA_PLUGIN, 0755)))
                $install = false;
            if (!is_dir(UPLOAD) && (!@mkdir(UPLOAD) || !@chmod(UPLOAD, 0755)))
                $install = false;
            if (!file_exists(UPLOAD . '.htaccess')) {
                if (!@file_put_contents(UPLOAD . '.htaccess', "allow from all", 0604))
                    $install = false;
            }
            if (!file_exists(__FILE__) || !@chmod(__FILE__, 0644))
                $install = false;
            $key = uniqid(true);
            if (!file_exists(DATA . 'key.php') && !@file_put_contents(DATA
                            . 'key.php', "<?php\ndefined('ROOT') OR exit"
                            . "('No direct script access allowed');"
                            . "\ndefine('KEY', '$key'); ?>", 0604))
                $install = false;
        }
        return $install;
    }

    /**
     * Return the htaccess content
     * @return string htaccess content
     */
    public function getHtaccess() {
        return @file_get_contents(ROOT . '.htaccess');
    }

    /**
     * Save content to htaccess
     * @param string htaccess Content
     */
    public function saveHtaccess($content) {
        $content = str_replace("&amp;", "&", $content);
        @file_put_contents(ROOT . '.htaccess', $content);
    }

}
