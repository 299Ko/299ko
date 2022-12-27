<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

require_once PLUGINS . 'page/lib/PagesManager.php';
require_once PLUGINS . 'page/lib/PageItem.php';

## Fonction d'installation

function pageInstall() {
    $page = new page();
    if (count($page->getItems()) < 1) {
        $pageItem = new pageItem();
        $pageItem->setName('Accueil');
        $pageItem->setPosition(1);
        $pageItem->setIsHomepage(1);
        $pageItem->setContent('<p>Félicitations, l\'installation de 299ko s\'est déroulée avec succès !<br>Par mesure de sécurité, vous devez maintenant supprimer le fichier install.php du répertoire d\'installation.</p>');
        $pageItem->setIsHidden(0);
        $page->save($pageItem);
        $page = new page();
        $page = new page();
        $pageItem = new pageItem();
        $pageItem->setName('Liens');
        $pageItem->setPosition(2);
        $pageItem->setContent('<ul><li><a href="https://github.com/299Ko">Suivre 299Ko sur GitHub/a></li>'
                . '<li><a href="https://facebook.com/299kocms/">Suivre 299Ko sur Facebook</a></li>'
                . '<li><a href="https://twitter.com/299kocms">Suivre 299Ko sur Twitter</a></li>'
                . '<li><a href="https://299ko.tuxfamily.org">Site Officiel 299Ko</a></li>'
                . '<li><a href="https://299ko.tuxfamily.org/page/documentation-6.html">Documentation de 299Ko</a></li></ul>');
        $page->save($pageItem);
    }
    $core = core::getInstance();
    $htaccess = $core->getHtaccess();
    $htaccess .= "\nRewriteRule ^page/([a-z-0-9]+)-([0-9]+).html$  index.php?p=page&url=$1&id=$2 [L]";
    $core->saveHtaccess($htaccess);
}

## Hooks

function pageEndFrontHead() {
    global $runPlugin;
    if ($runPlugin && $runPlugin->getName() == 'page') {
        global $pageItem;
        if ($pageItem && $pageItem->noIndex) {
            echo '<meta name="robots" content="noindex"><meta name="googlebot" content="noindex">';
        }
        $core = core::getInstance();
        $pluginsManager = pluginsManager::getInstance();
        if ($pageItem && $pluginsManager->isActivePlugin('galerie') && galerie::searchByfileName($pageItem->img))
            echo '<meta property="og:image" content="' . $core->getConfigVal('siteUrl') . '/' . str_replace('./', '', UPLOAD) . 'galerie/' . $pageItem->img . '" />';
    }
}

function pageEditCategories(Categorie $categorie) {
    if ($categorie->pluginId !== 'page') {
        return;
    }
    echo '<p><input type="checkbox" name="pageCategoriesHide" id="pageCategoriesHide" ';
    if (isset($categorie->pluginArgs['hide']) && $categorie->pluginArgs['hide'] == true) {
        echo ' checked ';
    }
    echo ' /><label for="pageCategoriesHide">Cacher dans le menu</label></p>';
    
    echo '<label for="pageCategoriesPosition">Position</label>';
    echo '<p><input type="number" name="pageCategoriesPosition" id="pageCategoriesPosition" value="' .$categorie->pluginArgs['position']. '" />';

}

function pageBeforeSaveCategorie(Categorie &$categorie) {
    if ($categorie->pluginId !== 'page') {
        return $categorie;
    }
    if (isset($_POST['pageCategoriesHide'])) {
        $categorie->pluginArgs['hide'] = true;
    } else {
        $categorie->pluginArgs['hide'] = false;
    }
    $position = $_POST['pageCategoriesPosition'] ?? 0;
    $pageManager = new PagesManager();
    if ($position < 0.5) {
        $position = $pageManager->makePosition();
    }
    $categorie->pluginArgs['position'] = $position;
    return $categorie;
}

## Code relatif au plugin

PagesManager::addToNavigation();

class pagee {

    private $items;
    private $pagesFile;

