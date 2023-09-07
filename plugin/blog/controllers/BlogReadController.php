<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('No direct script access allowed');

class BlogReadController extends Controller
{


    public function read($name, $id)
    {
        $antispam = ($this->pluginsManager->isActivePlugin('antispam')) ? new antispam() : false;
        $newsManager = new newsManager();

        $item = $newsManager->create($id);
        if (!$item) {
            $this->core->error404();
        }

        $newsManager->loadComments($item->getId());
        $this->addMetas($item);

        $antispamField = ($antispam) ? $antispam->show() : '';
        // Traitements divers : métas, fil d'ariane...
        $this->runPlugin->setMainTitle($item->getName());
        $this->runPlugin->setTitleTag($item->getName());

        $response = new PublicResponse();
        $tpl = $response->createPluginTemplate('blog', 'read');

        $tpl->set('antispam', $antispam);
        $tpl->set('antispamField', $antispamField);
        $tpl->set('item', $item);
        $tpl->set('newsManager', $newsManager);
        $tpl->set('commentSendUrl', $this->router->generate('blog-send'));

        $response->addTemplate($tpl);
        return $response;

    }

    protected function addMetas($item)
    {
        $this->core->addMeta('<meta property="og:url" content="' . util::getCurrentURL() . '" />');
        $this->core->addMeta('<meta property="twitter:url" content="' . util::getCurrentURL() . '" />');
        $this->core->addMeta('<meta property="og:type" content="article" />');
        $this->core->addMeta('<meta property="og:title" content="' . $item->getName() . '" />');
        $this->core->addMeta('<meta name="twitter:card" content="summary" />');
        $this->core->addMeta('<meta name="twitter:title" content="' . $item->getName() . '" />');
        $this->core->addMeta('<meta property="og:description" content="' . $item->getSEODesc() . '" />');
        $this->core->addMeta('<meta name="twitter:description" content="' . $item->getSEODesc() . '" />');

        if ($this->pluginsManager->isActivePlugin('galerie') && galerie::searchByfileName($item->getImg())) {
            $this->core->addMeta('<meta property="og:image" content="' . util::urlBuild(UPLOAD . 'galerie/' . $item->getImg()) . '" />');
            $this->core->addMeta('<meta name="twitter:image" content="' . util::urlBuild(UPLOAD . 'galerie/' . $item->getImg()) . '" />');
        }
    }

    public function send()
    {
        $antispam = ($this->pluginsManager->isActivePlugin('antispam')) ? new antispam() : false;
        $newsManager = new newsManager();
        // quelques contrôle et temps mort volontaire avant le send...
        sleep(2);
        if ($this->runPlugin->getConfigVal('comments') && $_POST['_author'] == '') {
            if (($antispam && $antispam->isValid()) || !$antispam) {
                $comments = $newsManager->loadComments($_POST['id']);
                $comment = new newsComment();
                $comment->setIdNews($_POST['id']);
                $comment->setAuthor($_POST['author']);
                $comment->setAuthorEmail($_POST['authorEmail']);
                $comment->setDate('');
                $comment->setContent($_POST['commentContent']);
                if ($newsManager->saveComment($comment)) {
                    header('location:' . $_POST['back'] . '#comment' . $comment->getId());
                    die();
                }
            } else {
                header('location:' . $_POST['back']);
                die();
            }
        }
    }

    public function rss() {
        $newsManager = new newsManager();
        echo $newsManager->rss();
        die();
    }
}