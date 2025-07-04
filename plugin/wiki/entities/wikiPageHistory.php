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
defined('ROOT') or exit('Access denied!');

class WikiPageHistory
{

    private $id;
    private $pageId;
    private $version;
    private $name;
    private $content;
    private $intro;
    private $seoDesc;
    private $modifiedBy;
    private $modifiedAt;
    private $changeDescription;

    public function __construct($val = [])
    {
        if (count($val) > 0) {
            $this->id = isset($val['id']) && $val['id'] !== null ? intval($val['id']) : 0;
            $this->pageId = isset($val['pageId']) ? intval($val['pageId']) : 0;
            $this->version = isset($val['version']) ? intval($val['version']) : 1;
            $this->name = isset($val['name']) ? trim($val['name']) : '';
            $this->content = isset($val['content']) ? trim($val['content']) : '';
            $this->intro = isset($val['intro']) ? trim($val['intro']) : '';
            $this->seoDesc = isset($val['seoDesc']) ? trim($val['seoDesc']) : '';
            $this->modifiedBy = isset($val['modifiedBy']) ? trim($val['modifiedBy']) : '';
            $this->modifiedAt = isset($val['modifiedAt']) ? $val['modifiedAt'] : date('Y-m-d H:i:s');
            $this->changeDescription = isset($val['changeDescription']) ? trim($val['changeDescription']) : '';
        }
    }

    public function setId($val)
    {
        $this->id = intval($val);
    }

    public function setPageId($val)
    {
        $this->pageId = intval($val);
    }

    public function setVersion($val)
    {
        $this->version = intval($val);
    }

    public function setName($val)
    {
        $this->name = trim($val);
    }

    public function setContent($val)
    {
        $this->content = trim($val);
    }

    public function setIntro($val)
    {
        $this->intro = trim($val);
    }

    public function setSEODesc($val)
    {
        $this->seoDesc = trim($val);
    }

    public function setModifiedBy($val)
    {
        $this->modifiedBy = trim($val);
    }

    public function setModifiedAt($val)
    {
        $this->modifiedAt = $val;
    }

    public function setChangeDescription($val)
    {
        $this->changeDescription = trim($val);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPageId()
    {
        return $this->pageId;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getIntro()
    {
        return $this->intro;
    }

    public function getSEODesc()
    {
        return $this->seoDesc;
    }

    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function getChangeDescription()
    {
        return $this->changeDescription;
    }

    public function getReadableDate()
    {
        return util::getDate($this->modifiedAt);
    }

} 