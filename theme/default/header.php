<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(THEMES . $core->getConfigVal('theme') . '/functions.php');
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php eval($core->callHook('frontHead')); 
        core::executeHookAction('frontHead', $runPlugin->getName()); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php show::titleTag(); ?></title>
        <base href="<?php show::siteUrl(); ?>/" />
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
        <meta name="description" content="<?php show::metaDescriptionTag(); ?>" />
        <link rel="icon" href="<?php show::themeIcon(); ?>" />
        <link rel="stylesheet" href="<?php show::siteUrl();?>/assets/css/pico.min.css" />
        <?php show::linkTags(); ?>
        <?php show::scriptTags(); ?>
        <?php eval($core->callHook('endFrontHead'));
        core::executeHookAction('endFrontHead'); ?>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="header_content">
                    <div id="mobile_menu"></div>
                    <p id="siteName"><a href="<?php show::siteUrl(); ?>"><?php show::siteName(); ?></a></p>
                    <ul id="navigation">
                        <?php
                        show::mainNavigation();
                        eval($core->callHook('endMainNavigation'));
                        ?>
                    </ul>
                </div>
            </div>
            <div id="alert-msg">
                <?php show::displayMsg(); ?>
            </div>
            <div id="banner"></div>
            <div id="body">
                <div id="content" class="<?php show::pluginId(); ?>">
                    <?php show::mainTitle(); ?>