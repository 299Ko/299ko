<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class WikiAdminCategoriesController extends AdminController {

    public WikiCategoriesManager $categoriesManager;

    public WikiPageManager $wikiPageManager;

    public function __construct() {
        parent::__construct();
        $this->categoriesManager = new WikiCategoriesManager();
        $this->wikiPageManager = new WikiPageManager();
    }

    public function addCategory() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        
        $label = filter_var($this->request->post('label'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $parentId = filter_var($this->request->post('parentId'), FILTER_SANITIZE_NUMBER_INT);
        $parentId = ($parentId === false || $parentId === '') ? 0 : (int)$parentId;
        
        if (empty($label)) {
            show::msg(lang::get('core-required-fields'), 'error');
            $this->core->redirect($this->router->generate('admin-wiki-list'));
            return;
        }
        
        $this->categoriesManager->createCategory($label, $parentId);
        // Synchroniser automatiquement avec les pages
        $this->categoriesManager->syncWithWikiPages();
        
        show::msg(lang::get('core-item-added'), 'success');
        $this->core->redirect($this->router->generate('admin-wiki-list'));
    }

    public function deleteCategory() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        
        $id = (int) $this->request->post('id') ?? 0;
        
        if (!$this->categoriesManager->isCategoryExist($id)) {
            show::msg(lang::get('core-item-not-found'), 'error');
            $this->core->redirect($this->router->generate('admin-wiki-list'));
            return;
        }
        
            if ($this->categoriesManager->deleteCategory($id)) {
            // Synchroniser automatiquement avec les pages
            $this->categoriesManager->syncWithWikiPages();
            show::msg(lang::get('core-item-deleted'), 'success');
            } else {
            show::msg(lang::get('core-item-not-deleted'), 'error');
        }
        
        $this->core->redirect($this->router->generate('admin-wiki-list'));
    }

    public function editCategory() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        
        $id = (int) $this->request->post('id') ?? 0;
        
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'admin-edit-category');
        
        if (!$this->categoriesManager->isCategoryExist($id)) {
            $this->core->error404();
        }
        
        $category = $this->categoriesManager->getCategory($id);

        // Forcer le chargement des catégories imbriquées
        $this->categoriesManager->getNestedCategories();
        
        error_log("WikiAdminCategoriesController::editCategory - Nombre de catégories: " . count($this->categoriesManager->getCategories()));
        error_log("WikiAdminCategoriesController::editCategory - Catégories imbriquées: " . print_r($this->categoriesManager->getNestedCategories(), true));

        $tpl->set('categoriesManager', $this->categoriesManager);
        $tpl->set('category', $category);
        $tpl->set('token', $this->user->attributes['token']);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function saveCategory() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        
        $id = (int) $this->request->post('id') ?? 0;
        $label = filter_var($this->request->post('label'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $parentId = filter_var($this->request->post('parentId'), FILTER_SANITIZE_NUMBER_INT);
        $parentId = ($parentId === false || $parentId === '') ? 0 : (int)$parentId;
        
        if (!$this->categoriesManager->isCategoryExist($id)) {
            $this->core->error404();
        }
        if ($parentId !== 0 && !$this->categoriesManager->isCategoryExist($parentId)) {
            $this->core->error404();
        }
        
        $category = $this->categoriesManager->getCategory($id);
        $category->parentId = $parentId;
        $category->label = $label;
        $this->categoriesManager->saveCategory($category);
        // Recharger les catégories pour mettre à jour les données
        $this->categoriesManager = new WikiCategoriesManager();
        
        show::msg(lang::get('core-changes-saved'), 'success');
        $this->core->redirect($this->router->generate('admin-wiki-list'));
    }




}