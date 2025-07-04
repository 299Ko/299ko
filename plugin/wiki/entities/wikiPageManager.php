<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

if (!class_exists('WikiPageManager')) {
class WikiPageManager {

    private $items;
    private $history;

    private int $nbItemsToPublic;

    public function __construct() {
        $categoriesManager = new WikiCategoriesManager();
        $i = 0;
        $data = [];
        if (file_exists(ROOT . 'data/plugin/wiki/wiki.json')) {
            $temp = util::readJsonFile(ROOT . 'data/plugin/wiki/wiki.json');
            if (!is_array($temp)) {
                $temp = [];
            }
            $temp = util::sort2DimArray($temp, 'date', 'desc');
            foreach ($temp as $k => $v) {
                $categories = [];
                foreach ($categoriesManager->getCategories() as $cat) {
                    if (in_array($v['id'], $cat->items)) {
                        $categories['categories'][$cat->id] = [
                            'label' => $cat->label,
                            'url' => router::getInstance()->generate('wiki-category', ['name' => util::strToUrl($cat->label), 'id' => $cat->id]),
                            'id' => $cat->id
                        ];
                    }
                }
                $v = array_merge($v, $categories);
                $data[] = new WikiPage($v);
                if ($v['draft'] === "0") {
                    $i++;
                }
            }
        }
        $this->nbItemsToPublic = $i;
        $this->items = $data;
        $this->loadHistory();
    }
    
    /**
     * Retrieves all wiki pages.
     *
     * @return WikiPage[] An array of wiki page objects.
     */

    public function getItems() {
        return $this->items;
    }


    /**
     * Summary of create
     * @param mixed $id
     * @return \WikiPage | boolean
     */
    public function create($id) {
        foreach ($this->items as $obj) {
            if ($obj->getId() == $id)
                return $obj;
        }
        return false;
    }

    public function saveWikiPage($obj, $changeDescription = '') {
        $id = intval($obj->getId());
        $isNew = ($id < 1);
        
        if ($isNew) {
            $obj->setId($this->makeId());
            $obj->setVersion(1);
            $obj->setLastModified(date('Y-m-d H:i:s'));
            $this->items[] = $obj;
        } else {
            // Sauvegarder l'ancienne version dans l'historique
            $oldPage = $this->create($id);
            if ($oldPage) {
                $this->saveToHistory($oldPage, $changeDescription);
            }
            
            // Incrémenter la version
            $obj->incrementVersion();
            
            foreach ($this->items as $k => $v) {
                if ($id == $v->getId())
                    $this->items[$k] = $obj;
            }
        }
        
        // Enregistrer l'activité
        $this->recordActivity($obj, $isNew ? 'add' : 'edit');
        
        return $this->save();
    }

    /**
     * Delete a WikiPage from wiki & her comments
     * @param WikiPage $obj
     * @return bool WikiPage correctly deleted
     */
    public function delWikiPage(\WikiPage $obj):bool
    {
        $id = $obj->getId();
        foreach ($this->items as $k => $v) {
            if ($id == $v->getId())
                unset($this->items[$k]);
        }
        if ($this->save()) {
            // Enregistrer l'activité de suppression
            $this->recordActivity($obj, 'delete');
            return true;
        }
        return false;
    }

    public function count() {
        return count($this->items);
    }

    /**
     * Return number of wiki pages who can be displayed in public mode
     */
    public function getNbItemsToPublic() {
        return $this->nbItemsToPublic;
    }

    public function rss() {
        $core = core::getInstance();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0">';
        $xml .= '<channel>';
        $xml .= ' <title>' . $core->getConfigVal('siteName') . ' - ' . pluginsManager::getPluginConfVal('wiki', 'label') . '</title>';
        $xml .= ' <link>' . $core->getConfigVal('siteUrl') . '/</link>';
        $xml .= ' <description>' . $core->getConfigVal('siteDescription') . '</description>';
        $xml .= ' <language>' . $core->getConfigVal('siteLang') . '</language>';
        foreach ($this->getItems() as $k => $v)
            if (!$v->getDraft()) {
                $xml .= '<item>';
                $xml .= '<title><![CDATA[' . $v->getName() . ']]></title>';
                $xml .= '<link>' . $core->getConfigVal('siteUrl') . '/wiki/' . $v->getSlug() . '-' . $v->getId() . '.html</link>';
                $xml .= '<pubDate>' . (date("D, d M Y H:i:s O", strtotime($v->getDate()))) . '</pubDate>';
                $xml .= '<description><![CDATA[' . $v->getContent() . ']]></description>';
                $xml .= '</item>';
            }
        $xml .= '</channel>';
        $xml .= '</rss>';
        header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $xml;
        die();
    }

    private function makeId() {
        $ids = array(0);
        foreach ($this->items as $obj) {
            $ids[] = $obj->getId();
        }
        return max($ids) + 1;
    }

    private function save() {
        $data = array();
        foreach ($this->items as $k => $v) {
            $data[] = array(
                'id' => $v->getId(),
                'name' => $v->getName(),
                'content' => $v->getContent(),
                'intro' => $v->getIntro(),
                'seoDesc' => $v->getSEODesc(),
                'date' => $v->getDate(),
                'draft' => $v->getDraft(),
                'img' => $v->getImg(),

                'version' => $v->getVersion(),
                'lastModified' => $v->getLastModified(),
                'modifiedBy' => $v->getModifiedBy(),
                'slug' => $v->getSlug(),
                'categories' => $v->categories,
            );
        }
        if (util::writeJsonFile(ROOT . 'data/plugin/wiki/wiki.json', $data))
            return true;
        return false;
    }

    // History management
    private function loadHistory() {
        $this->history = [];
        if (file_exists(ROOT . 'data/plugin/wiki/history.json')) {
            $temp = util::readJsonFile(ROOT . 'data/plugin/wiki/history.json');
            if (is_array($temp)) {
                $nextId = 1;
                foreach ($temp as $v) {
                    // Corriger les IDs null existants
                    if (!isset($v['id']) || $v['id'] === null) {
                        $v['id'] = $nextId++;
                    } else {
                        $nextId = max($nextId, $v['id'] + 1);
                    }
                    $this->history[] = new WikiPageHistory($v);
                }
                // Sauvegarder les corrections si nécessaire
                if ($nextId > 1) {
                    $this->saveHistory();
                }
            }
        }
    }

    private function saveToHistory($page, $changeDescription = '') {
        // Générer un ID unique pour l'entrée d'historique
        $historyId = $this->makeHistoryId();
        
        $historyEntry = new WikiPageHistory([
            'id' => $historyId,
            'pageId' => $page->getId(),
            'version' => $page->getVersion(),
            'name' => $page->getName(),
            'content' => $page->getContent(),
            'intro' => $page->getIntro(),
            'seoDesc' => $page->getSEODesc(),
            'modifiedBy' => $page->getModifiedBy(),
            'modifiedAt' => $page->getLastModified(),
            'changeDescription' => $changeDescription
        ]);
        
        $this->history[] = $historyEntry;
        
        $this->saveHistory();
    }

    private function makeHistoryId() {
        $ids = array(0);
        foreach ($this->history as $entry) {
            $ids[] = $entry->getId();
        }
        return max($ids) + 1;
    }

    private function saveHistory() {
        $data = [];
        foreach ($this->history as $entry) {
            $data[] = [
                'id' => $entry->getId(),
                'pageId' => $entry->getPageId(),
                'version' => $entry->getVersion(),
                'name' => $entry->getName(),
                'content' => $entry->getContent(),
                'intro' => $entry->getIntro(),
                'seoDesc' => $entry->getSEODesc(),
                'modifiedBy' => $entry->getModifiedBy(),
                'modifiedAt' => $entry->getModifiedAt(),
                'changeDescription' => $entry->getChangeDescription()
            ];
        }
        
        return util::writeJsonFile(ROOT . 'data/plugin/wiki/history.json', $data);
    }

    public function getPageHistory($pageId) {
        $pageHistory = [];
        foreach ($this->history as $entry) {
            if ($entry->getPageId() == $pageId) {
                $pageHistory[] = $entry;
            }
        }
        // Trier par version décroissante
        usort($pageHistory, function($a, $b) {
            return $b->getVersion() - $a->getVersion();
        });
        return $pageHistory;
    }

    public function getPageVersion($pageId, $version) {
        foreach ($this->history as $entry) {
            if ($entry->getPageId() == $pageId && $entry->getVersion() == $version) {
                return new WikiPage([
                    'id' => $entry->getPageId(),
                    'name' => $entry->getName(),
                    'content' => html_entity_decode($entry->getContent(), ENT_QUOTES, 'UTF-8'),
                    'intro' => html_entity_decode($entry->getIntro(), ENT_QUOTES, 'UTF-8'),
                    'seoDesc' => $entry->getSEODesc(),
                    'date' => $entry->getModifiedAt(),
                    'draft' => '0',
                    'img' => '',

                    'version' => $entry->getVersion(),
                    'lastModified' => $entry->getModifiedAt(),
                    'modifiedBy' => $entry->getModifiedBy(),
                    'slug' => util::strToUrl($entry->getName())
                ]);
            }
        }
        return false;
    }



    /**
     * Enregistrer une activité dans le gestionnaire d'activités
     */
    private function recordActivity($page, $action) {
        if (class_exists('WikiActivityManager')) {
            $activityManager = new WikiActivityManager();
            
            // Trouver la catégorie principale de la page
            $categoryName = '';
            $categoriesManager = new WikiCategoriesManager();
            foreach ($categoriesManager->getCategories() as $cat) {
                if (in_array($page->getId(), $cat->items)) {
                    $categoryName = $cat->label;
                    break;
                }
            }
            
            $activityManager->addActivity($action, $page->getName(), $categoryName, $page->getId());
        }
    }
}
}