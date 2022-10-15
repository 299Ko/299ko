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
$newsManager = new newsManager();

switch ($action) {
    case 'saveconf':
        if ($administrator->isAuthorized()) {
            $runPlugin->setConfigVal('label', trim($_REQUEST['label']));
            $runPlugin->setConfigVal('itemsByPage', trim(intval($_REQUEST['itemsByPage'])));
            $runPlugin->setConfigVal('hideContent', (isset($_POST['hideContent']) ? 1 : 0));
            $runPlugin->setConfigVal('comments', (isset($_POST['comments']) ? 1 : 0));
            $runPlugin->setConfigVal('displayCategories', (isset($_POST['comments']) ? 1 : 0));
            $pluginsManager->savePluginConfig($runPlugin);
            header('location:index.php?p=blog');
            die();
        }
        break;
    case 'save':
        if ($administrator->isAuthorized()) {
            $imgId = (isset($_POST['delImg'])) ? '' : $_REQUEST['imgId'];
            if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
                if ($pluginsManager->isActivePlugin('galerie')) {
                    $galerie = new galerie();
                    $img = new galerieItem(array('category' => ''));
                    $img->setTitle($_POST['name'] . ' (image à la une)');
                    $img->setContent('');
                    $img->setDate(date('Y-m-d H:i:s'));
                    $img->setHidden(1);
                    $galerie->saveItem($img);
                    $imgId = $galerie->getLastId() . '.' . util::getFileExtension($_FILES['file']['name']);
                }
            }
            $news = ($_REQUEST['id']) ? $newsManager->create($_REQUEST['id']) : new news();
            $news->setName($_REQUEST['name']);
            $news->setContent($core->callHook('beforeSaveEditor', $_REQUEST['content']));
            $newsUrl = util::urlBuild($runPlugin->getName() . '/'. $news->getName() . '-' . $news->getId() . '.html');
            core::executeHookAction('adminOnSaveItem', [$runPlugin->getName(), $news->getId(), $newsUrl]);
            $news->setDraft((isset($_POST['draft']) ? 1 : 0));
            if (!isset($_REQUEST['date']) || $_REQUEST['date'] == "")
                $news->setDate($news->getDate());
            else
                $news->setDate($_REQUEST['date']);
            $news->setImg($imgId);
            $news->setCommentsOff((isset($_POST['commentsOff']) ? 1 : 0));
            if ($newsManager->saveNews($news)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=blog');
            die();
        }
        break;
    case 'edit':
        $mode = 'edit';
        $news = (isset($_REQUEST['id'])) ? $newsManager->create($_GET['id']) : new news();
        $showDate = (isset($_REQUEST['id'])) ? true : false;
        break;
    case 'del':
        if ($administrator->isAuthorized()) {
            $news = $newsManager->create($_REQUEST['id']);
            if ($newsManager->delNews($news)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=blog');
            die();
        }
        break;
    case 'listcomments':
        $mode = 'listcomments';
        $newsManager->loadComments($_GET['id']);
        break;
    case 'delcomment':
        if ($administrator->isAuthorized()) {
            $newsManager->loadComments($_GET['id']);
            $comment = $newsManager->createComment($_GET['idcomment']);
            if ($newsManager->delComment($comment)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=blog&action=listcomments&id=' . $_GET['id']);
            die();
        }
        break;
    case 'updatecomment':
        if ($administrator->isAuthorized()) {
            $newsManager->loadComments($_GET['id']);
            $comment = $newsManager->createComment($_GET['idcomment']);
            $newsManager->delComment($comment);
            $comment->setContent($_POST['content' . $_GET['idcomment']]);
            if ($newsManager->saveComment($comment)) {
                show::msg("Les modifications ont été enregistrées", 'success');
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=blog&action=listcomments&id=' . $_GET['id']);
            die();
        }
        break;
    default:
        $mode = 'list';
}