    public function __construct() {
        $this->pagesFile = DATA_PLUGIN . 'page/pages.json';
        $this->items = $this->loadPages();
    }

    public static function addToNavigation() {
        $page = new page();
        $pluginsManager = pluginsManager::getInstance();
        // Création d'items de navigation absents (plugins)
        foreach ($pluginsManager->getPlugins() as $k => $plugin)
            if ($plugin->getConfigVal('activate') && $plugin->getPublicFile() && $plugin->getName() != 'page') {
                $find = false;
                foreach ($page->getItems() as $k2 => $pageItem) {
                    if ($pageItem->getTarget() == $plugin->getName())
                        $find = true;
                }
                if (!$find) {
                    $pageItem = new pageItem();
                    $pageItem->setName($plugin->getInfoVal('name'));
                    $pageItem->setPosition($page->makePosition());
                    $pageItem->setIsHomepage(0);
                    $pageItem->setContent('');
                    $pageItem->setIsHidden(0);
                    $pageItem->setFile('');
                    $pageItem->setTarget($plugin->getName());
                    $pageItem->setNoIndex(0);
                    $page->save($pageItem);
                }
            }
        // génération de la navigation
        foreach ($page->getItems() as $k => $pageItem)
            if (!$pageItem->getIsHidden()) {
                if ($pageItem->targetIs() == 'plugin' && !$pluginsManager->isActivePlugin($pageItem->getTarget())) {
                    // no item !
                } else {
                    $url = ($pageItem->targetIs() == 'parent') ? $pageItem->getTarget() : $page->makeUrl($pageItem);
                    $pluginsManager->getPlugin('page')->addToNavigation($pageItem->getName(), $url, $pageItem->getTargetAttr(), $pageItem->getId(), $pageItem->getParent(), $pageItem->getCssClass());
                }
            }
    }

    public static function getPageContent($id) {
        $page = new page();
        if ($temp = $page->create($id))
            return $temp->getContent();
        else
            return '';
    }

    public function getItems() {
        return $this->items;
    }

    public function create($id) {
        foreach ($this->items as $pageItem) {
            if ($pageItem->getId() == $id)
                return $pageItem;
        }
        return false;
    }

    public function createHomepage() {
        foreach ($this->items as $pageItem) {
            if ($pageItem->getIshomepage())
                return $pageItem;
        }
        return false;
    }

    public function save($obj) {
        $id = intval($obj->getId());
        if ($id < 1)
            $id = $this->makeId();
        $position = floatval($obj->getPosition());
        if ($position < 0.5)
            $position = $this->makePosition();
        $data = array(
            'id' => $id,
            'name' => $obj->getName(),
            'position' => $position,
            'isHomepage' => $obj->getIsHomepage(),
            'content' => $obj->getContent(),
            'isHidden' => $obj->getIsHidden(),
            'file' => $obj->getFile(),
            'mainTitle' => $obj->getMainTitle(),
            'metaDescriptionTag' => $obj->getMetaDescriptionTag(),
            'metaTitleTag' => $obj->getMetaTitleTag(),
            'targetAttr' => $obj->getTargetAttr(),
            'target' => $obj->getTarget(),
            'noIndex' => $obj->getNoIndex(),
            'parent' => $obj->getParent(),
            'cssClass' => $obj->getCssClass(),
            'password' => $obj->getPassword(),
            'img' => $obj->getImg(),
        );
        $update = false;
        foreach ($this->items as $k => $v) {
            if ($v->getId() == $obj->getId()) {
                $this->items[$k] = $obj;
                $update = true;
            }
        }
        if (!$update)
            $this->items[] = $obj;
        if ($obj->getIsHomepage() > 0)
            $this->initIshomepageVal();
        $pages = $this->loadPages(true);
        if ($update) {
            if (is_array($pages)) {
                foreach ($pages as $k => $v) {
                    if ($v['id'] == $obj->getId()) {
                        $pages[$k] = $data;
                        $update = true;
                    }
                }
            }
        } else
            $pages[] = $data;
        $pages = util::sort2DimArray($pages, 'position', 'num');
        if (util::writeJsonFile($this->pagesFile, $pages))
            return true;
        return false;
    }

