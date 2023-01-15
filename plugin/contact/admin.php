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
if (!defined('ROOT'))
    die();
$action = (isset($_GET['action'])) ? urldecode($_GET['action']) : '';
switch ($action) {
    case 'save':
        if ($administrator->isAuthorized()) {
            if (isset($_GET['fromparam'])) {
                $runPlugin->setConfigVal('label', $_POST['label']);
                $runPlugin->setConfigVal('copy', $_POST['copy']);
                $runPlugin->setConfigVal('acceptation', $_POST['acceptation']);
            } else {
                $runPlugin->setConfigVal('content1', core::executeHookFilter('beforeSaveEditor', $_POST['content1']));
                $runPlugin->setConfigVal('content2', core::executeHookFilter('beforeSaveEditor', $_POST['content2']));
            }
            if ($pluginsManager->savePluginConfig($runPlugin))
                show::msg("Les modifications ont été enregistrées", 'success');
            else
                show::msg("Une erreur est survenue", 'error');
            header('location:index.php?p=contact');
            die();
        }
        break;
    case 'emptymails':
        if ($administrator->isAuthorized()) {
            util::writeJsonFile(DATA_PLUGIN . 'contact/emails.json', []);
            show::msg("La base des emails a été vidée", 'info');
            header('location:index.php?p=contact');
            die();
        }
        break;
    default;
        $emails = implode("\n", util::readJsonFile(DATA_PLUGIN . 'contact/emails.json'));
        break;
}