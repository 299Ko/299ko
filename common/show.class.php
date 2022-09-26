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

class show {

    /**
     * Add a message to display in the next view, saved in session
     * 
     * Class can be error, success, info (default), warning
     *
     * @param string Message Content
     * @param string Class Message
     */
    public static function msg($content, $class = 'info') {
        if (function_exists('msg')) {
            call_user_func('msg', $content, $class);
            return;
        }

        if (!isset($_SESSION['flash_msg']) || !is_array($_SESSION['flash_msg'])) {
            $_SESSION['flash_msg'] = [];
        }
        $_SESSION['flash_msg'][] = [
            'class' => $class,
            'content' => $content
        ];
    }

    /**
     * Display all messages added with 'msg' method, who were saved in session
     */
    public static function displayMsg() {
        if (function_exists('displayMsg')) {
            call_user_func('displayMsg');
            return;
        }
        if (!isset($_SESSION['flash_msg']) || !is_array($_SESSION['flash_msg'])) {
            return;
        }
        foreach ($_SESSION['flash_msg'] as $msg) {
            echo '<div class="msg ' . $msg['class'] . '"><p>' . $msg['content'] . '</p><a href="#" class="msg-button-close"><i class="fa-solid fa-xmark"></i></a></div>';
        }
        unset($_SESSION['flash_msg']);
    }

    ## Affiche les balises "link" type css (admin + theme)

    public static function linkTags() {
        if (function_exists('linkTags'))
            call_user_func('linkTags');
        else {
            $core = core::getInstance();
            $pluginsManager = pluginsManager::getInstance();
            foreach ($core->getCss() as $k => $v) {
                echo '<link href="' . $v . '" rel="stylesheet" type="text/css" />';
            }
            foreach ($pluginsManager->getPlugins() as $k => $plugin)
                if ($plugin->getConfigval('activate') == 1) {
                    if (ROOT == './' && $plugin->getConfigVal('activate') && $plugin->getPublicCssFile())
                        echo '<link href="' . $plugin->getPublicCssFile() . '" rel="stylesheet" type="text/css" />';
                    elseif (ROOT == '../' && $plugin->getConfigVal('activate') && $plugin->getAdminCssFile())
                        echo '<link href="' . $plugin->getAdminCssFile() . '" rel="stylesheet" type="text/css" />';
                }
            if (ROOT == './')
                echo '<link href="' . $core->getConfigVal('siteUrl') . '/' . 'theme/' . $core->getConfigVal('theme') . '/styles.css" rel="stylesheet" type="text/css" />';
        }
    }

    ## Affiche les balises "script" type javascript (admin + theme)

    public static function scriptTags() {
        if (function_exists('scriptTags'))
            call_user_func('scriptTags');
        else {
            $core = core::getInstance();
            $pluginsManager = pluginsManager::getInstance();
            foreach ($core->getJs() as $k => $v) {
                echo '<script type="text/javascript" src="' . $v . '"></script>';
            }
            foreach ($pluginsManager->getPlugins() as $k => $plugin)
                if ($plugin->getConfigval('activate') == 1) {
                    if (ROOT == './' && $plugin->getConfigVal('activate') && $plugin->getPublicJsFile())
                        echo '<script type="text/javascript" src="' . $plugin->getPublicJsFile() . '"></script>';
                    elseif (ROOT == '../' && $plugin->getConfigVal('activate') && $plugin->getAdminJsFile())
                        echo '<script type="text/javascript" src="' . $plugin->getAdminJsFile() . '"></script>';
                }
            if (ROOT == './')
                echo '<script type="text/javascript" src="' . $core->getConfigVal('siteUrl') . '/' . 'theme/' . $core->getConfigVal('theme') . '/scripts.js' . '"></script>';
        }
    }

    ## Affiche un champ de formulaire contenant le jeton de session (admin)

    public static function adminTokenField() {
        $core = core::getInstance();
        echo '<input type="hidden" name="token" value="' . administrator::getToken() . '" />';
    }

    ## Affiche le contenu de la meta title (theme)

    public static function titleTag() {
        if (function_exists('titleTag'))
            call_user_func('titleTag');
        else {
            $core = core::getInstance();
            global $runPlugin;
            if (!$runPlugin)
                echo '404';
            else
                echo $runPlugin->getTitleTag() . ' - ' . $core->getConfigVal('siteName');
        }
    }

    ## Affiche le contenu de la meta description (theme)

    public static function metaDescriptionTag() {
        if (function_exists('metaDescriptionTag'))
            call_user_func('metaDescriptionTag');
        else {
            $core = core::getInstance();
            global $runPlugin;
            if (!$runPlugin)
                echo '404';
            else
                echo $runPlugin->getMetaDescriptionTag();
        }
    }

    ## Affiche le titre de page (theme)

