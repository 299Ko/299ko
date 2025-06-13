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
 * WikiPageController.php
 *
 * This controller handles the public display of Wiki pages.
 * It displays either a single Wiki page or a list of Wiki pages,
 * depending on the provided parameters.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class WikiPageController {
    protected $pageManager;
    protected $categoryManager;

    /**
     * Constructor.
     *
     * Initializes the WikiPageManager and WikiCategoryManager.
     */
    public function __construct() {
        $this->pageManager = new WikiPageManager(DATA_PLUGIN . 'wiki' . DS . 'pages' . DS);
        $this->categoryManager = WikiCategoryManager::getInstance();
    }

    /**
     * Display a Wiki page or a list of Wiki pages.
     *
     * @return PublicResponse
     */
    public function view() {
        $router = getRouter();
        $pageId = $_GET['page'] ?? '';
        $catId  = $_GET['cat'] ?? '';
        $q      = isset($_GET['q']) ? trim($_GET['q']) : '';
        $baseUrl = rtrim($router->generate('wiki-view'), '/') . '/';
        $navMenu = $this->buildNavigationMenu();

        // If no parameters provided, load the default page.
        if (empty($pageId) && empty($catId) && empty($q)) {
            $defaultPage = $this->pageManager->getPage('default.json');
            if ($defaultPage) {
                $response = new PublicResponse();
                $tpl = $response->createPluginTemplate('wiki', 'public/wiki-view');
                $response->setTitle($defaultPage['title']);
                $tpl->set('pageTitle', $defaultPage['title']);
                $tpl->set('content', $defaultPage['content']);
                $tpl->set('menu', $navMenu);
                $tpl->set('router', $router);
                $tpl->set('baseUrl', $baseUrl);
                $response->addTemplate($tpl);
                return $response;
            }
        }

        if ($pageId) {
            $page = $this->pageManager->getPage($pageId);
            if (!$page) {
                die("Page not found.");
            }
            if (isset($page['draft']) && ($page['draft'] === true || $page['draft'] == '1')) {
                die("Unpublished page.");
            }
            $response = new PublicResponse();
            $tpl = $response->createPluginTemplate('wiki', 'public/wiki-view');
            $response->setTitle($page['title']);
            $tpl->set('pageTitle', $page['title']);
            $tpl->set('content', $page['content']);
            $tpl->set('menu', $navMenu);
            $tpl->set('router', $router);
            $tpl->set('baseUrl', $baseUrl);
            $response->addTemplate($tpl);
            return $response;
        } else {
            if ($catId) {
                $pages = $this->pageManager->getPagesByCategory($catId);
            } elseif ($q) {
                $pages = $this->pageManager->searchPages($q);
            } else {
                $pages = $this->pageManager->getPagesTree();
            }
            $response = new PublicResponse();
            $tpl = $response->createPluginTemplate('wiki', 'public/wiki-list');
            $response->setTitle("Wiki");
            $tpl->set('pages', $pages);
            $tpl->set('menu', $navMenu);
            $tpl->set('router', $router);
            $tpl->set('searchQuery', $q);
            $tpl->set('baseUrl', $baseUrl);
            $response->addTemplate($tpl);
            return $response;
        }
    }

    /**
     * Build the navigation menu based on categories and orphan pages.
     *
     * @return string Returns the HTML for the navigation menu.
     */
    protected function buildNavigationMenu() {
        $categoriesTree = $this->categoryManager->getCategoriesTree();
        foreach ($categoriesTree as &$cat) {
            $pages = $this->pageManager->getPagesByCategory($cat['id']);
            $cat['pages'] = $this->flattenPagesTree($pages);
        }
        unset($cat);
        $orphanPages = $this->pageManager->getOrphanPages();
        $router = getRouter();
        $baseUrl = rtrim($router->generate('wiki-view'), '/') . '/';
        $html = '<ul>';
        foreach ($categoriesTree as $cat) {
            $html .= $this->renderCategoryMenu($cat, $baseUrl);
        }
        if (!empty($orphanPages)) {
            $html .= '<li><strong>Pages</strong><ul>';
            foreach ($orphanPages as $page) {
                $html .= '<li><a href="' . $baseUrl . '?page=' . $page['filename'] . '">' . $page['title'] . '</a></li>';
            }
            $html .= '</ul></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Flattens a hierarchical pages tree into a simple array.
     *
     * @param array $pages The hierarchical array of pages.
     * @return array Returns a flat array of pages.
     */
    private function flattenPagesTree($pages) {
        $flat = [];
        foreach ($pages as $page) {
            $flat[] = $page;
            if (!empty($page['children'])) {
                $flat = array_merge($flat, $this->flattenPagesTree($page['children']));
            }
        }
        return $flat;
    }

    /**
     * Renders a category menu item, including its pages and subcategories.
     *
     * @param array $cat The category data.
     * @param string $baseUrl The base URL for links.
     * @return string Returns the HTML for the category menu item.
     */
    protected function renderCategoryMenu($cat, $baseUrl) {
        $html = '<li><a href="' . $baseUrl . '?cat=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
        if (!empty($cat['pages'])) {
            $html .= '<ul>';
            foreach ($cat['pages'] as $page) {
                $html .= '<li><a href="' . $baseUrl . '?page=' . $page['filename'] . '">' . htmlspecialchars($page['title']) . '</a></li>';
            }
            $html .= '</ul>';
        }
        if (!empty($cat['children'])) {
            $html .= '<ul>';
            foreach ($cat['children'] as $subcat) {
                $html .= $this->renderCategoryMenu($subcat, $baseUrl);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }
}
