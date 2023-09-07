<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class BlogListController extends Controller {

    public function home($currentPage = 1)
    {
        $newsManager = new newsManager();
        // Mode d'affichage
        $mode = ($newsManager->count() > 0) ? 'list' : 'list_empty';

        // Contruction de la pagination
        $nbNews = count($newsManager->getItems());
        $newsByPage = $this->runPlugin->getConfigVal('itemsByPage');
        $nbPages = ceil($nbNews / $newsByPage);
        $start = ($currentPage - 1) * $newsByPage + 1;
        $end = $start + $newsByPage - 1;
        $pagination = array();
        for ($i = 0; $i != $nbPages; $i++) {
            if ($i != 0)
                $pagination[$i]['url'] = $this->router->generate('blog-page', ['page' => $i+1]);
            else
                $pagination[$i]['url'] = $this->runPlugin->getPublicUrl();
            $pagination[$i]['num'] = $i + 1;
        }
        // Récupération des news
        $news = array();
        $i = 1;
        foreach ($newsManager->getItems() as $k => $v)
            if (!$v->getDraft()) {
                $date = $v->getDate();
                if ($i >= $start && $i <= $end) {
                    $news[$k]['name'] = $v->getName();
                    $news[$k]['date'] = util::FormatDate($date, 'en', 'fr');
                    $news[$k]['id'] = $v->getId();
                    $news[$k]['content'] = $v->getContent();
                    $news[$k]['intro'] = $v->getIntro();
                    $news[$k]['url'] = $this->runPlugin->getPublicUrl() . util::strToUrl($v->getName()) . '-' . $v->getId() . '.html';
                    $news[$k]['img'] = $v->getImg();
                    $news[$k]['imgUrl'] = util::urlBuild(UPLOAD . 'galerie/' . $v->getImg());
                    $news[$k]['commentsOff'] = $v->getcommentsOff();
                }
                $i++;
            }
        // Traitements divers : métas, fil d'ariane...
        $this->runPlugin->setMainTitle($this->pluginsManager->getPlugin('blog')->getConfigVal('label'));
        $this->runPlugin->setTitleTag($this->pluginsManager->getPlugin('blog')->getConfigVal('label') . ' : page ' . $currentPage);
        if ($this->runPlugin->getIsDefaultPlugin() && $currentPage == 1) {
            $this->runPlugin->setTitleTag($this->pluginsManager->getPlugin('blog')->getConfigVal('label'));
            $this->runPlugin->setMetaDescriptionTag($this->core->getConfigVal('siteDescription'));
        }

        $response = new PublicResponse();
        $tpl = $response->createPluginTemplate('blog', 'list');

        $tpl->set('news', $news);
        $tpl->set('newsManager', $newsManager);
        $tpl->set('pagination', $pagination);
        $tpl->set('mode', $mode);
        $response->addTemplate($tpl);
        return $response;
    }

    public function page(int $page) {
        $page = $page > 1 ? $page : 1;
        return $this->home($page);
    }
}