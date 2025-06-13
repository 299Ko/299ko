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
 * WikiCategoryManager.php
 *
 * This class adapts the Wiki category management to use the native CMS
 * via the WikiCategoryCMS class.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class WikiCategoryManager {
    protected static $instance;

    /**
     * Private constructor to enforce singleton pattern.
     */
    private function __construct() {}

    /**
     * Returns the singleton instance.
     *
     * @return WikiCategoryManager
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new WikiCategoryManager();
        }
        return self::$instance;
    }

    /**
     * Retrieves all categories from the JSON file.
     *
     * @return array List of categories.
     */
    public function getAllCategories() {
        $dummy = new WikiCategoryCMS();
        $file = DATA_PLUGIN . $dummy->getPluginId() . '/categories-' . $dummy->getName() . '.json';
        $data = util::readJsonFile($file);
        if (!is_array($data)) {
            $data = [];
        }
        $categories = [];
        foreach ($data as $id => $catData) {
            $categories[] = [
                'id'     => $id,
                'name'   => $catData['label'] ?? '',
                'parent' => $catData['parentId'] ?? ''
            ];
        }
        return $categories;
    }

    /**
     * Builds the hierarchical tree of categories.
     *
     * @return array
     */
    public function getCategoriesTree() {
        $all = $this->getAllCategories();
        $tree = [];
        $indexed = [];
        foreach ($all as $cat) {
            $cat['children'] = [];
            $indexed[$cat['id']] = $cat;
        }
        foreach ($indexed as $id => &$cat) {
            if (!empty($cat['parent']) && isset($indexed[$cat['parent']])) {
                $indexed[$cat['parent']]['children'][] = &$cat;
            } else {
                $tree[] = &$cat;
            }
        }
        return $tree;
    }

    /**
     * Retrieve a single category by its ID.
     *
     * @param mixed $id
     * @return array|null
     */
    public function getCategory($id) {
        $all = $this->getAllCategories();
        foreach ($all as $cat) {
            if ($cat['id'] == $id) {
                return $cat;
            }
        }
        return null;
    }

    /**
     * Saves a category to the JSON file.
     *
     * @param array $data Category data.
     */
    public function saveCategory($data) {
        $dummy = new WikiCategoryCMS();
        $file = DATA_PLUGIN . $dummy->getPluginId() . '/categories-' . $dummy->getName() . '.json';
        $categories = util::readJsonFile($file);
        if (!is_array($categories)) {
            $categories = [];
        }
        $id = !empty($data['id']) ? $data['id'] : uniqid();
        $categories[$id] = [
            'label'      => $data['name'],
            'parentId'   => $data['parent'] ?? '',
            'items'      => $categories[$id]['items'] ?? [],
            'childrenId' => $categories[$id]['childrenId'] ?? [],
            'pluginArgs' => $categories[$id]['pluginArgs'] ?? []
        ];
        file_put_contents($file, json_encode($categories, JSON_PRETTY_PRINT));
    }

    /**
     * Deletes a category from the JSON file.
     *
     * @param mixed $id
     */
    public function deleteCategory($id) {
        $dummy = new WikiCategoryCMS();
        $file = DATA_PLUGIN . $dummy->getPluginId() . '/categories-' . $dummy->getName() . '.json';
        $categories = util::readJsonFile($file);
        if (!is_array($categories)) {
            $categories = [];
        }
        if (isset($categories[$id])) {
            unset($categories[$id]);
            file_put_contents($file, json_encode($categories, JSON_PRETTY_PRINT));
        }
    }
}
