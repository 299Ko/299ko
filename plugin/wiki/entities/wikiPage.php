<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

/**
 * Page Wiki
 * 
 * Classe représentant une page du plugin Wiki
 */
class WikiPage
{

    private $id;
    private $name;
    private $date;
    private $content = '';
    private $intro;
    private $seoDesc;
    private $draft;
    private $img;

    private $version;
    private $lastModified;
    private $modifiedBy;
    private $slug;

    private ContentParser $parser;

    /**
     * Tableau des IDs des catégories associées
     * @var array
     */
    public array $categories = [];

    /**
     * Constructeur de la page Wiki
     * 
     * @param array $val Données de la page
     */
    public function __construct($val = [])
    {
        if (count($val) > 0) {
            $this->id = $val['id'];
            $this->name = $val['name'];
            $this->content = $val['content'];
            $this->intro = $val['intro'] ?? '';
            $this->seoDesc = $val['seoDesc'] ?? '';
            $this->date = $val['date'];
            $this->draft = $val['draft'];
            $this->img = (isset($val['img']) ? $val['img'] : '');

            $this->categories = $val['categories'] ?? [];
            $this->version = $val['version'] ?? 1;
            $this->lastModified = $val['lastModified'] ?? date('Y-m-d H:i:s');
            $this->modifiedBy = $val['modifiedBy'] ?? '';
            $this->slug = $val['slug'] ?? util::strToUrl($val['name']);
        }
        $this->parser = new ContentParser($this->content);
    }

    /**
     * Définir l'ID de la page
     * 
     * @param int $val ID de la page
     * @return void
     */
    public function setId($val)
    {
        $this->id = intval($val);
    }

    /**
     * Définir le nom de la page
     * 
     * @param string $val Nom de la page
     * @return void
     */
    public function setName($val)
    {
        $this->name = trim($val);
        $this->slug = util::strToUrl($val);
    }

    /**
     * Définir le contenu de la page
     * 
     * @param string $val Contenu de la page
     * @return void
     */
    public function setContent($val)
    {
        $this->content = trim($val);
        $this->parser->setContent($this->content);
    }

    /**
     * Définir l'introduction de la page
     * 
     * @param string $val Introduction de la page
     * @return void
     */
    public function setIntro($val)
    {
        $this->intro = trim($val);
    }

    /**
     * Définir la description SEO de la page
     * 
     * @param string $val Description SEO
     * @return void
     */
    public function setSEODesc($val)
    {
        $this->seoDesc = trim($val);
    }

    /**
     * Définir la date de la page
     * 
     * @param string|null $val Date de la page
     * @return void
     */
    public function setDate($val)
    {
        if ($val === null || empty($val)) {
            $val = date('Y-m-d');
        }
        $val = trim($val);
        $this->date = $val;
    }

    /**
     * Définir le statut brouillon de la page
     * 
     * @param bool $val Statut brouillon
     * @return void
     */
    public function setDraft($val)
    {
        $this->draft = trim($val);
    }

    /**
     * Définir l'image de la page
     * 
     * @param string $val Chemin de l'image
     * @return void
     */
    public function setImg($val)
    {
        $this->img = trim($val);
    }



    /**
     * Définir la version de la page
     * 
     * @param int $val Numéro de version
     * @return void
     */
    public function setVersion($val)
    {
        $this->version = intval($val);
    }

    /**
     * Définir la date de dernière modification
     * 
     * @param string $val Date de dernière modification
     * @return void
     */
    public function setLastModified($val)
    {
        $this->lastModified = $val;
    }

    /**
     * Définir l'auteur de la dernière modification
     * 
     * @param string $val Auteur de la modification
     * @return void
     */
    public function setModifiedBy($val)
    {
        $this->modifiedBy = $val;
    }

    /**
     * Définir le slug de la page
     * 
     * @param string $val Slug de la page
     * @return void
     */
    public function setSlug($val)
    {
        $this->slug = $val;
    }

    /**
     * Obtenir l'ID de la page
     * 
     * @return int ID de la page
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtenir le nom de la page
     * 
     * @return string Nom de la page
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Obtenir le contenu brut de la page
     * 
     * @return string Contenu brut
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Obtenir le contenu parsé de la page
     * 
     * @return string Contenu parsé
     */
    public function getParsedContent(): string
    {
        return $this->parser->getParsedContent();
    }

    /**
     * Obtenir le contenu sans shortcodes
     * 
     * @return string Contenu sans shortcodes
     */
    public function getContentWithoutShortcodes():string
    {
        return $this->parser->getWithoutShortcodesContent();
    }

    /**
     * Obtenir l'URL de la page
     * 
     * @return string URL de la page
     */
    public function getUrl()
    {
        return router::getInstance()->generate('wiki-read', ['name' => $this->slug, 'id' => $this->id]);
    }

    /**
     * Obtenir l'introduction de la page
     * 
     * @return string|false Introduction ou false si vide
     */
    public function getIntro()
    {
        return ($this->intro === '' ? false : $this->intro);
    }

    /**
     * Obtenir la description SEO de la page
     * 
     * @return string|false Description SEO ou false si vide
     */
    public function getSEODesc()
    {
        return ($this->seoDesc === '' ? false : $this->seoDesc);
    }

    /**
     * Obtenir la date de la page
     * 
     * @return string Date de la page
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Obtenir la date lisible de la page
     * 
     * @return string Date formatée
     */
    public function getReadableDate() {
        return util::getDate($this->date);
    }

    /**
     * Obtenir le statut brouillon de la page
     * 
     * @return bool Statut brouillon
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * Obtenir l'image de la page
     * 
     * @return string Chemin de l'image
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Obtenir l'URL de l'image de la page
     * 
     * @return string URL de l'image
     */
    public function getImgUrl()
    {
        return util::urlBuild($this->img);
    }



    /**
     * Obtenir la version de la page
     * 
     * @return int Numéro de version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Obtenir la date de dernière modification
     * 
     * @return string Date de dernière modification
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Obtenir l'auteur de la dernière modification
     * 
     * @return string Auteur de la modification
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Obtenir le slug de la page
     * 
     * @return string Slug de la page
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Incrémenter la version de la page
     * 
     * @return void
     */
    public function incrementVersion()
    {
        $this->version++;
        $this->lastModified = date('Y-m-d H:i:s');
    }

}