    public function del($obj) {
        if ($obj->getIsHomepage() < 1 && $this->count() > 1) {
            foreach ($this->items as $k => $v) {
                if ($v->getId() == $obj->getId())
                    unset($this->items[$k]);
                if ($v->getParent() == $obj->getId())
                    unset($this->items[$k]);
            }
            $pages = $this->loadPages(true);
            foreach ($pages as $k => $v) {
                if ($v['id'] == $obj->getId())
                    unset($pages[$k]);
                if ($v['parent'] == $obj->getId())
                    unset($pages[$k]);
            }
            if (util::writeJsonFile($this->pagesFile, $pages))
                return true;
            return false;
        }
        return false;
    }

    public function makePosition() {
        $pos = array(0);
        foreach ($this->items as $pageItem) {
            $pos[] = $pageItem->getPosition();
        }
        return max($pos) + 1;
    }

    public function count() {
        return count($this->items);
    }

    public function listTemplates() {
        $core = core::getInstance();
        $data = array();
        $items = util::scanDir(THEMES . $core->getConfigVal('theme') . '/', array('header.php', 'footer.php', 'style.css', '404.php', 'functions.php'));
        foreach ($items['file'] as $file) {
            if (in_array(util::getFileExtension($file), array('htm', 'html', 'txt', 'php')))
                $data[] = $file;
        }
        return $data;
    }

    public function makeUrl($obj) {
        $core = core::getInstance();
        // => Page
        if ($obj->targetIs() == 'page')
            $temp = ($core->getConfigVal('defaultPlugin') == 'page' && $obj->getIsHomepage()) ? $core->getConfigVal('siteUrl') : $core->getConfigVal('siteUrl') . '/page/' . util::strToUrl(preg_replace ("#\<i.+\<\/i\>#i", '', $obj->getName())) . '-' . $obj->getId() . '.html';
        // => URL
        elseif ($obj->targetIs() == 'url')
            $temp = $obj->getTarget();
        // => Plugin
        else
            $temp = $core->getConfigVal('siteUrl') . '/' . $obj->getTarget() . '/';
        return $temp;
    }

    public function isUnlocked($obj) {
        if ($obj->getPassword() == '')
            return true;
        elseif (isset($_SESSION['pagePassword']) && sha1($obj->getId()) . $obj->getPassword() . sha1($_SERVER['REMOTE_ADDR']) == $_SESSION['pagePassword'])
            return true;
        else
            return false;
    }

    public function unlock($obj, $password) {
        if (sha1(trim($password)) == $obj->getPassword()) {
            $_SESSION['pagePassword'] = sha1($obj->getId()) . $obj->getPassword() . sha1($_SERVER['REMOTE_ADDR']);
            return true;
        }
        return false;
    }

    private function makeId() {
        $ids = array(0);
        foreach ($this->items as $pageItem) {
            $ids[] = $pageItem->getId();
        }
        return max($ids) + 1;
    }

    private function initIshomepageVal() {
        foreach ($this->items as $obj) {
            $obj->setIsHomepage(0);
            $this->save($obj);
        }
    }

    private function loadPages($array = false) {
        $data = array();
        if (file_exists($this->pagesFile)) {
            $items = util::readJsonFile($this->pagesFile);
            $items = util::sort2DimArray($items, 'position', 'num');
            // Phase de correction des positions
            for ($i = 0; $i != count($items); $i++) {
                $pos = $i + 1;
                $items[$i]['position'] = $pos;
            }
            if ($array)
                return $items;
            foreach ($items as $pageItem) {
                $data[] = new pageItem($pageItem);
            }
        }
        return $data;
    }

}

class pageIteme {

    private $id;
    private $name;
    private $position;
    private $isHomepage;
    private $content;
    private $isHidden;
    private $file;
    private $mainTitle;
    private $metaDescriptionTag;
    private $metaTitleTag;
    private $target;
    private $targetAttr;
    private $noIndex;
    private $parent;
    private $cssClass;
    private $password;
    private $img;
    
