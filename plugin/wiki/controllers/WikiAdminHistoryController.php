<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class WikiAdminHistoryController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function showHistory($id)
    {
        $pageId = (int) $id;
        $wikiPageManager = new WikiPageManager();
        $page = $wikiPageManager->create($pageId);
        
        if (!$page) {
            show::msg(lang::get('wiki-item-dont-exist'), 'error');
            $this->core->redirect($this->router->generate('admin-wiki-list'));
        }

        // Créer une nouvelle instance pour avoir l'historique à jour
        $historyManager = new WikiPageManager();
        $history = $historyManager->getPageHistory($pageId);

        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'admin-history');
        
        $tpl->set('page', $page);
        $tpl->set('history', $history);
        $tpl->set('wikiPageManager', $wikiPageManager);
        $tpl->set('token', $this->user->token);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function showVersion($id, $version)
    {
        $pageId = (int) $id;
        $version = (int) $version;
        
        $wikiPageManager = new WikiPageManager();
        $page = $wikiPageManager->getPageVersion($pageId, $version);
        
        if (!$page) {
            $this->logger->log("Version not found: PageID $pageId, Version $version", "ERROR");
            show::msg(lang::get('wiki-version-not-found'), 'error');
            $this->core->redirect($this->router->generate('admin-wiki-list'));
        }

        $currentPage = $wikiPageManager->create($pageId);
        
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'admin-version');
        
        $tpl->set('page', $page);
        $tpl->set('currentPage', $currentPage);
        $tpl->set('version', $version);
        $tpl->set('token', $this->user->token);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function restoreVersion()
    {
        // Lire les données JSON
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        $pageId = (int) ($data['id'] ?? 0);
        $version = (int) ($data['version'] ?? 0);
        
        $this->logger->log("Restore version request: PageID $pageId, Version $version", "INFO");
        
        $wikiPageManager = new WikiPageManager();
        $oldPage = $wikiPageManager->getPageVersion($pageId, $version);
        
        if (!$oldPage) {
            $this->logger->log("Old version not found: PageID $pageId, Version $version", "ERROR");
            show::msg(lang::get('wiki-version-not-found'), 'error');
            $this->core->redirect($this->router->generate('admin-wiki-list'));
        }

        $currentPage = $wikiPageManager->create($pageId);
        if (!$currentPage) {
            $this->logger->log("Current page not found: PageID $pageId", "ERROR");
            show::msg(lang::get('wiki-item-dont-exist'), 'error');
            $this->core->redirect($this->router->generate('admin-wiki-list'));
        }

        // Restaurer le contenu de l'ancienne version
        $currentPage->setContent($oldPage->getContent());
        $currentPage->setName($oldPage->getName());
        $currentPage->setIntro($oldPage->getIntro());
        $currentPage->setSEODesc($oldPage->getSEODesc());
        $currentPage->setModifiedBy($this->user->email);
        
        $changeDescription = lang::get('wiki-restore-version') . ' ' . $version;
        
        $result = $wikiPageManager->saveWikiPage($currentPage, $changeDescription);
        
        if ($result) {
            $this->logger->log("Version restored successfully: PageID $pageId, Version $version", "INFO");
            show::msg(lang::get('wiki-version-restored'), 'success');
        } else {
            $this->logger->log("Failed to restore version: PageID $pageId, Version $version", "ERROR");
            show::msg(lang::get('wiki-version-restore-error'), 'error');
        }
        
        $this->core->redirect($this->router->generate('admin-wiki-edit-page', ['id' => $pageId]));
    }

} 