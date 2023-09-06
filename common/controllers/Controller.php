<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class Controller {
    
    /**
     * Core instance
     * @var \core
     */
    protected core $core;
    
    /**
     * Router instance
     * @var \router
     */
    protected router $router;

    /**
     * pluginsManager instance
     * @var pluginsManager
     */
    protected pluginsManager $pluginsManager;
    
    /**
     * Current plugin instance
     * @var plugin
     */
    protected plugin $runPlugin;
    
    public function __construct() {
        $this->core = core::getInstance();
        $this->router = router::getInstance();
        $this->pluginsManager = pluginsManager::getInstance();
        $this->runPlugin = $this->pluginsManager->getPlugin($this->core->getPluginToCall());
    }
}