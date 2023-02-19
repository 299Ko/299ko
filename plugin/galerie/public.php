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

$galerie = new galerie();
$runPlugin->setTitleTag($runPlugin->getConfigVal('label'));
if($runPlugin->getIsDefaultPlugin()){
    $runPlugin->setTitleTag($core->getConfigVal('siteName'));
    $runPlugin->setMetaDescriptionTag($core->getConfigVal('siteDescription'));
}
$runPlugin->setMainTitle($runPlugin->getConfigVal('label'));