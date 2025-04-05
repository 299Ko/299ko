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
defined('ROOT') OR exit('Access denied!');

## Fonction d'installation

function antispamInstall() {
    
}

## Hooks
## Code relatif au plugin

class antispam {

    protected $captcha;

    public function __construct() {
        $pluginManager = pluginsManager::getInstance();
        $typeCaptcha = $pluginManager->getPluginConfVal('antispam', 'type');
        if ($typeCaptcha === 'useText') {
            // UseText lib
            require_once PLUGINS . 'antispam' . DS . 'lib' . DS . 'AntispamAbstractCaptcha.php';
            require_once PLUGINS . 'antispam' . DS . 'lib' . DS . 'AntispamTextCaptcha.php';
            $this->captcha = new AntispamTextCaptcha();
        } elseif ($typeCaptcha === 'useRecaptcha') {
            // ReCaptcha lib
            require_once PLUGINS . 'antispam' . DS . 'lib' . DS . 'AntispamReCaptcha.php';
            $public = $pluginManager->getPluginConfVal('antispam', 'recaptchaPublicKey');
            $secret = $pluginManager->getPluginConfVal('antispam', 'recaptchaSecretKey');
            $this->captcha = new AntispamReCaptcha($public, $secret);
        } elseif ($typeCaptcha === 'useIcon') {
            require_once PLUGINS . 'antispam' . DS . 'lib' . DS . 'AntispamAbstractCaptcha.php';
            require_once PLUGINS . 'antispam' . DS . 'lib' . DS . 'AntispamIconCaptcha.php';
            $this->captcha = new AntispamIconCaptcha();
        }
    }

    public function show() {
        return $this->captcha->getText();
    }

    public function isValid() {
        return $this->captcha->isValid();
    }

}

