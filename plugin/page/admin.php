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

$mode = '';
$action = (isset($_GET['action'])) ? urldecode($_GET['action']) : '';
$error = false;
//$page = new page();
$pageManager = new PagesManager();
$categoriesManager = new CategoriesManager('page');

switch ($action) {
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
            if ($_POST['id'] != '')
                $pageItem = new PageItem($_POST['id']);
            else
                $pageItem = new pageItem();
            $pageItem->name = $_POST['name'];
            $pageItem->position = $_POST['position'];
            $pageItem->homepage = $_POST['isHomepage'] ?? false;
            $pageItem->content = isset($_POST['content']) ? $core->callHook('beforeSaveEditor', $_POST['content']) : '';
            $pageItem->file = $_POST['file'] ?? '';
            $pageItem->isHidden = $_POST['isHidden'] ?? false;
            $pageItem->mainTitle = $_POST['mainTitle'] ?? '';
            $pageItem->metaDescriptionTag = $_POST['metaDescriptionTag'] ?? '';
            $pageItem->metaTitleTag = $_POST['metaTitleTag'] ?? '';
            $pageItem->target = $_POST['target'] ?? '';
            $pageItem->targetAttr = $_POST['targetAttr'] ?? '';
            $pageItem->noIndex = $_POST['noIndex'] ?? false;
            $pageItem->parent = $_POST['parent'] ?? '';
            $pageItem->cssClass = $_POST['cssClass'];
            $pageItem->img = $imgId;
            if (isset($_POST['_password']) && $_POST['_password'] != '')
                $pageItem->password = $_POST['_password'];
            if (isset($_POST['resetPassword']))
                $pageItem->password = '';
            if ($pageManager->savePageItem($pageItem)) {
                show::msg("Les modifications ont été enregistrées", 'success');
                core::executeHookAction('adminOnSaveItem', [$runPlugin->getName(), $pageItem->id, $pageItem->getUrl()]);
            } else {
                show::msg("Une erreur est survenue", 'error');
            }
            header('location:index.php?p=page');
            die();
        }
        break;
    case 'edit':
        if (isset($_GET['id']))
            $pageItem = new PageItem($_GET['id']);
        else
            $pageItem = new PageItem();
        $isLink = (isset($_GET['link']) || $pageItem->type === PageItem::URL) ? true : false;
        $mode = 'edit';
        break;
    case 'del':
        if ($administrator->isAuthorized()) {
            $pageItem = $page->create($_GET['id']);
            if ($page->del($pageItem))
                show::msg("Les modifications ont été enregistrées", 'success');
            else
                show::msg("Une erreur est survenue", 'error');
            header('location:index.php?p=page');
            die();
        }
        break;
    case 'up':
        if ($administrator->isAuthorized()) {
            if (strpos($_GET['id'], 'cat-') === false) {
                // Item
                $pageItem = new PageItem($_GET['id']);
                $newPos = $pageItem->position - 1.5;
                $pageItem->position = $newPos;
                $pageManager->savePageItem($pageItem);
                
            } else {
                $categorie = $categoriesManager->getCategorie(str_replace('cat-', '', $_GET['id']));
                $categorie->pluginArgs['position'] = $categorie->pluginArgs['position'] - 1.5;
                $categoriesManager->saveCategorie($categorie);
            }
            header('location:index.php?p=page');
            die();
        }
        break;
    case 'down':
        if ($administrator->isAuthorized()) {
            if (strpos($_GET['id'], 'cat-') === false) {
                // Item
                $pageItem = new PageItem($_GET['id']);
                $newPos = $pageItem->position + 1.5;
                $pageItem->position = $newPos;
                $pageManager->savePageItem($pageItem);
                
            } else {
                $categorie = $categoriesManager->getCategorie(str_replace('cat-', '', $_GET['id']));
                $categorie->pluginArgs['position'] = $categorie->pluginArgs['position'] + 1.5;
                $categoriesManager->saveCategorie($categorie);
            }
            header('location:index.php?p=page');
            die();
        }
        break;
    case 'maintenance':
        $id = explode(',', $_GET['id']);
        foreach ($id as $k => $v)
            if ($v != '') {
                $pageItem = $page->create($v);
                $page->del($pageItem);
            }
        header('location:index.php?p=page');
        die();
        break;
    default:
        // Recherche des pages perdues
//        $parents = array();
//        $lost = '';
//        foreach ($page->getItems() as $k => $v)
//            if ($v->getParent() == 0) {
//                $parents[] = $v->getId();
//            }
//        foreach ($page->getItems() as $k => $v)
//            if ($v->getParent() > 0) {
//                if (!in_array($v->getParent(), $parents))
//                    $lost .= $v->getId() . ',';
//            }
//        // Suite...
//        if (!$page->createHomepage() && $pluginsManager->getPlugin('page')->getIsDefaultPlugin())
//            show::msg("Aucune page d'accueil n'a été définie", 'warning');
        $mode = 'list';
        $categoriesManager = new CategoriesManager('page');
}
?>