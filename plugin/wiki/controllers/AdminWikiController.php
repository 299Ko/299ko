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
 * AdminWikiController.php
 *
 * This controller handles administration operations for Wiki pages,
 * including listing, editing, saving, and deleting pages.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class AdminWikiController extends AdminController {
    protected $pageManager;

    /**
     * Constructor.
     *
     * Initializes the Wiki page manager with the pages directory.
     */
    public function __construct() {
        $this->pageManager = new WikiPageManager(DATA_PLUGIN . 'wiki' . DS . 'pages' . DS);
    }

    /**
     * Display the list of Wiki pages in the admin panel.
     *
     * @return AdminResponse Returns the response with the pages list template.
     */
    public function index() {
        $pagesTree = $this->pageManager->getPagesTree();
        $flattened = $this->flattenPages($pagesTree, 0, null);
        $pagesMapping = [];
        foreach ($flattened as $p) {
            $pagesMapping[$p['filename']] = $p['title'];
        }
        $categories = WikiCategoryManager::getInstance()->getAllCategories();
        $catMapping = [];
        foreach ($categories as $cat) {
            $catMapping[$cat['id']] = $cat['name'];
        }
        $pagesTree = $this->addCategoryNameToPages($pagesTree, $catMapping, $pagesMapping);
        $response = buildAdminResponse('wiki', 'admin/wiki-list', [
            'pagesTree' => $pagesTree,
            'categoriesMapping' => $catMapping
        ]);
        $response->setTitle("List of Wiki Pages");
        return $response;
    }

    /**
     * Display the Wiki page edit form.
     *
     * @return AdminResponse Returns the response with the page edit template.
     */
    public function edit() {
        $pageId = $_GET['page'] ?? '';
        $pageData = $this->pageManager->getPage($pageId);
        if (empty($pageData)) {
            $pageData = [
                'filename'   => '',
                'title'      => '',
                'content'    => '',
                'category'   => '',
                'parent'     => '',
                'draft'      => false,
                'created_at' => '',
                'updated_at' => ''
            ];
        }
        $response = buildAdminResponse('wiki', 'admin/wiki-edit', [
            'page'         => $pageData,
            'categories'   => WikiCategoryManager::getInstance()->getAllCategories(),
            'parentPages'  => $this->flattenPages($this->pageManager->getPagesTree(), 0, (!empty($pageData['filename']) ? $pageData['filename'] : null)),
            'contentEditor'=> new Editor('content', $pageData['content'])
        ]);
        $response->setTitle($pageId ? "Edit Page" : "New Page");
        return $response;
    }

    /**
     * Save a Wiki page.
     */
    public function save() {
        $data = $_POST;
        $this->pageManager->savePage($data);
        header("Location: " . getRouter()->generate('admin-wiki'));
        exit;
    }

    /**
     * Delete a Wiki page.
     */
    public function delete() {
        $pageId = $_GET['page'] ?? '';
        if ($pageId) {
            $this->pageManager->deletePage($pageId);
        }
        header("Location: " . getRouter()->generate('admin-wiki'));
        exit;
    }

    /**
     * Recursively adds category names and parent titles to the pages.
     *
     * @param array $pagesTree
     * @param array $catMapping Mapping of category IDs to names.
     * @param array $pagesMapping Mapping of filenames to titles.
     * @return array
     */
    protected function addCategoryNameToPages(array $pagesTree, array $catMapping, array $pagesMapping) {
        foreach ($pagesTree as &$page) {
            $page['categoryName'] = (!empty($page['category']) && isset($catMapping[$page['category']])) ? $catMapping[$page['category']] : 'None';
            $page['parentTitle'] = (!empty($page['parent']) && isset($pagesMapping[$page['parent']])) ? $pagesMapping[$page['parent']] : '';
            if (!empty($page['children'])) {
                $page['children'] = $this->addCategoryNameToPages($page['children'], $catMapping, $pagesMapping);
            }
        }
        return $pagesTree;
    }

    /**
     * Flattens the pages tree into a simple list with indentation.
     *
     * @param array $pages
     * @param int $depth
     * @param string|null $exclude
     * @return array
     */
    protected function flattenPages(array $pages, $depth = 0, $exclude = null) {
        $result = [];
        foreach ($pages as $page) {
            if ($exclude !== null && $page['filename'] == $exclude) {
                continue;
            }
            $result[] = [
                'filename' => $page['filename'],
                'title'    => str_repeat('-- ', $depth) . $page['title']
            ];
            if (!empty($page['children'])) {
                $result = array_merge($result, $this->flattenPages($page['children'], $depth + 1, $exclude));
            }
        }
        return $result;
    }

    /**
     * Returns the admin menu HTML.
     *
     * @return string
     */
    public static function getAdminMenu() {
        $router = getRouter();
        $menu = [
            ['label' => 'Pages', 'url' => $router->generate('admin-wiki'), 'icon' => 'fa-solid fa-file-lines'],
            ['label' => 'Categories', 'url' => $router->generate('admin-wiki-categories'), 'icon' => 'fa-solid fa-tags'],
            ['label' => 'Settings', 'url' => $router->generate('admin-wiki-parameters'), 'icon' => 'fa-solid fa-sliders-h']
        ];
        $html = '<section>';
        foreach ($menu as $item) {
            $iconHtml = isset($item['icon']) && !empty($item['icon']) ? '<i title="' . $item['label'] . '" class="' . $item['icon'] . '"></i> ' : '';
            $html .= '<a href="' . $item['url'] . '" class="button">' . $iconHtml . $item['label'] . '</a>';
        }
        $html .= '</section>';
        return $html;
    }
}
