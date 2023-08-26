<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(THEMES . $core->getConfigVal('theme') . '/functions.php');
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php eval($core->callHook('frontHead')); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php show::titleTag(); ?></title>
        <base href="<?php show::siteUrl(); ?>/" />
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5" />
        <meta name="description" content="<?php show::metaDescriptionTag(); ?>" />
        <link rel="icon" href="<?php show::themeIcon(); ?>" />
        <?php show::linkTags(); ?>
        <?php show::scriptTags(); ?>
        <?php show::showMetas(); ?>
        <?php eval($core->callHook('endFrontHead')); ?>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <nav id="header_content">
                    <button id="mobile_menu" aria-label="Menu"></button>
                    <p id="siteName"><a href="<?php show::siteUrl(); ?>"><?php show::siteName(); ?></a></p>
                    <ul id="navigation">
                        <?php
                        show::mainNavigation();
                        eval($core->callHook('endMainNavigation'));
                        ?>
                    </ul>
                </nav>
            </div>
            <div id="alert-msg">
                <?php show::displayMsg(); ?>
            </div>
            <div id="banner">
                <div id="siteDesc">
                    <?php show::siteDesc(); ?>
                </div>
            </div>
            <main id="body">
                <?php if ($core->getConfigVal('hideTitles') == 0) {
                    ?>
                    <div id="pageTitle">
                        <?php show::mainTitle(); ?>
                    </div>
                <?php } ?>
                <div id="body-page">
                    <?php show::displayPublicSidebar(); ?>
                    <div id="content" class="<?php show::pluginId(); ?>">