<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

require_once PLUGINS . 'filesmanager/lib/FilesManager.php';

function filesmanagerInstall() {
    $path = FilesManager::PATH;
    if (!is_dir($path)) {
        mkdir($path);
        @chmod($path, 0755);
    }
    if (!is_file(FilesManager::FILE)) {
        util::writeJsonFile(FilesManager::FILE, []);
    }
}

function filesmanagerInsertUploadButtonForEditor($editorId) {
    static $nbreButtons = 0;

    $tplButton = new Template(PLUGINS . 'filesmanager/template/buttonforeditor.tpl');
    $tplButton->set('buttonId', $nbreButtons);
    $tplButton->set('ajaxUrl', util::urlBuild('plugin/filesmanager/lib/ajax.php?action=manage&editor=' .
                    $editorId . '&button=' . $nbreButtons . '&token=' . administrator::getToken()));
    echo $tplButton->output();

    if ($nbreButtons === 0) {
        $tplScripts = new Template(PLUGINS . 'filesmanager/template/scripts.tpl');
        $tplScripts->set('formSubmitUrl', util::urlBuild('plugin/filesmanager/lib/ajax.php?action=upload&editor=' .
                        $editorId . '&button=' . $nbreButtons . '&token=' . administrator::getToken()));
        $tplScripts->set('ajaxUrl', util::urlBuild('plugin/filesmanager/lib/ajax.php?action=manage&editor=' .
                        $editorId . '&button=' . $nbreButtons . '&token=' . administrator::getToken()));
        echo $tplScripts->output();
    }
    $nbreButtons++;
}