    protected $type;
    
    const PAGE      = 'page';
    const CATEGORIE = 'categorie';
    const URL       = 'url';
    const PLUGIN    = 'plugin';

    public function __construct($val = array()) {
        if (count($val) > 0) {
            $this->id = $val['id'];
            $this->name = $val['name'];
            $this->position = $val['position'];
            $this->isHomepage = $val['isHomepage'];
            $this->content = $val['content'];
            $this->isHidden = $val['isHidden'];
            $this->file = $val['file'];
            $this->mainTitle = $val['mainTitle'];
            $this->metaDescriptionTag = $val['metaDescriptionTag'];
            $this->metaTitleTag = (isset($val['metaTitleTag']) ? $val['metaTitleTag'] : '');
            $this->target = (isset($val['target']) ? $val['target'] : '');
            $this->targetAttr = (isset($val['targetAttr']) ? $val['targetAttr'] : '_self');
            $this->noIndex = (isset($val['noIndex']) ? $val['noIndex'] : 0);
            $this->parent = (isset($val['parent']) ? $val['parent'] : 0);
            $this->cssClass = (isset($val['cssClass']) ? $val['cssClass'] : '');
            $this->password = (isset($val['password']) ? $val['password'] : '');
            $this->img = (isset($val['img']) ? $val['img'] : '');
            $this->type = $val['type'];
        }
    }

    public function setName($val) {
        $val = trim($val);
        $this->name = $val;
    }

    public function setPosition($val) {
        $this->position = trim($val);
    }

    public function setIsHomepage($val) {
        $this->isHomepage = trim($val);
    }

    public function setContent($val) {
        $this->content = trim($val);
    }

    public function setIsHidden($val) {
        $this->isHidden = intval($val);
    }

    public function setFile($val) {
        $this->file = trim($val);
    }

    public function setMainTitle($val) {
        $this->mainTitle = trim($val);
    }

    public function setMetaDescriptionTag($val) {
        $val = trim($val);
        if (mb_strlen($val) > 150)
            $val = mb_strcut($val, 0, 150) . '...';
        $this->metaDescriptionTag = $val;
    }

    public function setMetaTitleTag($val) {
        $val = trim($val);
        if (mb_strlen($val) > 50)
            $val = mb_strcut($val, 0, 50) . '...';
        $this->metaTitleTag = $val;
    }

    public function setTarget($val) {
        $this->target = trim($val);
    }

    public function setTargetAttr($val) {
        $this->targetAttr = trim($val);
    }

    public function setNoIndex($val) {
        $this->noIndex = trim($val);
    }

    public function setParent($val) {
        $this->parent = trim($val);
    }

    public function setCssClass($val) {
        $this->cssClass = trim($val);
    }

    public function setPassword($val) {
        $this->password = ($val == '') ? $val : sha1(trim($val));
    }

    public function setImg($val) {
        $this->img = trim($val);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getIsHomepage() {
        return $this->isHomepage;
    }

    public function getContent() {
        return $this->content;
    }

    public function getIsHidden() {
        return $this->isHidden;
    }

    public function getFile() {
        return $this->file;
    }

    public function getMainTitle() {
        return $this->mainTitle;
    }

    public function getMetaDescriptionTag() {
        return $this->metaDescriptionTag;
    }

    public function getMetaTitleTag() {
        return $this->metaTitleTag;
    }

    public function getTarget() {
        return $this->target;
    }

    public function getTargetAttr() {
        return $this->targetAttr;
    }

    public function getNoIndex() {
        return $this->noIndex;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getCssClass() {
        return $this->cssClass;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getImg() {
        return $this->img;
    }

    public function targetIs() {
        if ($this->getTarget() == '')
            return 'page';
        elseif ($this->getTarget() == 'javascript:')
            return 'parent';
        elseif (filter_var($this->getTarget(), FILTER_VALIDATE_URL))
            return 'url';
        else
            return 'plugin';
    }
    
    public function setType(string $type) {
        $this->type = $type;
    }
    
    public function getType() {
        return $this->type;
    }

}