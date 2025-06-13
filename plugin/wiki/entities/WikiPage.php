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
 * WikiPage.php
 *
 * Represents a Wiki page with properties such as filename, title,
 * content, category, parent, draft status, timestamps, and version history.
 *
 * Assumes that the central plugin initialization (wiki.php) is already loaded.
 *
 * @package WikiPlugin
 */

class WikiPage {
    public $filename;
    public $title;
    public $content;
    public $category;
    public $parent;
    public $draft;
    public $updated_at;
    public $created_at;
    public $versions;

    /**
     * Constructor.
     *
     * Initializes the Wiki page properties from the given data array.
     *
     * @param array $data
     */
    public function __construct($data) {
        $this->filename   = $data['filename'] ?? '';
        $this->title      = $data['title'] ?? '';
        $this->content    = $data['content'] ?? '';
        $this->category   = $data['category'] ?? '';
        $this->parent     = $data['parent'] ?? '';
        $this->draft      = $data['draft'] ?? false;
        $this->updated_at = $data['updated_at'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->versions   = $data['versions'] ?? [];
    }

    /**
     * Converts the Wiki page object into an associative array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'filename'   => $this->filename,
            'title'      => $this->title,
            'content'    => $this->content,
            'category'   => $this->category,
            'parent'     => $this->parent,
            'draft'      => $this->draft,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'versions'   => $this->versions
        ];
    }
}
