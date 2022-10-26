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
$pagesManager = new PagesManager();
# Création, de la page
$id = (isset($_GET['id'])) ? $_GET['id'] : false;
if (!$id)
    $pageItem = new PageItem();
elseif ($pageItem = new PageItem($id)) {
    
} else
    $core->error404();
if ($pageItem->type === PageItem::PAGE)
    $core->error404();
$action = (isset($_POST['unlock'])) ? 'unlock' : '';
switch ($action) {
    case 'unlock':
        // quelques contrôle et temps mort volontaire avant le send...
        sleep(2);
        if ($_POST['_password'] == '' && $_SERVER['HTTP_REFERER'] == $runPlugin->getPublicUrl() . util::strToUrl($pageItem->name) . '-' . $pageItem->id . '.html')
            $page->unlock($pageItem, $_POST['password']);
        $redirect = $runPlugin->getPublicUrl() . util::strToUrl($pageItem->name) . '-' . $pageItem->id . '.html';
        header('location:' . $redirect);
        die();
        break;
    default:
        if ($pagesManager->isUnlocked($pageItem)) {
            # Gestion du titre
            if ($runPlugin->getConfigVal('hideTitles'))
                $runPlugin->setMainTitle('');
            else
                $runPlugin->setMainTitle(($pageItem->mainTitle != '') ? $pageItem->mainTitle : $pageItem->name);
            # Gestion des metas
            if ($pageItem->metaTitleTag)
                $runPlugin->setTitleTag($pageItem->metaTitleTag);
            else
                $runPlugin->setTitleTag($pageItem->name);
            if ($pageItem->metaDescriptionTag)
                $runPlugin->setMetaDescriptionTag($pageItem->metaDescriptionTag);
            // template
            $pageFile = ($pageItem->file) ? THEMES . $core->getConfigVal('theme') . '/' . $pageItem->file : false;
        } else {
            $runPlugin->setTitleTag('Accès restreint');
            $runPlugin->setMainTitle('Accès restreint');
        }
}
?>