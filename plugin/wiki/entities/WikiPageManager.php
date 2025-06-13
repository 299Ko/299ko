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
 * WikiPageManager.php
 *
 * This class manages Wiki pages by loading, saving, deleting, searching,
 * and organizing pages into a hierarchical tree.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class WikiPageManager {
    protected $pagesDir;

    /**
     * Constructor.
     *
     * Initializes the pages directory and creates it if it does not exist.
     *
     * @param string $pagesDir The directory where Wiki pages are stored.
     */
    public function __construct($pagesDir) {
        $this->pagesDir = $pagesDir;
        ensureDirectoryExists($this->pagesDir, 0755);
    }

    /**
     * Retrieves all pages organized as a hierarchical tree.
     *
     * @return array The tree of pages.
     */
    public function getPagesTree() {
        $pages = $this->loadAllPages();
        return $this->buildTree($pages);
    }

    /**
     * Retrieves a single page by its filename.
     *
     * @param string $filename
     * @return array|null
     */
    public function getPage($filename) {
        if (empty($filename)) {
            return [];
        }
        $filePath = $this->pagesDir . $filename;
        if (!file_exists($filePath)) return null;
        $data = json_decode(file_get_contents($filePath), true);
        $data['filename'] = $filename;
        return $data;
    }

    /**
     * Saves a Wiki page. If the page content or title has changed,
     * the old version is stored in the version history.
     *
     * @param array $data The Wiki page data.
     */
    public function savePage($data) {
        $filename = (isset($data['filename']) && !empty($data['filename']))
            ? $data['filename']
            : uniqid() . '.json';
        $data['filename'] = $filename;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $filepath = $this->pagesDir . $filename;

        // Initialize versions array
        $versions = [];
        if (file_exists($filepath)) {
            $oldData = json_decode(file_get_contents($filepath), true);
            $data['created_at'] = $oldData['created_at'] ?? date('Y-m-d H:i:s');
            // If content or title changes, record old version
            if (
                ($oldData['content'] ?? '') !== ($data['content'] ?? '')
                || ($oldData['title'] ?? '') !== ($data['title'] ?? '')
            ) {
                $oldVersion = [
                    'title'      => $oldData['title'] ?? '',
                    'content'    => $oldData['content'] ?? '',
                    'category'   => $oldData['category'] ?? '',
                    'parent'     => $oldData['parent'] ?? '',
                    'draft'      => $oldData['draft'] ?? false,
                    'created_at' => $oldData['created_at'] ?? '',
                    'updated_at' => $oldData['updated_at'] ?? '',
                ];
                if (isset($oldData['versions']) && is_array($oldData['versions'])) {
                    $versions = $oldData['versions'];
                }
                array_unshift($versions, $oldVersion);
                $configPath = PLUGINS . 'wiki' . DS . 'param' . DS . 'config.json';
                $versionLimit = 2;
                if (file_exists($configPath)) {
                    $config = json_decode(file_get_contents($configPath), true);
                    if (isset($config['versionLimit'])) {
                        $versionLimit = (int)$config['versionLimit'];
                    }
                }
                if (count($versions) > $versionLimit) {
                    $versions = array_slice($versions, 0, $versionLimit);
                }
            } else {
                if (isset($oldData['versions']) && is_array($oldData['versions'])) {
                    $versions = $oldData['versions'];
                }
                $data['created_at'] = $oldData['created_at'] ?? date('Y-m-d H:i:s');
            }
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $data['versions'] = $versions;
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Deletes a Wiki page.
     *
     * @param string $filename
     */
    public function deletePage($filename) {
        $filePath = $this->pagesDir . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Searches pages by title or content matching a query.
     *
     * @param string $query
     * @return array Matching pages.
     */
    public function searchPages($query) {
        $all = $this->loadAllPages();
        $results = [];
        foreach ($all as $page) {
            if (stripos($page['title'], $query) !== false || stripos($page['content'], $query) !== false) {
                $results[] = $page;
            }
        }
        return $results;
    }

    /**
     * Retrieves pages by a specific category ID, organized as a tree.
     *
     * @param mixed $catId
     * @return array
     */
    public function getPagesByCategory($catId) {
        $all = $this->loadAllPages();
        $filtered = [];
        foreach ($all as $page) {
            if (isset($page['category']) && $page['category'] == $catId) {
                $filtered[] = $page;
            }
        }
        return $this->buildTree($filtered);
    }

    /**
     * Retrieves pages with no category assigned.
     *
     * @return array
     */
    public function getOrphanPages() {
        $all = $this->loadAllPages();
        $orphans = [];
        foreach ($all as $page) {
            if (empty($page['category'])) {
                $orphans[] = $page;
            }
        }
        return $orphans;
    }

    /**
     * Loads all Wiki pages from the pages directory.
     *
     * @return array All Wiki pages.
     */
    protected function loadAllPages() {
        $pages = [];
        $files = scandir($this->pagesDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') continue;
            $data = json_decode(file_get_contents($this->pagesDir . $file), true);
            if (!defined('ADMIN_MODE') || ADMIN_MODE === false) {
                if (!empty($data['draft']) && ($data['draft'] === true || $data['draft'] == '1')) {
                    continue;
                }
            }
            $data['filename'] = $file;
            $pages[] = $data;
        }
        return $pages;
    }

    /**
     * Builds a hierarchical tree from a flat array of pages.
     *
     * @param array $pages
     * @return array
     */
    protected function buildTree(array $pages) {
        $tree = [];
        $indexed = [];
        foreach ($pages as $page) {
            $page['children'] = [];
            $indexed[$page['filename']] = $page;
        }
        foreach ($indexed as $filename => $page) {
            if (!empty($page['parent']) && isset($indexed[$page['parent']])) {
                $indexed[$page['parent']]['children'][] = &$indexed[$filename];
            } else {
                $tree[] = &$indexed[$filename];
            }
        }
        return $tree;
    }
}
