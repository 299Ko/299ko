<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
define('ROOT', '../../../');
include_once(ROOT . 'common/common.php');
include_once(COMMON . 'administrator.class.php');
$administrator = new administrator($core->getConfigVal('adminEmail'), $core->getConfigVal('adminPwd'));
if (!$administrator->isAuthorized()) {
    die();
}

$editorId = $_GET['editor'] ?? '';
$button = $_GET['button'] ?? '';
if ($_GET['action'] === 'manage') {
    
    $files = FilesManager::getFilesMetas();
    if (is_null($files)) {
        $files = false;
    } else {
        foreach ($files as $id => &$file) {
            $file['deleteurl'] = util::urlBuild('?p=filesmanager&action=delete&id=' . $id .
                            '&token=' . administrator::getToken(), true);
        }
    }
    $tpl = new Template(PLUGINS . 'filesmanager/template/ajaxmanage.tpl');
    $tpl->set('files', $files);
    $tpl->set('editorId', $editorId);
    echo $tpl->output();
} elseif ($_GET['action'] === 'upload') {
    if ($_FILES['image']['name'] != '') {
        $image = $_FILES['image']['name'];
        if (FilesManager::uploadFile('image')) {
            echo json_encode(['success' => 1,
                'editor' => $editorId,
                'button' => $button]);
            return true;
        } else {
            echo json_encode(['success' => 0]);
            return false;
        }
    }
}
