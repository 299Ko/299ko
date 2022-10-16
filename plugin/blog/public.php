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

if (isset($_GET['id']))
    $action = 'read';
elseif (isset($_GET['rss']))
    $action = 'rss';
elseif (isset($_GET['send']))
    $action = 'send';
else
    $action = '';
$antispam = ($pluginsManager->isActivePlugin('antispam')) ? new antispam() : false;
if (isset($_GET['idcat'])) {
    $catId = $_GET['idcat'];
    $categoriesManager = new CategoriesManager('blog');
    if ($categoriesManager->isCategorieExist($catId)) {
        $categorie = new Categorie('blog', $catId);
        $newsManager = new newsManager($categorie->items);
    } else {
        core::getInstance()->error404();
    }
} else {
    $newsManager = new newsManager();
}

switch ($action) {
    case '':
        // Mode d'affichage
        $mode = ($newsManager->count() > 0) ? 'list' : 'list_empty';
        // Détermination de la page courante
        if (!isset($_GET['page']))
            $currentPage = 1;
        else
            $currentPage = $_GET['page'];
        // Contruction de la pagination
        $nbNews = count($newsManager->getItems());
        $newsByPage = $runPlugin->getConfigVal('itemsByPage');
        $nbPages = ceil($nbNews / $newsByPage);
        $start = ($currentPage - 1) * $newsByPage + 1;
        $end = $start + $newsByPage - 1;
        $pagination = array();
        for ($i = 0; $i != $nbPages; $i++) {
            if (isset($_GET['idcat'])) {
                if ($i != 0)
                    $pagination[$i]['url'] = $runPlugin->getPublicUrl() . 'cat-' . $catId . '/page-' . ($i + 1) . '/' . util::strToUrl($categorie->label) . '.html';
                else
                    $pagination[$i]['url'] = $runPlugin->getPublicUrl() . 'cat-' . $catId . '/' . util::strToUrl($categorie->label) . '.html';
            } else {
                if ($i != 0)
                    $pagination[$i]['url'] = $runPlugin->getPublicUrl() . ($i + 1) . '/';
                else
                    $pagination[$i]['url'] = $runPlugin->getPublicUrl();
            }

            $pagination[$i]['num'] = $i + 1;
        }
        // Récupération des news
        $news = array();
        $i = 1;
        $categoriesManager = new CategoriesManager('blog');
        foreach ($newsManager->getItems() as $k => $v)
            if (!$v->getDraft()) {
                $date = $v->getDate();
                if ($i >= $start && $i <= $end) {
                    // Categories
                    $news[$k]['cats'] = [];
                    foreach ($categoriesManager->getCategories() as $cat) {
                        if (in_array($v->getId(), $cat->items)) {
                            $news[$k]['cats'][] = [
                                'label' => $cat->label,
                                'url' => blogCreateCategorieUrl($cat)
                            ];
                        }
                    }

                    $news[$k]['name'] = $v->getName();
                    $news[$k]['date'] = util::FormatDate($date, 'en', 'fr');
                    $news[$k]['id'] = $v->getId();
                    $news[$k]['content'] = $v->getContent();
                    $news[$k]['url'] = $runPlugin->getPublicUrl() . util::strToUrl($v->getName()) . '-' . $v->getId() . '.html';
                    $news[$k]['img'] = $v->getImg();
                    $news[$k]['commentsOff'] = $v->getcommentsOff();
                }
                $i++;
            }
        // Traitements divers : métas, fil d'ariane...
        $runPlugin->setMainTitle($pluginsManager->getPlugin('blog')->getConfigVal('label'));
        $runPlugin->setTitleTag($pluginsManager->getPlugin('blog')->getConfigVal('label') . ' : page ' . $currentPage);
        if (isset($categorie)) {
            $runPlugin->setTitleTag($pluginsManager->getPlugin('blog')->getConfigVal('label') . ' : ' . $categorie->label);
        }
        if ($runPlugin->getIsDefaultPlugin() && $currentPage == 1) {
            $runPlugin->setTitleTag($pluginsManager->getPlugin('blog')->getConfigVal('label'));
            $runPlugin->setMetaDescriptionTag($core->getConfigVal('siteDescription'));
        }
        break;
    case 'read':
        // Mode d'affichage
        $mode = 'read';
        // Récupération de la news
        $item = $newsManager->create($_GET['id']);
        if (!$item)
            $core->error404();
        $newsManager->loadComments($item->getId());
        $antispamField = ($antispam) ? $antispam->show() : '';
        // Traitements divers : métas, fil d'ariane...
        $runPlugin->setMainTitle($item->getName());
        $runPlugin->setTitleTag($item->getName());
        $categoriesManager = new CategoriesManager('blog');
        $cats = [];
        foreach ($categoriesManager->getCategories() as $cat) {
            if (in_array($item->getId(), $cat->items)) {
                $cats[] = [
                    'label' => $cat->label,
                    'url' => blogCreateCategorieUrl($cat)
                ];
            }
        }
        if ($pluginsManager->isActivePlugin('galerie') && galerie::searchByfileName($item->getImg())) {
            show::setFeaturedImage(util::urlBuild(UPLOAD . 'galerie/' . $item->getImg()));
        }
        break;
    case 'rss':
        echo $newsManager->rss();
        break;
    case 'send':
        // quelques contrôle et temps mort volontaire avant le send...
        sleep(2);
        if ($runPlugin->getConfigVal('comments') && $_POST['_author'] == '') {
            if (($antispam && $antispam->isValid()) || !$antispam) {
                $comments = $newsManager->loadComments($_POST['id']);
                $comment = new newsComment();
                $comment->setIdNews($_POST['id']);
                $comment->setAuthor($_POST['author']);
                $comment->setAuthorEmail($_POST['authorEmail']);
                $comment->setDate('');
                $comment->setContent($_POST['content']);
                if ($newsManager->saveComment($comment)) {
                    header('location:' . $_POST['back'] . '#comment' . $comment->getId());
                    die();
                }
            } else {
                header('location:' . $_POST['back']);
                die();
            }
        }
        break;
    default:
        $core->error404();
}
?>