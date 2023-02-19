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
define('ROOT', './');
include_once(ROOT . 'common/common.php');
if (!$runPlugin || $runPlugin->getConfigVal('activate') < 1) {
    $core->error404();
} elseif ($runPlugin->getPublicFile()) {
    core::executeHookAction('publicBeforeRunPlugin', $runPlugin->getName());
    if (util::getFileExtension($runPlugin->getPublicTemplate()) === 'tpl' && file_exists(THEMES . $core->getConfigVal('theme') . '/layout.tpl')) {
        $layout = new Template(THEMES . $core->getConfigVal('theme') . '/layout.tpl');
        $tpl = new Template($runPlugin->getPublicTemplate());
        include($runPlugin->getPublicFile());
        $layout->set('CONTENT', $tpl->output());
        echo $layout->output();
    } else {
        include($runPlugin->getPublicFile());
        include($runPlugin->getPublicTemplate());
    }
}