<?php
/**
 * WikiMenu.php
 *
 * This class handles the construction and rendering of the Wiki navigation menu.
 * It manages the hierarchical display of categories and pages, handles active states,
 * and provides customization options for the menu appearance.
 *
 * @package WikiPlugin
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

/**
 * Class WikiMenu
 *
 * Manages the Wiki navigation menu structure and rendering.
 * Provides methods for building the menu hierarchy, handling active states,
 * and customizing the menu appearance through options.
 */
class WikiMenu {
    private $pageManager;
    private $categoryManager;
    private $currentPage;
    private $currentCategory;
    private $options;
    private $template;

    /**
     * Default options for the menu
     */
    private $defaultOptions = [
        'showIcons' => true,
        'expandActive' => true,
        'sortBy' => 'title', // title, date, custom
        'sortOrder' => 'asc',
        'maxDepth' => 3,
        'showOrphanPages' => true,
        'orphanPagesTitle' => 'Pages',
        'activeClass' => 'active',
        'categoryIcon' => 'fa-folder',
        'pageIcon' => 'fa-file-alt',
        'orphanIcon' => 'fa-file'
    ];

    /**
     * Constructor
     *
     * @param WikiPageManager $pageManager The page manager instance.
     * @param WikiCategoryManager $categoryManager The category manager instance.
     * @param array $options Custom options to override defaults.
     */
    public function __construct(WikiPageManager $pageManager, WikiCategoryManager $categoryManager, array $options = []) {
        $this->pageManager = $pageManager;
        $this->categoryManager = $categoryManager;
        $this->options = array_merge($this->defaultOptions, $options);
        $this->currentPage = $_GET['page'] ?? '';
        $this->currentCategory = $_GET['cat'] ?? '';
        $this->template = new Template(PLUGINS . 'wiki' . DS . 'template' . DS . 'public' . DS . 'wiki-menu.tpl');
    }

    /**
     * Builds the complete menu structure
     *
     * @return string The rendered menu HTML.
     */
    public function build(): string {
        $categoriesTree = $this->categoryManager->getCategoriesTree();
        $menuData = $this->prepareMenuData();
        
        // Debug data
        error_log('Menu Data: ' . print_r($menuData, true));
        
        return $this->renderMenu($menuData);
    }

    /**
     * Prepares the menu data structure
     *
     * @return array The prepared menu data including categories and orphan pages.
     */
    private function prepareMenuData() {
        $categoriesTree = $this->categoryManager->getCategoriesTree();
        $menuData = [
            'categories' => [],
            'orphanPages' => []
        ];

        // Debug log
        error_log('Categories tree: ' . json_encode($categoriesTree));

        // Process main categories
        foreach ($categoriesTree as $category) {
            $preparedCategory = $this->prepareCategoryData($category);
            $menuData['categories'][] = $preparedCategory;
        }

        // Get all pages
        $allPages = $this->pageManager->loadAllPages();
        
        // Identify orphan pages (not assigned to any category)
        foreach ($allPages as $page) {
            if (!$this->isPageInCategories($page['filename'], $categoriesTree)) {
                $menuData['orphanPages'][] = [
                    'id' => $page['filename'],
                    'title' => $page['title'],
                    'url' => $this->buildPageUrl($page)
                ];
            }
        }

        // Debug log
        error_log('Menu Data: ' . print_r($menuData, true));

        return $menuData;
    }

    /**
     * Prepares data for a single category
     *
     * @param array $category The category data to prepare.
     * @return array The prepared category data.
     */
    private function prepareCategoryData($category) {
        error_log('Preparing category data for: ' . json_encode($category));
        
        $preparedData = [
            'id' => $category['id'],
            'name' => $category['name'],
            'isActive' => false,
            'pages' => [],
            'children' => []
        ];

        // Add category pages
        if (!empty($category['items'])) {
            $addedPages = []; // To avoid duplicates
            foreach ($category['items'] as $item) {
                if (in_array($item, $addedPages)) {
                    continue; // Skip if page already added
                }
                $page = $this->pageManager->getPage($item);
                if ($page) {
                    $preparedData['pages'][] = [
                        'id' => $page['filename'],
                        'title' => $page['title'],
                        'url' => $this->buildPageUrl($page)
                    ];
                    $addedPages[] = $item;
                }
            }
        }

        // Add subcategories
        if (!empty($category['childrenId'])) {
            foreach ($category['childrenId'] as $childId) {
                $childCategory = $this->categoryManager->getCategory($childId);
                if ($childCategory) {
                    $preparedData['children'][] = $this->prepareCategoryData($childCategory);
                }
            }
        }

        error_log('Prepared category data: ' . json_encode($preparedData));
        return $preparedData;
    }

