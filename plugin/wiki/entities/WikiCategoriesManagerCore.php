<?php
/**
 * WikiCategoriesManagerCore.php
 *
 * This class extends the core CategoriesManager to provide Wiki-specific category management.
 * It handles category CRUD operations and URL generation for the admin interface.
 *
 * @package WikiPlugin
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

require_once COMMON . 'content' . DS . 'categories' . DS . 'CategoriesManager.php';
require_once __DIR__ . DS . 'WikiCategoryCore.php';

/**
 * Class WikiCategoriesManager
 *
 * Manages Wiki categories through the CMS category system.
 * Provides methods for category management and admin interface URLs.
 */
class WikiCategoriesManager extends CategoriesManager {
    /** @var string The plugin identifier */
    protected string $pluginId = 'wiki';
    
    /** @var string The name of the category type */
    protected string $name = 'categories';
    
    /** @var string The class name for category instances */
    protected string $className = 'WikiCategory';
    
    /** @var bool Whether categories can be nested */
    protected bool $nested = true;
    
    /** @var bool Whether multiple categories can be selected */
    protected bool $chooseMany = false;

    /**
     * Constructor
     *
     * Initializes the Wiki categories manager.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Gets the URL for adding a new category
     *
     * @return string The URL for the add category page.
     */
    public function getAddCategoryUrl(): string {
        return getRouter()->generate('admin-wiki-categories-add');
    }

    /**
     * Gets the URL for deleting a category
     *
     * @return string The URL for the delete category action.
     */
    public function getDeleteUrl(): string {
        return getRouter()->generate('admin-wiki-categories-delete');
    }

    /**
     * Gets the URL for the AJAX category list display
     *
     * @return string The URL for the AJAX category list endpoint.
     */
    public function getAjaxDisplayListUrl(): string {
        return getRouter()->generate('admin-wiki-categories-list');
    }
} 