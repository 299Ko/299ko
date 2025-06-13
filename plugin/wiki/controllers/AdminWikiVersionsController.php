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
 * AdminWikiVersionsController.php
 *
 * This controller manages the version history for Wiki pages.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class AdminWikiVersionsController extends AdminController {
    protected $pageManager;

    /**
     * Constructor.
     *
     * Initializes the WikiPageManager with the pages directory.
     */
    public function __construct() {
        $this->pageManager = new WikiPageManager(DATA_PLUGIN . 'wiki' . DS . 'pages' . DS);
    }

    /**
     * Display the version history for a specific Wiki page.
     *
     * @return AdminResponse
     */
    public function index() {
        $pageId = $_GET['page'] ?? '';
        if (empty($pageId)) {
            header("Location: " . getRouter()->generate('admin-wiki'));
            exit;
        }
        $page = $this->pageManager->getPage($pageId);
        if (!$page) {
            die("Page not found");
        }
        $versions = $page['versions'] ?? [];
        if (is_array($versions)) {
            $i = 0;
            foreach ($versions as $key => $version) {
                $versions[$key]['versionIndex'] = $i;
                $i++;
            }
        }
        $response = buildAdminResponse('wiki', 'admin/wiki-versions', [
            'page' => $page,
            'versions' => $versions
        ]);
        $response->setTitle("Version History");
        return $response;
    }

    /**
     * Display details of a specific version of a Wiki page.
     *
     * @return AdminResponse
     */
    public function viewVersion() {
        $pageId = $_GET['page'] ?? '';
        if (empty($pageId) || !isset($_GET['version'])) {
            header("Location: " . getRouter()->generate('admin-wiki'));
            exit;
        }
        $versionIndex = $_GET['version'];
        $page = $this->pageManager->getPage($pageId);
        if (!$page) {
            die("Page not found");
        }
        $versions = $page['versions'] ?? [];
        if (!isset($versions[$versionIndex])) {
            die("Version not found");
        }
        $version = $versions[$versionIndex];
        $response = buildAdminResponse('wiki', 'admin/wiki-versions-view', [
            'page' => $page,
            'version' => $version,
            'versionIndex' => $versionIndex
        ]);
        $response->setTitle("Version Details");
        return $response;
    }

    /**
     * Restore a selected version of a Wiki page.
     */
    public function restore() {
        $pageId = $_GET['page'] ?? '';
        $versionIndex = $_GET['version'] ?? '';
        if (empty($pageId) || $versionIndex === '') {
            header("Location: " . getRouter()->generate('admin-wiki'));
            exit;
        }
        $page = $this->pageManager->getPage($pageId);
        if (!$page) {
            die("Page not found");
        }
        $versions = $page['versions'] ?? [];
        if (!isset($versions[$versionIndex])) {
            die("Version not found");
        }
        $restored = $versions[$versionIndex];
        $page['title']    = $restored['title'];
        $page['content']  = $restored['content'];
        $page['category'] = $restored['category'];
        $page['parent']   = $restored['parent'];
        $page['draft']    = $restored['draft'];
        $this->pageManager->savePage($page);
        header("Location: " . getRouter()->generate('admin-wiki-edit') . "?page=" . $pageId);
        exit;
    }
}
