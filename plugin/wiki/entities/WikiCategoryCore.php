<?php
/**
 * WikiCategoryCore.php
 *
 * This class extends the core Category class to provide Wiki-specific category functionality.
 * It defines the basic properties and behavior for Wiki categories in the CMS.
 *
 * @package WikiPlugin
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

require_once COMMON . 'content' . DS . 'categories' . DS . 'Category.php';

/**
 * Class WikiCategory
 *
 * Represents a Wiki category in the CMS category system.
 * Provides basic category functionality with Wiki-specific settings.
 */
class WikiCategory extends Category {
    /** @var string The plugin identifier */
    protected string $pluginId = 'wiki';
    
    /** @var string The name of the category type */
    protected string $name = 'categories';
    
    /** @var bool Whether categories can be nested */
    protected bool $nested = true;
    
    /** @var bool Whether multiple categories can be selected */
    protected bool $chooseMany = false;

    /**
     * Constructor
     *
     * Initializes a new Wiki category instance.
     *
     * @param mixed $id The category ID (-1 for new instances).
     */
    public function __construct($id = -1) {
        parent::__construct($id);
    }
}
