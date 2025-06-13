<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 *
 * Wiki Plugin for 299Ko CMS
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 *
 * AdminWikiCategoriesController.php
 *
 * This controller manages the administration actions for Wiki categories.
 * Responsibilities include listing, editing, saving, and deleting categories.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class AdminWikiCategoriesController extends AdminController {
    protected $categoryManager;

    /**
     * Constructor.
     *
     * Initializes the category manager using its singleton instance.
     */
    public function __construct() {
        $this->categoryManager = WikiCategoryManager::getInstance();
    }

    /**
     * Display the categories tree in the admin panel.
     *
     * @return AdminResponse Returns the admin response with the categories template.
     */
    public function index() {
        $categoriesTree = $this->categoryManager->getCategoriesTree();
        $response = buildAdminResponse('wiki', 'admin/wiki-categories', [
            'categoriesTree' => $categoriesTree
        ]);
        return $response;
    }

    /**
     * Display the category edit form.
     *
     * @return AdminResponse Returns the admin response with the category edit template.
     */
    public function edit() {
        $catId = $_GET['id'] ?? '';
        $category = $catId ? $this->categoryManager->getCategory($catId) : null;
        $response = buildAdminResponse('wiki', 'admin/wiki-categories-edit', [
            'category'      => $category,
            'allCategories' => $this->categoryManager->getAllCategories()
        ]);
        $response->setTitle($catId ? "Edit Category" : "New Category");
        return $response;
    }

    /**
     * Save category data.
     */
    public function save() {
        $data = $_POST;
        $this->categoryManager->saveCategory($data);
        header("Location: " . getRouter()->generate('admin-wiki-categories'));
        exit;
    }

    /**
     * Delete a category.
     */
    public function delete() {
        $catId = $_GET['id'] ?? '';
        if ($catId) {
            $this->categoryManager->deleteCategory($catId);
        }
        header("Location: " . getRouter()->generate('admin-wiki-categories'));
        exit;
    }
}
