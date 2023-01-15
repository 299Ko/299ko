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
$galerie = new galerie();

switch ($action) {
    case 'saveconf':
        if ($administrator->isAuthorized()) {
            $runPlugin->setConfigVal('label', trim($_POST['label']));
            $runPlugin->setConfigVal('order', trim($_POST['order']));
            $runPlugin->setConfigVal('introduction', trim($_POST['introduction']));
            $runPlugin->setConfigVal('showTitles', trim($_POST['showTitles']));
            $runPlugin->setConfigVal('size', trim($_POST['size']));
            if ($pluginsManager->savePluginConfig($runPlugin)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=galerie');
            die();
        }
        break;
    case 'save':
        if ($administrator->isAuthorized()) {
            $item = ($_REQUEST['id']) ? $galerie->createItem($_REQUEST['id']) : new galerieItem();
            $item->setCategory($_REQUEST['category']);
            $item->setTitle($_REQUEST['title']);
            $item->setContent(core::executeHookFilter('beforeSaveEditor', $_REQUEST['content']));
            $item->setDate($_REQUEST['date']);
            $item->setHidden((isset($_POST['hidden'])) ? 1 : 0);
            if ($galerie->saveItem($item)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=galerie');
            die();
        }
        break;
    case 'del':
        if ($administrator->isAuthorized()) {
            $item = $galerie->createItem($_REQUEST['id']);
            if ($galerie->delItem($item)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=galerie');
            die();
        }
        break;
    case 'edit':
        $mode = 'edit';
        $item = (isset($_REQUEST['id'])) ? $galerie->createItem($_GET['id']) : new galerieItem();
        if ($item->getDate() == '')
            $item->setDate('');
        break;
    default:
        $mode = 'list';
}