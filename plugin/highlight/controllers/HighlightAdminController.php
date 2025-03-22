<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('No direct script access allowed');

class HighlightAdminController extends AdminController
{
    public function home() {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('highlight', 'config');
        $response->addTemplate($tpl);
        return $response;
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        $this->runPlugin->setConfigVal('theme', trim($_POST['theme']));
        $this->pluginsManager->savePluginConfig($this->runPlugin);

        show::msg(Lang::get('core-changes-saved'), 'success');
        $this->core->redirect($this->router->generate('highlight-admin'));
    }
}