    /**
     * Prepares data for a list of pages
     *
     * @param array $pages The pages to prepare.
     * @return array The prepared pages data.
     */
    private function preparePagesData(array $pages): array {
        $preparedPages = [];
        foreach ($pages as $page) {
            if (is_array($page) && isset($page['filename']) && isset($page['title'])) {
                $preparedPages[] = [
                    'id' => $page['filename'],
                    'title' => $page['title'],
                    'isActive' => $this->currentPage === $page['filename'],
                    'icon' => $this->options['pageIcon']
                ];
            }
        }

        // Sort pages
        if ($this->options['sortBy'] === 'title') {
            usort($preparedPages, function($a, $b) {
                return strcasecmp($a['title'], $b['title']);
            });
        }

        if ($this->options['sortOrder'] === 'desc') {
            $preparedPages = array_reverse($preparedPages);
        }

        return $preparedPages;
    }

    /**
     * Renders the menu as HTML
     *
     * @param array $menuData The menu data to render.
     * @return string The rendered menu HTML.
     */
    private function renderMenu(array $menuData): string {
        // Debug simplified data
        $debugData = [
            'categories' => array_map(function($cat) {
                return [
                    'id' => $cat['id'],
                    'name' => $cat['name'],
                    'has_pages' => !empty($cat['pages']),
                    'has_children' => !empty($cat['children'])
                ];
            }, $menuData['categories'])
        ];
        error_log('Menu data (simplified): ' . json_encode($debugData, JSON_PRETTY_PRINT));
        
        $this->template->set('menuData', $menuData);
        $this->template->set('options', $this->options);
        $this->template->set('currentPage', $this->currentPage);
        $this->template->set('currentCategory', $this->currentCategory);
        $this->template->set('baseUrl', rtrim(getRouter()->generate('wiki-view'), '/') . '/');
        $this->template->set('searchQuery', $_GET['q'] ?? '');
        
        $output = $this->template->output();
        return $output;
    }

    /**
     * Sets a single menu option
     *
     * @param string $key The option key.
     * @param mixed $value The option value.
     * @return void
     */
    public function setOption(string $key, $value): void {
        $this->options[$key] = $value;
    }

    /**
     * Sets multiple menu options
     *
     * @param array $options The options to set.
     * @return void
     */
    public function setOptions(array $options): void {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Gets a menu option value
     *
     * @param string $key The option key.
     * @return mixed The option value or null if not set.
     */
    public function getOption(string $key) {
        return $this->options[$key] ?? null;
    }

    /**
     * Checks if a category is currently active
     *
     * @param mixed $categoryId The category ID to check.
     * @return bool True if the category is active.
     */
    private function isCategoryActive($categoryId) {
        return $this->currentCategory === $categoryId;
    }

    /**
     * Checks if a page is currently active
     *
     * @param mixed $pageId The page ID to check.
     * @return bool True if the page is active.
     */
    private function isPageActive($pageId) {
        return $this->currentPage === $pageId;
    }

    /**
     * Builds the URL for a page
     *
     * @param array $page The page data.
     * @return string The page URL.
     */
    private function buildPageUrl($page) {
        return '/wiki/?page=' . urlencode($page['filename']);
    }

    /**
     * Checks if a page belongs to any category
     *
     * @param string $filename The page filename.
     * @param array $categories The categories to check.
     * @return bool True if the page belongs to any category.
     */
    private function isPageInCategories($filename, $categories) {
        foreach ($categories as $category) {
            if (!empty($category['items']) && in_array($filename, $category['items'])) {
                return true;
            }
            if (!empty($category['childrenId'])) {
                foreach ($category['childrenId'] as $childId) {
                    $childCategory = $this->categoryManager->getCategory($childId);
                    if ($childCategory && $this->isPageInCategories($filename, [$childCategory])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}