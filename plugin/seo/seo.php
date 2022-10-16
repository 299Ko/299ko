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

## Fonction d'installation

function seoInstall() {
    
}

## Hooks

function seoEndFrontHead() {
    $plugin = pluginsManager::getInstance()->getPlugin('seo');
    if (!$plugin->getConfigVal('wt') || $plugin->getConfigVal('wt') == '') {
        return;
    }
    $temp = "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '" . $plugin->getConfigVal('trackingId') . "', 'auto');
  ga('send', 'pageview');

</script>";
    $temp .= '<meta name="google-site-verification" content="' . $plugin->getConfigVal('wt') . '" />';
    echo $temp;
}

function seoEndFrontBody() {
    echo '<div id="seo_social_float"><ul>';
    echo seoGetSocialIcons('<li>', '</li>');
    echo '</ul></div>';
}

function seoMainNavigation() {
    echo seoGetSocialIcons('<li class="seo_element">', '</li>');
}

function seoFooter() {
    echo '<div id="seo_social"><ul>';
    echo seoGetSocialIcons('<li>', '</li>');
    echo '</ul></div>';
}

function seoGetSocialIcons($before = '', $after = '') {
    $social = seoGetSocialVars();
    $plugin = pluginsManager::getInstance()->getPlugin('seo');
    $str = "";

    foreach ($social as $k => $v) {
        $tConfig = $plugin->getConfigVal($v);
        if ($tConfig && $tConfig !== '') {
            $str .= $before . '<a target="_blank" title="Suivez-nous sur ' . $k . '" href="' . $tConfig . '"><i class="fa-brands fa-' . $v . '"></i></a>' . $after;
        }
    }
    return $str;
}

function seoGetSocialVars() {
    return [
        'Facebook' => 'facebook',
        'Twitter' => 'twitter',
        'YouTube' => 'youtube',
        'Instagram' => 'instagram',
        'TikTok' => 'tiktok',
        'Pinterest' => 'pinterest',
        'Linkedin' => 'linkedin',
        'Viadeo' => 'viadeo',
        'GitHub' => 'github',
        "Mastodon" => 'mastodon'
    ];
}

/**
 * Display Open Graph to socials networks and Google
 * 
 * @global plugin $runPlugin
 */
function seoAddMetaOpenGraph() {
    global $runPlugin;
    $core = core::getInstance();
    // Facebook, Pinterest
    echo '<meta property="og:title" content="'. $runPlugin->getMainTitle() .'" />
    <meta property="og:url" content="'.util::getCurrentURL() . '" />
    <meta property="og:image" content="'.show::getFeaturedImage() . '" />
    <meta property="og:description" content="'.$runPlugin->getMetaDescriptionTag() . '" />
    <meta property="og:site_name" content="'.$core->getConfigVal('siteName') . '" />
    <meta property="og:type" content="article" />';
    
    // Google
    echo '<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "headline": "'.$runPlugin->getMainTitle(). '",
      "image": "' .show::getFeaturedImage().'"
    }
    </script>';
    
    // Twitter
    $twitterUser = pluginsManager::getPluginConfVal('seo', 'twitter');
    echo '<meta name="twitter:card" content="summary_large_image" />';
    if ($twitterUser && $twitterUser !== '') {
        $user = basename($twitterUser);
        echo '<meta name="twitter:site" content="@' . $user .'">';
    }
    echo '<meta name="twitter:title" content="'. $runPlugin->getMainTitle() .'" />
    <meta name="twitter:description" content="'.$runPlugin->getMetaDescriptionTag() . '" />
    <meta name="twitter:image:src" content="'.show::getFeaturedImage() . '" />';
}
