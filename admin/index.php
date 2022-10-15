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
define('ROOT', '../');
include_once(ROOT . 'common/common.php');
include_once(COMMON . 'administrator.class.php');
$administrator = new administrator($core->getConfigVal('adminEmail'), $core->getConfigVal('adminPwd'));
if ($administrator->isAuthorized() && $core->detectAdminMode() == 'login') {
    // quelques contrôle et temps mort volontaire avant le login...
    sleep(2);
    if ($_POST['_email'] == '') {
        // authentification
        if ($administrator->login($_POST['adminEmail'], $_POST['adminPwd'])) {
            show::msg("Vous êtes maintenant loggués en tant qu'administrateur", 'success');
            header('location:index.php');
            die();
        } else {
            show::msg("Mot de passe incorrect", 'error');
            include_once('login.php');
        }
    }
} elseif ($administrator->isAuthorized() && $core->detectAdminMode() == 'logout') {
    $administrator->logout();
    header('location:index.php');
    die();
} elseif ($administrator->isAuthorized() && $core->detectAdminMode() == 'lostpwd') {
    $step = (isset($_GET['step']) ? $_GET['step'] : 'form');
    if ($step == 'send' && $administrator->isAuthorized() && $administrator->getEmail() == $_POST['adminEmail']) {
        // quelques contrôle et temps mort volontaire avant le login...
        sleep(2);
        $administrator->makePwd();
    } elseif ($step == 'confirm' && $administrator->isAuthorized()) {
        // quelques contrôle et temps mort volontaire avant le login...
        sleep(2);
        $config = $core->getConfig();
        $config['adminPwd'] = $administrator->encrypt($administrator->getNewPwd());
        $core->saveConfig($config);
    }
    include_once('lostpwd.php');
}
if (!$administrator->isLogged() && $core->detectAdminMode() != 'lostpwd')
    include_once('login.php');
elseif ($core->detectAdminMode() == 'plugin') {
    core::executeHookAction('adminBeforeRunPlugin', $runPlugin->getName());
    if ($runPlugin->getAdminTemplate()) {
        if (util::getFileExtension($runPlugin->getAdminTemplate()) === 'tpl') {
            $layout = new Template(ADMIN_PATH . 'layout.tpl');
            $tpl = new Template($runPlugin->getAdminTemplate());
            include($runPlugin->getAdminFile());
            $layout->set('CONTENT', $tpl->output());
            echo $layout->output();
        } else {
            include($runPlugin->getAdminFile());
            include($runPlugin->getAdminTemplate());
        }
    } else {
        include($runPlugin->getAdminFile());
    }
}