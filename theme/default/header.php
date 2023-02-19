<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(THEMES . $core->getConfigVal('theme') . '/functions.php');
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php core::executeHookAction('frontHead'); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php show::titleTag(); ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
        <meta name="description" content="<?php show::metaDescriptionTag(); ?>" />
        <link rel="icon" href="<?php show::themeIcon(); ?>" />
        <link rel="stylesheet" href="<?php show::siteUrl();?>/assets/css/pico.min.css" />
        <?php show::linkTags(); ?>
        <?php show::scriptTags(); ?>
        <?php core::executeHookAction('endFrontHead'); ?>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <nav id="header_content">
                    <div id="mobile_menu"></div>
                    <ul id="siteName">
                        <li>
                            <a href="<?php show::siteUrl(); ?>"><?php show::siteName(); ?></a>
                        </li>
                    </ul>
                    <ul id="navigation">
                        <?php
                        show::mainNavigation();
                        ?>
                    </ul>
                    <ul id="end-navigation">
                        <?php
                        core::executeHookAction('endMainNavigation');
                        ?>
                    </ul>
                </nav>
            </div>
            <div id="alert-msg">
                <?php show::displayMsg(); ?>
            </div>
            <div id="banner">
                <?php
                core::executeHookAction('topBanner');
                core::executeHookAction('bottomBanner');
                ?>
            </div>
            <div id="body">
                <div id="content" class="<?php show::pluginId(); ?>">
                    <?php show::mainTitle(); ?>