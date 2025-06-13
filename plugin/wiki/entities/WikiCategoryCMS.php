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
 * WikiCategoryCMS.php
 *
 * This class extends the core Category class to add plugin-specific properties
 * for managing Wiki categories.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class WikiCategoryCMS extends Category {
    /**
     * Constructor for WikiCategoryCMS.
     *
     * Sets the plugin-specific properties and calls the parent constructor.
     *
     * @param int $id Category ID (-1 for new instances).
     */
    public function __construct(int $id = -1) {
        $this->pluginId = 'wiki';
        $this->name = 'categories';
        $this->nested = true;
        $this->chooseMany = false;
        parent::__construct($id);
    }

    /**
     * Get the plugin ID.
     *
     * @return string
     */
    public function getPluginId(): string {
        return $this->pluginId;
    }

    /**
     * Get the category name.
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
