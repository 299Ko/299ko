<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class WikiAdminConfigController extends AdminController {

    public function saveConfig() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        
        // Configuration générale
        $this->runPlugin->setConfigVal('pluginName', trim($this->request->post('pluginName')));
        $this->runPlugin->setConfigVal('label', trim($this->request->post('label')));
        $this->runPlugin->setConfigVal('homeText', $this->core->callHook('beforeSaveEditor', htmlspecialchars($this->request->post('homeText'))));
        $this->runPlugin->setConfigVal('showLastActivity', (isset($_POST['showLastActivity']) ? 1 : 0));
        
        // Affichage
        $this->runPlugin->setConfigVal('displayTOC', filter_input(INPUT_POST, 'displayTOC', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $this->runPlugin->setConfigVal('hideContent', (isset($_POST['hideContent']) ? 1 : 0));
        
        // Fonctionnalités
        $this->runPlugin->setConfigVal('enableVersioning', (isset($_POST['enableVersioning']) ? 1 : 0));
        $this->runPlugin->setConfigVal('enableInternalLinks', (isset($_POST['enableInternalLinks']) ? 1 : 0));
        
        if ($this->pluginsManager->savePluginConfig($this->runPlugin)) {
            show::msg(lang::get('core-changes-saved'), 'success');
        } else {
            show::msg(lang::get('core-changes-not-saved'), 'error');
        }
        
        $this->core->redirect($this->router->generate('admin-wiki-list'));
    }
}