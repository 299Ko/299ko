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
$action = (isset($_GET['action'])) ? $_GET['action'] : '';
switch ($action) {
    case 'save':
        if ($administrator->isAuthorized()) {
            $pos = $_POST['position'];

            $runPlugin->setConfigVal('position', $pos);
            $runPlugin->setConfigVal('trackingId', trim($_POST['trackingId']));
            $runPlugin->setConfigVal('wt', trim($_POST['wt']));

            // Save Social adress
            $vars = seoGetSocialVars();
            foreach ($vars as $k => $v) {
                $runPlugin->setConfigVal($v, trim($_POST[$v]));
            }

            if ($pluginsManager->savePluginConfig($runPlugin)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=seo');
            die();
        }
        break;
    default:
}
