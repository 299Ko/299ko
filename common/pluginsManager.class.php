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

class pluginsManager {

    private $plugins;
    private static $instance = null;

    ## Constructeur

    public function __construct() {
        $this->plugins = $this->listPlugins();
    }

    ## Retourne la liste des plugins

    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * Retourne un objet plugin
     * 
     * @param string Nom du plugin
     * @return \plugin
     */
    public function getPlugin($name) {
        foreach ($this->plugins as $plugin) {
            if ($plugin->getName() == $name)
                return $plugin;
        }
        return false;
    }

    ## Sauvegarde la configuration d'un objet plugin

    public function savePluginConfig(plugin $obj) {
        if ($obj->getIsValid() && $path = $obj->getDataPath()) {
            return util::writeJsonFile($path . 'config.json', $obj->getConfig());
        }
    }

    ## Installe un plugin ciblé

    public function installPlugin($name, $activate = false) {
        core::getInstance()->callHook('beforePluginInstall', $name);
        // Create data plugin folder
        @mkdir(DATA_PLUGIN . $name, 0755, true);
        @chmod(DATA_PLUGIN . $name, 0755);

        // Lecture du fichier config usine
        $config = util::readJsonFile(PLUGINS . $name . '/param/config.json');

        // Par défaut le plugin est inactif
        if ($activate)
            $config['activate'] = "1";
        else
            $config['activate'] = "0";
        // Création du fichier config
        @util::writeJsonFile(DATA_PLUGIN . $name . '/config.json', $config);
        @chmod(DATA_PLUGIN . $name . '/config.json', 0644);
        // Appel de la fonction d'installation du plugin
        if (function_exists($name . 'Install'))
            call_user_func($name . 'Install');

        core::getInstance()->callHook('afterPluginInstall', $name);
        // Check du fichier config
        if (!file_exists(DATA_PLUGIN . $name . '/config.json'))
            return false;
        return true;
    }

    /**
     * Retourne l'instance de l'objet pluginsManager
     * 
     * @return \self
     */
    public static function getInstance() {
        if (is_null(self::$instance))
            self::$instance = new pluginsManager();
        return self::$instance;
    }

    ## Retourne une valeur de configuration ciblée d'un plugin

    public static function getPluginConfVal($pluginName, $kConf) {
        $instance = self::getInstance();
        $plugin = $instance->getPlugin($pluginName);
        return $plugin->getConfigVal($kConf);
    }
    
    public static function getPluginInfoVal($pluginName, $kConf) {
        $instance = self::getInstance();
        $plugin = $instance->getPlugin($pluginName);
        return $plugin->getInfoVal($kConf);
    }

    ## Détermine si le plugin ciblé existe et s'il est actif

    public static function isActivePlugin($pluginName) {
        $instance = self::getInstance();
        $plugin = $instance->getPlugin($pluginName);
        if ($plugin && $plugin->isInstalled() && $plugin->getConfigval('activate'))
            return true;
        return false;
    }
    
    public static function isExistPlugin($pluginName) {
        $instance = self::getInstance();
        $plugin = $instance->getPlugin($pluginName);
        return ($plugin && $plugin->isInstalled());
    }

    ## Génère la liste des plugins

    private function listPlugins() {
        $data = array();
        $dataNotSorted = array();
        $items = util::scanDir(PLUGINS);
        foreach ($items['dir'] as $dir) {
            // Si le plugin est installé on récupère sa configuration
            if (file_exists(DATA_PLUGIN . $dir . '/config.json'))
                $dataNotSorted[$dir] = util::readJsonFile(DATA_PLUGIN . $dir . '/config.json', true);
            // Sinon on lui attribu une priorité faible
            else
                $dataNotSorted[$dir]['priority'] = '9';
        }
        // On tri les plugins par priorité
        $dataSorted = @util::sort2DimArray($dataNotSorted, 'priority', 'num');
        foreach ($dataSorted as $plugin => $config) {
            $data[] = $this->createPlugin($plugin);
        }
        return $data;
    }

    ## Créée un objet plugin

    private function createPlugin($name) {
        // Instance du core
        $core = core::getInstance();
        // Infos du plugin
        $infos = util::readJsonFile(PLUGINS . $name . '/param/infos.json');
        // Configuration du plugin
        $config = util::readJsonFile(DATA_PLUGIN . $name . '/config.json');
        // Hooks du plugin
        @include PLUGINS . $name . '/param/hooks.php';
        $hooks = util::readJsonFile(PLUGINS . $name . '/param/hooks.json');
        //
        // Config usine
        $initConfig = util::readJsonFile(PLUGINS . $name . '/param/config.json');
        // Derniers checks
        if (!is_array($config))
            $config = array();
        if (!is_array($hooks))
            $hooks = array();
        // Création de l'objet
        $plugin = new plugin($name, $config, $infos, $hooks, $initConfig);
        return $plugin;
    }

}