    public static function mainTitle($format = '<h1>[mainTitle]</h1>') {
        if (function_exists('mainTitle'))
            call_user_func('mainTitle', $format);
        else {
            $core = core::getInstance();
            global $runPlugin;
            $data = $format;
            if (!$runPlugin)
                $data = str_replace('[mainTitle]', '404', $data);
            else {
                if ($core->getConfigVal('hideTitles') == 0 && $runPlugin->getMainTitle() != '') {
                    $data = $format;
                    $data = str_replace('[mainTitle]', $runPlugin->getMainTitle(), $data);
                } else
                    $data = '';
            }
            echo $data;
        }
    }

    ## Affiche le nom du site (theme)

    public static function siteName() {
        if (function_exists('siteName'))
            call_user_func('siteName');
        else {
            $core = core::getInstance();
            echo $core->getConfigVal('siteName');
        }
    }

    ## Affiche l'url du site (theme)

    public static function siteUrl() {
        if (function_exists('siteUrl'))
            call_user_func('siteUrl');
        else {
            $core = core::getInstance();
            echo $core->getConfigVal('siteUrl');
        }
    }

    ## Affiche la navigation principale (theme)

    public static function mainNavigation($format = '<li><a class="[cssClass]" href="[target]" target="[targetAttribut]">[label]</a>[childrens]</li>') {
        if (function_exists('mainNavigation'))
            call_user_func('mainNavigation', $format);
        else {
            $pluginsManager = pluginsManager::getInstance();
            $core = core::getInstance();
            $data = '';
            foreach ($pluginsManager->getPlugins() as $k => $plugin)
                if ($plugin->getConfigval('activate') == 1) {
                    foreach ($plugin->getNavigation() as $k2 => $item)
                        if ($item['label'] != '') {
                            if ($item['parent'] < 1) {
                                $temp = $format;
                                $temp = str_replace('[target]', $item['target'], $temp);
                                $temp = str_replace('[label]', $item['label'], $temp);
                                $temp = str_replace('[targetAttribut]', $item['targetAttribut'], $temp);
                                $temp = str_replace('[cssClass]', $item['cssClass'], $temp);
                                $data2 = '<ul>';
                                $i = 0;
                                foreach ($plugin->getNavigation() as $k3 => $item2)
                                    if ($item2['label'] != '' && $item2['parent'] == $item['id']) {
                                        $temp2 = $format;
                                        $temp2 = str_replace('[target]', $item2['target'], $temp2);
                                        $temp2 = str_replace('[label]', $item2['label'], $temp2);
                                        $temp2 = str_replace('[targetAttribut]', $item2['targetAttribut'], $temp2);
                                        $temp2 = str_replace('[cssClass]', $item2['cssClass'], $temp2);
                                        $temp2 = str_replace('[childrens]', '', $temp2);
                                        $data2 .= $temp2;
                                        $i++;
                                    }
                                $data2 .= '</ul>';
                                if ($i == 0)
                                    $data2 = '';
                                $temp = str_replace('[childrens]', $data2, $temp);
                                $data .= $temp;
                            }
                        }
                }
            echo $data;
        }
    }

    public static function adminNavigation() {
        if (function_exists('adminNavigation'))
            call_user_func('adminNavigation', $format);
        else {
            $pluginsManager = pluginsManager::getInstance();
            $data = '';
            foreach ($pluginsManager->getPlugins() as $k => $v) {
                if ($v->getConfigVal('activate') && $v->getAdminFile()) {
                    if ($v->getIsDefaultAdminPlugin()) {
                        $data = '<li><a href="index.php?p=' . $v->getName() .'">' .
                            $v->getInfoVal('name') .'</a></li>' . $data;
                    } else {
                    $data .= '<li><a href="index.php?p=' . $v->getName() .'">' .
                            $v->getInfoVal('name') .'</a></li>';
                    }
                }
            }
            echo $data;
        }
    }

    ## Affiche le theme courant (theme)

    public static function theme($format = '<a target="_blank" href="[authorWebsite]">[name]</a>') {
        if (function_exists('theme'))
            call_user_func('theme', $format);
        else {
            $core = core::getInstance();
            $data = $format;
            $data = str_replace('[authorWebsite]', $core->getThemeInfo('authorWebsite'), $data);
            $data = str_replace('[name]', $core->getThemeInfo('name'), $data);
            $data = str_replace('[id]', $core->getConfigVal('theme'), $data);
            echo $data;
        }
    }

    ## Affiche l'identifiant du plugin courant (theme)

    public static function pluginId() {
        if (function_exists('pluginId'))
            call_user_func('pluginId');
        else {
            $core = core::getInstance();
            global $runPlugin;
            if (!$runPlugin)
                echo '';
            else
                echo $runPlugin->getName();
        }
    }

    ## Affiche l'URL courante (theme)

    public static function currentUrl() {
        if (function_exists('currentUrl'))
            call_user_func('currentUrl');
        else {
            $core = core::getInstance();
            echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
    }

    ## Affiche l'URL de l'icon du thème

    public static function themeIcon() {
        if (function_exists('themeIcon'))
            call_user_func('themeIcon');
        $core = core::getInstance();
        $icon = 'theme/' . $core->getConfigVal('theme') . '/icon.png';
        if (file_exists($icon))
            echo $icon;
    }

}
?>